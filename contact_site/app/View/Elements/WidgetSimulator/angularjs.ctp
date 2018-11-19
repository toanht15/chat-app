<script type="text/javascript">
'use strict';

sincloApp.controller('SimulatorController', ['$scope', '$timeout', 'SimulatorService',  function($scope, $timeout, SimulatorService) {
  //thisを変数にいれておく
  var self = this;
  $scope.simulatorSettings = SimulatorService;

  $scope.isTabDisplay = document.querySelector('[id$="IsTabDisplay"]').value == true;
  $scope.canVisitorSendMessage = document.querySelector('[id$="CanVisitorSendMessage"]').value == true;

  // 自由入力エリアの表示状態
  $scope.isTextAreaOpen = true;
  // 自由入力エリアの、改行入力の許可状態
  $scope.allowInputLF = true;
  // 自由入力エリアの、メッセージ送信の許可状態
  $scope.allowSendMessageByShiftEnter = false;
  // 入力制御
  $scope.inputRule = <?= C_MATCH_INPUT_RULE_ALL ?>;
  // can skip hearing action
  $scope.isRequired = true;
  $scope.isShowSkipBtn = false;
  // 自由入力エリアのdisabled状態
  $scope.isTextAreaDisabled = false;

  // calendar japanese custom
  $scope.japaneseCalendar = {
    dateFormat: "Y/m/d",
    locale: {
      firstDayOfWeek: 0,
      weekdays: {
        shorthand: ["日", "月", "火", "水", "木", "金", "土"],
        longhand: ["日曜日", "月曜日", "火曜日", "水曜日", "木曜日", "金曜日", "土曜日"],
      },
      months: {
        shorthand: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
        longhand: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
      },
    },
  };


  /**
   * addReMessage
   * 企業側メッセージの追加
   * @param String message 追加するメッセージ
   * @param String prefix  ラジオボタンに付与するプレフィックス
   */
  $scope.$on('addReMessage', function(event, message, prefix) {
    $scope.addMessage('re', message, prefix);
  });

  $scope.$on('addReErrorMessage', function(event, message, prefix) {
    $scope.addMessage('re', message, prefix, 'deleteme');
  });

  $scope.$on('addReForm', function(event, data) {
    $scope.addForm(data);
  });

  $scope.$on('addReRadio', function(event, data) {
    $scope.addRadioButton(data);
  });

  $scope.$on('addRePulldown', function(event, data) {
    $scope.addPulldown(data);
  });

  $scope.$on('addReCalendar', function(event, message, settings, design, prefix) {
    $scope.addCalendar(message, settings, design, prefix);
  });

  /**
   * addSeMessage
   * サイト訪問者側メッセージの追加 TODO: 現在使用されていないため、仮実装状態
   * @param String message 追加するメッセージ
   */
  $scope.$on('addSeMessage', function(event, message, prefix) {
    console.log("=== SimulatorController::addSeMessage ===");
    $scope.addMessage('se', message, prefix);
  });

  /**
   * addReFileMessage
   * 企業側ファイル送信のメッセージ追加
   * @param Object fileObj 追加するメッセージ
   */
  $scope.$on('addReFileMessage', function(event, fileObj) {
    $scope.addFileMessage('re', fileObj);
  });

  /**
   *
   */
  $scope.extensionType = null;
  $scope.extendedExtensions = null;
  $scope.$on('addSeReceiveFileUI', function(event, dropAreaMessage, calcelable, cancelLabel, extensionType, extendedExtensions) {
    $scope.extensionType = extensionType;
    $scope.extendedExtensions = extendedExtensions.split(",");
    $scope.addReceiveFileUI(dropAreaMessage, calcelable, cancelLabel);
  });

  /**
   * removeMessage
   * メッセージの消去（コピー元となる非表示要素を残して削除する）
   */
  $scope.$on('removeMessage', function(event) {
    document.querySelector('#sincloChatMessage').value = '';
    document.querySelector('#miniSincloChatMessage').value = '';
    var elms = $('#chatTalk > div:not([style*="display: none;"])');
    angular.forEach(elms, function(elm) {
      document.querySelector('#chatTalk').removeChild(elm);
    });
    $scope.resizeWidgetHeightByWindowHeight();
  });

  /**
   * visitorSendMessage
   * サイト訪問者のメッセージ受信と、呼び出し元アクションへの通知
   */
  $scope.visitorSendMessage = function() {
    var message = $('#sincloChatMessage').val() ? $('#sincloChatMessage').val() : $('#miniSincloChatMessage').val();
    if (typeof message === 'undefined' || message.trim() === '') {
      return;
    }

    // 設定を初期状態に戻す
    $scope.allowInputLF = true;
    $scope.allowSendMessageByShiftEnter = false;
    $scope.inputRule = <?= C_MATCH_INPUT_RULE_ALL ?>;

    var prefix = 'action' + $scope.simulatorSettings.getCurrentActionStep() + '_hearing' + $scope.simulatorSettings.getCurrentHearingIndex() + '_underline';
    console.log(message);
    $scope.addMessage('se', message, prefix);
    $('#sincloChatMessage').val('');
    $('#miniSincloChatMessage').val('');
    $scope.$emit('receiveVistorMessage', message)
  };

  /**
   * addMessage
   * シミュレーター上へのメッセージ追加
   * @param String type     企業側(re)・訪問者側(se)のメッセージタイプ
   * @param String message  追加するメッセージ
   * @param String prefix   ラジオボタンに付与するプレフィックス
   */
  $scope.addMessage = function(type, message, prefix, appendClass) {
    // ベースとなる要素をクローンし、メッセージを挿入する
    if (type === 're') {
      var divElm = document.querySelector('#chatTalk div > li.sinclo_re.chat_left').parentNode.cloneNode(true);
      divElm.id = prefix + '_question';
    } else {
      var divElm = document.querySelector('#chatTalk div > li.sinclo_se.chat_right').parentNode.cloneNode(true);
      divElm.id = prefix + '_answer';
    }
    var formattedMessage = $scope.simulatorSettings.createMessage(message, prefix);
    divElm.querySelector('li .details:not(.cName)').innerHTML = formattedMessage;
    if(appendClass) {
      divElm.classList.add(appendClass);
    }
    // 要素を追加する
    document.getElementById('chatTalk').appendChild(divElm);
    $('#chatTalk > div:last-child').show();
    self.autoScroll();
  };

  $scope.addForm = function(data) {
    var divElm = null;
    if(data.isConfirm) {
      divElm = document.querySelector('#chatTalk div > li.sinclo_re.sinclo_form').parentNode.cloneNode(true);
      divElm.id = data.prefix + '_question';
    } else {
      divElm = document.querySelector('#chatTalk div > li.sinclo_se.sinclo_form').parentNode.cloneNode(true);
      divElm.id = data.prefix + '_answer';
    }

    var html = ""
    if(data.isConfirm) {
      html = $scope.simulatorSettings.createForm(data.isConfirm, data.bulkHearings, data.resultData.data);
      divElm.querySelector('li.sinclo_re.sinclo_form').innerHTML = html;
    } else {
      html = $scope.simulatorSettings.createFormFromLog(data.resultData.data);
      divElm.querySelector('li.sinclo_se.sinclo_form').innerHTML = html;
    }
    // if(isEmptyRequire) {
    //   $(divElm).find('li.sinclo_form span.formOKButton').addClass('disabled');
    // }
    $(divElm).find('li.sinclo_form span.formOKButton').on('click', function(e){
      if($(this).hasClass('disabled')) return;
      var returnValue = {};
      var targetArray = $(divElm).find('li.sinclo_form .formInput');
      targetArray.each(function(index, element) {
        console.log("CHANGED : %s vs %s", $(element).val(), data.resultData.data[$(element).data('input-type')]);
        returnValue[$(element).attr('name')] = {
          label: $(element).data('label-text'),
          value: $(element).val(),
          required: $(element).data('required'),
          changed: $(element).val() !== data.resultData.data[Number($(element).data('input-type'))]
        }
      });
      console.log("return Value : %s",JSON.stringify(returnValue));
      $scope.$emit('pressFormOK', returnValue);
    });
    $(divElm).find('li.sinclo_form input.formInput').on('input', function(){
      $(divElm).find('li.sinclo_form input.formInput').each(function(idx, elem){
        if(data.bulkHearings[idx].required && $(this).val().length === 0) {
          $(divElm).find('li.sinclo_form span.formOKButton').addClass('disabled');
          return false;
        } else if (data.  bulkHearings.length - 1 === idx){
          $(divElm).find('li.sinclo_form span.formOKButton').removeClass('disabled');
        }
      });
    });

    // 要素を追加する
    document.getElementById('chatTalk').appendChild(divElm);
    $('#chatTalk > div:last-child').show();
    self.autoScroll();
  };

  $scope.addRadioButton = function(data) {
    var divElm = document.querySelector('#chatTalk div > li.sinclo_re.chat_left').parentNode.cloneNode(true);
    divElm.id = data.prefix + '_question';
    var html = $scope.simulatorSettings.createRadioButton(data);
    divElm.querySelector('li .details:not(.cName)').innerHTML = html;
    document.getElementById('chatTalk').appendChild(divElm);

    $('#chatTalk > div:last-child').show();
    self.autoScroll();
  };

  $scope.getInputType = function(bulkHearingInputType) {
    var type = '';
    switch(Number(bulkHearingInputType)) {
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
        type ='email';
        break;
      case 11:
        type = 'text';
        break;
      default:
        type = 'text';
        break;
    }
    return type;
  };

  $scope.addPulldown = function(data) {
    var divElm = document.querySelector('#chatTalk div > li.sinclo_re.chat_left').parentNode.cloneNode(true);
    divElm.id = data.prefix + '_question';
    var html = $scope.simulatorSettings.createPulldown(data);
    divElm.querySelector('li .details:not(.cName)').innerHTML = html;
    document.getElementById('chatTalk').appendChild(divElm);

    $('#chatTalk > div:last-child').show();
    self.autoScroll();
  };

  $scope.addCalendar = function(data) {
    var divElm = document.querySelector('#chatTalk div > li.sinclo_re.chat_left').parentNode.cloneNode(true);
    divElm.id = data.prefix + '_question';
    var calendar = $scope.simulatorSettings.createCalendarInput(data);
    divElm.querySelector('li .details:not(.cName)').innerHTML = calendar.html;
    // 要素を追加する
    document.getElementById('chatTalk').appendChild(divElm);
    $scope.addDatePicker(calendar.selector, data.settings, data.design);

    $('#chatTalk > div:last-child').show();
    self.autoScroll();
  };

  // add date picker for calendar in semulator
  $scope.addDatePicker = function (selector, settings, design) {
    var options = $scope.getCalendarOptions(settings);
    $(selector.replace('calendar', 'datepicker')).flatpickr(options);
    $(selector.replace('calendar', 'datepicker')).hide();

    var firstDayOfWeek = $(selector).find('.flatpickr-weekday');
    firstDayOfWeek[0].innerText = settings.language == 1 ? '日' : 'Sun';

    var calendarTextColorTarget = $(selector).find('.flatpickr-calendar .flatpickr-day');
    calendarTextColorTarget.each(function () {
      if (!$(this).hasClass('disabled') && !$(this).hasClass('nextMonthDay') && !$(this).hasClass('prevMonthDay')) {
        $(this).css('color', design.calendarTextColor);
      }
    });

    var sundayTarget = $(selector).find('.dayContainer span:nth-child(7n+1)');
    sundayTarget.each(function () {
      if (!$(this).hasClass('disabled') && !$(this).hasClass('nextMonthDay') && !$(this).hasClass('prevMonthDay')) {
        $(this).css('color', design.sundayColor);
      }
    });

    var saturdayTarget = $(selector).find('.dayContainer span:nth-child(7n+7)');
    saturdayTarget.each(function () {
      if (!$(this).hasClass('disabled') && !$(this).hasClass('nextMonthDay') && !$(this).hasClass('prevMonthDay')) {
        $(this).css('color', design.saturdayColor);
      }
    });

    // change color when change month
    $(selector).find('.flatpickr-calendar .flatpickr-months').on('mousedown', function () {
      $scope.customCalendarTextColor($(selector), design);
    });
    // keep color when click on date
    $(selector.replace('calendar', 'datepicker')).on('change', function () {
      $scope.customCalendarTextColor($(selector), design);
    });
  };

  $scope.customCalendarTextColor = function (calendarTarget, design) {
    var calendarTextColorTarget = calendarTarget.find('.flatpickr-calendar .flatpickr-day');
    calendarTextColorTarget.each(function () {
      if (!$(this).hasClass('disabled') && !$(this).hasClass('nextMonthDay') && !$(this).hasClass('prevMonthDay')) {
        $(this).css('color', design.calendarTextColor);
      }
    });

    var sundayTarget = calendarTarget.find('.dayContainer span:nth-child(7n + 1)');
    sundayTarget.each(function () {
      if (!$(this).hasClass('disabled') && !$(this).hasClass('nextMonthDay') && !$(this).hasClass('prevMonthDay')) {
        $(this).css('color', design.sundayColor);
      }
    });

    var saturdayTarget = calendarTarget.find('.dayContainer span:nth-child(7n+7)');
    saturdayTarget.each(function () {
      if (!$(this).hasClass('disabled') && !$(this).hasClass('nextMonthDay') && !$(this).hasClass('prevMonthDay')) {
        $(this).css('color', design.saturdayColor);
      }
    });

    calendarTarget.find('.flatpickr-calendar .dayContainer').css('background-color', design.calendarBackgroundColor);
  };

  $scope.getCalendarOptions = function(settings) {
    var options = {
      dateFormat: "Y/m/d",
      minDate: 'today',
      inline: 'true',
      disable: [],
      enable: [],
      locale: {
        firstDayOfWeek: 0
      },
    };
    // language
    options.locale = settings.language == 1 ? $scope.japaneseCalendar.locale : {firstDayOfWeek: 0};
    // set min date
    if (settings.isEnableAfterDate) {
      options.minDate = new Date().fp_incr(settings.enableAfterDate);
    } else {
      options.minDate = settings.disablePastDate ? 'today' : '';
    }
    // disable day of week
    if (settings.isDisableDayOfWeek) {
      var disableWeekDays = [];
      angular.forEach(settings.dayOfWeekSetting, function (item, key) {
        if (item) {
          disableWeekDays.push(key);
        }
      });

      options.disable = [
        function (date) {
          return disableWeekDays.indexOf(date.getDay()) !== -1;
        },
      ];
    } else {
      options.disable = [];
    }

    // set specific date
    if (settings.isSetSpecificDate) {
      if (settings.setSpecificDateType == 1) {
        var disableLength = options.disable.length;
        angular.forEach(settings.specificDateData, function (item, key) {
          options.disable[key + disableLength] = item;
        });
      }

      if (settings.setSpecificDateType == 2) {
        angular.forEach(settings.specificDateData, function (item, key) {
          options.enable[key] = item;
        });
      }
    }

    return options;
  };

  /**
   * addFileMessage
   * シミュレーター上へのファイル送信メッセージ追加
   * @param String type     企業側(re)・訪問者側(se)のメッセージタイプ
   * @param Object fileObj  追加するメッセージ
   * @param String prefix   ラジオボタンに付与するプレフィックス
   */
  $scope.addFileMessage = function(type, fileObj) {
    // ベースとなる要素をクローンする
    if (type === 're') {
      var list = document.querySelector('#chatTalk div > li.sinclo_re.file_left');
      var divElm = document.querySelector('#chatTalk div > li.sinclo_re.file_left').parentNode.cloneNode(true);
    } else {
      // 訪問者側からのファイル受信UIは未対応です
    }

    // パラメーターを表示用に設定する
    var tmbImage =  divElm.querySelector('li .sendFileThumbnailArea img.sendFileThumbnail');
    var tmbIcon =  divElm.querySelector('li .sendFileThumbnailArea i.sendFileThumbnail');
    if ($scope.simulatorSettings.isImage(fileObj.extension)) {
      tmbImage.src = fileObj.download_url;
      tmbImage.style.display = "";
      tmbIcon.style.display = "none";
    } else {
      tmbIcon.classList.add($scope.simulatorSettings.selectIconClassFromExtension(fileObj.extension));
      tmbIcon.style.display = "";
      tmbImage.style.display = "none";
    }
    divElm.querySelector('li .sendFileMetaArea .sendFileName').innerHTML = fileObj.file_name;
    divElm.querySelector('li .sendFileMetaArea .sendFileSize').innerHTML = fileObj.file_size;
    divElm.addEventListener('click', function() {
      window.open(fileObj.download_url);
    });

    // 要素を追加する
    document.getElementById('chatTalk').appendChild(divElm);
    $('#chatTalk > div:last-child').show();
    self.autoScroll()
  };

  /**
   * addReceiveFileUI
   * シミュレーター上へのファイル受信用UI表示追加
   */
  $scope.addReceiveFileUI = function(dropAreaMessage, cancelEnabled, cancelButtonLabel) {
    // ベースとなる要素をクローン
    var divElm = document.querySelector('#chatTalk div > li.sinclo_re.chat_left.recv_file_left').parentNode.cloneNode(true);
    var dropAreaElm = divElm.querySelector('li.chat_left.recv_file_left div.receiveFileContent div.selectFileArea');
    var dropAreaMessageElm = divElm.querySelector('li.chat_left.recv_file_left div.receiveFileContent div.selectFileArea p.drop-area-message');
    var selectFileButtonElm = divElm.querySelector('li.chat_left.recv_file_left div.receiveFileContent div.selectFileArea p.drop-area-button a');
    var selectInputElm = $(divElm).find('.receiveFileInput');
    if(cancelEnabled) {
      var cancelButtonElm = divElm.querySelector('li.chat_left.recv_file_left div.cancelReceiveFileArea a');
      cancelButtonElm.innerHTML = cancelButtonLabel;
      cancelButtonElm.addEventListener('click', function(){
        document.getElementById('chatTalk').removeChild(divElm);
        $scope.addMessage('se', "ファイル送信をキャンセル");
        $scope.$emit('receiveVistorMessage', "");
      });
    }
    dropAreaMessageElm.innerHTML = dropAreaMessage;
    // 要素を追加する
    document.getElementById('chatTalk').appendChild(divElm);
    $scope.fileUploader.init($(document.querySelector('#chatTalk')), $(dropAreaElm), $(selectFileButtonElm), $(selectInputElm));
    $('#chatTalk > div:last-child').show();
    self.autoScroll();
  };

  // ===========
  // ファイル送信
  // ===========
  $scope.fileUploader = {
    isDisable: false,
    dragging: false,
    dragArea: null,
    droppable: null,
    selectFileBtn: null,
    selectInput: null,
    fileObj: null,
    loadData: null,

    init: function(dragArea, droppable, selectFileButton, selectInput) {
      this.dragArea = dragArea;
      this.droppable = droppable;
      this.selectFileBtn = selectFileButton;
      this.selectInput = selectInput;
      if(window.FileReader) {
        this._addDragAndDropEvents();
      } else {
        this.isDisable = true;
      }
      this._addSelectFileEvents();
    },
    _addDragAndDropEvents: function() {
      this.dragArea.on("dragenter", this._enterEvent);
      this.dragArea.on("dragover", this._overEvent);
      this.dragArea.on("dragleave", this._leaveEvent);
      this.dragArea.on("drop", function(){ $scope.fileUploader.droppable.css('display', 'none'); event.preventDefault(); event.stopPropagation(); return false;});
      this.droppable.on("drop", this._handleDroppedFile);
    },
    _addSelectFileEvents: function() {
      this.selectFileBtn.on('click', function(event){
        $scope.fileUploader.selectInput.trigger('click');
      });
      this.selectInput.on("click", function(event){
        $scope.fileUploader._hideInvalidError();
        $(this).val(null);
      }).on("change",function(event){
        if($scope.fileUploader.selectInput[0].files[0]) {
          var self = this;
          $scope.fileUploader.fileObj = $scope.fileUploader.selectInput[0].files[0];
          // ファイルの内容は FileReader で読み込みます.
          $scope.showLoadingPopup($(self).parents('li.sinclo_re'));
          var fileReader = new FileReader();
          fileReader.onload = function (event) {
            $scope.hideLoadingPopup($(self).parents('li.sinclo_re'));
            if(!$scope.fileUploader._validExtension($scope.fileUploader.fileObj.name)) {
              $scope.$emit('onErrorSelectFile');
              return;
            }
            // event.target.result に読み込んだファイルの内容が入っています.
            // ドラッグ＆ドロップでファイルアップロードする場合は result の内容を Ajax でサーバに送信しましょう!
            $scope.fileUploader.loadData = event.target.result;
            // プレビュー表示前にエラーを消す
            $('#chatTalk').find('.deleteme').remove();
            $scope.showPreview(self, $scope.fileUploader.fileObj, $scope.fileUploader.loadData);
          };
          fileReader.readAsArrayBuffer($scope.fileUploader.fileObj);
        }
      });
    },
    _enterEvent: function(event) {
      $scope.fileUploader.dragging = true;
      $scope.fileUploader._cancelEvent(event);
      return false;
    },
    _overEvent: function(event) {
      $scope.fileUploader.dragging = false;
      $scope.fileUploader.droppable.css('opacity', '0.5');
      $scope.fileUploader._cancelEvent(event);
      return false;
    },
    _leaveEvent: function(event) {
      if($scope.fileUploader.dragging) {
        $scope.fileUploader.dragging = false;
      } else {
        $scope.fileUploader.droppable.css('opacity', '1.0');
      }
      $scope.fileUploader._cancelEvent(event);
      return false;
    },
    _handleDroppedFile: function(event) {
      $scope.fileUploader.droppable.css('display', 'none');
      $scope.fileUploader._hideInvalidError();
      // ファイルは複数ドロップされる可能性がありますが, ここでは 1 つ目のファイルを扱います.
      $scope.fileUploader.fileObj = event.originalEvent.dataTransfer.files[0];

      var self = this;
      // ファイルの内容は FileReader で読み込みます.
      var fileReader = new FileReader();
      $scope.showLoadingPopup($(self).parents('li.sinclo_re'));
      fileReader.onload = function(event) {
        $scope.hideLoadingPopup($(self).parents('li.sinclo_re'));
        if(!$scope.fileUploader._validExtension($scope.fileUploader.fileObj.name)) {
          $scope.$emit('onErrorSelectFile');
          return;
        }
        // event.target.result に読み込んだファイルの内容が入っています.
        // ドラッグ＆ドロップでファイルアップロードする場合は result の内容を Ajax でサーバに送信しましょう!
        $scope.fileUploader.loadData = event.target.result;
        $scope.showPreview(self, $scope.fileUploader.fileObj, $scope.fileUploader.loadData);
      }
      fileReader.readAsArrayBuffer($scope.fileUploader.fileObj);

      // デフォルトの処理をキャンセルします.
      $scope.fileUploader._cancelEvent(event);
      return false;
    },
    _cancelEvent: function(e) {
      e.preventDefault();
      e.stopPropagation();
    },
    _validExtension: function(filename) {
      var allowExtensions = this._getAllowExtension();

      var split = filename.split(".");
      var targetExtension = split[split.length-1];
      var regex = new RegExp(allowExtensions.join("|"), 'i');
      return regex.test(targetExtension);
    },
    _getAllowExtension: function() {
      var base = ["pdf","pptx","ppt","jpg","png","gif"];
      switch(Number($scope.extensionType)) {
        case 1:
          return base;
        case 2:
          var extendSettings = $scope.extendedExtensions;
          return base.concat(extendSettings);
        default:
          return base;
      }
    },
    _showInvalidError: function() {
      var span = document.createElement("span");
      span.classList.add('errorMsg');
      span.textContent = "指定のファイルは送信を許可されていません。";
      $("#sendMessageArea").append(span);
    },
    _hideInvalidError: function() {
      $('#sendMessageArea').find('span.errorMsg').remove();
    },
    _showConfirmDialog: function(message) {
      modalOpen.call(window, message, 'p-cus-file-upload', '確認', 'moment');
      popupEvent.closePopup = function() {
        $scope.uploadFile($scope.fileUploader.fileObj, $scope.fileUploader.loadData);
        popupEvent.close();
      };
    }
  };

  $scope.onClickSelectFileButton = function(event) {
    var targetInput = $(event.target).parents('div.selectFileArea').find('.receiveFileInput');
    if(targetInput.length === 1) {
      targetInput.off('change');
      var self = this;
      targetInput.val(null);
      targetInput.trigger('click');
      targetInput.on('change', function(e){
        var fileObj = this.files.item(0);
        var fileReader = new FileReader();

        fileReader.onload = function(fileEv) {
          if(!fileObj.name){
            return;
          }
          var loadData = fileEv.target.result;
          $scope.showPreview(self, fileObj, loadData);
        };
        fileReader.readAsArrayBuffer(fileObj);
      });
    }
  };

    $scope.showPreview = function(target, fileObj, loadData) {
    $scope.effectScene(false, $(target).parents('li.sinclo_re.recv_file_left').parent(), function(){
      // ベースとなる要素をクローン
      var divElm = document.querySelector('#chatTalk div > li.sinclo_se.recv_file_right').parentNode.cloneNode(true);
      var split = fileObj.name.split(".");
      var targetExtension = split[split.length-1];

      function afterDesideThumbnail(elm) {
        divElm.querySelector('li.sinclo_se.recv_file_right div.receiveFileContent p.preview').appendChild(elm);
        divElm.querySelector('li.sinclo_se.recv_file_right div.receiveFileContent div.selectFileArea p.commentarea').style.textAlign = 'center';
        divElm.querySelector('li.sinclo_se.recv_file_right div.actionButtonWrap a.cancel-file-button').addEventListener('click', function(e){
          $scope.effectScene(false, $(divElm), function(){
            document.getElementById('chatTalk').removeChild(divElm);
            $(target).parents('li.sinclo_re.recv_file_left').parent().show();
          });
        });
        divElm.querySelector('li.sinclo_se.recv_file_right div.actionButtonWrap a.send-file-button').addEventListener('click', function(e){
          var comment = divElm.querySelector('li.sinclo_se.recv_file_right div.receiveFileContent div.selectFileArea p.commentarea textarea').value;
          if(!comment) {
            comment = "（なし）";
          }
          $scope.showLoadingPopup(divElm);
          $scope.uploadFile(divElm, comment, fileObj, loadData);
        });
        // 要素を追加する
        document.getElementById('chatTalk').appendChild(divElm);
        $('#chatTalk > div:last-child').show();
        var targetTextarea = divElm.querySelector('li.sinclo_se.recv_file_right div.receiveFileContent div.selectFileArea p.commentarea textarea');
        $scope.changeResizableTextarea(targetTextarea);
        self.autoScroll();
        $scope.$apply();
      }

      if(SimulatorService.isImage(targetExtension)) {
        var imgElm = document.createElement('img');
        imgElm.classList.add($scope.selectPreviewImgClass());
        var fileReader = new FileReader();
        fileReader.onload = function(e) {
          imgElm.src = this.result;
          afterDesideThumbnail(imgElm);
        };
        fileReader.readAsDataURL(fileObj);
      } else {
        var iconElm = document.createElement('i');
        iconElm.classList.add('sinclo-fal');
        iconElm.classList.add('fa-4x');
        iconElm.classList.add(SimulatorService.selectIconClassFromExtension(targetExtension));
        iconElm.setAttribute("aria-hidden","true");
        afterDesideThumbnail(iconElm);
      }
    });
  };

  $scope.selectPreviewImgClass = function() {
    var widgetSizeType = Number($scope.simulatorSettings.widgetSizeTypeToggle);
    switch(widgetSizeType) {
      case 1:
        return 'small';
      case 2:
        return 'middle';
      case 3:
        return 'large';
      default:
        return 'middle';
    }
  };

  $scope.effectScene = function(isBack, jqObj, callback){
    if(isBack) {
      jqObj.fadeIn('fast', callback);
    } else {
      jqObj.fadeOut('fast', callback);
    }
  };

  $scope.showLoadingPopup = function(divElm) {
    $(divElm).find('div.receiveFileContent').find('div.loadingPopup').removeClass('hide');
  };

  $scope.hideLoadingPopup = function(divElm) {
    $(divElm).find('div.receiveFileContent').find('div.loadingPopup').addClass('hide');
  };

  $scope.changeResizableTextarea = function(elm) {
    var maxRow = 5;                       // 表示可能な最大行数
    var fontSize = parseFloat(elm.style.fontSize, 10);           // 行数計算のため、templateにて設定したフォントサイズを取得
    var borderSize = parseFloat(elm.style.borderWidth, 10) * 2;  // 行数計算のため、templateにて設定したボーダーサイズを取得(上下/左右)
    var paddingSize = parseFloat(elm.style.padding, 10) * 2;     // 表示高さの計算のため、templateにて設定したテキストエリア内の余白を取得(上下/左右)
    var lineHeight = parseFloat(elm.style.lineHeight, 10);       // 表示高さの計算のため、templateにて設定した行の高さを取得

    function autoResize() {
      console.log("autoResize");
      // テキストエリアの要素のサイズから、borderとpaddingを引いて文字入力可能なサイズを取得する
      var areaWidth = elm.getBoundingClientRect().width - borderSize - paddingSize;

      // フォントサイズとテキストエリアのサイズを基に、行数を計算する
      var textRow = 0;
      elm.value.split('\n').forEach(function(string) {
        var stringWidth = string.length * fontSize;
        textRow += Math.max(Math.ceil(stringWidth/areaWidth), 1);
      });

      // 表示する行数に応じて、テキストエリアの高さを調整する
      if (textRow > maxRow) {
        elm.style.height = (maxRow * (fontSize*lineHeight)) + paddingSize + 'px';
        elm.style.overflow = 'auto';
        self.autoScroll();
      } else {
        elm.style.height = (textRow * (fontSize*lineHeight)) + paddingSize + 'px';
        elm.style.overflow = 'hidden';
        self.autoScroll();
      }
    }

    autoResize();
    elm.addEventListener('input', autoResize);
  };

  // ファイル送信
  $scope.uploadFile = function(targetDivElm, comment, fileObj, loadFile) {
    var fd = new FormData();
    var blob = new Blob([loadFile], {type: fileObj.type});
    fd.append("k", "<?= $companyKey; ?>");
    fd.append("c", comment)
    fd.append("f", blob, fileObj.name);

    $.ajax({
      url  : "<?= $this->Html->url('/FC/pus') ?>",
      type : "POST",
      data : fd,
      cache       : false,
      contentType : false,
      processData : false,
      dataType    : "json",
      xhr : function(){
        var XHR = $.ajaxSettings.xhr();
        /*
        if(XHR.upload){
          XHR.upload.addEventListener('progress',function(e){
            $scope.uploadProgress = parseInt(e.loaded/e.total*10000)/100;
            console.log($scope.uploadProgress);
            if($scope.uploadProgress === 100) {
              $('#uploadMessage').css('display', 'none');
              $('#processingMessage').css('display', 'block');
            }
            $scope.$apply();
          }, false);
        }
        */
        return XHR;
      }
    })
      .done(function(data, textStatus, jqXHR){
        console.log(JSON.stringify(data));
        $scope.hideLoadingPopup(targetDivElm);
        $scope.effectScene(true, $(targetDivElm), function(){
          $(targetDivElm).find('li.sinclo_se').removeClass('recv_file_right').addClass('uploaded');
          var commentLabel = targetDivElm.querySelector('li.sinclo_se.uploaded div.receiveFileContent div.selectFileArea p.commentLabel');
          var commentArea = targetDivElm.querySelector('li.sinclo_se.uploaded div.receiveFileContent div.selectFileArea p.commentarea');
          var actionButtonWrap = targetDivElm.querySelector('li.sinclo_se.uploaded div.actionButtonWrap');
          commentArea.innerHTML = "";
          commentArea.style.textAlign = "left";
          actionButtonWrap.remove();
          commentLabel.innerHTML = "＜コメント＞";
          commentArea.innerHTML = data.comment;
          $scope.$emit('receiveVistorMessage', "");
        });
      })
      .fail(function(jqXHR, textStatus, errorThrown){
        alert("fail");
      });
  };

  /**
   * setPlaceholder
   * プレースホルダの設定
   * （サイト訪問者のメッセージ送信後に、プレースホルダの内容を戻す）
   * @param String message プレースホルダに設定するメッセージ
   */
  $scope.$on('setPlaceholder', function(event, message) {
    var elm = document.querySelector('#sincloChatMessage');
    var miniElm = document.querySelector('#miniSincloChatMessage');
    $scope.defaultPlaceholder = elm.placeholder;
    elm.placeholder = message;
    miniElm.placeholder = message;
  });

  /**
   * switchChatTextAreaDisplay
   * シミュレーションの自由入力エリアの表示状態を切り替える
   * @param Boolean status 自由入力エリアの表示状態(true: 表示, false: 非表示）
   */
  $scope.$on('switchSimulatorChatTextArea', function(event, status, uiType, isRequired) {
    var uiType = uiType || false;
    var isRequired = typeof isRequired !== 'undefined' ? isRequired : true;
    $scope.isRequired = isRequired;
    $scope.isTextAreaOpen = status;
    if ($scope.isTextAreaOpen) {
      $scope.isShowSkipBtn = !$scope.isRequired && !$('#miniSincloChatMessage').val() && !$('#sincloChatMessage').val();
    }
    $scope.isTextAreaDisabled = !isRequired && (uiType !== '1' && uiType !== '2') ? true : false;

    $timeout(function(){
      $scope.$apply();
    },0);
  });

  /**
   * 自由入力エリアの改行入力の許可状態を一時的に切り替える
   * （allowSendMessageByShiftEnterと同時に設定しないことを前提とする）
   * @param Boolean status 改行入力の許可状態
   */
  $scope.$on('allowInputLF', function(event, status, inputType) {
    console.log("$scope.$on('allowInputLF') inputType: %s", inputType);
    var _inputType = {
      "1": "text",
      "2": "number",
      "3": "email",
      "4": "tel"
    };
    $scope.allowInputLF = status === true;
    if($scope.allowInputLF) {
      $scope.hideMiniMessageArea();
    } else {
      $scope.showMiniMessageArea(_inputType[inputType]);
    }
    self.setPlaceholder('メッセージを入力してください');
  });

  /**
   * 自由入力エリアのメッセージ送信設定を一時的に切り替える
   * （allowInputLFと同時に設定しないことを前提とする）
   * @param Boolean status Shift+Enterでのメッセージ送信の許可状態
   */
  $scope.$on('allowSendMessageByShiftEnter', function(event, status, inputType) {
    console.log("allowSendMessageByShiftEnter");
    var _inputType = {
      "1": "text",
      "2": "number",
      "3": "email",
      "4": "tel"
    };
    $scope.allowSendMessageByShiftEnter = status === true;
    if($scope.allowSendMessageByShiftEnter) {
      $scope.hideMiniMessageArea();
    } else {
      $scope.showMiniMessageArea(_inputType[inputType]);
    }
    self.setPlaceholder('メッセージを入力してください\n（Enterで改行/Shift+Enterで送信）');
    $scope.$apply();
  });

  /**
   * setInputRule
   * 入力制限の設定
   * （サイト訪問者のメッセージ送信後に、状態を戻す）
   * @param Boolean rule 設定したい入力制限(正規表現)
   */
  $scope.$on('setInputRule', function(event, rule) {
    $scope.inputRule = rule;
  });

  /**
   * 改行ありのtextarea入力欄を非表示にし、改行不可のinput[type="*"]を表示する
   */
  $scope.showMiniMessageArea = function(inputType) {
    console.log("showMiniMessageArea");
    if($('#messageBox').is(':visible')) {
      var chatTalkElm = document.getElementById('chatTalk');
      var chatTalkHeight = chatTalkElm.getBoundingClientRect().height;
      document.getElementById('chatTalk').style.height = chatTalkHeight + 27 + "px";
    }
    $('#messageBox').addClass('sinclo-hide');
    $('#miniSincloChatMessage').get(0).type = inputType;
    $('#miniFlexBoxHeight').removeClass('sinclo-hide').find('#miniSincloChatMessage').focus();
    var msgBoxElm = document.getElementById('flexBoxWrap');
    msgBoxElm.dataset.originalHeight = 48;
  };

  /**
   * 改行ありのtextarea入力欄を表示し、改行不可のinput[type="*"]を非表示にする
   */
  $scope.hideMiniMessageArea = function() {
    console.log("hideMiniMessageArea");
    if($('#miniFlexBoxHeight').is(':visible')) {
      var chatTalkElm = document.getElementById('chatTalk');
      var chatTalkHeight = chatTalkElm.getBoundingClientRect().height;
      document.getElementById('chatTalk').style.height = chatTalkHeight - 27 + "px";
    }
    $('#miniFlexBoxHeight').addClass('sinclo-hide');
    $('#miniSincloChatMessage').get(0).type = 'text';
    $('#messageBox').removeClass('sinclo-hide').find('#sincloChatMessage').focus();
    var msgBoxElm = document.getElementById('flexBoxWrap');
    msgBoxElm.dataset.originalHeight = 75;
  };

  /**
   * isTextAreaOpen
   * showWidgetTypeを元に自由入力エリアの表示を切り替える
   */
  $scope.$watch('isTextAreaOpen', function() {
    $scope.setTextAreaOpenToggle();
  });
  $scope.setTextAreaOpenToggle = function() {
    console.log("setTextAreaOpenToggle");
    var msgBoxElm = document.getElementById('flexBoxWrap');
    var chatTalkElm = document.getElementById('chatTalk');
    if (msgBoxElm === null || chatTalkElm === null) {
      return;
    }

    var chatTalkHeight = chatTalkElm.getBoundingClientRect().height;
    var msgBoxHeight = msgBoxElm.getBoundingClientRect().height;

    // ウェブ接客コードが非表示な場合に、発生してしまう差分を埋める
    var offset = $scope.simulatorSettings.showWidgetType === 1 ? 0 : 3;

    console.log("msgBoxElm.dataset.originalHeight : %s",msgBoxElm.dataset.originalHeight);
    if ($scope.isTextAreaOpen) {
      console.log("SHOW");
      msgBoxElm.style.display = "";
      chatTalkElm.style.height = (chatTalkHeight - msgBoxElm.dataset.originalHeight + offset) + "px";
    } else {
      console.log("HIDE");
      msgBoxElm.style.display = "none";
      msgBoxElm.dataset.originalHeight = msgBoxHeight;
      chatTalkElm.style.height = (chatTalkHeight + msgBoxHeight - offset) + "px";
    }

    self.autoScroll();
  };

  //位置調整
  $scope.$watch(function(){
    return {'openFlg': $scope.simulatorSettings.openFlg, 'showWidgetType': $scope.simulatorSettings.showWidgetType, 'widgetSizeType': $scope.simulatorSettings.widgetSizeTypeToggle, 'chat_radio_behavior': $scope.simulatorSettings.settings['chat_radio_behavior'], 'chat_trigger': $scope.simulatorSettings.settings['chat_trigger'], 'show_name': $scope.simulatorSettings.settings['show_name'], 'widget.showTab': $scope.simulatorSettings.showTab};
  },
  function(){
    var main = document.getElementById("miniTarget");
    if ( !main ) return false;
    if ( $scope.simulatorSettings.openFlg ) {
      $timeout(function() {
        $scope.$apply();
      }).then(function() {
        angular.element("#sincloBox").addClass("open");
        var height = 0;
        for(var i = 0; main.children.length > i; i++){
          height += main.children[i].offsetHeight;
        }
        main.style.height = height + "px";

        console.log($scope.simulatorSettings.showWidgetType);

        var msgBox = document.getElementById('flexBoxWrap');
        if (msgBox != null && !$scope.isTextAreaOpen && msgBox.style.display !== 'none') {
          $scope.setTextAreaOpenToggle();
        } else
        if (msgBox != null && $scope.isTextAreaOpen && msgBox.style.display === 'none') {
          $scope.setTextAreaOpenToggle();
        }
      });
    }
    else {
      angular.element("#sincloBox").removeClass("open");
      main.style.height = "0";
    }
  }, true);

  /**
   * ウィジェットの表示タブ切替
   * @param Integer type 表示タイプ(1:通常, 2:スマートフォン(横), 3:スマートフォン(縦))
   */
  $scope.switchWidget = function(type) {
    var self = this;
    $scope.simulatorSettings.showWidgetType = type;
    var sincloBox = document.getElementById("sincloBox");

    // chatTalkの高さをリセットする(自由入力エリアの表示/非表示切替で設定が追加されるため)
    var chatTalkElm = document.getElementById('chatTalk');
    chatTalkElm.style.height = '';

    if ( Number(type) === 3 ) { // ｽﾏｰﾄﾌｫﾝ（縦）の表示
      $scope.simulatorSettings.showTab = 'chat'; // 強制でチャットにする
      $("#sincloBox ul#chatTalk li.boxType.chat_left").css('margin-right','17.5px');
    }

    if ( Number(type) === 1 ) { // 通常の表示
      $("#sincloBox ul#chatTalk li.boxType.chat_left").css('margin-right','');
    }

    if ( Number(type) !== 2 ) { // ｽﾏｰﾄﾌｫﾝ（横）以外は最大化する
      if(sincloBox){
        if(sincloBox.style.display == 'none'){
          sincloBox.style.display = 'block';
        }
      }
      /* ウィジェットが最小化されていたら最大化する */
      if ( !$scope.simulatorSettings.openFlg ) { // 最小化されている場合
        var main = document.getElementById("miniTarget");  // 非表示対象エリア
        var height = 0;
        if(main){
          for(var i = 0; main.children.length > i; i++){ // 非表示エリアのサイズを計測する
            if ( Number(type) === 3 && main.children[i].id === 'navigation' ) continue; // SPの場合はナビゲーションは基本表示しない
            height += main.children[i].offsetHeight;
          }
          main.style.height = height + "px";
        }
      }
    }
    if( Number(type) !== 4 ){
      if($scope.simulatorSettings.coreSettingsChat){
        document.getElementById("switch_widget").value = type;
      }
    }
    $scope.simulatorSettings.openFlg = true;

    // タブ切替後も、自由入力エリアの表示内容を保持する
    var textareaMessage = document.getElementById('sincloChatMessage').value;
    $timeout(function(){
      document.getElementById('sincloChatMessage').value = textareaMessage;
      // タブ切替の通知
      $scope.$emit('switchWidget', type);
      //画像がない場合
      if($('#mainImage').css('display') == 'none'　|| $('#mainImage').css('display') == undefined) {
        if($scope.simulatorSettings._settings.widget_title_top_type == 1) {
          $('#widgetTitle').css({'cssText': 'text-align: left !important;padding-left: 15px !important;'});
        }
        if($scope.simulatorSettings._settings.widget_title_top_type == 2) {
          $('#widgetTitle').css({'cssText': 'text-align: center !important;padding-left: 0px !important;padding-right: 0px !important;'});
        }
      }
      //画像がある場合
      else if($('#mainImage').css('display') == 'block') {
        if($scope.simulatorSettings._settings.widget_title_top_type == 1) {
          $('#widgetTitle').css({'cssText': 'text-align: left !important;padding-left: calc(2.5em + 43px)!important;'});
        }
        if($scope.simulatorSettings._settings.widget_title_top_type == 2) {
          $('#widgetTitle').css({'cssText': 'text-align: center !important;padding-left: calc(2.5em + 35px) !important;padding-right: 26px !important;'});
        }
      }
    },0);
  };

  $scope.currentWindowHeight = $(window).height();
  angular.element(window).on('load',function(e){
    $(window).on('resize', function(e){
      if($scope.simulatorSettings.showWidgetType === 1) {
        $scope.resizeWidgetHeightByWindowHeight();
      }
    });
    $scope.resizeWidgetHeightByWindowHeight();
  });

  $scope.resizeWidgetHeightByWindowHeight = function() {
    var windowHeight = $(window).innerHeight(),
      minCurrentWidgetHeight = $scope._getMinWidgetHeight(),
      currentWidgetHeight = $('#titleWrap').height() + $('#descriptionSet').height() + $('#miniTarget').height(),
      maxCurrentWidgetHeight = $scope._getMaxWidgetHeight(),
      delta = windowHeight - $scope.currentWindowHeight;

    if(windowHeight * 0.7 < currentWidgetHeight && delta === 0) {
      delta = (windowHeight * 0.7) - currentWidgetHeight;
    }

    // 変更後サイズ
    var afterWidgetHeight = currentWidgetHeight + delta;
    var changed = false;
    if(delta > 0 && afterWidgetHeight > maxCurrentWidgetHeight) {
      console.log('1 %s', delta);
      changed = true;
      $('#chatTalk').height($scope._getMaxChatTalkHeight());
    } else if(delta < 0 && afterWidgetHeight < minCurrentWidgetHeight) {
      console.log('2-1 %s ', delta, minCurrentWidgetHeight, $scope._getMinChatTalkHeight());
      changed = true;
      $('#chatTalk').height($scope._getMinChatTalkHeight());
      console.log('2-2 %s ', $('#sincloBox').height());
    } else if((delta < 0 && windowHeight * 0.7 < currentWidgetHeight) || (delta > 0 && windowHeight * 0.7 >= afterWidgetHeight)) {
      console.log('3 %s', delta);
      changed = true;
      $('#chatTalk').height($('#chatTalk').height() + delta);
    }
    $scope.currentWindowHeight = windowHeight;

    if(changed) {
      $(document).trigger('onWidgetSizeChanged');
    }
  };

  $scope._getMaxWidgetHeight = function() {
    var offset = $scope._getMessageAreaOffset();
    switch(Number($scope.widgetSizeType)) {
      case 1:
        return 405 - offset;
      case 2:
        return 496 - offset;
      case 3:
        return 596 - offset;
      default:
        return 496 - offset;
    }
  };

  $scope._getMinWidgetHeight = function() {
    var offset = $scope._getMessageAreaOffset();
    switch(Number($scope.widgetSizeType)) {
      case 1:
        return 318 - offset;
      case 2:
        return 364 - offset;
      case 3:
        return 409 - offset;
      default:
        return 364 - offset;
    }
  };

  $scope._getMaxChatTalkHeight = function() {
    var offset = $scope._getMessageAreaOffset(true);
    switch(Number($scope.widgetSizeType)) {
      case 1:
        // 小
        return 194 + offset;
      case 2:
        return 284 + offset;
      case 3:
        return 374 + offset;
      default:
        return 284 + offset;
    }
  };

  $scope._getMinChatTalkHeight = function() {
    var offset = $scope._getMessageAreaOffset(true);
    switch(Number($scope.widgetSizeType)) {
      case 1:
        // 小
        return 97 + offset;
      case 2:
        return 142+ offset;
      case 3:
        return 187 + offset;
      default:
        return 142 + offset;
    }
  };

  $scope._getMessageAreaOffset = function(forChatTalkOffset) {
    var invisibleUIOffset = 0;
    if(!forChatTalkOffset) {
      if(!$('#sincloAccessInfo').is(':visible')) {
        invisibleUIOffset += 26.5;
      }
      invisibleUIOffset +=  53 - $('#descriptionSet').height();
    }
    if(!$('#flexBoxWrap').is(':visible')) {
      // 非表示
      if(forChatTalkOffset) {
        return 75;
      } else {
        return 0 + invisibleUIOffset;
      }
    } else if($('#messageBox').is(':visible')) {
      return 0 + invisibleUIOffset;
    } else if($('#miniFlexBoxHeight').is(':visible')) {
      return 27 + invisibleUIOffset;
    } else {
      // とりあえず表示されている状態
      return 0 + invisibleUIOffset;
    }
  };

  /**
   * メッセージ追加後のスクロールアニメーション
   */
  this.autoScroll = function() {
    $timeout(function() {
      var target = $('#chatTalk');
      var paddingBottom = parseFloat($('#chatTalk').css('padding-bottom'));
      var lastMessageHeight = $("#chatTab ul").find("div:last").height();
      var chatTalk = document.getElementById('chatTalk');
      var time = 500;
      //ウィジェットサイズに合わせた余白で計算
      if(chatTalk.clientHeight > (lastMessageHeight + paddingBottom)) {
        target.stop().animate({
          scrollTop: target.get(0).scrollHeight - target.outerHeight()
        }, time);
      }
      else {
        target.stop().animate({
          scrollTop: (chatTalk.scrollHeight - (lastMessageHeight + paddingBottom))
        }, time);
      }
    }, 0);
  }

  /**
   * プレースホルダを設定する
   * @param String message プレースホルダに設定するメッセージ（指定がない場合は変更前に戻す）
   */
  this.setPlaceholder = function(message) {
    var elm = document.querySelector('#sincloChatMessage');
    var miniElm = document.querySelector('#miniSincloChatMessage');

    if (typeof message === 'undefined' || message == null) {
      elm.placeholder = $scope.placeholder || elm.placeholder;
      miniElm.placeholder = elm.placeholder;
    } else {
      $scope.placeholder = elm.placeholder;
      elm.placeholder = message;
      miniElm.placeholder = elm.placeholder;
    }
  }

  /**
   * 自由入力エリアのキーイベント
   */
  $(document).on('keypress', '#sincloChatMessage,#miniSincloChatMessage', function(e) {
    if (!$scope.allowInputLF && e.key === 'Enter') {
      // ヒアリング：改行不可（Enterキーでメッセージ送信）
      $scope.visitorSendMessage();
      return false;
    } else
    if ($scope.allowSendMessageByShiftEnter && e.key === 'Enter' && e.shiftKey) {
      // ヒアリング：改行可（Shift+Enterキーでメッセージ送信）
      $scope.visitorSendMessage();
      return false;
    }
  });
  /**
   * 自由入力エリアのテキスト入力イベント
   */
  $(document).on('input paste', '#sincloChatMessage,#miniSincloChatMessage', function(e) {
    var targetElm = $(this);
    var inputText = targetElm.val();
    // show skip button
    $scope.isShowSkipBtn = !(targetElm.val().length > 0);
    $scope.$apply();

    var regex = new RegExp($scope.inputRule);
    var changed = '';
    // 入力された文字列を改行ごとに分割し、設定された正規表現のルールに則っているかチェックする
    var isMatched = inputText.split(/\r\n|\n/).every(function(string) {
      var matchResult = string.match(regex);
      // 入力文字列が適切ではない場合、先頭から適切な文字列のみを取り出して処理を終了する
      if (matchResult === null || matchResult[0] !== matchResult.input) {
        changed += (matchResult === null || matchResult.index !== 0) ? '' : matchResult[0];
        return false;
      }
      changed += string + '\n';
      return true;
    });
    if (!isMatched) {
      targetElm.val(changed);
    }
  });

  // ダウンロード可能な吹き出しの背景色切替
  $(document).on('mouseenter', '#chatTalk .file_left', function(e){
    e.target.style.backgroundColor = $scope.simulatorSettings.makeFaintColor(0.9);
  }).on('mouseleave', '#chatTalk .file_left', function(e){
    e.target.style.backgroundColor = $scope.simulatorSettings.makeFaintColor();
  });

  $(document).on('click', '.sincloChatSkipBtn', function (e) {
    $scope.isShowSkipBtn = false;
    $scope.$apply();
    $scope.$emit('nextHearingAction');
  });
}]);

function chatBotTyping(){
  var widgetSizeType = getWidgetSettings().widget_size_type;
  var html  = "";
      html += "<div class='botNowDiv'>";
      html += "<li class='";
      if(getWidgetSettings().chat_message_with_animation == 1){
      html += "effect_left_wait ";
      }else{
      html += "effect_left_wait_none ";
      }
      //ウィジェットサイズが小で余白がない場合のみ、特殊なクラスを設ける
      if(widgetSizeType == 1 && $('#chatTalk').get(0).offsetHeight < $('#chatTalk').get(0).scrollHeight){
        html += "botDotOnlyTyping'>";
        //メインカラーが白の場合は、違うクラスになる
        if(getWidgetSettings().main_color != "#FFFFFF"){
          html += "  <div class='reload_only_dot_left'></div>";
          html += "  <div class='reload_only_dot_center'></div>";
          html += "  <div class='reload_only_dot_right'></div>";
        }else{
          html += "  <div class='reload_only_white_left'></div>";
          html += "  <div class='reload_only_white_center'></div>";
          html += "  <div class='reload_only_white_right'></div>";
        }
      }else{
        //ウィジェットサイズが大の場合
        if(widgetSizeType == 3 || widgetSizeType == 4){
          html += "botNowTypingLarge'>";
          //ウィジェットサイズが中の場合
        }else if(widgetSizeType == 2){
          html += "botNowTypingMedium'>";
          //ウィジェットサイズが小の場合
        }else if(widgetSizeType == 1){
          html += "botNowTypingSmall'>";
        }
        html += "    <div class='reload_dot_left'></div>";
        html += "    <div class='reload_dot_center'></div>";
        html += "    <div class='reload_dot_right'></div>";
      }
    html += "  </li>";
    html += "</div>";
  setTimeout(function(){
    $("#chatTalk").append(html)
  },800);
  return;
}

function chatBotTypingRemove(){
  $('div.botNowDiv').remove();
}

</script>
