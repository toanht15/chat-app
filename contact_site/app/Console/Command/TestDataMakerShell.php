<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/07/11
 * Time: 22:44
 */

class TestDataMakerShell extends AppShell {
  public $uses = array('THistory','THistoryChatLog','THistoryStayLog','THistoryWidgetDisplays');

  //private $companyIds = array("1","2","3","4","5");
  private $companyId = 1;
  private $beginDate;
  private $dataCountPerHour = 1;
  private $dataCountPerHourForChat = 5;
  private $dataCountPerDay = 1;

  public function makeHistory() {
    //return false;
    $this->beginDate = new DateTime("2017-01-02 00:00:00");

    $baseBeginDate = clone $this->beginDate;
    $visitors_id = "2017032810183667";
    $companyId = "1";
    echo 'create ' . $baseBeginDate->format('Y-m-d H:i:s') . PHP_EOL;
    for ($i = 0; $i < 20 ; $i++) {
      for ($i3 = 0; $i3 < 1000000 ; $i3++) {
      $outDate = (clone $baseBeginDate);
      $this->THistory->create();
      $this->THistory->set(array(
        'm_companies_id' => $companyId,
        'visitors_id' => $visitors_id,
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
      $baseBeginDate->modify("+2 second");
      $visitors_id = $visitors_id + 1;
      }
      $baseBeginDate->modify("+2 second");
      $visitors_id = $visitors_id + 1;
      $companyId = $companyId + 1;
    }
  }

  public function makeHistoryStayLogs() {
    //return false;
    $this->beginDate2 = new DateTime("2017-01-02 00:00:00");
    $baseBeginDate3 = "1960";

    $baseBeginDate2 = clone $this->beginDate2;
    echo 'create ' . $baseBeginDate2->format('Y-m-d H:i:s') . PHP_EOL;
    for ($i3 = 0; $i3 < 15000000 ; $i3++) {
      for ($i2 = 0; $i2 < 2; $i2++) {
        $this->THistoryStayLog->create();
        $this->THistoryStayLog->set(array(
          't_histories_id' => $baseBeginDate3,
          'title' => "目次",
          'url' => "http://contact.sinclo/ScriptSettings/testpage",
          'stay_time' => "00:00:04",
          'del_flg' => "0",
          'created' => $baseBeginDate2->modify("+1 second")->format('Y-m-d H:i:s'),
          'created_user_id' => NULL,
          'modified' => $baseBeginDate2->modify("+1 second")->format('Y-m-d H:i:s'),
          'modified_user_id' => NULL,
        ));
        $this->THistoryStayLog->save();
        $baseBeginDate2->modify("+1 second");
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
      /*$this->saveHistoryChatRecord($startHistoriesId, 1, "nyuusitu", 98, $distinction, 0, 1, null,$created,$visitors_id);
      $this->saveHistoryChatRecord($startHistoriesId, 1, "konnichiwa", 2, $distinction, 0, 1, null,$created,$visitors_id);
      $this->saveHistoryChatRecord($startHistoriesId, null, "syouhisyamessage", 1, $distinction, 0, 1, null,$created,$visitors_id);
      $this->saveHistoryChatRecord($startHistoriesId, 1, "operetamessage", 2, $distinction, 0, 1, null,$created,$visitors_id);
      $this->saveHistoryChatRecord($startHistoriesId, null, "syouhisyamessage", 1, $distinction, 0, 1, null,$created,$visitors_id);
      $this->saveHistoryChatRecord($startHistoriesId, 1, "operetamessage", 2, $distinction, 0, 1, 2,$created,$visitors_id);
      $this->saveHistoryChatRecord($startHistoriesId, 1, "taishitsu", 99, $distinction, 0, 1, null,$created,$visitors_id);
      $this->saveHistoryChatRecord($startHistoriesId, null, "denialmessage", 4, $distinction, 0, 1, null,$created,$visitors_id);*/
      $startHistoriesId++;
      $visitors_id = $visitors_id + 1;
      $startHistoriesId = $startHistoriesId + 5;
    }
  }

  /**
   * @param $startHistoriesId
   */
  private function saveHistoryChatRecord($startHistoriesId, $userId, $message, $messageType, $messageDistinction, $messageReqFlg, $messageReadFlg, $achivementFlg, $createdDateTime, $visitors_id)
  {
    $createdDateTime->modify('+1 second');
    $this->THistoryChatLog->create();
    $this->THistoryChatLog->set(array(
        "t_histories_id" => $startHistoriesId,
        "t_history_stay_logs_id" => $startHistoriesId,
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
    $createdDateTime->modify('+1 second');
    $this->THistoryChatLog->save();
  }

  /**
   * @param $startHistoriesId
   */
  private function saveTHistoryChatActiveUsersRecord($startHistoriesId, $userId, $message, $messageType, $messageDistinction, $messageReqFlg, $messageReadFlg, $achivementFlg, &$createdDateTime)
  {
    $createdDateTime->modify('+1 second');
    $this->THistoryChatLog->create();
    $this->THistoryChatLog->set(array(
        "t_history_chat_logs_id" => $startHistoriesId,
        "m_companies_id" => $m_companies_id,
        "m_users_id" => $userId,
        "created" => $createdDateTime->format('Y-m-d H:i:s')
    ));
    $createdDateTime->modify('+1 second');
    $this->THistoryChatLog->save();
  }

  public function insertAll(){

    $this->beginDate = new DateTime("2015-01-01 00:00:00");
    $baseBeginDate = clone $this->beginDate;
    echo 'create ' . $baseBeginDate->format('Y-m-d H:i:s') . PHP_EOL;
    //echo 'create ' . $companyId . ' => ' . $i. PHP_EOL;
    //$outDate = (clone $baseBeginDate);
  for ($repeat = 1; $repeat < 10001; $repeat++) {
    $datas = [];
    for ($i = 0; $i < 1000 ; $i++) {
      //echo 'create ' . $companyId . ' => ' . $i. PHP_EOL;
      $outDate = (clone $baseBeginDate);
      $datas[] = array(
        'm_companies_id' => 1,
        'visitors_id' => "'2017032810183667'",
        'ip_address' => "'123.123.123.123'",
        'tab_id' => "'2017032810183667_lyXUbC52KMuvVqZRCIu6'",
        'user_agent' => "'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36'",
        'access_date' => "'".$baseBeginDate->modify('+1 second')->format('Y-m-d H:i:s')."'",
        'out_date' => "'".$outDate->modify("+10 second")->format('Y-m-d H:i:s')."'",
        'referrer_url' => "'http://contact.sinclo/ScriptSettings'",
        'created' => "'".$baseBeginDate->format('Y-m-d H:i:s')."'",
        'created_user_id' => "1",
        'modified' => "''",
        'modified_user_id' => "''",
        'deleted' => "''",
        'deleted_user_id' => "''"
      );
      $baseBeginDate->modify("+2 second");
    }

    //$this->log('data',LOG_DEBUG);
    //$this->log($datas,LOG_DEBUG);

    $table = 't_histories';
    $columns = array('id','m_companies_id','visitors_id','ip_address','tab_id','user_agent','access_date','out_date','referrer_url',
      'created','created_user_id','modified','modified_user_id','deleted','deleted_user_id');

    // SQL前半の作成
    $sql = 'INSERT INTO '.$table.' (';
    foreach ($columns as $column){
      // idはオートインクリメントなのでスキップ
      if($column == 'id'){
        continue;
      }
      $sql = $sql.$column.', ';
    }
    $sql = mb_substr($sql, 0, (mb_strlen($sql)-2));   // 最後のコンマを除いた文字列に整形
    $sql = $sql.') VALUES ';

    // SQL後半の挿入データ部分の作成
    foreach ($datas as $data){
      $sql = $sql.'(';
      foreach ($columns as $column){
        // idはオートインクリメントなのでスキップ
        if($column == 'id'){
          continue;
        }

        if(isset($data[$column])){
          $sql = $sql.$data[$column].', ';
        }else{
          $sql = $sql.'null, ';
        }
      }
      $sql = mb_substr($sql, 0, (mb_strlen($sql)-2));
      $sql = $sql.'),';
    }
    $sql = mb_substr($sql, 0, (mb_strlen($sql)-1)); // 最後のコンマを除いた文字列に整形
    // 環境に合わせてSQL実行文章書けば保存完了
    //$this->log($sql,LOG_DEBUG);
    $this->THistory->query($sql);
    $this->log(" insert ".($repeat * 1000)." records", LOG_DEBUG);
    }
  }
}