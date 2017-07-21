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
      'automatic' => 5,
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

  const LABEL_INVALID = "−";

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
        $date = $this->request->data['dateFormat'];
        //月別の場合
        if($date == '月別'){
          $type = $this->request->data['selectName2'];
          $data = $this->calculateMonthlyData($type);
        }
        //日別の場合
        else if($date == '日別'){
          $type = $this->request->data['selectName3'];
          $data = $this->calculateDailyData($type);
        }
        //時別の場合
        else if($date == '時別') {
          $type = $this->request->data['datefilter'].' 00:00:00';
          $data = $this->calculateHourlyData($type);
        }
      }
    }
    //デフォルト画面
    else {
      $date = '時別';
      $type = date("Y-m-d");
      $data = $this->calculateHourlyData($type);
    }

    //各企業の日付けの範囲
    $rangeData = $this->determineRange();

    $this->set('companyRangeDate',$rangeData['companyRangeDate']);
    $this->set('companyRangeYear',$rangeData['companyRangeYear']);
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
    $startDate = strtotime($data); // 2016-11-02 00:00:00
    $endDate = strtotime("+23 hour",$startDate); // 2016-11-02 23:00:00
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

  //各企業の日付けの範囲
  public function determineRange(){
    //企業がsincloを開始した日付
    $companyStartDate = strtotime($this->userInfo['MCompany']['created']);
    //契約開始月
    $companyStartDate = strtotime('first day of ' . date('Y-m',$companyStartDate));
    //現在の月
    $endDate = strtotime( 'last day of '.date('Y-m',strtotime("now" )));
    //契約開始年
    $companyStartYear = date('Y',$companyStartDate);
    $companyStartYear = strtotime( 'first day of '.date('Y-m',strtotime($companyStartYear."-01" )));
    //現在の年
    $endYear = date('Y',strtotime( "now" ));
    $endYear = strtotime( 'last day of '.date('Y-m',strtotime($endYear."-12" )));
    $companyRangeDate = [];
    $companyRangeYear = [];

    while($companyStartDate <= $endDate){
      $companyRangeDate = $companyRangeDate + array(date('Y-m',$companyStartDate) => date('Y/m',$companyStartDate));
      $companyStartDate = strtotime("+1 month", $companyStartDate);
    }
    $companyStartDate = strtotime($this->userInfo['MCompany']['created']);

    while($companyStartYear <= $endYear){
      $companyRangeYear = $companyRangeYear + array(date('Y',$companyStartYear) => date('Y',$companyStartYear));
      $companyStartYear = strtotime("+1 year", $companyStartYear);
    }

    return ['companyRangeDate' => $companyRangeDate,'companyRangeYear' => $companyRangeYear];
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

    //自動返信応対件数、自動返信応対率
    $automaticResponseData = $this->getAutomaticResponseData($date_format,$baseData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period,$requestDatas['requestNumberData'],$requestDatas['allRequestNumberData']);

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
    "automaticResponseData" => $automaticResponseData,"coherentDatas" => $coherentDatas,"avgRequestTimeDatas" => $avgRequestTimeDatas,"consumerWatingAvgTimeDatas" => $consumerWatingAvgTimeDatas,
    "responseAvgTimeData" => $responseAvgTimeData];
  }

  public function getAccessData($date_format,$baseData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period) {
    $accessNumberData = [];

    //アクセス件数
    $access = "SELECT date_format(th.access_date, ?) as date, count(th.id) FROM t_histories as th where th.access_date
    between ? and ? and th.m_companies_id = ? group by date";
    $accessNumber = $this->THistory->query($access, array($date_format,$correctStartDate,$correctEndDate,$this->userInfo['MCompany']['id']));

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
    ? and ? and tw.m_companies_id = ? group by date";
    $widgetNumber = $this->THistoryWidgetDisplays->query($widget, array($date_format,$correctStartDate,$correctEndDate,$this->userInfo['MCompany']['id']));

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
    $requestNumber = "SELECT
      date_format(th.access_date, ?) as date,
        count(th.id) as request_count
    FROM t_histories as th, t_history_chat_logs as thcl
    WHERE
      th.m_companies_id = ?
    AND
      thcl.t_histories_id = th.id
    AND
      thcl.message_request_flg = ?
    AND
      th.access_date between ? and ?
    group by date";

    $requestNumber = $this->THistory->query($requestNumber, array($date_format,$this->userInfo['MCompany']['id'],$this->chatMessageType['requestFlg']['effectiveness'],$correctStartDate,$correctEndDate));

    foreach($requestNumber as $k => $v) {
      $requestNumberData =  $requestNumberData + array($v[0]['date'] => $v[0]['request_count']);
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
    $response = "SELECT date_format(th.access_date, ?) as date,
    count(thcl.t_histories_id) as response_count
    FROM (select id, m_companies_id, access_date from t_histories where m_companies_id = ? AND access_date between
    ? and ?) as th,(select t_histories_id, message_type, message_distinction
    from t_history_chat_logs where message_type = ? group by t_histories_id, message_distinction) as thcl,(select t_histories_id, message_request_flg,
    message_distinction from t_history_chat_logs where message_request_flg = ?) as thcl2
    WHERE
      thcl.t_histories_id = th.id
    AND
      th.id = thcl2.t_histories_id
    AND
      thcl.t_histories_id = thcl2.t_histories_id
    AND
      thcl.message_distinction = thcl2.message_distinction
    group by date";

    $responseNumber = $this->THistory->query($response, array($date_format,$this->userInfo['MCompany']['id'],$correctStartDate,$correctEndDate,$this->chatMessageType['messageType']['enteringRoom'],$this->chatMessageType['requestFlg']['effectiveness']));

    foreach($responseNumber as $k => $v) {
      if($v[0]['response_count'] != 0 and $requestNumberData[$v[0]['date']] != 0) {
        $responseRate = $responseRate + array($v[0]['date'] => round($v[0]['response_count']/$requestNumberData[$v[0]['date']]*100));
      } else if ($requestNumberData[$v[0]['date']] === 0) {
        $responseRate = $responseRate + array($v[0]['date'] => self::LABEL_INVALID);
      }
      $responseNumberData = $responseNumberData + array($v[0]['date'] => $v[0]['response_count']);
    }

    //チャット応答率
    $responseRate = array_merge($this->convertBaseDataForPercent($baseData),$responseRate);

    //チャット応答件数
    $responseNumberData = array_merge($baseData,$responseNumberData);

    //応対件数合計値
    $allResponseNumberData = array_sum($responseNumberData);

    //合計チャット応答率
    $allResponseRate = 0;
    if($allResponseNumberData != 0 and $allRequestNumberData != 0) {
      $allResponseRate = round($allResponseNumberData/$allRequestNumberData*100);
    } else if ($allRequestNumberData === 0) {
      $allResponseRate = self::LABEL_INVALID;
    }

    return ['responseRate' => $responseRate,'responseNumberData' => $responseNumberData,'allResponseNumberData' => $allResponseNumberData,'allResponseRate' => $allResponseRate];
  }


  public function getAutomaticResponseData($date_format,$baseData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period,$requestNumberData,$allRequestNumberData) {
    $automaticResponseNumberData = [];
    $automaticResponseRate = [];

    //自動返信応対件数
    $automaticResponse = "SELECT date_format(th.access_date, ?) as date,
    count(distinct thcl.message_distinction,thcl.t_histories_id) as automaticResponse_count
    FROM (select id, m_companies_id, access_date from t_histories where m_companies_id = ? AND access_date between
    ? and ?) as th,(select t_histories_id, message_type,message_distinction from t_history_chat_logs where message_type = ?) as thcl
    WHERE
      thcl.t_histories_id = th.id
    group by date";

    $automaticResponseNumber = $this->THistory->query($automaticResponse, array($date_format,$this->userInfo['MCompany']['id'],
    $correctStartDate,$correctEndDate,$this->chatMessageType['messageType']['automatic']));
    foreach($automaticResponseNumber as $k => $v) {
      $automaticResponseNumberData =  $automaticResponseNumberData + array($v[0]['date'] => $v[0]['automaticResponse_count']);
      if($v[0]['automaticResponse_count'] != 0 and $requestNumberData[$v[0]['date']] != 0) {
        $automaticResponseRate = $automaticResponseRate + array($v[0]['date'] => round($v[0]['automaticResponse_count']/$requestNumberData[$v[0]['date']]*100));
      } else if ($requestNumberData[$v[0]['date']] === 0) {
        $automaticResponseRate = $automaticResponseRate + array($v[0]['date'] => self::LABEL_INVALID);
      }
    }

    //自動返信応対件数
    $automaticResponseNumberData = array_merge($baseData,$automaticResponseNumberData);

    //自動返信応答率
    $automaticResponseRate = array_merge($this->convertBaseDataForPercent($baseData),$automaticResponseRate);

    //自動返信応対件数合計値
    $allAutomaticResponseNumberData = array_sum($automaticResponseNumberData);

    //合計チャット応答率
    $allAutomaticResponseRate = 0;
    if($allAutomaticResponseNumberData != 0 and $allRequestNumberData != 0) {
      $allAutomaticResponseRate = round($allAutomaticResponseNumberData/$allRequestNumberData*100);
    } else if ($allRequestNumberData === 0) {
      $allAutomaticResponseRate = self::LABEL_INVALID;
    }

    return ['automaticResponseNumberData' => $automaticResponseNumberData,'automaticResponseRate' => $automaticResponseRate,
    'allAutomaticResponseNumberData' => $allAutomaticResponseNumberData,'allAutomaticResponseRate' => $allAutomaticResponseRate];
  }

  public function getCoherentData($date_format,$baseData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period,$requestNumberData,$allRequestNumberData) {
    $effectivenessNumberData = [];
    $denialNumberData = [];
    $effectivenessRate = [];

    //チャット有効件数
    $effectiveness = "SELECT date_format(th.access_date, ?) as date,SUM(case when thcl.achievement_flg = ? THEN 1 ELSE 0 END) effectiveness,
    SUM(case when thcl.message_type = ? THEN 1 ELSE 0 END) denial
    FROM (select id, m_companies_id, access_date from t_histories where m_companies_id = ? AND access_date between
    ? and ?) as th,(select t_histories_id, achievement_flg, message_type from t_history_chat_logs where achievement_flg = ? or message_type = ? ) as thcl
    WHERE
      thcl.t_histories_id = th.id
    group by date;";

    $effectiveness = $this->THistory->query($effectiveness, array($date_format,$this->chatMessageType['achievementFlg']['effectiveness'],$this->chatMessageType['messageType']['denial'],
      $this->userInfo['MCompany']['id'],$correctStartDate,$correctEndDate,$this->chatMessageType['achievementFlg']['effectiveness'],$this->chatMessageType['messageType']['denial']));
    $this->log('有効件数',LOG_DEBUG);
    $this->log($effectiveness,LOG_DEBUG);

    if(!empty($effectiveness)) {
      foreach($effectiveness as $k => $v) {
        $effectivenessNumberData =  $effectivenessNumberData + array($v[0]['date'] => $v[0]['effectiveness']);
        $denialNumberData =  $denialNumberData + array($v[0]['date'] => $v[0]['denial']);
        if( $v[0]['effectiveness'] != 0 and $requestNumberData[$v[0]['date']] != 0){
          $effectivenessRate = $effectivenessRate + array($v[0]['date'] => round($v[0]['effectiveness']/$requestNumberData[$v[0]['date']]*100));
        } else if($requestNumberData[$v[0]['date']] === 0) {
          $effectivenessRate = $effectivenessRate + array($v[0]['date'] => self::LABEL_INVALID);
        }
      }
    }
    //チャット有効件数
    $effectivenessNumberData = array_merge($baseData,$effectivenessNumberData);

    //チャット拒否件数
    $denialNumberData = array_merge($baseData,$denialNumberData);

    //チャット有効率
    $effectivenessRate = array_merge($this->convertBaseDataForPercent($baseData),$effectivenessRate);

    //有効件数合計値
    $allEffectivenessNumberData = array_sum($effectivenessNumberData);

    //拒否件数合計値
    $allDenialNumberData = array_sum($denialNumberData);

    //合計有効率
    $allEffectivenessRate = 0;
    if($allEffectivenessNumberData != 0 and $allRequestNumberData != 0) {
      $allEffectivenessRate = round($allEffectivenessNumberData/$allRequestNumberData*100);
    } else if ($allRequestNumberData === 0) {
      $allEffectivenessRate = self::LABEL_INVALID;
    }

    return ['effectivenessNumberData' => $effectivenessNumberData,'denialNumberData' => $denialNumberData,'effectivenessRate' => $effectivenessRate,
    'allEffectivenessNumberData' => $allEffectivenessNumberData,'allDenialNumberData' => $allDenialNumberData,'allEffectivenessRate' => $allEffectivenessRate];

  }

  public function getAvgRequestTimeData($date_format,$baseData,$baseTimeData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period) {
    $requestAvgTime = [];
    $avgForcalculation = [];
    $effectivenessRate = [];

    //平均チャットリクエスト時間
    $requestTime = "SELECT date_format(th.access_date, ?) as date,AVG(UNIX_TIMESTAMP(thcl.created)
      - UNIX_TIMESTAMP(th.access_date)) as average
    FROM (select id, m_companies_id, access_date from t_histories where m_companies_id = ? AND access_date between
    ? and ?) as th,(select t_histories_id, message_request_flg, message_distinction,created
    from t_history_chat_logs where message_request_flg = ? group by t_histories_id) as thcl,(select t_histories_id, message_type,message_distinction
    from t_history_chat_logs where message_type = ? group by t_histories_id) as thcl2
    WHERE
      thcl.t_histories_id = th.id
    AND
      th.id = thcl2.t_histories_id
    AND
      thcl.t_histories_id = thcl2.t_histories_id
    AND
      thcl.message_distinction = thcl2.message_distinction group by date";

    $requestTime = $this->THistory->query($requestTime, array($date_format,$this->userInfo['MCompany']['id'],
      $correctStartDate,$correctEndDate,$this->chatMessageType['requestFlg']['effectiveness'],$this->chatMessageType['messageType']['consumerMessage']));

    foreach($requestTime as $k => $v) {
      $timeFormat = $this->changeTimeFormat(round($v[0]['average']));
      $requestAvgTime =  $requestAvgTime + array($v[0]['date'] => $timeFormat);
      $avgForcalculation = $avgForcalculation + array($v[0]['date'] => round($v[0]['average']));
    }
    //チャットリクエスト平均時間
    $requestAvgTimeData = array_merge($baseTimeData,$requestAvgTime);

    //全チャットリクエスト平均時間
    $allRequestAvgTimeData = 0;
    if(!empty($v)) {
      $allRequestAvgTimeData = array_sum($avgForcalculation)/($k+1);
    }
    $allRequestAvgTimeData = $this->changeTimeFormat($allRequestAvgTimeData);

    return['requestAvgTimeData' => $requestAvgTimeData, 'allRequestAvgTimeData' => $allRequestAvgTimeData];

  }

  public function getConsumerWatingAvgTimeData($date_format,$baseData,$baseTimeData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period) {
    $consumerWatingAvgTime = [];
    $avgForcalculation = [];

    //平均消費者待機時間
    $consumerWatingTime = "SELECT date_format(th.access_date,?) as date,AVG(UNIX_TIMESTAMP(thcl2.created)
      - UNIX_TIMESTAMP(thcl.created)) as average
    FROM (select id, m_companies_id, access_date from t_histories where m_companies_id = ? AND access_date between
    ? and ?) as th,(select t_histories_id, message_request_flg,created,message_distinction
    from t_history_chat_logs where message_request_flg = ? group by t_histories_id) as thcl,(select t_histories_id, message_type,created,message_distinction
    from t_history_chat_logs where message_type = ? group by t_histories_id) as thcl2
    WHERE
      thcl.t_histories_id = th.id
    AND
      th.id = thcl2.t_histories_id
    AND
      thcl.t_histories_id = thcl2.t_histories_id
    AND
    thcl.message_distinction = thcl2.message_distinction
    group by date";

    $consumerWatingTime = $this->THistory->query($consumerWatingTime, array($date_format,$this->userInfo['MCompany']['id'],
      $correctStartDate,$correctEndDate,$this->chatMessageType['requestFlg']['effectiveness'],$this->chatMessageType['messageType']['enteringRoom']));

    foreach($consumerWatingTime as $k => $v) {
      $timeFormat = $this->changeTimeFormat(round($v[0]['average']));
      $consumerWatingAvgTime =  $consumerWatingAvgTime + array($v[0]['date'] => $timeFormat);
      $avgForcalculation = $avgForcalculation + array($v[0]['date'] =>round($v[0]['average']));
    }

    //消費者待機平均時間
    $consumerWatingAvgTimeData = array_merge($baseTimeData,$consumerWatingAvgTime);

    //全消費者待機平均時間
    $allConsumerWatingAvgTimeData = 0;
    if(!empty($v)) {
      $allConsumerWatingAvgTimeData = array_sum($avgForcalculation)/($k+1);
    }
    $allConsumerWatingAvgTimeData = $this->changeTimeFormat($allConsumerWatingAvgTimeData);

    return ['consumerWatingAvgTimeData' => $consumerWatingAvgTimeData,'allConsumerWatingAvgTimeData' => $allConsumerWatingAvgTimeData];

  }

  public function getResponseAvgTimeData($date_format,$baseData,$baseTimeData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period) {
    $responseAvgTime = [];
    $avgForcalculation = [];

    //平均応答時間
    $responseTime = "SELECT date_format(th.access_date, ?) as date,AVG(UNIX_TIMESTAMP(thcl2.created)
      - UNIX_TIMESTAMP(thcl.created)) as average
    FROM (select id, m_companies_id, access_date from t_histories where m_companies_id = ? AND access_date between
    ? and ?) as th,(select t_histories_id, message_request_flg,created,message_distinction
    from t_history_chat_logs where message_request_flg = ? group by t_histories_id) as thcl,(select t_histories_id, message_type,created,message_distinction
    from t_history_chat_logs where message_type = ? group by t_histories_id) as thcl2
    WHERE
      thcl.t_histories_id = th.id
    AND
      th.id = thcl2.t_histories_id
    AND
      thcl.t_histories_id = thcl2.t_histories_id
    AND
    thcl.message_distinction = thcl2.message_distinction group by date";

    $responseTime = $this->THistory->query($responseTime, array($date_format,$this->userInfo['MCompany']['id'],
      $correctStartDate,$correctEndDate,$this->chatMessageType['requestFlg']['effectiveness'],$this->chatMessageType['messageType']['operatorMessage']));

    foreach($responseTime as $k => $v) {
      $timeFormat = $this->changeTimeFormat(round($v[0]['average']));
      $responseAvgTime =  $responseAvgTime + array($v[0]['date'] => $timeFormat);
      $avgForcalculation = $avgForcalculation + array($v[0]['date'] =>round($v[0]['average']));
    }

    //平均応答時間
    $responseAvgTimeData = array_merge($baseTimeData,$responseAvgTime);

    //全応答平均時間
    $allResponseAvgTimeData = 0;
    if(!empty($v)) {
      $allResponseAvgTimeData = array_sum($avgForcalculation)/($k+1);
    }
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

    //json_decode
    $requestData = (array)json_decode($this->request->data['statistics']['outputData']);
    if($requestData['dateFormat'] == '月別') {
      $start = $requestData['date'].'-01';
      $end = $requestData['date'].'-12';

      $startDate = strtotime('first day of' .$start);
      $endDate = strtotime('last day of' .$end);
      $yearData = [];
      $yearData[] = '統計項目/月別';
      while($startDate <= $endDate) {
        $yearData[] = date('Y-m',$startDate);
        $startDate = strtotime("+1 month", $startDate);
      }
      $yearData[] = '合計・平均';
      $csv[] = $yearData;
      $csvData = $this->calculateMonthlyData($requestData['date']);
    }
    else if($requestData['dateFormat'] == '日別') {
      $firstDate = strtotime('first day of ' .$requestData['date']);
      $lastDate = strtotime('last day of ' .$requestData['date']);

      $monthData = [];
      $monthData[] = '統計項目/月別';
      while($firstDate <= $lastDate) {
        $monthData[] = date('Y-m-d',$firstDate);
        $firstDate = strtotime("+1 day", $firstDate);
      }
      $monthData[] = '合計・平均';
      $csv[] = $monthData;
      $csvData = $this->calculateDailyData($requestData['date']);
    }
    else if($requestData['dateFormat'] == '時別') {
      $startTime = strtotime($requestData['date']);
      $endTime = strtotime("+1 day",$startTime);
      $dayData = [];
      $dayData[] = '統計項目/月別';
      while($startTime < $endTime) {
        $dayData[] = date('H:i',$startTime).'-'.date('H:i',strtotime("+1 hour", $startTime));
        $startTime = strtotime("+1 hour", $startTime);
      }
      $dayData[] = '合計・平均';
      $csv[] = $dayData;
      $csvData = $this->calculateHourlyData($requestData['date']);
    }

    foreach($this->insertCsvData($csvData) as $key => $v) {
      $csv[] = $v;
    }

    $this->outputCSVStatistics($csv);
  }

  public function insertCsvData($csvData) {
    $accessNumber = [];
    $accessNumber[] = '合計アクセス件数';
    $widgetNumber = [];
    $widgetNumber[] = 'ウィジェット件数';
    $requestNumber = [];
    $requestNumber[] = 'チャットリクエスト件数';
    $responseNumber = [];
    $responseNumber[] = 'チャット応対件数';
    $automaticResponseNumber = [];
    $automaticResponseNumber[] = '自動返信応対件数';
    $noNumber = [];
    $noNumber[] = 'チャット拒否件数';
    $effectivenessNumber = [];
    $effectivenessNumber[] = 'チャット有効件数';
    $requestAvgTime = [];
    $requestAvgTime[] = '平均チャットリクエスト時間';
    $consumerWatingAvgTime = [];
    $consumerWatingAvgTime[] = '平均消費者待機時間';
    $responseAvgTime = [];
    $responseAvgTime[] = '平均応答時間';
    $responseRate = [];
    $responseRate[] = 'チャット応対率';
    $automaticResponseRate = [];
    $automaticResponseRate[] = '自動返信応対率';
    $effectivenessRate = [];
    $effectivenessRate[] = 'チャット有効率';

    $csvData = $this->insertEachItemCsvData($csvData,$accessNumber,$widgetNumber,$requestNumber,
            $responseNumber,$automaticResponseNumber, $noNumber,$effectivenessNumber,
            $requestAvgTime,$consumerWatingAvgTime,$responseAvgTime,$responseRate,$automaticResponseRate,
            $effectivenessRate);

    $csv[] = $csvData['accessNumber'];
    $csv[] = $csvData['widgetNumber'];
    $csv[] = $csvData['requestNumber'];
    $csv[] = $csvData['responseNumber'];
    $csv[] = $csvData['automaticResponseNumber'];
    $csv[] = $csvData['noNumber'];
    $csv[] = $csvData['effectivenessNumber'];
    $csv[] = $csvData['requestAvgTime'];
    $csv[] = $csvData['consumerWatingAvgTime'];
    $csv[] = $csvData['responseAvgTime'];
    $csv[] = $csvData['responseRate'];
    $csv[] = $csvData['automaticResponseRate'];
    $csv[] = $csvData['effectivenessRate'];

    return $csv;
  }

  public function insertEachItemCsvData($csvData,$accessNumber,$widgetNumber,$requestNumber,
      $responseNumber,$automaticResponseNumber, $noNumber,$effectivenessNumber,
      $requestAvgTime,$consumerWatingAvgTime,$responseAvgTime,$responseRate,$automaticResponseRate,
      $effectivenessRate) {

    foreach($csvData['accessDatas']['accessNumberData'] as $key => $v) {
      $accessNumber[] = $v;
    }
    $accessNumber[] = $csvData['accessDatas']['allAccessNumberData'];

    foreach($csvData['widgetDatas']['widgetNumberData'] as $key => $v) {
      $widgetNumber[] = $v;
    }
    $widgetNumber[] = $csvData['widgetDatas']['allWidgetNumberData'];

    foreach($csvData['requestDatas']['requestNumberData'] as $key => $v) {
      $requestNumber[] = $v;
    }
    $requestNumber[] = $csvData['requestDatas']['allRequestNumberData'];

    foreach($csvData['responseDatas']['responseNumberData'] as $key => $v) {
      $responseNumber[] = $v;
    }
    $responseNumber[] = $csvData['responseDatas']['allResponseNumberData'];

    foreach($csvData['automaticResponseData']['automaticResponseNumberData'] as $key => $v) {
      $automaticResponseNumber[] = $v;
    }
    $automaticResponseNumber[] = $csvData['automaticResponseData']['allAutomaticResponseNumberData'];

    foreach($csvData['coherentDatas']['denialNumberData'] as $key => $v) {
      $noNumber[] = $v;
    }
    $noNumber[] = $csvData['coherentDatas']['allDenialNumberData'];

    foreach($csvData['coherentDatas']['effectivenessNumberData'] as $key => $v) {
      $effectivenessNumber[] = $v;
    }
    $effectivenessNumber[] = $csvData['coherentDatas']['allEffectivenessNumberData'];

    foreach($csvData['avgRequestTimeDatas']['requestAvgTimeData'] as $key => $v) {
      $requestAvgTime[] = $v;
    }
    $requestAvgTime[] = $csvData['avgRequestTimeDatas']['allRequestAvgTimeData'];

    foreach($csvData['consumerWatingAvgTimeDatas']['consumerWatingAvgTimeData'] as $key => $v) {
      $consumerWatingAvgTime[] = $v;
    }
    $consumerWatingAvgTime[] = $csvData['consumerWatingAvgTimeDatas']['allConsumerWatingAvgTimeData'];

    foreach($csvData['responseAvgTimeData']['responseAvgTimeData'] as $key => $v) {
      $responseAvgTime[] = $v;
    }
    $responseAvgTime[] = $csvData['responseAvgTimeData']['allResponseAvgTimeData'];

    foreach($csvData['responseDatas']['responseRate'] as $key => $v2) {
      $responseRate[] = $v2;
    }
    $responseRate[] = $csvData['responseDatas']['allResponseRate'];

    foreach($csvData['automaticResponseData']['automaticResponseRate'] as $key => $v2) {
      $automaticResponseRate[] = $v2;
    }
    $automaticResponseRate[] = $csvData['automaticResponseData']['allAutomaticResponseRate'];

    foreach($csvData['coherentDatas']['effectivenessRate'] as $key => $v3) {
      $effectivenessRate[] = $v3;
    }

    $effectivenessRate[] = $csvData['coherentDatas']['allEffectivenessRate'];

    return ['accessNumber' => $accessNumber,'widgetNumber' => $widgetNumber,'requestNumber' => $requestNumber,
      'responseNumber' => $responseNumber,'automaticResponseNumber' => $automaticResponseNumber, 'noNumber' =>$noNumber,
      'effectivenessNumber' => $effectivenessNumber,'requestAvgTime' =>$requestAvgTime,'consumerWatingAvgTime' => $consumerWatingAvgTime,
      'responseAvgTime' => $responseAvgTime,'responseRate' => $responseRate,'automaticResponseRate' => $automaticResponseRate,
      'effectivenessRate' => $effectivenessRate];

  }

  public function outputCSVStatistics($csv = []) {
    $this->layout = null;
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

  private function convertBaseDataForPercent($baseData) {
    $array = array();
    foreach ($baseData as $k => $v) {
      $array[$k] = self::LABEL_INVALID;
    }
    return $array;
  }
}
