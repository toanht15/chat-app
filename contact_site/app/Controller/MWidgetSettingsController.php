<?php
/**
 * MWidgetSettingsController controller.
 * ウィジェット設定マスタ
 */
class MWidgetSettingsController extends AppController {
  public $uses = ['MWidgetSetting','MOperatingHour'];
  public $components = ['ImageTrimming', 'NodeSettingsReload'];
  public $helpers = ['ngForm'];
  public $coreSettings = null;
  public $styleSetting = [
    'common' => [
      'display_style_type', 'show_timing', 'max_show_timing_site', 'max_show_timing_page',
      'show_time', 'max_show_time', 'max_show_time_page', 'show_position', 'show_access_id', 'widget_size_type', 'title', 'show_subtitle', 'sub_title', 'show_description', 'description',
      'show_main_image', 'main_image', 'show_chatbot_icon' ,'chatbot_icon_type' ,'chatbot_icon' ,'show_operator_icon', 'operator_icon_type','operator_icon', 'radius_ratio', 'box_shadow', 'minimize_design_type','close_button_setting','close_button_mode_type','bannertext','widget_custom_height','widget_custom_width',
      /* カラー設定start */
      'color_setting_type','main_color','string_color','message_text_color','other_text_color','header_text_size','widget_border_color','chat_talk_border_color','header_background_color','sub_title_text_color','description_text_color',
      'chat_talk_background_color','c_name_text_color','re_text_color','re_text_size','re_background_color','re_border_color','re_border_none','se_text_color','se_text_size','se_background_color','se_border_color','se_border_none','chat_message_background_color',
      'message_box_text_color','message_box_text_size','message_box_background_color','message_box_border_color','message_box_border_none','chat_send_btn_text_color','chat_send_btn_text_size','chat_send_btn_background_color','widget_inside_border_color','widget_inside_border_none',
      'widget_title_top_type','widget_title_name_type','widget_title_explain_type', /* カラー設定end */
      /* 隠しパラメータstart */
      'btw_button_margin', 'line_button_margin','sp_banner_position','sp_scroll_view_setting','sp_banner_vertical_position_from_top','sp_banner_vertical_position_from_bottom','sp_banner_horizontal_position','sp_banner_text','sp_widget_view_pattern'
      /* 隠しパラメータend */
    ],
    'synclo' => ['tel', 'content', 'display_time_flg', 'time_text'],
    'chat' => ['chat_init_show_textarea', 'chat_radio_behavior', 'chat_trigger', 'show_name', 'show_automessage_name', 'show_op_name', 'chat_message_design_type', 'chat_message_with_animation', 'chat_message_copy', 'sp_show_flg', 'sp_header_light_flg', 'sp_auto_open_flg', 'sp_maximize_size_type'],
  ];



  public function beforeRender(){
    $this->set('title_for_layout', 'ウィジェット設定');
    $this->set('companyKey', $this->userInfo['MCompany']['company_key']);
  }

  /* *
   * 一覧画面
   * @return void
   * */
  public function index() {
    //$image->resize('/img/Penguins.jpg?1517909330', 60, 60, true);
    if ( $this->request->is('post') ) {
      $errors = $this->_update($this->request->data);
      if ( empty($errors) ) {
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));

        $this->redirect(['controller' =>'MWidgetSettings', 'action' => 'index', 'showTab' => $this->request->data['widget']['showTab']]);
      }
      else {
        $mWidgetSetting = $this->request->data['MWidgetSetting'];
        if($mWidgetSetting['re_border_none'] === '1'){
          $this->set('re_border_color_flg', false);
        }
        else{
          $this->set('re_border_color_flg', true);
        }
        if($mWidgetSetting['se_border_none'] === '1'){
          $this->set('se_border_color_flg', false);
        }
        else{
          $this->set('se_border_color_flg', true);
        }
        if($mWidgetSetting['message_box_border_none'] === '1'){
          $this->set('message_box_border_color_flg', false);
        }
        else{
          $this->set('message_box_border_color_flg', true);
        }
        if($mWidgetSetting['widget_outside_border_none'] === '1'){
          $this->set('widget_border_color_flg', false);
        }
        else{
          $this->set('widget_border_color_flg', true);
        }
        if($mWidgetSetting['widget_inside_border_none'] === '1'){
          $this->set('widget_inside_border_color_flg', false);
        }
        else{
          $this->set('widget_inside_border_color_flg', true);
        }
        $this->set('alertMessage', ['type' => C_MESSAGE_TYPE_ERROR, 'text' => Configure::read('message.const.saveFailed')]);
        //営業時間設定確認
        $operatingHourData = $this->MOperatingHour->find('first', ['conditions' => [
          'm_companies_id' => $this->userInfo['MCompany']['id']
        ]]);
        if(empty($operatingHourData)) {
          $operatingHourData['MOperatingHour']['active_flg'] = 2;
        }
        $this->set('operatingHourData',$operatingHourData['MOperatingHour']['active_flg']);
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
      if(array_key_exists ('re_border_color',$json)){
        if($json['re_border_color'] === 'none'){
          $this->set('re_border_color_flg', false);
          $inputData['MWidgetSetting']['re_border_color'] = 'なし';
          $inputData['MWidgetSetting']['re_border_none'] = true;
        }
        else{
          $this->set('re_border_color_flg', true);
        }
      }
      else{
        //初回読み込み時
//         $this->set('re_border_color_flg', false);
//         $inputData['MWidgetSetting']['re_border_color'] = 'なし';
//         $inputData['MWidgetSetting']['re_border_none'] = true;
        $this->set('re_border_color_flg', true);
      }
      if(array_key_exists ('se_border_color',$json)){
        if($json['se_border_color'] === 'none'){
          $this->set('se_border_color_flg', false);
          $inputData['MWidgetSetting']['se_border_color'] = 'なし';
          $inputData['MWidgetSetting']['se_border_none'] = true;
        }
        else{
          $this->set('se_border_color_flg', true);
        }
      }
      else{
        //初回読み込み時
//         $this->set('se_border_color_flg', false);
//         $inputData['MWidgetSetting']['se_border_color'] = 'なし';
//         $inputData['MWidgetSetting']['se_border_none'] = true;
        $this->set('se_border_color_flg', true);
      }
      if(array_key_exists ('message_box_border_color',$json)){
        if($json['message_box_border_color'] === 'none'){
          $this->set('message_box_border_color_flg', false);
          $inputData['MWidgetSetting']['message_box_border_color'] = 'なし';
          $inputData['MWidgetSetting']['message_box_border_none'] = true;
        }
        else{
          $this->set('message_box_border_color_flg', true);
        }
      }
      else{
        $this->set('message_box_border_color_flg', true);
      }
      //ウィジェット外枠線
      if(array_key_exists ('widget_border_color',$json)){
        if($json['widget_border_color'] === 'none'){
          $this->set('widget_border_color_flg', false);
          $inputData['MWidgetSetting']['widget_border_color'] = 'なし';
          $inputData['MWidgetSetting']['widget_outside_border_none'] = true;
        }
        else{
          $this->set('widget_border_color_flg', true);
        }
      }
      else{
        $this->set('widget_border_color_flg', true);
      }
      //ウィジェット内枠線
      if(array_key_exists ('widget_inside_border_color',$json)){
        if($json['widget_inside_border_color'] === 'none'){
          $this->set('widget_inside_border_color_flg', false);
          $inputData['MWidgetSetting']['widget_inside_border_color'] = 'なし';
          $inputData['MWidgetSetting']['widget_inside_border_none'] = true;
        }
        else{
          $this->set('widget_inside_border_color_flg', true);
        }
      }
      else{
        $this->set('widget_inside_border_color_flg', true);
      }
      //仕様変更常に高度な設定の設定値が反映されるようにする
      if(array_key_exists ('color_setting_type',$json)){
        if($json['color_setting_type'] === '1'){
          $inputData['MWidgetSetting']['color_setting_type'] = '0';
        }
      }

      $this->data = $inputData;

      //営業時間設定確認
      $operatingHourData = $this->MOperatingHour->find('first', ['conditions' => [
        'm_companies_id' => $this->userInfo['MCompany']['id']
      ]]);
      if(empty($operatingHourData)) {
        $operatingHourData['MOperatingHour']['active_flg'] = 2;
      }
      $this->set('operatingHourData',$operatingHourData['MOperatingHour']['active_flg']);
    }
    switch ($inputData['MWidgetSetting']['widget_size_type']) {
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
      case '4': //最大
        $titleLength = 19;
        $subTitleLength = 24;
        $descriptionLength = 24;
        break;
      default:
        $titleLength = 19;
        $subTitleLength = 24;
        $descriptionLength = 24;
    }
    switch ($inputData['MWidgetSetting']['widget_size_type']) {
      //大きさにより各種フォントサイズのmaxを可変とする(最大のみ別設定)
      case '1': //小
        $maxFontSize = 20;
        $maxHeaderFontSize = 20;
        $maxSendBtnFontSize = 26;
        break;
      case '2': //中
        $maxFontSize = 20;
        $maxHeaderFontSize = 20;
        $maxSendBtnFontSize = 30;
        break;
      case '3': //大
        $maxFontSize = 20;
        $maxHeaderFontSize = 20;
        $maxSendBtnFontSize = 36;
        break;
      case '4': //最大
        $maxFontSize = 64;
        $maxHeaderFontSize = 42;
        $maxSendBtnFontSize = 36;
        break;
      default:
        $maxFontSize = 64;
        $maxHeaderFontSize = 42;
        $maxSendBtnFontSize = 36;
    }

    $this->set('max_fontsize', $maxFontSize);
    $this->set('max_header_fontsize', $maxHeaderFontSize);
    $this->set('max_send_btn_fontsize', $maxSendBtnFontSize);
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
        'background-color' => $this->request->data['color'],
        'color' => $this->request->data['string_color']
      ];
    }

    $this->set('iconType', $this->request->data['iconType']);
    $this->set('cssStyle', $cssStyle);
    $this->_viewElement();

    $this->render('/Elements/MWidgetSettings/remoteGallary');
  }

  private function _viewElement() {
    $this->set('widgetDisplayType', Configure::read('WidgetDisplayType'));
    $this->set('widgetDisplayStyleType', Configure::read('WidgetDisplayStyleType'));
    $this->set('widgetPositionType', Configure::read('widgetPositionType'));
    $this->set('widgetShowAccessId', Configure::read('widgetShowAccessId'));
    $this->set('widgetShowNameType', Configure::read('widgetShowNameType'));
    $this->set('widgetShowAutomessageNameType', Configure::read('widgetShowAutomessageNameType'));
    $this->set('widgetShowOpNameType', Configure::read('widgetShowOpNameType'));
    $this->set('chatMessageDesignType', Configure::read('chatMessageDesignType'));
    $this->set('widgetSendActType', Configure::read('widgetSendActType'));
    $this->set('chatMessageCopy', Configure::read('chatMessageCopy'));
    $this->set('normalChoices', Configure::read('normalChoices')); // はい・いいえ
    $this->set('widgetShowChoices', Configure::read('widgetShowChoices')); // 表示する・表示しない
    $this->set('widgetRadioBtnBehaviorType', Configure::read('widgetRadioBtnBehaviorType'));
    $this->set('gallaryPath', C_NODE_SERVER_ADDR.C_NODE_SERVER_FILE_PORT.'/img/widget/');
    $this->set('spMiximizeSizeType', Configure::read('widgetSpMiximizeSizeType'));
    $this->set('widgetSpPositionType', Configure::read('widgetSpPositionType'));
    $this->set('widgetSpViewPattern' , Configure::read('widgetSpViewPattern'));
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
    //ウィジェット外枠線色
    if($inputData['MWidgetSetting']['widget_border_color'] === 'なし'){
      $inputData['MWidgetSetting']['widget_border_color'] = 'none';
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

    $uploadBotIcon = $inputData['MWidgetSetting']['uploadBotIcon'];

    $prevBotIconFileInfo = mb_split("/", $inputData['MWidgetSetting']['chatbot_icon']);
    if ( is_numeric($inputData['MWidgetSetting']['show_chatbot_icon']) === 1 && count($prevFileInfo) > 0 ) {
      $botIconFilename = $prevBotIconFileInfo[count($prevBotIconFileInfo) - 1];
    }

    if ( !(isset($uploadBotIcon['tmp_name']) && is_uploaded_file($uploadBotIcon['tmp_name'])) ) {
      $inputData['MWidgetSetting']['uploadBotIcon'] = [];
      $uploadBotIcon = null;
    }
    else {
      $this->request->data['MWidgetSetting']['chatbot_icon'] = "";
    }

    $uploadOpIcon = $inputData['MWidgetSetting']['uploadOpIcon'];

    $prevOpIconFileInfo = mb_split("/", $inputData['MWidgetSetting']['operator_icon']);
    if ( is_numeric($inputData['MWidgetSetting']['show_operator_icon']) === 1 && count($prevOpIconFileInfo) > 0 ) {
      $opIconFilename = $prevOpIconFileInfo[count($prevOpIconFileInfo) - 1];
    }

    if ( !(isset($uploadOpIcon['tmp_name']) && is_uploaded_file($uploadOpIcon['tmp_name'])) ) {
      $inputData['MWidgetSetting']['uploadOpIcon'] = [];
      $uploadOpIcon = null;
    }
    else {
      $this->request->data['MWidgetSetting']['operator_icon'] = "";
    }


    //仕様変更常に高度な設定の設定値が反映されるようにする
    $inputData['MWidgetSetting']['color_setting_type'] = "1";

    if($inputData['MWidgetSetting']['widget_size_type'] !== '5') {
      unset($inputData['MWidgetSetting']['widget_custom_width']);
      unset($inputData['MWidgetSetting']['widget_custom_height']);
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
        case '4': //最大
        case '5': //カスタム
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
        $imageConjunction = "_";
        $filename = $this->_startTrimming( $uploadImage, "info", $imageConjunction, $inputData );
        $inputData['MWidgetSetting']["main_custom_image"] = $filename;
      }
      if ( !empty($uploadBotIcon) ) {
        $botIconConjunction = "_botIcon";
        $botIconFilename = $this->_startTrimming( $uploadBotIcon, "botIconInfo", $botIconConjunction, $inputData );
        $inputData['MWidgetSetting']["bot_custom_icon"] = $botIconFilename;
      }
      if ( !empty($uploadOpIcon) ) {
        $opIconConjunction = "_opIcon";
        $opIconFilename = $this->_startTrimming( $uploadOpIcon, "opIconInfo", $opIconConjunction, $inputData );
        $inputData['MWidgetSetting']["op_custom_icon"] = $opIconFilename;
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
        $pattern = "files/".$this->userInfo['MCompany']['company_key']."_"."[0-9]*.*";
        $botIconPattern = "files/".$this->userInfo['MCompany']['company_key']."_botIcon"."[0-9]*.*";
        $opIconPattern = "files/".$this->userInfo['MCompany']['company_key']."_opIcon"."[0-9]*.*";

        foreach (glob($pattern) as $file) {
          if ( !empty($uploadImage) && strcmp("files/".$filename, $file) !== 0 ) {
            unlink($file);
          }
        }
        foreach (glob($botIconPattern) as $file) {
          if ( !empty($uploadBotIcon) && strcmp("files/".$botIconFilename, $file) !== 0 ) {
            unlink($file);
          }
        }
        foreach (glob($opIconPattern) as $file) {
          if ( !empty($uploadOpIcon) && strcmp("files/".$opIconFilename, $file) !== 0 ) {
            unlink($file);
          }
        }
        NodeSettingsReloadComponent::reloadWidgetSettings($this->userInfo['MCompany']['company_key']);
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

  private function _startTrimming( $targetImg, $trimmingKey, $conjunction, $inputData) {
    $extension = pathinfo($targetImg['name'], PATHINFO_EXTENSION);
    $filename = $this->userInfo['MCompany']['company_key'] . $conjunction . date('YmdHis') . '.' . $extension;
    $saveFile = C_PATH_WIDGET_IMG_DIR . DS . $filename;
    if (!empty($inputData['Trimming'][$trimmingKey])) {
      $trimmingInfo = json_decode($inputData['Trimming'][$trimmingKey], TRUE);
      $this->_useTrimmingInfo( $targetImg, $saveFile, $trimmingInfo);
    } else {
      $this->_notUseTrimmingInfo( $targetImg, $saveFile, $extension);
    }
    return $filename;
  }

  private function _useTrimmingInfo( $targetImg, $saveFile, $trimmingInfo ) {
    $component = new ImageTrimmingComponent();
    $component->setFileData($targetImg);
    $component->setSavePath($saveFile);
    $component->setX($trimmingInfo['x']);
    $component->setY($trimmingInfo['y']);
    $component->setWidth($trimmingInfo['width']);
    $component->setHeight($trimmingInfo['height']);
    $component->save();
  }

  private function _notUseTrimmingInfo( $targetImg, $saveFile, $extension ) {
    $tmpFile = $targetImg['tmp_name'];
    $in = $this->imageCreate($extension, $tmpFile); // 元画像ファイル読み込み
    $width = ImageSx($in); // 画像の幅を取得
    $height = ImageSy($in); // 画像の高さを取得
    $save_width = C_TRIMMING_MIN_WIDTH; // 幅の最低サイズ
    $save_height = C_TRIMMING_MIN_HEIGHT; // 高さの最低サイズ
    $image_type = exif_imagetype($tmpFile); // 画像タイプ判定用
    $out = ImageCreateTrueColor($save_width , $save_height);
    //ブレンドモードを無効にする
    imagealphablending($out, false);
    //完全なアルファチャネル情報を保存するフラグをonにする
    imagesavealpha($out, true);
    ImageCopyResampled($out, $in,0,0,0,0, $save_width, $save_height, $width, $height);
    $this->imageOut($extension, $out, $saveFile);
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
    if ( isset($settings['showChatbotIcon']) && strcmp($settings['showChatbotIcon'], "2") === 0 ) {
      $settings['chatbotIcon'] = "";
    }
    else if( !empty($settings['chatbotIcon']) ) {
      if ( isset($objData['bot_custom_icon']) ) {
        $settings['chatbotIcon'] = C_PATH_WIDGET_CUSTOM_IMG.'/'.$objData['bot_custom_icon'];
      }
      else if( !empty($settings['mainImage'] ) && strcmp($settings['chatBotIconType'], "1") === 0 ) {
        $settings['chatbotIcon'] = $settings['mainImage'];
      }
    }
    if ( isset($settings['showOperatorIcon']) && strcmp($settings['showOperatorIcon'], "2") === 0 ) {
      $settings['operatorIcon'] = "";
    }
    else if( !empty($settings['operatorIcon']) ) {
      if ( isset($objData['op_custom_icon']) ) {
        $settings['operatorIcon'] = C_PATH_WIDGET_CUSTOM_IMG.'/'.$objData['op_custom_icon'];
      }
      else if( !empty($settings['mainImage'] ) && strcmp($settings['operatorIconType'], "1") === 0 ) {
        $settings['operatorIcon'] = $settings['mainImage'];
      }
      else if( strcmp($settings['showOperatorIcon'], "1") === 0 && strcmp($settings['operatorIconType'], "3") === 0 ){
        $settings['operatorIcon'] = "";
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
            if ( strcmp($v, 'chat_init_show_textarea') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['chat_init_show_textarea'] = C_AUTO_WIDGET_TEXTAREA_OPEN; // デフォルト値
            }
            if ( strcmp($v, 'chat_radio_behavior') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['chat_radio_behavior'] = C_WIDGET_RADIO_CLICK_SEND; // デフォルト値
            }
            if ( strcmp($v, 'chat_trigger') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['chat_trigger'] = C_WIDGET_SEND_ACT_PUSH_KEY; // デフォルト値
            }
            if ( strcmp($v, 'show_name') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['show_name'] = strval(C_WIDGET_SHOW_COMP); // デフォルト値
              break;
            }
            if ( strcmp($v, 'show_automessage_name') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['show_automessage_name'] = strval(C_SELECT_CAN); // デフォルト値
              break;
            }
            if ( strcmp($v, 'show_op_name') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              if(isset($d['show_name']) && is_numeric($d['show_name'])) {
                $d['show_op_name'] = $d['show_name']; // 設定値が存在しない場合は既存使用に依存する
              } else {
                $d['show_op_name'] = strval(C_WIDGET_SHOW_COMP); // デフォルト値
              }
              break;
            }
            if ( strcmp($v, 'chat_message_design_type') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['chat_message_design_type'] = C_WIDGET_CHAT_MESSAGE_DESIGN_TYPE_BOX; // デフォルト値
            }
            if ( strcmp($v, 'chat_message_with_animation') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['chat_message_with_animation'] = C_CHECK_OFF; // デフォルト値（非選択状態：アニメーション無効）
            }
            if ( strcmp($v, 'chat_message_copy') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['chat_message_copy'] = C_WIDGET_CHAT_MESSAGE_CAN_COPY; // デフォルト値
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

            if ( strcmp($v, 'sp_maximize_size_type') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['sp_maximize_size_type'] = C_SELECT_CAN; // デフォルト値
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
            // 旧IFマージ
            if ( strcmp($v, "show_time") === 0 && isset($json[$v]) ) {
              if ( strcmp($json[$v], C_WIDGET_AUTO_OPEN_TYPE_ON) === 0 ) {
                $json['display_style_type'] = C_WIDGET_DISPLAY_STYLE_TYPE_MAX;
                $d['display_style_type'] = C_WIDGET_DISPLAY_STYLE_TYPE_MAX;
                $json['show_time'] = C_WIDGET_AUTO_OPEN_TYPE_NONE;
                $d['show_time'] = C_WIDGET_AUTO_OPEN_TYPE_NONE; // 自動で最大化しない
              }
              else if ( strcmp($json[$v], C_WIDGET_AUTO_OPEN_TYPE_OFF) === 0 ) {
                $json['display_style_type'] = C_WIDGET_DISPLAY_STYLE_TYPE_MIN;
                $d['display_style_type'] = C_WIDGET_DISPLAY_STYLE_TYPE_MIN;
                $json['show_time'] = C_WIDGET_AUTO_OPEN_TYPE_NONE;
                $d['show_time'] = C_WIDGET_AUTO_OPEN_TYPE_NONE; // 自動で最大化しない
              }
            }
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
            if ( strcmp($v, 'display_style_type') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['display_style_type'] = strval(C_WIDGET_DISPLAY_STYLE_TYPE_MIN); // デフォルト値
              if(isset($d['show_time']) && is_numeric($d['show_time']) && strcmp($d['show_time'], 1) === 0) {
                $d['display_style_type'] = strval(C_WIDGET_DISPLAY_STYLE_TYPE_MAX); // 常に最大化する設定がされていたらこちらにする
              }
              break;
            }

            // デフォルト値（プレミアムプランのみ表示する）
            if ( strcmp($v, 'show_access_id') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              if($this->coreSettings[C_COMPANY_USE_CHAT] && $this->coreSettings[C_COMPANY_USE_SYNCLO]){
                $d['show_access_id'] = C_SELECT_CAN;
              } else {
                $d['show_access_id'] = C_SELECT_CAN_NOT;
              }
            }
            //ウィジットサイズタイプ
            if ( strcmp($v, 'widget_size_type') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['widget_size_type'] = C_WIDGET_SIZE_TYPE_SMALL; // デフォルト値
            }
            //カスタム時横幅
            if ( strcmp($v, 'widget_custom_width') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              // デフォルト値はウィジェットサイズによって違う
              switch($d['widget_size_type']) {
                case C_WIDGET_SIZE_TYPE_SMALL:
                  $d['widget_custom_width'] = 285;
                  break;
                case C_WIDGET_SIZE_TYPE_MEDIUM:
                  $d['widget_custom_width'] = 344;
                  break;
                case C_WIDGET_SIZE_TYPE_LARGE:
                case C_WIDGET_SIZE_TYPE_MAXIMUM:
                  $d['widget_custom_width'] = 400;
                  break;
              }
            }
            //カスタム時高さ
            if ( strcmp($v, 'widget_custom_height') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              // デフォルト値はウィジェットサイズによって違う
              switch($d['widget_size_type']) {
                case C_WIDGET_SIZE_TYPE_SMALL:
                  $d['widget_custom_height'] = 194;
                  break;
                case C_WIDGET_SIZE_TYPE_MEDIUM:
                  $d['widget_custom_height'] = 284;
                  break;
                case C_WIDGET_SIZE_TYPE_LARGE:
                case C_WIDGET_SIZE_TYPE_MAXIMUM:
                  $d['widget_custom_height'] = 374;
                  break;
              }
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
            /* スマホ用隠しパラメータstart *
             * デフォルト値は機能が本格的に
             * 実装されるまで設定しない    */

            //スマホ小さなバナー縦の上から割合
            if ( strcmp($v, 'sp_banner_vertical_position_from_top') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['sp_banner_vertical_position_from_top'] = "50%"; // デフォルト値
            }

            //スマホ小さなバナー縦の下から割合
            /*if ( strcmp($v, 'sp_banner_vertical_position_from_bottom') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['sp_banner_vertical_position_from_bottom'] = "5px"; // デフォルト値
            }*/

            //スマホ小さなバナー横の割合
            if ( strcmp($v, 'sp_banner_horizontal_position') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['sp_banner_horizontal_position'] = "5px"; // デフォルト値
            }

            /* スマホ用隠しパラメータend */

            //スマホ_スクロール中の表示制御
            if ( strcmp($v, 'sp_scroll_view_setting') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d[' sp_scroll_view_setting'] = C_SP_SCROLL_VIEW_SETTING; // デフォルト値
            }

            //スマホ_小さなバナー表示位置
            if ( strcmp($v, 'sp_banner_position') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['sp_banner_position'] = $d['show_position']; // デフォルト値(PC版の右下・左下)
            }

            //スマホ_小さなバナーテキスト
            if ( strcmp($v, 'sp_banner_text') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_string($json[$v]))) ) {
              $d['sp_banner_text'] = $d['bannertext']; // デフォルト値(PC版のテキスト)
            }

            //スマホ_ウィジェット状態フラグ
            if ( strcmp($v, 'sp_widget_view_pattern') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['sp_widget_view_pattern'] = C_WIDGET_SP_VIEW_THERE_PATTERN_BANNER; // デフォルト値
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
            //ヘッダー文字サイズ
            if ( strcmp($v, 'header_text_size') === 0 && (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              switch(intval($d['widget_size_type'])) {
                case 1:
                  $d['header_text_size'] = "14";
                  break;
                case 2:
                case 3:
                case 4:
                  $d['header_text_size'] = "15";
                  break;
                default:
                  $d['header_text_size'] = "15"; // 中
                  break;
              }
              // 空文字列が設定されていると後続の処理で上書きされるためここでbreakする
              break;
            }
            //5.ウィジェット枠線色
            if ( strcmp($v, 'widget_border_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['widget_border_color'] = WIDGET_BORDER_COLOR; // デフォルト値
            }
            //6.ヘッダー背景色
            if ( strcmp($v, 'header_background_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['header_background_color'] = HEADER_BACKGROUND_COLOR; // デフォルト値
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
            //企業側吹き出し文字サイズ
            if ( strcmp($v, 're_text_size') === 0 && (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              switch(intval($d['widget_size_type'])) {
                case 1:
                  $d['re_text_size'] = "12";
                  break;
                case 2:
                case 3:
                case 4:
                  $d['re_text_size'] = "13";
                  break;
                default:
                  $d['re_text_size'] = "13"; // 中
                  break;
              }
              // 空文字列が設定されていると後続の処理で上書きされるためここでbreakする
              break;
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
            //訪問者側吹き出し文字サイズ
            if ( strcmp($v, 'se_text_size') === 0 && (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              switch(intval($d['widget_size_type'])) {
                case 1:
                  $d['se_text_size'] = "12";
                  break;
                case 2:
                case 3:
                case 4:
                  $d['se_text_size'] = "13";
                  break;
                default:
                  $d['se_text_size'] = "13"; // 中
                  break;
              }
              // 空文字列が設定されていると後続の処理で上書きされるためここでbreakする
              break;
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
            //メッセージBOX文字サイズ
            if ( strcmp($v, 'message_box_text_size') === 0 && (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              switch(intval($d['widget_size_type'])) {
                case 1:
                  $d['message_box_text_size'] = "12";
                  break;
                case 2:
                case 3:
                case 4:
                  $d['message_box_text_size'] = "13";
                  break;
                default:
                  $d['message_box_text_size'] = "13"; // 中
                  break;
              }
              // 空文字列が設定されていると後続の処理で上書きされるためここでbreakする
              break;
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
            //送信ボタン文字サイズ
            if ( strcmp($v, 'chat_send_btn_text_size') === 0 && (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              switch(intval($d['widget_size_type'])) {
                case 1:
                  $d['chat_send_btn_text_size'] = "12";
                  break;
                case 2:
                case 3:
                case 4:
                  $d['chat_send_btn_text_size'] = "13";
                  break;
                default:
                  $d['chat_send_btn_text_size'] = "13"; // 中
                  break;
              }
              // 空文字列が設定されていると後続の処理で上書きされるためここでbreakする
              break;
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

            //無人対応アイコン設定
            if ( strcmp($v, 'show_chatbot_icon') === 0 && (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['show_chatbot_icon'] = C_CHATBOT_ICON_SETTING_OFF;
            }

            //無人対応アイコンタイプ
            if ( strcmp($v, 'chatbot_icon_type') === 0 && (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['chatbot_icon_type'] = ICON_USE_MAIN_IMAGE;
            }

            //有人対応アイコン設定
            if ( strcmp($v, 'show_operator_icon') === 0 && (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['show_operator_icon'] = C_OPERATOR_ICON_SETTING_OFF;
            }

            //有人対応アイコンタイプ
            if ( strcmp($v, 'operator_icon_type') === 0 && (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['operator_icon_type'] = ICON_USE_MAIN_IMAGE;
            }

            //行：ラジオボタン間マージン
            if ( strcmp($v, 'line_button_margin') === 0 && (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['line_button_margin'] = 8;
            }

            //ラジオボタン間マージン
            if ( strcmp($v, 'btw_button_margin') === 0 && (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['btw_button_margin'] = 4;
            }

            //タイトル位置
            if ( strcmp($v, 'widget_title_top_type') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['widget_title_top_type'] = WIDGET_TITLE_TOP_TYPE_CENTER; // デフォルト値
            }

            //企業名位置
            if ( strcmp($v, 'widget_title_name_type') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['widget_title_name_type'] = WIDGET_TITLE_NAME_TYPE_LEFT; // デフォルト値
            }

            //説明文位置
            if ( strcmp($v, 'widget_title_explain_type') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['widget_title_explain_type'] = WIDGET_TITLE_EXPLAIN_TYPE_LEFT; // デフォルト値
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

/*$app->post('/cropper', function(Request $request){

  $str =  str_random(7);

  $crop =  value(function() use ($request, $str) {*/
  public function trimming() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';

    $this->log('入ってるかチェック',LOG_DEBUG);
    $this->log($this->request->data,LOG_DEBUG);
    $request = $this->request->data['image'];
    $str = str_random(7);
     // Laravelの場合は public_path()ヘルパー関数、Facadeが使えます
     $image = Image::make('../public/img/art.jpg')
             ->crop(
                    $request->get('width'),
                    $request->get('height'),
                    $request->get('x'),
                    $request->get('y')
                  )
             ->resize(256,256) // 256 * 256にリサイズ
             // 画像の保存
             ->save('../public/img/'. $str . '.jpg')
             ->resize(128,128) //サムネイル用にリサイズ
             ->save('../public/img/'. $str . '_t' . '.jpg');
      // \File::delete('Your image File);
      $this->log('ここまで来てない？',LOG_DEBUG);
      $this->log($image,LOG_DEBUG);
      return $image ?: false;
      }

  public function remoteTimmingInfo() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $this->render('/Elements/MWidgetSettings/remoteTimmingInfo');
  }

  public function cropper() {
    header ('Content-Type: image/png');
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $this->log('入った',LOG_DEBUG);
    $file1 = "/img/company_g.png?1490169962";                            //　元画像ファイル
    $file2 = "/img/company_g.png?1490169963";                                //　画像保存先
    $this->log('画像チェック12',LOG_DEBUG);
    $this->log($file1 ,LOG_DEBUG);
    $img = imagecreatefromjpeg($file1);                            //　元画像
    $this->log('画像チェック2',LOG_DEBUG);
    $w = array(100, 120);                                    //　切り出し開始位置,サイズ（横）
    $h = array(110, 100);                                    //　切り出し開始位置,サイズ（縦）
    $out = ImageCreateTrueColor($w[1], $h[1]);                        //　画像を生成
    ImageCopyResampled($out, $img, 0, 0, $w[0], $h[0], $w[1], $h[1], $w[1], $h[1]);        //　サイズ変更・コピー
    ImageJPEG($out, $file2);                                //　画像表示
    ImageDestroy($img);
    ImageDestroy($out);
    $this->log('画像チェック',LOG_DEBUG);
    $this->log($out,LOG_DEBUG);
    $this->log($file2,LOG_DEBUG);

  }



}
