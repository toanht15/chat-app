<?php
/**
 * MWidgetSettingsController controller.
 * ウィジェット設定マスタ
 */
class MWidgetSettingsController extends AppController {
    public $uses = array('MWidgetSetting');
    public $helpers = array('ngForm');

    public $coreSettings = null;

    public function beforeRender(){
        $this->set('title_for_layout', 'ウィジェット設定');
    }

    /* *
     * 一覧画面
     * @return void
     * */
    public function index() {

        if ( $this->request->is('post') ) {
            $errors = $this->_update($this->request->data);
            if ( empty($errors) ) {
                $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));

                $this->redirect(['controller' =>'MWidgetSettings', 'action' => 'index', 'showTab' => $this->request->data['widget']['showTab']]);
            }
            else {
                $this->set('alertMessage', ['type' => C_MESSAGE_TYPE_ERROR, 'text' => Configure::read('message.const.saveFailed')]);
            }
        }
        else {
            $inputData = [];
            $ret = $this->MWidgetSetting->coFind('first');
            $inputData = $ret;

            if ( empty($this->userInfo['MCompany']['core_settings']) ) {
                $this->redirect("/");
            }

            // チャットのみ
            if ( $this->coreSettings[C_COMPANY_USE_CHAT] && !$this->coreSettings[C_COMPANY_USE_SYNCLO] ) {
              $inputData['widget']['showTab'] = "chat";
            }
            // 画面同期のみ
            else if ( $this->coreSettings[C_COMPANY_USE_SYNCLO] && !$this->coreSettings[C_COMPANY_USE_CHAT] ) {
              $inputData['widget']['showTab'] = "call";
            }
            // どちらも
            else {
              // チャットがデフォルト
              $inputData['widget']['showTab'] = "chat";
              if ( isset($this->request->params['named']['showTab']) && strcmp($this->request->params['named']['showTab'], "call") === 0 ) {
                $inputData['widget']['showTab'] = "call";
              }
            }

            if ( isset($ret['MWidgetSetting']['style_settings']) ) {
              $json = $this->_settingToObj($ret['MWidgetSetting']['style_settings']);
              if ( isset($json['show_time']) ) {
                $inputData['MWidgetSetting']['show_time'] = $json['show_time'];
              }
              if ( isset($json['max_show_time']) ) {
                $inputData['MWidgetSetting']['max_show_time'] = $json['max_show_time'];
              }
              if ( isset($json['show_position']) ) {
                $inputData['MWidgetSetting']['show_position'] = $json['show_position'];
              }
              if ( isset($json['title']) ) {
                $inputData['MWidgetSetting']['title'] = $json['title'];
              }
              if ( isset($json['show_subtitle']) ) {
                $inputData['MWidgetSetting']['show_subtitle'] = $json['show_subtitle'];
              }
              if ( isset($json['sub_title']) ) {
                $inputData['MWidgetSetting']['sub_title'] = $json['sub_title'];
              }
              if ( isset($json['show_description']) ) {
                $inputData['MWidgetSetting']['show_description'] = $json['show_description'];
              }
              if ( isset($json['description']) ) {
                $inputData['MWidgetSetting']['description'] = $json['description'];
              }
              if ( isset($json['main_color']) ) {
                $inputData['MWidgetSetting']['main_color'] = $json['main_color'];
              }
              if ( isset($json['string_color']) ) {
                $inputData['MWidgetSetting']['string_color'] = $json['string_color'];
              }
              if ( isset($json['show_main_image']) ) {
                $inputData['MWidgetSetting']['show_main_image'] = $json['show_main_image'];
              }
              if ( isset($json['main_image']) ) {
                $inputData['MWidgetSetting']['main_image'] = $json['main_image'];
              }
              if ( $this->coreSettings['synclo'] ) {
                if ( isset($json['tel']) ) {
                  $inputData['MWidgetSetting']['tel'] = $json['tel'];
                }
                if ( isset($json['content']) ) {
                  $inputData['MWidgetSetting']['content'] = $json['content'];
                }
                if ( isset($json['display_time_flg']) ) {
                  $inputData['MWidgetSetting']['display_time_flg'] = $json['display_time_flg'];
                }
                if ( isset($json['time_text']) ) {
                  $inputData['MWidgetSetting']['time_text'] = $json['time_text'];
                }
              }
              if ( isset($json['radius_ratio']) ) {
                $inputData['MWidgetSetting']['radius_ratio'] = $json['radius_ratio'];
              }
            }
            $this->data = $inputData;
        }
        $this->_viewElement();
    }

    public function remoteShowGallary() {
        Configure::write('debug', 0);
        $this->autoRender = FALSE;
        $this->layout = 'ajax';

        $cssStyle = [];

        if (  isset($this->request->data['color']) ) {
          $cssStyle['.p-show-gallary .bgOn'] = [
            'background-color' => $this->request->data['color']
          ];
        }

        $this->set('cssStyle', $cssStyle);
        $this->_viewElement();

        $this->render('/Elements/MWidgetSettings/remoteGallary');
    }

    private function _viewElement() {
        $this->set('widgetDisplayType', Configure::read('WidgetDisplayType'));
        $this->set('widgetPositionType', Configure::read('widgetPositionType'));
        $this->set('gallaryPath', C_NODE_SERVER_ADDR.C_NODE_SERVER_FILE_PORT.'/img/widget/');
    }

    /* *
     * 更新
     *
     * @params $inputData(array)
     * @return $errors(array) エラー文言
     * */
    private function _update($inputData) {
        $errors = [];
        $filename = null;
        $uploadImage = $inputData['MWidgetSetting']['uploadImage'];

        $prevFileInfo = mb_split("/", $inputData['MWidgetSetting']['main_image']);
        if ( is_numeric($inputData['MWidgetSetting']['show_main_image']) === 1 && count($prevFileInfo) > 0 ) {
            $filename = $prevFileInfo[count($prevFileInfo) - 1];
        }

        if ( !(isset($uploadImage['tmp_name']) && is_uploaded_file($uploadImage['tmp_name'])) ) {
            $inputData['MWidgetSetting']['uploadImage'] = [];
            $uploadImage = null;
        }
        else {
            $this->request->data['MWidgetSetting']['main_image'] = "";
        }

        // バリデーションチェック
        $this->MWidgetSetting->set($inputData);
        $this->MWidgetSetting->begin();

        if ( $this->MWidgetSetting->validates() ) {
            if ( !empty($uploadImage) ) {
                $extension = pathinfo($uploadImage['name'], PATHINFO_EXTENSION);
                $filename = $this->userInfo['MCompany']['company_key'].'_'.date('YmdHis').'.'.$extension;
                $tmpFile = $uploadImage['tmp_name'];
                // ファイルの保存先フルパス＋ファイル名
                $saveFile = C_PATH_WIDGET_IMG_DIR.DS.$filename;
                $in = $this->_imageCreate($extension, $tmpFile); // 元画像ファイル読み込み
                $width = ImageSx($in); // 画像の幅を取得
                $height = ImageSy($in); // 画像の高さを取得
                $save_width = 248; // 幅の最低サイズ
                $save_height = 280; // 高さの最低サイズ
                $image_type = exif_imagetype($tmpFile); // 画像タイプ判定用
                $out = ImageCreateTrueColor($save_width , $save_height);
                //ブレンドモードを無効にする
                imagealphablending($out, false);
                //完全なアルファチャネル情報を保存するフラグをonにする
                imagesavealpha($out, true);
                ImageCopyResampled($out, $in,0,0,0,0, $save_width, $save_height, $width, $height);
                $this->_imageOut($extension, $out, $saveFile);
                $inputData['MWidgetSetting']['main_custom_image'] = $filename;
            }
            // ウィジェットのスタイル設定周りをJSON化
            $widgetStyle = $this->_settingToJson($inputData['MWidgetSetting']);

            $saveData = [
              'MWidgetSetting' => [
                'id' => $inputData['MWidgetSetting']['id'],
                'display_type' => $inputData['MWidgetSetting']['display_type'],
                'style_settings' => $widgetStyle
              ]
            ];


            // 保存処理
            if ( $this->MWidgetSetting->save($saveData, false) ) {
                $this->MWidgetSetting->commit();
                $pattern = "files/".$this->userInfo['MCompany']['company_key']."_[0-9]*.*";

                foreach (glob($pattern) as $file) {
                    if ( !empty($uploadImage) && strcmp("files/".$filename, $file) !== 0 ) {
                      unlink($file);
                    }
                }
            }
            else {
                $this->MWidgetSetting->rollback();
                $errors['rollback'] = "保存処理に失敗しました。";
                foreach (glob($pattern) as $file) {
                    if ( !empty($uploadImage) && strcmp("files/".$filename, $file) !== 0 ) {
                      unlink($file);
                    }
                }

            }
        }
        else {
            // 画面に返す
            $errors = $this->MWidgetSetting->validationErrors;
        }
        return $errors;
    }

    private function _imageCreate($extension, $file){
        if ( preg_match('/^png$/i', $extension) ) {
            return imagecreatefrompng($file);
        }
        if ( preg_match('/^jpeg|jpg$/i', $extension) ) {
            return imagecreatefromjpeg($file);
        }
    }

    private function _imageOut($extension, $file, $saveFile){
        if ( preg_match('/^png$/i', $extension) ) {
            return imagepng($file, $saveFile);
        }
        if ( preg_match('/^jpeg|jpg$/i', $extension) ) {
            return imagejpeg($file, $saveFile);
        }
    }

    /**
     * _settingToJson
     * 配列で渡された値を保存用にJSON形式に変換
     *
     *
     *
     * */
    private function _settingToJson($objData){
        $settings = [];
        foreach ($objData as $key => $val ) {
          if ( isset($this->MWidgetSetting->styleColumns[$key]) ) {
            $settings[$this->MWidgetSetting->styleColumns[$key]] = $val;
          }
        }
        if ( isset($settings['showMainImage']) && strcmp($settings['showMainImage'], "2") === 0 ) {
          $settings['mainImage'] = "";
        }
        else if( !empty($settings['mainImage']) ) {
          if ( isset($objData['main_custom_image']) ) {
            $settings['mainImage'] = C_PATH_WIDGET_CUSTOM_IMG.'/'.$objData['main_custom_image'];
          }
        }
        return json_encode($settings, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_FORCE_OBJECT );
    }

    /**
     * _settingToObj
     * JSON形式で取得した値をオブジェクト形式に変換
     *
     * @param $jsonData JSON JSON形式のデータ
     * @return $settings オブジェクト JSON形式のデータをオブジェクトに変換したもの
     *
     * */
    private function _settingToObj($jsonData){
        $settings = [];

        // キーの管理用変数のキーと値を入れ替える
        $styleColumns = array_flip($this->MWidgetSetting->styleColumns);

        // JSONからオブジェクトに変更
        $json = json_decode($jsonData);

        // 保持していた設定ごとループ処理
        foreach($json as $key => $val){
          // 設定名が管理しているキーである場合、値を $settings にセット
          if ( isset($styleColumns[$key]) ) {
            $settings[$styleColumns[$key]] = $val;
          }
        }
        return $settings;
    }

}
