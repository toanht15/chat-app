<?php
/**
 * TCampaignsController
 * キャンペーン設定
 */
class TCampaignsController extends AppController {
  public $uses = ['TCampaign'];

  /* *
   * 一覧画面
   * @return void
   * */
  public function index() {
    $this->set('tCampaignList', $this->TCampaign->find('all', $this->_setParams()));
  }

  /* *
   * 登録,更新画面
   * @return void
   * */
  public function remoteOpenEntryForm() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    // const
    if ( strcmp($this->request->data['type'], 2) === 0 ) {
      $this->request->data = $this->TCampaign->read(null, $this->request->data['id']);
    }
    $this->render('/Elements/TCampaigns/remoteEntry');
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

    if (!empty($this->request->data['campaignId'])) {
      $this->TCampaign->recursive = -1;
      $saveData = $this->TCampaign->read(null, $this->request->data['campaignId']);
    }
    else {
      $this->TCampaign->create();
    }
    $saveData['TCampaign']['m_companies_id'] = $this->userInfo['MCompany']['id'];
    $saveData['TCampaign']['name'] = $this->request->data['name'];
    $saveData['TCampaign']['parameter'] = $this->request->data['parameter'];
    $saveData['TCampaign']['comment'] = $this->request->data['comment'];
    // const
    $this->TCampaign->set($saveData);
    $this->TCampaign->begin();

    // バリデーションチェックでエラーが出た場合
    if ( $this->TCampaign->save() ) {
      $this->TCampaign->commit();
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
    }
    else {
      $this->TCampaign->rollback();
    }
    $errorMessage = $this->TCampaign->validationErrors;
    return new CakeResponse(['body' => json_encode($errorMessage)]);
  }

  /* *
   * 削除
   * @return void
   * */
  public function remoteDeleteUser() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $this->TCampaign->recursive = -1;
    if ( $this->TCampaign->delete($this->request->data['id']) ) {
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
    }
    else {
      $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
    }
  }

  private function _setParams(){
    $params = [
      'order' => [
        'TCampaign.id' => 'asc'
      ],
      'fields' => [
        'TCampaign.*'
      ],
      'conditions' => [
        'TCampaign.m_companies_id' => $this->userInfo['MCompany']['id']
      ]
    ];
    return $params;
  }
}
