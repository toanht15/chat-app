<?php
/**
 * CustomersController controller.
 * ログイン機能
 */
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
class LoginController extends AppController {
  public $uses = ['MAdministrator'];

  public function beforeFilter() {
    parent::beforeFilter();
    $this->Auth->allow(['index', 'login']);
    $this->set('title_for_layout', 'ログイン');
  }

  /* *
   * ログイン画面
   * @return void
   * */
  public function login() {
    if ($this->request->is('post')) {
      if ($this->Auth->login()) {
        $userInfo = $this->Auth->user();
        parent::setUserInfo($userInfo);
        $this->redirect(['controller' => 'Tops', 'action' => 'index']);
      }
    }
  }
}
