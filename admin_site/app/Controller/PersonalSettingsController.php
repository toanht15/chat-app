<?php
/**
 * PersonalSettingsController controller.
 * ユーザーマスタ
 */
class PersonalSettingsController extends AppController {
  public $uses = ['MAdministrator'];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', '個人設定');
  }

  /* *
   * 一覧画面
   * @return void
   * */
  public function index() {
    if ( $this->request->is('post') ) {
      $errors = $this->_update($this->request->data);
      if ( empty($errors) ) {
        //通知メッセージ読み込み
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
        $this->set('alertMessage', $this->Session->read('global.message'));
        $this->Session->delete('global.message');
      }
    }
    else {
      $this->data = $this->MAdministrator->read(null, $this->userInfo['id']);
    }
  }

  /* *
   * 更新
   * @return void
   * */
   private function _update($inputData) {
    $errors = [];
    // パスワードを変更する場合
    if ( !empty($inputData['MAdministrator']['edit_password']) ) {
      $this->MAdministrator->validate = $this->MAdministrator->updateValidate;
    }
    // パスワードチェックが問題なければ単独でバリデーションチェックのみ
    $this->MAdministrator->set($inputData);
    $this->MAdministrator->begin();

    if ( $this->MAdministrator->validates() ) {
      // バリデーションチェックが成功した場合
      // 保存処理
      if ( $this->MAdministrator->save($inputData, false) ) {
        $this->MAdministrator->commit();
      }
      else {
        $this->MAdministrator->rollback();
        $errors['rollback'] = Configure::read('message.const.saveFailed');
      }
    }
    else {
      // 画面に返す
      $errors = $this->MAdministrator->validationErrors;
    }
    return $errors;
  }
}
