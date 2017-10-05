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
      'show_main_image', 'main_image', 'radius_ratio', 'box_shadow', 'minimize_design_type','close_button_setting','close_button_mode_type','bannertext',
      /* カラー設定styat */
      'color_setting_type','main_color','string_color','message_text_color','other_text_color','widget_border_color','chat_talk_border_color','sub_title_text_color','description_text_color',
      'chat_talk_background_color','c_name_text_color','re_text_color','re_background_color','re_border_color','re_border_none','se_text_color','se_background_color','se_border_color','se_border_none','chat_message_background_color',
      'message_box_text_color','message_box_background_color','message_box_border_color','message_box_border_none','chat_send_btn_text_color','chat_send_btn_background_color','widget_inside_border_color','widget_inside_border_none'
      /* カラー設定end */
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
      if($json['re_border_color'] === 'none'){
        $this->set('re_border_color_flg', false);
        $inputData['MWidgetSetting']['re_border_color'] = 'なし';
        $inputData['MWidgetSetting']['re_border_none'] = true;
      }
      else{
        $this->set('re_border_color_flg', true);
      }
      if($json['se_border_color'] === 'none'){
        $this->set('se_border_color_flg', false);
        $inputData['MWidgetSetting']['se_border_color'] = 'なし';
        $inputData['MWidgetSetting']['se_border_none'] = true;
      }
      else{
        $this->set('se_border_color_flg', true);
      }
      if($json['message_box_border_color'] === 'none'){
        $this->set('message_box_border_color_flg', false);
        $inputData['MWidgetSetting']['message_box_border_color'] = 'なし';
        $inputData['MWidgetSetting']['message_box_border_none'] = true;
      }
      else{
        $this->set('message_box_border_color_flg', true);
      }
      if($json['widget_inside_border_color'] === 'none'){
        $this->set('widget_inside_border_color_flg', false);
        $inputData['MWidgetSetting']['widget_inside_border_color'] = 'なし';
        $inputData['MWidgetSetting']['widget_inside_border_none'] = true;
      }
      else{
        $this->set('widget_inside_border_color_flg', true);
      }
      $this->data = $inputData;
    }
    $titleLength = 12;
    $subTitleLength = 15;
    $descriptionLength = 15;
    switch ($json['widget_size_type']) {
      //大きさによってトップタイトル、企業名、説明文のmaxlengthを可変とする
      case '1': //小
        $titleLength = 12;
        $subTitleLength = 15;
        $descriptionLength = 15;
        break;
      case '2': //中
        $titleLength = 16;
        $subTitleLength = 20;
        $descriptionLength = 20;
        break;
      case '3': //大
        $titleLength = 19;
        $subTitleLength = 24;
        $descriptionLength = 24;
        break;
    }
    $this->set('titleLength_maxlength', $titleLength);
    $this->set('subTitleLength_maxlength', $subTitleLength);
    $this->set('descriptionLength_maxlength', $descriptionLength);
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

    //各枠線なしチェックボックスが入っていた場合対応するカラーの値を置き換える
    //企業側吹き出し枠線色
    if($inputData['MWidgetSetting']['re_border_color'] === 'なし'){
      $inputData['MWidgetSetting']['re_border_color'] = 'none';
    }
    //訪問者側吹き出し枠線色
    if($inputData['MWidgetSetting']['se_border_color'] === 'なし'){
      $inputData['MWidgetSetting']['se_border_color'] = 'none';
    }
    //メッセージBOX枠線色
    if($inputData['MWidgetSetting']['message_box_border_color'] === 'なし'){
      $inputData['MWidgetSetting']['message_box_border_color'] = 'none';
    }
    //ウィジェット内枠線色
    if($inputData['MWidgetSetting']['widget_inside_border_color'] === 'なし'){
      $inputData['MWidgetSetting']['widget_inside_border_color'] = 'none';
    }

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

    //ウィジットサイズが中もしくは大の場合バリデーションの上限をトップタイトル、企業名、説明文のみ可変とする
    if($inputData['MWidgetSetting']['widget_size_type'] !== '1'){
      $titleLength = 12;
      $subTitleLength = 15;
      $descriptionLength = 15;
      switch ($inputData['MWidgetSetting']['widget_size_type']) {
        //大きさによってトップタイトル、企業名、説明文のmaxlengthを可変とする
        case '2': //中
          $titleLength = 16;
          $subTitleLength = 20;
          $descriptionLength = 20;
          $title_message = '１６文字以内で設定してください。';
          $subTitle_message = '２０文字以内で設定してください';
          $description_message = '２０文字以内で設定してください';
          break;
        case '3': //大
          $titleLength = 19;
          $subTitleLength = 24;
          $descriptionLength = 24;
          $title_message = '１９文字以内で設定してください。';
          $subTitle_message = '２４文字以内で設定してください';
          $description_message = '２４文字以内で設定してください';
          break;
      }
      $validate = $this->MWidgetSetting->validate;
      $validate['title']['maxLength']['rule'][1] = $titleLength;
      $validate['title']['maxLength']['message'] = $title_message;
      $validate['sub_title']['maxLength']['rule'][1] = $subTitleLength;
      $validate['sub_title']['maxLength']['message'] = $subTitle_message;
      $validate['description']['maxLength']['rule'][1] = $descriptionLength;
      $validate['description']['maxLength']['message'] = $description_message;
      $this->MWidgetSetting->validate = $validate;
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
            //最小化時のデザインタイプ
            if ( strcmp($v, 'minimize_design_type') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['minimize_design_type'] = C_MINIMIZED_DESIGN_NO_SIMPLE; // デフォルト値
            }
            //背景の影
            if ( strcmp($v, 'box_shadow') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['box_shadow'] = C_BOX_SHADOW; // デフォルト値
            }
            //閉じるボタン
            //閉じるボタン有効無効
            if ( strcmp($v, 'close_button_setting') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['close_button_setting'] = C_CLOSE_BUTTON_SETTING_OFF; // デフォルト値
            }
            //小さなバナー表示有効無効
            if ( strcmp($v, 'close_button_mode_type') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['close_button_mode_type'] = C_CLOSE_BUTTON_SETTING_MODE_TYPE_HIDDEN; // デフォルト値
            }
            //バナーテキスト
            if ( strcmp($v, 'bannertext') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['bannertext'] = C_BANNER_TEXT; // デフォルト値
            }
            //閉じるボタン
            /* カラー設定styat */
            //0.通常設定・高度設定
            if ( strcmp($v, 'color_setting_type') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['color_setting_type'] = COLOR_SETTING_TYPE_OFF; // デフォルト値
            }
            //1.メインカラー
            if ( strcmp($v, 'main_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['main_color'] = MAIN_COLOR; // デフォルト値
            }
            //2.タイトル文字色
            if ( strcmp($v, 'string_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['string_color'] = STRING_COLOR; // デフォルト値
            }
            //3.吹き出し文字色
            if ( strcmp($v, 'message_text_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['message_text_color'] = MESSAGE_TEXT_COLOR; // デフォルト値
            }
            //4.その他文字色
            if ( strcmp($v, 'other_text_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['other_text_color'] = OTHER_TEXT_COLOR; // デフォルト値
            }
            //5.ウィジェット枠線色
            if ( strcmp($v, 'widget_border_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['widget_border_color'] = WIDGET_BORDER_COLOR; // デフォルト値
            }
            //6.吹き出し枠線色
            if ( strcmp($v, 'chat_talk_border_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['chat_talk_border_color'] = CHAT_TALK_BORDER_COLOR; // デフォルト値
            }
            //7.企業名文字色
            if ( strcmp($v, 'sub_title_text_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              if($json['main_color'] && $json['main_color'] !== MAIN_COLOR){
                $d['sub_title_text_color'] = $json['main_color'];
              }
              else{
                $d['sub_title_text_color'] = SUB_TITLE_TEXT_COLOR; // デフォルト値
              }
            }
            //8.説明文文字色
            if ( strcmp($v, 'description_text_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['description_text_color'] = DESCRIPTION_TEXT_COLOR; // デフォルト値
            }
            //9.チャットエリア背景色
            if ( strcmp($v, 'chat_talk_background_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['chat_talk_background_color'] = CHAT_TALK_BACKGROUND_COLOR; // デフォルト値
            }
            //10.企業名担当者名文字色
            if ( strcmp($v, 'c_name_text_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              if($json['main_color'] && $json['main_color'] !== MAIN_COLOR){
                $d['c_name_text_color'] = $json['main_color'];
              }
              else{
                $d['c_name_text_color'] = C_NAME_TEXT_COLOR; // デフォルト値
              }
            }
            //11.企業側吹き出し文字色
            if ( strcmp($v, 're_text_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['re_text_color'] = RE_TEXT_COLOR; // デフォルト値
            }
            //12.企業側吹き出し背景色
            if ( strcmp($v, 're_background_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              if($json['main_color'] || $json['main_color'] !== MAIN_COLOR){
                //企業側吹き出し用の色をメインカラーから算出
                $main_color = $json['main_color'];
                $code = substr($main_color,1);
                if(strlen($code) === 3){
                  $r = substr($code,0,1).substr($code,0,1);
                  $g = substr($code,1,1).substr($code,1,1);
                  $b = substr($code,2).substr($code,2);
                }
                else{
                  $r = substr($code,0,2);
                  $g = substr($code,2,2);
                  $b = substr($code,4);
                }

                $balloonR = dechex(255 - (255 - intval($r,16)) * 0.1);
                $balloonG = dechex(255 - (255 - intval($g,16)) * 0.1);
                $balloonB = dechex(255 - (255 - intval($b,16)) * 0.1);
                $defColor = '#'.$balloonR.$balloonG.$balloonB;
                $d['re_background_color'] = $defColor;
              }
              else{
                $d['re_background_color'] = RE_BACKGROUND_COLOR; // デフォルト値
              }
            }
            //13.企業側吹き出し枠線色
            if ( strcmp($v, 're_border_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
//               if($json['re_border_color'] === 'false'){
//                 $d['re_border_color'] = 'false';
//               }
//               else{
                $d['re_border_color'] = RE_BORDER_COLOR; // デフォルト値
//               }
            }
//             //14.企業側吹き出し枠線なし
//             if ( strcmp($v, 're_border_none') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
//               $d['re_border_none'] = COLOR_SETTING_TYPE_OFF; // デフォルト値
//             }
            //15.訪問者側吹き出し文字色
            if ( strcmp($v, 'se_text_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['se_text_color'] = SE_TEXT_COLOR; // デフォルト値
            }
            //16.訪問者側吹き出し背景色
            if ( strcmp($v, 'se_background_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['se_background_color'] = SE_BACKGROUND_COLOR; // デフォルト値
            }
            //17.訪問者側吹き出し枠線色
            if ( strcmp($v, 'se_border_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['se_border_color'] = SE_BORDER_COLOR; // デフォルト値
            }
//             //18.訪問者側吹き出し枠線なし
//             if ( strcmp($v, 'se_border_none') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
//               $d['se_border_none'] = COLOR_SETTING_TYPE_OFF; // デフォルト値
//             }
            //19.メッセージエリア背景色
            if ( strcmp($v, 'chat_message_background_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['chat_message_background_color'] = CHAT_MESSAGE_BACKGROUND_COLOR; // デフォルト値
            }
            //20.メッセージBOX文字色
            if ( strcmp($v, 'message_box_text_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['message_box_text_color'] = MESSAGE_BOX_TEXT_COLOR; // デフォルト値
            }
            //21.メッセージBOX背景色
            if ( strcmp($v, 'message_box_background_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['message_box_background_color'] = MESSAGE_BOX_BACKGROUND_COLOR; // デフォルト値
            }
            //22.メッセージBOX枠線色
            if ( strcmp($v, 'message_box_border_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['message_box_border_color'] = MESSAGE_BOX_BORDER_COLOR; // デフォルト値
            }
//             //23.メッセージBOX枠線なし
//             if ( strcmp($v, 'message_box_border_none') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
//               $d['message_box_border_none'] = COLOR_SETTING_TYPE_OFF; // デフォルト値
//             }
            //24.送信ボタン文字色
            if ( strcmp($v, 'chat_send_btn_text_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              if($json['string_color'] && $json['string_color'] !== STRING_COLOR){
                $d['chat_send_btn_text_color'] = $json['string_color'];
              }
              else{
                $d['chat_send_btn_text_color'] = CHAT_SEND_BTN_TEXT_COLOR; // デフォルト値
              }
            }
            //25.送信ボタン背景色
            if ( strcmp($v, 'chat_send_btn_background_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              if($json['main_color'] && $json['main_color'] !== MAIN_COLOR){
                $d['chat_send_btn_background_color'] = $json['main_color'];
              }
              else{
                $d['chat_send_btn_background_color'] = CHAT_SEND_BTN_BACKGROUND_COLOR; // デフォルト値
              }
            }
            //26.ウィジット内枠線色
            if ( strcmp($v, 'widget_inside_border_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['widget_inside_border_color'] = WIDGET_INSIDE_BORDER_COLOR; // デフォルト値
            }
//             //26.ウィジット内枠線色
//             if ( strcmp($v, 'widget_inside_border_none') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
//               $d['widget_inside_border_none'] = COLOR_SETTING_TYPE_OFF; // デフォルト値
//             }
            /* カラー設定end */

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
