<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/02/20
 * Time: 19:07
 * @property THistoryChatLog $THistoryChatLog
 * @property THistoryChatLogTime $THistoryChatLogTime
 */

class AddAllChatLogTimeShell extends AppShell
{
  const LOG_INFO = 'batch-info';
  const LOG_ERROR = 'batch-error';

  public $uses = array('THistory', 'THistoryChatLog', 'THistoryChatLogTime');

  public function startup()
  {
    parent::startup();
  }

  /**
   * オートメッセージの
   */
  public function addAll()
  {
    ini_set('memory_limit', -1);
    $limit = 200000;
    $offset = 0;
    try {
      $count = $this->THistory->find('count');
      $loopCount = ceil($count / $limit);
      $this->printLog('loop-count: '.$loopCount);
      for ($i = 0; $i < $loopCount; $i++) {
        $this->THistoryChatLogTime->begin();
        $allData = $this->THistory->find('all', array(
          'order' => array('id' => 'desc'),
          'limit' => $limit,
          'offset' => $offset
        ));
        $queries = 'INSERT INTO `t_history_chat_log_times` VALUES ';
        $minTime = $this->THistoryChatLog->query('SELECT id, t_histories_id, t_history_stay_logs_id, MIN(created) as firstSpeechTime FROM t_history_chat_logs as THistoryChatLog WHERE t_histories_id >= ' . $allData[0]['THistory']['id'] . ' AND t_histories_id <= ' . $allData[count($allData) - 1]['THistory']['id'] . ' GROUP BY t_histories_id ORDER BY t_histories_id', false);
        foreach($minTime as $index => $datum) {
          $queries .= "(" . $datum['THistoryChatLog']['id'] . ", " . $datum['THistoryChatLog']['t_histories_id'] . ", " . $this->THistoryChatLogTime->type[THistoryChatLogTime::MIN_CHAT_LOG_TIME] . ", '" . $datum[0]['firstSpeechTime'] . "'),";
        }

        $minCustomerTime = $this->THistoryChatLog->query('SELECT id, t_histories_id, t_history_stay_logs_id, message_type, MIN(created) as firstSpeechTime FROM t_history_chat_logs as THistoryChatLog WHERE t_histories_id >= ' . $allData[0]['THistory']['id'] . ' AND t_histories_id <= ' . $allData[count($allData) - 1]['THistory']['id'] . ' AND (message_type = 1 OR message_type = 8) GROUP BY t_histories_id ORDER BY t_histories_id', false);
        foreach($minCustomerTime as $idx => $dat) {
          $queries .= "(" . $dat['THistoryChatLog']['id'] . ", " . $dat['THistoryChatLog']['t_histories_id'] . ", " . $this->THistoryChatLogTime->type[THistoryChatLogTime::FIRST_CONSUMER_CHAT_LOG_TIME] . ", '" . $dat[0]['firstSpeechTime'] . "'),";
        }

        $noticeChatTime = $this->THistoryChatLog->query('SELECT id, t_histories_id, message_type, notice_flg,created FROM t_history_chat_logs as THistoryChatLog WHERE message_type = 1 AND notice_flg = 1 AND t_histories_id = t_histories_id >= ' . $allData[0]['THistory']['id'] . ' AND t_histories_id <= ' . $allData[count($allData) - 1]['THistory']['id'] . ' GROUP BY t_histories_id ORDER BY t_histories_id desc', false);
        foreach($noticeChatTime as $ix => $dt) {
          $queries .= "(" . $dt['THistoryChatLog']['id'] . ", " . $dt['THistoryChatLog']['t_histories_id'] . ", " . $this->THistoryChatLogTime->type[THistoryChatLogTime::FIRST_NOTICE_CHAT_LOG_TIME] . ", '" . $dt['THistoryChatLog']['created'] . "'),";
        }

        unset($allData);
        unset($minTime);
        unset($minCustomerTime);
        unset($noticeChatTime);
        if(strpos($queries, '(') === false) {
          $offset += $limit;
          $this->printLog('skip. next : ' . $offset);
          continue;
        }
        $queries = rtrim($queries, ',');
        $queries .= ';';
        $this->printLog('INSERT count = ' . $offset + $limit);
        //$this->printLog($queries);
        $this->THistoryChatLogTime->query($queries, false);
        $offset += $limit;
        $this->printLog('next : ' . $offset);
        $this->THistoryChatLogTime->commit();
      }
    } catch (Exception $e) {
      $this->THistoryChatLogTime->rollback();
      $this->printLog('ERROR FOUND. message : ' . $e->getMessage());
    }
    $this->printLog('FINISHED');
  }

  private function printLog($msg)
  {
    $this->log($msg, self::LOG_INFO);
    $this->out($msg);
  }
}
