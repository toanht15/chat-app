<?php
/**
 * Created by PhpStorm.
 * User: ryo.hosokawa
 * Date: 2019/02/18
 * Time: 21:03
 */
class TChatbotDiagramsController extends AppController
{
  public function beforeFilter()
  {
    parent::beforeFilter();
    $this->set('title_for_layout', 'チャットツリー設定');
  }

  public function index() {

  }

  public function add() {

  }
}