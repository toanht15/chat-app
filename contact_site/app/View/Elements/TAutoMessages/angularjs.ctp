<script type="text/javascript">
'use strict';

var sincloApp = angular.module('sincloApp', ['ngSanitize', 'ui.validate']);

sincloApp.controller('MainController', function($scope) {
    //thisを変数にいれておく
    var self = this;

    var setActivity = <?=( !empty($this->data['TAutoMessage']['activity']) ) ? json_encode($this->data['TAutoMessage']['activity'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) : "{}" ?>;
    this.setItemList = {};
    var setItemListTmp = (typeof(setActivity) === "string") ? JSON.parse(setActivity) : setActivity;
    if ( 'conditions' in setItemListTmp ) {
        this.setItemList = setItemListTmp['conditions'];
    }
    this.keys = function(obj){
      //営業時間を利用しない場合
      if(<?= $operatingHourData ?> == 2) {
        delete obj[4];
      }
      if (angular.isObject(obj)) {
          return Object.keys(obj).length;
      }
      else {
          return obj.length;
      }
    };

    this.tmpList = <?php echo json_encode($outMessageTriggerList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);?>;

    $scope.decodeHtmlSpecialChar = function(action) {
      $scope.action = $("<div/>").html( action ).text();
    };

    $scope.$watch(function(){
      return self.setItemList;
    });

    this.checkDisabled = function(itemId){
        //営業時間設定を利用しない場合
        if(<?= $operatingHourData ?> == 2 && itemId == 4) {
          //ツールチップ表示
          $('#triggerList div ul li').each(function(i){
            if(i == 3) {
              $(this).addClass("commontooltip");
              $(this).attr('data-text', 'こちらの機能は営業時間設定で「利<br>用する」を選択すると、ご利用いただけます。');
              $(this).attr('data-balloon-position', '14');
              $(this).attr('operatingHours', 'widgetHoursPage');
            }
          });
          return true;
        }
        return (itemId in this.setItemList && this.setItemList[itemId].length >= this.tmpList[itemId].createLimit[this.condition_type]);
    };

    this.addItem = function(tmpId){
        if ( tmpId in this.tmpList ) {
            if ( !(tmpId in this.setItemList) ) {
                this.setItemList[tmpId] = [];
            }
            else if (tmpId in this.setItemList && this.setItemList[tmpId].length >= this.tmpList[tmpId].createLimit[this.condition_type]) {
                return false;
            }
            //営業時間設定を利用しない場合
            if(<?= $operatingHourData ?> == 2 && tmpId == 4) {
              return false;
            }
            if(tmpId === "<?= C_AUTO_TRIGGER_STAY_PAGE ?>"
              || tmpId === "<?= C_AUTO_TRIGGER_REFERRER ?>"
              || tmpId === "<?= C_AUTO_TRIGGER_SPEECH_CONTENT ?>"
              || tmpId === "<?= C_AUTO_TRIGGER_STAY_PAGE_OF_FIRST ?>"
              || tmpId === "<?= C_AUTO_TRIGGER_STAY_PAGE_OF_PREVIOUS ?>") {
              this.tmpList[tmpId].default['keyword_contains_enabled'] = true;
              this.tmpList[tmpId].default['keyword_exclusions_enabled'] = false;
            }
            this.setItemList[tmpId].push(angular.copy(this.tmpList[tmpId].default));
        }
    };

    this.openList = function(elm){
        var target = null;
        target = $(String(elm));
        if (!target.is(".selected")) {
            target.css('height', target.children("ng-form").children("div").prop("offsetHeight") + 34 + "px").addClass("selected");
        }
        else {
            target.css('height', "34px").removeClass("selected");
        }
    };

    this.requireCheckBox = function(form){
        if (form === undefined) return false;
        var ret = Object.keys(form).filter(function(k) {
            return form[k] == true;
        })[0];
        return ( ret === undefined || ret.length === 0 );
    };

    this.removeItem = function(itemType, itemId){
        if ( itemType in this.setItemList ) {
            if ( itemId in this.setItemList[itemType] ) {
                if ( Object.keys(this.setItemList[itemType]).length === 1 ) {
                    delete this.setItemList[itemType];
                }
                else {
                    this.setItemList[itemType].splice(itemId, 1);
                }
                angular.bind(this, function() {
                    this.setItemList = self.setItemList;
                    $scope.$apply();
                })
                $("div.balloon").hide();
            }
        }
    };

    this.saveAct = function(){
        var setList = {
            'conditionType': Number(this.condition_type),
            'conditions': angular.copy(this.setItemList),
            'widgetOpen': Number(this.widget_open),
             // TODO 後々動的に
            'message': angular.element("#TAutoMessageAction").val(),
            'chatTextarea': Number(this.chat_textarea),
            'cv': Number(this.cv),
        };
        var keys = Object.keys(setList['conditions']);
        for (var i = 0; i < keys.length; i++) {
          var target = String(keys[i]);
          switch(target) {
            case "<?=C_AUTO_TRIGGER_DAY_TIME?>":
              for (var j = 0; setList['conditions'][target].length > j; j++) {
                if ('timeSetting' in setList['conditions'][target][j] && Number(setList['conditions'][target][j].timeSetting) === 2) {
                  delete setList['conditions'][target][j]['startTime'];
                  delete setList['conditions'][target][j]['endTime'];
                }
              }
              break;
            case "<?=C_AUTO_TRIGGER_STAY_PAGE?>":
            case "<?=C_AUTO_TRIGGER_REFERRER?>":
            case "<?=C_AUTO_TRIGGER_SPEECH_CONTENT?>":
            case "<?=C_AUTO_TRIGGER_STAY_PAGE_OF_FIRST?>":
            case "<?=C_AUTO_TRIGGER_STAY_PAGE_OF_PREVIOUS?>":
              for (var j = 0; setList['conditions'][target].length > j; j++) {
                if(!setList['conditions'][target][j]['keyword_contains_enabled']) {
                  setList['conditions'][target][j]['keyword_contains'] = "";
                  setList['conditions'][target][j]['keyword_contains_type'] = "1";
                }
                if(!setList['conditions'][target][j]['keyword_exclusions_enabled']) {
                  setList['conditions'][target][j]['keyword_exclusions'] = "";
                  setList['conditions'][target][j]['keyword_exclusions_type'] = "1";
                }
                delete setList['conditions'][target][j]['keyword_contains_enabled'];
                delete setList['conditions'][target][j]['keyword_exclusions_enabled'];
              }
              break;
          }
        }

        $('#TAutoMessageActivity').val(JSON.stringify(setList));
        submitAct();
    };

    this.isVisitCntRule = function(cnt, cond){
        if ( Number(cond) === 3 && Number(cnt) === 1) {
            return false;
        }
        return true;
    };

    // ウィジェットのシミュレーター表示
    var json = JSON.parse(document.getElementById('TAutoMessageWidgetSettings').value);
    var widgetSettings = [];
    for (var item in json) {
      widgetSettings[item] = json[item];
    }
    $scope.widgetSettings = widgetSettings;

    var coreSettingsChat = "<?= $coreSettings[C_COMPANY_USE_CHAT]?>";
    $scope.main_image = $scope.widgetSettings['main_image'];

    $scope.showWidgetType = 1; // デフォルト表示するウィジェット
    $scope.openFlg = true;

    $scope.widgetSizeTypeToggle = $scope.widgetSettings['widget_size_type'];
    $scope.subTitleToggle = $scope.widgetSettings['show_subtitle'];
    $scope.descriptionToggle = $scope.widgetSettings['show_description'];
    $scope.mainImageToggle = $scope.widgetSettings['show_main_image'];
    $scope.minimizedDesignToggle = $scope.widgetSettings['minimize_design_type'];
    $scope.closeButtonSettingToggle = $scope.widgetSettings['close_button_setting'];
    $scope.closeButtonModeTypeToggle = $scope.widgetSettings['close_button_mode_type'];
    $scope.timeTextToggle = $scope.widgetSettings['display_time_flg'];

    $scope.chat_message_copy = $scope.widgetSettings['chat_message_copy'];
    $scope.chat_message_design_type = $scope.widgetSettings['chat_message_design_type'];
    $scope.widget_outside_border_none = $scope.widgetSettings['widget_border_color'] === 'なし';
    $scope.widget_inside_border_none = $scope.widgetSettings['widget_inside_border_color'] === 'なし';
    $scope.re_border_none = $scope.widgetSettings['re_border_color'] === 'なし';
    $scope.se_border_none = $scope.widgetSettings['se_border_color'] === 'なし';
    $scope.show_name = <?=C_WIDGET_SHOW_COMP?>; // オートメッセージは企業名で表示する
    $scope.message_box_border_none = $scope.widgetSettings['message_box_border_color'] === 'なし';

    $scope.switchWidget = function(num){
      $scope.showWidgetType = num;
      sincloChatMessagefocusFlg = true;
      var sincloBox = document.getElementById("sincloBox");

      if ( Number(num) === 3 ) { // ｽﾏｰﾄﾌｫﾝ（縦）の表示
        $scope.widget.showTab = 'chat'; // 強制でチャットにする
      }

      if ( Number(num) !== 2 ) { // ｽﾏｰﾄﾌｫﾝ（横）以外は最大化する
        if(sincloBox){
          if(sincloBox.style.display == 'none'){
            sincloBox.style.display = 'block';
          }
        }
        /* ウィジェットが最小化されていたら最大化する */
        if ( !$scope.openFlg ) { // 最小化されている場合
          var main = document.getElementById("miniTarget");  // 非表示対象エリア
          var height = 0;
          if(main){
            for(var i = 0; main.children.length > i; i++){ // 非表示エリアのサイズを計測する
              if ( Number(num) === 3 && main.children[i].id === 'navigation' ) continue; // SPの場合はナビゲーションは基本表示しない
              height += main.children[i].offsetHeight;
            }
            main.style.height = height + "px";
          }
        }
      }
      if( Number(num) !== 4 ){
        if(coreSettingsChat){
          document.getElementById("switch_widget").value = num;
        }
      }
      $scope.openFlg = true;

      setTimeout(function(){
        $scope.createMessage();
        $scope.toggleChatTextareaView($scope.main.chat_textarea);
      },0);
    }

    $scope.$watch('chat_trigger', function(){
      if ( Number($scope.widgetSettings.chat_trigger) === 1 ) {
        $scope.widgetSettings.chat_area_placeholder_pc = "（Shift+Enterで改行/Enterで送信）";
        $scope.widgetSettings.chat_area_placeholder_sp = "（改行で送信）";
      }
      else {
        $scope.widgetSettings.chat_area_placeholder_pc = "";
        $scope.widgetSettings.chat_area_placeholder_sp = "";
      }
    });

    $scope.makeFaintColor = function(){
      var defColor = "#F1F5C8";
      //仕様変更、常に高度な設定が当たっている状態とする
      defColor = $scope.widgetSettings.re_background_color;
//       if($scope.color_setting_type === '1'){
//         defColor = $scope.re_background_color;
//       }
//       else{
//         if ( $scope.main_color.indexOf("#") >= 0 ) {
//           var code = $scope.main_color.substr(1), r,g,b;
//           if (code.length === 3) {
//             r = String(code.substr(0,1)) + String(code.substr(0,1));
//             g = String(code.substr(1,1)) + String(code.substr(1,1));
//             b = String(code.substr(2)) + String(code.substr(2));
//           }
//           else {
//             r = String(code.substr(0,2));
//             g = String(code.substr(2,2));
//             b = String(code.substr(4));
//           }
//           var balloonR = String(Math.floor(255 - (255 - parseInt(r,16)) * 0.1));
//           var balloonG = String(Math.floor(255 - (255 - parseInt(g,16)) * 0.1));
//           var balloonB = String(Math.floor(255 - (255 - parseInt(b,16)) * 0.1));
//           defColor = 'rgb(' + balloonR  + ', ' +  balloonG  + ', ' +  balloonB + ')';
//         }
//       }
      return defColor;
    };

    $scope.getTalkBorderColor = function(chk){
      var defColor = "#E8E7E0";
      //仕様変更、常に高度な設定が当たっている状態とする
      if(chk === 're'){
        defColor = $scope.widgetSettings.re_border_color;
      }
      else{
        defColor = $scope.widgetSettings.se_border_color;
      }
//       if($scope.color_setting_type === '1'){
//         if(chk === 're'){
//           defColor = $scope.re_border_color;
//         }
//         else{
//           defColor = $scope.se_border_color;
//         }
//       }
//       else{
//         defColor = $scope.chat_talk_border_color;
//       }
      return defColor;
    }

    $scope.getSeBackgroundColor = function(){
      var defColor = "#FFFFFF";
      //仕様変更、常に高度な設定が当たっている状態とする
      defColor = $scope.widgetSettings.se_background_color;
//       if($scope.color_setting_type === '1'){
//         defColor = $scope.se_background_color;
//       }
      return defColor;
    }

    //シンプル表示判定
    /*
    * 最小化時のデザイン
    * $scope.minimizedDesignToggle = 1/2/3:シンプル表示しない/スマホのみシンプル表示する/すべての端末でシンプル表示する
    * $scope.showWidgetType = 1/3:通常/スマホ（縦）
    * 最大時のシンプル表示(スマホ)
    * $scope.sp_header_light_flg = 0/1:しない/する
    * $scope.openFlg = true/false:最大化/最小化
    */
    $scope.spHeaderLightToggle = function(){
      switch ($scope.minimizedDesignToggle) {
      case "1": //シンプル表示しない
        if($scope.showWidgetType === 1){
          //通常（PC）
          if($scope.widgetSettings.sp_header_light_flg === '<?=C_SELECT_CAN?>'){
            //最大時のシンプル表示(スマホ)する
            if(!$scope.openFlg){
              //最小化中
              var res = false;
            }
            else{
              //最大化中
              var res = false;
            }
          }
          else{
            //最大時のシンプル表示(スマホ)しない
            if(!$scope.openFlg){
              //最小化中
              var res = false;
            }
            else{
              //最大化中
              var res = false;
            }
          }
        }
        else{
          //スマホ（縦）
          if($scope.widgetSettings.sp_header_light_flg === '<?=C_SELECT_CAN?>'){
            //最大時のシンプル表示(スマホ)する
            if(!$scope.openFlg){
              //最小化中
              var res = false;
            }
            else{
              //最大化中
              var res = true;
            }
          }
          else{
            //最大時のシンプル表示(スマホ)しない
            if(!$scope.openFlg){
              //最小化中
              var res = false;
            }
            else{
              //最大化中
              var res = false;
            }
          }
        }
        break;
      case "2": //スマホのみシンプル表示する
        if($scope.showWidgetType === 1){
          //通常（PC）
          if($scope.widgetSettings.sp_header_light_flg === '<?=C_SELECT_CAN?>'){
            //最大時のシンプル表示(スマホ)する
            if(!$scope.openFlg){
              //最小化中
              var res = false;
            }
            else{
              //最大化中
              var res = false;
            }
          }
          else{
            //最大時のシンプル表示(スマホ)しない
            if(!$scope.openFlg){
              //最小化中
              var res = false;
            }
            else{
              //最大化中
              var res = false;
            }
          }
        }
        else{
          //スマホ（縦）
          if($scope.widgetSettings.sp_header_light_flg === '<?=C_SELECT_CAN?>'){
            //最大時のシンプル表示(スマホ)する
            if(!$scope.openFlg){
              //最小化中
              var res = true;
            }
            else{
              //最大化中
              var res = true;
            }
          }
          else{
            //最大時のシンプル表示(スマホ)しない
            if(!$scope.openFlg){
              //最小化中
              var res = true;
            }
            else{
              //最大化中
              var res = false;
            }
          }
        }
        break;
      case "3": //すべての端末でシンプル表示する
        if($scope.showWidgetType === 1){
          //通常（PC）
          if($scope.widgetSettings.sp_header_light_flg === '<?=C_SELECT_CAN?>'){
            //最大時のシンプル表示(スマホ)する
            if(!$scope.openFlg){
              //最小化中
              var res = true;
            }
            else{
              //最大化中
              var res = false;
            }
          }
          else{
            //最大時のシンプル表示(スマホ)しない
            if(!$scope.openFlg){
              //最小化中
              var res = true;
            }
            else{
              //最大化中
              var res = false;
            }
          }
        }
        else{
          //スマホ（縦）
          if($scope.widgetSettings.sp_header_light_flg === '<?=C_SELECT_CAN?>'){
            //最大時のシンプル表示(スマホ)する
            if(!$scope.openFlg){
              //最小化中
              var res = true;
            }
            else{
              //最大化中
              var res = true;
            }
          }
          else{
            //最大時のシンプル表示(スマホ)しない
            if(!$scope.openFlg){
              //最小化中
              var res = true;
            }
            else{
              //最大化中
              var res = false;
            }
          }
        }
        break;
      }
      if($scope.openFlg){
        //最大化時
        $("#minimizeBtn").show();
        $("#addBtn").hide();
        $("#closeBtn").hide();
      }
      else{
        //最小化時
        $("#addBtn").show();
        $("#minimizeBtn").hide();
        if($scope.closeButtonSettingToggle === '2'){
          $("#closeBtn").show();
        }
        else{
          $("#closeBtn").hide();
        }
        var coreSettingsChat = "<?= $coreSettings[C_COMPANY_USE_CHAT]?>";
        if(coreSettingsChat){
          document.getElementById("switch_widget").value = $scope.showWidgetType;
        }
      }
      return res;
    };

    //位置調整
    $scope.$watch(function(){
      return {'openFlg': $scope.openFlg, 'showWidgetType': $scope.showWidgetType, 'widgetSizeType': $scope.widgetSizeTypeToggle, 'chat_radio_behavior': $scope.widgetSettings.chat_radio_behavior, 'chat_trigger': $scope.chat_trigger, 'show_name': $scope.widgetSettings.show_name, 'widget.showTab': $scope.widget.showTab};
    },
    function(){
      var main = document.getElementById("miniTarget");
      if ( !main ) return false;
      if ( $scope.openFlg ) {
        setTimeout(function(){
          angular.element("#sincloBox").addClass("open");
          var height = 0;
          for(var i = 0; main.children.length > i; i++){
              height += main.children[i].offsetHeight;
          }
          main.style.height = height + "px";
        }, 0);
      }
      else {
        angular.element("#sincloBox").removeClass("open");
        main.style.height = "0";
      }
    }, true);

    $scope.addOption = function(type) {
      var sendMessage = document.getElementById('TAutoMessageAction');
      switch(type){
        case 1:
          if (sendMessage.value.length > 0) {
              sendMessage.value += "\n";
          }
          sendMessage.value += "[] ";
          sendMessage.focus();
          break;
        case 2:
          if (sendMessage.value.length > 0) {
            sendMessage.value += "\n";
          }
          sendMessage.value += "<telno></telno>";
          sendMessage.focus();
          // 開始と終了タブの真ん中にカーソルを配置する
          if (sendMessage.createTextRange) {
            var range = sendMessage.createTextRange();
            range.move('character', sendMessage.value.length-8);
            range.select();
          } else if (sendMessage.setSelectionRange) {
            sendMessage.setSelectionRange(sendMessage.value.length, sendMessage.value.length-8);
          }
          break;
      }
      $scope.action = sendMessage.value;
      // シミュレーター上のメッセージ表示更新
      $scope.createMessage();
    }

    //位置調整
    $scope.$watch(function(){
      return {'widgetSizeType': $scope.widgetSizeTypeToggle};
    },
    function(){
      $scope.switchWidget(1); // 標準に切り替える
    }, true);

    // シミュレーター上のメッセージ表示更新
    angular.element(window).on('load', function(e) {
      $('#TAutoMessageAction').on('keydown keyup', function(e) {
        $scope.createMessage();
      });
      $(document).on('keyup', 'input[name^="keyword_"]', function(e){
        var val = $(this).val();
        console.log(val);
        if(val.replace(/".*?"/g, "").match(/.+\s+.+/)) {
          if($(this).next('select').val() === "1") {
            $(this).parents('li').next('li').find('input[value="1"]').prop("disabled", true).parents('label').css('color', '#CCCCCC');
            $(this).parents('li').next('li').find('input[value="2"]').attr("checked", true);
          } else {
            $(this).parents('li').next('li').find('input[value="1"]').prop("disabled", false).parents('label').css('color', '#595959');
          }
        } else {
          $(this).parents('li').next('li').find('input[value="1"]').prop("disabled", false).parents('label').css('color', '#595959');
        }
      });
      $(document).on('change', '.searchKeywordContainsTypeSelect,.searchKeywordExclusionsTypeSelect', function(){
        var target = $(this).parents('.keywordWrapper').find('input[name^="keyword_"]');
        target.each(function(elm){
          var val = $(this).val();
          console.log(val);
          if(val.replace(/".*?"/g, "").match(/.+\s+.+/)) {
            if($(this).next('select').val() === "1") {
              $(this).parents('li').next('li').find('input[value="1"]').prop("disabled", true).parents('label').css('color', '#CCCCCC');
              $(this).parents('li').next('li').find('input[value="2"]').attr("checked", true);
              return false;
            } else {
              $(this).parents('li').next('li').find('input[value="1"]').prop("disabled", false).parents('label').css('color', '#595959');
            }
          } else {
            $(this).parents('li').next('li').find('input[value="1"]').prop("disabled", false).parents('label').css('color', '#595959');
          }
        });
      });
      $scope.$watch('main.chat_textarea', function(value) {
        $scope.toggleChatTextareaView(value);
      });
    });
    $scope.createMessage = function() {
      var isSmartphone = $scope.showWidgetType != 1;
      var val = document.getElementById('TAutoMessageAction').value;
      var messageElement = document.querySelector('#sample_widget_re_message .details:not(.cName)');
      if(!messageElement) return;

      var strings = val.split('\n');
      var radioCnt = 1;
      var linkReg = RegExp(/(http(s)?:\/\/[\w\-\.\/\?\=\&\;\,\#\:\%\!\(\)\<\>\"\u3000-\u30FE\u4E00-\u9FA0\uFF01-\uFFE3]+)/);
      var telnoTagReg = RegExp(/&lt;telno&gt;([\s\S]*?)&lt;\/telno&gt;/);
      var htmlTagReg = RegExp(/<\/?("[^"]*"|'[^']*'|[^'">])*>/g)
      var radioName = "sinclo-radio0";
      var content = "";

      for (var i = 0; strings.length > i; i++) {
        var str = escape_html(strings[i]);

        // リンク
        var link = str.match(linkReg);
        if ( link !== null ) {
            var url = link[0];
            var a = "<a href='" + url + "' target='_blank'>" + url + "</a>";
            str = str.replace(url, a);
        }
        // ラジオボタン
        var radio = str.indexOf('[]');
        if ( radio > -1 ) {
            var value = str.slice(radio+2);
            var name = value.replace(htmlTagReg, '');
            str = "<span class='sinclo-radio'><input type='radio' name='" + radioName + "' id='" + radioName + "-" + i + "' class='sinclo-chat-radio' value='" + name + "'>";
            str += "<label for='" + radioName + "-" + i + "'>" + value + "</label></span>";
        }
        // 電話番号（スマホのみリンク化）
        var tel = str.match(telnoTagReg);
        if( tel !== null ) {
          var telno = tel[1];
          if(isSmartphone) {
            // リンクとして有効化
            var a = "<a href='tel:" + telno + "'>" + telno + "</a>";
            str = str.replace(tel[0], a);
          } else {
            // ただの文字列にする
            var span = "<span class='telno'>" + telno + "</span>";
            str = str.replace(tel[0], span);
          }
        }
        content += str + "\n";
      }

      // プレビューの吹き出し表示制御
      if(content.length > 1) {
        document.getElementById('sample_widget_re_message').style.display = "";
        messageElement.innerHTML = content;
      } else {
        document.getElementById('sample_widget_re_message').style.display = "none";
      }
    }

    // 自由入力エリアの表示・非表示切替
    $scope.toggleChatTextareaView = function(value) {
      var chatTalkHeight = 194;
      var messageBoxHeight = 75;

      switch($scope.showWidgetType) {
      case 1: // 表示タブ：通常
        // ウィジェットサイズごとにサイズを変更する
        if($scope.widgetSettings.widget_size_type == 2) {
          chatTalkHeight = 274;
        } else if($scope.widgetSettings.widget_size_type == 3) {
          chatTalkHeight = 364;
        }
        // プレミアムプラン以外の場合、高さを調整する
        <?php if ( !$coreSettings[C_COMPANY_USE_SYNCLO] && (!isset($coreSettings[C_COMPANY_USE_DOCUMENT]) || !$coreSettings[C_COMPANY_USE_DOCUMENT]) ): ?>
          messageBoxHeight -= 3;
        <?php endif; ?>
        break;
      case 2:  // 表示タブ：スマートフォン(横)
        chatTalkHeight = 90;
        messageBoxHeight = 62;
        break;
      case 3:  // 表示タブ：スマートフォン(縦)
        chatTalkHeight = 184;
        messageBoxHeight = 72;
        break;
      }

      if (value == <?= C_AUTO_WIDGET_TEXTAREA_OPEN ?>) {
        document.getElementById('messageBox').style.display = "block";
        document.getElementById('chatTalk').style.height = chatTalkHeight + "px";
      } else {
        document.getElementById('messageBox').style.display = "none";
        document.getElementById('chatTalk').style.height = chatTalkHeight + messageBoxHeight + "px";
      }
    }
});

// http://stackoverflow.com/questions/17035621/what-is-the-angular-way-of-displaying-a-tooltip-lightbox
sincloApp.directive('ngShowonhover',function() {
    return {
        controller: 'MainController',
        controllerAs: 'main',
        link : function(scope, element, attrs) {
            var balloon = $("div.balloon");
            var itemsTag = element.closest("li");
            element.parent().bind('mouseenter', function(e) {
                if ( scope.$parent === null || !('itemForm' in scope.$parent) ) { return false; }
                if (Object.keys(scope.$parent.itemForm.$error).length === 0) { return false; }
                createBalloon(attrs['ngShowonhover'], scope.$parent.itemForm);
                var top = itemsTag.prop('offsetTop');
                var left = itemsTag.prop('offsetLeft');
                var width = itemsTag.prop('offsetWidth');
                balloon.css({
                    "top": top + 10,
                    "left": width + left
                }).show();
            });
            element.parent().bind('mouseleave', function() {
                balloon.hide();
            });

            var createBalloon = function(key, form){
                var messageList = [];
                $("div.balloonContent").children().remove();

                /* 滞在時間 */
                if ( 'stayTimeRange' in form ) {
                    if ( 'required' in form.stayTimeRange.$error ) {
                        messageList.push("時間が未入力です");
                    }
                    if ( 'number' in form.stayTimeRange.$error ) {
                        messageList.push("時間は数値で入力してください");
                    }
                    if ('pattern' in form.stayTimeRange.$error) {
                        messageList.push("時間は0～100までの半角数字で指定できます");
                    }
                }
                /* 訪問回数 */
                if ( 'visitCnt' in form ) {
                    if ('required' in form.visitCnt.$error) {
                        messageList.push("訪問回数が未入力です");
                    }
                    if ( 'number' in form.visitCnt.$error ) {
                        messageList.push("訪問回数は数値で入力してください");
                    }
                    if ('pattern' in form.visitCnt.$error) {
                        messageList.push("訪問回数は1～100回までの半角数字で指定できます");
                    }
                    if ('isVisitCntRule' in form.visitCnt.$error) {
                        messageList.push("訪問回数は「1回未満」という設定はできません");
                    }
                }
                /* 検索キーワード */
                if ( 'keyword' in form ) {
                    if (String(key) === '<?=h(C_AUTO_TRIGGER_SEARCH_KEY)?>' && 'required' in form.keyword.$error) {
                        messageList.push("キーワードが未入力です");
                    }
                }
                /* ページ・リファラー・発言内容・最初に訪れたページ・前のページ */
                if ( 'keyword_contains' in form || 'keyword_exclusions' in form ) {
                  if(_isContainsExclusionsErrorFound(form)) {
                    switch(String(key)) {
                      case '<?=h(C_AUTO_TRIGGER_REFERRER)?>':
                        messageList.push("URLはいずれかの指定が必要です。");
                        break;
                      case '<?=h(C_AUTO_TRIGGER_SPEECH_CONTENT)?>':
                        messageList.push("発言内容はいずれかの指定が必要です。");
                        break;
                      default:
                        messageList.push("キーワードはいずれかの指定が必要です。");
                        break;
                    }
                  }
                }
                /* 曜日・日時 */
                if ( 'day' in form ) {
                    if ('required' in form.day.$error) {
                        messageList.push("曜日が未選択です");
                    }
                }
                if ( 'startTime' in form ) {
                    if ('required' in form.startTime.$error) {
                        messageList.push("開始時間が未入力です");
                    }
                    if ('pattern' in form.startTime.$error) {
                        messageList.push("開始時間は「00:00」の形で入力してください");
                    }
                }
                if ( 'endTime' in form ) {
                    if ('required' in form.endTime.$error) {
                        messageList.push("終了時間が未入力です");
                    }
                    if ('pattern' in form.endTime.$error) {
                        messageList.push("終了時間は「00:00」の形で入力してください");
                    }
                }

                /* 発言内容 */
                if ( 'speechContent' in form ) {
                    if ('required' in form.speechContent.$error) {
                      messageList.push("発言内容が未入力です");
                    }
                }

                /* 自動返信までの間隔 */
                if ( 'triggerTimeSec' in form ) {
                    if ('required' in form.triggerTimeSec.$error) {
                        messageList.push("自動返信までの間隔が未指定です。");
                    }
                    if ('pattern' in form.triggerTimeSec.$error) {
                      messageList.push("時間は1～60までの半角数字で指定できます");
                    }
                }

                /* 営業時間 */
                if( 'notOperatingHour' in form) {
                    messageList.push("営業時間設定を利用していません");
                }

                for( var i = 0; i <  messageList.length; i++ ){
                    var element = document.createElement("p");
                    element.textContent = "● " + messageList[i];
                    $("div.balloonContent").append(element);
                }
            };

            var _isContainsExclusionsErrorFound = function(form) {
              return (!'keyword_contains' in form && !('keyword_exclusions' in form))
                || ('keyword_contains' in form && 'required' in form.keyword_contains.$error && !('keyword_exclusions' in form))
                || ('keyword_exclusions' in form && 'required' in form.keyword_exclusions.$error && !('keyword_contains' in form))
                || (('keyword_contains' in form && 'required' in form.keyword_contains.$error)
                  && ('keyword_exclusions' in form && 'required' in form.keyword_exclusions.$error));
            };
        }
    };
});

function escape_html(unescapedString) {
  if(typeof unescapedString !== 'string') {
    return unescapedString;
  }
  var string = unescapedString.replace(/(<br>|<br \/>)/gi, '\n');
  string = string.replace(/[&'`"<>]/g, function(match) {
    return {
      '&': '&amp;',
      "'": '&#x27;',
      '`': '&#x60;',
      '"': '&quot;',
      '<': '&lt;',
      '>': '&gt;',
    }[match];
  });
  return string;
}

function removeAct(lastPage){
    modalOpen.call(window, "削除します、よろしいですか？", 'p-confirm', 'オートメッセージ設定', 'moment');
    popupEvent.closePopup = function(){
        $.ajax({
            type: 'post',
            data: {
                id: document.getElementById('TAutoMessageId').value
            },
            cache: false,
            url: "<?= $this->Html->url('/TAutoMessages/remoteDelete') ?>",
            success: function(){
                var url = "<?= $this->Html->url('/TAutoMessages/index') ?>";
                location.href = url + "/page:" + lastPage;
            }
        });
    };
}

function submitAct(){
  $('#TAutoMessageEntryForm').submit();
}

//スクロール位置把握
var topPosition = 0;
window.onload = function() {
  document.querySelector('#content').onscroll = function() {
    topPosition = this.scrollTop;
  };
};

$(document).ready(function(){
  // ツールチップの表示制御
  $('.questionBtn').off("mouseenter").on('mouseenter',function(event){
    var parentTdId = $(this).parent().parent().attr('id');
    console.log(parentTdId);
    var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
    console.log(targetObj);
    targetObj.find('icon-annotation').css('display','block');
    targetObj.css({
      top: ($(this).offset().top - targetObj.find('ul').outerHeight() - 170 + topPosition) + 'px',
      left: $(this).offset().left - 101 + 'px'
    });
  });

  $('.questionBtn').off("mouseleave").on('mouseleave',function(event){
    var parentTdId = $(this).parent().parent().attr('id');
    var targetObj = $("#" + parentTdId.replace(/Label/, "Tooltip"));
    targetObj.find('icon-annotation').css('display','none');
  });

  // これまでのチャット内容をメールで送信する
  var initializeFromMailAddressArea = function() {
    var checked = $('#mainSendMailFlg').prop('checked');
    if(checked) {
      $('.sendMailSettings').css('display', '');
      $('#sendMailSettingCheckBox').css("padding","15px 0 0 0");
    } else {
      $('.sendMailSettings').css('display', 'none');
      $('#sendMailSettingCheckBox').css("padding","15px 0 15px 0");
    }
    var atFirst = true;
    var prevObj = undefined;
    $('.mailAddressBlock').each(function(index){
      var mailAddress = $(this).find('input[type="text"]').val();
      if(mailAddress !== "") {
        $(this).css('display', 'inline-flex').addClass('show');
        $(this).find('.disOffgreenBtn').css('display', 'none');
        $(this).find('.deleteBtn').css('display', 'block');
        if(index !== 0 && index < 4) {
          prevObj.find('.disOffgreenBtn').css('display', 'none');
          $(this).find('.disOffgreenBtn').css('display', 'block');
        } else if(prevObj) {
          prevObj.find('.disOffgreenBtn').css('display', 'none');
        }
      } else {
        if(index === 0) {
          $(this).css('display', 'inline-flex').addClass('show');
          $(this).find('.disOffgreenBtn').css('display', 'block');
          $(this).find('.deleteBtn').css('display', 'none');
        } else if(index === 1) {
          $(this).css('display', 'none').removeClass('show');
          prevObj.find('.disOffgreenBtn').css('display', 'block');
          prevObj.find('.deleteBtn').css('display', 'none');
        } else {
          $(this).css('display', 'none').removeClass('show');
        }
      }
      prevObj = $(this);
    });

  };

  $('#mainSendMailFlg').on('change', function(event){
    initializeFromMailAddressArea();
  });

  $('.disOffgreenBtn').on('click', function(ev){
    $(this).parents('.mailAddressBlock').next('span').css('display', 'inline-flex').addClass('show').find('input[type="text"]').val('');
    $(this).css('display','none').parents('.btnBlock').find('.redBtn').css('display', 'block');
    if($('#fromMailAddressSettings').find('.show').length === 5) {
      $(this).parents('.mailAddressBlock').next('span').find('.disOffgreenBtn').css('display', 'none');
    }
  });

  $('.deleteBtn').on('click', function(ev){
    $('#mailAddressSetting').find('span.show').last().css('display','none').removeClass('show');
    var targetObj = $(this).parents('.mailAddressBlock').find('input[type="text"]');
    targetObj.val('');
    var nextAllObj = $(this).parents('.mailAddressBlock').nextAll('span');
    nextAllObj.each(function(idx){
      targetObj.val($(this).find('input[type="text"]').val());
      targetObj = $(this).find('input[type="text"]');
      if(nextAllObj.length-1 === idx) {
        targetObj.val('');
      }
    });

    $('#mailAddressSetting').find('span.show').last().find('.disOffgreenBtn').css('display', 'block');
    if($('#fromMailAddressSettings').find('.show').length === 1) {
      $('#mailAddressSetting').find('span.show').first().find('.disOffgreenBtn').css('display', 'block');
      $('#mailAddressSetting').find('span.show').first().find('.redBtn').css('display', 'none');
    }
  });
  initializeFromMailAddressArea();
});

/* [ #2243 ] IE緊急対応 */
// TODO 仮対応のため正式な対応をする
var sincloChatMessagefocusFlg = true;
$("body").on('focus', '#sincloChatMessage', function(e){
  if ( sincloChatMessagefocusFlg ) {
    e.target.value = "";
    sincloChatMessagefocusFlg = false;
  }
});
/* [ #2243 ] IE緊急対応 */

</script>
