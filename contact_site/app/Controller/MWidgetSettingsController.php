<?php
/**
 * MWidgetSettingsController controller.
 * ウィジェット設定マスタ
 */
class MWidgetSettingsController extends AppController {
  public $uses = ['MWidgetSetting'];
  public $helpers = ['ngForm'];

  public $coreSettings = null;
  public $styleSetting = [
    'common' => [
      'show_timing', 'max_show_timing_site', 'max_show_timing_page',
      'show_time', 'max_show_time', 'max_show_time_page', 'show_position', 'widget_size_type', 'title', 'show_subtitle', 'sub_title', 'show_description', 'description',
      'main_color', 'string_color', 'show_main_image', 'main_image', 'radius_ratio'
    ],
    'synclo' => ['tel', 'content', 'display_time_flg', 'time_text'],
    'chat' => ['chat_radio_behavior', 'chat_trigger', 'show_name',  'chat_message_design_type',  'chat_message_with_animation', 'sp_show_flg', 'sp_header_light_flg', 'sp_auto_open_flg',],
  ];

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

      // 表示ウィジェットのセット
      $inputData = $this->_setShowTab($inputData);

      // 詳細設定
      if ( isset($ret['MWidgetSetting']['style_settings']) ) {
        $json = $this->_settingToObj($ret['MWidgetSetting']['style_settings']);
        $inputData['MWidgetSetting'] = $this->_setStyleSetting($inputData['MWidgetSetting'], $json);
      }
      $this->data = $inputData;
    }
    $this->recurse_array_HTML_safe($this->request->data);
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
    $this->set('widgetShowNameType', Configure::read('widgetShowNameType'));
    $this->set('chatMessageDesignType', Configure::read('chatMessageDesignType'));
    $this->set('widgetSendActType', Configure::read('widgetSendActType'));
    $this->set('normalChoices', Configure::read('normalChoices')); // はい・いいえ
    $this->set('widgetRadioBtnBehaviorType', Configure::read('widgetRadioBtnBehaviorType'));
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
        $in = $this->imageCreate($extension, $tmpFile); // 元画像ファイル読み込み
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
        $this->imageOut($extension, $out, $saveFile);
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

    return $this->jsonEncode($settings);
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

  /**
   * デフォルトで表示するタブを選定
   * @param $d ($inputData)
   * @return $d ($inputData)
   * */
  private function _setShowTab($d){
    // チャットのみ
    if ( $this->coreSettings[C_COMPANY_USE_CHAT] && !$this->coreSettings[C_COMPANY_USE_SYNCLO] ) {
      $d['widget']['showTab'] = "chat";
    }
    // 画面・資料同期のみ
    else if ( ($this->coreSettings[C_COMPANY_USE_SYNCLO] || (isset($this->coreSettings[C_COMPANY_USE_DOCUMENT]) && $this->coreSettings[C_COMPANY_USE_DOCUMENT]) ) && !$this->coreSettings[C_COMPANY_USE_CHAT] ) {
      $d['widget']['showTab'] = "call";
    }
    // どちらも
    else {
      // チャットがデフォルト
      $d['widget']['showTab'] = "chat";
      if ( isset($this->request->params['named']['showTab']) && strcmp($this->request->params['named']['showTab'], "call") === 0 ) {
      $d['widget']['showTab'] = "call";
      }
    }
    return $d;
  }

  /**
   * jsonデータとして纏めていた設定値を配列に直す
   * @param $d ($inputData)
   * @param $json ($inputData['MWidgetSetting']['style_settings']をjson_decodeしたもの)
   * @return $d ($inputData)
   * */
  private function _setStyleSetting($d, $json) {
    foreach($this->styleSetting as $key => $list) {
      foreach($list as $v) {
        switch ($key) {
          case 'chat':
            if ( !$this->coreSettings[C_COMPANY_USE_CHAT] ) { continue; }
            if ( strcmp($v, 'chat_radio_behavior') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['chat_radio_behavior'] = C_WIDGET_RADIO_CLICK_SEND; // デフォルト値
            }
            if ( strcmp($v, 'chat_trigger') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['chat_trigger'] = C_WIDGET_SEND_ACT_PUSH_KEY; // デフォルト値
            }
            if ( strcmp($v, 'show_name') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['show_name'] = C_WIDGET_SHOW_COMP; // デフォルト値
            }
            if ( strcmp($v, 'chat_message_design_type') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['chat_message_design_type'] = C_WIDGET_CHAT_MESSAGE_DESIGN_TYPE_BOX; // デフォルト値
            }
            if ( strcmp($v, 'chat_message_with_animation') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['chat_message_with_animation'] = C_CHECK_OFF; // デフォルト値（非選択状態：アニメーション無効）
            }
            if ( strcmp($v, 'sp_show_flg') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['sp_show_flg'] = C_SELECT_CAN; // デフォルト値
            }

            if ( strcmp($v, 'sp_header_light_flg') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['sp_header_light_flg'] = C_SELECT_CAN_NOT; // デフォルト値
            }

            if ( strcmp($v, 'sp_auto_open_flg') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['sp_auto_open_flg'] = C_CHECK_OFF; // デフォルト値
            }

            if ( isset($json[$v]) ) {
              $d[$v] = $json[$v];
            }
            break;
          case 'synclo':
            if ( !($this->coreSettings[C_COMPANY_USE_SYNCLO] || (isset($this->coreSettings[C_COMPANY_USE_DOCUMENT]) && $this->coreSettings[C_COMPANY_USE_DOCUMENT]) ) ) { continue; }

            if ( isset($json[$v]) ) {
              $d[$v] = $json[$v];
            }
            break;
          case 'common':
            if ( strcmp($v, "max_show_timing_site") === 0 || strcmp($v, "max_show_timing_page") === 0 ) { continue; }
            if ( strcmp($v, "show_timing") === 0 && isset($json[$v]) ) {
              if ( strcmp($json[$v], C_WIDGET_SHOW_TIMING_SITE) === 0 ) {
                if ( isset($json["max_show_timing_site"]) ) {
                  $d["max_show_timing_site"] = $json["max_show_timing_site"];
                }
              }
              else if ( strcmp($json[$v], C_WIDGET_SHOW_TIMING_PAGE) === 0 ) {
                if ( isset($json["max_show_timing_page"]) ) {
                  $d["max_show_timing_page"] = $json["max_show_timing_page"];
                }
              }
            }
            if ( strcmp($v, "max_show_time") === 0 || strcmp($v, "max_show_time_page") === 0 ) { continue; }
            if ( strcmp($v, "show_time") === 0 && isset($json[$v]) ) {
              if ( strcmp($json[$v], C_WIDGET_AUTO_OPEN_TYPE_SITE) === 0 ) {
                if ( isset($json["max_show_time"]) ) {
                  $d["max_show_time"] = $json["max_show_time"];
                }
              }
              else if ( strcmp($json[$v], C_WIDGET_AUTO_OPEN_TYPE_PAGE) === 0 ) {
                if ( isset($json["max_show_time_page"]) ) {
                  $d["max_show_time_page"] = $json["max_show_time_page"];
                }
              }
            }
            //ウィジットサイズタイプ
            if ( strcmp($v, 'widget_size_type') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['widget_size_type'] = C_WIDGET_SIZE_TYPE_SMALL; // デフォルト値
            }

            if ( isset($json[$v]) ) {
              $d[$v] = $json[$v];
            }
            break;
        }
      }
    }
    return $d;
  }

}
