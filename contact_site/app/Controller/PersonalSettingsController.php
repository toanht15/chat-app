<?php
/**
 * PersonalSettingsController controller.
 * ユーザーマスタ
 */
class PersonalSettingsController extends AppController {
  public $uses = ['MUser', 'MChatSetting'];
  public $components = ['ImageTrimming'];

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
        $tmpData['MUser']['settings'] = $this->request->data['MUser']['settings'];
        $tmpData['MUser']['mail_address'] = $this->request->data['MUser']['mail_address'];
        $tmpData['MUser']['memo'] = $this->request->data['MUser']['memo'];
        $errors = [];
        // パスワードを変更する場合
        if ( $this->request->data['MUser']['edit_password'] === 'true' ) {
          $tmpData['MUser']['current_password'] = $this->request->data['MUser']['current_password'];
          $tmpData['MUser']['new_password'] = $this->request->data['MUser']['new_password'];
          $tmpData['MUser']['confirm_password'] = $this->request->data['MUser']['confirm_password'];
          $this->MUser->validate = $this->MUser->updateValidate;
        }

        //userInfoのidと$inputDataのidが違う場合、$inputDataのidが空の場合
        if($this->request->data['MUser']['id'] != $this->userInfo['id']  || empty($this->request->data['MUser']['id'])) {
          $errors['rollback'] = Configure::read('message.const.saveFailed');
          return $errors;
        }

        $tmpData['MUser']['settings'] = $this->_collectUserSettings($tmpData['MUser']['settings']);

        // パスワードチェックが問題なければ単独でバリデーションチェックのみ
        $this->MUser->set($tmpData);
        $this->MUser->begin();
        $errors = null;

        if ( $this->MUser->validates() ) {
          // バリデーションチェックが成功した場合
          // 保存処理
          if ( $this->MUser->save($tmpData, false) ) {
            $pattern = "files/".$this->userInfo['MCompany']['company_key']."_".$this->userInfo['id']."_"."[0-9]*.*";
            foreach (glob($pattern) as $file) {
              if ( !empty($uploadImage) && strcmp("files/".$filename, $file) !== 0 ) {
                unlink($file);
              }
            }
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
      if (!empty($this->request->data['Trimming']['profileIconInfo'])) {
        $trimmingInfo = json_decode($this->request->data['Trimming']['profileIconInfo'], TRUE);
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
