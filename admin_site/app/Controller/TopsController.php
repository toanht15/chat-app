<?php
/**
 * TopsController controller.
 * ホーム
 */

App::uses('AppController', 'Controller');
class TopsController extends AppController {

public $uses = ['MAdministrator'];

  /**
  *初期画面
  *@return void
  */
  public function index() {
  }

  // TODO 初回テナントリリース時に削除
  /**
  *デザインサンプル画面
  *@return void
  */
  public function sample(){
  }
}
