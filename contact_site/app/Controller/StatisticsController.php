<?php
/**
 * StatisticsController controller.
 * 統計機能
 */
class StatisticsController extends AppController {

  public $uses = ['THistory','MCompany','THistoryChatActiveUsers','THistoryWidgetDisplays','TLogin','MUser',
  'THistoryAccessCount','THistoryWidgetCount '];

  private $chatMessageType = [
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
  const LABEL_NONE = "";

  public function beforeFilter(){
    parent::beforeFilter();
    $ret = $this->MCompany->read(null, $this->userInfo['MCompany']['id']);
    $orList = [];
    //除外IPアドレス
    if ( !empty($ret['MCompany']['exclude_ips']) ) {
      foreach( explode("\n", trim($ret['MCompany']['exclude_ips'])) as $v ){
        if ( preg_match("/^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}$/", trim($v)) ) {
          $orList[] = "INET_ATON('".trim($v)."') = INET_ATON(THistory.ip_address)";
          continue;
        }
      }
    }
    $this->set('title_for_layout', '統計機能');
  }


  /* *
   * チャット統計
   * @return void
   * */
  public function forChat() {
    if($this->request->is('post')) {
      if ($this->THistory->validates() ) {
        $date = $this->request->data['dateFormat'];
        //月別の場合
        if($date == '月別'){
          $type = $this->request->data['monthlyName'];
          $data = $this->calculateMonthlyData($type);
        }
        //日別の場合
        else if($date == '日別'){
          $type = $this->request->data['daylyName'];
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

  /* *
   * オペレーター統計
   * @return void
   * */
  public function forOperator() {
    if($this->request->is('post')) {
      if ($this->THistory->validates() ) {
        $date = $this->request->data['dateFormat'];
        //月別の場合
        if($date == '月別'){
          $type = $this->request->data['monthlyName'];
          $timeData = $this->calculateOperatorMonthlyData($type,'list');
          //オペレータレポートデータ取得
          $users =$this->summaryOperatorSql($timeData['date_format'],$timeData['anotherWindowDateFormat'],
            $timeData['correctStartDate'],$timeData['correctEndDate']);
        }
        //日別の場合
        else if($date == '日別'){
          $type = $this->request->data['daylyName'];
          $timeData = $this->calculateOperatorDaylyData($type);
          //オペレータレポートデータ取得
          $users =$this->summaryOperatorSql($timeData['date_format'],$timeData['anotherWindowDateFormat'],
            $timeData['correctStartDate'],$timeData['correctEndDate']);
        }
        //時別の場合
        else if($date == '時別') {
          $type = $this->request->data['datefilter'];
          $timeData = $this->calculateOperatorHourlyData($type);
          //オペレータレポートデータ取得
          $users =$this->summaryOperatorSql($timeData['date_format'],$timeData['anotherWindowDateFormat'],
            $timeData['correctStartDate'],$timeData['correctEndDate']);
        }
      }
    }
    //デフォルト画面
    else {
      $date = '時別';
      $type = date("Y-m-d");
      $timeData = $this->calculateOperatorHourlyData($type);
      $users =$this->summaryOperatorSql($timeData['date_format'],$timeData['anotherWindowDateFormat'],$timeData['correctStartDate'],$timeData['correctEndDate']);
    }

    //各企業の日付けの範囲
    $rangeData = $this->determineRange();
    $data = ['users' => $users];
    $type = str_replace("/", "-", $type);
    $this->set('companyRangeDate',$rangeData['companyRangeDate']);
    $this->set('companyRangeYear',$rangeData['companyRangeYear']);
    $this->set('date',$date);
    $this->set('daylyEndDate',date("d",strtotime('last day of' .$type)));
    $this->set('type',$type);
    $this->set('time',$timeData['timeType']);
    $this->set('data',$data);
    $this->log('終了',LOG_DEBUG);
  }

  /* *
   * オペレーター統計別ウィンドウ画面
   * @return void
   * */
  public function baseForAnotherWindow() {
    if(!array_key_exists('url',$this->params)) {
      // エラー処理
      $errorMessage = '不正なアクセスです';
      $this->set('errorMessage',$errorMessage);
      return;
    }
    if(!empty($this->params['url']['id'])) {
      $userId = $this->params['url']['id'];
    }
    if(!empty($this->params['url']['item'])) {
      $item = $this->params['url']['item'];
    }
    //m_companies_idチェック
    if(isset($userId) && isset($this->userInfo['MCompany']['id'])) {
      $users = $this->getUserInfo('userId',$userId);
      if($this->userInfo['MCompany']['id'] !== $users[0]['m_users']['m_companies_id']) {
        // エラー処理
        $errorMessage = '該当するユーザーがいません';
        $this->set('errorMessage',$errorMessage);
        return;
      }
    }
    //m_companies_idチェック
   else if(isset($item) && isset($this->userInfo['MCompany']['id'])) {
     $users = $this->getUserInfo('item',null);
      if($this->userInfo['MCompany']['id'] !== $users[0]['m_users']['m_companies_id']) {
         // エラー処理
        $errorMessage = '該当するユーザーがいません';
        $this->set('errorMessage',$errorMessage);
        return;
      }
    }
    else {
      // エラー処理
      $errorMessage = '不正なアクセスです';
      $this->set('errorMessage',$errorMessage);
      return;
    }

    $timeType = $this->params['url']['type'];
    $dateType = $this->params['url']['target'];

    //時別の場合
    if($timeType=='daily') {
      $type = $dateType;
      $timeInfo = $this->calculateOperatorHourlyData($type);
    }
    //日別の場合
    else if($timeType=='monthly') {
      $type = $dateType;
      $timeInfo = $this->calculateOperatorDaylyData($type);
    }
    //月別の場合
    else if($timeType=='yearly') {
      $type = $dateType;
      $timeInfo = $this->calculateOperatorMonthlyData($type,'another');
    }
    //オペレータ1人の情報の場合
    if(!empty($userId)) {
      $data = $this->getPrivateOperatorInfo($users,$timeInfo['anotherWindowDateFormat'],$timeInfo['correctStartDate'],$timeInfo['correctEndDate'],
          $timeInfo['baseData'],$timeInfo['baseTimeData'],$userId);
    }
    //各項目の情報の場合
    else {
      $data = $this->getEachAllOperatorInfo($users,$timeInfo['anotherWindowDateFormat'],$timeInfo['correctStartDate'],$timeInfo['correctEndDate'],
          $timeInfo['baseData'],$timeInfo['baseTimeData'],$item);
    }
    $rangeData = $this->determineRange();
    $this->set('type',$type);
    $this->set('data',$data);
    $this->set('date',$timeInfo['date']);
    $this->set('companyRangeDate',$rangeData['companyRangeDate']);
    $this->set('companyRangeYear',$rangeData['companyRangeYear']);
    $this->set('daylyEndDate',date("d",strtotime('last day of' .$type)));
  }

  /* *
   * loading画像(別ウィンドウ画面が表示されるまで)
   * @return void
   * */
  public function loadingHtml() {
    if(!empty($this->params['url']['id'])) {
      $userId = $this->params['url']['id'];
      $this->set('userId',$userId);
    }
    if(!empty($this->params['url']['item'])) {
      $item = $this->params['url']['item'];
      $this->set('item',$item);
    }

    $timeType = $this->params['url']['type'];
    $dateType = $this->params['url']['target'];

    $this->set('timeType',$timeType);
    $this->set('dateType',$dateType);
  }
  /* *
   * チャットレポートCSV
   * @return void
   * */
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
      $monthData[] = '統計項目/日別';
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
      $dayData[] = '統計項目/時別';
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

  /* *
   * オペレータレポートCSV
   * @return void
   * */
  public function outputOperatorCsv() {
    //オペレータ統計一覧CSV
    $this->autoRender = false;

    //json_decode
    $requestData = (array)json_decode($this->request->data['statistics']['outputData']);

    if($requestData['dateFormat'] == '月別') {
      $timeData = $this->calculateOperatorMonthlyData($requestData['date'],'list');
      $csvData =$this->summaryOperatorSql($timeData['date_format'],$timeData['anotherWindowDateFormat'],
        $timeData['correctStartDate'],$timeData['correctEndDate']);
    }
    else if($requestData['dateFormat'] == '日別') {
      $timeData = $this->calculateOperatorDaylyData($requestData['date']);
      $csvData =$this->summaryOperatorSql($timeData['date_format'],$timeData['anotherWindowDateFormat'],
        $timeData['correctStartDate'],$timeData['correctEndDate']);
    }
    else if($requestData['dateFormat'] == '時別') {
      $timeData = $this->calculateOperatorHourlyData($requestData['date']);
      $csvData =$this->summaryOperatorSql($timeData['date_format'],$timeData['anotherWindowDateFormat'],
        $timeData['correctStartDate'],$timeData['correctEndDate']);
    }

    foreach($this->insertOperatorCsvData($csvData,$timeData['timeType']) as $key => $v) {
      $csv[] = $v;
    }

    $this->outputCSVStatistics($csv);
  }

  /* *
   * 別ウィンドウ画面オペレータレポートCSV
   * @return void
   * */
  public function outputEachOperatorCsv() {
    $this->autoRender = false;

    //json_decode
    $requestData = (array)json_decode($this->request->data['statistics']['outputData']);
    //月別の場合
    if($requestData['dateFormat'] == 'yearly') {
      $users = $this->calculateOperatorMonthlyData($requestData['date'],'another');
      if(!empty($requestData['item'])) {
        $csv[] = $this->getDateTimeInfo('月別',$requestData['date'],$requestData['item']);
        $user = $this->getUserInfo('item',null);
        //各項目オペレータ情報取得
        $csvData = $this->getEachAllOperatorInfo($user,$users['anotherWindowDateFormat'],$users['correctStartDate'],$users['correctEndDate'],
          $users['baseData'],$users['baseTimeData'],$requestData['item']);
      }
      if(!empty($requestData['id'])) {
        $csv[] = $this->getDateTimeInfo('月別',$requestData['date'],null);
        $user = $this->getUserInfo('userId',$requestData['id']);
        //オペレータ1人の情報取得
        $csvData = $this->getPrivateOperatorInfo($user,$users['anotherWindowDateFormat'],$users['correctStartDate'],$users['correctEndDate'],
          $users['baseData'],$users['baseTimeData'],$requestData['id']);
      }
    }
    //日別の場合
    else if($requestData['dateFormat'] == 'monthly') {
      $users = $this->calculateOperatorDaylyData($requestData['date']);
      if(!empty($requestData['item'])) {
        $csv[] = $this->getDateTimeInfo('日別',$requestData['date'],$requestData['item']);
        $user = $this->getUserInfo('item',null);
        //各項目オペレータ情報取得
        $csvData = $this->getEachAllOperatorInfo($user,$users['anotherWindowDateFormat'],$users['correctStartDate'],$users['correctEndDate'],
          $users['baseData'],$users['baseTimeData'],$requestData['item']);
      }
      if(!empty($requestData['id'])) {
        $csv[] = $this->getDateTimeInfo('日別',$requestData['date'],null);
        $user = $this->getUserInfo('userId',$requestData['id']);
        //オペレータ1人の情報取得
        $csvData = $this->getPrivateOperatorInfo($user,$users['anotherWindowDateFormat'],$users['correctStartDate'],$users['correctEndDate'],
          $users['baseData'],$users['baseTimeData'],$requestData['id']);
      }
    }
    //時別の場合
    else if($requestData['dateFormat'] == 'daily') {
      $users = $this->calculateOperatorHourlyData($requestData['date']);
      if(!empty($requestData['item'])) {
        $csv[] = $this->getDateTimeInfo('時別',$requestData['date'],$requestData['item']);
        $user = $this->getUserInfo('item',null);
        //各項目オペレータ情報取得
        $csvData = $this->getEachAllOperatorInfo($user,$users['anotherWindowDateFormat'],$users['correctStartDate'],$users['correctEndDate'],
          $users['baseData'],$users['baseTimeData'],$requestData['item']);
      }
      if(!empty($requestData['id'])) {
        $csv[] = $this->getDateTimeInfo('時別',$requestData['date'],null);
        $user = $this->getUserInfo('userId',$requestData['id']);
        //オペレータ1人の情報取得
        $csvData = $this->getPrivateOperatorInfo($user,$users['anotherWindowDateFormat'],$users['correctStartDate'],$users['correctEndDate'],
          $users['baseData'],$users['baseTimeData'],$requestData['id']);
      }
    }
    //各項目オペレータ情報取得の場合
    if(!empty($requestData['item'])) {
      foreach($this->insertEachItemOperatorCsvData($csvData,$requestData['dateFormat'],$requestData['date']
      ,$requestData['item']) as $key => $v) {
        $csv[] = $v;
      }
    }
    //オペレータ1人の情報取得の場合
    if(!empty($requestData['id'])) {
      foreach($this->insertPrivateOperatorCsvData($csvData,$requestData['dateFormat'],$requestData['date']
      ,$requestData['id']) as $key => $v) {
        $csv[] = $v;
      }
    }
    $this->outputCSVStatistics($csv);
  }
  //オペレータ月別
  private function calculateOperatorMonthlyData($type,$screen) {
    if($screen == 'list') {
      if($type == '2017') {
        $start = $type.'-07';
        $end = $type.'-12';
      }
      else {
        $start = $type.'-01';
        $end = $type.'-12';
      }
    }
    else if($screen == 'another'){
      $start = $type.'-01';
      $end = $type.'-12';
    }
    $startDate = strtotime('first day of' .$start);
    $endDate = strtotime('last day of' .$end);
    $correctStartDate = date("Y-m-d 00:00:00",$startDate);
    $correctEndDate = date("Y-m-d 23:59:59",$endDate);
    $date_format = "%Y";
    $anotherWindowDateFormat = '%Y-%m';
    $timeType = 'yearly';
    $baseData = [];
    $baseTimeData = [];
    $date = 'eachOperatorYearly';

    //array_mergeで使うためのデータを作成
    while($startDate <= $endDate){
      $baseData = $baseData + array(date('Y-m',$startDate) => $this->isInValidDatetime(date("Y-m-d",$startDate)) ? self::LABEL_NONE : 0);
      $baseTimeData = $baseTimeData + array(date('Y-m',$startDate) => $this->isInValidDatetime(date("Y-m-d",$startDate)) ? self::LABEL_NONE :'00:00:00');
      $startDate = strtotime("+1 month", $startDate);
    }

    $startDate = strtotime('first day of' .$start);
    return ['date_format' => $date_format,'anotherWindowDateFormat' => $anotherWindowDateFormat,'correctStartDate' => $correctStartDate,'correctEndDate' => $correctEndDate,
    'baseData' => $baseData,'baseTimeData' => $baseTimeData,'timeType' => $timeType,'date' => $date];
  }

  //オペレータ日別
  private function calculateOperatorDaylyData($type) {
    $startDate = strtotime('first day of' .$type);
    $endDate = strtotime('last day of' .$type);
    $correctStartDate = date("Y-m-d 00:00:00",$startDate);
    $correctEndDate = date("Y-m-d 23:59:59",$endDate);
    $date_format = "%Y-%m";
    $anotherWindowDateFormat = '%Y-%m-%d';
    $baseData = [];
    $baseTimeData = [];
    $timeType = 'monthly';
    $date = 'eachOperatorMonthly';

    //array_mergeで使うためのデータを作成
    while($startDate <= $endDate){
      $baseData = $baseData + array(date("Y-m-d",$startDate) => $this->isInValidDatetime(date("Y-m-d",$startDate)) ? self::LABEL_NONE : 0);
      $baseTimeData = $baseTimeData + array(date("Y-m-d",$startDate) => $this->isInValidDatetime(date("Y-m-d",$startDate)) ? self::LABEL_NONE :"00:00:00");
      $startDate = strtotime("+1 day", $startDate);
    }
    $startDate = strtotime('first day of' .$type);
    return ['date_format' => $date_format,'anotherWindowDateFormat' => $anotherWindowDateFormat,'correctStartDate' => $correctStartDate,'correctEndDate' => $correctEndDate,
    'baseData' => $baseData,'baseTimeData' => $baseTimeData,'timeType' => $timeType,'date' => $date];
  }

  //オペレータ時別
  private function calculateOperatorHourlyData($type) {
    $startDate = strtotime($type); // 2016-11-02 00:00:00
    $endDate = strtotime("+23 hour",$startDate); // 2016-11-02 23:00:00
    $correctStartDate = date("Y-m-d H:00:00",$startDate);
    $correctEndDate = date("Y-m-d H:59:59",$endDate);
    $date_format = "%Y-%m-%d";
    $anotherWindowDateFormat = '%H:00';
    $baseData = [];
    $baseTimeData = [];
    $timeType = 'daily';
    $date = 'eachOperatorDaily';

    //array_mergeで使うためのデータを作成
    while($startDate <= $endDate){
      $baseData = $baseData + array(date("H:00",$startDate) => $this->isInValidDatetime(date("Y-m-d",$startDate)) ? self::LABEL_NONE : 0);
      $baseTimeData = $baseTimeData + array(date("H:00",$startDate) => $this->isInValidDatetime(date("Y-m-d",$startDate)) ? self::LABEL_NONE : '00:00:00');
      $startDate = strtotime("+1 hour", $startDate);
    }
    $startDate = strtotime($type);
    $type = str_replace("/", "-", $type);
    return ['date_format' => $date_format,'anotherWindowDateFormat' => $anotherWindowDateFormat,'correctStartDate' => $correctStartDate,'correctEndDate' => $correctEndDate,
    'baseData' => $baseData,'baseTimeData' => $baseTimeData,'timeType' => $timeType,'date' => $date];
  }

  //オペレータレポートデータ取得
  private function summaryOperatorSql($date_format,$avg_date_format,$correctStartDate,$correctEndDate) {
    //オペレータ情報取得
    $users = "SELECT id,display_name FROM m_users
    WHERE
      m_users.m_companies_id = ?
    AND
      m_users.permission_level != ?
    AND
      m_users.del_flg = ?";

    $users = $this->MUser->query($users,
      array($this->userInfo['MCompany']['id'],C_AUTHORITY_SUPER,0));
    //オペレータ全員対象
    $allOperatorInfo = 0;

    //チャットリクエスト件数
    $requestNumber = $this->getSummaryOperatorRequestInfo($date_format,$allOperatorInfo,$correctStartDate,$correctEndDate);
    $allData = [];
    $allData['requestNumber'] = $requestNumber;

    //ログイン件数
    $loginNumber = $this->getSummaryLoginOperatorInfo($date_format,$allOperatorInfo,$correctStartDate,$correctEndDate);
    $allData['loginNumber'] = $loginNumber;

    //チャット応対件数
    $responseNumber = $this->getSummaryOperatorResponseInfo($date_format,$allOperatorInfo,$correctStartDate,$correctEndDate);
    $allData['responseNumber'] = $responseNumber;

    //チャット有効件数
    $effectivenessNumber = $this->getSummaryOperatorEffectivenessInfo($date_format,$allOperatorInfo,$correctStartDate,$correctEndDate);
    $allData['effectivenessNumber'] = $effectivenessNumber;

    //平均消費者待機時間時間
    $avgEnteringRommTime = $this->getSummaryOperatorAvgEnteringRommInfo($avg_date_format,$allOperatorInfo,$correctStartDate,$correctEndDate);
    $allData['avgEnteringRommTime'] = $avgEnteringRommTime;

    //平均応答時間
    $responseTime = $this->getSummaryOperatorAvgResponseInfo($avg_date_format,$allOperatorInfo,$correctStartDate,$correctEndDate);
    $allData['responseTime'] = $responseTime;

    $divideOperatorDatas =$this->divideOperatorDatas($users,$allData);
    return $divideOperatorDatas;
  }

  //オペレータごとのレポート作成
  private function divideOperatorDatas($users,$allData) {
    foreach($users as $k => $v){
      //チャットリクエスト件数
      if(!empty($allData['requestNumber'])) {
        foreach($allData['requestNumber'] as $k2 => $v2) {
          if($v['m_users']['id'] == $v2['thcau']['userId']) {
            $users[$k]['requestNumber'] = $v2[0]['request_count'];
          }
        }
      }
      //ログイン件数
      if(!empty($allData['loginNumber'])) {
        foreach($allData['loginNumber'] as $k2 => $v2) {
          if($v['m_users']['id'] == $v2['login']['userId']) {
            $users[$k]['loginNumber'] = $v2[0]['login_count'];
          }
        }
      }

      if(!empty($allData['responseNumber'])) {
        foreach($allData['responseNumber'] as $k2 => $v2) {
          if($v['m_users']['id'] == $v2['thcl']['userId']) {
            $users[$k]['responseNumber'] = $v2[0]['response_count'];
          }
        }
      }
      //有効件数,有効率
      if(!empty($allData['effectivenessNumber'])) {
        foreach($allData['effectivenessNumber'] as $k2 => $v2) {
          if(!empty($v2) and !empty($users[$k]['responseNumber'])){
            if($v['m_users']['id'] == $v2['thcl']['userId']) {
              $users[$k]['effectivenessNumber'] = $v2[0]['effectiveness_count'];
              $users[$k]['effectivenessRate'] = round($users[$k]['effectivenessNumber']/$users[$k]['responseNumber']*100);
            }
          }
          if(empty($users[$k]['responseNumber'])) {
            $users[$k]['effectivenessRate'] = self::LABEL_INVALID;
          }
        }
      }
      else {
        $users[$k]['effectivenessRate'] = self::LABEL_INVALID;
      }
      //平均消費者待機時間
      if(!empty($allData['avgEnteringRommTime'])) {
        $allAvgEnteringRommTime = 0;
        $totalConsumerWaitingAvgTimeDataCnt = 0;
        foreach($allData['avgEnteringRommTime'] as $k2 => $v2) {
          if($v['m_users']['id'] == $v2['thcl2']['userId']) {
            $allAvgEnteringRommTime = $allAvgEnteringRommTime + round($v2[0]['average']);
            if(!$this->isInValidDatetime($v2[0]['date'])) {
              $totalConsumerWaitingAvgTimeDataCnt++;
            }
          }
        }
        if(!empty($allAvgEnteringRommTime) && $totalConsumerWaitingAvgTimeDataCnt != 0) {
          $users[$k]['avgEnteringRommTime'] = $this->changeTimeFormat($allAvgEnteringRommTime/$totalConsumerWaitingAvgTimeDataCnt);
        }
        else {
          $users[$k]['avgEnteringRommTime'] = '00:00:00';
        }
      }
      else{
        $users[$k]['avgEnteringRommTime'] = '00:00:00';
      }
      //平均応答時間
      if(!empty($allData['responseTime'])) {
        $allAvgResponseTime = 0;
        $totalConsumerWaitingAvgTimeDataCnt = 0;
        foreach($allData['responseTime'] as $k2 => $v2) {
          if($v['m_users']['id'] == $v2['thcl2']['userId']) {
            $allAvgResponseTime = $allAvgResponseTime + round($v2[0]['average']);
            if(!$this->isInValidDatetime($v2[0]['date'])) {
              $totalConsumerWaitingAvgTimeDataCnt++;
            }
          }
        }
        if(!empty($allAvgResponseTime) && $totalConsumerWaitingAvgTimeDataCnt != 0) {
          $users[$k]['responseTime'] = $this->changeTimeFormat($allAvgResponseTime/$totalConsumerWaitingAvgTimeDataCnt);
        }
        else {
          $users[$k]['responseTime'] = '00:00:00';
        }
      }
      else {
        $users[$k]['responseTime'] = '00:00:00';
      }
    }
    return $users;
  }

  //オペレータ情報取得
  private function getUserInfo($type,$userId) {
    //オペレータ1人の情報取得の場合
    if($type == 'userId') {
      $users = "SELECT id,m_companies_id,display_name FROM m_users
      WHERE
        m_users.m_companies_id = ?
      AND
        m_users.id = ?
      AND
        m_users.permission_level != ?";

      $users = $this->MUser->query($users,
        array($this->userInfo['MCompany']['id'],$userId,C_AUTHORITY_SUPER));

      return $users;
    }
    //各項目の情報取得の場合
    else if($type == 'item') {
      //削除対象ではないフラグ
      $noDelFlg = 0;
      $users = "SELECT id,m_companies_id,display_name FROM m_users
      WHERE
        m_users.m_companies_id = ?
      AND
        m_users.del_flg = ?
      AND
        m_users.permission_level != ?";

      $users = $this->MUser->query($users,
      array($this->userInfo['MCompany']['id'],$noDelFlg,C_AUTHORITY_SUPER));

      return $users;
    }
  }

  //各オペレータ情報
  private function getPrivateOperatorInfo($users,$date_format,$correctStartDate,$correctEndDate,
    $baseData,$baseTimeData,$userId) {
    $requestNumberData = [];
    $loginNumberData = [];
    $responseNumberData = [];
    $effectivenessNumberData = [];
    $effectivenessRate = [];
    $avgEnteringRommTimeData = [];
    $avgForcalculation = [];
    $responseAvgTime = [];

    //チャットリクエスト件数
    $this->log("BEGIN RequestInfo : ".$this->getDateWithMilliSec(),LOG_DEBUG);
    $requestNumber = $this->getSummaryOperatorRequestInfo($date_format,$userId,$correctStartDate,$correctEndDate);
    foreach($requestNumber as $k => $v) {
      $requestNumberData =  $requestNumberData + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : intval($v[0]['request_count']));
    }
    $requestNumberData = array_merge($baseData,$requestNumberData);
    //チャットリクエスト件数合計値
    $allRequestNumberData = array_sum($requestNumberData);
    $this->log("END RequestInfo : ".$this->getDateWithMilliSec(),LOG_DEBUG);

    $this->log("BEGIN LoginOperatorInfo : ".$this->getDateWithMilliSec(),LOG_DEBUG);
    //ログイン件数
    $loginNumber = $this->getSummaryLoginOperatorInfo($date_format,$userId,$correctStartDate,$correctEndDate);
    foreach($loginNumber as $k => $v) {
      $loginNumberData =  $loginNumberData + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : intval($v[0]['login_count']));
    }
    $loginNumberData = array_merge($baseData,$loginNumberData);
    //ログイン件数合計値
    $allLoginNumberData = array_sum($loginNumberData);
    $this->log("END LoginOperatorInfo : ".$this->getDateWithMilliSec(),LOG_DEBUG);

    $this->log("BEGIN ResponseInfo : ".$this->getDateWithMilliSec(),LOG_DEBUG);
    $responseNumber = $this->getSummaryOperatorResponseInfo($date_format,$userId,$correctStartDate,$correctEndDate);
    foreach($responseNumber as $k => $v) {
      $responseNumberData =  $responseNumberData + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : intval($v[0]['response_count']));
    }
    $responseNumberData = array_merge($baseData,$responseNumberData);
    //チャット応対件数合計値
    $allResponseNumberData = array_sum($responseNumberData);
    $this->log("END ResponseInfo : ".$this->getDateWithMilliSec(),LOG_DEBUG);

    $this->log("BEGIN EffectivenessInfo : ".$this->getDateWithMilliSec(),LOG_DEBUG);
    //チャット有効件数
    $effectivenessNumber = $this->getSummaryOperatorEffectivenessInfo($date_format,$userId,$correctStartDate,$correctEndDate);
    foreach($effectivenessNumber as $k => $v) {
      $effectivenessNumberData =  $effectivenessNumberData + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : intval($v[0]['effectiveness_count']));
      if( $v[0]['effectiveness_count'] != 0 && $responseNumberData[$v[0]['date']] != 0){
        $effectivenessRate = $effectivenessRate + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : round($v[0]['effectiveness_count']/$responseNumberData[$v[0]['date']]*100));
      } else if($responseNumberData[$v[0]['date']] === 0) {
        $effectivenessRate = $effectivenessRate + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : self::LABEL_INVALID);
      }
    }
    $effectivenessNumberData = array_merge($baseData,$effectivenessNumberData);
    //チャット有効件数合計値
    $allEffectivenessNumberData = array_sum($effectivenessNumberData);
    //チャット有効率
    $effectivenessRate = array_merge($this->convertBaseDataForPercent($baseData),$effectivenessRate);
    foreach($responseNumberData as $k2 => $v2) {
      if(intval($v2) !== 0 && strcmp($effectivenessRate[$k2],self::LABEL_INVALID) === 0) {
        // 無効データと判定されたがリクエストチャット件数が存在する場合は0%（自動返信なし）として返却
        $effectivenessRate[$k2] = 0;
      }
    }

    //合計有効率
    $allEffectivenessRate = 0;
    if($allEffectivenessNumberData != 0 and $allResponseNumberData != 0) {
      $allEffectivenessRate = round($allEffectivenessNumberData/$allResponseNumberData*100);
    }
    else if($allEffectivenessNumberData === 0 && $allResponseNumberData != 0) {
      // リクエストチャット件数はあるけど自動返信がない場合
      $allEffectivenessRate = 0;
    }
    else {
      // リクエストチャットが0件の場合（無効データ）
      $allEffectivenessRate = self::LABEL_INVALID;
    }
    $this->log("END EffectivenessInfo : ".$this->getDateWithMilliSec(),LOG_DEBUG);

    $this->log("BEGIN AvgEnteringRommInfo : ".$this->getDateWithMilliSec(),LOG_DEBUG);
    //平均入室時間
    $avgEnteringRommTime = $this->getSummaryOperatorAvgEnteringRommInfo($date_format,$userId,$correctStartDate,$correctEndDate);

    $totalConsumerWaitingAvgTimeDataCnt = 0;
    foreach($avgEnteringRommTime as $k => $v) {
      $timeFormat = $this->changeTimeFormat(round($v[0]['average']));
      $avgEnteringRommTimeData =  $avgEnteringRommTimeData + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : $timeFormat);
      $avgForcalculation = $avgForcalculation + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : round($v[0]['average']));
      if(!$this->isInValidDatetime($v[0]['date'])) {
        $totalConsumerWaitingAvgTimeDataCnt++;
      }
    }

    // 0件だったらゼロ割を防ぐため1にする
    if($totalConsumerWaitingAvgTimeDataCnt === 0) {
      $totalConsumerWaitingAvgTimeDataCnt = 1;
    }
    //消費者待機平均時間
    $avgEnteringRommTimeData = array_merge($baseTimeData,$avgEnteringRommTimeData);

    //全消費者待機平均時間
    $allAvgEnteringRommTimeData = 0;
    if(!empty($v)) {
      $allAvgEnteringRommTimeData = array_sum($avgForcalculation)/$totalConsumerWaitingAvgTimeDataCnt;
    }
    $allAvgEnteringRommTimeData = $this->changeTimeFormat($allAvgEnteringRommTimeData);
    $this->log("END AvgEnteringRommInfo : ".$this->getDateWithMilliSec(),LOG_DEBUG);

    $this->log("BEGIN AvgResponseInfo : ".$this->getDateWithMilliSec(),LOG_DEBUG);
    //平均応答時間
    $responseTime = $this->getSummaryOperatorAvgResponseInfo($date_format,$userId,$correctStartDate,$correctEndDate);

    $avgForcalculation = [];

    $totalResponseAvgTimeDataCnt = 0;
    foreach($responseTime as $k => $v) {
      $timeFormat = $this->changeTimeFormat(round($v[0]['average']));
      $responseAvgTime =  $responseAvgTime + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : $timeFormat);
      $avgForcalculation = $avgForcalculation + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : round($v[0]['average']));
      if(!$this->isInValidDatetime($v[0]['date'])) {
        $totalResponseAvgTimeDataCnt++;
      }
    }

    // 0件だったらゼロ割を防ぐため1にする
    if($totalResponseAvgTimeDataCnt === 0) {
      $totalResponseAvgTimeDataCnt = 1;
    }

    //平均応答時間
    $responseAvgTimeData = array_merge($baseTimeData,$responseAvgTime);

    //全応答平均時間
    $allResponseAvgTimeData = 0;
    if(!empty($v)) {
      $allResponseAvgTimeData = array_sum($avgForcalculation)/$totalResponseAvgTimeDataCnt;
    }
    $allResponseAvgTimeData = $this->changeTimeFormat($allResponseAvgTimeData);
    $this->log("END AvgResponseInfo : ".$this->getDateWithMilliSec(),LOG_DEBUG);

    $data = ['users' => $users,'loginNumberData' => $loginNumberData,'allLoginNumberData' => $allLoginNumberData,
    'requestNumberData' => $requestNumberData,'allRequestNumberData' => $allRequestNumberData,
    'responseNumberData' => $responseNumberData,'allResponseNumberData' => $allResponseNumberData,
    'effectivenessNumberData' => $effectivenessNumberData,'allEffectivenessNumberData' => $allEffectivenessNumberData,
    'avgEnteringRommTimeData' => $avgEnteringRommTimeData,'allAvgEnteringRommTimeData' => $allAvgEnteringRommTimeData,
    'responseAvgTimeData' => $responseAvgTimeData,'allResponseAvgTimeData' => $allResponseAvgTimeData,
    'effectivenessRate' => $effectivenessRate,'allEffectivenessRate' => $allEffectivenessRate,];
    return $data;
  }

  private function getSummaryLoginOperatorInfo($date_format,$userId,$correctStartDate,$correctEndDate) {
    //ログイン件数
    //全オペレータ検索の場合
    if($userId == 0) {
      $loginNumber = "SELECT date_format(login.created, ?) as date,m_users_id as userId,
      count(login.id) as login_count
      FROM t_logins as login
      WHERE
        login.m_companies_id = ?
      AND
        login.created between ? and ?
      group by date,m_users_id";

      $loginNumber = $this->TLogin->query($loginNumber,
      array($date_format,$this->userInfo['MCompany']['id'],$correctStartDate,$correctEndDate));
    }
    //1人のオペレータ検索の場合
    else {
      $loginNumber = "SELECT date_format(login.created, ?) as date,m_users_id as userId,
      count(login.id) as login_count
      FROM t_logins as login
      WHERE
        login.m_companies_id = ?
      AND
        login.m_users_id = ?
      AND
        login.created between ? and ?
      group by date,m_users_id";

      $loginNumber = $this->TLogin->query($loginNumber,
      array($date_format,$this->userInfo['MCompany']['id'],$userId,$correctStartDate,$correctEndDate));
    }
    return $loginNumber;
  }

  private function getSummaryOperatorRequestInfo($date_format,$userId,$correctStartDate,$correctEndDate) {
    //チャットリクエスト件数
    //全オペレータ検索の場合
    if($userId == 0) {
    $requestNumber = "SELECT
      date_format(th.access_date, ?) as date,thcau.m_users_id as userId,
      count(thcau.id) as request_count
      FROM (select id,m_users_id,t_history_chat_logs_id,m_companies_id from t_history_chat_active_users
      force index(idx_m_companies_id_users_id_chat_logs_id) where m_companies_id = ?) as thcau ,
      t_history_chat_logs as thcl,t_histories as th
      WHERE
        thcau.t_history_chat_logs_id = thcl.id
      AND
        thcl.t_histories_id = th.id
      AND
        th.access_date between ? and ?
      group by date,userId";

      $requestNumber = $this->THistoryChatActiveUsers->query($requestNumber,
      array($date_format,$this->userInfo['MCompany']['id'],$correctStartDate,$correctEndDate,));
    }
    //1人のオペレータ検索の場合
    else {
      $requestNumber = "SELECT
        date_format(th.access_date, ?) as date,thcau.m_users_id as userId,
        count(thcau.id) as request_count
        FROM (select id,t_history_chat_logs_id,m_companies_id,m_users_id from t_history_chat_active_users
        force index(idx_m_companies_id_users_id_chat_logs_id) where m_companies_id = ? and m_users_id = ?) as thcau,
        t_history_chat_logs as thcl,t_histories as th
        WHERE
          thcau.t_history_chat_logs_id = thcl.id
        AND
          thcl.t_histories_id = th.id
        AND
          th.access_date between ? and ?
       group by date,userId";

        $requestNumber = $this->THistoryChatActiveUsers->query($requestNumber,
      array($date_format,$this->userInfo['MCompany']['id'],$userId,$correctStartDate,$correctEndDate));
    }
    return $requestNumber;
  }

  private function getSummaryOperatorResponseInfo($date_format,$userId,$correctStartDate,$correctEndDate) {
    //チャット応対件数
    //全オペレータ検索の場合
    if($userId == 0) {
      $responseNumber = "SELECT date_format(th.access_date, ?) as date,
      m_users_id as userId,count(th.id) as response_count
      FROM (select t_histories_id,m_companies_id,m_users_id,message_type from t_history_chat_logs
      force index(idx_t_history_chat_logs_message_type_companies_id_users_id)
      where message_type = ? and m_companies_id = ?)
      as thcl, t_histories as th
      WHERE
        thcl.t_histories_id = th.id
      AND
        th.access_date between ? and ?
      group by date,userId";

      $responseNumber = $this->THistory->query($responseNumber,
        array($date_format,$this->chatMessageType['messageType']['enteringRoom'],
        $this->userInfo['MCompany']['id'],$correctStartDate,$correctEndDate,));
    }
    //1人のオペレータ検索の場合
    else {
      $responseNumber = "SELECT
      date_format(th.access_date, ?) as date,m_users_id as userId,count(th.id) as response_count
      FROM (select t_histories_id,m_companies_id,m_users_id,message_type from
      t_history_chat_logs force index(idx_t_history_chat_logs_message_type_companies_id_users_id)
      where message_type = ? and  m_companies_id = ? and m_users_id = ?)as thcl,
      t_histories as th
      WHERE
        thcl.t_histories_id = th.id
      AND
        th.access_date between ? and ?
      group by date,userId";

      $responseNumber = $this->THistory->query($responseNumber,array($date_format,$this->chatMessageType['messageType']['enteringRoom'],
        $this->userInfo['MCompany']['id'],$userId,$correctStartDate,$correctEndDate,));
    }
    return $responseNumber;
  }

  private function getSummaryOperatorEffectivenessInfo($date_format,$userId,$correctStartDate,$correctEndDate) {
    //チャット有効件数
    //全オペレータ検索の場合
    if($userId == 0) {
      $effectivenessNumber = "SELECT
      date_format(th.access_date,?) as date,m_users_id as userId,count(th.id) as effectiveness_count
      FROM t_histories as th, (select t_histories_id,m_companies_id,m_users_id
      from t_history_chat_logs force index(idx_t_history_chat_logs_achievement_flg_companies_id_users_id)
      where achievement_flg = ? and m_companies_id = ? ) as thcl
      WHERE
        thcl.t_histories_id = th.id
      AND
        th.access_date between ? and ?
      group by date,m_users_id";

      $effectivenessNumber = $this->THistory->query($effectivenessNumber,array($date_format,$this->chatMessageType['achievementFlg']['effectiveness'],
        $this->userInfo['MCompany']['id'],$correctStartDate,$correctEndDate));
    }
    //1人のオペレータ検索の場合
    else {
      $effectivenessNumber = "SELECT
      date_format(th.access_date, ?) as date,m_users_id as userId,count(th.id) as effectiveness_count
      FROM t_histories as th, (select t_histories_id,m_companies_id,m_users_id
      from t_history_chat_logs force index(idx_t_history_chat_logs_achievement_flg_companies_id_users_id)
      where achievement_flg = ? and m_companies_id = ? and m_users_id = ?) as thcl
      WHERE
        thcl.t_histories_id = th.id
      AND
        th.access_date between ? and ?
      group by date,m_users_id";

      $effectivenessNumber = $this->THistory->query($effectivenessNumber,array($date_format,$this->chatMessageType['achievementFlg']['effectiveness'],
        $this->userInfo['MCompany']['id'],$userId,$correctStartDate,$correctEndDate));
    }
    return $effectivenessNumber;
  }

  private function getSummaryOperatorAvgEnteringRommInfo($date_format,$userId,$correctStartDate,$correctEndDate) {
    //消費者待機時間
    //全オペレータ検索の場合
    if($userId == 0) {
      $avgEnteringRommTime = "SELECT date_format(th.access_date,?) as date,thcl2.m_users_id as userId,
      AVG(UNIX_TIMESTAMP(thcl2.created) - UNIX_TIMESTAMP(thcl.created)) as average
      FROM t_histories as th,
      (select t_histories_id,m_companies_id, message_request_flg,created,message_distinction
      from t_history_chat_logs force index(idx_t_history_chat_logs_request_flg_companies_id_users_id)
      where message_request_flg = ? and  m_companies_id = ? group by t_histories_id) as thcl,
      (select t_histories_id,m_companies_id, message_type,created,message_distinction,m_users_id
      from t_history_chat_logs force index(idx_t_history_chat_logs_message_type_companies_id_users_id)
      where message_type = ? and m_companies_id = ? group by t_histories_id) as thcl2
      WHERE
        thcl.t_histories_id = th.id
      AND
        th.id = thcl2.t_histories_id
      AND
        thcl.t_histories_id = thcl2.t_histories_id
      AND
        thcl.message_distinction = thcl2.message_distinction
      AND
        th.access_date between ? and ?
      group by date,m_users_id";

      $avgEnteringRommTime = $this->THistory->query($avgEnteringRommTime,array($date_format,$this->chatMessageType['requestFlg']['effectiveness'],
      $this->userInfo['MCompany']['id'],$this->chatMessageType['messageType']['enteringRoom'],
      $this->userInfo['MCompany']['id'],$correctStartDate,$correctEndDate));
    }
    //1人のオペレータ検索の場合
    else {
      $avgEnteringRommTime = "SELECT date_format(th.access_date,?) as date,thcl2.m_users_id as userId,
      AVG(UNIX_TIMESTAMP(thcl2.created) - UNIX_TIMESTAMP(thcl.created)) as average
      FROM t_histories as th,
      (select t_histories_id,m_companies_id, message_request_flg,created,message_distinction
      from t_history_chat_logs force index(idx_t_history_chat_logs_request_flg_companies_id_users_id)
      where message_request_flg = ? and  m_companies_id = ? group by t_histories_id) as thcl,
      (select t_histories_id,m_companies_id, message_type,created,message_distinction,m_users_id
      from t_history_chat_logs force index(idx_t_history_chat_logs_message_type_companies_id_users_id)
      where message_type = ? and m_companies_id = ? and m_users_id = ?
      group by t_histories_id) as thcl2
      WHERE
        thcl.t_histories_id = th.id
      AND
        th.id = thcl2.t_histories_id
      AND
        thcl.t_histories_id = thcl2.t_histories_id
      AND
        thcl.message_distinction = thcl2.message_distinction
      AND
        th.access_date between ? and ?
      group by date,m_users_id";

      $avgEnteringRommTime = $this->THistory->query($avgEnteringRommTime,
        array($date_format,$this->chatMessageType['requestFlg']['effectiveness'],$this->userInfo['MCompany']['id'],
        $this->chatMessageType['messageType']['enteringRoom'],$this->userInfo['MCompany']['id'],
        $userId,$correctStartDate,$correctEndDate));
    }
    return $avgEnteringRommTime;
  }

  private function getSummaryOperatorAvgResponseInfo($date_format,$userId,$correctStartDate,$correctEndDate) {
    //平均応答時間
    //全オペレータ検索の場合
    if($userId == 0) {
      $responseTime = "SELECT date_format(th.access_date, ?) as date,thcl2.m_users_id as userId,
      AVG(UNIX_TIMESTAMP(thcl2.created) - UNIX_TIMESTAMP(thcl.created)) as average
      FROM t_histories as th,(select t_histories_id,
       message_request_flg,created,message_distinction
      from t_history_chat_logs force index(idx_t_history_chat_logs_request_flg_companies_id_users_id)
      where message_request_flg = ? and m_companies_id = ? group by t_histories_id) as thcl,
      (select t_histories_id, message_type,m_users_id,created,message_distinction
      from t_history_chat_logs force index(idx_t_history_chat_logs_message_type_companies_id_users_id)
      where message_type = ? and m_companies_id = ? group by t_histories_id) as thcl2
      WHERE
        thcl.t_histories_id = th.id
      AND
        th.id = thcl2.t_histories_id
      AND
        thcl.t_histories_id = thcl2.t_histories_id
      AND
        thcl.message_distinction = thcl2.message_distinction
      AND
        th.access_date between ? and ?
      group by date,m_users_id";

      $responseTime = $this->THistory->query($responseTime,array($date_format,$this->chatMessageType['requestFlg']['effectiveness'],
        $this->userInfo['MCompany']['id'],$this->chatMessageType['messageType']['operatorMessage'],
        $this->userInfo['MCompany']['id'],$correctStartDate,$correctEndDate));
    }
    //1人のオペレータ検索の場合
    else {
      $responseTime = "SELECT date_format(th.access_date, ?) as date,thcl2.m_users_id as userId,
      AVG(UNIX_TIMESTAMP(thcl2.created) - UNIX_TIMESTAMP(thcl.created)) as average
      FROM t_histories as th,(select t_histories_id,
       message_request_flg,created,message_distinction
      from t_history_chat_logs force index(idx_t_history_chat_logs_request_flg_companies_id_users_id)
      where message_request_flg = ? and m_companies_id = ? group by t_histories_id) as thcl,
      (select t_histories_id, message_type,m_users_id,created,message_distinction
      from t_history_chat_logs force index(idx_t_history_chat_logs_message_type_companies_id_users_id)
      where message_type = ? and m_companies_id = ? and
      m_users_id = ? group by t_histories_id) as thcl2
      WHERE
        thcl.t_histories_id = th.id
      AND
        th.id = thcl2.t_histories_id
      AND
        thcl.t_histories_id = thcl2.t_histories_id
      AND
        thcl.message_distinction = thcl2.message_distinction
      AND
        th.access_date between ? and ?
      group by date,m_users_id";

      $responseTime = $this->THistory->query($responseTime,array($date_format,$this->chatMessageType['requestFlg']['effectiveness'],
        $this->userInfo['MCompany']['id'],$this->chatMessageType['messageType']['operatorMessage'],
        $this->userInfo['MCompany']['id'],$userId,$correctStartDate,$correctEndDate));
    }
    return $responseTime;
  }

  private function getEachAllOperatorInfo($users,$date_format,$correctStartDate,$correctEndDate,$baseData,$baseTimeData,$item) {
    //各項目オペレータ情報取得
    //オペレータ情報取得
    //全オペレータ検索
    $allOperatorInfo = 0;
    //ログイン件数
    if($item == 'login') {
      $users = $this->getAllLoginOperatorInfo($date_format,$allOperatorInfo,
        $correctStartDate,$correctEndDate,$baseData,$users);
      $this->set('item','ログイン件数');
    }
    //リクエスト件数
    if($item == 'requestChat') {
      $users = $this->getAllRequestOperatorInfo($date_format,$allOperatorInfo,
        $correctStartDate,$correctEndDate,$baseData,$users);
      $this->set('item','リクエスト件数');
    }
    //応答件数
    if($item == 'responseChat') {
      $users = $this->getAllResponseOperatorInfo($date_format,$allOperatorInfo,
        $correctStartDate,$correctEndDate,$baseData,$users);
      $this->set('item','応対件数');
    }
    //有効件数
    if($item == 'effectiveness') {
      $users = $this->getAllEffectivenessOperatorInfo($date_format,$allOperatorInfo,
        $correctStartDate,$correctEndDate,$baseData,$users);
      $this->set('item','有効件数');
    }
    //平均消費者待機時間
    if($item == 'avgConsumersWaitTime') {
      $users = $this->getAllAvgEnteringRommTimeOperatorInfo($date_format,$allOperatorInfo,
        $correctStartDate,$correctEndDate,$baseTimeData,$users);
      $this->set('item','平均消費者待機時間');
    }
    //平均応答時間
    if($item == 'avgResponseTime') {
      $users = $this->getAllResponseAvgTimeOperatorInfo($date_format,$allOperatorInfo,
        $correctStartDate,$correctEndDate,$baseTimeData,$users);
      $this->set('item','平均応答時間');
    }
    //有効率
    if($item == 'effectivenessRate') {
      $users = $this->getAllEffectivenessRateOperatorInfo($date_format,$allOperatorInfo,
        $correctStartDate,$correctEndDate,$baseData,$users);
      $this->set('item','有効率');
    }
    return ['users' => $users];
  }

  private function getAllLoginOperatorInfo($date_format,$userId,$correctStartDate,$correctEndDate,$baseData,$users) {
    //ログイン件数
    $loginNumber = $this->getSummaryLoginOperatorInfo($date_format,$userId,$correctStartDate,$correctEndDate);
    $eachOperatorLoginNumber = [];
    foreach($users as $k => $v){
      $eachDayLoginNumber = [];
      if(!empty($loginNumber)) {
        foreach($loginNumber as $k2 => $v2) {
          if($v['m_users']['id'] == $v2['login']['userId']) {
            $eachDayLoginNumber =  $eachDayLoginNumber + array($v2[0]['date'] => $this->isInValidDatetime($v2[0]['date']) ? self::LABEL_NONE : intval($v2[0]['login_count']));
          }
        }
      }
      $eachOperatorLoginNumber[$k]['display_name'] = $v['m_users']['display_name'];
      $eachOperatorLoginNumber[$k]['loginNumber'] = array_merge($baseData,$eachDayLoginNumber);
      $eachOperatorLoginNumber[$k]['allLoginNumber'] = array_sum($eachDayLoginNumber);
    }
    return $eachOperatorLoginNumber;
  }

  private function getAllRequestOperatorInfo($date_format,$userId,$correctStartDate,$correctEndDate,$baseData,$users) {
    //チャットリクエスト件数
    $requestNumber = $this->getSummaryOperatorRequestInfo($date_format,$userId,$correctStartDate,$correctEndDate);
    $eachOperatorRequestNumber = [];
    foreach($users as $k => $v){
      $eachDayRequestNumber = [];
      if(!empty($requestNumber)) {
        foreach($requestNumber as $k2 => $v2) {
          if($v['m_users']['id'] == $v2['thcau']['userId']) {
            $eachDayRequestNumber =  $eachDayRequestNumber + array($v2[0]['date'] => $this->isInValidDatetime($v2[0]['date']) ? self::LABEL_NONE : intval($v2[0]['request_count']));
          }
        }
      }
      $eachOperatorRequestNumber[$k]['display_name'] = $v['m_users']['display_name'];
      $eachOperatorRequestNumber[$k]['requestNumber'] = array_merge($baseData,$eachDayRequestNumber);
      $eachOperatorRequestNumber[$k]['allRequestNumber'] =  array_sum($eachDayRequestNumber);
    }
    return $eachOperatorRequestNumber;
  }

  private function getAllResponseOperatorInfo($date_format,$userId,$correctStartDate,$correctEndDate,$baseData,$users) {
    //応答件数
    $responseNumber = $this->getSummaryOperatorResponseInfo($date_format,$userId,$correctStartDate,$correctEndDate);
    $eachOperatorResponseNumber = [];
    foreach($users as $k => $v){
      $eachDayResponseNumber = [];
      if(!empty($responseNumber)) {
        foreach($responseNumber as $k2 => $v2) {
          if($v['m_users']['id'] == $v2['thcl']['userId']) {
            $eachDayResponseNumber =  $eachDayResponseNumber + array($v2[0]['date'] => $this->isInValidDatetime($v2[0]['date']) ? self::LABEL_NONE : intval($v2[0]['response_count']));
          }
        }
      }
      $eachOperatorResponseNumber[$k]['display_name'] = $v['m_users']['display_name'];
      $eachOperatorResponseNumber[$k]['responseNumber'] = array_merge($baseData,$eachDayResponseNumber);
      $eachOperatorResponseNumber[$k]['allResponseNumber'] =  array_sum($eachDayResponseNumber);
    }
    return $eachOperatorResponseNumber;
  }

  private function getAllEffectivenessOperatorInfo($date_format,$userId,$correctStartDate,$correctEndDate,$baseData,$users) {
    //有効件数
    $effectivenessNumber = $this->getSummaryOperatorEffectivenessInfo($date_format,$userId,$correctStartDate,$correctEndDate);
    $eachOperatorEffectivenessNumber = [];
    foreach($users as $k => $v){
      $eachDayEffectivenessNumber = [];
      if(!empty($effectivenessNumber)) {
        foreach($effectivenessNumber as $k2 => $v2) {
          if($v['m_users']['id'] == $v2['thcl']['userId']) {
            $eachDayEffectivenessNumber =  $eachDayEffectivenessNumber + array($v2[0]['date'] => $this->isInValidDatetime($v2[0]['date']) ? self::LABEL_NONE : intval($v2[0]['effectiveness_count']));
          }
        }
      }
      $eachOperatorEffectivenessNumber[$k]['display_name'] = $v['m_users']['display_name'];
      $eachOperatorEffectivenessNumber[$k]['effectivenessNumber'] = array_merge($baseData,$eachDayEffectivenessNumber);
      $eachOperatorEffectivenessNumber[$k]['allEffectivenessNumber'] =  array_sum($eachDayEffectivenessNumber);
    }
    return $eachOperatorEffectivenessNumber;
  }

  private function getAllAvgEnteringRommTimeOperatorInfo($date_format,$userId,$correctStartDate,$correctEndDate,$baseTimeData,$users) {
    //消費者待機時間
    $avgEnteringRommTimeNumber = $this->getSummaryOperatorAvgEnteringRommInfo($date_format,$userId,$correctStartDate,$correctEndDate);
    $eachOperatorAvgEnteringRommTime = [];
    foreach($users as $k => $v){
      $eachDayavgEnteringRommTimeNumber = [];
      $avgForcalculation = [];
      $totalConsumerWaitingAvgTimeDataCnt = 0;
      foreach($avgEnteringRommTimeNumber as $k2 => $v2) {
        if($v['m_users']['id'] == $v2['thcl2']['userId']) {
          $timeFormat = $this->changeTimeFormat(round($v2[0]['average']));
          $eachDayavgEnteringRommTimeNumber =  $eachDayavgEnteringRommTimeNumber + array($v2[0]['date'] => $this->isInValidDatetime($v2[0]['date']) ? self::LABEL_NONE : $timeFormat);
          $avgForcalculation = $avgForcalculation + array($v2[0]['date'] => $this->isInValidDatetime($v2[0]['date']) ? self::LABEL_NONE : round($v2[0]['average']));
          if(!$this->isInValidDatetime($v2[0]['date'])) {
            $totalConsumerWaitingAvgTimeDataCnt++;
          }
        }
      }
      // 0件だったらゼロ割を防ぐため1にする
      if($totalConsumerWaitingAvgTimeDataCnt === 0) {
        $totalConsumerWaitingAvgTimeDataCnt = 1;
      }

      $eachOperatorAvgEnteringRommTime[$k]['display_name'] = $v['m_users']['display_name'];
      $eachOperatorAvgEnteringRommTime[$k]['avgEnteringRommTimeNumber'] = array_merge($baseTimeData,$eachDayavgEnteringRommTimeNumber);
      $avgEnteringRommTime = 0;
      if(!empty($v2)) {
        $avgEnteringRommTime = array_sum($avgForcalculation)/$totalConsumerWaitingAvgTimeDataCnt;
      }
      //全体消費者待機時間
      $eachOperatorAvgEnteringRommTime[$k]['allAvgEnteringRommTimeData'] = $this->changeTimeFormat($avgEnteringRommTime);
    }
    return $eachOperatorAvgEnteringRommTime;
  }

  private function getAllResponseAvgTimeOperatorInfo($date_format,$userId,$correctStartDate,$correctEndDate,$baseTimeData,$users) {
    //応答時間
    $responseAvgTimeNumber = $this->getSummaryOperatorAvgResponseInfo($date_format,$userId,$correctStartDate,$correctEndDate);
    $eachOperatorResponseTime = [];
    foreach($users as $k => $v){
      $eachDayResponseAvgTimeNumber = [];
      $avgForcalculation = [];
      $totalConsumerWaitingAvgTimeDataCnt = 0;
      foreach($responseAvgTimeNumber as $k2 => $v2) {
        if($v['m_users']['id'] == $v2['thcl2']['userId']) {
          $timeFormat = $this->changeTimeFormat(round($v2[0]['average']));
          $eachDayResponseAvgTimeNumber =  $eachDayResponseAvgTimeNumber + array($v2[0]['date'] => $this->isInValidDatetime($v2[0]['date']) ? self::LABEL_NONE : $timeFormat);
          $avgForcalculation = $avgForcalculation + array($v2[0]['date'] => $this->isInValidDatetime($v2[0]['date']) ? self::LABEL_NONE : round($v2[0]['average']));
          if(!$this->isInValidDatetime($v2[0]['date'])) {
            $totalConsumerWaitingAvgTimeDataCnt++;
          }
        }
      }
      // 0件だったらゼロ割を防ぐため1にする
      if($totalConsumerWaitingAvgTimeDataCnt === 0) {
        $totalConsumerWaitingAvgTimeDataCnt = 1;
      }
      $eachOperatorResponseTime[$k]['display_name'] = $v['m_users']['display_name'];
      $eachOperatorResponseTime[$k]['responseAvgTimeNumber'] = array_merge($baseTimeData,$eachDayResponseAvgTimeNumber);
      $avgResponseTime = 0;
      if(!empty($v2)) {
        $avgResponseTime = array_sum($avgForcalculation)/$totalConsumerWaitingAvgTimeDataCnt;
      }
      //全体消費者待機時間
      $eachOperatorResponseTime[$k]['allResponseAvgTimeNumber'] = $this->changeTimeFormat($avgResponseTime);
    }
    return $eachOperatorResponseTime;
  }

  private function getAllEffectivenessRateOperatorInfo($date_format,$userId,$correctStartDate,$correctEndDate,$baseData,$users) {
    //チャット有効率
    $responseNumber = $this->getSummaryOperatorResponseInfo($date_format,$userId,$correctStartDate,$correctEndDate);
    $eachOperatorNumber = [];
    foreach($users as $k => $v){
      $eachDayResponseNumber = [];
      if(!empty($responseNumber)) {
        foreach($responseNumber as $k2 => $v2) {
          if($v['m_users']['id'] == $v2['thcl']['userId']) {
            $eachDayResponseNumber =  $eachDayResponseNumber + array($v2[0]['date'] => $this->isInValidDatetime($v2[0]['date']) ? self::LABEL_NONE : intval($v2[0]['response_count']));
          }
        }
        $eachOperatorNumber[$k]['responseNumber'] = array_merge($baseData,$eachDayResponseNumber);
        $eachOperatorNumber[$k]['allResponseNumber'] =  array_sum($eachDayResponseNumber);
      }
      else {
        $eachOperatorNumber[$k]['allResponseNumber'] =  0;
      }

      $effectivenessNumber = $this->getSummaryOperatorEffectivenessInfo($date_format,$userId,$correctStartDate,$correctEndDate);
      $eachOperatorEffectivenessNumber = [];
      $effectivenessNumberData = [];
      $effectivenessRate = [];
      if(!empty($effectivenessNumber)) {
        foreach($effectivenessNumber as $k2 => $v2) {
          if($v['m_users']['id'] == $v2['thcl']['userId']) {
            $effectivenessNumberData =  $effectivenessNumberData + array($v2[0]['date'] => $this->isInValidDatetime($v2[0]['date']) ? self::LABEL_NONE : intval($v2[0]['effectiveness_count']));
            if( $v2[0]['effectiveness_count'] != 0 && $eachOperatorNumber[$k]['responseNumber'][$v2[0]['date']] != 0){
              $effectivenessRate = $effectivenessRate + array($v2[0]['date'] => $this->isInValidDatetime($v2[0]['date']) ? self::LABEL_NONE : round($v2[0]['effectiveness_count']/$eachOperatorNumber[$k]['responseNumber'][$v2[0]['date']]*100));
            }
            else if($eachOperatorNumber[$k]['responseNumber'][$v2[0]['date']] === 0) {
              $effectivenessRate = $effectivenessRate + array($v2[0]['date'] => $this->isInValidDatetime($v2[0]['date']) ? self::LABEL_NONE : self::LABEL_INVALID);
            }
          }
        }
        $eachOperatorNumber[$k]['effectivenessNumber'] = array_merge($baseData,$effectivenessNumberData);
        $eachOperatorNumber[$k]['allEffectivenessNumber'] =  array_sum($effectivenessNumberData);
      }
      else {
        $eachOperatorNumber[$k]['allEffectivenessNumber'] = 0;
      }

      $effectivenessRate = array_merge($this->convertBaseDataForPercent($baseData),$effectivenessRate);
      foreach($responseNumber as $k2 => $v2) {
        if($v['m_users']['id'] == $v2['thcl']['userId']) {
          if(intval($v2) != 0 &&
            strcmp($effectivenessRate[$v2[0]['date']],self::LABEL_INVALID) === 0) {
            // 無効データと判定されたが応対件数が存在する場合は0%（自動返信なし）として返却
            $effectivenessRate[$v2[0]['date']] = 0;
          }
        }
      }

      $eachOperatorNumber[$k]['display_name'] = $v['m_users']['display_name'];
      $eachOperatorNumber[$k]['effectivenessRate'] = $effectivenessRate;

      //合計有効率
      $allEffectivenessRate = 0;
      if($eachOperatorNumber[$k]['allEffectivenessNumber'] != 0 and $eachOperatorNumber[$k]['allResponseNumber'] != 0) {
        $allEffectivenessRate = round($eachOperatorNumber[$k]['allEffectivenessNumber']/$eachOperatorNumber[$k]['allResponseNumber']*100);
      } else if($eachOperatorNumber[$k]['allEffectivenessNumber'] === 0 && $eachOperatorNumber[$k]['allResponseNumber'] != 0) {
        // 応対件数はあるけど有効件数がない場合
        $allEffectivenessRate = 0;
      } else {
        // 応対件数が0件の場合（無効データ）
        $allEffectivenessRate = self::LABEL_INVALID;
      }

      $eachOperatorNumber[$k]['allEffectivenessRate'] = $allEffectivenessRate;
    }
    return $eachOperatorNumber;
  }

  //月別の場合
  private function calculateMonthlyData($data){
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
      $baseData = $baseData + array(date('Y-m',$startDate) => $this->isInValidDatetime(date("Y-m-d",$startDate)) ? self::LABEL_NONE : 0);
      $baseTimeData = $baseTimeData + array(date('Y-m',$startDate) => $this->isInValidDatetime(date("Y-m-d",$startDate)) ? self::LABEL_NONE :'00:00:00');
      $startDate = strtotime("+1 month", $startDate);
    }
    $startDate = strtotime('first day of' .$start);

    $sqlData =$this->summarySql($date_format,$baseData,$baseTimeData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period);
    return $sqlData;
  }

  //日別の場合
  private function calculateDailyData($data){
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
      $baseData = $baseData + array(date("Y-m-d",$startDate) => $this->isInValidDatetime(date("Y-m-d",$startDate)) ? self::LABEL_NONE : 0);
      $baseTimeData = $baseTimeData + array(date("Y-m-d",$startDate) => $this->isInValidDatetime(date("Y-m-d",$startDate)) ? self::LABEL_NONE :"00:00:00");
      $startDate = strtotime("+1 day", $startDate);
    }
    $startDate = strtotime('first day of' .$data);

    $sqlData =$this->summarySql($date_format,$baseData,$baseTimeData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period);
    return $sqlData;
  }

  //時別の場合
  private function calculateHourlyData($data){
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
      $baseData = $baseData + array(date("H:00",$startDate) => $this->isInValidDatetime(date("Y-m-d",$startDate)) ? self::LABEL_NONE : 0);
      $baseTimeData = $baseTimeData + array(date("H:00",$startDate) => $this->isInValidDatetime(date("Y-m-d",$startDate)) ? self::LABEL_NONE : '00:00:00');
      $startDate = strtotime("+1 hour", $startDate);
    }
    $startDate = strtotime($data);

    $sqlData =$this->summarySql($date_format,$baseData,$baseTimeData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period);
    return $sqlData;

  }

  //各企業の日付けの範囲
  private function determineRange(){
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
      if(!$this->isInValidDatetime(date('Y-m-d', $companyStartDate))) {
        $companyRangeDate = $companyRangeDate + array(date('Y-m',$companyStartDate) => date('Y/m',$companyStartDate));
      }
      $companyStartDate = strtotime("+1 month", $companyStartDate);
    }
    $companyStartDate = strtotime($this->userInfo['MCompany']['created']);

    while($companyStartYear <= $endYear){
      if(!$this->isInValidYear(date('Y-01-01', $companyStartYear))) {
        $companyRangeYear = $companyRangeYear + array(date('Y',$companyStartYear) => date('Y',$companyStartYear));
      }
      $companyStartYear = strtotime("+1 year", $companyStartYear);
    }

    return ['companyRangeDate' => $companyRangeDate,'companyRangeYear' => $companyRangeYear];
  }

  private function summarySql($date_format,$baseData,$baseTimeData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period) {

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
    $this->log("BEGIN getAutoResponseData : ".$this->getDateWithMilliSec(),LOG_DEBUG);
    $automaticResponseData = $this->getAutomaticResponseData($date_format,$baseData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period,$requestDatas['requestNumberData'],$requestDatas['allRequestNumberData']);
    $this->log("END   getAutoResponseData : ".$this->getDateWithMilliSec(),LOG_DEBUG);
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

  private function getAccessData($date_format,$baseData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period) {
    if($this->isInValidDatetime($correctStartDate) && $this->isInValidDatetime($correctEndDate)) {
      return ['accessNumberData' => $this->convertBaseDataForNone($baseData),'allAccessNumberData' => self::LABEL_NONE];
    }
    $accessNumberData = [];

    //アクセス件数
    //月別
    if($period == 'month') {
      $access = "SELECT CONCAT(year, '-', month) as date,sum(access_count)
      FROM t_history_access_counts
      where m_companies_id = ? and year = ?
      group by year,month";
      $accessNumber = $this->THistory->query($access,
       array($this->userInfo['MCompany']['id'],date('Y',  $startDate)));
    }
    //日別
    if($period == 'day') {
      $access = "SELECT CONCAT(year, '-', month,'-',day) as date,sum(access_count)
      FROM t_history_access_counts
      where m_companies_id = ? and year = ? and month = ?
      group by year,month,day";
      $accessNumber = $this->THistory->query($access,
       array($this->userInfo['MCompany']['id'],date('Y',  $startDate),date('m',  $startDate)));
    }
    //時別
    if($period == 'hour') {
      $access = "SELECT CONCAT(hour, ':00') as date,sum(access_count)
      FROM t_history_access_counts
      where m_companies_id = ? and year = ? and month = ? and day = ?
      group by year,month,day,hour;";
      $accessNumber = $this->THistory->query($access,
       array($this->userInfo['MCompany']['id'],date('Y',  $startDate),date('m',  $startDate),date('d',  $startDate)));
    }

    foreach($accessNumber as $k => $v) {
      $accessNumberData =  $accessNumberData + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : intval($v[0]['sum(access_count)']));
    }
    //アクセス件数
    $accessNumberData = array_merge($baseData,$accessNumberData);

    //アクセス件数合計値
    $allAccessNumberData = array_sum($accessNumberData);

    return ['accessNumberData' => $accessNumberData,'allAccessNumberData' => $allAccessNumberData];
  }

  private function getWidgetData($date_format,$baseData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period) {
    if($this->isInValidDatetime($correctStartDate) && $this->isInValidDatetime($correctEndDate)) {
      return ['widgetNumberData' => $this->convertBaseDataForNone($baseData),'allWidgetNumberData' => self::LABEL_NONE];
    }
    $widgetNumberData =[];

    //ウィジェット表示件数
    //月別
    if($period == 'month') {
      $widget = "SELECT CONCAT(year, '-', month) as date,sum(widget_count)
      FROM t_history_widget_counts
      where m_companies_id = ? and year = ?
      group by year,month";
      $widgetNumber = $this->THistory->query($widget,
       array($this->userInfo['MCompany']['id'],date('Y',  $startDate)));
    }
    //日別
    if($period == 'day') {
      $widget = "SELECT CONCAT(year, '-', month,'-',day) as date,sum(widget_count)
      FROM t_history_widget_counts
      where m_companies_id = ? and year = ? and month = ?
      group by year,month,day";
      $widgetNumber = $this->THistory->query($widget,
       array($this->userInfo['MCompany']['id'],date('Y',  $startDate),date('m',  $startDate)));
    }
    //時別
    if($period == 'hour') {
      $widget = "SELECT CONCAT(hour, ':00') as date,sum(widget_count)
      FROM t_history_widget_counts
      where m_companies_id = ? and year = ? and month = ? and day = ?
      group by year,month,day,hour;";
      $widgetNumber = $this->THistory->query($widget,
       array($this->userInfo['MCompany']['id'],date('Y',  $startDate),date('m',  $startDate),date('d',  $startDate)));
    }

    foreach($widgetNumber as $k => $v) {
      $widgetNumberData =  $widgetNumberData + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : intval($v[0]['sum(widget_count)']));
    }

    //ウィジェット件数
    $widgetNumberData = array_merge($baseData,$widgetNumberData);

    //ウィジェット件数合計値
    $allWidgetNumberData = array_sum($widgetNumberData);

    return['widgetNumberData' => $widgetNumberData,'allWidgetNumberData' => $allWidgetNumberData];
  }

  private function getRequestData($date_format,$baseData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period) {
    if($this->isInValidDatetime($correctStartDate) && $this->isInValidDatetime($correctEndDate)) {
      return ['requestNumberData' => $this->convertBaseDataForNone($baseData),'allRequestNumberData' => self::LABEL_NONE];
    }
    $requestNumberData = [];

    //チャットリクエスト件数
    $requestNumber = "SELECT
      date_format(th.access_date, ?) as date,
        count(th.id) as request_count
    FROM (select t_histories_id,m_companies_id,message_request_flg from t_history_chat_logs force index(idx_t_history_chat_logs_request_flg_companies_id)
      where message_request_flg = ? and m_companies_id = ?)as thcl,t_histories as th
    WHERE
      
      thcl.t_histories_id = th.id
    
    AND
      th.access_date between ? and ?
    ";

    $requestNumber = $this->exclusionIpAddress($requestNumber,'th');

    $requestNumber .= ' group by date';

    $requestNumber = $this->THistory->query($requestNumber, array($date_format,$this->chatMessageType['requestFlg']['effectiveness'],$this->userInfo['MCompany']['id'],
      $correctStartDate,$correctEndDate));

    foreach($requestNumber as $k => $v) {
      $requestNumberData =  $requestNumberData + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : intval($v[0]['request_count']));
    }

    //チャットリクエスト件数
    $requestNumberData = array_merge($baseData,$requestNumberData);

    //チャットリクエスト件数合計値
    $allRequestNumberData = array_sum($requestNumberData);

    return['requestNumberData' => $requestNumberData,'allRequestNumberData' => $allRequestNumberData];

  }

  private function getResponseData($date_format,$baseData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period,$requestNumberData,$allRequestNumberData) {
    if($this->isInValidDatetime($correctStartDate) && $this->isInValidDatetime($correctEndDate)) {
      $noneBaseData = $this->convertBaseDataForNone($baseData);
      return [
          'responseRate' => $noneBaseData,
          'responseNumberData' => $noneBaseData,
          'allResponseNumberData' => self::LABEL_NONE,
          'allResponseRate' => self::LABEL_NONE
      ];
    }
    $responseNumberData = [];
    $responseRate = [];

    //応対件数
    $response = "SELECT date_format(th.access_date, ?) as date,
      count(thcl.t_histories_id) as response_count
      FROM (select t_histories_id,m_companies_id, message_type, message_distinction
      from t_history_chat_logs force index(idx_t_history_chat_logs_message_type_companies_id)
      where message_type = ? and m_companies_id = ? group by t_histories_id,
       message_distinction) as thcl
      LEFT JOIN (select t_histories_id, message_request_flg,
      message_distinction from t_history_chat_logs force index(idx_t_history_chat_logs_request_flg_companies_id)
       where message_request_flg = ? and m_companies_id = ?) as thcl2
      ON
      thcl.t_histories_id = thcl2.t_histories_id
      AND
      thcl.message_distinction = thcl2.message_distinction,
      t_histories as th
      WHERE
        th.access_date between ? and ?
      AND
        thcl.t_histories_id = th.id
      AND
        th.id = thcl2.t_histories_id
      group by date";

    $responseNumber = $this->THistory->query($response,
      array($date_format,$this->chatMessageType['messageType']['enteringRoom'],$this->userInfo['MCompany']['id'],
        $this->chatMessageType['requestFlg']['effectiveness'],$this->userInfo['MCompany']['id'],
        $correctStartDate,$correctEndDate,));

    foreach($responseNumber as $k => $v) {
      if($v[0]['response_count'] != 0 and $requestNumberData[$v[0]['date']] != 0) {

        $responseRate = $responseRate + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : round($v[0]['response_count']/$requestNumberData[$v[0]['date']]*100));
      } else if ($requestNumberData[$v[0]['date']] === 0) {
        $responseRate = $responseRate + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : self::LABEL_INVALID);
      }
      $responseNumberData = $responseNumberData + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : intval($v[0]['response_count']));
    }

    //チャット応答率
    $responseRate = array_merge($this->convertBaseDataForPercent($baseData),$responseRate);

    foreach($requestNumberData as $k2 => $v2) {
      if(intval($v2) !== 0 && strcmp($responseRate[$k2],self::LABEL_INVALID) === 0) {
        // 無効データと判定されたがリクエストチャット件数が存在する場合は0%（応対なし）として返却
        $responseRate[$k2] = 0;
      }
    }

    //チャット応答件数
    $responseNumberData = array_merge($baseData,$responseNumberData);

    //応対件数合計値
    $allResponseNumberData = array_sum($responseNumberData);

    //合計チャット応答率
    $allResponseRate = 0;
    if($allResponseNumberData != 0 and $allRequestNumberData != 0) {
      $allResponseRate = round($allResponseNumberData/$allRequestNumberData*100);
    } else if($allResponseNumberData === 0 && $allRequestNumberData != 0) {
      // リクエストチャット件数はあるけど応答がない場合
      $allResponseRate = 0;
    } else {
      // リクエストチャットが0件の場合（無効データ）
      $allResponseRate = self::LABEL_INVALID;
    }

    return ['responseRate' => $responseRate,'responseNumberData' => $responseNumberData,'allResponseNumberData' => $allResponseNumberData,'allResponseRate' => $allResponseRate];
  }


  private function getAutomaticResponseData($date_format,$baseData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period,$requestNumberData,$allRequestNumberData) {
    if($this->isInValidDatetime($correctStartDate) && $this->isInValidDatetime($correctEndDate)) {
      $noneBaseData = $this->convertBaseDataForNone($baseData);
      return [
          'automaticResponseNumberData' => $noneBaseData,
          'automaticResponseRate' => $noneBaseData,
          'allAutomaticResponseNumberData' => self::LABEL_NONE,
          'allAutomaticResponseRate' => self::LABEL_NONE
      ];
    }
    $automaticResponseNumberData = [];
    $automaticResponseRate = [];

    //自動返信応対件数
    $automaticResponse = "SELECT date_format(th.access_date,?) as date,
    count(distinct thcl.message_distinction,thcl.t_histories_id) as automaticResponse_count
    FROM
      (select id,t_histories_id,message_distinction,message_type from t_history_chat_logs
       force index(idx_t_history_chat_logs_message_type_companies_id) where message_type = ?
      and m_companies_id = ?) as thcl
    LEFT JOIN
      (select id,t_histories_id,message_distinction,message_type from t_history_chat_logs
      force index(idx_t_history_chat_logs_message_type_companies_id) where message_type = ? and m_companies_id = ?) as thcl2
    ON
      thcl.t_histories_id = thcl2.t_histories_id
    AND
      thcl.message_distinction = thcl2.message_distinction,
    t_histories as th
    WHERE
      thcl2.t_histories_id IS NULL
    AND
      th.id = thcl.t_histories_id
    AND
      th.access_date between ? and ?
    group by date";

    $automaticResponseNumber = $this->THistory->query($automaticResponse, array($date_format,
    $this->chatMessageType['messageType']['automatic'],$this->userInfo['MCompany']['id'],
    $this->chatMessageType['messageType']['enteringRoom'],$this->userInfo['MCompany']['id'],
    $correctStartDate,$correctEndDate));
    foreach($automaticResponseNumber as $k => $v) {
      $automaticResponseNumberData =  $automaticResponseNumberData + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : intval($v[0]['automaticResponse_count']));
      if($v[0]['automaticResponse_count'] != 0 and $requestNumberData[$v[0]['date']] != 0) {

        $automaticResponseRate = $automaticResponseRate + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : round($v[0]['automaticResponse_count']/$requestNumberData[$v[0]['date']]*100));
      } else if ($requestNumberData[$v[0]['date']] === 0) {
        $automaticResponseRate = $automaticResponseRate + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : self::LABEL_INVALID);
      }
    }

    //自動返信応対件数
    $automaticResponseNumberData = array_merge($baseData,$automaticResponseNumberData);

    //自動返信応答率
    $automaticResponseRate = array_merge($this->convertBaseDataForPercent($baseData),$automaticResponseRate);

    foreach($requestNumberData as $k2 => $v2) {
      if(intval($v2) !== 0 && strcmp($automaticResponseRate[$k2],self::LABEL_INVALID) === 0) {
        // 無効データと判定されたがリクエストチャット件数が存在する場合は0%（自動返信なし）として返却
        $automaticResponseRate[$k2] = 0;
      }
    }

    //自動返信応対件数合計値
    $allAutomaticResponseNumberData = array_sum($automaticResponseNumberData);

    //合計チャット応答率
    $allAutomaticResponseRate = 0;
    if($allAutomaticResponseNumberData != 0 and $allRequestNumberData != 0) {
      $allAutomaticResponseRate = round($allAutomaticResponseNumberData/$allRequestNumberData*100);
    } else if($allAutomaticResponseNumberData === 0 && $allRequestNumberData != 0) {
      // リクエストチャット件数はあるけど自動返信がない場合
      $allAutomaticResponseRate = 0;
    } else {
      // リクエストチャットが0件の場合（無効データ）
      $allAutomaticResponseRate = self::LABEL_INVALID;
    }

    return ['automaticResponseNumberData' => $automaticResponseNumberData,'automaticResponseRate' => $automaticResponseRate,
    'allAutomaticResponseNumberData' => $allAutomaticResponseNumberData,'allAutomaticResponseRate' => $allAutomaticResponseRate];
  }

  private function getCoherentData($date_format,$baseData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period,$requestNumberData,$allRequestNumberData) {
    if($this->isInValidDatetime($correctStartDate) && $this->isInValidDatetime($correctEndDate)) {
      $noneBaseData = $this->convertBaseDataForNone($baseData);
      return [
          'effectivenessNumberData' => $noneBaseData,
          'denialNumberData' => $noneBaseData,
          'effectivenessRate' => $noneBaseData,
          'allEffectivenessNumberData' => self::LABEL_NONE,
          'allDenialNumberData' => self::LABEL_NONE,
          'allEffectivenessRate' => self::LABEL_NONE
      ];
    }
    $effectivenessNumberData = [];
    $denialNumberData = [];
    $effectivenessRate = [];

    //チャット有効件数
    $effectiveness = "SELECT date_format(th.access_date, ?) as date,SUM(case when thcl.achievement_flg = ? THEN 1 ELSE 0 END) effectiveness
    FROM (select t_histories_id, m_companies_id,achievement_flg from t_history_chat_logs
     force index(idx_t_history_chat_logs_achievement_flg_companies_id) where achievement_flg = ? and m_companies_id = ?) as thcl,
     t_histories as th
    WHERE
      thcl.t_histories_id = th.id
    AND
      th.access_date between ? and ?
    group by date";

    $effectiveness = $this->THistory->query($effectiveness, array($date_format,$this->chatMessageType['achievementFlg']['effectiveness'],
      $this->chatMessageType['achievementFlg']['effectiveness'],$this->userInfo['MCompany']['id'],
      $correctStartDate,$correctEndDate));

    $denial = "SELECT date_format(th.access_date,?) as date,SUM(case when thcl.message_type = ? THEN 1 ELSE 0 END) denial
      FROM (select t_histories_id, m_companies_id,message_type from t_history_chat_logs
       force index(idx_t_history_chat_logs_message_type_companies_id) where message_type = ? and m_companies_id = ?) as thcl,
       t_histories as th
      WHERE
        thcl.t_histories_id = th.id
      AND
        th.access_date between ? and ?
      group by date";

    $denial = $this->THistory->query($denial, array($date_format,$this->chatMessageType['messageType']['denial'],
      $this->chatMessageType['messageType']['denial'],$this->userInfo['MCompany']['id'],
      $correctStartDate,$correctEndDate));

    if(!empty($effectiveness)) {
      foreach($effectiveness as $k => $v) {
        $effectivenessNumberData =  $effectivenessNumberData + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : intval($v[0]['effectiveness']));
        if( $v[0]['effectiveness'] != 0 and $requestNumberData[$v[0]['date']] != 0){
          $effectivenessRate = $effectivenessRate + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : round($v[0]['effectiveness']/$requestNumberData[$v[0]['date']]*100));
        } else if($requestNumberData[$v[0]['date']] === 0) {
          $effectivenessRate = $effectivenessRate + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : self::LABEL_INVALID);
        }
      }
    }

    if(!empty($denial)) {
      foreach($denial as $k => $v) {
        $denialNumberData =  $denialNumberData + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : intval($v[0]['denial']));
      }
    }
    //チャット有効件数
    $effectivenessNumberData = array_merge($baseData,$effectivenessNumberData);

    //チャット拒否件数
    $denialNumberData = array_merge($baseData,$denialNumberData);

    //チャット有効率
    $effectivenessRate = array_merge($this->convertBaseDataForPercent($baseData),$effectivenessRate);

    foreach($requestNumberData as $k2 => $v2) {
      if(intval($v2) !== 0 && strcmp($effectivenessRate[$k2],self::LABEL_INVALID) === 0) {
        // 無効データと判定されたがリクエストチャット件数が存在する場合は0%（自動返信なし）として返却
        $effectivenessRate[$k2] = 0;
      }
    }

    //有効件数合計値
    $allEffectivenessNumberData = array_sum($effectivenessNumberData);

    //拒否件数合計値
    $allDenialNumberData = array_sum($denialNumberData);

    //合計有効率
    $allEffectivenessRate = 0;
    if($allEffectivenessNumberData != 0 and $allRequestNumberData != 0) {
      $allEffectivenessRate = round($allEffectivenessNumberData/$allRequestNumberData*100);
    } else if($allEffectivenessNumberData === 0 && $allRequestNumberData != 0) {
      // リクエストチャット件数はあるけど自動返信がない場合
      $allEffectivenessRate = 0;
    } else {
      // リクエストチャットが0件の場合（無効データ）
      $allEffectivenessRate = self::LABEL_INVALID;
    }

    return ['effectivenessNumberData' => $effectivenessNumberData,'denialNumberData' => $denialNumberData,'effectivenessRate' => $effectivenessRate,
    'allEffectivenessNumberData' => $allEffectivenessNumberData,'allDenialNumberData' => $allDenialNumberData,'allEffectivenessRate' => $allEffectivenessRate];

  }

  private function getAvgRequestTimeData($date_format,$baseData,$baseTimeData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period) {
    if($this->isInValidDatetime($correctStartDate) && $this->isInValidDatetime($correctEndDate)) {
      $noneBaseData = $this->convertBaseDataForNone($baseData);
      return [
          'requestAvgTimeData' => $noneBaseData,
          'allRequestAvgTimeData' => self::LABEL_NONE
      ];
    }
    $requestAvgTime = [];
    $avgForcalculation = [];
    $effectivenessRate = [];

    //
    $requestTime = "SELECT date_format(th.access_date,?) as date,AVG(UNIX_TIMESTAMP(thcl.created)
      - UNIX_TIMESTAMP(th.access_date)) as average
    FROM (select t_histories_id, message_request_flg, message_distinction,created
    from t_history_chat_logs force index(idx_t_history_chat_logs_request_flg_companies_id) where message_request_flg = ? and
     m_companies_id = ? group by t_histories_id) as thcl,
    t_histories as th
    WHERE
      thcl.t_histories_id = th.id
    AND
      th.access_date between ? and ?
    group by date";

    $requestTime = $this->THistory->query($requestTime, array($date_format,$this->chatMessageType['requestFlg']['effectiveness'],
      $this->userInfo['MCompany']['id'],$correctStartDate,$correctEndDate));

    $totalRequestAvgTimeDataCnt = 0;
    foreach($requestTime as $k => $v) {
      $timeFormat = $this->changeTimeFormat(round($v[0]['average']));
      $requestAvgTime =  $requestAvgTime + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : $timeFormat);
      $avgForcalculation = $avgForcalculation + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : round($v[0]['average']));
      if (!$this->isInValidDatetime($v[0]['date'])) {
        $totalRequestAvgTimeDataCnt++;
      }
    }
    // 0件だったらゼロ割を防ぐため1にする
    if($totalRequestAvgTimeDataCnt === 0) {
      $totalRequestAvgTimeDataCnt = 1;
    }
    //チャットリクエスト平均時間
    $requestAvgTimeData = array_merge($baseTimeData,$requestAvgTime);

    //全チャットリクエスト平均時間
    $allRequestAvgTimeData = 0;
    if(!empty($v)) {
      $allRequestAvgTimeData = array_sum($avgForcalculation)/$totalRequestAvgTimeDataCnt;
    }
    $allRequestAvgTimeData = $this->changeTimeFormat($allRequestAvgTimeData);

    return['requestAvgTimeData' => $requestAvgTimeData, 'allRequestAvgTimeData' => $allRequestAvgTimeData];

  }

  private function getConsumerWatingAvgTimeData($date_format,$baseData,$baseTimeData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period) {
    if($this->isInValidDatetime($correctStartDate) && $this->isInValidDatetime($correctEndDate)) {
      $noneBaseData = $this->convertBaseDataForNone($baseData);
      return [
          'consumerWatingAvgTimeData' => $noneBaseData,
          'allConsumerWatingAvgTimeData' => self::LABEL_NONE
      ];
    }
    $consumerWatingAvgTime = [];
    $avgForcalculation = [];

    //平均消費者待機時間
    $consumerWatingTime = "SELECT date_format(th.access_date,?) as date,AVG(UNIX_TIMESTAMP(thcl2.created)
      - UNIX_TIMESTAMP(thcl.created)) as average
    FROM (select t_histories_id, message_request_flg,created,message_distinction
    from t_history_chat_logs force index(idx_t_history_chat_logs_request_flg_companies_id)
    where message_request_flg = ? and m_companies_id = ? group by t_histories_id) as thcl,
    (select t_histories_id, message_type,created,message_distinction
    from t_history_chat_logs force index(idx_t_history_chat_logs_message_type_companies_id)
    where message_type = ? and m_companies_id = ? group by t_histories_id) as thcl2,
    t_histories as th
    WHERE
      thcl.t_histories_id = th.id
    AND
      th.id = thcl2.t_histories_id
    AND
      thcl.t_histories_id = thcl2.t_histories_id
    AND
      thcl.message_distinction = thcl2.message_distinction
    AND
      th.access_date between ? and ?
    group by date";

    $consumerWatingTime = $this->THistory->query($consumerWatingTime, array($date_format,$this->chatMessageType['requestFlg']['effectiveness'],
      $this->userInfo['MCompany']['id'],$this->chatMessageType['messageType']['enteringRoom'],$this->userInfo['MCompany']['id'],
      $correctStartDate,$correctEndDate));

    $totalConsumerWaitingAvgTimeDataCnt = 0;
    foreach($consumerWatingTime as $k => $v) {
      $timeFormat = $this->changeTimeFormat(round($v[0]['average']));
      $consumerWatingAvgTime =  $consumerWatingAvgTime + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : $timeFormat);
      $avgForcalculation = $avgForcalculation + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : round($v[0]['average']));
      if(!$this->isInValidDatetime($v[0]['date'])) {
        $totalConsumerWaitingAvgTimeDataCnt++;
      }
    }

    // 0件だったらゼロ割を防ぐため1にする
    if($totalConsumerWaitingAvgTimeDataCnt === 0) {
      $totalConsumerWaitingAvgTimeDataCnt = 1;
    }

    //消費者待機平均時間
    $consumerWatingAvgTimeData = array_merge($baseTimeData,$consumerWatingAvgTime);

    //全消費者待機平均時間
    $allConsumerWatingAvgTimeData = 0;
    if(!empty($v)) {
      $allConsumerWatingAvgTimeData = array_sum($avgForcalculation)/$totalConsumerWaitingAvgTimeDataCnt;
    }
    $allConsumerWatingAvgTimeData = $this->changeTimeFormat($allConsumerWatingAvgTimeData);

    return ['consumerWatingAvgTimeData' => $consumerWatingAvgTimeData,'allConsumerWatingAvgTimeData' => $allConsumerWatingAvgTimeData];

  }

  private function getResponseAvgTimeData($date_format,$baseData,$baseTimeData,$startDate,$endDate,$correctStartDate,$correctEndDate,$period) {
    if($this->isInValidDatetime($correctStartDate) && $this->isInValidDatetime($correctEndDate)) {
      $noneBaseData = $this->convertBaseDataForNone($baseData);
      return [
          'responseAvgTimeData' => $noneBaseData,
          'allResponseAvgTimeData' => self::LABEL_NONE
      ];
    }
    $responseAvgTime = [];
    $avgForcalculation = [];

    //平均応答時間
    $responseTime = "SELECT date_format(th.access_date,?) as date,AVG(UNIX_TIMESTAMP(thcl2.created)
      - UNIX_TIMESTAMP(thcl.created)) as average
    FROM (select t_histories_id, message_request_flg,created,message_distinction
    from t_history_chat_logs force index(idx_t_history_chat_logs_request_flg_companies_id)
    where message_request_flg = ? and m_companies_id = ? group by t_histories_id) as thcl,
    (select t_histories_id, message_type,created,message_distinction
    from t_history_chat_logs force index(idx_t_history_chat_logs_message_type_companies_id)
    where message_type = ? and m_companies_id = ? group by t_histories_id) as thcl2,
    t_histories as th
    WHERE
      thcl.t_histories_id = thcl2.t_histories_id
    AND
      thcl.message_distinction = thcl2.message_distinction
    AND
      th.access_date between ? and ?
    AND
      thcl.t_histories_id = th.id
    AND
      th.id = thcl2.t_histories_id
    group by date";

    $responseTime = $this->THistory->query($responseTime, array($date_format,$this->chatMessageType['requestFlg']['effectiveness'],
      $this->userInfo['MCompany']['id'],$this->chatMessageType['messageType']['operatorMessage'],
      $this->userInfo['MCompany']['id'],$correctStartDate,$correctEndDate));

    $totalResponseAvgTimeDataCnt = 0;
    foreach($responseTime as $k => $v) {
      $timeFormat = $this->changeTimeFormat(round($v[0]['average']));
      $responseAvgTime =  $responseAvgTime + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : $timeFormat);
      $avgForcalculation = $avgForcalculation + array($v[0]['date'] => $this->isInValidDatetime($v[0]['date']) ? self::LABEL_NONE : round($v[0]['average']));
      if(!$this->isInValidDatetime($v[0]['date'])) {
        $totalResponseAvgTimeDataCnt++;
      }
    }

    // 0件だったらゼロ割を防ぐため1にする
    if($totalResponseAvgTimeDataCnt === 0) {
      $totalResponseAvgTimeDataCnt = 1;
    }

    //平均応答時間
    $responseAvgTimeData = array_merge($baseTimeData,$responseAvgTime);

    //全応答平均時間
    $allResponseAvgTimeData = 0;
    if(!empty($v)) {
      $allResponseAvgTimeData = array_sum($avgForcalculation)/$totalResponseAvgTimeDataCnt;
    }
    $allResponseAvgTimeData = $this->changeTimeFormat($allResponseAvgTimeData);

    return ['responseAvgTimeData' => $responseAvgTimeData,'allResponseAvgTimeData' => $allResponseAvgTimeData];
  }

  private function changeTimeFormat($seconds) {

    $hours = round($seconds / 3600);
    $minutes = round(($seconds / 60) % 60);
    $seconds = $seconds % 60;

    $timeFormat = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);

    return $timeFormat;
  }

  private function insertCsvData($csvData) {
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

  private function insertEachItemCsvData($csvData,$accessNumber,$widgetNumber,$requestNumber,
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
      $percentMark = '';
      if(is_numeric($v2)) {
        $percentMark = '%';
      }
      $responseRate[] = $v2.$percentMark;
    }
    $percentMark = '';
    if(is_numeric($csvData['responseDatas']['allResponseRate'])) {
      $percentMark = '%';
    }
    $responseRate[] = $csvData['responseDatas']['allResponseRate'].$percentMark;

    foreach($csvData['automaticResponseData']['automaticResponseRate'] as $key => $v2) {
      $percentMark = '';
      if(is_numeric($v2)) {
        $percentMark = '%';
      }
      $automaticResponseRate[] = $v2.$percentMark;
    }
    $percentMark = '';
    if(is_numeric($csvData['automaticResponseData']['allAutomaticResponseRate'])) {
      $percentMark = '%';
    }
    $automaticResponseRate[] = $csvData['automaticResponseData']['allAutomaticResponseRate'].$percentMark;

    foreach($csvData['coherentDatas']['effectivenessRate'] as $key => $v3) {
      $percentMark = '';
      if(is_numeric($v3)) {
        $percentMark = '%';
      }
      $effectivenessRate[] = $v3.$percentMark;
    }
    $percentMark = '';
    if(is_numeric($csvData['coherentDatas']['allEffectivenessRate'])) {
      $percentMark = '%';
    }
    $effectivenessRate[] = $csvData['coherentDatas']['allEffectivenessRate'].$percentMark;

    return ['accessNumber' => $accessNumber,'widgetNumber' => $widgetNumber,'requestNumber' => $requestNumber,
      'responseNumber' => $responseNumber,'automaticResponseNumber' => $automaticResponseNumber, 'noNumber' =>$noNumber,
      'effectivenessNumber' => $effectivenessNumber,'requestAvgTime' =>$requestAvgTime,'consumerWatingAvgTime' => $consumerWatingAvgTime,
      'responseAvgTime' => $responseAvgTime,'responseRate' => $responseRate,'automaticResponseRate' => $automaticResponseRate,
      'effectivenessRate' => $effectivenessRate];
  }

  private function insertOperatorCsvData($csvData,$timeData) {
    $itemName = [];
    if($timeData == 'yearly') {
      $itemName[] = 'オペレータ/月別';
    }
    if($timeData == 'monthly') {
      $itemName[] = 'オペレータ/日別';
    }
    if($timeData == 'daily') {
      $itemName[] = 'オペレータ/時別';
    }
    $itemName[] = 'ログイン件数';
    $itemName[] = 'チャットリクエスト件数';
    $itemName[] = 'チャット応対件数';
    $itemName[] = 'チャット有効件数';
    $itemName[] = '平均消費者待機時間';
    $itemName[] = '平均応答時間';
    $itemName[] = 'チャット有効率';

    $csv[] = $itemName;
    foreach($csvData as $key => $v) {
      $operatorInfo = [];
      $operatorInfo[] = $v['m_users']['display_name'];
      if(!empty($v['loginNumber'])) {
        $operatorInfo[] = $v['loginNumber'];
      }
      else {
        $operatorInfo[] = 0;
      }
      if(!empty($v['requestNumber'])) {
        $operatorInfo[] = $v['requestNumber'];
      }
      else {
        $operatorInfo[] = 0;
      }
      if(!empty($v['responseNumber'])) {
        $operatorInfo[] = $v['responseNumber'];
      }
      else {
        $operatorInfo[] = 0;
      }
      if(!empty($v['effectivenessNumber'])) {
        $operatorInfo[] = $v['effectivenessNumber'];
      }
      else {
        $operatorInfo[] = 0;
      }
      if(!empty($v['avgEnteringRommTime'])) {
        $operatorInfo[] = $v['avgEnteringRommTime'];
      }
      else {
        $operatorInfo[] = '00:00:00';
      }
      if(!empty($v['responseTime'])) {
        $operatorInfo[] = $v['responseTime'];
      }
      else {
        $operatorInfo[] = '00:00:00';
      }
      if(empty($v['effectivenessRate'])) {
        $operatorInfo[] = '0%';
      }
      else {
        if(is_numeric($v['effectivenessRate'])) {
          $checkData = ' %';
        }
        else {
          $checkData = '';
        }
        $operatorInfo[] = $v['effectivenessRate'].$checkData;
      }
      $csv[] = $operatorInfo;
    }
     return $csv;
  }

  private function insertEachItemOperatorCsvData($csvData,$dateFormat,$date,$item) {
    //各項目オペレータ情報取得
    $itemInfo = [];
    foreach($csvData['users']as $k => $v) {
      $itemInfo[] = array($v['display_name']);
      if($dateFormat == 'daily'){
        $start = 0;
        $end = 23;
        $days ='';
        $seconds = ':00';
      }
      if($dateFormat == 'monthly') {
        $start = 1;
        $end = date("d",strtotime('last day of' .$date));
        $days = $date.'-';
        $seconds = '';
      }
      if($dateFormat == 'yearly') {
        $start = 1;
        $end = 12;
        $days = $date.'-';
        $seconds = '';
      }
      if($item == 'login'){
        for ($i = $start; $i <= $end; $i++) {
          array_push($itemInfo[$k], $v['loginNumber'][$days.sprintf("%02d",$i).$seconds]);
        }
        array_push($itemInfo[$k], $v['allLoginNumber']);
      }
      if($item == 'requestChat') {
        for ($i = $start; $i <= $end; $i++) {
          array_push($itemInfo[$k], $v['requestNumber'][$days.sprintf("%02d",$i).$seconds]);
        }
        array_push($itemInfo[$k], $v['allRequestNumber']);
      }
      if($item == 'responseChat') {
        for ($i = $start; $i <= $end; $i++) {
          array_push($itemInfo[$k], $v['responseNumber'][$days.sprintf("%02d",$i).$seconds]);
        }
        array_push($itemInfo[$k], $v['allResponseNumber']);
      }
      if($item == 'effectiveness') {
        for ($i = $start; $i <= $end; $i++) {
          array_push($itemInfo[$k], $v['effectivenessNumber'][$days.sprintf("%02d",$i).$seconds]);
        }
        array_push($itemInfo[$k], $v['allEffectivenessNumber']);
      }
      if($item == 'avgConsumersWaitTime') {
        for ($i = $start; $i <= $end; $i++) {
          array_push($itemInfo[$k], $v['avgEnteringRommTimeNumber'][$days.sprintf("%02d",$i).$seconds]);
        }
        array_push($itemInfo[$k], $v['allAvgEnteringRommTimeData']);
      }
      if($item == 'avgResponseTime') {
        for ($i = $start; $i <= $end; $i++) {
          array_push($itemInfo[$k], $v['responseAvgTimeNumber'][$days.sprintf("%02d",$i).$seconds]);
        }
        array_push($itemInfo[$k], $v['allResponseAvgTimeNumber']);
      }
      if($item == 'effectivenessRate') {
        for ($i = $start; $i <= $end; $i++) {
          if(is_numeric($v['effectivenessRate'][$days.sprintf("%02d",$i).$seconds])) {
            $checkData = ' %';
          }
          else {
            $checkData = '';
          }
          array_push($itemInfo[$k], $v['effectivenessRate'][$days.sprintf("%02d",$i).$seconds].$checkData);
        }
        if(is_numeric($v['allEffectivenessRate'])) {
          $checkData = ' %';
        }
        else {
          $checkData = '';
        }
        array_push($itemInfo[$k], $v['allEffectivenessRate'].$checkData);
      }
    }
     return $itemInfo;
  }

  private function getDateTimeInfo($dateType,$date,$item) {
    if($dateType == '月別') {
      $start = $date.'-01';
      $end = $date.'-12';
      $startDate = strtotime('first day of' .$start);
      $endDate = strtotime('last day of' .$end);
      $yearData = [];
      $yearData[] = '統計項目/月別';
      while($startDate <= $endDate) {
        $yearData[] = date('Y-m',$startDate);
        $startDate = strtotime("+1 month", $startDate);
      }
      if($item == 'login' || $item == 'requestChat' ||$item == 'responseChat' || $item =='effectiveness') {
        $yearData[] = '合計';
      }
      else if($item == 'avgConsumersWaitTime' || $item =='avgResponseTime' || $item =='effectivenessRate') {
        $yearData[] = '平均';
      }
      else {
        $yearData[] = '合計・平均';
      }
      return $yearData;
    }

    if($dateType == '日別') {
      $firstDate = strtotime('first day of ' .$date);
      $lastDate = strtotime('last day of ' .$date);
      $monthData = [];
      $monthData[] = '統計項目/日別';
      while($firstDate <= $lastDate) {
        $monthData[] = ltrim(date('d',$firstDate), "0").'日';
        $firstDate = strtotime("+1 day", $firstDate);
      }
      if($item == 'login' || $item == 'requestChat' ||$item == 'responseChat' || $item =='effectiveness') {
        $monthData[] = '合計';
      }
      else if($item == 'avgConsumersWaitTime' || $item =='avgResponseTime' || $item =='effectivenessRate') {
        $monthData[] = '平均';
      }
      else {
        $monthData[] = '合計・平均';
      }
      return $monthData;
    }

    if($dateType == '時別') {
      $startTime = strtotime($date);
      $endTime = strtotime("+1 day",$startTime);
      $dayData = [];
      $dayData[] = '統計項目/時別';
      while($startTime < $endTime) {
        $dayData[] = date('H:i',$startTime).'-'.date('H:i',strtotime("+1 hour", $startTime));
        $startTime = strtotime("+1 hour", $startTime);
      }
      if($item == 'login' || $item == 'requestChat' ||$item == 'responseChat' || $item =='effectiveness') {
        $dayData[] = '合計';
      }
      else if($item == 'avgConsumersWaitTime' || $item =='avgResponseTime' || $item =='effectivenessRate') {
        $dayData[] = '平均';
      }
      else {
        $dayData[] = '合計・平均';
      }
      return $dayData;
    }
  }

  private function insertPrivateOperatorCsvData($csvData,$dateFormat,$date,$userId) {
    //オペレータ1人の情報取得
    $userInfo = [];
    if($dateFormat == 'daily'){
      $start = 0;
      $end = 23;
      $days ='';
      $seconds = ':00';
    }
    if($dateFormat == 'monthly') {
      $start = 1;
      $end = date("d",strtotime('last day of' .$date));
      $days = $date.'-';
      $seconds = '';
    }
    if($dateFormat == 'yearly') {
      $start = 1;
      $end = 12;
      $days = $date.'-';
      $seconds = '';
    }

    $userInfo[] = array('ログイン件数');
    for ($i = $start; $i <= $end; $i++) {
      array_push($userInfo[0], $csvData['loginNumberData'][$days.sprintf("%02d",$i).$seconds]);
    }
    array_push($userInfo[0], $csvData['allLoginNumberData']);
    $userInfo[1] = array('チャットリクエスト件数');
    for ($i = $start; $i <= $end; $i++) {
      array_push($userInfo[1], $csvData['requestNumberData'][$days.sprintf("%02d",$i).$seconds]);
    }
    array_push($userInfo[1], $csvData['allRequestNumberData']);
    $userInfo[2] = array('チャット応対件数');
    for ($i = $start; $i <= $end; $i++) {
      array_push($userInfo[2], $csvData['responseNumberData'][$days.sprintf("%02d",$i).$seconds]);
    }
    array_push($userInfo[2], $csvData['allResponseNumberData']);
    $userInfo[3] = array('チャット有効件数');
    for ($i = $start; $i <= $end; $i++) {
      array_push($userInfo[3], $csvData['effectivenessNumberData'][$days.sprintf("%02d",$i).$seconds]);
    }
    array_push($userInfo[3], $csvData['allEffectivenessNumberData']);
    $userInfo[4] = array('平均消費者待機時間');
    for ($i = $start; $i <= $end; $i++) {
      array_push($userInfo[4], $csvData['avgEnteringRommTimeData'][$days.sprintf("%02d",$i).$seconds]);
    }
    array_push($userInfo[4], $csvData['allAvgEnteringRommTimeData']);
    $userInfo[5] = array('平均応答時間');
    for ($i = $start; $i <= $end; $i++) {
      array_push($userInfo[5], $csvData['responseAvgTimeData'][$days.sprintf("%02d",$i).$seconds]);
    }
    array_push($userInfo[5], $csvData['allResponseAvgTimeData']);
    $userInfo[6] = array('チャット有効率');
    for ($i = $start; $i <= $end; $i++) {
    if(is_numeric($csvData['effectivenessRate'][$days.sprintf("%02d",$i).$seconds])) {
      $checkData = ' %';
    }
    else {
      $checkData = '';
    }
    array_push($userInfo[6], $csvData['effectivenessRate'][$days.sprintf("%02d",$i).$seconds].$checkData);
    }
    if(is_numeric($csvData['allEffectivenessRate'])) {
      $checkData = ' %';
    }
    else {
      $checkData = '';
    }
    array_push($userInfo[6], $csvData['allEffectivenessRate'].$checkData);
    return $userInfo;
  }

  private function outputCSVStatistics($csv = []) {
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
      $array[$k] = $this->isInValidDatetime($k) ? self::LABEL_NONE : self::LABEL_INVALID;
    }
    return $array;
  }

  private function convertBaseDataForNone($baseData) {
    $array = array();
    foreach ($baseData as $k => $v) {
      $array[$k] = self::LABEL_NONE;
    }
    return $array;
  }

  private function isInValidDatetime($datetimeStr) {
    // しきい値（2017年6月分は無効とする）
    $borderDatetime = strtotime('2017-06-30 23:59:59');
    $dateTime = strtotime($datetimeStr);

    return $dateTime <= $borderDatetime;
  }

  private function isInValidYear($dateStr) {
    // しきい値（2017年6月分は無効とする）
    $borderDate = strtotime('2017-01-01');
    $date = strtotime($dateStr);

    return $date < $borderDate;
  }
}
