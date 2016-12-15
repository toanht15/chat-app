<?php
/**
 * CustomersController controller.
 * ログイン機能
 */
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
class LoginController extends AppController {
  public $uses = ['MUser'];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->Auth->allow(['index', 'logout', 'loginCheck','remoteSaveEntryForm']);
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
        $userInfo = $this->_setRandStr($this->Auth->user());
        parent::setUserInfo($userInfo);
        return $this->redirect(['controller' => 'Customers', 'action' => 'index']);
      }
    }
    $this->render('index');
  }

  public function logout(){
    $this->userInfo = [];
    $this->Session->destroy();
    if ( $this->Auth->logout() ) {
    $this->redirect($this->Auth->logout());
    }
    else {
    $this->redirect(['action' => 'index']);
    }
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

}
