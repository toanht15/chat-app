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
      //pr($this->request->data); //exit();
      if($this->MAdministrator->save($this->request->data)) {
        $this->redirect(['action' => 'index']);
      }
      return;
    }


}

}