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
  public $uses = ['MAdministrator','MCompany'];
  public $userInfo;

  public $components = [
    'Session',
    'Auth' => [
      //ログイン後の遷移先
      'loginRedirect' => [
        'controller' => 'Tops',
        'action' => 'index'
      ],
      //ログインしていない時に他ページへアクセスした場合
      'loginAction' => [
        'controller' => 'Login',
        'action' => 'login'
      ],
      //パスワードハッシュ化
      'authenticate' => [
        'Form' => [
          'userModel' => 'MAdministrator',
          'fields' => ['username' => 'mail_address'],
          'scope' => [
            'MAdministrator.del_flg' => 0
          ]
        ]
      ]
    ]
  ];

  /**
  *どのアクションが呼ばれてもはじめに実行される関数
  *@return void
  */
  public function beforeFilter(){
    // プロトコルチェック(本番のみ)
    if ( APP_MODE_DEV === false ) {
      $this->checkPort();
    }

    // 未ログインの場合は以降の処理を通さない
    if (!$this->Auth->user()) return false;

    // 通知メッセージをセット
    if ($this->Session->check('global.message')) {
      $this->set('alertMessage', $this->Session->read('global.message'));
      $this->Session->delete('global.message');
    }

    // ログイン情報をオブジェクトに格納
    if ( $this->Session->check('global.administratorInfo') ) {
      $this->userInfo = $this->Session->read('global.administratorInfo');
      //pr($this->userInfo); exit();
      $this->set('userInfo', $this->userInfo);
    }

    // コンフィグに企業IDを設定
    //Configure::write('logged_company_id', $this->userInfo['MCompany']['id']);

    //他のクラスでもbeforeFilterを使えるようにする
    parent::beforeFilter();
    //Authの情報をセット
    $this->set('auth',$this->Auth);
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

  public function setUserInfo($info){
    $this->userInfo = $info;
    $this->Session->write('global.administratorInfo', $info);
    $this->Session->write('global.tmpdata', $this->MCompany->find('all',array(
    'conditions'=>array(
        //'MCompany.company_key' => 'template'
    ),
    'fields'=>array('id','company_key'))
  ));
  }

  /**
  * 通知メッセージをセッションに保存
  * @param $type int (1:success, 2:error, 3:notice) 通知の種類
  * @param $text string メッセージ本文
  * */
  public function renderMessage($type, $text){
    $this->Session->write('global.message', ['type'=>$type, 'text' => $text]);
  }

  /**
  * jsonエンコード
  */
  public function jsonEncode($val) {
    return json_encode($val, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_FORCE_OBJECT );
  }
}
