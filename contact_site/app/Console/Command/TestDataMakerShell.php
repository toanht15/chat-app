<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/07/11
 * Time: 22:44
 */

class TestDataMakerShell extends AppShell {
  public $uses = array('THistory','THistoryChatLog','THistoryWidgetDisplays');

  private $companyIds = array("1");
  private $beginDate;
  private $dataCountPerHour = 1;
  private $dataCountPerHourForChat = 2000000;
  private $dataCountPerDay = 1;

  public function makeHistory() {
    return false;
    $this->beginDate = new DateTime("2015-01-01 00:00:00");

    foreach($this->companyIds as $companyId) {
      for($day = 0; $day < $this->dataCountPerDay; $day++) {
        for ($hour = 0; $hour < 24; $hour++) {
          $baseBeginDate = clone $this->beginDate;
          echo 'create ' . $baseBeginDate->format('Y-m-d H:i:s') . PHP_EOL;
          for ($i = 0; $i < $this->dataCountPerHour; $i++) {
            //echo 'create ' . $companyId . ' => ' . $i. PHP_EOL;
            $outDate = (clone $baseBeginDate);
            $this->THistory->create();
            $this->THistory->set(array(
                'm_companies_id' => $companyId,
                'visitors_id' => "2017032810183667",
                'ip_address' => "123.123.123.123",
                'tab_id' => "2017032810183667_lyXUbC52KMuvVqZRCIu6",
                'user_agent' => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36",
                'access_date' => $baseBeginDate->modify("+1 second")->format('Y-m-d H:i:s'),
                'out_date' => $outDate->modify("+10 second")->format('Y-m-d H:i:s'),
                'referrer_url' => "http://contact.sinclo/ScriptSettings",
                'created' => $baseBeginDate->format('Y-m-d H:i:s'),
                'created_user_id' => "1",
                'modified' => "",
                'modified_user_id' => "",
                'deleted' => "",
                'deleted_user_id' => ""
            ));
            $this->THistory->save();
            $baseBeginDate->modify("+10 second");
          }
          if($hour !== 23) {
            $this->beginDate->modify('+1 hour');
          } else {
            $this->beginDate->modify('+2 hour');
          }
        }
      }
    }
  }

  public function makeChatLog() {
    return false;
    $this->beginDate = new DateTime("2015-01-01 10:00:00");
    $startHistoriesId = 19172;
    foreach($this->companyIds as $companyId) {
      for ($i = 0; $i < $this->dataCountPerHourForChat; $i++) {
        echo 'create ' . $i . PHP_EOL;
        $createdObj = $this->THistory->findById($startHistoriesId);
        $created = new DateTime($createdObj['THistory']['access_date']);

        for ($distinction = 1; $distinction <= 5; $distinction++) {
          if($distinction === 1) {
            $this->saveHistoryChatRecord($startHistoriesId, null, "automessage", 3, $distinction, 0, 1, null,$created);
          }
          $this->saveHistoryChatRecord($startHistoriesId, null, "syouhisyamessage", 1, $distinction, 1, 1, null,$created);
          $this->saveHistoryChatRecord($startHistoriesId, 1, "nyuusitu", 98, $distinction, 0, 1, null,$created);
          $this->saveHistoryChatRecord($startHistoriesId, 1, "konnichiwa", 2, $distinction, 0, 1, null,$created);
          $this->saveHistoryChatRecord($startHistoriesId, null, "syouhisyamessage", 1, $distinction, 0, 1, null,$created);
          $this->saveHistoryChatRecord($startHistoriesId, 1, "operetamessage", 2, $distinction, 0, 1, null,$created);
          $this->saveHistoryChatRecord($startHistoriesId, null, "syouhisyamessage", 1, $distinction, 0, 1, null,$created);
          $this->saveHistoryChatRecord($startHistoriesId, null, "syouhisyamessage", 1, $distinction, 0, 1, null,$created);
          $this->saveHistoryChatRecord($startHistoriesId, 1, "operetamessage", 2, $distinction, 0, 1, 2,$created);
          $this->saveHistoryChatRecord($startHistoriesId, 1, "taishitsu", 99, $distinction, 0, 1, null,$created);
        }
        $startHistoriesId++;
      }
    }
  }

  /**
   * @param $startHistoriesId
   */
  private function saveHistoryChatRecord($startHistoriesId, $userId, $message, $messageType, $messageDistinction, $messageReqFlg, $messageReadFlg, $achivementFlg, &$createdDateTime)
  {
    $createdDateTime->modify('+1 second');
    $this->THistoryChatLog->create();
    $this->THistoryChatLog->set(array(
        "t_histories_id" => $startHistoriesId,
        "t_history_stay_logs_id" => $startHistoriesId,
        "visitors_id" => "2017032810183667",
        "m_users_id" => $userId,
        "message" => $message,
        "message_type" => $messageType,
        "message_distinction" => $messageDistinction,
        "message_request_flg" => $messageReqFlg,
        "message_read_flg" => $messageReadFlg,
        "achievement_flg" => $achivementFlg,
        "created" => $createdDateTime->format('Y-m-d H:i:s')
    ));
    $createdDateTime->modify('+1 second');
    $this->THistoryChatLog->save();
  }

}