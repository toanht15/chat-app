<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/07/11
 * Time: 22:44
 */

class EditNoticeDataMakerShell extends AppShell {
  public $uses = array('THistory','THistoryChatLog','MCompany');

  public function noticeFlgEdit() {
    $m_companies_id = $this->MCompany->find('all');
    foreach($m_companies_id as $key3 => $company) {

    $check = "SELECT chat.*,( CASE  WHEN chat.cmp = 0 AND chat.cus > 0 AND chat.unread > 0 AND (chat.cus > chat.sry + chat.auto_speech) THEN '未入室' END ) AS type
    FROM
    (
      SELECT t_histories_id,m_companies_id,message_type,created,message_read_flg, COUNT(*) AS count,
      SUM(CASE WHEN message_type = 98 THEN 1 ELSE 0 END) cmp,SUM(CASE WHEN message_type = 3 THEN 1 ELSE 0 END) auto_message,
      SUM(CASE WHEN message_type = 4 THEN 1 ELSE 0 END) sry, SUM(CASE WHEN message_type = 1 THEN 1 ELSE 0 END) cus,SUM(CASE WHEN message_type = 1 AND message_read_flg = 0 THEN 1 ELSE 0 END) unread,
      SUM(CASE WHEN message_type = 5 THEN 1 ELSE 0 END) auto_speech FROM t_history_chat_logs AS THistoryChatLog where m_companies_id = ".$company['MCompany']['id']." GROUP BY t_histories_id ORDER BY t_histories_id
     )
    AS chat order by t_histories_id asc";

    $check = $this->THistory->query($check);
    $idList = [];
    foreach($check as $key2 => $value) {
      $idList[] = $value['chat']['t_histories_id'];
    }

    $chatLogIdData = $this->THistoryChatLog->find('all', [
      'table' => '(SELECT id,t_histories_id,message_type FROM t_history_chat_logs)',
      'fields' => [
        'id',
        't_histories_id',
        'message_type',
        'created'
      ],
      'conditions' => [
        't_histories_id' => $idList,
        'm_companies_id' => $company['MCompany']['id']
      ],
      'order' => 'created'
    ]);

    $logsIdList = [];
    $check = "";
    foreach($chatLogIdData as $key => $val) {
      if(!empty($chatLogIdData[$key - 1]) && ($chatLogIdData[$key - 1]['THistoryChatLog']['t_histories_id'] != $val['THistoryChatLog']['t_histories_id'])) {
        $check = "";
      }
      if($val['THistoryChatLog']['message_type'] == 1) {
        //次のメッセージのt_hisotries_idチェック
        if(!empty($chatLogIdData[$key + 1]) && !empty($chatLogIdData[$key + 2]) &&
          $chatLogIdData[$key + 1]['THistoryChatLog']['t_histories_id'] == $val['THistoryChatLog']['t_histories_id'] &&
          $chatLogIdData[$key + 2]['THistoryChatLog']['t_histories_id'] == $val['THistoryChatLog']['t_histories_id']) {
          //message_type = 1の次のメッセージタイプが4,5ではないとき
          if(($chatLogIdData[$key + 1]['THistoryChatLog']['message_type'] != 4 && $chatLogIdData[$key + 1]['THistoryChatLog']['message_type'] != 5) &&
            ($chatLogIdData[$key + 2]['THistoryChatLog']['message_type'] != 4 && $chatLogIdData[$key + 2]['THistoryChatLog']['message_type'] != 5) &&
             ($check != 'true')) {
            //$noticeChatTime[$val['THistoryChatLog']['t_histories_id']] = $val['THistoryChatLog']['created'];
            //$saveNoticeChatTime =  $val['THistoryChatLog']['created'];
            $logsIdList[] = $val['THistoryChatLog']['id'];
            $logId = $val['THistoryChatLog']['id'];
            $check = 'true';
          }
        }
        //次のメッセージのt_hisotries_idチェック
        else if(!empty($chatLogIdData[$key + 1]) && $chatLogIdData[$key + 1]['THistoryChatLog']['t_histories_id'] == $val['THistoryChatLog']['t_histories_id']) {
          //message_type = 1の次のメッセージタイプが4,5ではないとき
          if($chatLogIdData[$key + 1]['THistoryChatLog']['message_type'] != 4 && $chatLogIdData[$key + 1]['THistoryChatLog']['message_type'] != 5 && $check != 'true') {
            //$noticeChatTime[$val['THistoryChatLog']['t_histories_id']] = $val['THistoryChatLog']['created'];
            //$saveNoticeChatTime =  $val['THistoryChatLog']['created'];
            $logsIdList[] = $val['THistoryChatLog']['id'];
            $logId = $val['THistoryChatLog']['id'];
            $check = 'true';
          }
        }
        //次のメッセージのt_hisotries_idチェック
        else if(!empty($chatLogIdData[$key + 1]) && $chatLogIdData[$key + 1]['THistoryChatLog']['t_histories_id'] != $val['THistoryChatLog']['t_histories_id'])  {
          if($chatLogIdData[$key - 1]['THistoryChatLog']['message_type'] != 1 && $check != 'true') {
            $logsIdList[] = $val['THistoryChatLog']['id'];
            $noticeChatTime[$val['THistoryChatLog']['t_histories_id']] = $val['THistoryChatLog']['created'];
          }
          else {
            //$noticeChatTime[$val['THistoryChatLog']['t_histories_id']] = $saveNoticeChatTime;
            //$logsIdList[] =  $logId;
          }
        }
        else if(empty($chatLogIdData[$key + 1]))  {
          if($chatLogIdData[$key - 1]['THistoryChatLog']['message_type'] != 1 && $check != 'true') {
            //$noticeChatTime[$val['THistoryChatLog']['t_histories_id']] = $val['THistoryChatLog']['created'];
            $logsIdList[] = $val['THistoryChatLog']['id'];
          }
          else {
            //$noticeChatTime[$val['THistoryChatLog']['t_histories_id']] = $saveNoticeChatTime;
            //$logsIdList[] =  $logId;
          }
        }
      }
    }

    $data = array(
      'THistoryChatLog.notice_flg' => '1',
    );
    $conditions = array(
      'THistoryChatLog.id' => $logsIdList,
      'THistoryChatLog.m_companies_id' => $company['MCompany']['id']
    );
    $this->THistoryChatLog->updateAll($data, $conditions);
  }
  }
}