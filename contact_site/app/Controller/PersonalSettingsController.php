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
      $this->log('これがデータだよ',LOG_DEBUG);
      $this->log($this->request->data,LOG_DEBUG);
      $token = $this->Session->read('token');
      //トークンチェック
      if($this->request->data['accessToken'] == $token) {
        $errors = $this->_update($this->request->data);
        if ( empty($errors) ) {
          $this->set('alertMessage', ['type' => C_MESSAGE_TYPE_SUCCESS, 'text' => Configure::read('message.const.saveSuccessful')]);
          $this->Session->read('token');
          $this->set('token', $token);
        }
        else {
          $this->set('alertMessage', ['type' => C_MESSAGE_TYPE_ERROR, 'text' => Configure::read('message.const.saveFailed')]);
          $this->Session->read('token');
          $this->set('token', $token);
        }
      }
      else {
        $this->set('alertMessage', ['type' => C_MESSAGE_TYPE_ERROR, 'text' => Configure::read('message.const.saveFailed')]);
        $this->Session->read('token');
        $this->set('token', $token);
      }
    }
    else {
      $this->data = $this->MUser->read(null, $this->userInfo['id']);
      $token = md5(uniqid(rand()));
      $this->set('token', $token);
      $this->Session->write('token', $token);
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

    $this->log('ここにこそ注目',LOG_DEBUG);
    $this->log($inputData,LOG_DEBUG);
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

    /* *
   * 登録画面
   * @return void
   * */
  public function remoteOpenEntryForm() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $this->data = $this->MUser->read(null, $this->userInfo['id']);
    $token = md5(uniqid(rand()));
    $this->set('token', $token);
    $this->Session->write('token', $token);
    $this->set('mChatSetting', $this->MChatSetting->coFind('first', [], false));
    $this->render('/PersonalSettings/remoteEntryUser');
  }

  public function remoteSaveEntryForm() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    if ( !$this->request->is('ajax') ) return false;

    if ( $this->request->is('post') ) {
      $this->log('これがデータだよ',LOG_DEBUG);
      $this->log($this->request->data,LOG_DEBUG);

      $token = $this->Session->read('token');
      //トークンチェック
      if($this->request->data['accessToken'] == $token) {
        $errors = $this->_update2($this->request->data);
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
        return $errors;
        /*if ( empty($errors) ) {
          $this->set('alertMessage', ['type' => C_MESSAGE_TYPE_SUCCESS, 'text' => Configure::read('message.const.saveSuccessful')]);
          $this->Session->read('token');
          $this->set('token', $token);
          $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
        }
        else {
          $this->set('alertMessage', ['type' => C_MESSAGE_TYPE_ERROR, 'text' => Configure::read('message.const.saveFailed')]);
          $this->Session->read('token');
          $this->set('token', $token);
          $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
        }*/
      }
      else {
        $this->set('alertMessage', ['type' => C_MESSAGE_TYPE_ERROR, 'text' => Configure::read('message.const.saveFailed')]);
        $this->Session->read('token');
        $this->set('token', $token);
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
      }
    }
    else {
      $this->data = $this->MUser->read(null, $this->userInfo['id']);
      $token = md5(uniqid(rand()));
      $this->set('token', $token);
      $this->Session->write('token', $token);
    }
    $this->set('mChatSetting', $this->MChatSetting->coFind('first', [], false));
  }


  /* *
   * 更新
   * @return void
   * */
  private function _update2($inputData) {
    $this->log('inputData',LOG_DEBUG);
    $this->log($inputData,LOG_DEBUG);
    $tmpData = [];

    $tmpData['MUser']['user_name'] = $inputData['userName'];
    $tmpData['MUser']['display_name'] = $inputData['displayName'];
    $tmpData['MUser']['settings'] = $inputData['settings'];
    $tmpData['MUser']['mail_address'] = $inputData['mailAddress'];

    $errors = [];
    // パスワードを変更する場合
    if ( $inputData['edit_password'] === 'true' ) {
      $tmpData['MUser']['current_password'] = $inputData['current_password'];
      $tmpData['MUser']['new_password'] = $inputData['new_password'];
      $tmpData['MUser']['confirm_password'] = $inputData['confirm_password'];
      $this->MUser->validate = $this->MUser->updateValidate;
    }

    //userInfoのidと$inputDataのidが違う場合、$inputDataのidが空の場合
    if($inputData['id'] != $this->userInfo['id']  || empty($inputData['id'])) {
      $errors['rollback'] = Configure::read('message.const.saveFailed');
      return $errors;
    }

    // パスワードチェックが問題なければ単独でバリデーションチェックのみ
    $this->MUser->set($tmpData);
    $this->MUser->begin();
    $this->log('バリデーションチェック',LOG_DEBUG);
    $this->log($tmpData,LOG_DEBUG);
    $error = null;

    if ( $this->MUser->validates() ) {
      // バリデーションチェックが成功した場合
      // 保存処理
      if ( $this->MUser->save($tmpData, false) ) {
        $this->log('データは保存できている33',LOG_DEBUG);
        $this->log($tmpData,LOG_DEBUG);
        $this->MUser->commit();
      }
      else {
        $this->log('データは保存できていない33',LOG_DEBUG);
        $this->MUser->rollback();
        $errors['rollback'] = Configure::read('message.const.saveFailed');
      }
    }
    else {
      $this->log('varidation',LOG_DEBUG);
      // 画面に返す
      $errors = $this->MUser->validationErrors;
    }
    return new CakeResponse(['body' => json_encode($errors)]);
  }

}
