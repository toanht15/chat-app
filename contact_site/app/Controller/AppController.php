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
                    'fields' => array('username' => 'mail_address')
                )
            )
        )
    );

    public $helper = array('');
    public $uses = array('MWidgetSetting');

    public $userInfo;

    public function beforeFilter(){
        // ログイン情報をオブジェクトに格納
        if ( $this->Session->check('global.userInfo') ) {
            $this->userInfo = $this->Session->read('global.userInfo');
            $this->set('userInfo', $this->userInfo);
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
    }

    // public function beforeRender(){
    //     // 通知メッセージをセット
    //     if ($this->Session->check('global.message')) {
    //         $this->set('successMessage', $this->Session->read('global.message'));
    //         $this->Session->delete('global.message');
    //     }
    // }

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

        $status = $this->Session->read('widget.operator.status');
        if ( $status === C_OPERATOR_PASSIVE ) {
            $status = C_OPERATOR_ACTIVE;
        }
        else {
            $status = C_OPERATOR_PASSIVE;
        }
        $this->Session->write('widget.operator.status', $status);
		return new CakeResponse(array('body' => json_encode(['status' => $status])));

    }

    // /**
    //  * 通知メッセージをセッションに保存
    //  * @param $type int (1:success, 2:error, 3:notice) 通知の種類
    //  * @param $text string メッセージ本文
    //  * */
    // public function renderMessage($type, $text){
    //     $this->Session->write('global.message', ['type'=>$type, 'text' => $text]);
    // }

}
