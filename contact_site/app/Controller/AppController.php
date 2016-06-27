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
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    public $components = array(
        'Session',
        'Auth' => array(
            'loginAction' => array(
                'controller' => 'Login',
                'action' => 'login'
            ),
            'authError' => 'ログインに失敗しました。',
            'authenticate' => array(
                'Form' => array(
                    'userModel' => 'MUser',
                    'fields' => array('username' => 'mail_address'),
                    'scope' => array('MUser.del_flg' => 0)
                )
            )
        )
    );

    public $helpers = array('formEx');
    public $uses = array('MUser', 'MWidgetSetting');

    public $userInfo;
    public $coreSettings;

    public function beforeFilter(){
        // プロトコルチェック(本番のみ)
        if ( APP_MODE_DEV === false ) {
            $this->checkPort();
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
        $this->coreSettings = json_decode($this->userInfo['MCompany']['core_settings'], true);
        $this->set('coreSettings', $this->coreSettings);


        // コンフィグにユーザーIDを設定
        Configure::write('logged_user_id', $this->Auth->user('id'));
        // コンフィグに企業IDを設定
        Configure::write('logged_company_id', $this->userInfo['MCompany']['id']);
        // ウィジェットの情報をビューへ渡す
        $widgetInfo = $this->MWidgetSetting->coFind('first', []);

        /* オペレーター待ち状態 */
        // 在籍/退席
        $opStatus = C_OPERATOR_PASSIVE; // 退席（デフォルト）
        if ( !empty($widgetInfo['MWidgetSetting']['display_type']) && strcmp($widgetInfo['MWidgetSetting']['display_type'], C_WIDGET_DISPLAY_CODE_OPER) === 0 ) {
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

        /* 権限 */
        if (strcmp($this->userInfo['permission_level'], C_AUTHORITY_ADMIN) !== 0) {
            switch($this->name){
              // 管理者権限のみのページ
              case "MUsers":
              case "MWidgetSettings":
                // 一先ずトップ画面へ
                $this->redirect("/");
              default:
                break;
            }
        }

        /* 契約ごと使用可能ページ */
        switch($this->name){
            case "TAutoMessages":
                if (!$this->coreSettings["chat"]) {
                    $this->redirect("/");
                }
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
		return new CakeResponse(array('body' => json_encode(['status' => $status])));

    }

    /**
     * 通知メッセージをセッションに保存
     * @param $type int (1:success, 2:error, 3:notice) 通知の種類
     * @param $text string メッセージ本文
     * */
    public function renderMessage($type, $text){
        $this->Session->write('global.message', ['type'=>$type, 'text' => $text]);
    }

}
