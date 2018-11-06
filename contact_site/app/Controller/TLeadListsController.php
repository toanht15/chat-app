<?php
/**
 *TDocumentsController  controller.
 * 資料設定
 */

class TLeadListsController extends AppController
{

  public function beforeFilter()
  {
    parent::beforeFilter();
    $this->set('title_for_layout', 'リードリスト出力');
  }

  /* *
   * 一覧画面
   * @return void
   * */
  public function index()
  {

  }
}