<?php
/**
 * CustomersController controller.
 * ログイン機能
 */
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
class LoginController extends AppController {
    public $uses = array('MUser');

    public function beforeFilter(){
      parent::beforeFilter();
      $this->Auth->allow(['index', 'logout']);
      $this->set('title_for_layout', 'ログイン');

      $notSupportBrowser = false;
      if ( preg_match("/Trident\[1-5]/i", $_SERVER['HTTP_USER_AGENT']) ) {
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
          return $this->redirect(array('controller' => 'Customers', 'action' => 'index'));
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
