<?php
/**
 * MtrialController controller.
 * 無料トライアル登録画面
 */
App::uses('HttpSocket', 'Network/Http', 'Component', 'Controller', 'Utility/Validation');
class MtrialController extends AppController {
  const CONTRACT_ADD_URL = "http://admin.sinclo:81/Contract/add";
  const ML_MAIL_ADDRESS= "cloud-service@medialink-ml.co.jp";
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
    //header("Access-Control-Allow-Origin: *");
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
    $this->set('errors','false');
  }

  public function add() {
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $data = $this->request->data;
    $errors = 'false';
    $this->log('data',LOG_DEBUG);
    $this->log($data,LOG_DEBUG);
    $data['MUser']['mail_address'] = $data['Contract']['user_mail_address'];
    $this->MUser->set($data);
    $this->log('validationチェック1',LOG_DEBUG);
    if(!$this->MUser->validates()) {
      $this->log('validationチェック2',LOG_DEBUG);
      $errors = 'true';
      $this->log('errors2222222',LOG_DEBUG);
      $this->log($errors,LOG_DEBUG);
      $this->MUser->rollback();
      //$this->response->statusCode(409); //CONFLICT
      return 'error';
    }
    $this->MUser->rollback();

    $socket = new HttpSocket(array(
      'timeout' => self::API_CALL_TIMEOUT
    ));
    $result = $socket->post(self::CONTRACT_ADD_URL,$data);

    $jobMailTemplateData = $this->MJobMailTemplate->find('all');
    $this->log('バリデーション引っかかった場合2',LOG_DEBUG);
    $this->log($jobMailTemplateData,LOG_DEBUG);

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

    //お客さん向け
    $sender = new MailSenderComponent();
    $sender->setFrom(MailSenderComponent::MAIL_SYSTEM_FROM_ADDRESS);
    $sender->setFromName('sinclo(シンクロ)');
    $sender->setTo($data['Contract']['user_mail_address']);
    $sender->setSubject($mailTemplateData[0]['MSystemMailTemplate']['subject']);
    $mailBodyData = str_replace(self::COMPANY_NAME, $data['MCompany']['company_name'], $mailTemplateData[0]['MSystemMailTemplate']['mail_body']);
    $mailBodyData = str_replace(self::USER_NAME, $data['MAgreements']['application_name'], $mailBodyData);
    $mailBodyData = str_replace(self::PASSWORD, $data['Contract']['user_password'], $mailBodyData);
    $mailBodyData = str_replace(self::MAIL_ADDRESS, $data['Contract']['user_mail_address'], $mailBodyData);
    $sender->setBody($mailBodyData);
    $sender->send();

    //会社向け
    $sender = new MailSenderComponent();
    $sender->setFrom(MailSenderComponent::MAIL_SYSTEM_FROM_ADDRESS);
    $sender->setFromName('sinclo(シンクロ)');
    $sender->setTo($data['Contract']['user_mail_address']);
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

  public function thanks() {

  }

   /* *
  * 検索画面
  * @return void
  * */
  public function remoteTermsOfService() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';

    $this->render('/Elements/Mtrial/remoteTermsOfService');
  }
}
