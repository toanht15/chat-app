<?php
/**
 * MShareDisplaySettingsContorller
 * 除外IP設定
 */
class MShareDisplaySettingsController extends AppController {
  public $uses = ['MShareDisplaySetting'];

  public function index(){
    $this->MShareDisplaySetting->recursive = -1;
    if ( $this->request->is('post') ) {
      $errors = $this->_update($this->request->data);
      if ( empty($errors) ) {
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
        $this->redirect(['controller' =>'MShareDisplaySettings', 'action' => 'index']);
      }
      else {
        $this->set('successMessage', ['type' => C_MESSAGE_TYPE_ERROR, 'text' => Configure::read('message.const.saveFailed')]);
      }
    }
    else {
      $this->data = $this->MShareDisplaySetting->read(null, $this->userInfo['id']);
    }
  }

  /* *
   * 更新
   * @return void
   * */
  private function _update($inputData) {
    $errors = [];

    $this->MShareDisplaySetting->set($inputData);
    $this->MShareDisplaySetting->begin();

    if ( $this->MShareDisplaySetting->validates() ) {
      // バリデーションチェックが成功した場合
      // 保存処理
      if ( $this->MShareDisplaySetting->save($inputData, false) ) {
        $this->MShareDisplaySetting->commit();
      }
      else {
        $this->MShareDisplaySetting->rollback();
        $errors['rollback'] = Configure::read('message.const.saveFailed');
      }
    }
    else {
      // 画面に返す
      $errors = $this->MShareDisplaySetting->validationErrors;
    }
    return $errors;
  }
}
