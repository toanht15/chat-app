<?php
/**
 * MChatNotificationsController controller.
 * チャット通知設定マスタ
 */
class MChatNotificationsController extends AppController {
  public $uses = ['MChatNotification'];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', 'チャット通知設定');
  }

  /* *
   * 一覧画面
   * @return void
   * */
  public function index() {
    $params['conditions'] = [
      'm_companies_id' => $this->userInfo['MCompany']['id'],
      'del_flg' => 0,
    ];
    $this->set('settingList', $this->MChatNotification->find('all', $params));
  }

  /* *
   * 登録画面
   * @return void
   * */
  public function add() {
    $this->set('imagePath', 'popup_icon_light_green.png');
    if ( $this->request->is('post') ) {
      if ( empty($this->request->data['MChatNotification']) ) return false;
      $inputData = $this->request->data['MChatNotification'];

      if ( empty($inputData['id']) ) {
        unset($inputData['id']);
      }
      $this->_entry($inputData);
    }
    else {
      $this->request->data['MChatNotification']['main_image'] = "popup_icon_light_green.png";
    }
    $this->_viewElement();
  }

  /* *
   * 更新画面
   * @return void
   * */
  public function edit($id) {
    $this->_viewElement();
    $imagePath = 'popup_icon_light_green.png';

    if ( $this->request->is('post') ) {
      if ( empty($this->request->data['MChatNotification']) ) return false;
      $inputData = $this->request->data['MChatNotification'];
      $this->_entry($inputData);
    }
    else {
      $inputData = $this->MChatNotification->coFind('first', ['conditions' => ['id'=>$id]]);
      if ( empty($inputData) ) {
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.notFoundId'));
        $this->redirect('/MChatNotifications/index');
        return false;
      }
    }
    if ( !empty($inputData['MChatNotification']['image']) ) {
      $imagePath = $inputData['MChatNotification']['image'];
      $inputData['MChatNotification']['main_image'] = $imagePath;
    }
    $this->set('imagePath', $imagePath);
    $this->request->data = $inputData;
  }

  /* *
   * 保存処理
   * @params $inputData フォーム内容
   * @return void
   * */
  private function _entry($inputData=[]){
    $uploadImage = "";
    $errors = null;
    // ADD or UPDATE
    if ( !isset($inputData['id']) ) {
      $this->MChatNotification->create();
    }

    // SET Company's Id
    $inputData['m_companies_id'] = $this->userInfo['MCompany']['id'];

    if ( !empty($inputData['uploadImage']) ) {
      $uploadImage = $inputData['uploadImage'];
    }

    if ( !(isset($uploadImage['tmp_name']) && is_uploaded_file($uploadImage['tmp_name'])) ) {
      unset($inputData['uploadImage']);
      $uploadImage = null;
    }
    else {
      $inputData['main_image'] = "";
    }

    // バリデーションチェック
    $this->MChatNotification->set($inputData);
    $this->MChatNotification->begin();
    if ( $this->MChatNotification->validates() ) {
      $saveFile = ""; // ファイルの保存先フルパス＋ファイル名
      if ( !empty($uploadImage) ) {
        $extension = pathinfo($uploadImage['name'], PATHINFO_EXTENSION);
        $filename = $this->userInfo['MCompany']['company_key'].'_'.date('YmdHis').'.'.$extension;
        $tmpFile = $uploadImage['tmp_name'];
        // ファイルの保存先フルパス＋ファイル名
        $saveFile = C_PATH_NOTIFICATION_IMG_SAVE_DIR."tmp".DS.$filename;
        $in = $this->imageCreate($extension, $tmpFile); // 元画像ファイル読み込み
        $width = ImageSx($in); // 画像の幅を取得
        $height = ImageSy($in); // 画像の高さを取得
        $save_width = 200; // 幅の最低サイズ
        $save_height = 200; // 高さの最低サイズ
        $image_type = exif_imagetype($tmpFile); // 画像タイプ判定用
        $out = ImageCreateTrueColor($save_width , $save_height);
        //ブレンドモードを無効にする
        imagealphablending($out, false);
        //完全なアルファチャネル情報を保存するフラグをonにする
        imagesavealpha($out, true);
        ImageCopyResampled($out, $in,0,0,0,0, $save_width, $save_height, $width, $height);
        $this->imageOut($extension, $out, $saveFile);
        $inputData['image'] = $filename;
      }
      else {
        $inputData['image'] = $inputData['main_image'];
        $filename = $inputData['main_image'];
        unset($inputData['main_image']);
      }

      if ( $this->MChatNotification->save(["MChatNotification" => $inputData], false) ) {
        /* アップロードしたファイルを移動する */
        // ファイルアップロードを行った場合
        if ( !empty($saveFile) ) {
          // 直前で保存したIDを取得
          $saveId = $this->MChatNotification->id;
          $filename = $this->userInfo['MCompany']['company_key'].'_'.$saveId.".".$extension;
          $newPath = C_PATH_NOTIFICATION_IMG_SAVE_DIR.$filename;
          if ( rename($saveFile, $newPath) ) {
            $this->MChatNotification->commit();

            // 新しいファイル名
            $saveData = [
              'MChatNotification' => [
                'id' => $saveId,
                'image' => $filename
              ]
            ];
            // 新しいファイル名を保存
            $this->MChatNotification->save($saveData, false);
            $this->_entry_success();
          }
          else {
            $this->_entry_false();
          }
        }
        // ファイルアップロードを行っていない場合
        else {
          $this->MChatNotification->commit();
          $this->_entry_success();
        }
      }
      else {
        $this->_entry_false();
      }
      $this->redirect('/MChatNotifications/index');
    }
  }

  /* *
   * 保存処理成功ロジック
   * @return void
   * */
  private function _entry_success(){
    $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
    $this->MChatNotification->commit();
    // tmpファイルが残っていれば削除
    $pattern = C_PATH_NOTIFICATION_IMG_SAVE_DIR."tmp/".$this->userInfo['MCompany']['company_key'].'_*';
    foreach (glob($pattern) as $file) {
      unlink($file);
    }
    $this->redirect('/MChatNotifications/index');
  }

  /* *
   * 保存処理失敗ロジック
   * @return void
   * */
  private function _entry_false(){
    $this->MChatNotification->rollback();
    $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
    $errors['rollback'] = "保存処理に失敗しました。";
    $this->redirect('/MChatNotifications/index');
  }

  /* *
   * アイコン画像ギャラリー
   * @return void
   * */
  public function remoteShowGallary() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    if ( !$this->request->is('ajax') ) return false;
    $this->render('/Elements/MChatNotifications/remoteGallary');
  }

  /* *
   * 削除
   * @return void
   * */
  public function remoteDelete() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $this->MChatNotification->recursive = -1;
    if ( $this->MChatNotification->logicalDelete($this->request->data['id']) ) {
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
    }
    else {
      $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
    }
  }

  private function _viewElement(){
    $this->set('chatNotificationType', Configure::read('chatNotificationType'));
  }

}
