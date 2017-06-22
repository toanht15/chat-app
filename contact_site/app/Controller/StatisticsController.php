<?php
/**
 * StatisticsController controller.
 * 統計機能
 */
class StatisticsController extends AppController {

  public $uses = ['THistory'];

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

  /* *
   * チャット統計
   * @return void
   * */
  public function forChat() {

    //合計アクセス件数
    $access = "SELECT count(*) FROM sinclo_db2.t_histories where t_histories.m_companies_id = ".$this->userInfo['MCompany']['id'];
    $access = $this->THistory->query($access);

    //ウィジェット表示件数
    $widjet = "SELECT count(*) FROM sinclo_db2.t_history_widget_displays where t_history_widget_displays.m_companies_id = ".$this->userInfo['MCompany']['id'];
    $widjet = $this->THistory->query($widjet);

    //チャット有効件数、チャット拒否件数
    $sql3 = "SELECT count(*),SUM(case when t_history_chat_logs.achievement_flg = 2 THEN 1 ELSE 0 END) yukou,
    SUM(case when t_history_chat_logs.message_type = 4 THEN 1 ELSE 0 END) no FROM sinclo_db2.t_histories LEFT JOIN
      sinclo_db2.t_history_chat_logs ON t_history_chat_logs.t_histories_id = t_histories.id where  t_histories.access_date
      between '2017-06-16 00:00:00' and '2017-06-16 23:59:59' and (t_history_chat_logs.achievement_flg = 2 or t_history_chat_logs.message_type = 4)
      and t_histories.m_companies_id = ".$this->userInfo['MCompany']['id'];
    $return3 = $this->THistory->query($sql3);

    //チャットリクエスト件数
    $sql3 = "SELECT count(*) FROM sinclo_db2.t_histories LEFT JOIN (SELECT * FROM sinclo_db2.t_history_chat_logs where t_history_chat_logs.message_request_flg = 1) as t_history_chat_logs ON t_history_chat_logs.t_histories_id = t_histories.id where t_histories.access_date between '2017-06-14 00:00:00' and '2017-06-14 09:24:07' and t_histories.m_companies_id = ".$this->userInfo['MCompany']['id'];
    $return4 = $this->THistory->query($sql3);

    //チャット応対件数
    $response = "SELECT count(*) FROM sinclo_db2.t_histories LEFT JOIN (SELECT * FROM sinclo_db2.t_history_chat_logs where
    t_history_chat_logs.message_type = 98) as t_history_chat_logs ON t_history_chat_logs.t_histories_id = t_histories.id where
    t_histories.access_date between '2017-06-16 00:00:00' and '2017-06-16 23:59:59' and t_histories.m_companies_id = ".$this->userInfo['MCompany']['id'];
    $response = $this->THistory->query($response);

    //チャットリクエスト時間
    $sql2 = "SELECT * FROM sinclo_db2.t_histories LEFT JOIN t_history_chat_logs ON t_history_chat_logs.t_histories_id = t_histories.id where t_histories.access_date between '2017-06-16 00:00:00' and '2017-06-16 23:59:59' and t_history_chat_logs.message_request_flg = 1 and t_histories.m_companies_id = ".$this->userInfo['MCompany']['id']." group by t_histories_id";
    $return4 = $this->THistory->query($sql2);

    $return5 = '00:00:00';
    foreach($return4 as $k => $v) {
    $start = new DateTime($v['t_history_chat_logs']['created']);
    $end = new DateTime($v['t_histories']['access_date']);
    $diff = $start->diff($end);
    $return4 = $diff->format('%H:%I:%S');
    $return5 = explode(":", $return5);
    $return4 = explode(":", $return4);
    $return5 = date("H:i:s", mktime($return5[0] + $return4[0], $return5[1] + $return4[1], $return5[2] + $return4[2]));
    }
    $return5 = $this->DivTime($return5,1/($k+1));

    //平均入室時間
    $sql6 = "SELECT * FROM sinclo_db2.t_histories LEFT JOIN t_history_chat_logs ON t_history_chat_logs.t_histories_id = t_histories.id where t_histories.access_date between '2017-06-16 00:00:00' and '2017-06-16 23:59:59' and t_history_chat_logs.message_type = 98 and t_histories.m_companies_id = ".$this->userInfo['MCompany']['id']." group by message_distinction,t_histories_id ";
    $sql7 = "SELECT * FROM sinclo_db2.t_histories LEFT JOIN t_history_chat_logs ON t_history_chat_logs.t_histories_id = t_histories.id where t_histories.access_date between '2017-06-16 00:00:00' and '2017-06-16 23:59:59' and t_history_chat_logs.message_request_flg = 1 and t_histories.m_companies_id = ".$this->userInfo['MCompany']['id']." group by t_histories_id";
    $return6 = $this->THistory->query($sql6);
    $return7 = $this->THistory->query($sql7);
    $return5 = '00:00:00';
    foreach($return6 as $k => $v) {
    }
    for ($x = 0;$x < $k+1; $x++){
        $start = new DateTime($return6[$x]['t_history_chat_logs']['created']);
        $end = new DateTime($return7[$x]['t_history_chat_logs']['created']);
        $diff = $start->diff($end);
        $return4 = $diff->format('%H:%I:%S');
        $return5 = explode(":", $return5);
        $return4 = explode(":", $return4);
        $return5 = date("H:i:s", mktime($return5[0] + $return4[0], $return5[1] + $return4[1], $return5[2] + $return4[2]));
      }
    $return5 = $this->DivTime($return5,1/($k+1));

    //平均応答時間
    $sql8 = "SELECT * FROM sinclo_db2.t_histories LEFT JOIN t_history_chat_logs ON t_history_chat_logs.t_histories_id = t_histories.id where t_histories.access_date between '2017-06-16 00:00:00' and '2017-06-16 23:59:59' and t_history_chat_logs.message_request_flg = 1 and t_histories.m_companies_id = ".$this->userInfo['MCompany']['id']." group by t_histories_id";
    $sql9 = "SELECT * FROM sinclo_db2.t_histories LEFT JOIN t_history_chat_logs ON t_history_chat_logs.t_histories_id = t_histories.id where t_histories.access_date between '2017-06-16 00:00:00' and '2017-06-16 23:59:59' and t_history_chat_logs.message_type = 2 and t_histories.m_companies_id = ".$this->userInfo['MCompany']['id']." group by message_distinction,t_histories_id";
    $return8 = $this->THistory->query($sql8);
    $return9 = $this->THistory->query($sql9);
    //pr($return9); exit();
    $return5 = '00:00:00';
    foreach($return8 as $k => $v) {
    }
    for ($x = 0;$x < $k+1; $x++){
        $start = new DateTime($return8[$x]['t_history_chat_logs']['created']);
        $end = new DateTime($return9[$x]['t_history_chat_logs']['created']);
        $diff = $start->diff($end);
        $return4 = $diff->format('%H:%I:%S');
        $return5 = explode(":", $return5);
        $return4 = explode(":", $return4);
        $return5 = date("H:i:s", mktime($return5[0] + $return4[0], $return5[1] + $return4[1], $return5[2] + $return4[2]));
      }
    $return5 = $this->DivTime($return5,1/($k+1));
    //pr($return5); exit();
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
