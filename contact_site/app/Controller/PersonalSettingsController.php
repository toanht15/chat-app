<?php
/**
 * PersonalSettingsController controller.
 * ユーザーマスタ
 */
class PersonalSettingsController extends AppController {
  public $uses = ['MUser', 'MChatSetting'];

  public function beforeFilter(){
    parent::beforeFilter();

    $this->set('title_for_layout', '個人設定');
    $this->set('siteKey', $this->userInfo['MCompany']['company_key']);
  }

  /* *
   * 一覧画面
   * @return void
   * */
  public function index() {
    $this->MUser->recursive = -1;
    if ( $this->request->is('post') ) {
      $errors = $this->_update($this->request->data);
      if ( empty($errors) ) {
        $this->set('alertMessage', ['type' => C_MESSAGE_TYPE_SUCCESS, 'text' => Configure::read('message.const.saveSuccessful')]);
      }
      else {
        $this->set('alertMessage', ['type' => C_MESSAGE_TYPE_ERROR, 'text' => Configure::read('message.const.saveFailed')]);
      }
    }
    else {
      $this->data = $this->MUser->read(null, $this->userInfo['id']);
    }
    $this->set('mChatSetting', $this->MChatSetting->coFind('first', [], false));
  }

  /* *
   * 更新
   * @return void
   * */
  private function _update($inputData) {
    $this->request->data['MUser']['current_password'] = '';
    $this->request->data['MUser']['new_password'] = '';
    $this->request->data['MUser']['confirm_password'] = '';
    $errors = [];
    // パスワードを変更する場合
    if ( !empty($inputData['MUser']['edit_password']) ) {
      $this->MUser->validate = $this->MUser->updateValidate;
    }

    //userInfoのidと$inputDataのidが違う場合、$inputDataのidが空の場合
    if($inputData['MUser']['id'] != $this->userInfo['id']  || empty($inputData['MUser']['id'])) {
      $errors['rollback'] = Configure::read('message.const.saveFailed');
      return $errors;
    }

    // パスワードチェックが問題なければ単独でバリデーションチェックのみ
    $this->MUser->set($inputData);
    $this->MUser->begin();

    if ( $this->MUser->validates() ) {
      // バリデーションチェックが成功した場合
      // 保存処理
      if ( $this->MUser->save($inputData, false) ) {
        $this->MUser->commit();
      }
      else {
        $this->MUser->rollback();
        $errors['rollback'] = Configure::read('message.const.saveFailed');
      }
    }
    else {
      // 画面に返す
      $errors = $this->MUser->validationErrors;
    }
    return $errors;
  }

}
