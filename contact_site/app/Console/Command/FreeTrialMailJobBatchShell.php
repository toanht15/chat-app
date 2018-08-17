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

  const ML_MAIL_ADDRESS= "henmi0201@gmail.com";

  const COMPANY_NAME = "##COMPANY_NAME##";
  const BUSINESS_MODEL = "##BUSINESS_MODEL##";
  /** 申込者 */
  const DEPARTMENT = "##DEPARTMENT##";
  const POSITION = "##POSITION##";
  const USER_NAME = "##USER_NAME##";
  const MAIL_ADDRESS = "##MAIL_ADDRESS##";
  const PHONE_NUMBER = "##PHONE_NUMBER##";
  const URL = "##URL##";
  const OTHER = "##OTHER##";
  const PLAN_NAME = "##PLAN_NAME##";
  const BEGIN_DATE = "##BEGIN_DATE##";
  const END_DATE = "##END_DATE##";
  const USABLE_USER_COUNT = "##USABLE_USER_COUNT##";
  const OPTION_COMPANY_INFO = "##OPTION_COMPANY_INFO##";
  const OPTION_SCENARIO = "##OPTION_SCENALIO##";
  const OPTION_CAPTURE = "##OPTION_CAPTURE##";

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
      'conditions' => [
        'm_companies_id' => $trialCompanyIds
      ]
    ]);
    //本契約中の企業の期間を検出
    $agreementDay = $this->MAgreement->find('all',[
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
          $nowTime = date('Y-m-d G');
          $this->log('BEGIN sendmail schedule3.', self::LOG_INFO);
          //現在の時刻と比較(無料トライアルの場合)
          if($trialTime == $nowTime && $val['MJobMailTemplate']['agreement_flg'] == 1) {
            $trialJobMailTemplatesData[$val['MJobMailTemplate']['id']][$trialNumber]['id'] = $val['MJobMailTemplate']['id'];
            $trialJobMailTemplatesData[$val['MJobMailTemplate']['id']][$trialNumber]['mail_type_cd'] = $val['MJobMailTemplate']['mail_type_cd'];
            $trialJobMailTemplatesData[$val['MJobMailTemplate']['id']][$trialNumber]['sender'] = $val['MJobMailTemplate']['sender'];
            $trialJobMailTemplatesData[$val['MJobMailTemplate']['id']][$trialNumber]['subject'] = $val['MJobMailTemplate']['subject'];
            $trialJobMailTemplatesData[$val['MJobMailTemplate']['id']][$trialNumber]['mail_body'] = $val['MJobMailTemplate']['mail_body'];
            $trialJobMailTemplatesData[$val['MJobMailTemplate']['id']][$trialNumber]['send_mail_ml_flg'] = $val['MJobMailTemplate']['send_mail_ml_flg'];
            $trialJobMailTemplatesData[$val['MJobMailTemplate']['id']][$trialNumber]['send_mail_application_user_flg'] = $val['MJobMailTemplate']['send_mail_application_user_flg'];
            $trialJobMailTemplatesData[$val['MJobMailTemplate']['id']][$trialNumber]['send_mail_administrator_user_flg'] = $val['MJobMailTemplate']['send_mail_administrator_user_flg'];
            $trialJobMailTemplatesData[$val['MJobMailTemplate']['id']][$trialNumber]['send_mail_sinclo_all_users_flg'] = $val['MJobMailTemplate']['send_mail_sinclo_all_users_flg'];
            $trialJobMailTemplatesData[$val['MJobMailTemplate']['id']][$trialNumber]['m_companies_id'] = $trial['MAgreement']['m_companies_id'];
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
          $nowTime = date('Y-m-d G');
          //現在の時刻と比較(いきなり本契約の場合)
          if($agreementTime == $nowTime && $val['MJobMailTemplate']['agreement_flg'] == 2) {
            $jobMailTemplatesData[$val['MJobMailTemplate']['id']][$agreementNumber]['id'] = $val['MJobMailTemplate']['id'];
            $jobMailTemplatesData[$val['MJobMailTemplate']['id']][$agreementNumber]['mail_type_cd'] = $val['MJobMailTemplate']['mail_type_cd'];
            $jobMailTemplatesData[$val['MJobMailTemplate']['id']][$agreementNumber]['sender'] = $val['MJobMailTemplate']['sender'];
            $jobMailTemplatesData[$val['MJobMailTemplate']['id']][$agreementNumber]['subject'] = $val['MJobMailTemplate']['subject'];
            $jobMailTemplatesData[$val['MJobMailTemplate']['id']][$agreementNumber]['mail_body'] = $val['MJobMailTemplate']['mail_body'];
            $jobMailTemplatesData[$val['MJobMailTemplate']['id']][$agreementNumber]['send_mail_ml_flg'] = $val['MJobMailTemplate']['send_mail_ml_flg'];
            $jobMailTemplatesData[$val['MJobMailTemplate']['id']][$agreementNumber]['send_mail_application_user_flg'] = $val['MJobMailTemplate']['send_mail_application_user_flg'];
            $jobMailTemplatesData[$val['MJobMailTemplate']['id']][$agreementNumber]['send_mail_administrator_user_flg'] = $val['MJobMailTemplate']['send_mail_administrator_user_flg'];
            $jobMailTemplatesData[$val['MJobMailTemplate']['id']][$agreementNumber]['send_mail_sinclo_all_users_flg'] = $val['MJobMailTemplate']['send_mail_sinclo_all_users_flg'];
            $jobMailTemplatesData[$val['MJobMailTemplate']['id']][$agreementNumber]['m_companies_id'] = $agreement['MAgreement']['m_companies_id'];
            $this->log($jobMailTemplatesData,LOG_DEBUG);
            $companyIds[] = $agreement['MAgreement']['m_companies_id'];
          }
      }
    }

    $trialAgreementsList = $this->MAgreement->find('all',[
      'conditions' => [
        'm_companies_id' => $trialCompanyIds
      ]
    ]);

    $agreementsList = $this->MAgreement->find('all',[
      'conditions' => [
        'm_companies_id' => $companyIds
      ]
    ]);

    //トライアル中の企業のユーザーのメールアドレスを検出
    $trialMailAdressData = $this->MUser->find('all',[
      'fields' => [
        'm_companies_id',
        'user_name',
        'mail_address'
      ],
      'conditions' => [
        'm_companies_id' => $trialCompanyIds,
        'permission_level' => [1,2],
        'MUser.del_flg' => 0
      ]
    ]);

    //本契約中の企業のユーザーのメールアドレスを検出
    $agreementMailAdressData = $this->MUser->find('all',[
      'fields' => [
        'm_companies_id',
        'user_name',
        'mail_address'
      ],
      'conditions' => [
        'm_companies_id' => $companyIds,
        'permission_level' => [1,2],
        'MUser.del_flg' => 0
      ]
    ]);

    if(empty($trialMailAdressData)) {
      $this->log('trialSchedule is not found.', self::LOG_INFO);
    } else {
      $this->log('BEGIN sendmail schedule4.', self::LOG_INFO);
      $isApplicationUserSended = false;
      $isAdminUserSended = false;
      $isAdminAllUserSended = false;
      foreach($trialJobMailTemplatesData as $templateId => $trialJobs) {
        $trialCompanyNames = "";
        foreach($trialJobs as $key => $jobMailTemplate) {
          foreach ($trialMailAdressData as $index => $mailAdress) {
            try {
              if ($mailAdress['MUser']['m_companies_id'] == $jobMailTemplate['m_companies_id']) {
                //m_companies_idが変わるごとに会社名取得
                if ((!empty($trialMailAdressData[$index - 1]) && $trialMailAdressData[$index - 1]['MUser']['m_companies_id'] != $mailAdress['MUser']['m_companies_id'] && $index != 0) ||
                  $index == 0) {
                  $trialCompanyData = $this->MCompany->find('all', [
                    'conditions' => [
                      'id' => $mailAdress['MUser']['m_companies_id']
                    ]
                  ]);
                  $trialCompanyName = $trialCompanyData[0]['MCompany']['company_name'];
                  if (empty($trialCompanyNames)) {
                    $trialCompanyNames = $trialCompanyName;
                  } else {
                    $trialCompanyNames .= ',' . $trialCompanyName;
                  }
                  $applicationSameAdress = false;
                  $administratorSameAdress = false;
                }
                $agreementData = $this->getRecordFromCompanyId($trialAgreementsList, $mailAdress['MUser']['m_companies_id']);
                //申込者に送る場合
                if ($jobMailTemplate['send_mail_application_user_flg']) {
                  if(strcmp(end($trialMailAdressData)['MUser']['mail_address'], $mailAdress['MUser']['mail_address']) == 0 && !$isApplicationUserSended ) {
                    $id = $jobMailTemplate['id'];
                    $to = $agreementData['application_mail_address'];
                    $sender = $jobMailTemplate['sender'];
                    $body = $jobMailTemplate['mail_body'];
                    $subject = $jobMailTemplate['subject'];

                    $replaceData = array(
                      'MCompany' => $trialCompanyData[0]['MCompany'],
                      'MAgreement' => $agreementData
                    );;
                    $body = $this->replaceAllMailConstString($replaceData, $body,'send_mail_application_user');
                    // 送信前にログを生成
                    $this->TMailTransmissionLog->create();
                    $this->TMailTransmissionLog->set(array(
                      'm_companies_id' => 0, // システムメールなので0で登録
                      'mail_type_cd' => 'JOB'.$id,
                      'from_address' => MailSenderComponent::MAIL_SYSTEM_FROM_ADDRESS,
                      'from_name' => $sender,
                      'to_address' => $to,
                      'subject' => $subject,
                      'body' => $body,
                      'send_flg' => 0
                    ));
                    $this->TMailTransmissionLog->save();
                    $lastInsertId = $this->TMailTransmissionLog->getLastInsertId();

                    $this->log("【TRIAL】Sending mail to Application User: " . $to . " subject : " . $subject . " JOB ID: " . $id, self::LOG_INFO);
                    $this->component->setFrom(self::ML_MAIL_ADDRESS);
                    $this->component->setFromName($sender);
                    $this->component->setTo($to);
                    $this->component->setBody($body);
                    $this->component->setSubject($subject);
                    $this->component->send();
                    $isApplicationUserSended = true;
                    if(strcmp($mailAdress['MUser']['mail_address'], $agreementData['application_mail_address']) == 0) {
                      $applicationSameAdress = true;
                    }
                    $now = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
                    $this->TMailTransmissionLog->read(null, $lastInsertId);
                    $this->TMailTransmissionLog->set([
                      'send_flg' => 1,
                      'sent_datetime' => $now->format("Y/m/d H:i:s")
                    ]);
                    $this->TMailTransmissionLog->save();
                  }
                }
                //管理者に送る場合
                if ($jobMailTemplate['send_mail_administrator_user_flg']) {
                  if((($isApplicationUserSended && strcmp($agreementData['application_mail_address'], $agreementData['administrator_mail_address']) !== 0)
                    || !$isApplicationUserSended) && (strcmp($agreementData['administrator_mail_address'], $mailAdress['MUser']['mail_address']) == 0 || (strcmp(end($trialMailAdressData)['MUser']['mail_address'], $mailAdress['MUser']['mail_address']) == 0 && !$isAdminUserSended))) {
                    $id = $jobMailTemplate['id'];
                    $to = $agreementData['administrator_mail_address'];
                    $sender = $jobMailTemplate['sender'];
                    $body = $jobMailTemplate['mail_body'];
                    $subject = $jobMailTemplate['subject'];

                    $replaceData = array(
                      'MCompany' => $trialCompanyData[0]['MCompany'],
                      'MAgreement' => $agreementData
                    );
                    $body = $this->replaceAllMailConstString($replaceData, $body,'send_mail_administrator_user');
                    // 送信前にログを生成
                    $this->TMailTransmissionLog->create();
                    $this->TMailTransmissionLog->set(array(
                      'm_companies_id' => 0, // システムメールなので0で登録
                      'mail_type_cd' => 'JOB'.$id,
                      'from_address' => MailSenderComponent::MAIL_SYSTEM_FROM_ADDRESS,
                      'from_name' => $sender,
                      'to_address' => $to,
                      'subject' => $subject,
                      'body' => $body,
                      'send_flg' => 0
                    ));
                    $this->TMailTransmissionLog->save();
                    $lastInsertId = $this->TMailTransmissionLog->getLastInsertId();
                    $this->log("【TRIAL】Sending mail to Administrator User: " . $to . " subject : " . $subject . " JOB ID: " . $id, self::LOG_INFO);
                    $this->component->setFrom(self::ML_MAIL_ADDRESS);
                    $this->component->setFromName($sender);
                    $this->component->setTo($to);
                    $this->component->setBody($body);
                    $this->component->setSubject($subject);
                    $this->component->send();
                    $isAdminUserSended = true;
                    if(strcmp($mailAdress['MUser']['mail_address'], $agreementData['administrator_mail_address']) == 0) {
                      $administratorSameAdress = true;
                    }
                    $now = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
                    $this->TMailTransmissionLog->read(null, $lastInsertId);
                    $this->TMailTransmissionLog->set([
                      'send_flg' => 1,
                      'sent_datetime' => $now->format("Y/m/d H:i:s")
                    ]);
                    $this->TMailTransmissionLog->save();
                  }
                }
                //sincloのユーザー一覧にいる全員に送る場合
                if ($jobMailTemplate['send_mail_sinclo_all_users_flg']) {
                  if(!$applicationSameAdress && !$administratorSameAdress) {
                    $id = $jobMailTemplate['id'];
                    $to = $mailAdress['MUser']['mail_address'];
                    $sender = $jobMailTemplate['sender'];
                    $body = $jobMailTemplate['mail_body'];
                    $subject = $jobMailTemplate['subject'];

                    $agreementData = $this->MAgreement->find('first', [
                      'conditions' => [
                        'm_companies_id' => $mailAdress['MUser']['m_companies_id']
                      ]
                    ]);
                    $agreementData['MAgreement']['sinclo_user_name'] = $mailAdress['MUser']['user_name'];
                    $agreementData['MAgreement']['sinclo_user_mail_address'] = $mailAdress['MUser']['mail_address'];
                    $replaceData = array(
                      'MCompany' => $trialCompanyData[0]['MCompany'],
                      'MAgreement' => $agreementData['MAgreement']
                    );
                    $body = $this->replaceAllMailConstString($replaceData, $body,'send_mail_sinclo_all_users');
                    unset($agreementData['MAgreement']['sinclo_user_name']);
                    unset($agreementData['MAgreement']['sinclo_user_mail_address']);
                    // 送信前にログを生成
                    $this->TMailTransmissionLog->create();
                    $this->TMailTransmissionLog->set(array(
                      'm_companies_id' => 0, // システムメールなので0で登録
                      'mail_type_cd' => 'JOB'.$id,
                      'from_address' => MailSenderComponent::MAIL_SYSTEM_FROM_ADDRESS,
                      'from_name' => $sender,
                      'to_address' => $to,
                      'subject' => $subject,
                      'body' => $body,
                      'send_flg' => 0
                    ));
                    $this->TMailTransmissionLog->save();
                    $lastInsertId = $this->TMailTransmissionLog->getLastInsertId();
                    $this->log("【TRIAL】Sending mail to sinclo User: " . $to . " subject : " . $subject . " JOB ID: " . $id, self::LOG_INFO);
                    $this->component->setFrom(self::ML_MAIL_ADDRESS);
                    $this->component->setFromName($sender);
                    $this->component->setTo($to);
                    $this->component->setBody($body);
                    $this->component->setSubject($subject);
                    $this->component->send();
                    $now = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
                    $this->TMailTransmissionLog->read(null, $lastInsertId);
                    $this->TMailTransmissionLog->set([
                      'send_flg' => 1,
                      'sent_datetime' => $now->format("Y/m/d H:i:s")
                    ]);
                    $this->TMailTransmissionLog->save();
                  }
                }
              }
            } catch (Exception $e) {
              $this->log('send mail trial error !!!!', self::LOG_ERROR);
            }
          }
          if ($jobMailTemplate['send_mail_ml_flg'] == 0 && $jobMailTemplate === end($trialJobs)) {
            $this->component->setFrom(self::ML_MAIL_ADDRESS);
            $this->component->setFromName($jobMailTemplate['sender']);
            $this->component->setTo(self::ML_MAIL_ADDRESS);
            $agreementData = $this->MAgreement->find('first', array(
              'conditions' => array(
                'm_companies_id' => $mailAdress['MUser']['m_companies_id']
              )
            ));

            $replaceData = $this->createReplaceDataForMLSendmail($trialCompanyNames);
            $body = $this->replaceAllMailConstString($replaceData, $jobMailTemplate['mail_body']);
            $this->component->setBody($body);
            $this->component->setSubject($jobMailTemplate['subject']);
            $this->component->send();
          }
        }
      }
    }

    if(empty($agreementMailAdressData)) {
      $this->log('schedule is not found.', self::LOG_INFO);
    } else {
      $isApplicationUserSended = false;
      $isAdminUserSended = false;
      $isAdminAllUserSended = false;
      foreach($jobMailTemplatesData as $templateId => $jobs) {
        $companyNames = "";
        foreach($jobs as $key => $jobMailTemplate) {
          $isApplicationUserSended = false;
          $isAdminUserSended = false;
          foreach ($agreementMailAdressData as $index => $mailAdress) {
            try {
              if ($mailAdress['MUser']['m_companies_id'] == $jobMailTemplate['m_companies_id']) {
                if ((!empty($agreementMailAdressData[$index - 1]) && $agreementMailAdressData[$index - 1]['MUser']['m_companies_id'] != $mailAdress['MUser']['m_companies_id'] && $index != 0) ||
                  $index == 0) {
                  $companyData = $this->MCompany->find('all', [
                    'conditions' => [
                      'id' => $mailAdress['MUser']['m_companies_id']
                    ]
                  ]);
                  $companyName = $companyData[0]['MCompany']['company_name'];
                  if (empty($companyNames)) {
                    $companyNames = $companyName;
                  } else {
                    $companyNames .= ',' . $companyName;
                  }
                  $applicationSameAdress = false;
                  $administratorSameAdress = false;
                }
                $agreementData = $this->getRecordFromCompanyId($agreementsList, $mailAdress['MUser']['m_companies_id']);
                //申込者に送る場合
                if ($jobMailTemplate['send_mail_application_user_flg']) {
                  if(strcmp(end($agreementMailAdressData)['MUser']['mail_address'], $mailAdress['MUser']['mail_address']) == 0 && !$isApplicationUserSended ) {
                    $id = $jobMailTemplate['id'];
                    $to = $agreementData['application_mail_address'];
                    $sender = $jobMailTemplate['sender'];
                    $body = $jobMailTemplate['mail_body'];
                    $subject = $jobMailTemplate['subject'];

                    $replaceData = array(
                      'MCompany' => $companyData[0]['MCompany'],
                      'MAgreement' => $agreementData
                    );
                    $body = $this->replaceAllMailConstString($replaceData, $body,'send_mail_application_user');
                    // 送信前にログを生成
                    $this->TMailTransmissionLog->create();
                    $this->TMailTransmissionLog->set(array(
                      'm_companies_id' => 0, // システムメールなので0で登録
                      'mail_type_cd' => 'JOB'.$id,
                      'from_address' => MailSenderComponent::MAIL_SYSTEM_FROM_ADDRESS,
                      'from_name' => $sender,
                      'to_address' => $to,
                      'subject' => $subject,
                      'body' => $body,
                      'send_flg' => 0
                    ));
                    $this->TMailTransmissionLog->save();
                    $this->log("【TRIAL】Sending mail to Application User: " . $to . " subject : " . $subject . " JOB ID: " . $id, self::LOG_INFO);
                    $this->component->setFrom(self::ML_MAIL_ADDRESS);
                    $this->component->setFromName($sender);
                    $this->component->setTo($to);
                    $this->component->setBody($body);
                    $this->component->setSubject($subject);
                    $this->component->send();
                    $isApplicationUserSended = true;
                    if($mailAdress['MUser']['mail_address'] == $agreementData['application_mail_address']) {
                      $applicationSameAdress = true;
                    }
                    $now = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
                    $this->TMailTransmissionLog->read(null, $lastInsertId);
                    $this->TMailTransmissionLog->set([
                      'send_flg' => 1,
                      'sent_datetime' => $now->format("Y/m/d H:i:s")
                    ]);
                    $this->TMailTransmissionLog->save();
                  }
                }
                //管理者に送る場合
                if ($jobMailTemplate['send_mail_administrator_user_flg']) {
                  if((($isApplicationUserSended && strcmp($agreementData['application_mail_address'], $agreementData['administrator_mail_address']) !== 0 )
                    || !$isApplicationUserSended) && (strcmp($agreementData['administrator_mail_address'], $mailAdress['MUser']['mail_address']) == 0 || (strcmp(end($agreementMailAdressData)['MUser']['mail_address'], $mailAdress['MUser']['mail_address']) == 0 && !$isAdminUserSended))) {
                    $id = $jobMailTemplate['id'];
                    $to = $agreementData['administrator_mail_address'];
                    $sender = $jobMailTemplate['sender'];
                    $body = $jobMailTemplate['mail_body'];
                    $subject = $jobMailTemplate['subject'];

                    $replaceData = array(
                      'MCompany' => $companyData[0]['MCompany'],
                      'MAgreement' => $agreementData
                    );
                    $body = $this->replaceAllMailConstString($replaceData, $body,'send_mail_administrator_user');
                    // 送信前にログを生成
                    $this->TMailTransmissionLog->create();
                    $this->TMailTransmissionLog->set(array(
                      'm_companies_id' => 0, // システムメールなので0で登録
                      'mail_type_cd' => 'JOB'.$id,
                      'from_address' => MailSenderComponent::MAIL_SYSTEM_FROM_ADDRESS,
                      'from_name' => $sender,
                      'to_address' => $to,
                      'subject' => $subject,
                      'body' => $body,
                      'send_flg' => 0
                    ));
                    $this->TMailTransmissionLog->save();
                    $this->log("【TRIAL】Sending mail to Administrator User: " . $to . " subject : " . $subject . " JOB ID: " . $id, self::LOG_INFO);
                    $this->component->setFrom(self::ML_MAIL_ADDRESS);
                    $this->component->setFromName($sender);
                    $this->component->setTo($to);
                    $this->component->setBody($body);
                    $this->component->setSubject($subject);
                    $this->component->send();
                    $isAdminUserSended = true;
                    if(strcmp($mailAdress['MUser']['mail_address'], $agreementData['administrator_mail_address']) == 0) {
                      $administratorSameAdress = true;
                    }
                    $now = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
                    $this->TMailTransmissionLog->read(null, $lastInsertId);
                    $this->TMailTransmissionLog->set([
                      'send_flg' => 1,
                      'sent_datetime' => $now->format("Y/m/d H:i:s")
                    ]);
                    $this->TMailTransmissionLog->save();
                  }
                }
                //sincloのユーザー一覧にいる全員に送る場合
                if ($jobMailTemplate['send_mail_sinclo_all_users_flg']) {
                  if(!$applicationSameAdress && !$administratorSameAdress) {
                    $id = $jobMailTemplate['id'];
                    $to = $mailAdress['MUser']['mail_address'];
                    $sender = $jobMailTemplate['sender'];
                    $body = $jobMailTemplate['mail_body'];
                    $subject = $jobMailTemplate['subject'];

                    $agreementData = $this->MAgreement->find('first', [
                      'conditions' => [
                        'm_companies_id' => $mailAdress['MUser']['m_companies_id']
                      ]
                    ]);
                    $agreementData['MAgreement']['sinclo_user_name'] = $mailAdress['MUser']['user_name'];
                    $agreementData['MAgreement']['sinclo_user_mail_address'] = $mailAdress['MUser']['mail_address'];
                    $replaceData = array(
                      'MCompany' => $companyData[0]['MCompany'],
                      'MAgreement' => $agreementData['MAgreement']
                    );
                    $body = $this->replaceAllMailConstString($replaceData, $body,'send_mail_sinclo_all_users');
                    unset($agreementData['MAgreement']['sinclo_user_name']);
                    unset($agreementData['MAgreement']['sinclo_user_mail_address']);
                    // 送信前にログを生成
                    $this->TMailTransmissionLog->create();
                    $this->TMailTransmissionLog->set(array(
                      'm_companies_id' => 0, // システムメールなので0で登録
                      'mail_type_cd' => 'JOB'.$id,
                      'from_address' => MailSenderComponent::MAIL_SYSTEM_FROM_ADDRESS,
                      'from_name' => $sender,
                      'to_address' => $to,
                      'subject' => $subject,
                      'body' => $body,
                      'send_flg' => 0
                    ));
                    $this->TMailTransmissionLog->save();
                    $this->log("【TRIAL】Sending mail to sinclo User: " . $to . " subject : " . $subject . " JOB ID: " . $id, self::LOG_INFO);
                    $this->component->setFrom(self::ML_MAIL_ADDRESS);
                    $this->component->setFromName($sender);
                    $this->component->setTo($to);
                    $this->component->setBody($body);
                    $this->component->setSubject($subject);
                    $this->component->send();
                    $now = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
                    $this->TMailTransmissionLog->read(null, $lastInsertId);
                    $this->TMailTransmissionLog->set([
                      'send_flg' => 1,
                      'sent_datetime' => $now->format("Y/m/d H:i:s")
                    ]);
                    $this->TMailTransmissionLog->save();
                  }
                }
              }
            } catch (Exception $e) {
              $this->log('send mail error !!!!', self::LOG_ERROR);
            }
          }
          if ($jobMailTemplate['send_mail_ml_flg'] == 0 && $jobMailTemplate === end($jobs)) {
            $this->component->setFrom(self::ML_MAIL_ADDRESS);
            $this->component->setFromName($jobMailTemplate['sender']);
            $this->component->setTo(self::ML_MAIL_ADDRESS);

            $replaceData = $this->createReplaceDataForMLSendmail($companyNames);
            $body = $this->replaceAllMailConstString($replaceData, $jobMailTemplate['mail_body']);

            $this->component->setBody($body);
            $this->component->setSubject($jobMailTemplate['subject']);
            $this->component->send();
          }
        }
      }
    }
    $this->log('END   sendmail schedule.', self::LOG_INFO);
  }

  private function replaceAllMailConstString($data, $mailTemplateData, $mailType) {
    if(!empty($data['MAgreement']['business_model'])) {
      if($data['MAgreement']['business_model'] == 1) {
        $data['MAgreement']['business_model'] = 'BtoB';
      }
      if($data['MAgreement']['business_model'] == 2) {
        $data['MAgreement']['business_model'] = 'BtoC';
      }
      if($data['MAgreement']['business_model'] == 3) {
        $data['MAgreement']['business_model'] = 'どちらも';
      }
    }
    $this->log($data);
    $mailBodyData = $this->replaceConstToString($data['MCompany']['company_name'],self::COMPANY_NAME, $mailTemplateData);
    $mailBodyData = $this->replaceConstToString($data['MAgreement']['business_model'], self::BUSINESS_MODEL, $mailBodyData);
    //部署 申込者の場合
    if($mailType == 'send_mail_application_user') {
      $mailBodyData = $this->replaceConstToString($data['MAgreement']['application_department'], self::DEPARTMENT, $mailBodyData);
    }
    //部署 管理者の場合の場合
    if($mailType == 'send_mail_administrator_user') {
      $mailBodyData = $this->replaceConstToString($data['MAgreement']['administrator_department'], self::DEPARTMENT, $mailBodyData);
    }
    //役職 申込者の場合
    if($mailType == 'send_mail_application_user') {
      $mailBodyData = $this->replaceConstToString($data['MAgreement']['application_position'], self::POSITION, $mailBodyData);
    }
    //役職 管理者の場合の場合
    if($mailType == 'send_mail_administrator_user') {
      $mailBodyData = $this->replaceConstToString($data['MAgreement']['administrator_position'], self::POSITION, $mailBodyData);
    }
    if(!empty($data['MAgreement']['sinclo_user_name'])) {
      $mailBodyData = $this->replaceConstToString($data['MAgreement']['sinclo_user_name'], self::USER_NAME, $mailBodyData);
    } else {
      //お名前 申込者の場合
      if($mailType == 'send_mail_application_user') {
        $mailBodyData = $this->replaceConstToString($data['MAgreement']['application_name'], self::USER_NAME, $mailBodyData);
      }
      //お名前 管理者の場合
      if($mailType == 'send_mail_administrator_user') {
        $mailBodyData = $this->replaceConstToString($data['MAgreement']['administrator_name'], self::USER_NAME, $mailBodyData);
      }
    }
    if(!empty($data['MAgreement']['sinclo_user_mail_address'])) {
      $mailBodyData = $this->replaceConstToString($data['MAgreement']['sinclo_user_mail_address'], self::MAIL_ADDRESS, $mailBodyData);
    } else {
      //メールアドレス 申込者の場合
      if($mailType == 'send_mail_application_user') {
        $mailBodyData = $this->replaceConstToString($data['MAgreement']['application_mail_address'], self::MAIL_ADDRESS, $mailBodyData);
      }
      //メールアドレス 管理者の場合
      if($mailType == 'send_mail_administrator_user') {
        $mailBodyData = $this->replaceConstToString($data['MAgreement']['administrator_mail_address'], self::MAIL_ADDRESS, $mailBodyData);
      }
    }
    $mailBodyData = $this->replaceConstToString($data['MAgreement']['telephone_number'], self::PHONE_NUMBER, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($data['MAgreement']['installation_url'], self::URL, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($data['MCompany']['limit_users'], self::USABLE_USER_COUNT, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($this->getPlanNameStr($data), self::PLAN_NAME, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($this->getBeginDate($data), self::BEGIN_DATE, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($this->getEndDate($data), self::END_DATE, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($this->getOptionCompanyInfoEnabled($data), self::OPTION_COMPANY_INFO, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($this->getOptionChatbotScenario($data), self::OPTION_SCENARIO, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($this->getOptionLaCoBrowse($data), self::OPTION_CAPTURE, $mailBodyData);

    return $mailBodyData;
  }

  private function replaceConstToString($string, $const, $body) {
    if(!empty($string)) {
      return str_replace($const, $string, $body);
    }
    else {
      return str_replace($const, "", $body);
    }
  }

  private function getPlanNameStr($data) {
    $planId = $data['MCompany']['m_contact_types_id'];
    switch(intval($planId)) {
      case 1:
        return 'プレミアムプラン';
      case 2:
        return 'スタンダードプラン';
      case 3:
        return 'シェアリングプラン';
      case 4:
        return 'ベーシックプラン';
      default:
        return '不明なプラン';
    }
  }

  private function getBeginDate($data) {
    if(intval($data['MCompany']['trial_flg']) === 1) {
      return $data['MAgreement']['trial_start_day'] ? $data['MAgreement']['trial_start_day'] : "";
    } else {
      return $data['MAgreement']['agreement_start_day'] ? $data['MAgreement']['agreement_start_day'] : "";
    }
  }

  private function getEndDate($data) {
    if(intval($data['MCompany']['trial_flg']) === 1) {
      return $data['MAgreement']['trial_end_day'] ? $data['MAgreement']['trial_end_day'] : "";
    } else {
      return $data['MAgreement']['agreement_end_day'] ? $data['MAgreement']['agreement_end_day'] : "";
    }
  }

  private function getOptionCompanyInfoEnabled($data) {
    if(!empty($data['MCompany']['options']['refCompanyData']) || json_decode($data['MCompany']['core_settings'], TRUE)['refCompanyData']) {
      return '企業情報付与オプション：あり';
    } else {
      return '企業情報付与オプション：なし';
    }
  }

  private function getOptionChatbotScenario($data) {
    if(!empty($data['MCompany']['options']['chatbotScenario']) || json_decode($data['MCompany']['core_settings'], TRUE)['chatbotScenario']) {
      return 'チャットボットシナリオオプション：あり';
    } else {
      return 'チャットボットシナリオオプション：なし';
    }
  }

  private function getOptionLaCoBrowse($data) {
    if(!empty($data['MCompany']['options']['laCoBrowse']) || json_decode($data['MCompany']['core_settings'], TRUE)['laCoBrowse']) {
      return '画面キャプチャオプション：あり（最大同時セッション数：'.$data['MCompany']['la_limit_users'].'）';
    } else {
      return '画面キャプチャオプション：なし';
    }
  }

  private function getRecordFromCompanyId($array, $m_companies_id) {
    foreach($array as $index => $record) {
      foreach($record as $tableName => $data) {
        if(strcmp($data['m_companies_id'], $m_companies_id) === 0) return $data;
      }
    }
    return [];
  }

  private function createReplaceDataForMLSendmail($companyNames) {
    return array(
      'MCompany' => array(
        'company_key' => 'COMPANY_KEY',
        'company_name' => $companyNames,
        'm_contact_types_id' => 0,
        'limit_users' => 'USABLE_USER_COUNT',
        'la_limit_users' => 'CAPTURE_USER_COUNT',
        'core_settings' => 'CORE_SETTINGS',
        'trial_flg' => 0
      ),
      'MAgreement' => array(
        'company_name' => $companyNames,
        'business_model' => 'BUSINESS_MODEL',
        'application_day' => 'APPLICATION_DAY',
        'trial_start_day' => 'BEGIN_DATE',
        'trial_end_day' => 'END_DATE',
        'agreement_start_day' => 'BEGIN_DATE',
        'agreement_end_day' => 'END_DATE',
        'application_department' => 'APPLICATION_DEPARTMENT',
        'application_position' => 'APPLICATION_POSITION',
        'application_name' => 'APPLICATION_NAME',
        'application_mail_address' => 'APPLICATION_MAIL_ADDRESS',
        'administrator_department' => 'ADMINISTRATOR_DEPARTMENT',
        'administrator_position' => 'ADMINISTRATOR_POSITION',
        'administrator_name' => 'ADMINISTRATOR_NAME',
        'administrator_mail_address' => 'ADMINISTRATOR_MAIL_ADDRESS',
        'installation_site_name' => 'SITE_NAME',
        'installation_url' => 'SITE_URL',
        'telephone_number' => 'TELEPHONE_NUMBER',
        'note' => 'NOTE'
      )
    );
  }
}