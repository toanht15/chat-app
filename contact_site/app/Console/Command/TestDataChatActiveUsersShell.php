<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/07/11
 * Time: 22:44
 */

class TestDataChatActiveUsersShell extends AppShell {
  public $uses = array('THistoryChatActiveUsers','THistoryChatLog');

  private $beginDate;
  private $beginDate2;
  //private $dataCountPerHourForChat = 222;

  public function makeChatActiveUsers() {
    $startTHistoryChatLogsId = 1114758;
      for($day = 0; $day < 2601; $day++) {
        for ($i = 0; $i <= 4; $i++) {
          $createdObj = $this->THistoryChatLog->findById($startTHistoryChatLogsId);
          $this->beginDate = new DateTime($createdObj['THistoryChatLog']['created']);
          $baseBeginDate = clone $this->beginDate;
          echo 'create ' . $baseBeginDate->format('Y-m-d H:i:s') . PHP_EOL;
          for ($userId = 1; $userId <= 6; $userId++) {
            $this->THistoryChatActiveUsers->create();
            $this->THistoryChatActiveUsers->set(array(
              't_history_chat_logs_id' => $startTHistoryChatLogsId,
              'm_companies_id' => 1,
              'm_users_id' => $userId,
              'created' => $baseBeginDate->format('Y-m-d H:i:s'),
            ));
            $this->THistoryChatActiveUsers->save();
          }
          $startTHistoryChatLogsId = $startTHistoryChatLogsId + 9;
         }
      $startTHistoryChatLogsId = $startTHistoryChatLogsId + 11;
    }
  }
}