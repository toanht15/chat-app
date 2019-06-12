<script type="text/javascript">
  'use strict';

  sincloApp.factory('SimulatorService', function() {

    var self = this;

    this.escape_html = function() {
      if (typeof unescapedString !== 'string') {
        return unescapedString;
      }
      var string = unescapedString.replace(/(<br>|<br \/>)/gi, '\n');
      string = string.replace(/[&'`"<>]/g, function(match) {
        return {
          '&': '&amp;',
          '\'': '&#x27;',
          '`': '&#x60;',
          '"': '&quot;',
          '<': '&lt;',
          '>': '&gt;',
        }[match];
      });
      return string;
    };

    return {
      _settings: {},
      _coreSettingsChat: {},
      _showWidgetType: 1,
      _openFlg: true,
      _showTab: 'chat',
      _isTextAreaOpen: true,
      _currentActionStep: 0,
      _currentHearingIndex: 0,
      set settings(obj) {
        this._settings = obj;
      },
      get settings() {
        return this._settings;
      },
      set coreSettingsChat(settings) {
        this._coreSettingsChat = settings;
      },
      get coreSettingsChat() {
        return this._coreSettingsChat;
      },
      set showWidgetType(type) {
        this._showWidgetType = type;
      },
      get showWidgetType() {
        return this._showWidgetType;
      },
      set openFlg(status) {
        this._openFlg = status;
      },
      get openFlg() {
        return this._openFlg;
      },
      // パラメータ取得(実際のパラメータと、取得時の名称が異なるもの)
      get widgetSizeTypeToggle() {
        return this._settings['widget_size_type'];
      },
      get subTitleToggle() {
        return this._settings['show_subtitle'];
      },
      get descriptionToggle() {
        return this._settings['show_description'];
      },
      get mainImageToggle() {
        return this._settings['show_main_image'];
      },
      get chatbotIconToggle() {
        return this._settings['show_chatbot_icon'];
      },
      get chatbotIconPath() {
        return this._settings['chatbot_icon'];
      },
      get isBotIconImg() {
        return this._settings['chatbot_icon'].match(/^fa/) === null;
      },
      get isBotIconIcon() {
        return this._settings['chatbot_icon'].match(/^fa/) !== null;
      },
      get operatorIconToggle() {
        return this._settings['show_operator_icon'];
      },
      get operatorIconPath() {
        return this._settings['operator_icon'];
      },
      get minimizedDesignToggle() {
        return this._settings['minimize_design_type'];
      },
      get chatMessageArrowPosition() {
        return this._settings['chat_message_arrow_position'];
      },
      get closeButtonSettingToggle() {
        return this._settings['close_button_setting'];
      },
      get closeButtonModeTypeToggle() {
        return this._settings['close_button_mode_type'];
      },
      get timeTextToggle() {
        return this._settings['display_time_flg'];
      },
      get chat_area_placeholder_pc() {
        return Number(this._settings['chat_trigger']) === 1 ? '（Shift+Enterで改行/Enterで送信）' : '';
      },
      get chat_area_placeholder_sp() {
        return Number(this._settings['chat_trigger']) === 1 ? '（改行で送信）' : '';
      },
      /**
       * ウィジェットサイズ
       */
      get isMiddleSize() {
        return this._showWidgetType === 1 && this._settings['widget_size_type'] === '2';
      },
      get isLargeSize() {
        return this._showWidgetType === 1 &&
            (this._settings['widget_size_type'] === '3' || this._settings['widget_size_type'] === '4');
      },
      get isCustomSize() {
        return this._showWidgetType === 1 && this._settings['widget_size_type'] === '5';
      },
      get isSpPortrait() {
        return this._showWidgetType === 3;
      },
      get isSpLandscape() {
        return this._showWidgetType === 2;
      },
      get isMaximumSize() {
        return this._showWidgetType === 1 && this._settings['widget_size_type'] === '4';
      },
      // パラメータ取得(設定の有無)
      get widget_outside_border_none() {
        return this._settings['widget_border_color'] === 'なし';
      },
      get widget_inside_border_none() {
        return this._settings['widget_inside_border_color'] === 'なし';
      },
      get re_border_none() {
        return this._settings['re_border_color'] === 'なし';
      },
      get re_text_size() {
        return Number(this._settings['re_text_size']);
      },
      get re_text_color() {
        return this._settings['re_text_color'];
      },
      get radioButtonBeforeTop() {
        return Math.ceil((Number(this.re_text_size) / 2));
      },
      get radioButtonAfterTop() {
        return Math.ceil((Number(this.re_text_size) / 2));
      },
      get radioButtonAfterLeft() {
        return (this.re_text_size / 2 - ((this.re_text_size - 6) / 2)) + 1;
      },
      get radioButtonAfterMarginTop() {
        return Math.round(this.re_text_size / 2) - 4;
      },
      get se_border_none() {
        return this._settings['se_border_color'] === 'なし';
      },
      get message_box_border_none() {
        return this._settings['message_box_border_color'] === 'なし';
      },
      // 表示タブ
      set showTab(val) {
        this._showTab = val;
      },
      get showTab() {
        return this._showTab;
      },
      setCurrentActionStep: function(val) {
        this._currentActionStep = val;
      },
      setCurrentHearingIndex: function(val) {
        this._currentHearingIndex = val;
      },
      getCurrentActionStep: function() {
        return this._currentActionStep;
      },
      getCurrentHearingIndex: function() {
        return this._currentHearingIndex;
      },
      // 関数
      getSeBackgroundColor: function() {
        var defColor = '#FFFFFF';
        //仕様変更、常に高度な設定が当たっている状態とする
        defColor = this._settings.se_background_color;
        return defColor;
      },
      getTalkBorderColor: function(chk) {
        var defColor = '#E8E7E0';
        //仕様変更、常に高度な設定が当たっている状態とする
        if (chk === 're') {
          defColor = this._settings.re_border_color;
        } else {
          defColor = this._settings.se_border_color;
        }
        return defColor;
      },
      /**
       * 吹き出しの背景色設定
       * @param Integer opacity 透明度(省略可)
       * @return String         RGBAカラーコード
       */
      makeFaintColor: function(opacity) {
        opacity = opacity || 1;
        var colorCode = this._settings.re_background_color || '#F1F5C8';

        var red = parseInt(colorCode.substring(1, 3), 16);
        var green = parseInt(colorCode.substring(3, 5), 16);
        var blue = parseInt(colorCode.substring(5, 7), 16);
        return 'rgba(' + red + ', ' + green + ', ' + blue + ', ' + opacity + ')';
      },
      //シンプル表示判定
      spHeaderLightToggle: function() {
        switch (this.minimizedDesignToggle) {
          case '1': //シンプル表示しない
            if (this._showWidgetType === 1) {
              //通常（PC）
              if (this.settings['sp_header_light_flg'] === '<?=C_SELECT_CAN?>') {
                //最大時のシンプル表示(スマホ)する
                if (!this._openFlg) {
                  //最小化中
                  var res = false;
                } else {
                  //最大化中
                  var res = false;
                }
              } else {
                //最大時のシンプル表示(スマホ)しない
                if (!this._openFlg) {
                  //最小化中
                  var res = false;
                } else {
                  //最大化中
                  var res = false;
                }
              }
            } else {
              //スマホ（縦）
              if (this.settings['sp_header_light_flg'] === '<?=C_SELECT_CAN?>') {
                //最大時のシンプル表示(スマホ)する
                if (!this._openFlg) {
                  //最小化中
                  var res = false;
                } else {
                  //最大化中
                  var res = true;
                }
              } else {
                //最大時のシンプル表示(スマホ)しない
                if (!this._openFlg) {
                  //最小化中
                  var res = false;
                } else {
                  //最大化中
                  var res = false;
                }
              }
            }
            break;
          case '2': //スマホのみシンプル表示する
            if (this._showWidgetType === 1) {
              //通常（PC）
              if (this.settings['sp_header_light_flg'] === '<?=C_SELECT_CAN?>') {
                //最大時のシンプル表示(スマホ)する
                if (!this._openFlg) {
                  //最小化中
                  var res = false;
                } else {
                  //最大化中
                  var res = false;
                }
              } else {
                //最大時のシンプル表示(スマホ)しない
                if (!this._openFlg) {
                  //最小化中
                  var res = false;
                } else {
                  //最大化中
                  var res = false;
                }
              }
            } else {
              //スマホ（縦）
              if (this.settings['sp_header_light_flg'] === '<?=C_SELECT_CAN?>') {
                //最大時のシンプル表示(スマホ)する
                if (!this._openFlg) {
                  //最小化中
                  var res = true;
                } else {
                  //最大化中
                  var res = true;
                }
              } else {
                //最大時のシンプル表示(スマホ)しない
                if (!this._openFlg) {
                  //最小化中
                  var res = true;
                } else {
                  //最大化中
                  var res = false;
                }
              }
            }
            break;
          case '3': //すべての端末でシンプル表示する
            if (this._showWidgetType === 1) {
              //通常（PC）
              if (this.settings['sp_header_light_flg'] === '<?=C_SELECT_CAN?>') {
                //最大時のシンプル表示(スマホ)する
                if (!this._openFlg) {
                  //最小化中
                  var res = true;
                } else {
                  //最大化中
                  var res = false;
                }
              } else {
                //最大時のシンプル表示(スマホ)しない
                if (!this._openFlg) {
                  //最小化中
                  var res = true;
                } else {
                  //最大化中
                  var res = false;
                }
              }
            } else {
              //スマホ（縦）
              if (this.settings['sp_header_light_flg'] === '<?=C_SELECT_CAN?>') {
                //最大時のシンプル表示(スマホ)する
                if (!this._openFlg) {
                  //最小化中
                  var res = true;
                } else {
                  //最大化中
                  var res = true;
                }
              } else {
                //最大時のシンプル表示(スマホ)しない
                if (!this._openFlg) {
                  //最小化中
                  var res = true;
                } else {
                  //最大化中
                  var res = false;
                }
              }
            }
            break;
        }
        if (this._openFlg) {
          //最大化時
          $('#minimizeBtn').show();
          $('#addBtn').hide();
          $('#closeBtn').hide();

          $("#fw-minimize-btn").show();
          $("#fw-close-btn").hide();
        } else {
          //最小化時
          $('#addBtn').show();
          $('#minimizeBtn').hide();
          $("#fw-minimize-btn").hide();
          if (this.closeButtonSettingToggle === '2') {
            $('#closeBtn').show();
            $("#fw-close-btn").show();
          } else {
            $('#closeBtn').hide();
            $("#fw-close-btn").hide();
          }
          if (this._coreSettingsChat) {
            document.getElementById('switch_widget').value = this._showWidgetType;
          }
        }
        return res;
      },
      isIconImage: function() {
        return this.settings['main_image'].match(/^fa/) !== null;
      },
      isPictureImage: function() {
        return this.settings['main_image'].match(/^http|data|\/\/node/) !== null;
      },
      //param : String型 ng-classで付けたい情報を渡す
      resultClass: {},
      viewWidgetSetting: function(param) {
        //初期化
        this.resultClass = {};
        var classArray = param.split(',');
        for (var i = 0; i < classArray.length; i++) {
          switch (classArray[i]) {
            case 'size':
              this.getWidgetSizeClassName();
              break;
            case 'sp':
            case 'spText':
              this.setSmartPhoneClassName(classArray[i]);
              break;
            case 'titlePosition':
              this.setTitlePositionSetting();
              break;
            case 'image':
              this.setMainImageClassName();
              break;
            case 'notNone':
              this.setNotNoneClassName();
              break;
            case 'notNoneWidgetOutsideBorder':
              this.notNoneWidgetOutsideBorderChecker();
              break;
            case 'headerContents' :
              this.setHeaderContentsCount();
              break;
            case 'headerName' :
              this.setHeaderClassName();
              break;
            case 'headerDescription' :
              this.setDescriptionClassName();
              break;

          }
        }
        return this.resultClass;
      },

      /*ng-class用のオブジェクトを設定する関数群--開始--*/


      getWidgetSizeClassName: function() {
        if (this.showWidgetType === 3) {
          //スマホ縦の場合
          this.resultClass['spSize'] = true;
          return;
        }
        switch (Number(this.widgetSizeTypeToggle)) {
          case 1:
            this.resultClass['smallSize'] = true;
            break;
          case 2:
            this.resultClass['middleSize'] = true;
            break;
          case 3:
          case 4:
            this.resultClass['largeSize'] = true;
            break;
          case 5:
            this.resultClass['customSize'] = true;
            break;
          default:
            break;
        }
      },

      setSmartPhoneClassName: function(param) {
        this.resultClass[param] = this.isSmartPhonePortrait();
      },

      setTitlePositionSetting: function() {
        switch (Number(this._settings.widget_title_top_type)) {
          case 1:
            this.resultClass['leftPositionTitle'] = true;
            break;
          case 2:
            this.resultClass['centerPositionTitle'] = true;
            break;
          default:
            break;
        }
      },

      setHeaderClassName: function() {
        if (Number(this.subTitleToggle) === 2) {
          this.resultClass['noCompany'] = true;
        } else {
          this.setHeaderPosition(Number(this._settings.widget_title_name_type));
        }
      },

      setDescriptionClassName: function() {
        if (Number(this.descriptionToggle) === 2) {
          this.resultClass['noExplain'] = true;
        } else {
          this.setHeaderPosition(Number(this._settings.widget_title_explain_type));
        }
      },

      setHeaderPosition: function(param) {
        switch (param) {
          case 1:
            this.resultClass['leftPosition'] = true;
            break;
          case 2:
            this.resultClass['centerPosition'] = true;
            break;
        }
      },

      setMainImageClassName: function() {
        switch (Number(this.mainImageToggle)) {
          case 1:
            this.resultClass['Image'] = true;
            break;
          case 2:
            this.resultClass['NoImage'] = true;
            break;
        }
      },

      notNoneWidgetOutsideBorderChecker: function() {
        this.resultClass['notNoneWidgetOutsideBorder'] = this.widget_outside_border_none === '' ||
            !this.widget_outside_border_none;
      },

      setNotNoneClassName: function() {
        this.resultClass['notNone'] = this.widget_inside_border_none === '' || !this.widget_inside_border_none;
      },

      setHeaderContentsCount: function() {
        //コンテンツ数が多いほどNumは小さい
        var contentsNum = Number(this.descriptionToggle) + Number(this.subTitleToggle);
        switch (contentsNum) {
          case 2:
            this.resultClass['twoContents'] = true;
            break;
          case 3:
            this.resultClass['oneContents'] = true;
            break;
          case 4:
            this.resultClass['noContents'] = true;
            break;
        }
      },

      /*ng-class用のオブジェクトを設定する関数群--終了--*/

      isSmartPhonePortrait: function() {
        return Number(this.showWidgetType) === 3;
      },

      /**
       * 表示用HTMLへの変換
       * @param String val    変換したいメッセージ
       * @param String prefix ラジオボタンに付与するプレフィックス
       * @param String align 文字寄せを指定したい場合に使う。'2': 中央寄せ、'3': 右寄せ
       * @param boolean nospan 出力するHTMLにspanのラップを付けるかどうか
       * @return String       変換したメッセージ
       */
      createMessage: function(val, prefix, align, nospan) {
        if (val === '') return '';
        prefix = (typeof prefix !== 'undefined' && prefix !== '') ? prefix + '-' : '';
        var isSmartphone = Number(this._showWidgetType) !== 1;
        var messageIndex = $('#chatTalk > div:not([style*="display: none;"])').length;
        var strings = val.split('\n');
        var radioCnt = 1;
        var htmlTagReg = RegExp(/<\/?("[^"]*"|'[^']*'|[^'">])*>/g);
        var radioName = prefix + 'sinclo-radio' + messageIndex;
        var content = '';
        var isAddUnderline = prefix.indexOf('underline') !== -1 ? true : false;
        var alignStyle = 'text-align: left;';
        switch(align) {
          case '2':
            alignStyle = 'text-align: center;';
            break;
          case '3':
            alignStyle = 'text-align: right;';
            break;
          default:
            break;
        }

        var isFreeBlock = false;
        var hasFreeBlock = false;
        for (var i = 0; strings.length > i; i++) {
          if(strings[i].match(/(<div class="free-block")/)) {
            content += strings[i];
            isFreeBlock = true;
            hasFreeBlock = true;
            continue;
          } else if(strings[i].match(/(<\/div>)/)) {
            isFreeBlock = false;
            content += strings[i];
            continue;
          } else if(isFreeBlock) {
            content += strings[i];
            continue;
          }
          var str = escape_html(strings[i]);
          // ラジオボタン
          var radio = str.indexOf('[]');
          if (radio > -1) {
            var value = str.slice(radio + 2).trim();
            var name = value.replace(htmlTagReg, '');
            str = '<span class=\'sinclo-radio\'><input type=\'radio\' name=\'' + radioName + '\' id=\'' + radioName + '-' + i +
                '\' class=\'sinclo-chat-radio\' value=\'' + name + '\'>';
            str += '<label for=\'' + radioName + '-' + i + '\'>' + value + '</label></span>';
          }
          //リンク、電話番号、imgタグ
          str = replaceVariable(str, isSmartphone, this._settings['widget_size_type'] + 'sim');

          if (str.match(/<(".*?"|'.*?'|[^'"])*?>/)) {
            content += '' + str + '\n';
          } else {
            if (isAddUnderline) {
              content += '<span class=\'sinclo-text-line underlineText\' style=\'' + alignStyle + '\'>' + str + '</span>\n';
            } else {
              content += '<span class=\'sinclo-text-line\' style=\'' + alignStyle + '\'>' + str + '</span>\n';
            }
          }
        }

        return content;
      },
      createCheckboxMessage: function(message, separator) {
        var checkboxData = JSON.parse(message);
        var array        = checkboxData.message.split(checkboxData.separator);
        var html = '<ul style="margin-top: -7px; display: block;">';
        angular.forEach(array, function(item) {
          html += '<li style="list-style-type: disc; background-color: transparent; margin: 5px 0 0 15px; padding: 0; display: list-item;">' + item + '</li>';
        });
        html += '</ul>';

        return html;
      },
      createForm: function(isConfirm, hearingTarget, resultData) {
        var self = this;
        var formElements = '';
        var isEmptyRequire = false;

        var content = '';
        if (isConfirm) {
          hearingTarget.forEach(function(elm, idx, arr) {
            if (elm.required && resultData[Number(elm.inputType)].length === 0) {
              isEmptyRequire = true;
            }
            formElements += (arr.length - 1 === idx) ?
                '    <div class=\'formElement\'>' :
                '    <div class=\'formElement withMB\'>';
            formElements += '      <label class=\'formLabel\'>' + elm.label +
                (elm.required ? '<span class=\'require\'></span>' : '') + '</label>';
            formElements += '      <input type=\'' + self.getInputType(elm.inputType) +
                '\' class=\'formInput\' placeholder=\'' + elm.label + 'を入力してください\' data-required=\'' + elm.required +
                '\' data-input-type=\'' + elm.inputType + '\' data-label-text=\'' + elm.label + '\' name=\'' +
                elm.variableName + '\' value=\'' + resultData[Number(elm.inputType)] + '\'/>';
            formElements += '    </div>';
          });

          //content +=  (Number(window.sincloInfo.widget.showAutomessageName) !== 2) ? "<span class='cName'>" + sincloInfo.widget.subTitle + "</span>" : "";
          content += '<div class=\'formContentArea\'>';
          content += '  <p class=\'formMessage\'>' +
              ((isEmptyRequire) ? '必須項目の入力が認識できませんでした。\n*印の項目を入力してください。' : 'こちらの内容でよろしいでしょうか？') + '</p>';
          content += '  <div class=\'formArea\'>';
          content += formElements;
          content += '    <p class=\'formOKButtonArea\'><span class=\'formOKButton\'>OK</span></p>';
          content += '  </div>';
          content += '</div>';
        } else {
          hearingTarget.forEach(function(elm, idx, arr) {
            if (elm.required && resultData[elm.variableName].value.length === 0) {
              isEmptyRequire = true;
            }
            formElements += (arr.length - 1 === idx) ?
                '    <div class=\'formElement\'>' :
                '    <div class=\'formElement withMB\'>';
            formElements += '      <label class=\'formLabel\'>' + elm.label +
                (elm.required ? '<span class=\'require\'></span>' : '') + '</label>';
            formElements += '      <input type=\'' + self.getInputType(elm.inputType) +
                '\' class=\'formInput\' placeholder=\'' + elm.label + 'を入力してください\' data-required=\'' + elm.required +
                '\' data-label-text=\'' + elm.label + '\' name=\'' + elm.variableName + '\' value=\'' +
                resultData[elm.variableName].value + '\' readonly/>';
            formElements += '    </div>';
          });

          //content += (Number(window.sincloInfo.widget.showAutomessageName) !== 2) ? "<span class='cName'>" + sincloInfo.widget.subTitle + "</span>" : "";
          content += '<div class=\'formContentArea\'>';
          content += '  <div class=\'formArea\'>';
          content += formElements;
          content += '    <p class=\'formOKButtonArea\'><span class=\'formOKButton disabled\'>OK</span></p>';
          content += '  </div>';
          content += '</div>';
        }

        return {
          content: content,
          isEmptyRequire: isEmptyRequire,
        };
      },

      createFormFromLog: function(data) {
        var formElements = '';
        var content = '';
        var objKeys = Object.keys(data);
        objKeys.forEach(function(variableName, index, array) {
          formElements += (array.length - 1 === index) ?
              '    <div class=\'formElement\'>' :
              '    <div class=\'formElement withMB\'>';
          formElements += '      <span class=\'formLabel\'>' + data[variableName].label +
              (data[variableName].required ? '<span class=\'require\'></span>' : '') + '</span>';
          formElements += '      <span class=\'formLabelSeparator\'>：</span>';
          formElements += '      <span class=\'formValue\'>' +
              (data[variableName].value ? data[variableName].value : '') + '</span>';
          formElements += '    </div>';
        });

        content += '<div class=\'formContentArea\'>';
        content += '  <div class=\'formSubmitArea\'>';
        content += formElements;
        content += '  </div>';
        content += '</div>';

        return content;
      },

      getInputType: function(bulkHearingInputType) {
        var type = '';
        switch (Number(bulkHearingInputType)) {
          case 1:
            type = 'text';
            break;
          case 2:
            type = 'text';
            break;
          case 3:
            type = 'tel';
            break;
          case 4:
            type = 'text';
            break;
          case 5:
            type = 'text';
            break;
          case 6:
            type = 'text';
            break;
          case 7:
            type = 'tel';
            break;
          case 8:
            type = 'tel';
            break;
          case 9:
            type = 'tel';
            break;
          case 10:
            type = 'email';
            break;
          case 11:
            type = 'text';
            break;
          default:
            type = 'text';
            break;
        }
        return type;
      },

      createRadioButton: function(data) {
        var messageHtml = this.createMessage(data.message, data.prefix);
        var prefix = (typeof data.prefix !== 'undefined' && data.prefix !== '') ? data.prefix + '-' : '';
        var index = $('#chatTalk > div:not([style*="display: none;"])').length;
        var radioName = prefix + 'sinclo-radio-' + index;
        var hasOldOptionValue = false;
        // style
        var html = '<div id="' + radioName + '">';
        var style = '<style>';
        style += '#sincloBox #' + radioName + ' span.sinclo-radio [type="radio"] + label:before {background-color: ' +
            data.settings.customDesign.radioBackgroundColor + ' !important;}';
        style += '#sincloBox #' + radioName + ' span.sinclo-radio [type="radio"]:checked + label:after {background: ' +
            data.settings.customDesign.radioActiveColor + ' !important;}';
        style += '#sincloBox #' + radioName + ' span.sinclo-radio:first-of-type {margin-top: 4px !important}';
        style += '#sincloBox #' + radioName + ' span.sinclo-radio {margin-top: ' +
            data.settings.customDesign.radioSelectionDistance + 'px !important;}';
        if (data.settings.radioNoneBorder) {
          style += '#sincloBox #' + radioName +
              ' span.sinclo-radio [type="radio"] + label:before {border-color: transparent !important;}';
        } else {
          style += '#sincloBox #' + radioName + ' span.sinclo-radio [type="radio"] + label:before {border-color: ' +
              data.settings.customDesign.radioBorderColor + '!important;}';
        }

        if (data.settings.radioStyle !== '1') {
          style += '#sincloBox #' + radioName + ' span.sinclo-radio [type="radio"] + label {background-color: transparent;}';
        } else {
          style += '#sincloBox #' + radioName + ' span.sinclo-radio {padding: 8px; color: ' + data.settings.customDesign.radioTextColor + ';}';
        }
        style += '</style>';
        html += style;
        angular.forEach(data.options, function(option, key) {
          if (!option || option == '') return false;
          if (data.isRestore && option === data.oldValue) {
            html += '<span style="display: block;" class=\'sinclo-radio\'><input type=\'radio\' checked=\'checked\' name=\'' + radioName + '\' id=\'' +
                radioName + '-' + key + '\' class=\'sinclo-chat-radio\' value=\'' + option + '\'>';
            html += '<label for=\'' + radioName + '-' + key + '\'>' + option + '</label></span>';
            hasOldOptionValue = true;
          } else {
            html += '<span style="display: block" class=\'sinclo-radio\'><input type=\'radio\' name=\'' + radioName + '\' id=\'' + radioName + '-' +
                key + '\' class=\'sinclo-chat-radio\' value=\'' + option + '\'>';
            html += '<label for=\'' + radioName + '-' + key + '\'>' + option + '</label></span>';
          }
        });
        html += '</select>';
        html += '</div>';
        if (data.isRestore && hasOldOptionValue) {
          html += '<div><a class="nextBtn" style="color: ' + data.textColor + '; background-color: ' +
              data.backgroundColor + ';" id="' + data.prefix + '_next"">次へ</a></div>';
        }

        return {
          html: messageHtml + html,
          radioName: radioName
        };
      },

      _needResizeCauseIcon: function() {
        return Number(this.settings.widget_size_type) === 1
            && Number(this.settings.show_chatbot_icon) === 1;
      },

      createPulldown: function(data) {
        var messageHtml = this.createMessage(data.message, data.prefix);
        var prefix = (typeof data.prefix !== 'undefined' && data.prefix !== '') ? data.prefix + '-' : '';
        var index = $('#chatTalk > div:not([style*="display: none;"])').length;
        var pulldownName = prefix + 'sinclo-pulldown-' + index;
        var hasOldOptionValue = false;
        // style
        var style = 'style="margin-top: 10px; border: 1px solid #909090; height: 30px; width: 100%; word-break: break-all;';
        style += 'background-color: ' + data.design.backgroundColor + ';';
        style += 'color: ' + data.design.textColor + ';';
        style += 'border-color: ' + data.design.borderColor + ';"';

        var html = '<select id="' + pulldownName + '" ' + style + '>';
        html += '<option>選択してください</option>';
        angular.forEach(data.options, function(option, key) {
          if (!option || option == '') return false;
          if (data.isRestore && option === data.oldValue) {
            html += '<option selected="selected" value="' + option + '">' + option + '</option>';
            hasOldOptionValue = true;
          } else {
            html += '<option value="' + option + '">' + option + '</option>';
          }
        });
        html += '</select>';
        if (data.isRestore && hasOldOptionValue) {
          html += '<div><a class="nextBtn" style="color: ' + data.textColor + '; background-color: ' +
              data.backgroundColor + ';" id="' + data.prefix + '_next"">次へ</a></div>';
        }

        return messageHtml + html;
      },

      createButtonUI: function(data) {
        var messageHtml = this.createMessage(data.message, data.prefix);
        var prefix = (typeof data.prefix !== 'undefined' && data.prefix !== '') ? data.prefix + '-' : '';
        var index = $('#chatTalk > div:not([style*="display: none;"])').length;
        var buttonUIName = prefix + 'sinclo-buttonUI-' + index;
        var hasOldOptionValue = false;
        var style = '<style>';
        style += '#sincloBox #' + buttonUIName + ' button {cursor: pointer; min-height: 35px; margin-bottom: 1px; padding: 10px 15px;}';
        style += '#sincloBox #' + buttonUIName + ' button {background-color: ' + data.settings.customDesign.buttonUIBackgroundColor + '}';
        style += '#sincloBox #' + buttonUIName + ' button {width: 100%;}';
        style += '#sincloBox #' + buttonUIName + ' button {color: ' + data.settings.customDesign.buttonUITextColor + '}';
        style += '#sincloBox #' + buttonUIName + ' button:focus {outline: none}';
        style += '#sincloBox #' + buttonUIName + ' button:active {background-color: ' + data.settings.customDesign.buttonUIActiveColor +'}';
        style += '#sincloBox #' + buttonUIName + ' button:first-of-type {border-top-left-radius: 8px; border-top-right-radius: 8px}';
        style += '#sincloBox #' + buttonUIName + ' button:last-child {border-bottom-left-radius: 8px; border-bottom-right-radius: 8px}';
        style += '#sincloBox #' + buttonUIName + ' button.selected {background-color: ' + data.settings.customDesign.buttonUIActiveColor + ' !important;}';
        if (data.message) {
          style += '#sincloBox #' + buttonUIName + ' {margin-top: 8px}';
        }
        if (data.settings.outButtonUINoneBorder) {
          style += '#sincloBox #' + buttonUIName + ' button {border: none}';
        } else {
          style += '#sincloBox #' + buttonUIName + ' button {border: 1px solid ' + data.settings.customDesign.buttonUIBorderColor +' }';
        }
        switch (Number(data.settings.customDesign.buttonUITextAlign)) {
          case 1:
            style += '#sincloBox #' + buttonUIName + ' button {text-align: left}';
            break;
          case 2:
            style += '#sincloBox #' + buttonUIName + ' button {text-align: center}';
            break;
          case 3:
            style += '#sincloBox #' + buttonUIName + ' button {text-align: right}';
            break;
          default:
            style += '#sincloBox #' + buttonUIName + ' button {text-align: center}';
            break;
        }
        style += '</style>';

        var html = '<div id="' + buttonUIName + '">';
        html += style;
        angular.forEach(data.options, function(option, key) {
          if (!option || option === '') return false;
          if (data.isRestore && option === data.oldValue) {
            html += '<button onclick="return false;" class="sinclo-button-ui selected">' + option + '</button>';
            hasOldOptionValue = true;
          } else {
            html += '<button onclick="return false;" class="sinclo-button-ui">' + option + '</button>'
          }
        });
        html += '</div>';
        if (data.isRestore && hasOldOptionValue) {
          html += '<div><a class="nextBtn" style="color: ' + data.textColor + '; background-color: ' +
              data.backgroundColor + ';" id="' + data.prefix + '_next"">次へ</a></div>';
        }

        return messageHtml + html;
      },

      createCheckbox: function(data) {
        var messageHtml = this.createMessage(data.message, data.prefix);
        var prefix = (typeof data.prefix !== 'undefined' && data.prefix !== '') ? data.prefix + '-' : '';
        var index = $('#chatTalk > div:not([style*="display: none;"])').length;
        var checkboxName = prefix + 'sinclo-checkbox-' + index;
        var separator = this.getCheckboxSeparator(data.settings.checkboxSeparator);
        var style = '<style>';
        style += '#sincloBox #' + checkboxName +
            ' .sinclo-checkbox {display: block;position: relative;padding-left: 20px;margin-bottom: ' + data.settings.customDesign.checkboxSelectionDistance + 'px;cursor: pointer;font-size: ' + this.re_text_size + 'px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none; color: ' + this.re_text_color + ';}';
        style += '#sincloBox #' + checkboxName +
            ' .sinclo-checkbox input {position: absolute;opacity: 0;cursor: pointer;height: 0;width: 0;}';
        style += '#sincloBox #' + checkboxName +
            ' .sinclo-checkbox .checkmark {position: absolute;top: 1px;left: 0px;height: ' + (this.re_text_size + 2) + 'px;width: ' + (this.re_text_size + 2) + 'px; background-color: ' +
            data.settings.customDesign.checkboxBackgroundColor + '}';
        style += '#sincloBox #' + checkboxName +
            ' .sinclo-checkbox .checkmark:after {content: "";position: absolute;display: none;left: ' + (this.re_text_size - 9) + 'px;top: ' + (this.re_text_size - 12) + 'px;width: 3px;height: 6px;border: solid ' + data.settings.customDesign.checkboxCheckmarkColor + ';border-width: 0 2px 2px 0;-webkit-transform: rotate(45deg);-ms-transform: rotate(45deg);transform: rotate(45deg);}';
        style += '#sincloBox #' + checkboxName + ' .sinclo-checkbox input:checked ~ .checkmark {background-color: ' +
            data.settings.customDesign.checkboxActiveColor + '}';
        style += '#sincloBox #' + checkboxName + ' .sinclo-checkbox input:checked ~ .checkmark:after {display: block}';
        style += '#sincloBox #' + checkboxName + ' .sinclo-checkbox input:hover ~ .checkmark {background-color: ' +
            data.settings.customDesign.checkboxActiveColor + '}';
        style += '#sincloBox #' + checkboxName +
            ' span.ok-button {width: 100px; height: 30px; line-height: 30px; cursor: pointer; margin: auto; margin-top: 10px; display: block; text-align: center; justify-content: center; align-items: center; border-radius: 12px; background-color: ' + this._settings['chat_send_btn_background_color'] + '; color: ' + this._settings['chat_send_btn_text_color'] + ';}';
        style += '#sincloBox #' + checkboxName + '{display: table;}';
        if (data.settings.checkboxNoneBorder) {
          style += '#sincloBox #' + checkboxName + ' .sinclo-checkbox .checkmark {border: none;}';
        } else {
          style += '#sincloBox #' + checkboxName + ' .sinclo-checkbox .checkmark {border: 1px solid ' +
              data.settings.customDesign.checkboxBorderColor + ';}';
        }

        if (data.settings.checkboxStyle !== '1') {
          style += '#sincloBox #' + checkboxName + ' .sinclo-checkbox {background-color: transparent;}';
        } else {
          style += '#sincloBox #' + checkboxName + ' .sinclo-checkbox {padding: 8px 8px 8px 28px;}';
          style += '#sincloBox #' + checkboxName + ' .sinclo-checkbox .checkmark {top: 9px;left: 8px; }';
          style += '#sincloBox #' + checkboxName + ' .sinclo-checkbox {background-color: ' +
              data.settings.customDesign.checkboxEntireBackgroundColor + ';}';
          style += '#sincloBox #' + checkboxName + ' .sinclo-checkbox {color: ' +
              data.settings.customDesign.checkboxTextColor + ';}';
        }
        if (data.message) {
          style += '#sincloBox #' + checkboxName + ' {margin-top: 8px}';
        }
        style += '</style>';

        var html = '<div id="' + checkboxName + '" style="display: block;" data-separator="' + data.settings.checkboxSeparator + '">';
        html += style;
        if (data.oldValue) {
          var oldMessages = data.oldValue.split(separator);
        }
        var i = 0;
        angular.forEach(data.options, function(option, key) {
          if (!option || option === '') return false;
          var hasOldOptionValue = false;
          html += '<label class="sinclo-checkbox">';
          if (data.isRestore && data.oldValue) {
            angular.forEach(oldMessages, function(oldMessage) {
              if (option === oldMessage) {
                html += '<input type="checkbox" checked="checked" value="' + option + '">';
                hasOldOptionValue = true;
                i++;
              }
            });
            if (!hasOldOptionValue) {
              html += '<input type="checkbox" value="' + option + '">';
            }
          } else {
            html += '<input type="checkbox" value="' + option + '">';
          }
          html += option;
          html += '<span class="checkmark"></span>';
          html += '</label>';
        });
        if (i > 0) {
          html += '<span class="ok-button checkbox-submit-btn">OK</span>';
        } else {
          html += '<span  class="ok-button checkbox-submit-btn disabledArea">OK</span >';
        }
        html += '</div>';

        return {
          html: messageHtml + html,
          checkboxName: checkboxName
        };
      },

      getCheckboxSeparator: function(separatorType) {
        switch (Number(separatorType)) {
          case 1:
            return ',';
          case 2:
            return '/';
          case 3:
            return '|';
          default:
            return ',';
        }
      },
      createCalendarInput: function(data) {
        var result = {html: '', selector: ''};
        var messageHtml = this.createMessage(data.message, data.prefix);
        var prefix = (typeof data.prefix !== 'undefined' && data.prefix !== '') ? data.prefix + '-' : '';
        var index = $('#chatTalk > div:not([style*="display: none;"])').length;
        var calendarId = prefix + 'sinclo-calendar' + index;
        var inputId = prefix + 'sinclo-datepicker' + index;
        var html = '<div id="' + calendarId + '">';
        html += '<style>';
        html += '#sincloBox #' + calendarId + ' .flatpickr-calendar { border: 2px solid ' + data.design.borderColor +
            ';}';
        html += '#sincloBox #' + calendarId + ' .flatpickr-calendar .flatpickr-months { background: ' +
            data.design.headerBackgroundColor + ';}';
        html += '#sincloBox #' + calendarId + ' .flatpickr-calendar .flatpickr-months .flatpickr-month { color: ' +
            data.design.headerTextColor + ';}';
        html += '#sincloBox #' + calendarId + ' .flatpickr-calendar .flatpickr-weekdays { background: ' +
            data.design.headerWeekdayBackgroundColor + ';}';
        html += '#sincloBox #' + calendarId +
            ' .flatpickr-calendar .flatpickr-weekdaycontainer .flatpickr-weekday { color: ' +
            this.getContrastColor(data.design.headerWeekdayBackgroundColor) + ' !important;}';
        html += '#sincloBox #' + calendarId + ' .flatpickr-calendar .flatpickr-months .flatpickr-prev-month { fill: ' +
            data.design.headerTextColor + ';}';
        html += '#sincloBox #' + calendarId + ' .flatpickr-calendar .flatpickr-months .flatpickr-next-month { fill: ' +
            data.design.headerTextColor + ';}';
        html += '#sincloBox #' + calendarId + ' .flatpickr-calendar .dayContainer { background-color: ' +
            data.design.calendarBackgroundColor + ';}';
        html += '#sincloBox #' + calendarId +
            ' .flatpickr-calendar .dayContainer .flatpickr-day.today:after { content: "";position: absolute;top: 0px;left: 0px;width: 27px;height: 29px;display: inline-block;  border: 1px solid ' +
            data.design.headerBackgroundColor + '; outline: 1px solid ' + data.design.headerWeekdayBackgroundColor +
            ';}';
        html += '#sincloBox #' + calendarId +
            ' .flatpickr-calendar .dayContainer .flatpickr-day { border-top: none;  border-left:none; border-bottom: 1px solid  ' +
            data.design.headerWeekdayBackgroundColor + '; border-right: 1px solid  ' +
            data.design.headerWeekdayBackgroundColor + ';}';
        html += '#sincloBox #' + calendarId + ' .dayContainer .flatpickr-day.selected { background-color: ' +
            data.design.headerBackgroundColor + '; color: ' + this.getContrastColor(data.design.headerBackgroundColor) +
            ' !important;}';
        html += '#sincloBox #' + calendarId +
            ' .flatpickr-calendar .dayContainer span:nth-child(7n+7) { border-right: none;};';
        html += '</style>';
        if (data.isRestore && Date.parse(data.oldValue)) {
          html += '<input type="hidden" id="' + inputId + '" value="' + data.oldValue + '"></div>';
          html += '<div><a class="nextBtn" style="color: ' + data.textColor + '; background-color: ' +
              data.backgroundColor + ';" id="' + data.prefix + '_next"">次へ</a></div>';
        } else {
          html += '<input type="hidden" id="' + inputId + '"></div>';
        }

        result.html = messageHtml + html;
        result.selector = '#' + calendarId;

        return result;
      },

      createButton: function(data) {
        var messageHtml = this.createMessage(data.message, data.prefix, data.settings.customDesign.messageAlign);
        var prefix = (typeof data.prefix !== 'undefined' && data.prefix !== '') ? data.prefix + '-' : '';
        var index = $('#chatTalk > div:not([style*="display: none;"])').length;
        var buttonName = prefix + 'sinclo-button-' + index;
        var hasOldOptionValue = false;
        /* ウィジェットサイズ小でアイコン表示だとプルダウンが見切れてしまう対策*/
        var minWidth = '210';
        if (this._needResizeCauseIcon()) {
          minWidth = '183';
        }
        var sideBySideClass = '';
        if (data.options.length === 2) {
          sideBySideClass = ' sideBySide';
        }
        var noTextClass = '';
        if (messageHtml.length === 0) {
          noTextClass = ' noText';
        }
        var alignClass = '';
        switch(Number(data.settings.customDesign.buttonAlign)) {
          case 1:
            alignClass = ' alignLeft';
            break;
          case 2:
            alignClass = '';
            break;
          case 3:
            alignClass = ' alignRight';
            break;
        }
        // style
        var style = '';
        if (data.options.length === 2) {
          style += 'flex-flow: row nowrap; ';
        } else {
          style += 'flex-flow: column nowrap; ';
        }

        var html = '<style>';
        html += '  #sincloBox li.sinclo_re.no-wrap.all-round .sinclo-button-wrap .sinclo-button:hover {background-color: ' + this.getRawColor(this._settings.main_color, 0.2) + '!important;}';
            data.settings.customDesign.buttonActiveColor + '!important;}';
        html += '  #sincloBox li.sinclo_re.no-wrap.all-round .sinclo-button-wrap .sinclo-button:active {background-color: ' +
            data.settings.customDesign.buttonActiveColor + '!important;}';
        html += '  #sincloBox li.sinclo_re.no-wrap.all-round .sinclo-button-wrap .sinclo-button.selected {background-color: ' +
            data.settings.customDesign.buttonActiveColor + '!important;}';
        html += '</style>';

        html += messageHtml + '<div id="' + buttonName + '" class="sinclo-button-wrap' + sideBySideClass + noTextClass + '" >';

        angular.forEach(data.options, function(option, key) {
          var buttonStyle = 'padding: 12px; color: ' + data.settings.customDesign.buttonTextColor +
              '; background-color: ' + data.settings.customDesign.buttonBackgroundColor + ';';
          if (!data.settings.customDesign.outButtonNoneBorder) {
            if (noTextClass.length === 0) {
              buttonStyle += 'border-top: 1px solid ' + data.settings.customDesign.buttonBorderColor + ';';
            } else if(data.settings.options.length > 2 && Number(key) !== data.settings.options.length - 1) {
              buttonStyle += 'border-bottom: 1px solid ' + data.settings.customDesign.buttonBorderColor + ';';
            }
          }
          if (data.settings.options.length === 2) {
            buttonStyle += 'flex-flow: row nowrap; ';
            if (data.settings.customDesign)
              if (Number(key) === 0) {
                buttonStyle += 'border-bottom-left-radius: 12px; ';
                if (!data.settings.customDesign.outButtonNoneBorder) {
                  buttonStyle += 'border-right: 1px solid ' + data.settings.customDesign.buttonBorderColor + '!important;';
                }
              } else {
                buttonStyle += 'border-bottom-right-radius: 12px; ';
              }
          } else {
            buttonStyle += 'flex-flow: column nowrap; ';
            if (Number(key) === data.settings.options.length - 1) {
              buttonStyle += 'border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; ';
            }
          }
          if (!option || option == '') return false;
          if (data.isRestore && option === data.oldValue) {
            html += '<span class="sinclo-button selected' + noTextClass + alignClass + '" style="' + buttonStyle + '">' + option + '</span>';
            hasOldOptionValue = true;
          } else {
            html += '<span class="sinclo-button' + noTextClass + alignClass + '" style="' + buttonStyle + '">' + option + '</span>';
          }
        });
        html += '</div>';

        return html;
      },

      getContrastColor: function(hex) {
        var rgb = this.hexToRgb(hex);
        var brightness;
        brightness = (rgb.r * 299) + (rgb.g * 587) + (rgb.b * 114);
        brightness = brightness / 255000;
        // values range from 0 to 1
        // anything greater than 0.5 should be bright enough for dark text
        return brightness >= 0.5 ? 'black' : 'white';
      },

      getRawColor: function(hex, opacity) {
        if(!opacity) {
          opacity = 0.1;
        }
        var code = hex.substr(1), r, g, b;
        if (code.length === 3) {
          r = String(code.substr(0, 1)) + String(code.substr(0, 1));
          g = String(code.substr(1, 1)) + String(code.substr(1, 1));
          b = String(code.substr(2)) + String(code.substr(2));
        } else {
          r = String(code.substr(0, 2));
          g = String(code.substr(2, 2));
          b = String(code.substr(4));
        }
        var balloonR = String(Math.floor(255 - (255 - parseInt(r, 16)) * opacity));
        var balloonG = String(Math.floor(255 - (255 - parseInt(g, 16)) * opacity));
        var balloonB = String(Math.floor(255 - (255 - parseInt(b, 16)) * opacity));
        var codeR = parseInt(balloonR).toString(16);
        var codeG = parseInt(balloonG).toString(16);
        var codeB = parseInt(balloonB).toString(16);

        return ('#' + codeR + codeG + codeB).toUpperCase();
      },

    createCarousel: function (data) {
      var result = { html: '', selector: ''};
      var messageHtml = data.message !== "" ? this.createMessage(data.message, data.prefix) : "";
      var prefix = (typeof data.prefix !== 'undefined' && data.prefix !== '') ? data.prefix + '-' : '';
      var index = $('#chatTalk > div:not([style*="display: none;"])').length;
      var carouselId = prefix + 'sinclo-carousel-' + index;
      var html = '';
      var carouselSize = this.getCarouselSize(data.settings);
      var arrowPosition = this.getArrowPosition(data.settings);
      var thumbnailWidth = carouselSize.width + 2;
      var containerWidth = carouselSize.containerWidth + 2;
      var imgWidth = data.settings.outCarouselNoneBorder ? carouselSize.width + 2 : carouselSize.width;
      html+= '<div class=\'carousel-container\' style="width: ' + containerWidth + 'px; margin-top: 6px;">';
      html += '<style>';
      html += '#sincloBox #' + carouselId + ' .slick-dots li { border-radius: unset; background: none; padding: 0 5px;}';
      html += '#sincloBox #' + carouselId + ' .slick-dots li button:before { font-size: 25px;}';
      html += '#sincloBox #' + carouselId + ' .slick-next:before { font-family: "Font Awesome 5 Pro"; font-size: 28px; opacity: .5; color: ' + data.settings.customDesign.arrowColor + ';}';
      html += '#sincloBox #' + carouselId + ' .slick-prev:before { font-family: "Font Awesome 5 Pro"; font-size: 28px; opacity: .5; color: ' + data.settings.customDesign.arrowColor + ';}';
      html += '#sincloBox #' + carouselId + ' .thumbnail .caption .title strong { font-size: ' + data.settings.customDesign.titleFontSize + 'px; color: ' + data.settings.customDesign.titleColor + '; text-align: ' + this.getTitleTextAlign(data.settings.titlePosition) + ';}';
      html += '#sincloBox #' + carouselId + ' .thumbnail .caption .title {  margin: 10px 12px 3px 12px; text-align: ' + this.getTitleTextAlign(data.settings.titlePosition) + ';}';
      html += '#sincloBox #' + carouselId + ' .thumbnail .caption .sub-title { margin: 0 12px 8px 12px; font-size: ' + data.settings.customDesign.subTitleFontSize + 'px; color: ' + data.settings.customDesign.subTitleColor + '; text-align: ' + this.getTitleTextAlign(data.settings.subTitlePosition) + ';}';
      html += '#sincloBox #' + carouselId + ' .thumbnail:hover { -webkit-filter: brightness(110%); filter: brightness(110%);}';
      if (data.settings.outCarouselNoneBorder) {
        html += '#sincloBox #' + carouselId + ' .thumbnail { border: none;} ';
      } else {
        html += '#sincloBox #' + carouselId + ' .thumbnail { border: 1px solid ' + data.settings.customDesign.outBorderColor + ';} ';
      }

      if (data.settings.inCarouselNoneBorder) {
        html += '#sincloBox #' + carouselId + ' .thumbnail img { border-bottom: none;} ';
      } else {
        html += '#sincloBox #' + carouselId + ' .thumbnail img { border-bottom: 1px solid ' + data.settings.customDesign.inBorderColor + ';} ';
      }

      if (data.settings.arrowType !== '2') {
        html += '#sincloBox #' + carouselId + ' .slick-next:before { font-weight: 900 }';
        html += '#sincloBox #' + carouselId + ' .slick-prev:before { font-weight: 900 }';
      }

      html += '#sincloBox #' + carouselId + ' .slick-next { right: ' + arrowPosition.right + 'px }';
      html += '#sincloBox #' + carouselId + ' .slick-prev { left: ' + arrowPosition.left + 'px }';
      html += '</style>';

      html+= '<div class="single-item" id="' + carouselId + '">';
      angular.forEach(data.images, function (image, key) {
        html+= '<div style="width: ' + containerWidth + 'px">';
        html+= '<div class="thumbnail" id="' + prefix + 'image' + key + '" style="cursor: pointer; margin-left: auto; margin-right: auto; display: flex; flex-direction: column; background-color: #FFFFFF; width: ' + thumbnailWidth + 'px;">';
        html+= '<img id="img_' + prefix + 'image' + key +'" style="cursor: pointer; width: ' + imgWidth + 'px; height: ' + carouselSize.height + 'px" src="' + image.url + '" />';
        html+= '<div class="caption" style="display: flex; flex-direction: column; flex: 1 0 auto;">';
        html+= '<div class="title"><strong style="font-weight: bold">' + image.title + '</strong></div>';
        html+= '<p class="sub-title">' + image.subTitle + '</p>';
        html+= '</div></div></div>';
      });
      html+= '</div></div>';

      result.html = messageHtml === "" ? html :messageHtml +  html;
      result.selector = '#' + carouselId;
      return result;
    },
    getButtonUIWidth: function() {
        var width = 280;
        switch (Number(this.widgetSizeTypeToggle)) {
          case 1:
            width= 183;
            break;
          case 2:
            width= 230;
            break;
          case 3:
            width= 280;
            break;
          case 4:
            width= 280;
            break;
          default:
            width= 280;
            break;
        }
        return Number(this.settings.show_chatbot_icon) === 1 ? width : width + 20;
    },
    getCarouselSize: function(settings) {
      if (settings.carouselPattern === '1') {
        return this.getInsideArrowCarouselSize(settings);
      } else {
        return this.getOutsideArrowCarouselSize(settings);
      }
    },
    getOutsideArrowCarouselSize: function(settings) {
      if (settings.balloonStyle === '1'){
        return this.getOutsideArrowHasBalloonCarouselSize(settings);
      } else {
        return this.getOutsideArrowNoneBalloonCarouselSize(settings);
      }
    },
    getOutsideArrowNoneBalloonCarouselSize: function(settings) {
      var aspectRatio = settings.aspectRatio;
      if (!aspectRatio) {
        aspectRatio = 1;
      }
      var data = {width: 0, height: 0, containerWidth: 0};
      switch (Number(this.widgetSizeTypeToggle)) {
        case 1:
          data.containerWidth = 170;
          data.width          = settings.lineUpStyle === '1' ? 170 : 100;
          break;
        case 2:
          data.containerWidth = 220;
          data.width          = settings.lineUpStyle === '1' ? 220 : 130;
          break;
        case 3:
          data.containerWidth = 280;
          data.width          = settings.lineUpStyle === '1' ? 280 : 170;
          break;
        case 4:
          data.containerWidth = 280;
          data.width          = settings.lineUpStyle === '1' ? 280 : 170;
          break;
        default:
          data.containerWidth = 280;
          data.width          = settings.lineUpStyle === '1' ? 280 : 170;
          break;
      }
      if (Number(this.settings.show_chatbot_icon) === 1 && settings.balloonStyle === '1') {
        data.containerWidth = data.containerWidth - this.getChatIconWidth();
        data.width = settings.lineUpStyle === '1' ? data.width - this.getChatIconWidth() : data.width - this.getChatIconWidth() + 13;
      }
      data.height = data.width / aspectRatio;

      return data;
    },
    getOutsideArrowHasBalloonCarouselSize: function(settings) {
      var aspectRatio = settings.aspectRatio;
      if (!aspectRatio) {
        aspectRatio = 1;
      }
      var data = {width: 0, height: 0, containerWidth: 0};
      switch (Number(this.widgetSizeTypeToggle)) {
        case 1:
          data.containerWidth = 170;
          data.width          = settings.lineUpStyle === '1' ? 170 : 100;
          break;
        case 2:
          data.containerWidth = 220;
          data.width          = settings.lineUpStyle === '1' ? 220 : 130;
          break;
        case 3:
          data.containerWidth = 260;
          data.width          = settings.lineUpStyle === '1' ? 260 : 158;
          break;
        case 4:
          data.containerWidth = 260;
          data.width          = settings.lineUpStyle === '1' ? 260 : 158;
          break;
        default:
          data.containerWidth = 260;
          data.width          = settings.lineUpStyle === '1' ? 260 : 158;
          break;
      }
      if (Number(this.settings.show_chatbot_icon) === 1 && settings.balloonStyle === '1') {
        data.containerWidth = data.containerWidth - this.getChatIconWidth();
        data.width = settings.lineUpStyle === '1' ? data.width - this.getChatIconWidth() : data.width - this.getChatIconWidth() + 13;
      }
      data.height = data.width / aspectRatio;

      return data;
    },
    getInsideArrowCarouselSize: function(settings) {
      if (settings.balloonStyle === '1'){
        return this.getInsideArrowHasBalloonCarouselSize(settings);
      } else {
        return this.insideArrowNoneBalloonCarouselSize(settings);
      }
    },
    getInsideArrowHasBalloonCarouselSize: function(settings) {
      var aspectRatio = settings.aspectRatio;
      if (!aspectRatio) {
        aspectRatio = 1;
      }
      var data = {width: 0, height: 0, containerWidth: 0};
      switch (Number(this.widgetSizeTypeToggle)) {
        case 1:
          data.containerWidth = 200;
          data.width          = settings.lineUpStyle === '1' ? 200 : 125;
          break;
        case 2:
          data.containerWidth = 250;
          data.width          = settings.lineUpStyle === '1' ? 250 : 155;
          break;
        case 3:
          data.containerWidth = 310;
          data.width          = settings.lineUpStyle === '1' ? 310 : 193;
          break;
        case 4:
          data.containerWidth = 310;
          data.width          = settings.lineUpStyle === '1' ? 310 : 193;
          break;
        default:
          data.containerWidth = 310;
          data.width          = settings.lineUpStyle === '1' ? 310 : 193;
          break;
      }
      if (Number(this.settings.show_chatbot_icon) === 1 && settings.balloonStyle === '1') {
        data.containerWidth = data.containerWidth - this.getChatIconWidth();
        data.width = settings.lineUpStyle === '1' ? data.width - this.getChatIconWidth() : data.width - this.getChatIconWidth() + 13;
      }
      data.height = data.width / aspectRatio;

      return data;
    },
    insideArrowNoneBalloonCarouselSize: function(settings) {
      var aspectRatio = settings.aspectRatio;
      if (!aspectRatio) {
        aspectRatio = 1;
      }
      var data = {width: 0, height: 0, containerWidth: 0};
      switch (Number(this.widgetSizeTypeToggle)) {
        case 1:
          data.containerWidth = 220;
          data.width          = settings.lineUpStyle === '1' ? 220 : 136;
          break;
        case 2:
          data.containerWidth = 280;
          data.width          = settings.lineUpStyle === '1' ? 280 : 170;
          break;
        case 3:
          data.containerWidth = 340;
          data.width          = settings.lineUpStyle === '1' ? 340 : 215;
          break;
        case 4:
          data.containerWidth = 340;
          data.width          = settings.lineUpStyle === '1' ? 340 : 215;
          break;
        default:
          data.containerWidth = 340;
          data.width          = settings.lineUpStyle === '1' ? 340 : 215;
          break;
      }
      if (Number(this.settings.show_chatbot_icon) === 1 && settings.balloonStyle === '1') {
        data.containerWidth = data.containerWidth - this.getChatIconWidth();
        data.width = settings.lineUpStyle === '1' ? data.width - this.getChatIconWidth() : data.width - this.getChatIconWidth() + 13;
      }
      data.height = data.width / aspectRatio;

      return data;
    },
    getChatIconWidth: function() {
      switch (Number(this.widgetSizeTypeToggle)) {
        case 1:
          return 32;
        case 2:
          return 37;
        case 3:
          return 42;
        case 4:
          return 42;
        default:
          return 37;
      }
    },
    getArrowPosition: function(setting){
      var data = { left: 0, right: 0 };
      if (setting.lineUpStyle === '1') {
        if (setting.carouselPattern === '2') {
          if (setting.arrowType === '3') {
            data.left = -30;
            data.right = -30;
          } else {
            data.left = -34;
            data.right = -30;
          }
        } else {
          switch (setting.arrowType) {
            case '1':
            case '2':
              data.left = 8;
              data.right = 14;
              break;
            case '3':
              data.left = 8;
              data.right = 8;
              break;
            case '4':
              data.left = 8;
              data.right = 10;
              break;
            default:
              data.left = 8;
              data.right = 8;
              break;
          }
        }
      } else {
        if (setting.carouselPattern === '2') {
          switch (setting.arrowType) {
            case '1':
            case '2':
              data.left = -27;
              data.right = -25;
              break;
            case '3':
              data.left = -20;
              data.right = -25;
              break;
            case '4':
              data.left = -27;
              data.right = -25;
              break;
            default:
              data.left = -27;
              data.right = -25;
              break;
          }
        } else {
          switch (setting.arrowType) {
            case '1':
            case '2':
              data.left = 16;
              data.right = 16;
              break;
            case '3':
              data.left = 12;
              data.right = 8;
              break;
            case '4':
              data.left = 16;
              data.right = 16;
              break;
            default:
              data.left = 16;
              data.right = 16;
              break;
          }
        }
      }

      return data;
    },

    getTitleTextAlign: function (value){
      switch (Number(value)) {
        case 1:
          return 'left';
        case 2:
          return 'center';
        case 3:
          return 'right';
        default:
          return 'left';
      }
    },

    hexToRgb: function (hex) {
      var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
      return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
      } : null;
    },

    /**
     * ファイル拡張子から、画像か判別する
     * @param String extension ファイル拡張子
     */
    isImage: function(extension) {
      return /jpeg|jpg|gif|png/i.test(extension);
    },

    /**
     * ファイルタイプ別ごとに、font-awesome用のクラスを出し分ける
     * @param String extension ファイルの拡張子
     */
    selectIconClassFromExtension: function(extension) {
      var selectedClass = "";
      var icons = {
        image:      'fa-file-image',
        pdf:        'fa-file-pdf',
        word:       'fa-file-word',
        powerpoint: 'fa-file-powerpoint',
        excel:      'fa-file-excel',
        audio:      'fa-file-audio',
        video:      'fa-file-video',
        zip:        'fa-file-zip',
        code:       'fa-file-code',
        text:       'fa-file-text',
        file:       'fa-file'
      };
      var extensions = {
        gif: icons.image,
        jpeg: icons.image,
        jpg: icons.image,
        png: icons.image,
        pdf: icons.pdf,
        doc: icons.word,
        docx: icons.word,
        ppt: icons.powerpoint,
        pptx: icons.powerpoint,
        xls: icons.excel,
        xlsx: icons.excel,
        aac: icons.audio,
        mp3: icons.audio,
        ogg: icons.audio,
        avi: icons.video,
        flv: icons.video,
        mkv: icons.video,
        mp4: icons.video,
        gz: icons.zip,
        zip: icons.zip,
        css: icons.code,
        html: icons.code,
        js: icons.code,
        txt: icons.text,
        csv: icons.csv,
        file: icons.file
      };

      return extensions[extension] || extensions['file'];
    },

      /* ===============
         diagram methods
         =============== */

      createBranchRadioMessage: function(nodeId, message, selection, labels, settings) {
        var messageHtml = this.createMessage(message, nodeId);
        var suffix = (typeof nodeId !== 'undefined' && nodeId !== '') ? nodeId + '-' : '';
        var index = $('#chatTalk > div:not([style*="display: none;"])').length;
        var radioName = 'sinclo-radio-' + index + suffix;
        // style
        var html = '<div id="' + radioName + '" style="line-height: 0!important; ">';
        var style = '<style>';
        style += '#sincloBox #' + radioName + ' span.sinclo-radio [type="radio"] + label:before {background-color: ' +
            settings.customDesign.radioBackgroundColor + ' !important;}';
        style += '#sincloBox #' + radioName + ' span.sinclo-radio [type="radio"]:checked + label:after {background: ' +
            settings.customDesign.radioActiveColor + ' !important;}';
        style += '#sincloBox #' + radioName + ' span.sinclo-radio:first-of-type {margin-top: 4px !important}';
        style += '#sincloBox #' + radioName + ' span.sinclo-radio {margin-top: ' +
            settings.customDesign.radioSelectionDistance + 'px !important;}';
        style += '#sincloBox #' + radioName + ' span.sinclo-text-line {margin-top: ' +
            settings.customDesign.radioSelectionDistance + 'px}';
        if (settings.radioNoneBorder) {
          style += '#sincloBox #' + radioName +
              ' span.sinclo-radio [type="radio"] + label:before {border-color: transparent !important;}';
        } else {
          style += '#sincloBox #' + radioName + ' span.sinclo-radio [type="radio"] + label:before {border-color: ' +
              settings.customDesign.radioBorderColor + '!important;}';
        }

        if (settings.customDesign.radioStyle !== '1') {
          style += '#sincloBox #' + radioName + ' span.sinclo-radio [type="radio"] + label {background-color: transparent;}';
        } else {
          style += '#sincloBox #' + radioName + ' span.sinclo-radio {display: block!important; padding: 8px; color: ' + settings.customDesign.radioTextColor + ';}';
        }
        style += '</style>';
        html += style;

        var selfobj = this;
        angular.forEach(labels, function(option, key) {
          if (!option || option == '') return false;
          if (option.type && option.value && Number(option.type) === 2) {
            html += selfobj.createMessage(option.value, nodeId);
          } else {
            var message = option.value ? option.value : option;
            html += '<span class=\'sinclo-radio\'><input type=\'radio\' name=\'' + radioName + '\' id=\'' + radioName + '-' +
                option.uuid + '\' class=\'sinclo-chat-radio\' value=\'' + message + '\' data-nid=\'' + nodeId +
                '\' data-next-nid=\'' + selection[option.uuid] + '\'>';
            html += '<label for=\'' + radioName + '-' + option.uuid + '\'>' + message + '</label></span>' + "\n";
          }
        });
        html += '</select>';
        html += '</div>';

        return messageHtml + html;
      },

      createBranchButtonMessage: function(nodeId, message, selection, labels, settings) {
        var messageHtml = this.createMessage(message, nodeId);
        var suffix = (typeof nodeId !== 'undefined' && nodeId !== '') ? nodeId + '-' : '';
        var index = $('#chatTalk > div:not([style*="display: none;"])').length;
        var buttonUIName = 'sinclo-buttonUI-' + index + suffix;
        var hasOldOptionValue = false;
        var style = '<style>';
        style += ' #sincloBox #' + buttonUIName + ' .sinclo-button-ui {cursor: pointer; min-height: 35px; margin-bottom: 1px; padding: 10px 15px;}';
        style += ' #sincloBox #' + buttonUIName + ' button.sinclo-button-ui {background-color: ' + settings.customDesign.buttonUIBackgroundColor + '}';
        style += ' #sincloBox #' + buttonUIName + ' button.sinclo-button-ui {width: ' + this.getButtonUIWidth() + 'px;}';
        style += ' #sincloBox #' + buttonUIName + ' button.sinclo-button-ui {color: ' + settings.customDesign.buttonUITextColor + '}';
        style += ' #sincloBox #' + buttonUIName + ' button.sinclo-button-ui:focus {outline: none}';
        style += ' #sincloBox #' + buttonUIName + ' button.sinclo-button-ui:active {background-color: ' + settings.customDesign.buttonUIActiveColor +'}';
        style += ' #sincloBox #' + buttonUIName + ' button.sinclo-button-ui.top {border-top-left-radius: 8px; border-top-right-radius: 8px}';
        style += ' #sincloBox #' + buttonUIName + ' button.sinclo-button-ui.bottom {border-bottom-left-radius: 8px; border-bottom-right-radius: 8px}';
        style += ' #sincloBox #' + buttonUIName + ' button.sinclo-button-ui.selected {background-color: ' + settings.customDesign.buttonUIActiveColor + ' !important;}';
        style += '#sincloBox #' + buttonUIName +
            ' span.sinclo-text-line { margin: 4px 0; }';
        if (message) {
          style += ' #sincloBox #' + buttonUIName + ' {margin-top: 8px}';
        }
        if (settings.outButtonUINoneBorder) {
          style += ' #sincloBox #' + buttonUIName + ' button.sinclo-button-ui {border: none}';
        } else {
          style += ' #sincloBox #' + buttonUIName + ' button.sinclo-button-ui {border: 1px solid ' + settings.customDesign.buttonUIBorderColor +' }';
        }
        switch (Number(settings.customDesign.buttonUITextAlign)) {
          case 1:
            style += ' #sincloBox #' + buttonUIName + ' button.sinclo-button-ui {text-align: left}';
            break;
          case 2:
            style += ' #sincloBox #' + buttonUIName + ' button.sinclo-button-ui {text-align: center}';
            break;
          case 3:
            style += ' #sincloBox #' + buttonUIName + ' button.sinclo-button-ui {text-align: right}';
            break;
          default:
            style += ' #sincloBox #' + buttonUIName + ' button.sinclo-button-ui {text-align: center}';
            break;
        }
        style += '</style>';

        var html = '<div id="' + buttonUIName + '">';
        html += style;

        var selfobj = this;
        angular.forEach(labels, function(option, key) {
          if (!option || option === '') return false;
          var isPrevMessage = (Number(key) > 0 && Number(labels[Number(key) - 1].type) === 2);
          var isNextMessage = (Number(key) < Object.keys(labels).length - 1 && Number(labels[Number(key) + 1].type) === 2);
          var isEnd = (Number(key) === Object.keys(labels).length - 1);
          var addClass = '';
          if(Number(key) === 0 || isPrevMessage) {
            addClass += 'top';
          }
          if(isNextMessage || isEnd) {
            addClass += ' bottom';
          }
          if (option.type && option.value && Number(option.type) === 2) {
            html += selfobj.createMessage(option.value, nodeId);
          } else {
            var message = option.value ? option.value : option;
            html += '<button onclick="return false;" class="sinclo-button-ui ' + addClass + '" data-nid=\'' + nodeId +
                '\' data-next-nid=\'' + selection[option.uuid] + '\'>' + message + '</button>';
          }
        });
        html += '</div>';

        return messageHtml + html;
      }

  };
});
</script>
