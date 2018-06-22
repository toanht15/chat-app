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
   * 更新画面
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

    /* *
   * 保存処理
   * @return void
   * */
  public function remoteSaveEntryForm() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    if ( !$this->request->is('ajax') ) return false;

    if ( $this->request->is('post') ) {
      $token = $this->Session->read('token');
      //トークンチェック
      if($this->request->data['accessToken'] == $token) {
        $tmpData = [];

        $tmpData['MUser']['user_name'] = $this->request->data['userName'];
        $tmpData['MUser']['display_name'] = $this->request->data['displayName'];
        $tmpData['MUser']['settings'] = $this->request->data['settings'];
        $tmpData['MUser']['mail_address'] = $this->request->data['mailAddress'];

        $errors = [];
        // パスワードを変更する場合
        if ( $this->request->data['edit_password'] === 'true' ) {
          $tmpData['MUser']['current_password'] = $this->request->data['current_password'];
          $tmpData['MUser']['new_password'] = $this->request->data['new_password'];
          $tmpData['MUser']['confirm_password'] = $this->request->data['confirm_password'];
          $this->MUser->validate = $this->MUser->updateValidate;
        }

        //userInfoのidと$inputDataのidが違う場合、$inputDataのidが空の場合
        if($this->request->data['id'] != $this->userInfo['id']  || empty($this->request->data['id'])) {
          $errors['rollback'] = Configure::read('message.const.saveFailed');
          return $errors;
        }

        // パスワードチェックが問題なければ単独でバリデーションチェックのみ
        $this->MUser->set($tmpData);
        $this->MUser->begin();
        $errors = null;

        if ( $this->MUser->validates() ) {
          // バリデーションチェックが成功した場合
          // 保存処理
          if ( $this->MUser->save($tmpData, false) ) {
            $this->MUser->commit();
            $this->userInfo['display_name'] = $tmpData['MUser']['display_name'];
            $this->Session->write('global.userInfo',$this->userInfo);
            $this->Session->read('token');
            $this->set('token', $token);
            $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
          }
          else {
            $this->MUser->rollback();
            $this->Session->read('token');
            $this->set('token', $token);
          }
        }
        $errors = $this->MUser->validationErrors;
        return new CakeResponse(['body' => json_encode($errors)]);
      }
      else {
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
}
