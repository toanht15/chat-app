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
  public $uses = ['MAdministrator'];
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
    // 未ログインの場合は以降の処理を通さない
    if (!$this->Auth->user()) return false;

    // 通知メッセージをセット
    if ($this->Session->check('global.message')) {
      $this->set('alertMessage', $this->Session->read('global.message'));
      $this->Session->delete('global.message');
    }

    // ログイン情報をオブジェクトに格納
    if ( $this->Session->check('global.userInfo') ) {
      $this->userInfo = $this->Session->read('global.userInfo');
      $this->set('userInfo', $this->userInfo);
    }

    //他のクラスでもbeforeFilterを使えるようにする
    parent::beforeFilter();
    //Authの情報をセット
    $this->set('auth',$this->Auth);
  }

  public function setUserInfo($info){
    $this->Session->write('global.userInfo', $info);
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
