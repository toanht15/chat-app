<?php
/**
 * LoginController controller.
 * ログイン機能
 */
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
class LoginController extends AppController {
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
  public $components = ['MailSender'];
  public $uses = ['MUser','TLogin', 'MIpFilterSetting','MCompany','MAgreement','MSystemMailTemplate'];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->Auth->allow(['index', 'logout', 'loginCheck','editPassword']);
    $this->set('title_for_layout', 'ログイン');

    $notSupportBrowser = false;
    if ( preg_match("/Trident\/[1-5]/i", $_SERVER['HTTP_USER_AGENT']) ) {
    $notSupportBrowser = true;
    }

    $this->set('notSupportBrowser', $notSupportBrowser);
  }

  /* *
   * ログイン画面
   * @return void
   * */
  public function index() {
    $this->set('isLogin', false);
    $this->Session->destroy();
  }

  public function login() {
    if ($this->request->is('post')) {
      if ($this->Auth->login()) {
        //ログイン情報を送信
        $ipAddress = $this->request->clientIp(false);
        $userAgent = $this->request->header('User-Agent');
        if(!$this->isValidIpFilter($ipAddress, $this->Auth->user())) {
          $this->processLogout();
          $this->set('alertMessage', ['type'=>C_MESSAGE_TYPE_ERROR, 'text' => "この端末のIPアドレスからはログインできません"]);
          $this->request->data['MUser']['password'] = '';
          $this->render('index');
          return;
        }
        $userInfo = $this->_setRandStr($this->Auth->user());
        parent::setUserInfo($userInfo);
        //トライアル期間中かチェック
        $trialCompany = $this->MCompany->find('all', [
          'fields' => '*',
          'conditions' => [
            'id' => $userInfo['MCompany']['id'],
            'trial_flg' => C_TRIAL_FLG
          ],
        ]);
        $mAgreementData = $this->MAgreement->find('all', [
          'fields' => 'trial_end_day',
          'conditions' => [
            'm_companies_id' => $userInfo['MCompany']['id']
          ],
        ]);
        $mUserData = $this->MUser->find('all', [
          'fields' => 'change_password_flg',
          'conditions' => [
            'm_companies_id' => $userInfo['MCompany']['id'],
            'mail_address' => $this->request->data['MUser']['mail_address']
          ],
        ]);
        if(!empty($trialCompany)) {
          //今日の日程
          $today = date("Y/m/d");
          //トライアル期間終了日
          $trialEndDay = date("Y/m/d",strtotime($mAgreementData[0]['MAgreement']['trial_end_day']));
          if(strtotime($today) > strtotime($trialEndDay)){
            $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>"トライアル期間を過ぎています"]);
            $this->render('index');
            return;
          }
        }
        if($mUserData[0]['MUser']['change_password_flg'] == C_NO_CHANGE_PASSWORD_FLG) {
          $this->Session->write('editPass', 'true');
          $this->redirect(['action' => 'editPassword']);
        }
        $loginInfo['TLogin']['m_companies_id'] = $userInfo['MCompany']['id'];
        $loginInfo['TLogin']['m_users_id'] = $userInfo['id'];
        $loginInfo['TLogin']['ip_address'] = $ipAddress;
        $loginInfo['TLogin']['user_agent'] = $userAgent;
        $loginInfo['TLogin']['created'] = date("Y/m/d H:i:s");
        $this->request->data['MUser']['password'] = '';
        $this->TLogin->begin();
        $this->TLogin->set($loginInfo);
        if($this->TLogin->save()) {
          $this->TLogin->commit();
        }
        else {
          $this->TLogin->rollback();
        }
        return $this->redirect(['controller' => 'Customers', 'action' => 'index']);
      }
    }
    $this->request->data['MUser']['password'] = '';
    $this->render('index');
    return;
  }

  public function logout(){
    if ( $this->processLogout() ) {
      $this->redirect($this->Auth->logout());
    }
    else {
      $this->redirect(['action' => 'index']);
    }
  }

  public function editPassword(){
    if ( $this->request->is('post') ) {
      $inputData = $this->request->data;
      $inputData['MUser']['change_password_flg'] = C_CHANGE_PASSWORD_FLG;
      $errors = [];
      $this->MUser->validate = $this->MUser->updateValidate;

      // パスワードチェックが問題なければ単独でバリデーションチェックのみ
      $this->MUser->set($inputData);
      $this->MUser->begin();

      if ( $this->MUser->validates() ) {
        // バリデーションチェックが成功した場合
        // 保存処理
        if ( $this->MUser->save($inputData, false) ) {
          $this->MUser->commit();
          $agreementData = $this->MAgreement->find('all', [
            'fields' => '*',
            'conditions' => [
              'm_companies_id' => $this->request->data['MUser']['m_companies_id']
            ],
          ]);
          $agreementData = $agreementData[0];
          $companyData = $this->MCompany->find('all', [
            'fields' => '*',
            'conditions' => [
              'id' => $this->request->data['MUser']['m_companies_id']
            ],
          ]);
          $companyData = $companyData[0];
          $mailTemplateData = $this->MSystemMailTemplate->find('all');
          $sender = new MailSenderComponent();
          $sender->setFrom(self::ML_MAIL_ADDRESS);
          $sender->setFromName('sinclo（シンクロ）');
          $sender->setTo(self::ML_MAIL_ADDRESS);
          $sender->setSubject($mailTemplateData[2]['MSystemMailTemplate']['subject']);
          $mailBodyData = $mailTemplateData[2]['MSystemMailTemplate']['mail_body'];
          $sender->setSubject($mailTemplateData[2]['MSystemMailTemplate']['subject']);
          $mailBodyData = str_replace(self::COMPANY_NAME, $companyData['MCompany']['company_name'], $mailTemplateData[2]['MSystemMailTemplate']['mail_body']);
          $mailBodyData = str_replace(self::USER_NAME, $agreementData['MAgreement']['application_name'], $mailBodyData);
          if($agreementData['MAgreement']['business_model'] == 1) {
            $agreementData['MAgreement']['business_model'] = 'BtoB';
          }
          if($agreementData['MAgreement']['business_model'] == 2) {
            $agreementData['MAgreement']['business_model'] = 'BtoC';
          }
          if($agreementData['MAgreement']['business_model'] == 3) {
            $agreementData['MAgreement']['business_model'] = 'どちらも';
          }
          $mailBodyData = str_replace(self::BUSINESS_MODEL, $agreementData['MAgreement']['business_model'], $mailBodyData);
          if(!empty($agreementData['MAgreement']['application_department'])) {
            $mailBodyData = str_replace(self::DEPARTMENT, $agreementData['MAgreement']['application_department'], $mailBodyData);
          }
          else {
            $mailBodyData = str_replace(self::DEPARTMENT, "", $mailBodyData);
          }
          if(!empty($agreementData['MAgreement']['application_position'])) {
            $mailBodyData = str_replace(self::POSITION, $agreementData['MAgreement']['application_position'], $mailBodyData);
          }
          else {
            $mailBodyData = str_replace(self::POSITION, "", $mailBodyData);
          }
          $mailBodyData = str_replace(self::MAIL_ADDRESS, $inputData['MUser']['mail_address'], $mailBodyData);
          $mailBodyData = str_replace(self::PHONE_NUMBER, $agreementData['MAgreement']['telephone_number'], $mailBodyData);
          if(!empty($agreementData['MAgreement']['installation_url'])) {
            $mailBodyData = str_replace(self::URL, $agreementData['MAgreement']['installation_url'], $mailBodyData);
          }
          else {
            $mailBodyData = str_replace(self::URL, "", $mailBodyData);
          }
          if(!empty($agreementData['MAgreement']['note'])) {
            $mailBodyData = str_replace(self::OTHER, $agreementData['MAgreement']['note'], $mailBodyData);
          }
          else {
            $mailBodyData = str_replace(self::OTHER, "", $mailBodyData);
          }
          $sender->setBody($mailBodyData);
          $sender->send();
          $this->set('alertMessage', ['type' => C_MESSAGE_TYPE_SUCCESS, 'text' => Configure::read('message.const.saveSuccessful')]);
          $this->redirect(['action' => 'index']);
        }
        if ( empty($errors) ) {
          $this->set('alertMessage', ['type' => C_MESSAGE_TYPE_SUCCESS, 'text' => Configure::read('message.const.saveSuccessful')]);
        }
        else {
          $this->set('alertMessage', ['type' => C_MESSAGE_TYPE_ERROR, 'text' => Configure::read('message.const.saveFailed')]);
        }
      }
      else {
        $errors = $this->MUser->validationErrors;
        if(!empty($errors['new_password']) && !empty($errors['confirm_password'])) {
          $this->set('errorNumbers',2);
        }
        else if(!empty($errors['new_password']) && empty($errors['confirm_password'])) {
          $this->set('errorNumbers',1);
        }
        else if(empty($errors['new_password']) && !empty($errors['confirm_password'])) {
          $this->set('errorNumbers',1);
        }
        return $errors;
      }
    }
    else {
      $editPass = $this->Session->read('editPass');
      //初期パスワードを変更していない場合
      if($editPass == 'true') {
        $this->data = $this->MUser->read(null, $this->userInfo['id']);
        $this->Session->destroy();
        $this->Session->write('editPass', 'true');
        $this->set('errorNumbers',0);
      }
      else {
        $this->Session->destroy();
        $this->redirect(['action' => 'index']);
        return;
      }
    }
  }

  private function processLogout() {
    $this->userInfo = [];
    $this->Session->destroy();
    return $this->Auth->logout();
  }

  public function loginCheck(){
    Configure::write('debug', 2);

    // 専用ログファイル
    $dir = ROOT.DS.APP_DIR.DS.'tmp'.DS.'loginCheck';
    // 専用ログファイルへのパスチェック
    if ( !file_exists($dir) ) {
      // 専用ログファイルが無ければ生成
      if (!mkdir($dir, 0766, true)) {
        // 専用ログファイルの生成に失敗した場合
        $this->log('ログディレクトリの作成に失敗しました', LOG_DEBUG);
        $this->redirect('/Login/logout'); // ログアウトさせる
      }
    }
    // Ajax失敗時のエラー結果のデコード
    $jsonData = (array)json_decode($this->request->query['error']);
    $dt = new DateTime(); // 今日の日付
    $fileName = $dt->format('Ymd').".log"; // 今日の日付をタイトルに
    $url = $this->referer(['controller' => 'Customers', 'action' => 'index']); // 処理が終わったら、基本は元のページに飛ぶ

    /* ログの記入 */
    $handle = fopen($dir.DS.$fileName, "a"); // ファイルを生成
    fwrite($handle, $dt->format('[Y-m-d H:i:s] ')."------------------------------".PHP_EOL);
    // ログイン状態を追記
    if ( $this->Auth->user() ) {
      $user = $this->Auth->user();
      fwrite($handle, " ログイン状態：ログイン中".PHP_EOL);
    }
    else {
      fwrite($handle, " ログイン状態：ログアウト".PHP_EOL);
      // ログアウト処理
      $this->userInfo = [];
      $this->Session->destroy();
      $this->renderMessage(C_MESSAGE_TYPE_ALERT, "タイムアウトしました");
      $url = ['controller'=>'Login', 'action' => 'index'];
    }
    foreach($jsonData as $k => $v){
      $v = ( is_object($v) ) ? json_encode($v) : $v;
      fwrite($handle, " [".$k."]: ".$v.PHP_EOL);
    }
    fwrite($handle, "----------------------------------------------------".PHP_EOL.PHP_EOL);
    fclose($handle);
    /* ログの記入終了 */

    // 元アクションへ移動
    $this->redirect($url);
  }

  private function _setRandStr($userInfo){
    $str = abs(mt_rand(111111111111, 99999999999999));
    $params = [
    'id' => $userInfo['id'],
    'session_rand_str' => $str
    ];
    $this->MUser->set($params);
    if ( !$this->MUser->save(['modified' => false]) ) {
    $this->logout();
    }

    $userInfo = $this->MUser->read(null, $this->userInfo['id']);
    $ret = $userInfo['MUser'];
    $ret['MCompany'] = $userInfo['MCompany'];
    return $ret;
  }


  public function phpinfo(){
    phpinfo();
    exit();
  }

  public function isValidIpFilter($ipAddress, $userInfo) {
    // FIXME ML用アカウントは必ずtrueにする
    if(strcmp($userInfo['permission_level'], C_AUTHORITY_SUPER) === 0 ) return true;
    $mcompany = $userInfo['MCompany'];
    $ipSettings = $this->MIpFilterSetting->find('first', array(
      'conditions' => array(
        'm_companies_id' => $mcompany['id']
      )
    ));
    if(empty($ipSettings) || !$ipSettings['MIpFilterSetting']['active_flg']) {
      // 設定なし or 無効設定
      return true;
    } else if($ipSettings['MIpFilterSetting']['active_flg']) {
      // 設定あり
      if($ipSettings['MIpFilterSetting']['filter_type'] === "1") { // FIXME 定数化（ホワイトリスト）
        return $this->isValidIpWhitelistFilter($ipAddress, $ipSettings['MIpFilterSetting']['ips']);
      } else if($ipSettings['MIpFilterSetting']['filter_type'] === "2") { // FIXME 定数化（ブラックリスト）
        return $this->isValidIpBlacklistFilter($ipAddress, $ipSettings['MIpFilterSetting']['ips']);
      } else {
        return false; // 設定が有効なのにfilter_typeが無いのは異常系
      }
    }
    // 異常系
    return false;
  }

  private function isValidIpWhitelistFilter($ip, $setting) {
    $result = false;
    foreach( explode("\n", trim($setting)) as $v ){
      if ( preg_match("/^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}$/", trim($v)) && strcmp($v, $ip) === 0 ) {
        $result = true;
        break;
      }
      else if(preg_match("/^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}\/[0-9]{1,3}$/", trim($v)) && $this->inCIDR($ip, $v)) {
        $result = true;
        break;
      }
    }
    return $result;
  }

  private function isValidIpBlacklistFilter($ip, $setting) {
    $result = true;
    foreach( explode("\n", trim($setting)) as $v ){
      if ( preg_match("/^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}$/", trim($v)) && strcmp($v, $ip) === 0) {
        $result = false;
        break;
      }
      else if(preg_match("/^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}\/[0-9]{1,3}$/", trim($v)) && $this->inCIDR($ip, $v)) {
        $result = false;
        break;
      }
    }
    return $result;
  }

  /**
   * 指定のIPアドレス（v4）がcidrで指定したIPレンジの中にあるかどうかを判定する
   * @see http://unoh.github.io/2009/03/18/ip.html
   * @param $ip
   * @param $cidr
   * @return bool
   */
  private function inCIDR($ip, $cidr) {
    list($network, $mask_bit_len) = explode('/', $cidr);
    $host = 32 - $mask_bit_len;
    $netlong = ip2long($network);
    $ipnetlong = ip2long($ip);
    $net = ip2long($network) >> $host << $host; // 11000000101010000000000000000000
    $ip_net = ip2long($ip) >> $host << $host; // 11000000101010000000000000000000
    return $net === $ip_net;
  }

}
