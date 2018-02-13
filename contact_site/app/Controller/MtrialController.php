<?php
/**
 * MtrialController controller.
 * 無料トライアル登録画面
 */
App::uses('Controller', 'MUsers');
class MtrialController extends AppController {
  public $uses = ['MChatSetting', 'MUser','MOperatingHour'];

  public function beforeFilter(){
    parent::beforeFilter();
    //header("Access-Control-Allow-Origin: *");
    $this->header('Access-Control-Allow-Origin: http://127.0.0.1:81/Contract/add');
    $this->set('title_for_layout', '無料トライアル登録画面');
  }

  /* *
   * 基本設定ページ
   * @return void
   * */
  public function index() {
    $businessModel = Configure::read('businessModelType');
    $this->set('businessModel',$businessModel);
  }

  public function add() {
    $this->log('入っています',LOG_DEBUG);
    //$this->Html->url('http://127.0.0.1:81/Contract/add');
    $this->log($this->request->data,LOG_DEBUG);
    $data = $this->request->data;
    $this->redirect('http://admin.sinclo:81/Contract/add/'.$data);
  }

  public function thanks() {

  }
}
