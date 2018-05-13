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
  const PLAN_NAME = "##PLAN_NAME##";
  const BEGIN_DATE = "##BEGIN_DATE##";
  const END_DATE = "##END_DATE##";
  const USABLE_USER_COUNT = "##USABLE_USER_COUNT##";
  const OPTION_COMPANY_INFO = "##OPTION_COMPANY_INFO##";
  const OPTION_SCENARIO = "##OPTION_SCENALIO##";
  const OPTION_CAPTURE = "##OPTION_CAPTURE##";
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
          'fields' => array('id','change_password_flg'),
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
          if($userInfo['permission_level'] != 99 && strtotime($today) > strtotime($trialEndDay)){
            $this->set('alertMessage',['type' => C_MESSAGE_OUT_OF_TERM_TRIAL, 'text'=>"トライアル期間を過ぎています"]);
            $this->render('index');
            return;
          }
        }
        if($mUserData[0]['MUser']['change_password_flg'] == C_NO_CHANGE_PASSWORD_FLG) {
          $this->Session->write('editPass', 'true');
          $this->redirect(['action' => 'editPassword']);
        }
        // ログイン失敗カウントを消す
        $this->MUser->resetErrorCount($mUserData[0]['MUser']);
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
      else {
        // ログイン失敗カウントを増やす
        $options = array('conditions' => array('MUser.mail_address' => $this->request->data['MUser']['mail_address']));
        $lockedUser = $this->MUser->find('first', $options);
        if(!empty($lockedUser) && $lockedUser['MUser']['error_count'] >= self::CONTINUOUS_ERROR_COUNT) {
          if(strtotime($lockedUser['MUser']['locked_datetime']) + self::RETRY_INTERVAL_AFTER_LOCKED_SEC > time()) {
            $this->set('alertMessage',['type' => C_MESSAGE_OUT_OF_TERM_TRIAL, 'text'=>"このアカウントはロックされています。しばらく経ってからやり直してください"]);
          } else {
            // ロック情報をリセットして再帰的に呼び出し
            $this->MUser->resetErrorCount($lockedUser['MUser']);
            $this->login();
          }
        } else {
          if(!empty($lockedUser)) {
            $this->MUser->incrementErrorCount($lockedUser['MUser']);
          }
          $this->set('alertMessage',['type' => C_MESSAGE_OUT_OF_TERM_TRIAL, 'text'=>"メールアドレスまたはパスワードが正しくありません"]);
        }
        $this->render('index');
        return;
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

          $mailType = "false";
          //無料トライアルの場合
          if($companyData['MCompany']['trial_flg'] == 1) {
            foreach($mailTemplateData as $key => $mailTemplate) {
              if($mailTemplate['MSystemMailTemplate']['id'] == C_AFTER_FREE_PASSWORD_CHANGE_TO_CUSTOMER) {
                $mailType = $key;
              }
            }
          }
          else {
            //いきなり本契約の場合
            foreach($mailTemplateData as $key => $mailTemplate) {
              if($mailTemplate['MSystemMailTemplate']['id'] == C_AFTER_PASSWORD_CHANGE_TO_CUSTOMER) {
                $mailType = $key;
              }
            }
          }

          if($mailType !== 'false') {
            $data['MAgreement']['user_mail_address'] = $inputData['MUser']['mail_address'];
            $replaceData = array(
              'MCompany' => $companyData['MCompany'],
              'MAgreement' => $agreementData['MAgreement']
            );
            $mailBodyData = $this->replaceAllMailConstString($replaceData, $mailTemplateData[$mailType]['MSystemMailTemplate']['mail_body']);

            //お客さん向け
            $sender = new MailSenderComponent();
            $sender->setFrom(self::ML_MAIL_ADDRESS);
            $sender->setFromName($mailTemplateData[$mailType]['MSystemMailTemplate']['sender']);
            $sender->setTo($inputData['MUser']['mail_address']);
            $sender->setSubject($mailTemplateData[$mailType]['MSystemMailTemplate']['subject']);
            $sender->setBody($mailBodyData);
            $sender->send();
          }

          //会社(メディアリンク)向けにメール
          $sender = new MailSenderComponent();
          $sender->setFrom(self::ML_MAIL_ADDRESS);
          $sender->setFromName('sinclo（シンクロ）');
          $sender->setTo(self::ML_MAIL_ADDRESS);
          $sender->setSubject($mailTemplateData[2]['MSystemMailTemplate']['subject']);
          $mailBodyData = $mailTemplateData[2]['MSystemMailTemplate']['mail_body'];
          $sender->setSubject($mailTemplateData[2]['MSystemMailTemplate']['subject']);
          $replaceData = array(
            'MCompany' => $companyData['MCompany'],
            'MAgreement' => $agreementData['MAgreement']
          );
          $mailBodyData = $this->replaceAllMailConstString($replaceData, $mailTemplateData[2]['MSystemMailTemplate']['mail_body']);

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

  private function replaceAllMailConstString($data, $mailTemplateData) {
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
    $mailBodyData = $this->replaceConstToString($data['MCompany']['company_name'],self::COMPANY_NAME, $mailTemplateData);
    $mailBodyData = $this->replaceConstToString($data['MAgreement']['application_name'], self::USER_NAME, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($data['MAgreement']['business_model'], self::BUSINESS_MODEL, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($data['MAgreement']['application_department'], self::DEPARTMENT, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($data['MAgreement']['application_position'], self::POSITION, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($data['MAgreement']['user_mail_address'], self::MAIL_ADDRESS, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($data['MAgreement']['telephone_number'], self::PHONE_NUMBER, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($data['MAgreement']['installation_url'], self::URL, $mailBodyData);
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
    swtich(intval($planId)) {
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
      return $data['MAgreement']['trial_start_day'];
    } else {
      return $data['MAgreement']['agreement_start_day'];
    }
  }

  private function getEndDate($data) {
    if(intval($data['MCompany']['trial_flg']) === 1) {
      return $data['MAgreement']['trial_end_day'];
    } else {
      return $data['MAgreement']['agreement_end_day'];
    }
  }

  private function getOptionCompanyInfoEnabled($data) {
    if(!empty($data['MCompany']['options']['refCompanyData'])) {
      return '企業情報付与オプション：あり';
    } else {
      return '企業情報付与オプション：なし';
    }
  }

  private function getOptionChatbotScenario($data) {
    if(!empty($data['MCompany']['options']['chatbotScenario'])) {
      return 'チャットボットシナリオオプション：あり';
    } else {
      return 'チャットボットシナリオオプション：なし';
    }
  }

  private function getOptionLaCoBrowse($data) {
    if(!empty($data['MCompany']['options']['laCoBrowse'])) {
      return '画面キャプチャオプション：あり';
    } else {
      return '画面キャプチャオプション：なし';
    }
  }
}
