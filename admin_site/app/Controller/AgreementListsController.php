<?php
/**
 * PersonalSettingsController controller.
 * ユーザーマスタ
 */
class AgreementListsController extends AppController {
  public $uses = ['MCompany','MUser'];

  public $paginate = [
    'MCompany' => [
      'fields' => ['*'],
      'joins' => [
        [
          'type' => 'inner',    // もしくは left
          'table' => '(SELECT m_companies_id,count(*) AS del_flg FROM  m_users WHERE del_flg != 0)',
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

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', '契約管理');
  }

  /* *
   * 一覧画面
   * @return void
   * */
  public function index() {
    $this->set('companyList', $this->paginate('MCompany'));
  }
}
