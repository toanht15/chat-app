<?php
App::uses('AppController', 'Controller');
class TopsController extends AppController {
public $uses = ['MAdministrator'];

  /**
  *初期画面
  *@return void
  */
  public function index() {
    if($this->request->is('post')) {
      $this->MAdministrator->create();
      if($this->MAdministrator->save($this->request->data)) {
        $this->redirect(['action' => 'index']);
      }
      return;
    }
  }

  // TODO 初回テナントリリース時に削除
  /**
  *デザインサンプル画面
  *@return void
  */
  public function sample(){
  }

}
