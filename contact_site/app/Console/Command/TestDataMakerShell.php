<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/07/11
 * Time: 22:44
 */

class TestDataMakerShell extends AppShell {
  public $uses = array('THistory','THistoryChatLog','THistoryStayLog','THistoryWidgetDisplays');

  private $companyId = 1;
  private $baseBeginDate;
  private $dataCountPerHour = 1;
  private $dataCountPerHourForChat = 5;
  private $dataCountPerDay = 1;
  private $baseUserId = 1234567890;

  public function makeLog() {
    $this->baseBeginDate = new DateTime('2017-01-01 00:00:00');
    for($count = 0; $count < 10000; $count++) {
      $tHistoryId = $this->makeHistoryLog();
      $stayLogIds = $this->makeStayLog($tHistoryId);
      $this->makeChatHistoryLog($tHistoryId, $stayLogIds);
      $this-> baseBeginDate->modify("+1 minute");
    }
  }

  private function makeHistoryLog() {
    $beginDate = clone $this->baseBeginDate;
    $createdDate = clone $this->baseBeginDate;
    $this->THistory->create();
    $this->THistory->set(array(
      'm_companies_id' => $this->companyId,
      'visitors_id' => $this->baseUserId,
      'ip_address' => "123.123.123.123",
      'tab_id' => "2017032810183667_lyXUbC52KMuvVqZRCIu6",
      'user_agent' => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36",
      'access_date' => $beginDate->modify("+5 second")->format('Y-m-d H:i:s'),
      'out_date' => $beginDate->modify("+30 second")->format('Y-m-d H:i:s'),
      'referrer_url' => "http://contact.sinclo/ScriptSettings",
      'created' => $createdDate->modify("+5 second")->format('Y-m-d H:i:s'),
      'created_user_id' => "1",
      'modified' => "",
      'modified_user_id' => "",
      'deleted' => "",
      'deleted_user_id' => ""
    ));
    $this->THistory->save();
    return $this->THistory->getLastInsertId();
  }

  private function makeChatHistoryLog($tHistoriesId, $tHistoryStayLogIds) {
    foreach($tHistoryStayLogIds as $index => $id) {
      $createdDatetime = clone $this->baseBeginDate;
      $created = $createdDatetime->modify("+5 second");
      for($distinction = 1; $distinction < 5; $distinction++) {
        $this->saveHistoryChatRecord($tHistoriesId, $id, null, "automessage", 3, $distinction, 0, 1, null, $created, $this->baseUserId);
        $this->saveHistoryChatRecord($tHistoriesId, $id, null, "syouhisyamessage", 1, $distinction, 1, 1, null, $created, $this->baseUserId);
        $this->saveHistoryChatRecord($tHistoriesId, $id, 1, "nyuusitu", 98, $distinction, 0, 1, null,$created, $this->baseUserId);
        $this->saveHistoryChatRecord($tHistoriesId, $id, 1, "konnichiwa", 2, $distinction, 0, 1, null,$created, $this->baseUserId);
        $this->saveHistoryChatRecord($tHistoriesId, $id, null, "syouhisyamessage", 1, $distinction, 0, 1, null, $created, $this->baseUserId);
        $this->saveHistoryChatRecord($tHistoriesId, $id, null, "syouhisyamessage", 1, $distinction, 0, 1, null, $created, $this->baseUserId);
        $this->saveHistoryChatRecord($tHistoriesId, $id, 1, "operetamessage", 2, $distinction, 0, 1, 2, $created, $this->baseUserId);
        $this->saveHistoryChatRecord($tHistoriesId, $id, 1, "taishitsu", 99, $distinction, 0, 1, null, $created, $this->baseUserId);
        $this->saveHistoryChatRecord($tHistoriesId, $id, null, "denialmessage", 4, $distinction, 0, 1, null, $created, $this->baseUserId);
      }
    $baseBeginDate2->modify("+1 second");
    $baseBeginDate3 = $baseBeginDate3 + 1;
    }
  }

  public function makeChatLog() {
    //return false;
    $this->beginDate = new DateTime("2017-01-02 00:00:00");
    $visitors_id = '2017032810183667';
    $startHistoriesId = "311482";
    for ($i = 0; $i < 50000; $i++) {
      echo 'create ' . $i . PHP_EOL;
      $createdObj = $this->THistory->findById($startHistoriesId);
      $created = new DateTime($createdObj['THistory']['access_date']);

      $this->saveHistoryChatRecord($startHistoriesId, null, "automessage", 3, $distinction, 0, 1, null,$created,$visitors_id);
      $this->saveHistoryChatRecord($startHistoriesId, null, "syouhisyamessage", 1, $distinction, 1, 1, null,$created,$visitors_id);
      /*$this->saveHistoryChatRecord($startHistoriesId, 1, "nyuusitu", 998, $distinction, 0, 1, null,$created,$visitors_id);
      $this->saveHistoryChatRecord($startHistoriesId, 1, "konnichiwa", 2, $distinction, 0, 1, null,$created,$visitors_id);
      $this->saveHistoryChatRecord($startHistoriesId, null, "syouhisyamessage", 1, $distinction, 0, 1, null,$created,$visitors_id);
      $this->saveHistoryChatRecord($startHistoriesId, 1, "operetamessage", 2, $distinction, 0, 1, null,$created,$visitors_id);
      $this->saveHistoryChatRecord($startHistoriesId, null, "syouhisyamessage", 1, $distinction, 0, 1, null,$created,$visitors_id);
      $this->saveHistoryChatRecord($startHistoriesId, 1, "operetamessage", 2, $distinction, 0, 1, 2,$created,$visitors_id);
      $this->saveHistoryChatRecord($startHistoriesId, 1, "taishitsu", 999, $distinction, 0, 1, null,$created,$visitors_id);
      $this->saveHistoryChatRecord($startHistoriesId, null, "denialmessage", 4, $distinction, 0, 1, null,$created,$visitors_id);*/
      $startHistoriesId++;
      $visitors_id = $visitors_id + 1;
      $startHistoriesId = $startHistoriesId + 5;
    }
  }

  /**
   * @param $startHistoriesId
   */
  private function saveHistoryChatRecord($tHistoriesId, $stayLogId, $userId, $message, $messageType, $messageDistinction, $messageReqFlg, $messageReadFlg, $achivementFlg, $createdDateTime, $visitors_id)
  {
    $createdDateTime->modify('+1 second');
    $this->THistoryChatLog->create();
    $this->THistoryChatLog->set(array(
      "t_histories_id" => $tHistoriesId,
      "t_history_stay_logs_id" => $stayLogId,
      "m_companies_id" => 1  ,
      "visitors_id" => $visitors_id,
      "m_users_id" => $userId,
      "message" => $message,
      "message_type" => $messageType,
      "message_distinction" => $messageDistinction,
      "message_request_flg" => $messageReqFlg,
      "message_read_flg" => $messageReadFlg,
      "achievement_flg" => $achivementFlg,
      "created" => $createdDateTime->format('Y-m-d H:i:s')
    ));
    $this->THistoryChatLog->save();
  }

  private function makeStayLog($tHistoriesId) {
    $insertIds = array();
    for($i = 0; $i < 6; $i++) {
      $createdDatetime = clone $this->baseBeginDate;
      $modifiedDatetime = clone $this->baseBeginDate;
      $this->THistoryStayLog->create();
      $this->THistoryStayLog->set(array(
        't_histories_id' => $tHistoriesId,
        'title' => "目次",
        'url' => "http://contact.sinclo/ScriptSettings/testpage",
        'stay_time' => "00:00:30",
        'del_flg' => "0",
        'created' => $createdDatetime->modify("+5 second")->format('Y-m-d H:i:s'),
        'created_user_id' => NULL,
        'modified' => $modifiedDatetime->modify("+35 second")->format('Y-m-d H:i:s'),
        'modified_user_id' => NULL,
      ));
      $this->THistoryStayLog->save();
      array_push($insertIds, $this->THistoryStayLog->getLastInsertId());
    }
    return $insertIds;
  }
}
