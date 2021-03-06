<?php
/**
 * TopsController controller.
 * ホーム
 */

App::uses('AppController', 'Controller');
class TopsController extends AppController {

  public $uses = ['MCompany','MUser','MAgreement'];

  public $paginate = [
    'MCompany' => [
      'order' => ['MCompany.id' => 'asc'],
      'fields' => ['*'],
      'joins' => [
        [
          'type' => 'inner',
          'table' => '(SELECT id,m_companies_id,count(m_companies_id) AS user_account FROM  m_users WHERE del_flg != 1 GROUP BY m_companies_id)',
          'alias' => 'MUser',
          'conditions' => [
          'MUser.m_companies_id = MCompany.id',
          ],
        ],
      ],
      'conditions' => [
        'MCompany.del_flg != ' => 1,
      ],
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
    $this->set('companyList', $this->paginate('MCompany'));
  }

  // TODO 初回テナントリリース時に削除
  /**
  *デザインサンプル画面
  *@return void
  */
  public function sample(){

  }
}
