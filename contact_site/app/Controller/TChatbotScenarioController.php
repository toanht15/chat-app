<?php
/**
 * TChatbotScenarioController controller.
 * ユーザーマスタ
 */
class TChatbotScenarioController extends AppController {
  public $uses = ['TChatbotScenario'];
  public $paginate = [
    'TChatbotScenario' => [
      'limit' => 100,
      'order' => [
          'TChatbotScenario.sort' => 'asc',
          'TChatbotScenario.id' => 'asc'
      ],
      'fields' => ['TChatbotScenario.*'],
      'conditions' => ['TChatbotScenario.del_flg != ' => 1],
      'recursive' => -1
    ]
  ];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', 'チャットボット設定');
    $this->chatbotScenarioActionList = Configure::read('chatbotScenarioActionList');
  }

  /**
   * 一覧画面
   * @return void
   * */
  public function index() {
    $this->paginate['TChatbotScenario']['conditions']['TChatbotScenario.m_companies_id'] = $this->userInfo['MCompany']['id'];
    $data = $this->paginate('TChatbotScenario');

    $this->set('settingList', $data);
    $this->_viewElement();
  }

  /**
   * 登録画面
   * @return void
   * */
  public function add() {
    if ( $this->request->is('post') ) {
      $this->_entry($this->request->data);
    }

    $this->_viewElement();
  }

  /**
   * 更新画面
   * @return void
   * */
  public function edit() {
    if ( $this->request->is('put') ) {
      $this->_entry($this->request->data);
    }

    $this->_viewElement();
  }

  /**
   * 保存機能
   * @param array $inputData
   * @return void
   * */
  private function _entry($saveData) {
    // TODO: シナリオ設定を保存する
  }

  /**
   * ビュー部品セット
   * @return void
   * */
  private function _viewElement() {
    // アクションリスト
    $this->set('chatbotScenarioActionList', $this->chatbotScenarioActionList);
    // 最後に表示していたページ番号
    if(!empty($this->request->query['lastpage'])){
      $this->set('lastPage', $this->request->query['lastpage']);
    }
  }
}
