<?php
/**
 * MUsersController controller.
 * ユーザーマスタ
 */
class MUsersController extends AppController {
  public $uses = ['MUser', 'MCompany', 'MChatSetting'];
  public $paginate = [
    'MUser' => [
      'limit' => 10,
      'order' => ['MUser.id' => 'asc'],
      'fields' => ['MUser.*'],
      'conditions' => [
        'MUser.del_flg != ' => 1,
        'MUser.permission_level !=' => C_AUTHORITY_SUPER
      ],
      'recursive' => -1
    ]
  ];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', 'ユーザー管理');
    $this->set('siteKey', $this->userInfo['MCompany']['company_key']);
    $this->set('limitUserNum', $this->userInfo['MCompany']['limit_users']);
    $this->Auth->allow(['remoteSaveForm']);
    header('Access-Control-Allow-Origin: *');
  }

  /* *
   * 一覧画面
   * @return void
   * */
  public function index() {
    $this->paginate['MUser']['conditions']['MUser.m_companies_id'] = $this->userInfo['MCompany']['id'];
    $this->_viewElement();
    $this->set('userList', $this->paginate('MUser'));
    $this->set('userListCnt', $this->MUser->find('count', [
      'conditions' => [
      'MUser.del_flg != ' => 1,
      'MUser.permission_level !=' => C_AUTHORITY_SUPER,
      'MUser.m_companies_id' => $this->userInfo['MCompany']['id']
      ]
    ]));
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
      $this->MUser->recursive = -1;
      $this->request->data = $this->MUser->read(null, $this->request->data['id']);
      $this->log('渡すデータ',LOG_DEBUG);
      $this->log($this->request->data,LOG_DEBUG);
      if($this->request->data['MUser']['m_companies_id'] == $this->userInfo['MCompany']['id'] && $this->request->data['MUser']['permission_level'] != 99 && $this->request->data['MUser']['del_flg'] != 1) {
        $this->render('/MUsers/remoteEntryUser');
      }
      else {
        $this->response->statusCode(403); //Forbidden
        return;
      }
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
      $this->MUser->recursive = -1;
      $tmpData = $this->MUser->read(null, $this->request->data['userId']);
      $insertFlg = false;
    }
    else {
      $this->MUser->create();

      // アカウント数チェック
      if (!$this->_checkAcoundNum()) {
        $errorMessage = ['other' => ["契約しているアカウント数をオーバーしています"]];
      }
    }

    $tmpData['MUser']['user_name'] =  htmlspecialchars($this->request->data['userName'], ENT_QUOTES, 'UTF-8');
    $tmpData['MUser']['display_name'] = htmlspecialchars($this->request->data['displayName'], ENT_QUOTES, 'UTF-8');
    $tmpData['MUser']['mail_address'] = htmlspecialchars($this->request->data['mailAddress'], ENT_QUOTES, 'UTF-8');
    $tmpData['MUser']['permission_level'] = htmlspecialchars($this->request->data['permissionLevel'], ENT_QUOTES, 'UTF-8');

    if ( !$insertFlg && empty($this->request->data['password']) ) {
      unset($this->MUser->validate['password']);
    }
    else {
      $tmpData['MUser']['new_password'] = htmlspecialchars($this->request->data['password'], ENT_QUOTES, 'UTF-8');
    }

    // チャットアカウント用処理（アカウント登録時のみ）
    if ( !isset($tmpData['MUser']['id']) && isset($this->coreSettings[C_COMPANY_USE_CHAT]) && $this->coreSettings[C_COMPANY_USE_CHAT] ) {
      $tmpData['MUser']['settings'] = htmlspecialchars($this->_setChatSetting($tmpData), ENT_QUOTES, 'UTF-8');
    }
    // const
    $this->MUser->set($tmpData);

    $this->MUser->begin();

    // バリデーションチェックでエラーが出た場合
    if ( empty($errorMessage) && $this->MUser->validates() ) {
      $saveData = $tmpData;
      $saveData['MUser']['m_companies_id'] = $this->userInfo['MCompany']['id'];
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

  /**
   * _setChatSetting チャット関連設定
   * @param $tmpData array POSTデータ
   * @return string(json) JSONデータ(settings)に格納される
   * */
  private function _setChatSetting($tmpData = []){
    $chatSetting = $this->MChatSetting->coFind('first', [], false);
    if ( isset($chatSetting['MChatSetting']['sc_flg']) && strcmp($chatSetting['MChatSetting']['sc_flg'], C_SC_ENABLED) === 0 ) {
      return $this->jsonEncode([
        'sc_num' => $chatSetting['MChatSetting']['sc_default_num']
      ]);
    }
    return "";
  }


  /* *
   * 削除
   * @return void
   * */
  public function remoteDeleteUser() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $this->MUser->recursive = -1;
    if ( $this->MUser->logicalDelete($this->request->data['id']) ) {
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
    }
    else {
      $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
    }
  }

  private function _viewElement(){
    $this->set('authorityList', Configure::read("Authority"));
  }

  /**
   * アカウント数のチェック
   * @return bool
   **/
  private function _checkAcoundNum(){
    $mCompany = $this->MCompany->read(null, $this->userInfo['MCompany']['id']);
    if ( isset($mCompany['MCompany']) ) {
      $this->userInfo['MCompany'] = $mCompany['MCompany'];
    }

    $params = [
      'fields' => 'MUser.id',
      'conditions' => [
        'MUser.del_flg !=' => 1,
        'MUser.permission_level !=' => C_AUTHORITY_SUPER,
        'MUser.m_companies_id' => $this->userInfo['MCompany']['id']
      ],
      'recursive' => -1
    ];
    $mUserCnt = $this->MUser->find('count', $params);
    if ( $this->userInfo['MCompany']['limit_users'] <= $mUserCnt ) {
      return false;
    }
    return true;
  }

  /* *
   * 登録画面
   * @return void
   * */
  public function remoteSaveForm() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $data = $this->request->data;
    $password = $this->MUser->passwordHash($data['password']);
    return $password;
  }
}
