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

  const ML_MAIL_ADDRESS= "cloud-service@medialink-ml.co.jp";

  const COMPANY_NAME = "##COMPANY_NAME##";
  const USER_NAME = "##USER_NAME##";
  const PASSWORD = "##PASSWORD##";
  const BUSINESS_MODEL = "##BUSINESS_MODEL##";
  const DEPARTMENT = "##DEPARTMENT##";
  const POSITION = "##POSITION##";
  const MAIL_ADDRESS = "##MAIL_ADDRESS##";
  const PHONE_NUMBER = "##PHONE_NUMBER##";
  const URL = "##URL##";
  const OTHER = "##OTHER##";

  public $uses = ['MUser','TMailTransmissionLog','MSystemMailTemplate','TSendSystemMailSchedule','MJobMailTemplate','MCompany','MAgreement'];


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
    //全企業取得
    $trialCompany = $this->MCompany->find('all',[
      'conditions' => [
        'del_flg' => 0
      ]
    ]);
    $trialCompanyIds = [];
    $companyIds = [];
    foreach($trialCompany as $company) {
      //トライアル企業だけ抜粋
      if($company['MCompany']['trial_flg'] == 1) {
        $trialCompanyIds[] .= $company['MCompany']['id'];
      }
      //契約中の企業だけ抜粋
      if($company['MCompany']['trial_flg'] == 0) {
        $companyIds[] .= $company['MCompany']['id'];
      }
    }
    //トライアル中の企業のトライアル期間を検出
    $trialDay = $this->MAgreement->find('all',[
      'fields' => [
        'm_companies_id',
        'application_day',
        'trial_end_day'
      ],
      'conditions' => [
        'm_companies_id' => $trialCompanyIds
      ]
    ]);
    //本契約中の企業の期間を検出
    $agreementDay = $this->MAgreement->find('all',[
      'fields' => [
        'm_companies_id',
        'application_day',
        'agreement_end_day'
      ],
      'conditions' => [
        'm_companies_id' => $companyIds
      ]
    ]);

    $trialJobMailTemplatesData = [];
    $jobMailTemplatesData = [];
    $trialCompanyIds = [];
    $companyIds = [];
    $this->log('BEGIN sendmail schedule2.', self::LOG_INFO);
    //何日後の何時に当てはまる企業を抜粋
    foreach($schedules as $key => $val) {
        foreach($trialDay as $trialNumber => $trial) {
          //何日後の日付、時間
          if($val['MJobMailTemplate']['value_type'] == 0) {
            $trialTime = date('Y-m-d '.$val['MJobMailTemplate']['time'], strtotime('+'.$val['MJobMailTemplate']['value'].'day',strtotime($trial['MAgreement']['application_day'])));
          }
          //何日前の日付、時間
          if($val['MJobMailTemplate']['value_type'] == 1) {
            $trialTime = date('Y-m-d '.$val['MJobMailTemplate']['time'], strtotime('-'.$val['MJobMailTemplate']['value'].'day',strtotime($trial['MAgreement']['trial_end_day'])));
          }
          $nowTime = date('Y-m-d H');
          $this->log('BEGIN sendmail schedule3.', self::LOG_INFO);
          //現在の時刻と比較(無料トライアルの場合)
          if($trialTime == $nowTime && $val['MJobMailTemplate']['agreement_flg'] == 1) {
            $trialJobMailTemplatesData[$trialNumber]['id'] = $val['MJobMailTemplate']['id'];
            $trialJobMailTemplatesData[$trialNumber]['mail_type_cd'] = $val['MJobMailTemplate']['mail_type_cd'];
            $trialJobMailTemplatesData[$trialNumber]['sender'] = $val['MJobMailTemplate']['sender'];
            $trialJobMailTemplatesData[$trialNumber]['subject'] = $val['MJobMailTemplate']['subject'];
            $trialJobMailTemplatesData[$trialNumber]['mail_body'] = $val['MJobMailTemplate']['mail_body'];
            $trialJobMailTemplatesData[$trialNumber]['send_mail_ml_flg'] = $val['MJobMailTemplate']['send_mail_ml_flg'];
            $trialJobMailTemplatesData[$trialNumber]['m_companies_id'] = $trial['MAgreement']['m_companies_id'];
            $trialCompanyIds[] .= $trial['MAgreement']['m_companies_id'];
          }
        }
        foreach($agreementDay as $agreementNumber => $agreement) {
          if($val['MJobMailTemplate']['value_type'] == 0) {
            //何日後の日付、時間
            $agreementTime = date('Y-m-d '.$val['MJobMailTemplate']['time'], strtotime('+'.$val['MJobMailTemplate']['value'].'day',strtotime($agreement['MAgreement']['application_day'])));
          }
          if($val['MJobMailTemplate']['value_type'] == 1) {
            //何日前の日付、時間
            $agreementTime = date('Y-m-d '.$val['MJobMailTemplate']['time'], strtotime('-'.$val['MJobMailTemplate']['value'].'day',strtotime($agreement['MAgreement']['agreement_end_day'])));
          }
          $nowTime = date('Y-m-d H');
          //現在の時刻と比較(いきなり本契約の場合)
          if($agreementTime == $nowTime && $val['MJobMailTemplate']['agreement_flg'] == 2) {
            $jobMailTemplatesData[$agreementNumber]['id'] = $val['MJobMailTemplate']['id'];
            $jobMailTemplatesData[$agreementNumber]['mail_type_cd'] = $val['MJobMailTemplate']['mail_type_cd'];
            $jobMailTemplatesData[$agreementNumber]['sender'] = $val['MJobMailTemplate']['sender'];
            $jobMailTemplatesData[$agreementNumber]['subject'] = $val['MJobMailTemplate']['subject'];
            $jobMailTemplatesData[$agreementNumber]['mail_body'] = $val['MJobMailTemplate']['mail_body'];
            $jobMailTemplatesData[$agreementNumber]['send_mail_ml_flg'] = $val['MJobMailTemplate']['send_mail_ml_flg'];
            $jobMailTemplatesData[$agreementNumber]['m_companies_id'] = $agreement['MAgreement']['m_companies_id'];
            $this->log($jobMailTemplatesData,LOG_DEBUG);
            $companyIds[] = $agreement['MAgreement']['m_companies_id'];
          }
      }
    }
    //トライアル中の企業のユーザーのメールアドレスを検出
    $trialMailAdressData = $this->MUser->find('all',[
      'fields' => [
        'm_companies_id',
        'user_name',
        'mail_address'
      ],
      'conditions' => [
        'm_companies_id' => $trialCompanyIds,
        'permission_level' => [1,2]
      ]
    ]);

    //本契約中の企業のユーザーのメールアドレスを検出
    $mailAdressData = $this->MUser->find('all',[
      'fields' => [
        'm_companies_id',
        'user_name',
        'mail_address'
      ],
      'conditions' => [
        'm_companies_id' => $companyIds,
        'permission_level' => [1,2]
      ]
    ]);
    if(empty($trialMailAdressData)) {
      $this->log('trialSchedule is not found.', self::LOG_INFO);
    } else {
      $this->log('BEGIN sendmail schedule4.', self::LOG_INFO);
      foreach($trialJobMailTemplatesData as $key => $jobMailTemplate) {
        foreach($trialMailAdressData as $index => $mailAdress) {
          try {
            if($mailAdress['MUser']['m_companies_id'] == $jobMailTemplate['m_companies_id']) {
              //m_companies_idが変わるごとに会社名取得
              if((!empty($trialMailAdressData[$index-1]) && $trialMailAdressData[$index-1]['MUser']['m_companies_id'] != $mailAdress['MUser']['m_companies_id'] && $index != 0) ||
              $index == 0) {
                $trialCompanyName = $this->MCompany->find('all',[
                  'fields' => [
                    'company_name'
                  ],
                  'conditions' => [
                    'id' => $mailAdress['MUser']['m_companies_id']
                  ]
                ]);
                $trialCompanyName = $trialCompanyName[0]['MCompany']['company_name'];
                if($index == 0) {
                  $trialCompanyNames = $trialCompanyName;
                }
                else {
                  $trialCompanyNames .= ','.$trialCompanyName;
                }
              }
              $id = $jobMailTemplate['id'];
              $to = $mailAdress['MUser']['mail_address'];
              $sender = $jobMailTemplate['sender'];
              $jobMailTemplate['mail_body'] = str_replace(self::COMPANY_NAME, $trialCompanyName, $jobMailTemplate['mail_body']);
              $jobMailTemplate['mail_body'] = str_replace(self::USER_NAME, $mailAdress['MUser']['user_name'], $jobMailTemplate['mail_body']);
              $body = $jobMailTemplate['mail_body'];
              $subject = $jobMailTemplate['subject'];
              $this->log("Sending mail to ".$to." subject : ".$subject." JOB ID: ".$id, self::LOG_INFO);
              $this->component->setFrom(self::ML_MAIL_ADDRESS);
              $this->component->setFromName($sender);
              $this->component->setTo($to);
              $this->component->setBody($body);
              $this->component->setSubject($subject);
              $this->component->send();
              $jobMailTemplate['mail_body'] = str_replace($trialCompanyName, self::COMPANY_NAME, $jobMailTemplate['mail_body']);
              $jobMailTemplate['mail_body'] = str_replace($mailAdress['MUser']['user_name'], self::USER_NAME, $jobMailTemplate['mail_body']);
            }
          } catch(Exception $e) {
            $this->log('send mail trial error !!!!', self::LOG_ERROR);
          }
        }
        if($jobMailTemplate['send_mail_ml_flg'] == 0 && $jobMailTemplate === end($trialJobMailTemplatesData)) {
          $this->component->setFrom(self::ML_MAIL_ADDRESS);
          $this->component->setFromName($jobMailTemplate['sender']);
          $this->component->setTo(self::ML_MAIL_ADDRESS);
          //送信した会社名をMLに送る
          $jobMailTemplate['mail_body'] = str_replace(self::COMPANY_NAME, $trialCompanyNames, $jobMailTemplate['mail_body']);
          $jobMailTemplate['mail_body'] = str_replace(self::USER_NAME, "", $jobMailTemplate['mail_body']);
          $body = $jobMailTemplate['mail_body'];
          $this->component->setBody($body);
          $this->component->setSubject($jobMailTemplate['subject']);
          $this->component->send();
        }
      }
    }

    if(empty($mailAdressData)) {
      $this->log('schedule is not found.', self::LOG_INFO);
    } else {
      foreach($jobMailTemplatesData as $key => $jobMailTemplate) {
        foreach($mailAdressData as $index => $mailAdress) {
          try {
            if($mailAdress['MUser']['m_companies_id'] == $jobMailTemplate['m_companies_id']) {
              if((!empty($mailAdressData[$index-1]) && $mailAdressData[$index-1]['MUser']['m_companies_id'] != $mailAdress['MUser']['m_companies_id'] && $index != 0) ||
              $index == 0) {
                $companyName = $this->MCompany->find('all',[
                  'fields' => [
                    'company_name'
                  ],
                  'conditions' => [
                    'id' => $mailAdress['MUser']['m_companies_id']
                  ]
                ]);
                $companyName = $companyName[0]['MCompany']['company_name'];
                if($index == 0) {
                  $companyNames = $companyName;
                }
                else {
                  $companyNames .= ','.$companyName;
                }
              }
              $id = $jobMailTemplate['id'];
              $to = $mailAdress['MUser']['mail_address'];
              $sender = $jobMailTemplate['sender'];
              $jobMailTemplate['mail_body'] = str_replace(self::COMPANY_NAME, $companyName, $jobMailTemplate['mail_body']);
              $jobMailTemplate['mail_body'] = str_replace(self::USER_NAME, $mailAdress['MUser']['user_name'], $jobMailTemplate['mail_body']);
              $body = $jobMailTemplate['mail_body'];
              $body = $jobMailTemplate['mail_body'];
              $subject = $jobMailTemplate['subject'];
              $this->log("Sending mail to ".$to." subject : ".$subject." JOB ID: ".$id, self::LOG_INFO);
              $this->component->setFrom(self::ML_MAIL_ADDRESS);
              $this->component->setFromName($sender);
              $this->component->setTo($to);
              $this->component->setBody($body);
              $this->component->setSubject($subject);
              $this->component->send();
              $jobMailTemplate['mail_body'] = str_replace($companyName, self::COMPANY_NAME, $jobMailTemplate['mail_body']);
              $jobMailTemplate['mail_body'] = str_replace($mailAdress['MUser']['user_name'], self::USER_NAME, $jobMailTemplate['mail_body']);
            }
          } catch(Exception $e) {
            $this->log('send mail error !!!!', self::LOG_ERROR);
          }
        }
        if($jobMailTemplate['send_mail_ml_flg'] == 0 && $jobMailTemplate === end($jobMailTemplatesData)) {
          $this->component->setFrom(self::ML_MAIL_ADDRESS);
          $this->component->setFromName($jobMailTemplate['sender']);
          $this->component->setTo(self::ML_MAIL_ADDRESS);
          //送信した会社名をMLに送る
          $jobMailTemplate['mail_body'] = str_replace(self::COMPANY_NAME, $companyNames, $jobMailTemplate['mail_body']);
          $jobMailTemplate['mail_body'] = str_replace(self::USER_NAME, "", $jobMailTemplate['mail_body']);
          $body = $jobMailTemplate['mail_body'];
          $this->component->setBody($body);
          $this->component->setSubject($jobMailTemplate['subject']);
          $this->component->send();
        }
      }
    }
    $this->log('END   sendmail schedule.', self::LOG_INFO);
  }
}