<?php
/**
 * DisplayExclusionsContorller
 * 除外IP設定
 */
class DisplayExclusionsController extends AppController {
  public $uses = ['MCompany'];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', '表示除外設定');
  }

  public function index(){
    $this->MCompany->recursive = -1;
    if ( $this->request->is('post') ) {
      $errors = $this->_update($this->request->data);
      if ( empty($errors) ) {
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
        $this->redirect(['controller' =>'DisplayExclusions', 'action' => 'index']);
      }
      else {
        $this->set('successMessage', ['type' => C_MESSAGE_TYPE_ERROR, 'text' => Configure::read('message.const.saveFailed')]);
      }
    }
    else {
      $this->data = $this->MCompany->read(null, $this->userInfo['MCompany']['id']);
    }
  }

  /* *
   * 更新
   * @return void
   * */
  private function _update($inputData) {
    $errors = [];

    $saveData = [];
    if ( isset($inputData['MCompany']['id']) && isset($inputData['MCompany']['exclude_params']) && isset($inputData['MCompany']['exclude_ips']) ) {
      $saveData['MCompany']['id'] = $inputData['MCompany']['id'];
      $saveData['MCompany']['exclude_params'] = $inputData['MCompany']['exclude_params'];
      $saveData['MCompany']['exclude_ips'] = $inputData['MCompany']['exclude_ips'];
    }

    $this->MCompany->set($saveData);
    $this->MCompany->begin();

    if ( $this->MCompany->validates() ) {
      // バリデーションチェックが成功した場合
      // 保存処理
      if ( $this->MCompany->save() ) {
        $this->MCompany->commit();
      }
      else {
        $this->MCompany->rollback();
        $errors['rollback'] = Configure::read('message.const.saveFailed');
      }
    }
    else {
      // 画面に返す
      $errors = $this->MCompany->validationErrors;
    }
    return $errors;
  }
}
