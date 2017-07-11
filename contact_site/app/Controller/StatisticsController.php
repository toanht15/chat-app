<?php
/**
 * StatisticsController controller.
 * 統計機能
 */
class StatisticsController extends AppController {

  public $uses = ['THistory','THistoryWidgetDisplays'];

  public $chatMessageType = [
    'messageType' => [
      'consumerMessage' => 1,
      'operatorMessage' => 2,
      'autoMessage' => 3,
      'denial' => 4,
      'enteringRoom' => 98,
      'exit' => 99
    ],
    'requestFlg' => [
      'invalid' => 0,
      'effectiveness' => 1
    ],
    'achievementFlg' => [
      'invalid' => 1,
      'effectiveness' => 2
    ]
  ];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', '統計機能');
  }

  /* *
   * オペレーター統計
   * @return void
   * */
  public function forOperator() {
  }

    public function index() {
  }

  /* *
   * チャット統計
   * @return void
   * */
  public function forChat() {
    Configure::write('debug', 2);
    if($this->request->is('post')) {
      if ($this->THistory->validates() ) {
        //$this->log($this->request->data,LOG_DEBUG);
        $date = $this->request->data['selectName1'];
        //月別の場合
        if($date == '月別'){
          $name = '月別';
          $type = $this->request->data['selectName2'];
          $data = $this->calculateMonthlyData($type);
        }
        //日別の場合
        else if($date == '日別'){
          $name = '日別';
          $type = $this->request->data['selectName3'];
          $data = $this->calculateDailyData($type);
        }
        //時別の場合
        else if($date == '時別') {
          $name = '時別';
          $type = $this->request->data['datefilter'].' 00:00:00';
          //$this->log('type',LOG_DEBUG);
          //$this->log($type,LOG_DEBUG);
          $data = $this->calculateHourlyData($type);
          //$this->log($data,LOG_DEBUG);
        }
      }
    }
    else {
      $date = '日別';
      $name = '日別';
      $type = date("Y-m");
      $data = $this->calculateDailyData($type);
    }
    $this->set('name',$name);
    $this->set('date',$date);
    $this->set('daylyEndDate',date("d",strtotime('last day of' .$type)));
    $this->set('type',$type);
    $this->set('data',$data);

  }

  //月別の場合
  public function calculateMonthlyData($data){
    $start = $data.'-01';
    $end = $data.'-12';
    $startDate = strtotime('first day of' .$start);
    $endMonth = strtotime('last day of' .$start);
    $endDate = strtotime('last day of' .$end);
    $correctStartDate = date("Y-m-d 00:00:00",$startDate);
    $correctEndDate = date("Y-m-d 23:59:59",$endDate);
    $date_format = "%Y-%m";
    $period = "month";
    $baseData = [];
    $baseTimeData = [];

    //array_mergeで使うためのデータを作成
    while($startDate <= $endDate){
      $baseData = $baseData + array(date('Y-m',$startDate) => 0);
      $baseTimeData = $baseTimeData + array(date('Y-m',$startDate) => '00:00:00');
      $startDate = strtotime("+1 month", $startDate);
    }
    $startDate = strtotime('first day of' .$start);

    $sqlData =$this->summarySql($date_format,$baseData,$baseTimeData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period);
    return $sqlData;
  }

  //日別の場合
  public function calculateDailyData($data){
    $startDate = strtotime('first day of' .$data);
    $endDate = strtotime('last day of' .$data);
    $correctStartDate = date("Y-m-d 00:00:00",$startDate);
    $correctEndDate = date("Y-m-d 23:59:59",$endDate);
    $date_format = "%Y-%m-%d";
    $baseData = [];
    $baseTimeData = [];
    $period = 'day';
    //$this->log($correctStartDate,LOG_DEBUG);
    //$this->log($correctEndDate,LOG_DEBUG);

    //array_mergeで使うためのデータを作成
    while($startDate <= $endDate){
      $baseData = $baseData + array(date("Y-m-d",$startDate) => 0);
      $baseTimeData = $baseTimeData + array(date("Y-m-d",$startDate) => "00:00:00");
      $startDate = strtotime("+1 day", $startDate);
    }
    $startDate = strtotime('first day of' .$data);

    $sqlData =$this->summarySql($date_format,$baseData,$baseTimeData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period);
    return $sqlData;
  }

  //時別の場合
  public function calculateHourlyData($data){
    $startDate = strtotime($data);
    $endDate = strtotime("+1 day",$startDate.' 00:00:00');
    $correctStartDate = date("Y-m-d H:00:00",$startDate);
    $correctEndDate = date("Y-m-d H:59:59",$endDate);
    $date_format = "%H:00";
    $baseData = [];
    $baseTimeData = [];
    $period = 'hour';

    //array_mergeで使うためのデータを作成
    while($startDate <= $endDate){
      $baseData = $baseData + array(date("H:00",$startDate) => 0);
      $baseTimeData = $baseTimeData + array(date("H:00",$startDate) => '00:00:00');
      $startDate = strtotime("+1 hour", $startDate);
    }
    $startDate = strtotime($data);

    $sqlData =$this->summarySql($date_format,$baseData,$baseTimeData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period);
    return $sqlData;

  }


  public function summarySql($date_format,$baseData,$baseTimeData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period) {

    //アクセス件数件数
    $this->log("BEGIN getAccessData : ".$this->getDateWithMilliSec(),LOG_DEBUG);
    $accessDatas = $this->getAccessData($date_format,$baseData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period);
    $this->log("END   getAccessData : ".$this->getDateWithMilliSec(),LOG_DEBUG);

    //ウィジェット表示件数
    $this->log("BEGIN getWidgetData : ".$this->getDateWithMilliSec(),LOG_DEBUG);
    $widgetDatas = $this->getWidgetData($date_format,$baseData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period);
    $this->log("END   getWidgetData : ".$this->getDateWithMilliSec(),LOG_DEBUG);

    //チャットリクエスト件数
    $this->log("BEGIN getRequestData : ".$this->getDateWithMilliSec(),LOG_DEBUG);
    $requestDatas = $this->getRequestData($date_format,$baseData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period);
    $this->log("END   getRequestData : ".$this->getDateWithMilliSec(),LOG_DEBUG);

    //チャット応答件数,チャット応答率　書き換え必要
    $this->log("BEGIN getResponseData : ".$this->getDateWithMilliSec(),LOG_DEBUG);
    $responseDatas = $this->getResponseData($date_format,$baseData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period,$requestDatas['requestNumberData'],$requestDatas['allRequestNumberData']);
    $this->log("END   getResponseData : ".$this->getDateWithMilliSec(),LOG_DEBUG);

    //チャット有効件数、チャット有効率、チャット拒否件数
    $this->log("BEGIN getCoherentData : ".$this->getDateWithMilliSec(),LOG_DEBUG);
    $coherentDatas = $this->getCoherentData($date_format,$baseData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period,$requestDatas['requestNumberData'],$requestDatas['allRequestNumberData']);
    $this->log("END   getCoherentData : ".$this->getDateWithMilliSec(),LOG_DEBUG);

    //平均チャットリクエスト時間
    $this->log("BEGIN getAvgRequestTimeData : ".$this->getDateWithMilliSec(),LOG_DEBUG);
    $avgRequestTimeDatas = $this->getAvgRequestTimeData($date_format,$baseData,$baseTimeData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period);
    $this->log("END   getAvgRequestTimeData : ".$this->getDateWithMilliSec(),LOG_DEBUG);

    //平均消費者待機時間
    $this->log("BEGIN getConsumerWatingAvgTimeData : ".$this->getDateWithMilliSec(),LOG_DEBUG);
    $consumerWatingAvgTimeDatas = $this->getConsumerWatingAvgTimeData($date_format,$baseData,$baseTimeData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period);
    $this->log("END   getConsumerWatingAvgTimeData : ".$this->getDateWithMilliSec(),LOG_DEBUG);

    //平均応答時間
    $this->log("BEGIN getResponseAvgTimeData : ".$this->getDateWithMilliSec(),LOG_DEBUG);
    $responseAvgTimeData = $this->getResponseAvgTimeData($date_format,$baseData,$baseTimeData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period);
    $this->log("END   getResponseAvgTimeData : ".$this->getDateWithMilliSec(),LOG_DEBUG);

    $this->log('終わり',LOG_DEBUG);

    return ["accessDatas" => $accessDatas,"widgetDatas" => $widgetDatas,"requestDatas" => $requestDatas,'responseDatas' => $responseDatas,
    "coherentDatas" => $coherentDatas,"avgRequestTimeDatas" => $avgRequestTimeDatas,"consumerWatingAvgTimeDatas" => $consumerWatingAvgTimeDatas,
    "responseAvgTimeData" => $responseAvgTimeData];
  }

  public function getAccessData($date_format,$baseData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period) {
    $accessNumberData = [];

    //アクセス件数
    $access = "SELECT date_format(th.access_date, ?) as date, count(th.id) FROM t_histories as th where th.access_date
    between ? and ? and th.m_companies_id = ? group by date_format(th.access_date, ?)";
    $accessNumber = $this->THistory->query($access, array($date_format,$correctStartDate,$correctEndDate,$this->userInfo['MCompany']['id'],$date_format));

    foreach($accessNumber as $k => $v) {
      $accessNumberData =  $accessNumberData + array($v[0]['date'] => $v[0]['count(th.id)']);
    }

    //アクセス件数
    $accessNumberData = array_merge($baseData,$accessNumberData);

    //アクセス件数合計値
    $allAccessNumberData = array_sum($accessNumberData);

    return ['accessNumberData' => $accessNumberData,'allAccessNumberData' => $allAccessNumberData];

  }

  public function getWidgetData($date_format,$baseData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period) {
    $widgetNumberData =[];

    //ウィジェット表示件数
    $widget = "SELECT date_format(tw.created, ?) as date,count(tw.id) FROM t_history_widget_displays as tw where tw.created between
    ? and ? and tw.m_companies_id = ? group by date_format(tw.created, ?)";
    $widgetNumber = $this->THistoryWidgetDisplays->query($widget, array($date_format,$correctStartDate,$correctEndDate,$this->userInfo['MCompany']['id'],$date_format));

    foreach($widgetNumber as $k => $v) {
      $widgetNumberData =  $widgetNumberData + array($v[0]['date'] => $v[0]['count(tw.id)']);
    }

    //ウィジェット件数
    $widgetNumberData = array_merge($baseData,$widgetNumberData);

    //ウィジェット件数合計値
    $allWidgetNumberData = array_sum($widgetNumberData);

    return['widgetNumberData' => $widgetNumberData,'allWidgetNumberData' => $allWidgetNumberData];
  }

  public function getRequestData($date_format,$baseData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period) {
    $requestNumberData = [];

    //チャットリクエスト件数
    $requestNumber = "SELECT date_format(th.access_date, ?) as date, count(th.id)
    FROM t_histories as th
    LEFT JOIN (SELECT t_histories_id,message_request_flg FROM t_history_chat_logs where message_request_flg = ? ) as t_history_chat_logs
    ON t_history_chat_logs.t_histories_id = th.id
    where th.access_date between ? and ? and t_history_chat_logs.message_request_flg = ? and th.m_companies_id = ?
    group by date_format(th.access_date, ?)";

    $requestNumber = $this->THistory->query($requestNumber, array($date_format,$this->chatMessageType['requestFlg']['effectiveness'],$correctStartDate,$correctEndDate,$this->chatMessageType['requestFlg']['effectiveness'],$this->userInfo['MCompany']['id'],$date_format));
    foreach($requestNumber as $k => $v) {
      $requestNumberData =  $requestNumberData + array($v[0]['date'] => $v[0]['count(th.id)']);
    }

    //チャットリクエスト件数
    $requestNumberData = array_merge($baseData,$requestNumberData);

    //チャットリクエスト件数合計値
    $allRequestNumberData = array_sum($requestNumberData);

    return['requestNumberData' => $requestNumberData,'allRequestNumberData' => $allRequestNumberData];

  }

  public function getResponseData($date_format,$baseData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period,$requestNumberData,$allRequestNumberData) {
    $responseNumberData = [];
    $responseRate = [];

    //応対件数
    $response = "SELECT date_format(th.access_date, ?) as date, count(distinct message_distinction,t_histories_id)  FROM t_histories as th LEFT JOIN
    (SELECT t_histories_id,message_type,message_distinction FROM t_history_chat_logs where message_type = ?) as t_history_chat_logs ON
    t_history_chat_logs.t_histories_id = th.id where t_history_chat_logs.message_type = ? and
    th.access_date between ? and ? and th.m_companies_id = ? group by date_format(th.access_date,?)";
    $responseNumber = $this->THistory->query($response, array($date_format,$this->chatMessageType['messageType']['enteringRoom'],$this->chatMessageType['messageType']['enteringRoom'],$correctStartDate,$correctEndDate,$this->userInfo['MCompany']['id'],$date_format));
    //$this->log('こここここおおっこおこｋ',LOG_DEBUG);
    //$this->log($responseNumber,LOG_DEBUG);
    //$this->log($requestNumberData,LOG_DEBUG);
    foreach($responseNumber as $k => $v) {
      $responseRate = $responseRate + array($v[0]['date'] => round($v[0]['count(distinct message_distinction,t_histories_id)']/$requestNumberData[$v[0]['date']]*100));
      $responseNumberData =  $responseNumberData + array($v[0]['date'] => $v[0]['count(distinct message_distinction,t_histories_id)']);
    }

    //チャット応答率
    $responseRate = array_merge($baseData,$responseRate);

    //チャット応答件数
    $responseNumberData = array_merge($baseData,$responseNumberData);

    //応対件数合計値
    $allResponseNumberData = array_sum($responseNumberData);

    //合計チャット応答率
    $allResponseRate = round($allResponseNumberData/$allRequestNumberData*100);

    return ['responseRate' => $responseRate,'responseNumberData' => $responseNumberData,'allResponseNumberData' => $allResponseNumberData,'allResponseRate' => $allResponseRate];
  }

  public function getCoherentData($date_format,$baseData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period,$requestNumberData,$allRequestNumberData) {
    $effectivenessNumberData = [];
    $denialNumberData = [];
    $effectivenessRate = [];

    //チャット有効件数,チャット拒否件数
    $effectiveness = "SELECT date_format(th.access_date, ?) as date, count(th.id),SUM(case when t_history_chat_logs.achievement_flg = ? THEN 1 ELSE 0 END) effectiveness,
    SUM(case when t_history_chat_logs.message_type = ? THEN 1 ELSE 0 END) denial FROM t_histories as th LEFT JOIN
    t_history_chat_logs ON t_history_chat_logs.t_histories_id = th.id where  th.access_date between
     ? and ? and (t_history_chat_logs.achievement_flg = ? or t_history_chat_logs.message_type = ?)
    and th.m_companies_id = ? group by date_format(th.access_date,?)";
    $effectiveness = $this->THistory->query($effectiveness, array($date_format,$this->chatMessageType['achievementFlg']['effectiveness'],$this->chatMessageType['messageType']['denial'],
      $correctStartDate,$correctEndDate,$this->chatMessageType['achievementFlg']['effectiveness'],$this->chatMessageType['messageType']['denial'],$this->userInfo['MCompany']['id'],$date_format));
    if($effectiveness[0][0]['count(th.id)'] == 0) {
      $effectiveness[0][0]['effectiveness'] = 0;
      $effectiveness[0][0]['denial'] = 0;
    }

    foreach($effectiveness as $k => $v) {
      $effectivenessNumberData =  $effectivenessNumberData + array($v[0]['date'] => $v[0]['effectiveness']);
      $effectivenessRate = $effectivenessRate + array($v[0]['date'] => round($v[0]['effectiveness']/$requestNumberData[$v[0]['date']]*100));
      $denialNumberData =  $denialNumberData + array($v[0]['date'] => $v[0]['denial']);
    }

    //チャット有効件数
    $effectivenessNumberData = array_merge($baseData,$effectivenessNumberData);

    //チャット拒否件数
    $denialNumberData = array_merge($baseData,$denialNumberData);

    //チャット有効率
    $effectivenessRate = array_merge($baseData,$effectivenessRate);

    //有効件数合計値
    $allEffectivenessNumberData = array_sum($effectivenessNumberData);

    //拒否件数合計値
    $allDenialNumberData = array_sum($denialNumberData);

    //合計有効率
    $allEffectivenessRate = $allEffectivenessNumberData/$allRequestNumberData*100;

    return ['effectivenessNumberData' => $effectivenessNumberData,'denialNumberData' => $denialNumberData,'effectivenessRate' => $effectivenessRate,
    'allEffectivenessNumberData' => $allEffectivenessNumberData,'allDenialNumberData' => $allDenialNumberData,'allEffectivenessRate' => $allEffectivenessRate];

  }

  public function getAvgRequestTimeData($date_format,$baseData,$baseTimeData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period) {
    $requestAvgTime = [];
    $avgForcalculation = [];
    $effectivenessRate = [];

    //平均チャットリクエスト時間
    $requestTime = "SELECT date_format(th.access_date, ?) as date, AVG(UNIX_TIMESTAMP(t_history_chat_logs.created)
      - UNIX_TIMESTAMP(th.access_date)) as average FROM t_histories as th LEFT JOIN (SELECT t_histories_id,message_request_flg,
      created FROM t_history_chat_logs where message_request_flg = ? group by t_histories_id) as
      t_history_chat_logs ON t_history_chat_logs.t_histories_id = th.id where th.access_date between ? and ?
       and t_history_chat_logs.message_request_flg = ? and th.m_companies_id = ? group by date_format(th.access_date,?)";

    $requestTime = $this->THistory->query($requestTime, array($date_format,$this->chatMessageType['requestFlg']['effectiveness'],$correctStartDate,$correctEndDate,$this->chatMessageType['requestFlg']['effectiveness'],$this->userInfo['MCompany']['id'],$date_format));

    foreach($requestTime as $k => $v) {
      $timeFormat = $this->changeTimeFormat($v[0]['average']);
      $requestAvgTime =  $requestAvgTime + array($v[0]['date'] => $timeFormat);
      $avgForcalculation = $avgForcalculation + array($v[0]['date'] =>$v[0]['average']);
    }

    //チャットリクエスト平均時間
    $requestAvgTimeData = array_merge($baseTimeData,$requestAvgTime);

    //全チャットリクエスト平均時間
    $allRequestAvgTimeData = array_sum($avgForcalculation)/($k+1);
    //$this->log('aaa',LOG_DEBUG);
    //$this->log($allRequestAvgTimeData,LOG_DEBUG);
    $allRequestAvgTimeData = $this->changeTimeFormat($allrequestAvgTimeData);

    return['requestAvgTimeData' => $requestAvgTimeData, 'allRequestAvgTimeData' => $allRequestAvgTimeData];

  }

  public function getConsumerWatingAvgTimeData($date_format,$baseData,$baseTimeData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period) {
    $consumerWatingAvgTime = [];
    $avgForcalculation = [];

    //平均消費者待機時間
    $consumerWatingTime = "SELECT date_format(th.access_date, ?) as date, AVG(UNIX_TIMESTAMP(s2.created) - UNIX_TIMESTAMP(s1.created)) as average
    FROM .t_histories as th LEFT JOIN (SELECT * FROM t_history_chat_logs where message_request_flg = ? group by t_histories_id) as s1
    ON th.id = s1.t_histories_id LEFT JOIN (SELECT * FROM t_history_chat_logs where message_type = ? group by t_histories_id) as s2 ON th.id = s2.t_histories_id
    where th.access_date between ? and ? and  th.m_companies_id = ? group by date_format(th.access_date,?)";

    $consumerWatingTime = $this->THistory->query($consumerWatingTime, array($date_format,$this->chatMessageType['requestFlg']['effectiveness'],$this->chatMessageType['messageType']['enteringRoom'],$correctStartDate,$correctEndDate,$this->userInfo['MCompany']['id'],$date_format));

    foreach($consumerWatingTime as $k => $v) {
      $timeFormat = $this->changeTimeFormat($v[0]['average']);
      $consumerWatingAvgTime =  $consumerWatingAvgTime + array($v[0]['date'] => $timeFormat);
      $avgForcalculation = $avgForcalculation + array($v[0]['date'] =>$v[0]['average']);
    }

    //消費者待機平均時間
    $consumerWatingAvgTimeData = array_merge($baseTimeData,$consumerWatingAvgTime);

    //全消費者待機平均時間
    $allConsumerWatingAvgTimeData = array_sum($avgForcalculation)/($k+1);
    $allConsumerWatingAvgTimeData = $this->changeTimeFormat($allConsumerWatingAvgTimeData);

    return ['consumerWatingAvgTimeData' => $consumerWatingAvgTimeData,'allConsumerWatingAvgTimeData' => $allConsumerWatingAvgTimeData];

  }

  public function getResponseAvgTimeData($date_format,$baseData,$baseTimeData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period) {
    $responseAvgTime = [];
    $avgForcalculation = [];

    //平均応答時間
    $responseTime = "SELECT date_format(th.access_date, ?) as date, AVG(UNIX_TIMESTAMP(s2.created) - UNIX_TIMESTAMP(s1.created)) as average
    FROM t_histories as th LEFT JOIN (SELECT * FROM t_history_chat_logs where message_request_flg = ? group by t_histories_id) as s1
    ON th.id = s1.t_histories_id LEFT JOIN (SELECT * FROM t_history_chat_logs where message_type = ? group by t_histories_id) as s2 ON th.id = s2.t_histories_id
    where th.access_date between ? and ? and  th.m_companies_id = ? group by date_format(th.access_date,?)";

    $responseTime = $this->THistory->query($responseTime, array($date_format,$this->chatMessageType['requestFlg']['effectiveness'],$this->chatMessageType['messageType']['operatorMessage'],
      $correctStartDate,$correctEndDate,$this->userInfo['MCompany']['id'],$date_format));

    foreach($responseTime as $k => $v) {
      $timeFormat = $this->changeTimeFormat($v[0]['average']);
      $responseAvgTime =  $responseAvgTime + array($v[0]['date'] => $timeFormat);
      $avgForcalculation = $avgForcalculation + array($v[0]['date'] =>$v[0]['average']);
    }

    //平均応答時間
    $responseAvgTimeData = array_merge($baseTimeData,$responseAvgTime);

    //全応答平均時間
    $allResponseAvgTimeData = array_sum($avgForcalculation)/($k+1);
    $allResponseAvgTimeData = $this->changeTimeFormat($allResponseAvgTimeData);

    return ['responseAvgTimeData' => $responseAvgTimeData,'allResponseAvgTimeData' => $allResponseAvgTimeData];
  }

  public function changeTimeFormat($seconds) {

    $hours = round($seconds / 3600);
    $minutes = round(($seconds / 60) % 60);
    $seconds = $seconds % 60;

    $timeFormat = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);

    return $timeFormat;
  }

  public function outputCsv() {
    $this->autoRender = false;

    //$this->log('requestData',LOG_DEBUG);
    //$this->log($this->request->data,LOG_DEBUG);

    if($this->request->data[0] == '月別') {
      $start = $allInfo['data'].'-01';
      $end = $allInfo['data'].'-12';
      $startDate = strtotime('first day of' .$start);
      $endDate = strtotime('last day of' .$end);
      $yearData = [];
      $yearData[] = '統計項目/月別';
      while($startDate <= $endDate) {
        $yearData[] = date('Yーm',$startDate);
        $startDate = strtotime("+1 month", $startDate);
      }
      $csv[] = $yearData;
    }
    else if($this->request->data[0] == '日別') {
      //$this->log('入っていますよcsv',LOG_DEBUG);
      $firstDate = strtotime('first day of ' .$this->request->data['date']);
      $lastDate = strtotime('last day of ' . $this->request->data['date']);
      //$this->log($this->request->data['date'],LOG_DEBUG);
      //$this->log($this->request->data['date'],LOG_DEBUG);
      $monthData = [];
      $monthData[] = '統計項目/月別';
      while($firstDate <= $lastDate) {
        $monthData[] = date('Y-m-d',$firstDate);
        $firstDate = strtotime("+1 day", $firstDate);
      }
      $csv[] = $monthData;
    }
    else if($this->request->data[0] == '時別') {
      $startTime = strtotime($Conditions['data']);
      $endTime = strtotime("+1 day",$startTime);
      $dayData = [];
      $dayData[] = '統計項目/月別';
      while($startTime < $endTime) {
        $dayData[] = date('H:i',$startTime).'-'.date('H:i',strtotime("+1 hour", $startTime));
        $startTime = strtotime("+1 hour", $startTime);
      }
      $csv[] = $dayData;
    }

    $accessNumber = [];
    $accessNumber[] = '合計アクセス件数';
    $widgetNumber = [];
    $widgetNumber[] = 'ウィジェット件数';
    $requestNumber = [];
    $requestNumber[] = 'チャットリクエスト件数';
    $responseNumber = [];
    $responseNumber[] = 'チャット応対件数';
    $noNumber = [];
    $noNumber[] = 'チャット拒否件数';
    $effectivenessNumber = [];
    $effectivenessNumber[] = 'チャット有効件数';
    $requestAvgTime = [];
    $requestAvgTime[] = '平均チャットリクエスト時間';
    $responseRate = [];
    $responseRate[] = 'チャット応対率';
    $effectivenessRate = [];
    $effectivenessRate[] = 'チャット有効率';
    foreach($Conditions['accessNumber'] as $key => $v) {
      $accessNumber[] = $v[0][0]['count(th.id)'];
    }
    foreach($Conditions['widjetNumber'] as $key => $v) {
      $widgetNumber[] = $v[0][0]['count(tw.id)'];
    }
    foreach($Conditions['requestNumber'] as $key => $v) {
      $requestNumber[] = $v[0][0]['count(th.id)'];
    }
    foreach($Conditions['responseNumber'] as $key => $v) {
      $responseNumber[] = $v[0][0]['count(distinct message_distinction,t_histories_id)'];
    }
    foreach($Conditions['effectivenessNumber'] as $key => $v) {;
      $noNumber[] = $v[0][0]['no'];
      $effectivenessNumber[] = $v[0][0]['yukou'];
    }
    foreach($Conditions['requestTimes'] as $key => $v) {;
      $requestAvgTime[] = $v;
    }
    foreach($Conditions['responseRate'] as $key => $v2) {;
      $responseRate[] = $v2;
    }
    foreach($Conditions['effectivenessRate'] as $key => $v3) {;
      $effectivenessRate[] = $v3;
    }
    $csv[] = $accessNumber;
    $csv[] = $widgetNumber;
    $csv[] = $requestNumber;
    $csv[] = $responseNumber;
    $csv[] = $noNumber;
    $csv[] = $effectivenessNumber;
    $csv[] = $requestAvgTime;
    $csv[] = $responseRate;
    $csv[] = $effectivenessRate;
    //$this->log('csv',LOG_DEBUG);
    //$this->log($csv,LOG_DEBUG);
    $this->outputCSVStatistics($csv);
  }

  public function outputCSVStatistics($csv = []) {
    $this->layout = null;
    //$this->log('ここまではいいてなぜでない',LOG_DEBUG);
    //メモリ上に領域確保
    $fp = fopen('php://temp/maxmemory:'.(5*1024*1024),'a');

    foreach($csv as $row){
      fputcsv($fp, $row);
    }

    //ビューを使わない
    $this->autoRender = false;

    $name =  'sinclo_statistics';

    $filename = date("YmdHis")."_".$name;

    //download()内ではheader("Content-Disposition: attachment; filename=hoge.csv")を行っている
    $this->response->download($filename.".csv");

    //ファイルポインタを先頭へ
    rewind($fp);

    //リソースを読み込み文字列を取得する
    $csv = stream_get_contents($fp);

    //Content-Typeを指定
    $this->response->type('csv');

    //CSVをエクセルで開くことを想定して文字コードをSJIS-win
    $csv = mb_convert_encoding($csv,'SJIS-win','utf8');

    $this->response->body($csv);

    fclose($fp);

  }

  private function getDateWithMilliSec() {
    //microtimeを.で分割
    $arrTime = explode('.',microtime(true));
    return date('Y-m-d H:i:s', $arrTime[0]) . '.' .$arrTime[1];
  }
}