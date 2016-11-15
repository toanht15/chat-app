<?php
/**
 * TopsController controller.
 * ホーム
 */

App::uses('AppController', 'Controller');
class TopsController extends AppController {

public $uses = ['MCompany','MUser'];

  public $paginate = [
    'MCompany' => [
      'fields' => ['*'],
      'joins' => [
        [
          'type' => 'inner',    // もしくは left
          'table' => '(SELECT m_companies_id,count(*) AS user_account FROM  m_users WHERE del_flg != 0)',
          'alias' => 'MUser',
          'conditions' => [
          'MUser.m_companies_id = MCompany.id',
          ],
        ],
      ],
      'conditions' => [
        'MCompany.del_flg != ' => 1,
      ],
      'order' => ['MCompany.id' => 'asc'],
      'limit' => 10,
    ]
  ];

    public function beforeFilter() {
    parent::beforeFilter();
    $this->set('title_for_layout', 'トップ');
  }
  /**
  *初期画面
  *@return void
  */
  public function index() {
    $this->set('userList', $this->paginate('MCompany'));
  }

  // TODO 初回テナントリリース時に削除
  /**
  *デザインサンプル画面
  *@return void
  */
  public function sample(){

  }
}
