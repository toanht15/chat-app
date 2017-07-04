<?php
/**
 * StatisticsController controller.
 * 統計機能
 */
class StatisticsController extends AppController {

  public $uses = ['THistory','THistoryWidgetDisplays'];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', '統計機能');
  }

  /* *
   * オペレーター統計
   * @return void
   * */
  public function forOperator() {
//LEFT JOIN  t_history_chat_logs as tha ON tha.t_histories_id = t_histories.id

  }

  /* *
   * チャット統計
   * @return void
   * */
  public function forChat() {
    Configure::write('debug', 2);
      $this->log('2',LOG_DEBUG);

    if($this->request->is('post')) {
      $this->THistory->set($this->request->data);
      if ($this->THistory->validates() ) {
        if(array_keys($this->request->data)[0] == 'year'){
          $data = $this->request->data['year'];
          $this->_yearCalculation($data);
        }
        else if(array_keys($this->request->data)[0] == 'month'){
          $data = $this->request->data['month'];
          $this->_monthCalculation($data);
        }
        else if(array_keys($this->request->data)[0] == 'day') {
          $data = $this->request->data['day'].' 00:00:00';
          $this->_dayCalculation($data);
        }
      }
    }
    $allInfo = $this->Session->read('allInfo');
    $Conditions = $this->Session->read('Conditions');
    $this->set('allInfo',$allInfo);
    $this->set('Conditions',$Conditions);
  }

  public function _yearCalculation($data){
    $accessNumber = [];
    $widjetNumber = [];
    $effectivenessNumber = [];
    $requestNumber = [];
    $responseNumber = [];
    $requestTimes = [];
    $start = $data.'-01';
    $end = $data.'-12';
    $startDate = strtotime('first day of' .$start);
    $endMonth = strtotime('last day of' .$start);
    $endDate = strtotime('last day of' .$end);
    $this->log('スタート',LOG_DEBUG);
    while($startDate < $endDate) {
      //合計アクセス件数
      $access = "SELECT count(th.id) FROM sinclo_db2.t_histories as th where th.access_date
      between '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$endMonth)." 23:59:59' and th.m_companies_id = ?";
      $accessNumber[] = $this->THistory->query($access, array($this->userInfo['MCompany']['id']));
      $this->log('access',LOG_DEBUG);
      //ウィジェット表示件数
      $widjet = "SELECT count(tw.id) FROM sinclo_db2.t_history_widget_displays as tw where tw.created between
      '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$endMonth)." 23:59:59' and tw.m_companies_id = ?";
      $widjetNumber[] = $this->THistoryWidgetDisplays->query($widjet, array($this->userInfo['MCompany']['id']));
      $this->log('widjet',LOG_DEBUG);
      //チャット有効件数、チャット拒否件数
      $this->log('effectivenessStart',LOG_DEBUG);
      $effectiveness = "SELECT count(th.id),SUM(case when t_history_chat_logs.achievement_flg = ? THEN 1 ELSE 0 END) yukou,
      SUM(case when t_history_chat_logs.message_type = ? THEN 1 ELSE 0 END) no FROM sinclo_db2.t_histories as th LEFT JOIN
      sinclo_db2.t_history_chat_logs ON t_history_chat_logs.t_histories_id = th.id where  th.access_date between
       '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$endMonth)." 23:59:59' and (t_history_chat_logs.achievement_flg = ? or t_history_chat_logs.message_type = ?)
      and th.m_companies_id = ?";
      $effectiveness = $this->THistory->query($effectiveness, array(2,4,2,4,$this->userInfo['MCompany']['id']));
      if($effectiveness[0][0]['count(*)'] == 0) {
        $effectiveness[0][0]['yukou'] = 0;
        $effectiveness[0][0]['no'] = 0;
      }
      $effectivenessNumber[] = $effectiveness;
      $this->log('effectivenessFinish',LOG_DEBUG);
      //チャットリクエスト件数
      $this->log('requestStart',LOG_DEBUG);
      $this->log('milli秒',date("H:i:s"));
      $request = "SELECT count(th.id) FROM sinclo_db2.t_histories as th LEFT JOIN (SELECT t_histories_id,message_request_flg FROM
      sinclo_db2.t_history_chat_logs where message_request_flg = ? ) as t_history_chat_logs ON t_history_chat_logs.t_histories_id = th.id where
      th.access_date between '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$endMonth)." 23:59:59' and
      t_history_chat_logs.message_request_flg = ? and th.m_companies_id = ?";
      $requestNumber[] = $this->THistory->query($request, array(1,1,$this->userInfo['MCompany']['id']));

      $this->log('milli秒',date("H:i:s"));
      $this->log('requestFinish',LOG_DEBUG);
      //チャット応対件数
      $this->log(microtime(true),LOG_DEBUG);
      $this->log('responseStart',LOG_DEBUG);
     //チャット応対件数
      $response = "SELECT count(distinct message_distinction,t_histories_id)  FROM sinclo_db2.t_histories as th LEFT JOIN
      (SELECT t_histories_id,message_type,message_distinction FROM sinclo_db2.t_history_chat_logs where message_type = ?) as t_history_chat_logs ON
      t_history_chat_logs.t_histories_id = th.id where t_history_chat_logs.message_type = ? and
      th.access_date between '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$startDate)." 23:59:59' and th.m_companies_id = ?";
      $responseNumber[] = $this->THistory->query($response, array(98,98,$this->userInfo['MCompany']['id']));
      $this->log('response',LOG_DEBUG);
      $this->log(microtime(true),LOG_DEBUG);

       //チャット応対率
      $responseRate[] = round($this->THistory->query($response, array(98,98,$this->userInfo['MCompany']['id']))[0][0]['count(distinct message_distinction,t_histories_id)']/$this->THistory->query($request, array(1,1,$this->userInfo['MCompany']['id']))[0][0]['count(th.id)']*100);

      //チャット有効率
      $effectivenessRate[] = round($effectiveness[0][0]['yukou']/$this->THistory->query($request, array(1,1,$this->userInfo['MCompany']['id']))[0][0]['count(th.id)']*100);

      //チャットリクエスト時間
      $sql2 = "SELECT th.id,th.m_companies_id,th.access_date,t_history_chat_logs.t_histories_id,t_history_chat_logs.created,
      t_history_chat_logs.message_request_flg FROM sinclo_db2.t_histories as th LEFT JOIN (SELECT t_histories_id,message_request_flg,created
      FROM sinclo_db2.t_history_chat_logs where message_request_flg = ?) as t_history_chat_logs ON t_history_chat_logs.t_histories_id = th.id where th.access_date between
      '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$startDate)." 23:59:59' and t_history_chat_logs.message_request_flg = ?
      and th.m_companies_id = ? group by t_histories_id";
      $return4 = $this->THistory->query($sql2, array(1,1,$this->userInfo['MCompany']['id']));
      $this->log('計算スタート',LOG_DEBUG);
      $return5 = '00:00:00';
      $v = 0;
      foreach($return4 as $k => $v) {
      $startDates = new DateTime($v['th']['access_date']);
      $endDates =new DateTime($v['t_history_chat_logs']['created']);
      $diff = $startDates->diff($endDates);
      $return4 = $diff->format('%H:%I:%S');
      $return5 = explode(":", $return5);
      $return4 = explode(":", $return4);
      $return5 = date("H:i:s", mktime($return5[0] + $return4[0], $return5[1] + $return4[1], $return5[2] + $return4[2]));
      }
      $this->log('計算終了',LOG_DEBUG);
      $this->log($return5,LOG_DEBUG);
      $requestTimes[] = $this->DivTime($return5,1/($k+1));
      $this->log('requestTimes',LOG_DEBUG);

      //平均消費者待機時間
      $sql6 = "SELECT th.id,th.access_date,th.m_companies_id,s1.t_histories_id,s1.message_request_flg,s1.created,s2.t_histories_id,s2.message_type,s2.created
      FROM sinclo_db2.t_histories as th LEFT JOIN (SELECT * FROM sinclo_db2.t_history_chat_logs where message_request_flg = ? group by t_histories_id)
      as s1 ON th.id = s1.t_histories_id LEFT JOIN (SELECT * FROM sinclo_db2.t_history_chat_logs where message_type = ? group by t_histories_id) as s2
      ON th.id = s2.t_histories_id where th.access_date between '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$startDate)." 23:59:59' and
      th.m_companies_id = ?";
      $nyuusituTime = $this->THistory->query($sql6, array(1,98,$this->userInfo['MCompany']['id']));
      //$this->log($this->THistory->query($sql6),LOG_DEBUG);
      $return5 = '00:00:00';
      foreach($nyuusituTime as $k => $v) {
        if(!empty($v['s1']['created']) && !empty($v['s2']['created'])) {
          $start2 = new DateTime($v['s1']['created']);
          $end2 = new DateTime($v['s2']['created']);
          $diff = $start2->diff($end2);
          $return4 = $diff->format('%H:%I:%S');
          $return5 = explode(":", $return5);
          $return4 = explode(":", $return4);
          $return5 = date("H:i:s", mktime($return5[0] + $return4[0], $return5[1] + $return4[1], $return5[2] + $return4[2]));
        }
      }
      $responseTimes[] = $this->DivTime($return5,1/($k+1));
      //平均応答時間
      $this->log(microtime(true),LOG_DEBUG);
      $sql7 = "SELECT * FROM sinclo_db2.t_histories LEFT JOIN (SELECT * FROM sinclo_db2.t_history_chat_logs where message_request_flg = ?
      group by t_histories_id) as s1 ON t_histories.id = s1.t_histories_id LEFT JOIN
      (SELECT * FROM sinclo_db2.t_history_chat_logs where message_type = ? group by t_histories_id) as s2 ON t_histories.id = s2.t_histories_id
      where t_histories.access_date between '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$startDate)." 23:59:59' and
      t_histories.m_companies_id = ?";
      $outouTime = $this->THistory->query($sql7, array(1,2,$this->userInfo['MCompany']['id']));
      $return5 = '00:00:00';
      foreach($outouTime as $k => $v) {
      if(!empty($v['s1']['created']) && !empty($v['s2']['created'])) {
      $start2 = new DateTime($v['s1']['created']);
      $end2 = new DateTime($v['s2']['created']);
      $diff = $start2->diff($end2);
      $return4 = $diff->format('%H:%I:%S');
      $return5 = explode(":", $return5);
      $return4 = explode(":", $return4);
      $return5 = date("H:i:s", mktime($return5[0] + $return4[0], $return5[1] + $return4[1], $return5[2] + $return4[2]));
      }
      }
      $outouTimes[] = $this->DivTime($return5,1/($k+1));
      $startDate = strtotime("+1 month", $startDate);
      $endMonth = strtotime('last day of' .date("Y-m-d",$startDate) );
    }
    $this->log($requestNumber,LOG_DEBUG);
    //合計値
    $allAccessNumber = 0;
    foreach($accessNumber as $k => $v) {
      $allAccessNumber = $allAccessNumber + $v[0][0]['count(*)'];
    }
    //ウィジェット合計値
    $allwidjetNumber = 0;
    foreach($widjetNumber as $k => $v) {
      $allwidjetNumber = $allwidjetNumber + $v[0][0]['count(*)'];
    }
    //有効、拒否件数合計値
    $allweffectiveness = 0;
    $allno = 0;
    foreach($effectivenessNumber as $k => $v) {
      $allweffectiveness = $allweffectiveness + $v[0][0]['yukou'];
      $allno = $allno + $v[0][0]['no'];
    }
    //リクエスト件数合計値
    $allrequest = 0;
    foreach($requestNumber as $k => $v) {
      $allrequest = $allrequest + $v[0][0]['count(*)'];
    }
    //応対数合計値
    $allresponse = 0;
    foreach($responseNumber as $k => $v) {
      $allresponse = $allresponse + $v[0][0]['count(distinct message_distinction,t_histories_id)'];
    }

    //合計チャット応対率
    $allResponseRate = round($allresponse/$allrequest*100);

    //合計チャット有効率
    $allEffectivenessRate = round($allweffectiveness/$allrequest*100);

    //合計リクエスト平均時間
    $time = '00:00:00';
    foreach($requestTimes as $k => $v) {
    $time = explode(":", $time);
    $averageTime = explode(":", $v);
    $averageTimes = date("H:i:s", mktime($time[0] + $averageTime[0], $time[1] + $averageTime[1], $time[2] + $averageTime[2]));
    }
    $averageRequestTimes = $this->DivTime($averageTimes,1/($k+1));

    $this->log('foreach',LOG_DEBUG);
    $this->log('応答率',LOG_DEBUG);
    $this->log($responseRate,LOG_DEBUG);
    $allInfo['data'] = $data;
    $allInfo['accessNumber'] = $accessNumber;
    $allInfo['widjetNumber'] = $widjetNumber;
    $allInfo['effectivenessNumber'] = $effectivenessNumber;
    $allInfo['requestNumber'] = $requestNumber;
    $allInfo['responseNumber'] = $responseNumber;
    $allInfo['responseRate'] = $responseRate;
    $allInfo['effectivenessRate'] = $effectivenessRate;
    $allInfo['requestTimes'] = $requestTimes;
    $allInfo['allAccessNumber'] = $allAccessNumber;
    $allInfo['allwidjetNumber'] = $allwidjetNumber;
    $allInfo['allweffectiveness'] = $allweffectiveness;
    $allInfo['allno'] = $allno;
    $allInfo['allrequest'] = $allrequest;
    $allInfo['allresponse'] = $allresponse;
    $allInfo['allResponseRate'] = $allResponseRate;
    $allInfo['allEffectivenessRate'] = $allEffectivenessRate;
    $allInfo['averageRequestTimes'] = $averageRequestTimes;

    $Conditions = [
      'accessNumber' => $accessNumber,
      'widjetNumber' => $widjetNumber,'effectivenessNumber' => $effectivenessNumber,'requestNumber' => $requestNumber,
      'responseNumber' => $responseNumber,'responseRate' => $responseRate,'effectivenessRate' => $effectivenessRate,
      'requestTimes' => $requestTimes,'data' => $data
    ];

    $this->Session->write('allInfo',$allInfo);
    $this->Session->write('Conditions',$Conditions);
    //$this->log($allInfo,LOG_DEBUG);
    $this->log('終わり',LOG_DEBUG);
  }

  public function _monthCalculation($data){
    $widjetNumber = [];
    $effectivenessNumber = [];
    $requestNumber = [];
    $responseNumber = [];
    $requestTimes = [];
    $responseTimes = [];
    $effectivenessRate = [];
    $responseRate = [];
    $outouTimes = [];
    $allInfo = [];
    $array02 = [];
    $array01 = [];
    $array03 = [];

    $startDate = strtotime('first day of' .$data);
    $endDate = strtotime('last day of' .$data);
    $correctStartDate = date("Y-m-d 00:00:00",$startDate);
    $correctEndDate = date("Y-m-d 23:59:59",$endDate);
    $this->log($startDate,LOG_DEBUG);
    $this->log($endDate,LOG_DEBUG);

    //応答件数
    $response = "SELECT date_format(th.access_date, '%Y-%m-%d') as date, count(distinct message_distinction,t_histories_id)  FROM sinclo_db2.t_histories as th LEFT JOIN
    (SELECT t_histories_id,message_type,message_distinction FROM sinclo_db2.t_history_chat_logs where message_type = ?) as t_history_chat_logs ON
    t_history_chat_logs.t_histories_id = th.id where t_history_chat_logs.message_type = ? and
    th.access_date between ? and ? and th.m_companies_id = ? group by date(th.access_date)";
    $responseNumber = $this->THistory->query($response, array(98,98,$correctStartDate,$correctEndDate,$this->userInfo['MCompany']['id']));

    while($startDate <= $endDate){
      $array01 = $array01 + array(date("Y-m-d",$startDate) => 0);
      $startDate = strtotime("+1 day", $startDate);
    }
    $startDate = strtotime('first day of' .$data);

    foreach($responseNumber as $v) {
      $array02 =  $array02 + array($v[0]['date'] => $v[0]['count(distinct message_distinction,t_histories_id)']);
    }

    $responseNumber = array_merge($array01,$array02);
    $this->log('response',LOG_DEBUG);
    $this->log($responseNumber,LOG_DEBUG);

    //アクセス件数
    $access = "SELECT date_format(th.access_date, '%Y-%m-%d') as date, count(th.id) FROM sinclo_db2.t_histories as th where th.access_date
    between ? and ? and th.m_companies_id = ? group by date(th.access_date)";
    $accessNumber = $this->THistory->query($access, array($correctStartDate,$correctEndDate,$this->userInfo['MCompany']['id']));

    //ウィジェット表示件数
    $widjet = "SELECT date_format(tw.created, '%Y-%m-%d') as date,count(tw.id) FROM sinclo_db2.t_history_widget_displays as tw where tw.created between
    ? and ? and tw.m_companies_id = ? group by date(tw.created)";
    $widjetNumber[] = $this->THistoryWidgetDisplays->query($widjet, array($correctStartDate,$correctEndDate,$this->userInfo['MCompany']['id']));
    $this->log('widjetNumber',LOG_DEBUG);
    $this->log($widjetNumber,LOG_DEBUG);

    //チャットリクエスト件数
    $requestNumber = "SELECT date_format(th.access_date, '%Y-%m-%d') as date, count(th.id) FROM sinclo_db2.t_histories as th
      LEFT JOIN (SELECT t_histories_id,message_request_flg FROM sinclo_db2.t_history_chat_logs where message_request_flg = ? ) as t_history_chat_logs
      ON t_history_chat_logs.t_histories_id = th.id
      where th.access_date between ? and ? and t_history_chat_logs.message_request_flg = ? and th.m_companies_id = ?
      group by date(th.access_date)";
    $requestNumber = $this->THistory->query($requestNumber, array(1,$correctStartDate,$correctEndDate,1,$this->userInfo['MCompany']['id']));
    $this->log('requestNumber0',LOG_DEBUG);
    $this->log($requestNumber,LOG_DEBUG);

    foreach($requestNumber as $v) {
      $array03 =  $array03 + array($v[0]['date'] => $v[0]['count(th.id)']);
    }

    $requestNumber = array_merge($array01,$array03);

    //チャット有効件数
     $effectiveness = "SELECT date_format(th.access_date, '%Y-%m-%d') as date, count(th.id),SUM(case when t_history_chat_logs.achievement_flg = ? THEN 1 ELSE 0 END) yukou,
     SUM(case when t_history_chat_logs.message_type = ? THEN 1 ELSE 0 END) no FROM sinclo_db2.t_histories as th LEFT JOIN
     sinclo_db2.t_history_chat_logs ON t_history_chat_logs.t_histories_id = th.id where  th.access_date between
      ? and ? and (t_history_chat_logs.achievement_flg = ? or t_history_chat_logs.message_type = ?)
     and th.m_companies_id = ? group by date(th.access_date)";
     $effectiveness = $this->THistory->query($effectiveness, array(2,4,$correctStartDate,$correctEndDate,2,4,$this->userInfo['MCompany']['id']));
     if($effectiveness[0][0]['count(th.id)'] == 0) {
       $effectiveness[0][0]['yukou'] = 0;
       $effectiveness[0][0]['no'] = 0;
     }
     $effectivenessNumber = $effectiveness;

    //チャット応対率
    $responseRate = round($this->THistory->query($response, array(98,98,$correctStartDate,$correctEndDate,$this->userInfo['MCompany']['id']))[0][0]['count(distinct message_distinction,t_histories_id)']/$this->THistory->query($request, array(1,1,$correctStartDate,$correctEndDate,$this->userInfo['MCompany']['id']))[0][0]['count(th.id)']*100);
    $this->log('応対率',LOG_DEBUG);
    $this->log($responseRate,LOG_DEBUG);
    //チャット有効率
    $effectivenessRate = round($effectiveness[0]['yukou']/$this->THistory->query($request, array(1,1,$correctStartDate,$correctEndDate,$this->userInfo['MCompany']['id']))[0][0]['count(th.id)']*100);
    $this->log('有効率',LOG_DEBUG);
    $this->log($effectivenessRate,LOG_DEBUG);

    $this->log('新しいクエリ',LOG_DEBUG);
      $startDate = strtotime('first day of' .$data);
    $endDate = strtotime('last day of' .$data);
    //$this->log($aaa,LOG_DEBUG);
    /*while($startDate <= $endDate) {
      $this->log('accessStart',LOG_DEBUG);
      //合計アクセス件数
      $this->log(microtime(true),LOG_DEBUG);
      $access = "SELECT count(th.id) FROM sinclo_db2.t_histories as th where th.access_date
      between '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$startDate)." 23:59:59' and th.m_companies_id = ?";
      $accessNumber[] = $this->THistory->query($access, array($this->userInfo['MCompany']['id']));
      $this->log('accessFinish',LOG_DEBUG);
      //$this->log($accessNumber,LOG_DEBUG);
      $this->log('wijetStart',LOG_DEBUG);
      //ウィジェット表示件数
      $this->log(microtime(true),LOG_DEBUG);
      $widjet = "SELECT count(tw.id) FROM sinclo_db2.t_history_widget_displays as tw where tw.created between
       '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$startDate)." 23:59:59' and tw.m_companies_id = ?";
      $widjetNumber[] = $this->THistoryWidgetDisplays->query($widjet, array($this->userInfo['MCompany']['id']));
      $this->log('wijetStartFinish',LOG_DEBUG);
      $this->log('yuukouStart',LOG_DEBUG);
       //チャット有効件数、チャット拒否件数
      $this->log(microtime(true),LOG_DEBUG);
      $effectiveness = "SELECT count(th.id),SUM(case when t_history_chat_logs.achievement_flg = ? THEN 1 ELSE 0 END) yukou,
      SUM(case when t_history_chat_logs.message_type = ? THEN 1 ELSE 0 END) no FROM sinclo_db2.t_histories as th LEFT JOIN
      sinclo_db2.t_history_chat_logs ON t_history_chat_logs.t_histories_id = th.id where  th.access_date between
       '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$startDate)." 23:59:59' and (t_history_chat_logs.achievement_flg = ? or t_history_chat_logs.message_type = ?)
      and th.m_companies_id = ?";
      $effectiveness = $this->THistory->query($effectiveness, array(2,4,2,4,$this->userInfo['MCompany']['id']));
      if($effectiveness[0][0]['count(*)'] == 0) {
        $effectiveness[0][0]['yukou'] = 0;
        $effectiveness[0][0]['no'] = 0;
      }
      $effectivenessNumber[] = $effectiveness;
      $this->log('yuukouFinish',LOG_DEBUG);
      $this->log('requestStart',LOG_DEBUG);
      //チャットリクエスト件数
      $this->log(microtime(true),LOG_DEBUG);
      $request = "SELECT count(th.id) FROM sinclo_db2.t_histories as th LEFT JOIN (SELECT t_histories_id,message_request_flg FROM
      sinclo_db2.t_history_chat_logs where message_request_flg = ? ) as t_history_chat_logs ON t_history_chat_logs.t_histories_id = th.id where
      th.access_date between '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$startDate)." 23:59:59' and
      t_history_chat_logs.message_request_flg = ? and th.m_companies_id = ?";
      $requestNumber[] = $this->THistory->query($request, array(1,1,$this->userInfo['MCompany']['id']));
      $this->log('requestFinish',LOG_DEBUG);

      $this->log('responseStart',LOG_DEBUG);
     //チャット応対件数
      $this->log(microtime(true),LOG_DEBUG);
      $response = "SELECT count(distinct message_distinction,t_histories_id)  FROM sinclo_db2.t_histories as th LEFT JOIN
      (SELECT t_histories_id,message_type,message_distinction FROM sinclo_db2.t_history_chat_logs where message_type = ?) as t_history_chat_logs ON
      t_history_chat_logs.t_histories_id = th.id where t_history_chat_logs.message_type = ? and
      th.access_date between '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$startDate)." 23:59:59' and th.m_companies_id = ?";
      $responseNumber[] = $this->THistory->query($response, array(98,98,$this->userInfo['MCompany']['id']));
      $this->log('responseFinish',LOG_DEBUG);
      $this->log('outairituStart',LOG_DEBUG);
      //チャット応対率
      $this->log(microtime(true),LOG_DEBUG);
      $responseRate[] = round($this->THistory->query($response, array(98,98,$this->userInfo['MCompany']['id']))[0][0]['count(distinct message_distinction,t_histories_id)']/$this->THistory->query($request, array(1,1,$this->userInfo['MCompany']['id']))[0][0]['count(th.id)']*100);
      $this->log('outairituFinsih',LOG_DEBUG);
      $this->log('yuukourituStart',LOG_DEBUG);
      //チャット有効率
      $this->log(microtime(true),LOG_DEBUG);
      $effectivenessRate[] = round($effectiveness[0][0]['yukou']/$this->THistory->query($request, array(1,1,$this->userInfo['MCompany']['id']))[0][0]['count(th.id)']*100);
      $this->log('yuukourituFinsish',LOG_DEBUG);
      //$this->log('requestTime',LOG_DEBUG);
      $this->log('responseTimeStart',LOG_DEBUG);
      $this->log('データ収集',LOG_DEBUG);
      //平均チャットリクエスト時間
      $this->log(microtime(true),LOG_DEBUG);
      $sql2 = "SELECT th.id,th.m_companies_id,th.access_date,t_history_chat_logs.t_histories_id,t_history_chat_logs.created,
      t_history_chat_logs.message_request_flg FROM sinclo_db2.t_histories as th LEFT JOIN (SELECT t_histories_id,message_request_flg,created
      FROM sinclo_db2.t_history_chat_logs where message_request_flg = ?) as t_history_chat_logs ON t_history_chat_logs.t_histories_id = th.id where th.access_date between
      '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$startDate)." 23:59:59' and t_history_chat_logs.message_request_flg = ?
      and th.m_companies_id = ? group by t_histories_id";
      $return4 = $this->THistory->query($sql2, array(1,1,$this->userInfo['MCompany']['id']));
      $this->log('計算スタート',LOG_DEBUG);
      $return5 = '00:00:00';
      $v = 0;
      foreach($return4 as $k => $v) {
      $startDates = new DateTime($v['th']['access_date']);
      $endDates =new DateTime($v['t_history_chat_logs']['created']);
      $diff = $startDates->diff($endDates);
      $return4 = $diff->format('%H:%I:%S');
      $return5 = explode(":", $return5);
      $return4 = explode(":", $return4);
      $return5 = date("H:i:s", mktime($return5[0] + $return4[0], $return5[1] + $return4[1], $return5[2] + $return4[2]));
      }
      $this->log('計算終了',LOG_DEBUG);
      $this->log($return5,LOG_DEBUG);
      $requestTimes[] = $this->DivTime($return5,1/($k+1));
      $this->log('responseTimeFinish',LOG_DEBUG);
      //平均消費者待機時間
      $this->log(microtime(true),LOG_DEBUG);
      $sql6 = "SELECT th.id,th.access_date,th.m_companies_id,s1.t_histories_id,s1.message_request_flg,s1.created,s2.t_histories_id,s2.message_type,s2.created
      FROM sinclo_db2.t_histories as th LEFT JOIN (SELECT * FROM sinclo_db2.t_history_chat_logs where message_request_flg = ? group by t_histories_id)
      as s1 ON th.id = s1.t_histories_id LEFT JOIN (SELECT * FROM sinclo_db2.t_history_chat_logs where message_type = ? group by t_histories_id) as s2
      ON th.id = s2.t_histories_id where th.access_date between '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$startDate)." 23:59:59' and
      th.m_companies_id = ?";
      $nyuusituTime = $this->THistory->query($sql6, array(1,98,$this->userInfo['MCompany']['id']));
      //$this->log($this->THistory->query($sql6),LOG_DEBUG);
      $return5 = '00:00:00';
      foreach($nyuusituTime as $k => $v) {
        if(!empty($v['s1']['created']) && !empty($v['s2']['created'])) {
          $start2 = new DateTime($v['s1']['created']);
          $end2 = new DateTime($v['s2']['created']);
          $diff = $start2->diff($end2);
          $return4 = $diff->format('%H:%I:%S');
          $return5 = explode(":", $return5);
          $return4 = explode(":", $return4);
          $return5 = date("H:i:s", mktime($return5[0] + $return4[0], $return5[1] + $return4[1], $return5[2] + $return4[2]));
        }
      }
      $responseTimes[] = $this->DivTime($return5,1/($k+1));
      //平均応答時間
      $this->log(microtime(true),LOG_DEBUG);
      $sql7 = "SELECT * FROM sinclo_db2.t_histories LEFT JOIN (SELECT * FROM sinclo_db2.t_history_chat_logs where message_request_flg = ?
      group by t_histories_id) as s1 ON t_histories.id = s1.t_histories_id LEFT JOIN
      (SELECT * FROM sinclo_db2.t_history_chat_logs where message_type = ? group by t_histories_id) as s2 ON t_histories.id = s2.t_histories_id
      where t_histories.access_date between '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$startDate)." 23:59:59' and
      t_histories.m_companies_id = ?";
      $outouTime = $this->THistory->query($sql7, array(1,2,$this->userInfo['MCompany']['id']));
      $return5 = '00:00:00';
      foreach($outouTime as $k => $v) {
      if(!empty($v['s1']['created']) && !empty($v['s2']['created'])) {
      $start2 = new DateTime($v['s1']['created']);
      $end2 = new DateTime($v['s2']['created']);
      $diff = $start2->diff($end2);
      $return4 = $diff->format('%H:%I:%S');
      $return5 = explode(":", $return5);
      $return4 = explode(":", $return4);
      $return5 = date("H:i:s", mktime($return5[0] + $return4[0], $return5[1] + $return4[1], $return5[2] + $return4[2]));
      }
      }
      $outouTimes[] = $this->DivTime($return5,1/($k+1));
      $startDate = strtotime("+1 day", $startDate);
    }*/
    $this->log('合計件数',LOG_DEBUG);
    //合計値
    $allAccessNumber = 0;
    foreach($accessNumber as $k => $v) {
      $allAccessNumber = $allAccessNumber + $v[0][0]['count(th.id)'];
    }
    //ウィジェット合計値
    $allwidjetNumber = 0;
    foreach($widjetNumber as $k => $v) {
      $allwidjetNumber = $allwidjetNumber + $v[0][0]['count(tw.id)'];
    }
    //有効、拒否件数合計値
    $allweffectiveness = 0;
    $allno = 0;
    foreach($effectivenessNumber as $k => $v) {
      $allweffectiveness = $allweffectiveness + $v[0][0]['yukou'];
      $allno = $allno + $v[0][0]['no'];
    }
    //リクエスト件数合計値
    $allrequest = 0;
    foreach($requestNumber as $k => $v) {
      $allrequest = $allrequest + $v[0][0]['count(th.id)'];
    }
    //応答件数合計値
    $allresponse = 0;
    foreach($responseNumber as $k => $v) {
      $allresponse = $allresponse + $v[0][0]['count(distinct message_distinction,t_histories_id)'];
    }
    $this->log('合計平均時間',LOG_DEBUG);
    //合計リクエスト平均時間
    /*$time = '00:00:00';
    foreach($requestTimes as $k => $v) {
    $time = explode(":", $time);
    $averageTime = explode(":", $v);
    $averageTimes = date("H:i:s", mktime($time[0] + $averageTime[0], $time[1] + $averageTime[1], $time[2] + $averageTime[2]));
    }
    $averageRequestTimes = $this->DivTime($averageTimes,1/($k+1));

    //合計消費者待機平均時間
    $time = '00:00:00';
    $number = 0;
    foreach($responseTimes as $k => $v) {
    if($v != "00:00:00" ) {
    $number = $number +1;
    $time = explode(":", $time);
    $averageTime = explode(":", $v);
    $time = date("H:i:s", mktime($time[0] + $averageTime[0], $time[1] + $averageTime[1], $time[2] + $averageTime[2]));
    }
    }
    $averageResponseTimes = $this->DivTime($time,1/($number));

    //合計消費者待機平均時間
    $time = '00:00:00';
    $number = 0;
    foreach($outouTimes as $k => $v) {
    if($v != "00:00:00" ) {
    $number = $number +1;
    $time = explode(":", $time);
    $averageTime = explode(":", $v);
    $time = date("H:i:s", mktime($time[0] + $averageTime[0], $time[1] + $averageTime[1], $time[2] + $averageTime[2]));
    }
    }
    $averageoutouTimes = $this->DivTime($time,1/($number));*/

    //合計チャット応対率
    $allResponseRate = round($allresponse/$allrequest*100);

    //合計チャット有効率
    $allEffectivenessRate = round($allweffectiveness/$allrequest*100);

    $allInfo['accessNumber'] = $accessNumber;
    $allInfo['data'] = $data;
    $allInfo['widjetNumber'] = $widjetNumber;
    $allInfo['effectivenessNumber'] = $effectivenessNumber;
    $allInfo['requestNumber'] = $requestNumber;
    $allInfo['responseNumber'] = $responseNumber;
    $allInfo['responseRate'] = $responseRate;
    $allInfo['effectivenessRate'] = $effectivenessRate;
    $allInfo['requestTimes'] = $requestTimes;
    $allInfo['allAccessNumber'] = $allAccessNumber;
    $allInfo['allwidjetNumber'] = $allwidjetNumber;
    $allInfo['allweffectiveness'] = $allweffectiveness;
    $allInfo['allno'] = $allno;
    $allInfo['allrequest'] = $allrequest;
    $allInfo['allresponse'] = $allresponse;
    $allInfo['allResponseRate'] = $allResponseRate;
    $allInfo['allEffectivenessRate'] = $allEffectivenessRate;
    $allInfo['averageRequestTimes'] = $averageRequestTimes;
    $Conditions = [
      'accessNumber' => $accessNumber,
      'widjetNumber' => $widjetNumber,'effectivenessNumber' => $effectivenessNumber,'requestNumber' => $requestNumber,
      'responseNumber' => $responseNumber,'responseRate' => $responseRate,'effectivenessRate' => $effectivenessRate,
      'requestTimes' => $requestTimes,'data' => $data,'requestNumber2' => $requestNumber2
    ];
    //$this->log($allInfo,LOG_DEBUG);
    $this->Session->write('allInfo',$allInfo);
    $this->Session->write('Conditions',$Conditions);
    $this->log('終わり',LOG_DEBUG);
  }

  public function OutputCsv() {
    $this->autoRender = false;
    $allInfo = $this->Session->read('allInfo');
    $Conditions = $this->Session->read('Conditions');

    if(mb_strlen($Conditions['data']) == 4) {
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
    else if(mb_strlen($Conditions['data']) == 7) {
      $firstDate = strtotime('first day of ' .$Conditions['data']);
      $lastDate = strtotime('last day of ' . $Conditions['data']);
      $monthData = [];
      $monthData[] = '統計項目/月別';
      while($firstDate <= $lastDate) {
        $monthData[] = date('Y-m-d',$firstDate);
        $firstDate = strtotime("+1 day", $firstDate);
      }
      $csv[] = $monthData;
    }
    else if(mb_strlen($Conditions['data']) == 19) {
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




    $row = [];
    $row[] = '合計アクセス件数';
    $row2 = [];
    $row2[] = 'ウィジェット件数';
    $row3 = [];
    $row3[] = 'チャットリクエスト件数';
    $row4 = [];
    $row4[] = 'チャット応対件数';
    $row5 = [];
    $row5[] = 'チャット拒否件数';
    $row6 = [];
    $row6[] = 'チャット有効件数';
    $row7 = [];
    $row7[] = '平均チャットリクエスト時間';
    $row8 = [];
    $row8[] = 'チャット応対率';
    $row9 = [];
    $row9[] = 'チャット有効率';
    foreach($Conditions['accessNumber'] as $key => $v) {
      $row[] = $v[0][0]['count(th.id)'];
    }
    foreach($Conditions['widjetNumber'] as $key => $v) {
      $row2[] = $v[0][0]['count(tw.id)'];
    }
    foreach($Conditions['requestNumber'] as $key => $v) {
      $row3[] = $v[0][0]['count(th.id)'];
    }
    foreach($Conditions['responseNumber'] as $key => $v) {;
      $row4[] = $v[0][0]['count(distinct message_distinction,t_histories_id)'];
    }
    foreach($Conditions['effectivenessNumber'] as $key => $v) {;
      $row5[] = $v[0][0]['no'];
      $row6[] = $v[0][0]['yukou'];
    }
    foreach($Conditions['requestTimes'] as $key => $v) {;
      $row7[] = $v;
    }
    foreach($Conditions['responseRate'] as $key => $v2) {;
      $row8[] = $v2;
    }
    foreach($Conditions['effectivenessRate'] as $key => $v3) {;
      $row9[] = $v3;
    }
    $csv[] = $row;
    $csv[] = $row2;
    $csv[] = $row3;
    $csv[] = $row4;
    $csv[] = $row5;
    $csv[] = $row6;
    $csv[] = $row7;
    $csv[] = $row8;
    $csv[] = $row9;
    $this->_outputCSV2($csv);
  }

  public function _outputCSV2($csv = []) {
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

  public function _dayCalculation($data){
    $this->log('day入ってる',LOG_DEBUG);
    $this->log($data,LOG_DEBUG);
    $accessNumber = [];
    $widjetNumber = [];
    $effectivenessNumber = [];
    $requestNumber = [];
    $responseNumber = [];
    $requestTimes = [];
    $effectivenessRate = [];
    $startDate = strtotime($data);
    $endDate = strtotime("+1 day",$startDate.' 00:00:00');
    while($startDate < $endDate) {
      //合計アクセス件数
      $access = "SELECT count(th.id) FROM sinclo_db2.t_histories as th where th.access_date
      between '".date("Y-m-d H",$startDate).":00:00' and '".date("Y-m-d H",$startDate).":59:59' and th.m_companies_id = ?";
      $accessNumber[] = $this->THistory->query($access, array($this->userInfo['MCompany']['id']));
      //ウィジェット表示件数
      $widjet = "SELECT count(tw.id) FROM sinclo_db2.t_history_widget_displays as tw where tw.created between
       '".date("Y-m-d H",$startDate).":00:00' and '".date("Y-m-d H",$startDate).":59:59' and tw.m_companies_id = ?";
      $widjetNumber[] = $this->THistoryWidgetDisplays->query($widjet, array($this->userInfo['MCompany']['id']));
      //チャット有効件数、チャット拒否件数
      $effectiveness = "SELECT count(th.id),SUM(case when t_history_chat_logs.achievement_flg = ? THEN 1 ELSE 0 END) yukou,
      SUM(case when t_history_chat_logs.message_type = ? THEN 1 ELSE 0 END) no FROM sinclo_db2.t_histories as th LEFT JOIN
      sinclo_db2.t_history_chat_logs ON t_history_chat_logs.t_histories_id = th.id where  th.access_date between
       '".date("Y-m-d H",$startDate).":00:00' and '".date("Y-m-d H",$startDate).":59:59' and (t_history_chat_logs.achievement_flg = ? or t_history_chat_logs.message_type = ?)
      and th.m_companies_id = ?";
      $effectiveness = $this->THistory->query($effectiveness, array(2,4,2,4,$this->userInfo['MCompany']['id']));
      if($effectiveness[0][0]['count(*)'] == 0) {
        $effectiveness[0][0]['yukou'] = 0;
        $effectiveness[0][0]['no'] = 0;
      }
      $effectivenessNumber[] = $effectiveness;
      //チャットリクエスト件数
      $request = "SELECT count(th.id) FROM sinclo_db2.t_histories as th LEFT JOIN (SELECT t_histories_id,message_request_flg FROM
      sinclo_db2.t_history_chat_logs where message_request_flg = ? ) as t_history_chat_logs ON t_history_chat_logs.t_histories_id = th.id where
      th.access_date between '".date("Y-m-d H",$startDate).":00:00' and '".date("Y-m-d H",$startDate).":59:59' and
      t_history_chat_logs.message_request_flg = ? and th.m_companies_id = ?";
      $requestNumber[] = $this->THistory->query($request, array(1,1,$this->userInfo['MCompany']['id']));;
     //チャット応対件数
      $response = "SELECT count(distinct message_distinction,t_histories_id)  FROM sinclo_db2.t_histories as th LEFT JOIN
      (SELECT t_histories_id,message_type,message_distinction FROM sinclo_db2.t_history_chat_logs where message_type = ?) as t_history_chat_logs ON
      t_history_chat_logs.t_histories_id = th.id where t_history_chat_logs.message_type = ? and
      th.access_date between '".date("Y-m-d H",$startDate).":00:00' and '".date("Y-m-d H",$startDate).":59:59' and th.m_companies_id = ?";
      $responseNumber[] = $this->THistory->query($response, array(98,98,$this->userInfo['MCompany']['id']));

      //チャット応対率
      $responseRate[] =round($this->THistory->query($response, array(98,98,$this->userInfo['MCompany']['id']))[0][0]['count(distinct message_distinction,t_histories_id)']/$this->THistory->query($request, array(1,1,$this->userInfo['MCompany']['id']))[0][0]['count(th.id)']*100);

      //チャット有効率
      $effectivenessRate[] = round($effectiveness[0][0]['yukou']/$this->THistory->query($request, array(1,1,$this->userInfo['MCompany']['id']))[0][0]['count(th.id)']*100);

      //平均チャットリクエスト時間
      $sql2 = "SELECT th.id,th.m_companies_id,th.access_date,t_history_chat_logs.t_histories_id,t_history_chat_logs.created,
      t_history_chat_logs.message_request_flg FROM sinclo_db2.t_histories as th LEFT JOIN (SELECT t_histories_id,message_request_flg,created
      FROM sinclo_db2.t_history_chat_logs where message_request_flg = ?) as t_history_chat_logs ON t_history_chat_logs.t_histories_id = th.id where th.access_date between
      '".date("Y-m-d H",$startDate).":00:00' and '".date("Y-m-d H",$startDate).":59:59' and t_history_chat_logs.message_request_flg = ?
      and th.m_companies_id = ? group by t_histories_id";
      $return4 = $this->THistory->query($sql2, array(1,1,$this->userInfo['MCompany']['id']));
      $this->log('計算スタート',LOG_DEBUG);
      $return5 = '00:00:00';
      $v = 0;
      foreach($return4 as $k => $v) {
      $startDates = new DateTime($v['th']['access_date']);
      $endDates =new DateTime($v['t_history_chat_logs']['created']);
      $diff = $startDates->diff($endDates);
      $return4 = $diff->format('%H:%I:%S');
      $return5 = explode(":", $return5);
      $return4 = explode(":", $return4);
      $return5 = date("H:i:s", mktime($return5[0] + $return4[0], $return5[1] + $return4[1], $return5[2] + $return4[2]));
      }
      $this->log('計算終了',LOG_DEBUG);
      $this->log($return5,LOG_DEBUG);
      $requestTimes[] = $this->DivTime($return5,1/($k+1));
      //平均消費者待機時間
      $sql6 = "SELECT th.id,th.access_date,th.m_companies_id,s1.t_histories_id,s1.message_request_flg,s1.created,s2.t_histories_id,s2.message_type,s2.created
      FROM sinclo_db2.t_histories as th LEFT JOIN (SELECT * FROM sinclo_db2.t_history_chat_logs where message_request_flg = ? group by t_histories_id)
      as s1 ON th.id = s1.t_histories_id LEFT JOIN (SELECT * FROM sinclo_db2.t_history_chat_logs where message_type = ? group by t_histories_id) as s2
      ON th.id = s2.t_histories_id where th.access_date between '".date("Y-m-d H",$startDate).":00:00' and '".date("Y-m-d H",$startDate).":59:59' and
      th.m_companies_id = ?";
      $nyuusituTime = $this->THistory->query($sql6, array(1,98,$this->userInfo['MCompany']['id']));
      //$this->log($this->THistory->query($sql6),LOG_DEBUG);
      $return5 = '00:00:00';
      foreach($nyuusituTime as $k => $v) {
        if(!empty($v['s1']['created']) && !empty($v['s2']['created'])) {
          $start2 = new DateTime($v['s1']['created']);
          $end2 = new DateTime($v['s2']['created']);
          $diff = $start2->diff($end2);
          $return4 = $diff->format('%H:%I:%S');
          $return5 = explode(":", $return5);
          $return4 = explode(":", $return4);
          $return5 = date("H:i:s", mktime($return5[0] + $return4[0], $return5[1] + $return4[1], $return5[2] + $return4[2]));
        }
      }
      $responseTimes[] = $this->DivTime($return5,1/($k+1));

      //平均応答時間
      $this->log(microtime(true),LOG_DEBUG);
      $sql7 = "SELECT * FROM sinclo_db2.t_histories LEFT JOIN (SELECT * FROM sinclo_db2.t_history_chat_logs where message_request_flg = ?
      group by t_histories_id) as s1 ON t_histories.id = s1.t_histories_id LEFT JOIN
      (SELECT * FROM sinclo_db2.t_history_chat_logs where message_type = ? group by t_histories_id) as s2 ON t_histories.id = s2.t_histories_id
      where t_histories.access_date between '".date("Y-m-d H",$startDate).":00:00' and '".date("Y-m-d H",$startDate).":59:59' and
      t_histories.m_companies_id = ?";
      $outouTime = $this->THistory->query($sql7, array(1,2,$this->userInfo['MCompany']['id']));
      $return5 = '00:00:00';
      foreach($outouTime as $k => $v) {
      if(!empty($v['s1']['created']) && !empty($v['s2']['created'])) {
      $start2 = new DateTime($v['s1']['created']);
      $end2 = new DateTime($v['s2']['created']);
      $diff = $start2->diff($end2);
      $return4 = $diff->format('%H:%I:%S');
      $return5 = explode(":", $return5);
      $return4 = explode(":", $return4);
      $return5 = date("H:i:s", mktime($return5[0] + $return4[0], $return5[1] + $return4[1], $return5[2] + $return4[2]));
      }
      }
      $outouTimes[] = $this->DivTime($return5,1/($k+1));
      $startDate = strtotime("+1 hour", $startDate);
    }

    $this->log($requestTimes,LOG_DEBUG);
    //合計値
    $allAccessNumber = 0;
    foreach($accessNumber as $k => $v) {
      $allAccessNumber = $allAccessNumber + $v[0][0]['count(*)'];
    }
    //ウィジェット合計値
    $allwidjetNumber = 0;
    foreach($widjetNumber as $k => $v) {
      $allwidjetNumber = $allwidjetNumber + $v[0][0]['count(*)'];
    }
    //有効、拒否件数合計値
    $allweffectiveness = 0;
    $allno = 0;
    foreach($effectivenessNumber as $k => $v) {
      $allweffectiveness = $allweffectiveness + $v[0][0]['yukou'];
      $allno = $allno + $v[0][0]['no'];
    }
    //リクエスト件数合計値
    $allrequest = 0;
    foreach($requestNumber as $k => $v) {
      $allrequest = $allrequest + $v[0][0]['count(*)'];
    }
    //応答件数合計値
    $allresponse = 0;
    foreach($responseNumber as $k => $v) {
      $allresponse = $allresponse + $v[0][0]['count(distinct message_distinction,t_histories_id)'];
    }

    //合計チャット応対率
    $allResponseRate = round($allresponse/$allrequest*100);

    //合計リクエスト平均時間
    $time = '00:00:00';
    foreach($requestTimes as $k => $v) {
    $time = explode(":", $time);
    $averageTime = explode(":", $v);
    $averageTimes = date("H:i:s", mktime($time[0] + $averageTime[0], $time[1] + $averageTime[1], $time[2] + $averageTime[2]));
    }
    $averageRequestTimes = $this->DivTime($averageTimes,1/($k+1));

    $this->log($averageRequestTimes,LOG_DEBUG);

    $allInfo['accessNumber'] = $accessNumber;
    $allInfo['data'] = $data;
    $allInfo['widjetNumber'] = $widjetNumber;
    $allInfo['effectivenessNumber'] = $effectivenessNumber;
    $allInfo['requestNumber'] = $requestNumber;
    $allInfo['responseNumber'] = $responseNumber;
    $allInfo['responseRate'] = $responseRate;
    $allInfo['effectivenessRate'] = $effectivenessRate;
    $allInfo['requestTimes'] = $requestTimes;
    $allInfo['allAccessNumber'] = $allAccessNumber;
    $allInfo['allwidjetNumber'] = $allwidjetNumber;
    $allInfo['allweffectiveness'] = $allweffectiveness;
    $allInfo['allno'] = $allno;
    $allInfo['allrequest'] = $allrequest;
    $allInfo['allresponse'] = $allresponse;
    $allInfo['allResponseRate'] = $allResponseRate;
    $allInfo['allEffectivenessRate'] = $allEffectivenessRate;
    $allInfo['averageRequestTimes'] = $averageRequestTimes;
    $Conditions = [
      'accessNumber' => $accessNumber,
      'widjetNumber' => $widjetNumber,'effectivenessNumber' => $effectivenessNumber,'requestNumber' => $requestNumber,
      'responseNumber' => $responseNumber,'responseRate' => $responseRate,'effectivenessRate' => $effectivenessRate,
      'requestTimes' => $requestTimes,'data' => $data
    ];
    $this->Session->write('allInfo',$allInfo);
    $this->Session->write('Conditions',$Conditions);
    //$this->log($effectivenessNumber,LOG_DEBUG);
    $this->log('終わり',LOG_DEBUG);
  }

  function DivTime($time,$Rate){
    $tArry=explode(":",$time);
    $hour=$tArry[0]*60;//時間→分
    $secnd=round($tArry[2]/60,2);//秒→分
    $mins=$hour+$tArry[1]+$secnd;//全て分に直して足す

    $ans= $mins*$Rate*60;//割合いを掛け算して秒に変f換
    $time = $this->s2h($ans);
    return $time;
  }

  function s2h($seconds) {
  $seconds = $seconds;
  $hours = floor($seconds / 3600);
  $minutes = floor(($seconds / 60) % 60);
  $seconds = $seconds % 60;
  $hms = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
  return $hms;
  }
}