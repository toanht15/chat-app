<?php
/**
 * MUsersController controller.
 * ユーザーマスタ
 */
class MUsersController extends AppController {
  public $uses = ['MUser', 'MCompany', 'MChatSetting'];
  public $components = ['ImageTrimming'];
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
    $this->log('あいうえお',LOG_DEBUG);
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $this->_viewElement();
    $this->set('page', $this->request->data['index']);
    $token = md5(uniqid(rand()));
    $this->set('token', $token);
    $this->Session->write('token', $token);
    // const
    if ( strcmp($this->request->data['type'], 2) === 0 ) {
      $this->MUser->recursive = -1;
      $this->request->data = $this->MUser->read(null, $this->request->data['id']);
      if($this->request->data['MUser']['m_companies_id'] == $this->userInfo['MCompany']['id']  && $this->request->data['MUser']['del_flg'] != 1
        && $this->request->data['MUser']['permission_level'] != 99) {
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

    $token = $this->Session->read('token');
    //トークンチェック
    if($this->request->data['accessToken'] == $token) {
      if (!empty($this->request->data['MUser']['id'])) {
        $this->MUser->recursive = -1;
        $tmpData = $this->MUser->read(null, $this->request->data['MUser']['id']);
        if($tmpData['MUser']['m_companies_id'] == $this->userInfo['MCompany']['id'] && $tmpData['MUser']['permission_level'] != 99 && $tmpData['MUser']['del_flg'] != 1) {
          $insertFlg = false;
        }
        else {
          $this->response->statusCode(403); //Forbidden
          return;
        }
      }
      else {
        $this->MUser->create();

        // アカウント数チェック
        if (!$this->_checkAcoundNum()) {
          $errorMessage = ['other' => ["契約しているアカウント数をオーバーしています"]];
        }
      }

      $uploadImage = $this->request->data['MUser']['uploadProfileIcon'];

      $prevFileInfo = mb_split("/", $this->request->data['MUser']['profile_icon']);
      if ( count($prevFileInfo) > 1 ) {
        $filename = $prevFileInfo[count($prevFileInfo) - 1];
        $this->request->data['MUser']["profile_custom_image"] = $filename;
      }

      if ( !(isset($uploadImage['tmp_name']) && is_uploaded_file($uploadImage['tmp_name'])) ) {
        unset($this->request->data['MUser']['uploadProfileIcon']);
        $uploadImage = null;
      }
      if ( !empty($uploadImage) ){
        $filename = $this->trimProfileImg();
        $this->request->data['MUser']["profile_custom_image"] = $filename;
      }

      $tmpData['MUser']['user_name'] = $this->request->data['MUser']['user_name'];
      $tmpData['MUser']['display_name'] = $this->request->data['MUser']['display_name'];
      $tmpData['MUser']['mail_address'] = $this->request->data['MUser']['mail_address'];
      $tmpData['MUser']['permission_level'] = $this->request->data['MUser']['permission_level'];
      $tmpData['MUser']['memo'] = $this->request->data['MUser']['memo'];

      if ( !$insertFlg && empty($this->request->data['MUser']['new_password']) ) {
        unset($this->MUser->validate['password']);
      }
      else {
        $tmpData['MUser']['new_password'] = $this->request->data['MUser']['new_password'];
      }

      // チャットアカウント用処理（アカウント登録時のみ）
      if ( !isset($tmpData['MUser']['id']) && isset($this->coreSettings[C_COMPANY_USE_CHAT]) && $this->coreSettings[C_COMPANY_USE_CHAT] ) {
        $tmpData['MUser']['settings'] = $this->_setChatSetting($tmpData);
      }

      $tmpData['MUser']['settings'] = $this->_collectUserSettings($tmpData['MUser']['settings']);
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
    else {
      $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
    }
  }

  /**
   * _setChatSetting チャット関連設定
   * @param $tmpData array POSTデータ
   * @return string(json) JSONデータ(settings)に格納される
   * */
  private function _setChatSetting($tmpData = []){
    $settings = [];
    $chatSetting = $this->MChatSetting->coFind('first', [], false);
    if ( isset($chatSetting['MChatSetting']['sc_flg']) && strcmp($chatSetting['MChatSetting']['sc_flg'], C_SC_ENABLED) === 0 ) {
	  $settings['sc_num'] = $chatSetting['MChatSetting']['sc_default_num'];
    }

    if (isset($chatSetting['MChatSetting']['sc_login_default_status'])) {
       $settings['login_default_status'] = $chatSetting['MChatSetting']['sc_login_default_status'];
    }

	return $this->jsonEncode($settings);
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
    $data = $this->MUser->find('all', [
      'fields' => [
        'id',
        'm_companies_id',
        'permission_level'
      ],
      'conditions' => [
        'id' => $this->request->data['selectedList']
      ]
    ]);
    $this->MUser->begin();
    $res = true;
    foreach($data as $key => $val){
      if($val['MUser']['permission_level'] != 99 && $val['MUser']['m_companies_id'] == $this->userInfo['MCompany']['id']) {
        if (! $this->MUser->delete($val['MUser']['id']) ) {
          $res = false;
        }
      }
    }
    if($res){
      $this->MUser->commit();
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
    }
    else{
      $this->MUser->rollback();
      $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
    }
    //}
//     if ( $this->MUser->logicalDelete($this->request->data['id']) ) {
//       $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
//     }
//     else {
//       $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
//     }
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

  private function _collectUserSettings($tmpData) {
    $settings = json_decode($tmpData, true);
    $imageName = $this->request->data['MUser']['profile_custom_image'];
    if ( isset($imageName) ) {
      $settings['profileIcon'] = C_PATH_WIDGET_CUSTOM_IMG.'/'.$imageName;
    } else {
      $settings['profileIcon'] = "";
    }
    $settings = json_encode($settings);
    return $settings;
  }

  /* *
   * トリミング処理
   *
   */
  private function trimProfileImg() {
    $uploadImage = "";
    $inputData = $this->request->data['MUser'];
    if ( !empty($inputData['uploadProfileIcon']) ) {
      $uploadImage = $inputData['uploadProfileIcon'];
    }
    $inputData['profile_icon'] = "";

    if ( !empty($uploadImage) ) {
      $extension = pathinfo($uploadImage['name'], PATHINFO_EXTENSION);
      $filename = $this->userInfo['MCompany']['company_key'].'_'.$this->userInfo['id'].'_'.date('YmdHis').'.'.$extension;
      $tmpFile = $uploadImage['tmp_name'];
      // ファイルの保存先フルパス＋ファイル名
      $saveFile = C_PATH_WIDGET_IMG_DIR . DS . $filename;
      if (!empty($this->request->data['Trimming']['info'])) {
        $trimmingInfo = json_decode($this->request->data['Trimming']['info'], TRUE);
        $component = new ImageTrimmingComponent();
        $component->setFileData($uploadImage);
        $component->setSavePath($saveFile);
        $component->setX($trimmingInfo['x']);
        $component->setY($trimmingInfo['y']);
        $component->setWidth($trimmingInfo['width']);
        $component->setHeight($trimmingInfo['height']);
        $component->save();
      } else {
        $in = $this->imageCreate($extension, $tmpFile); // 元画像ファイル読み込み
        $width = ImageSx($in); // 画像の幅を取得
        $height = ImageSy($in); // 画像の高さを取得
        $save_width = C_SQUARE_TRIMMING_MIN_SIDE_SIZE; // 幅の最低サイズ
        $save_height = C_SQUARE_TRIMMING_MIN_SIDE_SIZE; // 高さの最低サイズ
        $image_type = exif_imagetype($tmpFile); // 画像タイプ判定用
        $out = ImageCreateTrueColor($save_width , $save_height);
        //ブレンドモードを無効にする
        imagealphablending($out, false);
        //完全なアルファチャネル情報を保存するフラグをonにする
        imagesavealpha($out, true);
        ImageCopyResampled($out, $in,0,0,0,0, $save_width, $save_height, $width, $height);
        $this->imageOut($extension, $out, $saveFile);
      }
    }
    else {
      $inputData['image'] = $inputData['profile_icon'];
      $filename = $inputData['profile_icon'];
      unset($inputData['profile_icon']);
    }

    return $filename;

  }
}
