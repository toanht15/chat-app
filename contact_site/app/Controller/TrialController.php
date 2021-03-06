<?php
/**
 * TrialController controller.
 * 無料トライアル登録画面
 */
App::uses('HttpSocket', 'Network/Http', 'Component', 'Controller', 'Utility/Validation');
class TrialController extends AppController {

  const CONTRACT_ADD_URL = "http://127.0.0.1:81/Contract/add";
//  const ML_MAIL_ADDRESS= "cloud-service@medialink-ml.co.jp";
  const ML_MAIL_ADDRESS= "masashi.shimizu@medialink-ml.co.jp";
    const ML_MAIL_ADDRESS_AND_ALEX = "cloud-service@medialink-ml.co.jp";
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
  private $tuIps = array('150.60.30.56','150.60.30.57','150.60.30.58','150.60.30.59');
  // フリーメールアドレスドメイン
  private $freeMailaddressDomains = array(
    'gmail.com',
    'yahoo.co.jp',
    'outlook.jp',
    'outlook.com',
    'hotmail.co.jp',
    'excite.co.jp',
    'aol.jp',
    'biglobe.ne.jp',
    'zoho.com',
    'yandex.com',
    'mail.ru',
    'inbox.ru',
    'list.ru',
    'bk.ru',
    'dqnwara.com'
  );

  // 携帯電話ドメイン
  private $careerDomains = array(
    'ezweb.ne.jp',
    'ido.ne.jp',
    'biz.ezweb.ne.jp',
    'augps.ezweb.ne.jp',
    'uqmobile.jp',
    'docomo.ne.jp',
    'mopera.net',
    'dwmail.jp',
    'pdx.ne.jp',
    'wcm.ne.jp',
    'willcom.com',
    'y-mobile.ne.jp',
    'emnet.ne.jp',
    'emobile-s.ne.jp',
    'emobile.ne.jp',
    'ymobile1.ne.jp',
    'ymobile.ne.jp',
    'jp-c.ne.jp',
    'jp-d.ne.jp',
    'jp-h.ne.jp',
    'jp-k.ne.jp',
    'jp-n.ne.jp',
    'jp-q.ne.jp',
    'jp-r.ne.jp',
    'jp-s.ne.jp',
    'jp-t.ne.jp',
    'sky.tkc.ne.jp',
    'sky.tkk.ne.jp',
    'sky.tu-ka.ne.jp',
    'disney.ne.jp',
    'i.softbank.jp',
    'softbank.ne.jp',
    'vodafone.ne.jp'
  );

  public function beforeFilter(){
    parent::beforeFilter();
    $this->Auth->allow(['index','add','thanks','check','remoteTermsOfService']);
    //開発環境
    if(Configure::read('debug') == 2) {
      header('Access-Control-Allow-Origin: *');
    }
    //テスト環境
    else if($_SERVER['HTTP_HOST'] == 'ml1.sinclo.jp') {
      if($_SERVER['HTTP_ORIGIN'] == 'http://127.0.0.1:81/Contract/add' || $_SERVER['HTTP_ORIGIN'] == 'http://192.168.1.120') {
        header("Access-Control-Allow-Origin: ".$_SERVER['HTTP_ORIGIN']);
      } else if(array_search($_SERVER['X-FORWARDED-FOR'], $this->tuIps)) {
        header('Access-Control-Allow-Origin: *');
      }
    }
    //本番環境
    else if($_SERVER['HTTP_HOST'] == 'sinclo.jp') {
     if($_SERVER['HTTP_ORIGIN'] == 'http://127.0.0.1:81/Contract/add' || $_SERVER['HTTP_ORIGIN'] == 'https://sinclo.medialink-ml.co.jp') {
       header("Access-Control-Allow-Origin: ".$_SERVER['HTTP_ORIGIN']);
     } else if(array_search($_SERVER['X-FORWARDED-FOR'], $this->tuIps)) {
       header('Access-Control-Allow-Origin: *');
     }
    }
  }

  /* *
   * 基本設定ページ
   * @return void
   * */
  public function index() {
    $businessModel = Configure::read('businessModelType');
    $this->set('businessModel',$businessModel);
    $this->set('title_for_layout', '無料トライアル登録画面');
    session_destroy();
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
    $data['MCompany']['options']['chatbotTreeEditor'] = true;
    $data['MAgreements']['trial_start_day'] = date('Y-m-d');
    $data['MAgreements']['trial_end_day'] = date('Y-m-d', strtotime('+13 day', time()));
    $data['MAgreements']['agreement_start_day'] = '';
    $data['MAgreements']['agreement_end_day'] = '';
    $data['MAgreements']['administrator_department'] = $data['MAgreements']['application_department'];
    $data['MAgreements']['administrator_position'] = $data['MAgreements']['application_position'];
    $data['MAgreements']['administrator_name'] = $data['MAgreements']['application_name'];
    $data['MAgreements']['administrator_mail_address'] = $data['MAgreements']['application_mail_address'] = $data['Contract']['user_mail_address'];
    $this->MUser->set($data['MUser']);
    if(!$this->MUser->validates()) {
      $this->log('登録時バリデーションエラー：'.var_export($this->MUser->validationErrors, TRUE).'　データ：'.var_export($data['MUser'], TRUE), LOG_WARNING);
      // 画面に返す
      $this->response->statusCode(409);
      $returnData = json_encode([
        'success' => false,
        'message' => $this->MUser->validationErrors
      ]);
      $this->log('返却データ：'.$returnData, LOG_WARNING);
      return $returnData;
    }

    $socket = new HttpSocket(array(
      'timeout' => self::API_CALL_TIMEOUT
    ));
    $result = $socket->post(self::CONTRACT_ADD_URL,$data,array('header' => array('X-Forwarded-Port' => 443)));

    if(json_decode($result->code) === 409) {
      $this->response->statusCode(409);
      return json_encode([
        'success' => false,
        'message' => 'メール送信に失敗しました。システム管理者にお問い合わせください。'
      ]);
    }

    $socket = new HttpSocket(array(
      'timeout' => self::API_CALL_TIMEOUT
    ));
    $soketResult = $socket->get(C_NODE_SERVER_ADDR.'/socketCtrl/refreshCompanyList',$data,array('header' => array('X-Forwarded-Port' => 443)));
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
      $this->log('入力時バリデーションエラー：'.var_export($this->MUser->validationErrors, TRUE).'　データ：'.var_export($data['MUser'], TRUE), LOG_WARNING);
      $errorMessage = $this->MUser->validationErrors;
      return $errorMessage['mail_address'][0];
    } else if(!$this->isFreeAddressChk($data['MUser'])) {
      return 'フリーアドレスのご利用はできません。';
    } else if(!$this->isCareerDomainChk($data['MUser'])) {
      return '携帯電話のメールアドレスのご利用はできません。';
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

  private function isFreeAddressChk($field = array()) {
    foreach($this->freeMailaddressDomains as $k => $v){
      if(preg_match('/(@'.$v.'$)/', $field['mail_address'])) {
        return false;
      }
    }
    return true;
  }

  private function isCareerDomainChk($field=array()) {
    forEach($this->careerDomains as $index => $domain) {
      if(preg_match('/(@'.$domain.'$)/', $field['mail_address'])) {
        return false;
      }
    }
    return true;
  }
}
