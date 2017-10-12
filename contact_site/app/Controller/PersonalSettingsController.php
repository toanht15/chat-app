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
      $this->request->data['MUser']['userName'] =  htmlspecialchars($this->request->data['MUser']['userName'], ENT_QUOTES, 'UTF-8');
      $this->request->data['MUser']['display_name'] =  htmlspecialchars($this->request->data['MUser']['display_name'], ENT_QUOTES, 'UTF-8');
      $this->request->data['MUser']['mail_address'] =  htmlspecialchars($this->request->data['MUser']['mail_address'], ENT_QUOTES, 'UTF-8');
      $this->request->data['MUser']['current_password'] =  htmlspecialchars($this->request->data['MUser']['current_password'], ENT_QUOTES, 'UTF-8');
      $this->request->data['MUser']['new_password'] =  htmlspecialchars($this->request->data['MUser']['new_password'], ENT_QUOTES, 'UTF-8');
      $this->request->data['MUser']['confirm_password'] =  htmlspecialchars($this->request->data['MUser']['confirm_password'], ENT_QUOTES, 'UTF-8');
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
    $this->log('inputしてきたよ',LOG_DEBUG);
    $this->log($inputData,LOG_DEBUG);
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

    //if (preg_match("/[A-Za-z]/", $inputData['MUser']['new_password']) && preg_match("/[0-9]/", $inputData['MUser']['new_password'])) {
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
      $this->log('エラー内容',LOG_DEBUG);
      $this->log($errors,LOG_DEBUG);
    }
  /*}
    else {
      $this->log('外れた',LOG_DEBUG);
      $errors = '違うよ～';
    }*/
    return $errors;
  }

}
