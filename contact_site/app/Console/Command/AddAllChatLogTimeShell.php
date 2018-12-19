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
    $limit = 1000;
    $offset = 0;
    try {
      $this->THistoryChatLogTime->begin();
      $count = $this->THistory->find('count');
      $loopCount = ceil($count / $limit);
      for ($i = 0; $i < $loopCount; $i++) {
        $allData = $this->THistory->find('all', array(
          'limit' => $limit,
          'offset' => $offset
        ));
        $queries = 'INSERT INTO `t_history_chat_log_times` VALUES ';
        foreach ($allData as $index => $data) {
          $minTime = $this->THistoryChatLog->query('SELECT id, t_histories_id, t_history_stay_logs_id, MIN(created) as firstSpeechTime FROM t_history_chat_logs as THistoryChatLog WHERE t_histories_id = ' . $data['THistory']['id'] . ' GROUP BY t_histories_id ORDER BY t_histories_id');
          if (!empty($minTime)) {
            //$this->printLog('chatLog is exists');
//            $this->THistoryChatLogTime->create();
            if (!empty($this->THistoryChatLogTime)) {
              $queries .= "(" . $minTime[0]['THistoryChatLog']['id'] . ", " . $minTime[0]['THistoryChatLog']['t_histories_id'] . ", " . $this->THistoryChatLogTime->type[THistoryChatLogTime::MIN_CHAT_LOG_TIME] . ", '" . $minTime[0][0]['firstSpeechTime'] . "'),";
//              $this->THistoryChatLogTime->set(array(
//                't_history_chat_logs_id' => $minTime[0]['THistoryChatLog']['id'],
//                't_histories_id' => $minTime[0]['THistoryChatLog']['t_histories_id'],
//                'type' => $this->THistoryChatLogTime->type[THistoryChatLogTime::MIN_CHAT_LOG_TIME],
//                'datetime' => $minTime[0][0]['firstSpeechTime']
//              ));
            }
            //$this->printLog('saving initial message time');
//            $this->THistoryChatLogTime->save();

            $minCustomerTime = $this->THistoryChatLog->query('SELECT id, t_histories_id, t_history_stay_logs_id, message_type, MIN(created) as firstSpeechTime FROM t_history_chat_logs as THistoryChatLog WHERE t_histories_id = ' . $data['THistory']['id'] . ' AND (message_type = 1 OR message_type = 8) GROUP BY t_histories_id ORDER BY t_histories_id');
            if (!empty($minCustomerTime)) {
//              $this->THistoryChatLogTime->create();
              $queries .= "(" . $minCustomerTime[0]['THistoryChatLog']['id'] . ", " . $minCustomerTime[0]['THistoryChatLog']['t_histories_id'] . ", " . $this->THistoryChatLogTime->type[THistoryChatLogTime::FIRST_CONSUMER_CHAT_LOG_TIME] . ", '" . $minCustomerTime[0][0]['firstSpeechTime'] . "'),";
//              $this->THistoryChatLogTime->set(array(
//                't_history_chat_logs_id' => $minCustomerTime[0]['THistoryChatLog']['id'],
//                't_histories_id' => $minCustomerTime[0]['THistoryChatLog']['t_histories_id'],
//                'type' => $this->THistoryChatLogTime->type[THistoryChatLogTime::FIRST_CONSUMER_CHAT_LOG_TIME],
//                'datetime' => $minCustomerTime[0][0]['firstSpeechTime']
//              ));
              //$this->printLog('saving firstSpeechTime');
//              $this->THistoryChatLogTime->save();
            }

            $noticeChatTime = $this->THistoryChatLog->query('SELECT id, t_histories_id, message_type, notice_flg,created FROM t_history_chat_logs as THistoryChatLog WHERE message_type = 1 AND notice_flg = 1 AND t_histories_id = ' . $data['THistory']['id'] . ' GROUP BY t_histories_id ORDER BY t_histories_id desc');
            if (!empty($noticeChatTime)) {
//              $this->THistoryChatLogTime->create();
              $queries .= "(" . $noticeChatTime[0]['THistoryChatLog']['id'] . ", " . $noticeChatTime[0]['THistoryChatLog']['t_histories_id'] . ", " . $this->THistoryChatLogTime->type[THistoryChatLogTime::FIRST_NOTICE_CHAT_LOG_TIME] . ", '" . $noticeChatTime[0]['THistoryChatLog']['created'] . "'),";
//              $this->THistoryChatLogTime->set(array(
//                't_history_chat_logs_id' => $noticeChatTime[0]['THistoryChatLog']['id'],
//                't_histories_id' => $noticeChatTime[0]['THistoryChatLog']['t_histories_id'],
//                'type' => $this->THistoryChatLogTime->type[THistoryChatLogTime::FIRST_NOTICE_CHAT_LOG_TIME],
//                'datetime' => $noticeChatTime[0]['THistoryChatLog']['created']
//              ));
//              $this->THistoryChatLogTime->save();
              //$this->printLog('saving first notice consumer message time');
            }
          }
        }
        $queries = rtrim($queries, ',');
        $queries .= ';';
        $this->printLog('INSERT count = ' . $offset + $limit);
        $this->THistoryChatLogTime->query($queries);
        $offset += $limit;
        $this->printLog('next : ' . $offset);
      }
    } catch (Exception $e) {
      $this->THistoryChatLogTime->rollback();
      $this->printLog('ERROR FOUND. message : ' . $e->getMessage());
    }
    $this->THistoryChatLogTime->commit();
    $this->printLog('FINISHED');
  }

  private function printLog($msg)
  {
    $this->log($msg, self::LOG_INFO);
    $this->out($msg);
  }
}
