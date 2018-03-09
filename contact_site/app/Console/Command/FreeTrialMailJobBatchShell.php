<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/02/20
 * Time: 19:07
 */

App::uses('ComponentCollection', 'Controller'); //これが大事
App::uses('MailSenderComponent', 'Controller/Component');
class FreeTrialMailJobBatchShell extends AppShell
{
  const LOG_INFO = 'batch-info';
  const LOG_ERROR = 'batch-error';

  /*必ず治す*/
  const FROM_ADDRESS = "henmi0201@gmail.com";
  const FROM_NAME = "sinclo（シンクロ）";

  public $uses = array('TSendSystemMailSchedule');


  private $component;
  /**
   * MailSenderComponent.phpの呼び出し
   * @see https://qiita.com/colorrabbit/items/d302cc0eeec3adc18456
   */
  public function startup() {
    $collection = new ComponentCollection(); //これが大事です。
    $this->component = new MailSenderComponent($collection); //コンポーネントをインスタンス化
    parent::startup();
  }

  public function sendmail() {
    $this->log('BEGIN sendmail schedule.', self::LOG_INFO);
    $now = time();
    $beginDate = date('Y-m-d H:00:00', $now);
    $endDate = date('Y-m-d H:59:59', $now);
    $this->log('TARGET schedule is '.$beginDate.' 〜 '.$endDate.' .', self::LOG_INFO);

    //ここから消す//
    //何日後の何時にメールを飛ばすか取得
    $schedules = $this->MJobMailTemplate->find('all');
    //トライアル中の企業を抜粋
    $trialCompany = $this->MCompany->find('all',[
      'conditions' => [
        'trial_flg' => 1,
        'del_flg' => 0
      ]
    ]);
    $companyIds = [];
    foreach($trialCompany as $company) {
      $companyIds[] .= $company['MCompany']['id'];
    }
    //トライアル中の企業のトライアル期間を検出
    $trialDay = $this->MAgreement->find('all',[
      'fields' => [
        'm_companies_id',
        'trial_start_day',
        'trial_end_day'
      ],
      'conditions' => [
        'm_companies_id' => $companyIds
      ]
    ]);
    $jobMailTemplatesData = [];
    $companyIds = [];
    //何日後の何時に当てはまる企業を抜粋
    foreach($schedules as $key => $val) {
        foreach($trialDay as $day) {
          if($val['MJobMailTemplate']['value_type'] == 0) {
            $trialTime = date('Y-m-d '.$val['MJobMailTemplate']['time'], strtotime('+'.$val['MJobMailTemplate']['value'].'day',strtotime($day['MAgreement']['trial_start_day'])));
          }
          if($val['MJobMailTemplate']['value_type'] == 1) {
            $trialTime = date('Y-m-d '.$val['MJobMailTemplate']['time'], strtotime('-'.$val['MJobMailTemplate']['value'].'day',strtotime($day['MAgreement']['trial_end_day'])));
          }
          $nowTime = date('Y-m-d H');
          if($trialTime == $nowTime) {
            $jobMailTemplatesData[$key]['id'] = $val['MJobMailTemplate']['id'];
            $jobMailTemplatesData[$key]['mail_type_cd'] = $val['MJobMailTemplate']['mail_type_cd'];
            $jobMailTemplatesData[$key]['sender'] = $val['MJobMailTemplate']['sender'];
            $jobMailTemplatesData[$key]['subject'] = $val['MJobMailTemplate']['subject'];
            $jobMailTemplatesData[$key]['mail_body'] = $val['MJobMailTemplate']['mail_body'];
            $jobMailTemplatesData[$key]['send_mail_ml_flg'] = $val['MJobMailTemplate']['send_mail_ml_flg'];
            $companyIds[] .= $day['MAgreement']['m_companies_id'];
          }
      }
    }
    //トライアル中の企業のユーザーのメールアドレスを検出
    $mailAdressData = $this->MUser->find('all',[
      'fields' => [
        'mail_address'
      ],
      'conditions' => [
        'm_companies_id' => $companyIds,
        'permission_level' => [1,2]
      ]
    ]);

    if(empty($mailAdressData)) {
      $this->log('schedule is not found.', self::LOG_INFO);
    } else {
      foreach($jobMailTemplatesData as $jobMailTemplate) {
        foreach($mailAdressData as $index => $mailAdress) {
          try {
            $id = $jobMailTemplate['id'];
            $to = $mailAdress['MUser']['mail_address'];
            $sender = $jobMailTemplate['sender'];
            $body = $jobMailTemplate['mail_body'];
            $subject = $jobMailTemplate['subject'];
            $this->log("Sending mail to ".$to." subject : ".$subject." JOB ID: ".$id, self::LOG_INFO);
            /*$this->component->setFrom(self::FROM_ADDRESS);
            $this->component->setFromName($sender);
            $this->component->setTo($to);
            $this->component->setBody($body);
            $this->component->setSubject($subject);
            $this->component->send();*/
            $sender2 = new MailSenderComponent();
            $sender2->setFrom(self::FROM_ADDRESS);
            $sender2->setFromName($sender);
            $sender2->setTo($to);
            $sender2->setBody($body);
            $sender2->setSubject($subject);
            $sender2->send();

          } catch(Exception $e) {
            $this->log('send mail error !!!!', self::LOG_ERROR);
          }
        }
        if($jobMailTemplate['send_mail_ml_flg'] == 0) {
          $sender2 = new MailSenderComponent();
          $sender2->setFrom(self::FROM_ADDRESS);
          $sender2->setFromName($sender);
          $sender2->setTo('yuki.henmi@medialink-ml.co.jp');
          $sender2->setBody($body);
          $sender2->setSubject($subject);
          $sender2->send();
        }
      }
    }
    $this->log('END   sendmail schedule.', self::LOG_INFO);
  }
}