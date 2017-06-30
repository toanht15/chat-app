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
    $this->set('allInfo',$allInfo);
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
      $access = "SELECT count(*) FROM sinclo_db2.t_histories where t_histories.access_date
      between '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$endMonth)." 23:59:59' and t_histories.m_companies_id = ".$this->userInfo['MCompany']['id'];
      $accessNumber[] = $this->THistory->query($access);
      $this->log('access',LOG_DEBUG);
      //ウィジェット表示件数
      $widjet = "SELECT count(*) FROM sinclo_db2.t_history_widget_displays where t_history_widget_displays.created between
       '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$endMonth)." 23:59:59' and t_history_widget_displays.m_companies_id = ".$this->userInfo['MCompany']['id'];
      $widjetNumber[] = $this->THistoryWidgetDisplays->query($widjet);
      $this->log('widjet',LOG_DEBUG);
      //チャット有効件数、チャット拒否件数
      $this->log('effectivenessStart',LOG_DEBUG);
      $effectiveness = "SELECT count(*),SUM(case when t_history_chat_logs.achievement_flg = 2 THEN 1 ELSE 0 END) yukou,
      SUM(case when t_history_chat_logs.message_type = 4 THEN 1 ELSE 0 END) no FROM sinclo_db2.t_histories LEFT JOIN
      sinclo_db2.t_history_chat_logs ON t_history_chat_logs.t_histories_id = t_histories.id where  t_histories.access_date between
       '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$endMonth)." 23:59:59' and (t_history_chat_logs.achievement_flg = 2 or t_history_chat_logs.message_type = 4)
      and t_histories.m_companies_id = ".$this->userInfo['MCompany']['id'];
      $effectiveness = $this->THistory->query($effectiveness);
      if($effectiveness[0][0]['count(*)'] == 0){
        $effectiveness[0][0]['yukou'] = 0;
        $effectiveness[0][0]['no'] = 0;
      }
      $effectivenessNumber[] = $effectiveness;
      $this->log('effectivenessFinish',LOG_DEBUG);
      //チャットリクエスト件数
      $this->log('requestStart',LOG_DEBUG);
      $this->log('milli秒',date("H:i:s"));
      $request = "SELECT count(th.id) FROM sinclo_db2.t_histories as th LEFT JOIN (SELECT t_histories_id,message_request_flg FROM sinclo_db2.t_history_chat_logs where message_request_flg=1 )
      as t_history_chat_logs ON t_history_chat_logs.t_histories_id = th.id where th.access_date between '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$endMonth)." 23:59:59'
       and t_history_chat_logs.message_request_flg = 1 and th.m_companies_id = ".$this->userInfo['MCompany']['id'];
      $requestNumber[] = $this->THistory->query($request);
      $this->log('milli秒',date("H:i:s"));
      $this->log('requestFinish',LOG_DEBUG);
      //チャット応対件数
      $this->log(microtime(true),LOG_DEBUG);
      $this->log('responseStart',LOG_DEBUG);
     //チャット応対件数
      $response = "SELECT count(distinct message_distinction,t_histories_id)  FROM sinclo_db2.t_histories LEFT JOIN (SELECT * FROM sinclo_db2.t_history_chat_logs where message_type = ?) as t_history_chat_logs ON t_history_chat_logs.t_histories_id = t_histories.id where t_history_chat_logs.message_type = ? and
      t_histories.access_date between '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$startDate)." 23:59:59' and t_histories.m_companies_id = ?";
      $responseNumber[] = $this->THistory->query($response, array(98,98,$this->userInfo['MCompany']['id']));
      $this->log('response',LOG_DEBUG);
      $this->log(microtime(true),LOG_DEBUG);

       //チャット応対率
      $responseRate[] =round($this->THistory->query($response, array(98,98,$this->userInfo['MCompany']['id']))[0][0]['count(distinct message_distinction,t_histories_id)']/$this->THistory->query($request)[0][0]['count(*)']*100);

      //チャット有効率
      $effectivenessRate[] = round($effectiveness[0][0]['yukou']/$this->THistory->query($request)[0][0]['count(*)']*100);

      //チャットリクエスト時間
      $requestTime = "SELECT * FROM sinclo_db2.t_histories LEFT JOIN t_history_chat_logs ON t_history_chat_logs.t_histories_id = t_histories.id
      where t_histories.access_date between '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$endMonth)." 23:59:59' and
      t_history_chat_logs.message_request_flg = 1 and t_histories.m_companies_id = ".$this->userInfo['MCompany']['id']." group by t_histories_id";
      $return4 = $this->THistory->query($requestTime);
      $return5 = '00:00:00';
      foreach($return4 as $k => $v) {
      $start2 = new DateTime($v['t_history_chat_logs']['created']);
      $end2 = new DateTime($v['t_histories']['access_date']);
      $diff = $start2->diff($end2);
      $return4 = $diff->format('%H:%I:%S');
      $return5 = explode(":", $return5);
      $return4 = explode(":", $return4);
      $return5 = date("H:i:s", mktime($return5[0] + $return4[0], $return5[1] + $return4[1], $return5[2] + $return4[2]));
      }
      $requestTimes = $this->DivTime($return5,1/($k+1));
      $this->log('requestTimes',LOG_DEBUG);

      //平均消費者待機時間
      $sql6 = "SELECT * FROM sinclo_db2.t_histories LEFT JOIN (SELECT * FROM sinclo_db2.t_history_chat_logs where message_request_flg = 1
      group by t_histories_id) as s1 ON t_histories.id = s1.t_histories_id LEFT JOIN
      (SELECT * FROM sinclo_db2.t_history_chat_logs where message_type = 98 group by t_histories_id) as s2 ON t_histories.id = s2.t_histories_id
      where t_histories.access_date between '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$endMonth)." 23:59:59' and
      t_histories.m_companies_id = ".$this->userInfo['MCompany']['id'];
      $nyuusituTime = $this->THistory->query($sql6);
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
      $sql7 = "SELECT * FROM sinclo_db2.t_histories LEFT JOIN (SELECT * FROM sinclo_db2.t_history_chat_logs where message_request_flg = 1
      group by t_histories_id) as s1 ON t_histories.id = s1.t_histories_id LEFT JOIN
      (SELECT * FROM sinclo_db2.t_history_chat_logs where message_type = 2 group by t_histories_id) as s2 ON t_histories.id = s2.t_histories_id
      where t_histories.access_date between '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$endMonth)." 23:59:59' and
      t_histories.m_companies_id = ".$this->userInfo['MCompany']['id'];
      $outouTime = $this->THistory->query($sql7);
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

    $this->Session->write('allInfo',$allInfo);
    //$this->log($allInfo,LOG_DEBUG);
    $this->log('終わり',LOG_DEBUG);
  }

  public function _monthCalculation($data){
    $this->log('日入ってるよ？',LOG_DEBUG);
    $accessNumber = [];
    $widjetNumber = [];
    $effectivenessNumber = [];
    $requestNumber = [];
    $responseNumber = [];
    $requestTimes = [];
    $responseTimes = [];
    $effectivenessRate = [];
    $responseRate = [];
    $outouTimes = [];

    $startDate = strtotime('first day of' .$data);
    $endDate = strtotime('last day of' .$data);
    while($startDate <= $endDate) {
      $this->log('accessStart',LOG_DEBUG);
      //合計アクセス件数
      $access = "SELECT count(th.id) FROM sinclo_db2.t_histories as th where th.access_date
      between '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$startDate)." 23:59:59' and th.m_companies_id = ".$this->userInfo['MCompany']['id'];
      $accessNumber[] = $this->THistory->query($access);
      $this->log('accessFinish',LOG_DEBUG);
      //$this->log($accessNumber,LOG_DEBUG);
      $this->log('wijetStart',LOG_DEBUG);
      //ウィジェット表示件数
      $widjet = "SELECT count(tw.id) FROM sinclo_db2.t_history_widget_displays as tw where tw.created between
       '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$startDate)." 23:59:59' and tw.m_companies_id = ".$this->userInfo['MCompany']['id'];
      $widjetNumber[] = $this->THistoryWidgetDisplays->query($widjet);
      $this->log('wijetStartFinish',LOG_DEBUG);
      $this->log('yuukouStart',LOG_DEBUG);
       //チャット有効件数、チャット拒否件数
      $effectiveness = "SELECT count(th.id),SUM(case when t_history_chat_logs.achievement_flg = 2 THEN 1 ELSE 0 END) yukou,
      SUM(case when t_history_chat_logs.message_type = 4 THEN 1 ELSE 0 END) no FROM sinclo_db2.t_histories as th LEFT JOIN
      sinclo_db2.t_history_chat_logs ON t_history_chat_logs.t_histories_id = th.id where  th.access_date between
       '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$startDate)." 23:59:59' and (t_history_chat_logs.achievement_flg = 2 or t_history_chat_logs.message_type = 4)
      and th.m_companies_id = ".$this->userInfo['MCompany']['id'];
      $effectiveness = $this->THistory->query($effectiveness);
      if($effectiveness[0][0]['count(*)'] == 0) {
        $effectiveness[0][0]['yukou'] = 0;
        $effectiveness[0][0]['no'] = 0;
      }
      $effectivenessNumber[] = $effectiveness;
      $this->log('yuukouFinish',LOG_DEBUG);
      $this->log('requestStart',LOG_DEBUG);
      //チャットリクエスト件数
      $request = "SELECT count(th.id) FROM sinclo_db2.t_histories as th LEFT JOIN (SELECT t_histories_id,message_request_flg FROM
      sinclo_db2.t_history_chat_logs where message_request_flg = ? ) as t_history_chat_logs ON t_history_chat_logs.t_histories_id = th.id where
      th.access_date between '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$startDate)." 23:59:59' and
      t_history_chat_logs.message_request_flg = ? and th.m_companies_id = ?";
      $requestNumber[] = $this->THistory->query($request, array(1,1,$this->userInfo['MCompany']['id']));
      $this->log('requestFinish',LOG_DEBUG);

      $this->log('responseStart',LOG_DEBUG);
     //チャット応対件数
      $response = "SELECT count(distinct message_distinction,t_histories_id)  FROM sinclo_db2.t_histories as th LEFT JOIN
      (SELECT t_histories_id,message_type,message_distinction FROM sinclo_db2.t_history_chat_logs where message_type = ?) as t_history_chat_logs ON
      t_history_chat_logs.t_histories_id = th.id where t_history_chat_logs.message_type = ? and
      th.access_date between '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$startDate)." 23:59:59' and th.m_companies_id = ?";
      $responseNumber[] = $this->THistory->query($response, array(98,98,$this->userInfo['MCompany']['id']));
      $this->log('responseFinish',LOG_DEBUG);
      $this->log('outairituStart',LOG_DEBUG);
      //チャット応対率
      $responseRate[] =round($this->THistory->query($response, array(98,98,$this->userInfo['MCompany']['id']))[0][0]['count(distinct message_distinction,t_histories_id)']/$this->THistory->query($request, array(1,1,$this->userInfo['MCompany']['id']))[0][0]['count(th.id)']*100);
      $this->log('outairituFinsih',LOG_DEBUG);
      $this->log('yuukourituStart',LOG_DEBUG);
      //チャット有効率
      $effectivenessRate[] = round($effectiveness[0][0]['yukou']/$this->THistory->query($request, array(1,1,$this->userInfo['MCompany']['id']))[0][0]['count(th.id)']*100);
      $this->log('yuukourituFinsish',LOG_DEBUG);
      //$this->log('requestTime',LOG_DEBUG);
      $this->log('responseTimeStart',LOG_DEBUG);
      $this->log('データ収集',LOG_DEBUG);
      //平均チャットリクエスト時間
      $sql2 = "SELECT th.id,th.m_companies_id,th.access_date,t_history_chat_logs.t_histories_id,t_history_chat_logs.created,
      t_history_chat_logs.message_request_flg FROM sinclo_db2.t_histories as th LEFT JOIN (SELECT t_histories_id,message_request_flg,created
      FROM sinclo_db2.t_history_chat_logs where message_request_flg = ?) as t_history_chat_logs ON t_history_chat_logs.t_histories_id = th.id where th.access_date between
      '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$startDate)." 23:59:59' and t_history_chat_logs.message_request_flg = ?
      and th.m_companies_id = ? group by t_histories_id";
      $return4 = $this->THistory->query($sql2, array(1,1,$this->userInfo['MCompany']['id']));
      $this->log('計算スタート',LOG_DEBUG);
      $return5 = '00:00:00';
      $this->log(date("Y-m-d",$startDate),LOG_DEBUG);
      foreach($return4 as $k => $v) {
      $startDates = new DateTime($v['t_histories']['access_date']);
      $endDates =new DateTime($v['t_history_chat_logs']['created']);
      $diff = $startDates->diff($endDates);
      $return4 = $diff->format('%H:%I:%S');
      $return5 = explode(":", $return5);
      $this->log($return5,LOG_DEBUG);
      $return4 = explode(":", $return4);
      $this->log($return4,LOG_DEBUG);
      $return5 = date("H:i:s", mktime($return5[0] + $return4[0], $return5[1] + $return4[1], $return5[2] + $return4[2]));
      }
      $this->log('計算終了',LOG_DEBUG);
      $this->log($return5,LOG_DEBUG);
      $requestTimes[] = $this->DivTime($return5,1/($k+1));
      $this->log('responseTimeFinish',LOG_DEBUG);
      //平均消費者待機時間
      /*$sql6 = "SELECT th.id,th.access_date,th.m_companies_id,s1.t_histories_id,s1.message_request_flg,s1.created,s2.t_histories_id,s2.message_type,s2.created
      FROM sinclo_db2.t_histories as th LEFT JOIN (SELECT * FROM sinclo_db2.t_history_chat_logs where message_request_flg = 1 group by t_histories_id)
      as s1 ON th.id = s1.t_histories_id LEFT JOIN (SELECT * FROM sinclo_db2.t_history_chat_logs where message_type = 98 group by t_histories_id) as s2
      ON th.id = s2.t_histories_id where th.access_date between '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$startDate)." 23:59:59' and
      th.m_companies_id = ".$this->userInfo['MCompany']['id'];
      $nyuusituTime = $this->THistory->query($sql6);
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
      $responseTimes[] = $this->DivTime($return5,1/($k+1));*/
      //平均応答時間
      /*$sql7 = "SELECT * FROM sinclo_db2.t_histories LEFT JOIN (SELECT * FROM sinclo_db2.t_history_chat_logs where message_request_flg = 1
      group by t_histories_id) as s1 ON t_histories.id = s1.t_histories_id LEFT JOIN
      (SELECT * FROM sinclo_db2.t_history_chat_logs where message_type = 2 group by t_histories_id) as s2 ON t_histories.id = s2.t_histories_id
      where t_histories.access_date between '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$startDate)." 23:59:59' and
      t_histories.m_companies_id = ".$this->userInfo['MCompany']['id'];
      $outouTime = $this->THistory->query($sql7);
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
      $outouTimes[] = $this->DivTime($return5,1/($k+1));*/
      $startDate = strtotime("+1 day", $startDate);
    }
    $this->log('合計件数',LOG_DEBUG);
    $this->log($requestTimes,LOG_DEBUG);
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
    //$this->log($allInfo,LOG_DEBUG);
    $this->Session->write('allInfo',$allInfo);
    $this->log('終わり',LOG_DEBUG);
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
      $access = "SELECT count(*) FROM sinclo_db2.t_histories where t_histories.access_date
      between '".date("Y-m-d H",$startDate).":00:00' and '".date("Y-m-d H",$startDate).":59:59' and t_histories.m_companies_id = ".$this->userInfo['MCompany']['id'];
      $accessNumber[] = $this->THistory->query($access);
      //ウィジェット表示件数
      $widjet = "SELECT count(*) FROM sinclo_db2.t_history_widget_displays where t_history_widget_displays.created between
       '".date("Y-m-d H",$startDate).":00:00' and '".date("Y-m-d H",$startDate).":59:59' and t_history_widget_displays.m_companies_id = ".$this->userInfo['MCompany']['id'];
      $widjetNumber[] = $this->THistoryWidgetDisplays->query($widjet);
      //チャット有効件数、チャット拒否件数
      $effectiveness = "SELECT count(*),SUM(case when t_history_chat_logs.achievement_flg = 2 THEN 1 ELSE 0 END) yukou,
      SUM(case when t_history_chat_logs.message_type = 4 THEN 1 ELSE 0 END) no FROM sinclo_db2.t_histories LEFT JOIN
      sinclo_db2.t_history_chat_logs ON t_history_chat_logs.t_histories_id = t_histories.id where  t_histories.access_date between
       '".date("Y-m-d H",$startDate).":00:00' and '".date("Y-m-d H",$startDate).":59:59' and (t_history_chat_logs.achievement_flg = 2 or t_history_chat_logs.message_type = 4)
      and t_histories.m_companies_id = ".$this->userInfo['MCompany']['id'];
      $effectiveness = $this->THistory->query($effectiveness);
      if($effectiveness[0][0]['count(*)'] == 0){
        $effectiveness[0][0]['yukou'] = 0;
        $effectiveness[0][0]['no'] = 0;
      }
      $effectivenessNumber[] = $effectiveness;
      //チャットリクエスト件数
      $request = "SELECT count(*) FROM sinclo_db2.t_histories LEFT JOIN (SELECT * FROM sinclo_db2.t_history_chat_logs ) as t_history_chat_logs ON t_history_chat_logs.t_histories_id = t_histories.id
       where t_histories.access_date between '".date("Y-m-d H",$startDate).":00:00' and '".date("Y-m-d H",$startDate).":59:59' and
       t_history_chat_logs.message_request_flg = 1 and t_histories.m_companies_id = ".$this->userInfo['MCompany']['id'];
      $requestNumber[] = $this->THistory->query($request);
     //チャット応対件数
      $response = "SELECT count(distinct message_distinction,t_histories_id)  FROM sinclo_db2.t_histories LEFT JOIN (SELECT * FROM sinclo_db2.t_history_chat_logs where message_type = ?) as t_history_chat_logs ON t_history_chat_logs.t_histories_id = t_histories.id where t_history_chat_logs.message_type = ? and
      t_histories.access_date between '".date("Y-m-d",$startDate)." 00:00:00' and '".date("Y-m-d",$startDate)." 23:59:59' and t_histories.m_companies_id = ?";
      $responseNumber[] = $this->THistory->query($response, array(98,98,$this->userInfo['MCompany']['id']));

      //チャット応対率
      $responseRate[] =round($this->THistory->query($response, array(98,98,$this->userInfo['MCompany']['id']))[0][0]['count(distinct message_distinction,t_histories_id)']/$this->THistory->query($request)[0][0]['count(*)']*100);

      //チャット有効率
      $effectivenessRate[] = $effectiveness[0][0]['yukou']/$this->THistory->query($request)[0][0]['count(*)']*100;

      //平均チャットリクエスト時間
      $sql2 = "SELECT * FROM sinclo_db2.t_histories LEFT JOIN t_history_chat_logs ON t_history_chat_logs.t_histories_id = t_histories.id
      where t_histories.access_date between '".date("Y-m-d H",$startDate).":00:00' and '".date("Y-m-d H",$startDate).":59:59' and
      t_history_chat_logs.message_request_flg = ? and t_histories.m_companies_id = ? group by t_histories_id";
      $return4 = $this->THistory->query($sql2, array(1,$this->userInfo['MCompany']['id']));
      $return5 = '00:00:00';
      foreach($return4 as $k => $v) {
        $start2 = new DateTime($v['t_history_chat_logs']['created']);
        $end2 = new DateTime($v['t_histories']['access_date']);
        $diff = $start2->diff($end2);
        $return4 = $diff->format('%H:%I:%S');
        $return5 = explode(":", $return5);
        $return4 = explode(":", $return4);
        $return5 = date("H:i:s", mktime($return5[0] + $return4[0], $return5[1] + $return4[1], $return5[2] + $return4[2]));
        }
      $requestTimes[] = $this->DivTime($return5,1/($k+1));
      //平均消費者待機時間
      $sql6 = "SELECT * FROM sinclo_db2.t_histories LEFT JOIN (SELECT * FROM sinclo_db2.t_history_chat_logs where message_request_flg = 1
      group by t_histories_id) as s1 ON t_histories.id = s1.t_histories_id LEFT JOIN
      (SELECT * FROM sinclo_db2.t_history_chat_logs where message_type = 98 group by t_histories_id) as s2 ON t_histories.id = s2.t_histories_id
      where t_histories.access_date between '".date("Y-m-d H",$startDate).":00:00' and '".date("Y-m-d H",$startDate).":59:59' and
      t_histories.m_companies_id = ".$this->userInfo['MCompany']['id'];
      $nyuusituTime = $this->THistory->query($sql6);
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
      $sql7 = "SELECT * FROM sinclo_db2.t_histories LEFT JOIN (SELECT * FROM sinclo_db2.t_history_chat_logs where message_request_flg = 1
      group by t_histories_id) as s1 ON t_histories.id = s1.t_histories_id LEFT JOIN
      (SELECT * FROM sinclo_db2.t_history_chat_logs where message_type = 2 group by t_histories_id) as s2 ON t_histories.id = s2.t_histories_id
      where t_histories.access_date between '".date("Y-m-d H",$startDate).":00:00' and '".date("Y-m-d H",$startDate).":59:59' and
      t_histories.m_companies_id = ".$this->userInfo['MCompany']['id'];
      $outouTime = $this->THistory->query($sql7);
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

    //合計チャット有効率
    $allEffectivenessRate = $allweffectiveness/$allrequest*100;

    //合計リクエスト平均時間
    $time = '00:00:00';
    foreach($requestTimes as $k => $v) {
    $time = explode(":", $time);
    $averageTime = explode(":", $v);
    $averageTimes = date("H:i:s", mktime($time[0] + $averageTime[0], $time[1] + $averageTime[1], $time[2] + $averageTime[2]));
    }
    $averageRequestTimes = $this->DivTime($averageTimes,1/($k+1));

    $this->log($averageRequestTimes,LOG_DEBUG);

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
    $this->Session->write('allInfo',$allInfo);
    //$this->log($effectivenessNumber,LOG_DEBUG);
    $this->log('終わり',LOG_DEBUG);
  }

  function DivTime($time,$Rate){
    $tArry=explode(":",$time);
    $hour=$tArry[0]*60;//時間→分
    $secnd=round($tArry[2]/60,2);//秒→分
    $mins=$hour+$tArry[1]+$secnd;//全て分に直して足す

    $ans= $mins*$Rate*60;//割合いを掛け算して秒に変換
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