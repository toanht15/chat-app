<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package   app.Controller
 * @link    http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
  public $components = [
    'Session',
    'Auth' => [
      'loginAction' => [
        'controller' => 'Login',
        'action' => 'login'
      ],
      'authError' => 'ログインに失敗しました。',
      'authenticate' => [
        'Form' => [
          'userModel' => 'MUser',
          'fields' => ['username' => 'mail_address'],
          'scope' => [
            'MUser.del_flg' => 0
          ]
        ]
      ]
    ]
  ];

  public $helpers = ['formEx'];
  public $uses = ['MUser', 'MWidgetSetting'];

  public $userInfo;
  public $coreSettings;

  private $defaultCoreSettings = [
    C_COMPANY_USE_CHAT => false, // チャット機能有効
    C_COMPANY_USE_SYNCLO => false, // 画面共有機能有効
    C_COMPANY_USE_DOCUMENT => false, // ドキュメント共有機能有効
    C_COMPANY_USE_VIDEO_CHAT => false, // ビデオチャット機能有効（ただし未実装）
    C_COMPANY_USE_CHAT_LIMITER => false, // 同時対応数上限
    C_COMPANY_USE_HISTORY_EXPORTING => false, // 履歴エクスポート
    C_COMPANY_USE_HISTORY_DELETE => false, // 履歴削除
    C_COMPANY_USE_STATISTICS => false, // 統計
    C_COMPANY_USE_DICTIONARY_CATEGORY => false, // 定型文カテゴリ
    C_COMPANY_USE_LA_CO_BROWSE => false, // 画面キャプチャ共有
    C_COMPANY_USE_HIDE_REALTIME_MONITOR => false, // 通常時リアルタイムモニタ非表示
    C_COMPANY_USE_OPERATING_HOUR => false, //営業時間設定
    C_COMPANY_REF_COMPANY_DATA => false, // 企業情報参照（Landscape）
    C_COMPANY_USE_FREE_INPUT => false, //自由入力エリア
    C_COMPANY_USE_CV => false, //CV
    C_COMPANY_USE_AUTOMESSAGE_SEND_MAIL => false, //メール送信（オートメッセージ）
    C_COMPANY_USE_SEND_FILE => false, //ファイル送信
    C_COMPANY_USE_SECURITY_LOGIN_IP_FILTER => false, // セキュリティ設定（IP制限）
    C_COMPANY_USE_IMPORT_EXCEL_AUTO_MESSAGE => false, // オートメッセージExcelインポート
    C_COMPANY_USE_OPERATOR_PRESENCE_VIEW => false, // オペレータ在席状況確認
    C_COMPANY_USE_CHATBOT_SCENARIO => false, // シナリオ設定
  ];

  private $secretKey = 'x64rGrNWCHVJMNQ6P4wQyNYjW9him3ZK';

  // メディアリンクの企業情報を閲覧可能なcompany_keyのリスト
  private $viewableMLCompanyInfoList = ['medialink','59f6e3aa713de'];

  public function beforeFilter(){

    // プロトコルチェック(本番のみ)
    if ( APP_MODE_DEV === false && !( strpos($this->referer(), '/Customers/frame') )  ) {
      $this->checkPort();
    }

    //HTTPSの場合
    if(!empty($_SERVER['HTTP_X_FORWARDED_PORT']) && strcmp($_SERVER['HTTP_X_FORWARDED_PORT'],443) == 0){
      Configure::write('Session', array(
        'defaults' => 'php',
        'cookie' => 'CAKEPHP',
        'timeout' => 1440, // 24 hours
        'ini' => array(
          'session.gc_maxlifetime' =>  86400 // 24 hours
        )
      ));
      if(!empty($_COOKIE['CAKEPHP'])) {
        if(empty(session_get_cookie_params()['secure'])) {
          setcookie("CAKEPHP", $_COOKIE['CAKEPHP'], 0 ,"/","",1,1);
          $pass = $this->_createPass();
          setcookie("CAKE_HTTP", $pass , 0 ,"/","",0,1);
          copy('/var/lib/php/session/sess_'.$_COOKIE['CAKEPHP'] , '/var/lib/php/session/sess_'.$pass);
        }
      }
    }
    else {
      Configure::write('Session', array(
        'defaults' => 'php',
        'cookie' => 'CAKE_HTTP',
        'timeout' => 1440, // 24 hours
        'ini' => array(
          'session.gc_maxlifetime' =>  86400 // 24 hours
        )
      ));
    }

    // 通知メッセージをセット
    if ($this->Session->check('global.message')) {
      $this->set('alertMessage', $this->Session->read('global.message'));
      $this->Session->delete('global.message');
    }

    // 未ログインの場合は以降の処理を通さない
    if (!$this->Auth->user()) return false;

    // ログイン情報をオブジェクトに格納
    if ( $this->Session->check('global.userInfo') ) {
      $this->userInfo = $this->Session->read('global.userInfo');
      $this->set('userInfo', $this->userInfo);
    }

    // 多重ログインチェック
    if ( isset($this->userInfo['id']) && isset($this->userInfo['session_rand_str']) ) {
      $newInfo = $this->MUser->read(null, $this->userInfo['id']);
      if ( strcmp($this->userInfo['session_rand_str'], $newInfo['MUser']['session_rand_str']) !== 0 ) {
        $this->userInfo = [];
        $this->Session->destroy();
        $this->renderMessage(C_MESSAGE_TYPE_ALERT, Configure::read('message.const.doubleLoginFailed'));
        return $this->redirect(['controller'=>'Login', 'action' => 'index']);
      }
    }

    // 使用機能のセット
    if ( empty($this->userInfo['MCompany']['core_settings']) ) {
      $this->userInfo = [];
      $this->Session->destroy();
      return $this->redirect(['controller'=>'Login', 'action' => 'index']);
    }
    $this->coreSettings = $this->mergeCoreSettings(json_decode($this->userInfo['MCompany']['core_settings'], true));
    $this->set('coreSettings', $this->coreSettings);

    if(isset($this->coreSettings[C_COMPANY_REF_COMPANY_DATA]) && $this->coreSettings[C_COMPANY_REF_COMPANY_DATA]) {
      $this->userInfo['accessToken'] = 'x64rGrNWCHVJMNQ6P4wQyNYjW9him3ZK';
      $this->Session->write('global.userInfo.accessToken');
    }

    // コンフィグにユーザーIDを設定
    Configure::write('logged_user_id', $this->Auth->user('id'));
    // コンフィグに企業IDを設定
    Configure::write('logged_company_id', $this->userInfo['MCompany']['id']);
    // ウィジェットの情報をビューへ渡す
    $widgetInfo = $this->MWidgetSetting->coFind('first', []);

    /* オペレーター待ち状態 */
    // 在籍/退席
    $opStatus = C_OPERATOR_PASSIVE; // 退席（デフォルト）
    if ( (!empty($widgetInfo['MWidgetSetting']['display_type']) && (isset($this->coreSettings[C_COMPANY_USE_CHAT]) && $this->coreSettings[C_COMPANY_USE_CHAT])
      && (strcmp($widgetInfo['MWidgetSetting']['display_type'], C_WIDGET_DISPLAY_CODE_OPER) === 0 ||
      strcmp($widgetInfo['MWidgetSetting']['display_type'], C_WIDGET_DISPLAY_CODE_SHOW) === 0 ||
     strcmp($widgetInfo['MWidgetSetting']['display_type'], C_WIDGET_DISPLAY_CODE_TIME) === 0))
      || (!empty($widgetInfo['MWidgetSetting']['display_type']) && (empty($this->coreSettings[C_COMPANY_USE_CHAT]))
      && strcmp($widgetInfo['MWidgetSetting']['display_type'], C_WIDGET_DISPLAY_CODE_OPER) === 0) ) {
      // セッションから
      if ( $this->Session->check('widget.operator.status') ) {
        $opStatus = $this->Session->read('widget.operator.status');
      }
      else {
        $this->Session->write('widget.operator.status', C_OPERATOR_PASSIVE);
      }

      $this->set('widgetCheck', C_OPERATOR_ACTIVE); // オペレーターの在籍/退席を使用するか
      $this->set('opStatus', $opStatus);
    }
    else {
      $this->set('widgetCheck', C_OPERATOR_PASSIVE);
    }
    $this->set('displayType', $widgetInfo['MWidgetSetting']['display_type']);

    /* 権限 */
    if ( !(strcmp($this->userInfo['permission_level'], C_AUTHORITY_SUPER) === 0 || strcmp($this->userInfo['permission_level'], C_AUTHORITY_ADMIN) === 0) ) {
      switch($this->name){
        // 管理者権限のみのページ
        case "MUsers":
        case "MWidgetSettings":
        case "TAutoMessages":
        case "TCampaigns":
        case "DisplayExclusions":
        case "MFileTransferSetting":
        // 一先ずトップ画面へ
        $this->redirect("/");
        default:
        break;
      }
    }

    /* 管理者権限かどうかを渡す */
    $this->set('adminFlg', (strcmp($this->userInfo['permission_level'], C_AUTHORITY_SUPER) === 0 || strcmp($this->userInfo['permission_level'], C_AUTHORITY_ADMIN) === 0 ));

    /* 契約ごと使用可能ページ */
    switch($this->name){
      case "TDictionaries":
      case "TAutoMessages":
      case "MChatNotifications":
      case "MChatSettings":
      case "MFileTransferSetting":
        if ( !(isset($this->coreSettings[C_COMPANY_USE_CHAT]) && $this->coreSettings[C_COMPANY_USE_CHAT]) ) {
          $this->redirect("/");
        }
        break;
      case "TDocuments":
        if ( !(isset($this->coreSettings[C_COMPANY_USE_DOCUMENT]) && $this->coreSettings[C_COMPANY_USE_DOCUMENT]) ) {
          $this->redirect("/");
        }
        break;
      case "Statistics":
        if ( !(isset($this->coreSettings[C_COMPANY_USE_CHAT]) && $this->coreSettings[C_COMPANY_USE_CHAT])
          && isset($this->coreSettings[C_COMPANY_CHAT_BASIC_PLAN]) && $this->coreSettings[C_COMPANY_CHAT_BASIC_PLAN] ) {
          $this->redirect("/");
        }
        break;
    }
  }

  /**
   * checkPort プロトコルチェック
   * @return void
   * */
  public function checkPort(){
    $params = $this->request->params;
    $query = $this->request->query;

    switch($params['controller'] . "/" . $params['action']){
      case "Customers/frame":
        $port = 80;
        $protocol = "http";
        break;
      default:
        $port = 443;
        $protocol = "https";
    }

    // 推奨のプロトコルではなかった場合
    if(strcmp($_SERVER['HTTP_X_FORWARDED_PORT'],$port) !== 0){
      $queryStr = "";
      $url = $protocol . "://".env('SERVER_NAME').$this->here;
      foreach((array)$query as $key => $val){
        if ( empty($queryStr) ) {
          $queryStr = "?";
        }
        else {
          $queryStr .= "&";
        }
        if ( strcmp('url', $key) === 0 ) {
          $queryStr .= $key . "=" . urlencode($val);
        }
        else {
          $queryStr .= $key . "=" . $val;
        }
      }

      // 推奨のプロトコルでリダイレクト
      $this->redirect($url.$queryStr);
    }

  }

  /**
   * recurse_array_HTML_safe
   * 配列の中身をまるっとhtmlエスケープする
   * @param $arr エスケープしたい配列 or 文字列
   * @return void
   **/
  public function recurse_array_HTML_safe(&$arr) {
      foreach ($arr as $key => $val) {
        if (is_array($val)) {
          $this->recurse_array_HTML_safe($arr[$key]);
        }
        else {
          $arr[$key] = htmlspecialchars($val, ENT_QUOTES);
        }

      }
  }

  public function setUserInfo($info){
    $this->userInfo = $info;
    $this->Session->write('global.userInfo', $info);
  }

  /**
   * オペレーターの在籍状況を変更する
   * */
  public function remoteChangeOperatorStatus(){
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $status = C_OPERATOR_PASSIVE;
    if ( $this->Session->check('widget.operator.status') ) {
      $status = $this->Session->read('widget.operator.status');
    }
    if ( $status == C_OPERATOR_ACTIVE ) {
      $status = C_OPERATOR_PASSIVE;
    }
    else {
      $status = C_OPERATOR_ACTIVE;
    }
    $this->Session->write('widget.operator.status', $status);
    return new CakeResponse(['body' => json_encode(['status' => $status])]);

  }

  /**
   * 通知メッセージをセッションに保存
   * @param $type int (1:success, 2:error, 3:notice) 通知の種類
   * @param $text string メッセージ本文
   * */
  public function renderMessage($type, $text){
    $this->Session->write('global.message', ['type'=>$type, 'text' => $text]);
  }

  public function setChatValiable($val) {
    // 企業名取得
    $widgetSettings = $this->MWidgetSetting->find('first', ['conditions' => ['m_companies_id' => $this->userInfo['MCompany']['id']]]);
    $styleSettings = (array)json_decode($widgetSettings['MWidgetSetting']['style_settings']);
    $ret = $val;
    // 企業名
    $ret = str_replace("{!company}", $styleSettings['subTitle'], $ret);
    // 表示名
    $ret = str_replace("{!user}", $this->userInfo['display_name'], $ret);
    return $ret;
  }

  public function isViewableMLCompanyInfo() {
    if(empty($this->userInfo)) return false;
    return in_array($this->userInfo['MCompany']['company_key'], $this->viewableMLCompanyInfoList);
  }

  /**
   * jsonエンコード
   */
  public function jsonEncode($val) {
    return json_encode($val, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_FORCE_OBJECT );
  }

  /**
   * カラフルなvar_dump出力
   */
  public function hdump($var) {
      highlight_string("<?php\n" . var_export($var, true));
  }

  /* *
   * imagecreatefrom**
   * */
  public function imageCreate($extension, $file){
    if ( preg_match('/^png$/i', $extension) ) {
      return imagecreatefrompng($file);
    }
    if ( preg_match('/^jpeg|jpg$/i', $extension) ) {
      return imagecreatefromjpeg($file);
    }
  }

  /* *
   * image**
   * */
  public function imageOut($extension, $file, $saveFile){
    if ( preg_match('/^png$/i', $extension) ) {
      return imagepng($file, $saveFile);
    }
    if ( preg_match('/^jpeg|jpg$/i', $extension) ) {
      return imagejpeg($file, $saveFile);
    }
  }

  protected function mergeCoreSettings($coreSettings) {
    return array_merge($this->defaultCoreSettings, $coreSettings);
  }

  protected function isValidAccessToken($token) {
    if(strcmp($this->secretKey, $token) !== 0) {
      throw new Exception('アクセストークンが不正です', 400);
    }
  }

  /**
   * @return mixed
   */
  protected function getRequestJSONData()
  {
    $data = file_get_contents('php://input');
    $this->log('リクエストデータ: '. $data, 'mail-request');
    $jsonObj = json_decode($data, TRUE);
    return $jsonObj;
  }

  protected function prettyByte2Str($bytes)
  {
    if ($bytes >= 1073741824) {
      $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
      $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
      $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
      $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
      $bytes = $bytes . ' byte';
    } else {
      $bytes = '0 bytes';
    }
    return $bytes;
  }

  private function _createPass(){
    $pwd = array();
    $pwd_strings = array(
      "sletter" => range('a', 'z'),
      "number"  => range('0', '9'),
    );

    //logic
    while (count($pwd) < 32) {
      // 4種類必ず入れる
      if (count($pwd) < 2) {
          $key = key($pwd_strings);
          next($pwd_strings);
      } else {
      // 後はランダムに取得
          $key = array_rand($pwd_strings);
      }
      $pwd[] = $pwd_strings[$key][array_rand($pwd_strings[$key])];
    }
    // 生成したパスワードの順番をランダムに並び替え
    shuffle($pwd);

    return implode($pwd);
  }
}
