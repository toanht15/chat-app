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
  public $uses = ['MUser','TMailTransmissionLog','MSystemMailTemplate','TSendSystemMailSchedule','MJobMailTemplate','MCompany'];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->Auth->allow(['index','add','thanks','check','remoteTermsOfService']);
    $this->header('Access-Control-Allow-Origin: http://127.0.0.1:81/Contract/add');
  }

  /* *
   * 基本設定ページ
   * @return void
   * */
  public function index() {
    $businessModel = Configure::read('businessModelType');
    $this->set('businessModel',$businessModel);
    $this->set('title_for_layout', '無料トライアル登録画面');
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
    $data['MCompany']['options']['chatbotScenario'] = true;
    $data['MAgreements']['trial_start_day'] = date('Y-m-d');
    $data['MAgreements']['trial_end_day'] = date('Y-m-d', strtotime('+13 day', time()));
    $data['MAgreements']['agreement_start_day'] = '';
    $data['MAgreements']['agreement_end_day'] = '';

    $socket = new HttpSocket(array(
      'timeout' => self::API_CALL_TIMEOUT
    ));
    $result = $socket->post(self::CONTRACT_ADD_URL,$data,array('header' => array('X-Forwarded-Port' => 443)));

    $socket = new HttpSocket(array(
      'timeout' => self::API_CALL_TIMEOUT
    ));
    $result = $socket->post(C_NODE_SERVER_ADDR.'/socketCtrl/refreshCompanyList',$data,array('header' => array('X-Forwarded-Port' => 443)));
  }

  /* *
   * サンクスページ
   * @return void
   * */
  public function thanks() {
    $this->set('title_for_layout', '無料トライアルを受付いたしました');
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
