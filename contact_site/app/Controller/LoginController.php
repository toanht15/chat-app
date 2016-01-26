<?php
/**
 * CustomersController controller.
 * ログイン機能
 */
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
class LoginController extends AppController {
    public $uses = array('MUser');

    public function beforeFilter(){
      $this->Auth->allow('index');
      $this->set('title_for_layout', 'ログイン');
    }

    /* *
     * 一覧画面
     * @return void
     * */
    public function index() {
      $this->set('isLogin', false);
    }

    public function login() {
      if ($this->request->is('post')) {
        if ($this->Auth->login()) {
          parent::setUserInfo($this->Auth->user());
          return $this->redirect(array('controller' => 'Customers', 'action' => 'index'));
        }
      }
      $this->render('index');
    }
    public function logout(){
      $this->userInfo = [];
      $this->redirect($this->Auth->logout());
    }

}
