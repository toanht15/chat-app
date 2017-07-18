<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/07/11
 * Time: 22:44
 */

class TestDataMakerShell extends AppShell {
  public $uses = array('THistory','THistoryChatLog','THistoryWidgetDisplays');

  private $companyIds = array("1","2","3","4","5");
  private $beginDate;
  private $dataCountPerHour = 100;
  private $dataCountPerDay = 100;

  public function makeHistory() {
    return false;
    $this->beginDate = new DateTime("2017-05-01 00:00:00");

    foreach($this->companyIds as $companyId) {
      for($day = 0; $day < $this->dataCountPerDay; $day++) {
        for ($hour = 0; $hour < 24; $hour++) {
          for ($i = 0; $i < $this->dataCountPerHour; $i++) {
            echo 'create ' . $companyId . ' => ' . $i. PHP_EOL;
            $this->THistory->create();
            $this->THistory->set(array(
                'm_companies_id' => $companyId,
                'visitors_id' => "2017032810183667",
                'ip_address' => "123.123.123.123",
                'tab_id' => "2017032810183667_lyXUbC52KMuvVqZRCIu6",
                'user_agent' => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36",
                'access_date' => $this->beginDate->format('Y-m-d H:i:s'),
                'out_date' => $this->beginDate->modify("+10 second")->format('Y-m-d H:i:s'),
                'referrer_url' => "http://contact.sinclo/ScriptSettings",
                'created' => (new Datetime())->format('Y-m-d H:i:s'),
                'created_user_id' => "1",
                'modified' => "",
                'modified_user_id' => "",
                'deleted' => "",
                'deleted_user_id' => ""
            ));
            $this->THistory->save();
          }
          $this->beginDate->modify('+1 hour');
        }
        $this->beginDate->modify('+1 day');
      }
    }
  }

  public function makeChatLog() {
    return false;
    $this->beginDate = new DateTime("2017-05-01 00:00:00");
    $startHistoriesId = 10808;
    foreach($this->companyIds as $companyId) {
      for($day = 0; $day < $this->dataCountPerDay; $day++) {
        for ($hour = 0; $hour < 24; $hour++) {
          for ($i = 0; $i < $this->dataCountPerHour; $i++) {
            echo 'create ' . $companyId . ' => ' . $i. PHP_EOL;
            for ($distinction = 1; $distinction <= 10; $distinction++) {
              if($distinction === 1) {
                $this->saveHistoryChatRecord($startHistoriesId, null, "automessage", 3, $distinction, 0, 1, null);
              }
              $this->saveHistoryChatRecord($startHistoriesId, null, "syouhisyamessage", 1, $distinction, 1, 1, null);
              $this->saveHistoryChatRecord($startHistoriesId, 1, "nyuusitu", 98, $distinction, 0, 1, null);
              $this->saveHistoryChatRecord($startHistoriesId, 1, "konnichiwa", 2, $distinction, 0, 1, null);
              $this->saveHistoryChatRecord($startHistoriesId, null, "syouhisyamessage", 1, $distinction, 0, 1, null);
              $this->saveHistoryChatRecord($startHistoriesId, 1, "operetamessage", 2, $distinction, 0, 1, null);
              $this->saveHistoryChatRecord($startHistoriesId, null, "syouhisyamessage", 1, $distinction, 0, 1, null);
              $this->saveHistoryChatRecord($startHistoriesId, 1, "operetamessage", 2, $distinction, 0, 1, 2);
              $this->saveHistoryChatRecord($startHistoriesId, null, "syouhisyamessage", 1, $distinction, 0, 1, null);
              $this->saveHistoryChatRecord($startHistoriesId, 1, "taishitsu", 99, $distinction, 0, 1, null);
            }
            $startHistoriesId++;
          }
          $this->beginDate->modify('+1 hour');
        }
        $this->beginDate->modify('+1 day');
      }
    }
  }

  /**
   * @param $startHistoriesId
   */
  private function saveHistoryChatRecord($startHistoriesId, $userId, $message, $messageType, $messageDistinction, $messageReqFlg, $messageReadFlg, $achivementFlg)
  {
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
        "created" => $this->beginDate->format('Y-m-d H:i:s')
    ));
    $this->THistoryChatLog->save();
  }

}