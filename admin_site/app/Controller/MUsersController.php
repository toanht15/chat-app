<?php
/**
 * MUsersController controller.
 * ユーザーマスタ
 */
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
class MUsersController extends AppController {
  public $uses = ['MAdministrator'];
  public $paginate = [
    'MAdministrator' => [
      'limit' => 10,
      'order' => ['MAdministrator.id' => 'asc'],
      'fields' => ['MAdministrator.*'],
      'conditions' => [
        'MAdministrator.del_flg != ' => 1,
      ],
    ]
  ];

  public function beforeFilter() {
    parent::beforeFilter();
    $this->set('title_for_layout', 'ユーザー管理');
  }

  /* *
  * 一覧画面
  * @return void
  * */
  public function index() {
    $this->set('userList', $this->paginate('MAdministrator'));
  }

  /* *
  * 登録画面
  * @return void
  * */
  public function remoteOpenEntryForm() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    //const
    if ( strcmp($this->request->data['type'], 2) === 0 ) {
      $this->request->data = $this->MAdministrator->read(null, $this->request->data['id']);
    }
    $this->render('/MUsers/remoteEntryUser');
  }

  /* *
   * 登録処理
   * @return void
   * */
  public function remoteSaveEntryForm() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $tmpData = [];
    $saveData = [];
    $insertFlg = true;
    $errorMessage = null;

    if ( !$this->request->is('ajax') ) return false;

    if (!empty($this->request->data['userId'])) {
      $tmpData = $this->MUser->read(null, $this->request->data['userId']);
      $insertFlg = false;
    }
    else {
      $this->MUser->create();
    }

    $tmpData['MUser']['user_name'] = $this->request->data['userName'];
    $tmpData['MUser']['mail_address'] = $this->request->data['mailAddress'];

    if ( !$insertFlg && empty($this->request->data['password']) ) {
      unset($this->MUser->validate['password']);
    }
    else {
      $tmpData['MUser']['new_password'] = $this->request->data['password'];
    }
    // const
    $this->MUser->set($tmpData);
    $this->MUser->begin();

    //　バリデーションチェックでエラーが出た場合
    if (empty($errorMessage) && empty($this->request->data['userId']) && $this->MUser->validates()) {
      $saveData = $tmpData;
      if ( $this->MUser->save($saveData, false) ) {
        $this->MUser->commit();
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
      }
      else {
        $this->MUser->rollback();
      }
    }

    $this->MUser->validate = $this->MUser->updateValidate;
    if (empty($errorMessage) && !empty($this->request->data['userId']) && $this->MUser->validates()) {
      $saveData = $tmpData;
      if ( $this->MUser->save($saveData, false) ) {
        $this->MUser->commit();
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
      }
      else {
        $this->MUser->rollback();
      }
    }

    if ( empty($errorMessage) ) {
      $errorMessage = $this->MUser->validationErrors;
    }
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
    if ( $this->MAdministrator->logicalDelete($this->request->data['id']) ) {
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
    }
    else {
      $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
    }
  }
}
