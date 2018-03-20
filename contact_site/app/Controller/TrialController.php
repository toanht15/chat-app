<?php
/**
 * TrialController controller.
 * 無料トライアル登録画面
 */
App::uses('HttpSocket', 'Network/Http', 'Component', 'Controller', 'Utility/Validation');
class TrialController extends AppController {
  const CONTRACT_ADD_URL = "http://127.0.0.1:81/Contract/add";
  const ML_MAIL_ADDRESS= "cloud-service@medialink-ml.co.jp";
  const ML_MAIL_ADDRESS_AND_ALEX = "cloud-service@medialink-ml.co.jp,alexandre.mercier@medialink-ml.co.jp";
  const API_CALL_TIMEOUT = 5;
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
  public $components = ['AutoMessageMailTemplate', 'MailSender', 'Auth'];
  public $uses = ['MUser','TMailTransmissionLog','MSystemMailTemplate','TSendSystemMailSchedule','MJobMailTemplate'];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->Auth->allow(['index','add','thanks','check','remoteTermsOfService']);
    $this->header('Access-Control-Allow-Origin: http://127.0.0.1:81/Contract/add');
    $this->set('title_for_layout', '無料トライアル登録画面');
  }

  /* *
   * 基本設定ページ
   * @return void
   * */
  public function index() {
    $businessModel = Configure::read('businessModelType');
    $this->set('businessModel',$businessModel);
  }

  public function add() {
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $data = $this->request->data;
    $data['MUser']['mail_address'] = $data['Contract']['user_mail_address'];

    $data['MCompany']['trial_flg'] = C_TRIAL_FLG;
    $data['MCompany']['options']['refCompanyData'] = 1;
    $data['MCompany']['m_contact_types_id'] = 1;
    $data['MCompany']['limit_users'] = 3;
    $data['MAgreements']['trial_start_day'] = date('Y-m-d');
    $data['MAgreements']['trial_end_day'] = date('Y-m-d', strtotime('+13 day', time()));
    $data['MAgreements']['agreement_start_day'] = '';
    $data['MAgreements']['agreement_end_day'] = '';
    $data['Contract']['user_name'] = 'テストユーザー';
    $data['Contract']['user_display_name'] = 'テストユーザー';
    $password = $this->generateRandomPassword(8);
    $data['Contract']['user_password'] = $password;

    $socket = new HttpSocket(array(
      'timeout' => self::API_CALL_TIMEOUT
    ));
    $result = $socket->post(self::CONTRACT_ADD_URL,$data,array('header' => array('X-Forwarded-Port' => 443)));

    $jobMailTemplateData = $this->MJobMailTemplate->find('all');

    $tmpData = [];
    foreach($jobMailTemplateData as $k => $v){
      $sendingDatetime = date('Y-m-d', strtotime(' +'.$v['MJobMailTemplate']['days_after'].' day'));
      $sendingDatetime = date('Y-m-d H:i:s', strtotime($sendingDatetime.$v['MJobMailTemplate']['time'].':00:00'));
      $tmpData['TSendSystemMailSchedule']['sending_datetime'] = $sendingDatetime;
      $tmpData['TSendSystemMailSchedule']['subject'] = $v['MJobMailTemplate']['subject'];
      $tmpData['TSendSystemMailSchedule']['mail_body'] = $v['MJobMailTemplate']['mail_body'];
      $tmpData['TSendSystemMailSchedule']['mail_address'] = $data['Contract']['user_mail_address'];
      $tmpData['TSendSystemMailSchedule']['send-mail_flg'] = 0;
      $this->TSendSystemMailSchedule->create();
      $this->TSendSystemMailSchedule->set($tmpData);
      $this->TSendSystemMailSchedule->save();
      $this->TSendSystemMailSchedule->commit();
    }

    $mailTemplateData = $this->MSystemMailTemplate->find('all');

    $mailBodyData = str_replace(self::COMPANY_NAME, $data['MCompany']['company_name'], $mailTemplateData[0]['MSystemMailTemplate']['mail_body']);
    $mailBodyData = str_replace(self::USER_NAME, $data['MAgreements']['application_name'], $mailBodyData);
    $mailBodyData = str_replace(self::PASSWORD, $data['Contract']['user_password'], $mailBodyData);
    $mailBodyData = str_replace(self::MAIL_ADDRESS, $data['Contract']['user_mail_address'], $mailBodyData);

    // 送信前にログを生成
    $this->TMailTransmissionLog->create();
    $this->TMailTransmissionLog->set(array(
      'm_companies_id' => 0, // システムメールなので0で登録
      'mail_type_cd' => 'TL001',
      'from_address' => MailSenderComponent::MAIL_SYSTEM_FROM_ADDRESS,
      'from_name' => 'sinclo(メディアリンク株式会社)',
      'to_address' => $data['Contract']['user_mail_address'],
      'subject' => $mailTemplateData[0]['MSystemMailTemplate']['subject'],
      'body' => $mailBodyData,
      'send_flg' => 0
    ));
    $this->TMailTransmissionLog->save();
    $lastInsertId = $this->TMailTransmissionLog->getLastInsertId();

    //お客さん向け
    $sender = new MailSenderComponent();
    $sender->setFrom(self::ML_MAIL_ADDRESS);
    $sender->setFromName('sinclo（シンクロ）');
    $sender->setTo($data['Contract']['user_mail_address']);
    $sender->setSubject($mailTemplateData[0]['MSystemMailTemplate']['subject']);
    $sender->setBody($mailBodyData);
    $sender->send();

    $now = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
    $this->TMailTransmissionLog->read(null, $lastInsertId);
    $this->TMailTransmissionLog->set([
      'send_flg' => 1,
      'sent_datetime' => $now->format("Y/m/d H:i:s")
    ]);
    $this->TMailTransmissionLog->save();

    //会社向け
    $sender = new MailSenderComponent();
    $sender->setFrom($data['Contract']['user_mail_address']);
    $sender->setFromName($data['MCompany']['company_name'].'　'.$data['MAgreements']['application_name']);
    $sender->setTo(self::ML_MAIL_ADDRESS_AND_ALEX);
    $sender->setSubject($mailTemplateData[1]['MSystemMailTemplate']['subject']);
    $mailBodyData = str_replace(self::COMPANY_NAME, $data['MCompany']['company_name'], $mailTemplateData[1]['MSystemMailTemplate']['mail_body']);
    $mailBodyData = str_replace(self::USER_NAME, $data['MAgreements']['application_name'], $mailBodyData);
    if($data['MAgreements']['business_model'] == 1) {
      $data['MAgreements']['business_model'] = 'BtoB';
    }
    if($data['MAgreements']['business_model'] == 2) {
      $data['MAgreements']['business_model'] = 'BtoC';
    }
    if($data['MAgreements']['business_model'] == 3) {
      $data['MAgreements']['business_model'] = 'どちらも';
    }
    $mailBodyData = str_replace(self::BUSINESS_MODEL, $data['MAgreements']['business_model'], $mailBodyData);
    if(!empty($data['MAgreements']['application_department'])) {
      $mailBodyData = str_replace(self::DEPARTMENT, $data['MAgreements']['application_department'], $mailBodyData);
    }
    else {
      $mailBodyData = str_replace(self::DEPARTMENT, "", $mailBodyData);
    }
    if(!empty($data['MAgreements']['application_position'])) {
      $mailBodyData = str_replace(self::POSITION, $data['MAgreements']['application_position'], $mailBodyData);
    }
    else {
      $mailBodyData = str_replace(self::POSITION, "", $mailBodyData);
    }
    $mailBodyData = str_replace(self::MAIL_ADDRESS, $data['Contract']['user_mail_address'], $mailBodyData);
    $mailBodyData = str_replace(self::PHONE_NUMBER, $data['MAgreements']['telephone_number'], $mailBodyData);
    if(!empty($data['MAgreements']['installation_url'])) {
      $mailBodyData = str_replace(self::URL, $data['MAgreements']['installation_url'], $mailBodyData);
    }
    else {
      $mailBodyData = str_replace(self::URL, "", $mailBodyData);
    }
    if(!empty($data['MAgreements']['note'])) {
      $mailBodyData = str_replace(self::OTHER, $data['MAgreements']['note'], $mailBodyData);
    }
    else {
      $mailBodyData = str_replace(self::OTHER, "", $mailBodyData);
    }
    $sender->setBody($mailBodyData);
    $sender->send();


  }

  /* *
   * サンクスページ
   * @return void
   * */
  public function thanks() {

  }

  /* *
   * メールアドレス　validateチェック
   * @return void
   * */
  public function check() {
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $data = $this->request->data;
    $data['MUser']['mail_address'] = $data['Contract']['user_mail_address'];
    $this->MUser->set($data);
    if(!$this->MUser->validates()) {
      $errorMessage = $this->MUser->validationErrors;
      return $errorMessage['mail_address'][0];
    }
  }

   /* *
  * 利用規約
  * @return void
  * */
  public function remoteTermsOfService() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';

    $this->render('/Elements/Trial/remoteTermsOfService');
  }

  private function generateRandomPassword($length) {
    $str = array_merge(range('a', 'z'), range('0', '9'), range('A', 'Z'));
    $r_str = null;
    for ($i = 0; $i < $length; $i++) {
      $r_str .= $str[rand(0, count($str) - 1)];
    }
    return $r_str;
  }
}
