<?php
/**
 * TDictionariesController controller.
 * ユーザーマスタ
 */
class CampaignsController extends AppController {
  public $uses = ['Campaign'];

  /*public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', '単語帳管理');
  }*/

  /* *
   * 一覧画面
   * @return void
   * */
  public function index() {
    $this->set('campaignList', $this->Campaign->find('all'));
    //$this->_viewElement();
  }


  /* *
   * 登録画面
   * @return void
   * */
  public function remoteOpenEntryForm() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $this->_viewElement();
    // const
    if ( strcmp($this->request->data['type'], 2) === 0 ) {
      $this->Campaigns->recursive = -1;
      $this->request->data = $this->Campaigns->read(null, $this->request->data['id']);
    }
    $this->render('/Elements/Campaigns/remoteEntry');
  }

  private function _viewElement(){
    $this->set('dictionaryTypeList', Configure::read("dictionaryType"));
  }

  /* *
   * 保存処理
   * @return void
   * */
  public function remoteSaveEntryForm() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $saveData = [];
    $errorMessage = [];

    // if ( !$this->request->is('ajax') ) return false;
    if (!empty($this->request->data['campaignId'])) {
      $this->Campaign->recursive = -1;
      $saveData = $this->Campaign->read(null, $this->request->data['campaignId']);
    }
    else {
      $this->Campaign->create();
    }

    $saveData['Campaign']['m_companies_id'] = $this->userInfo['MCompany']['id'];
    $saveData['Campaign']['name'] = $this->request->data['name'];
    $saveData['Campaign']['parameter'] = $this->request->data['parameter'];
    $saveData['Campaign']['comment'] = $this->request->data['comment'];
    if (empty($this->request->data['campaignId'])) {
      $params = [

        'conditions' => [
          'Campaign.m_companies_id' => $this->userInfo['MCompany']['id']
        ],
        'order' => [
          'Campaign.sort' => 'desc',
          'Campaign.id' => 'desc'
        ],
        'limit' => 1,
        'recursive' => -1
      ];
      $lastData = $this->Campaign->find('first', $params);
      $nextSort = 1;
      if (!empty($lastData)) {
        $nextSort = intval($lastData['Campaign']['sort']) + 1;
      }
      $saveData['Campaign']['sort'] = $nextSort;
    }
    $saveData['Campaign']['type'] = $this->request->data['type'];
    if ( strcmp($saveData['Campaign']['type'], C_AUTHORITY_NORMAL) === 0 ) {
      $saveData['Campaign']['m_users_id'] = $this->userInfo['id'];
    }

    // const
    $this->Campaign->set($saveData);

    $this->Campaign->begin();

    // バリデーションチェックでエラーが出た場合
    if ( $this->Campaign->save() ) {
      $this->Campaign->commit();
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
    }
    else {
      $this->Campaign->rollback();
    }
    $errorMessage = $this->Campaign->validationErrors;
    return new CakeResponse(['body' => json_encode($errorMessage)]);
  }

}
