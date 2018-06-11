var express = require('express');
var router = express.Router();
var database = require('./database');
var common = require('./module/common');

var getTriggerListSql  = "SELECT am.* FROM t_auto_messages AS am ";
    getTriggerListSql += " INNER JOIN (SELECT * FROM m_companies WHERE company_key = ? AND del_flg = 0 ) AS com  ON ( com.id = am.m_companies_id )";
    getTriggerListSql += " WHERE am.active_flg = 0 AND am.del_flg = 0 AND am.action_type IN (?,?);";

/* GET home page. */
router.get("/", function(req, res, next) {
    /* Cross-Origin */
    // http://stackoverflow.com/questions/18310394/no-access-control-allow-origin-node-apache-port-issue

    // Website you wish to allow to connect
    res.setHeader('Access-Control-Allow-Origin', '*');
    // Request methods you wish to allow
    res.setHeader('Access-Control-Allow-Methods', 'GET');
    // Request headers you wish to allow
    res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type');
    // Set to true if you need the website to include cookies in the requests sent
    // to the API (e.g. in case you use sessions)
    res.setHeader('Access-Control-Allow-Credentials', true);

    /* no-cache */
    // http://garafu.blogspot.jp/2013/06/ajax.html
    res.setHeader("Cache-Control", "no-cache");
    res.setHeader("Pragma", "no-cache");

    if (  !('query' in req) || (('query' in req) && !('sitekey' in req['query'])) ) {
        var err = new Error('Not Found');
        err.status = 404;
        next(err);
        return false;
    }
    var ip = '0.0.0.0';
    if ( req.get('x-forwarded-for') !== undefined ) {
      ip = req.get('x-forwarded-for');
    }
    var siteKey = req['query']['sitekey'];
    var accessType = req['query']['accessType'];
    var sendData = { status: true, widget: {},chat: {}, messages: {}, contract: {} };

    function isNumeric(str){
                var num = Number(str);
                if (isNaN(num)){
                  num = 0;
                }
                return num;
            }

    try {
      // IPアドレス制限
      if ( Number(accessType) === 1 && ('exclude_ips' in common.companySettings[siteKey]) && common.companySettings[siteKey].exclude_ips !== "" && common.companySettings[siteKey].exclude_ips !== null && common.companySettings[siteKey].exclude_ips !== undefined ) {
        var ips = common.companySettings[siteKey].exclude_ips.split("\r\n");
        for( var i in ips ){
          var range = getIpRange(ips[i]);
          var ipOfBinary = convertToBinaryNum(ip.split('.'));
          if ( range.min !== "" && range.max !== "" && range.min <= ipOfBinary && range.max >= ipOfBinary ) {
            sendData.status = false;
            res.send(sendData);
            return false;
          }
        }
      }
      if ( 'style_settings' in common.widgetSettings[siteKey] ) {
        if(common.companySettings[siteKey].trial_flg) {
          // トライアル終了日を確認
          var nowTimestamp = (new Date()).getTime(),
              trialEndDay = new Date(common.companySettings[siteKey].trial_end_day);
          // 正しくは23時59分59秒なのでセットする
          trialEndDay.setHours(23);
          trialEndDay.setMinutes(59);
          trialEndDay.setSeconds(59);
          if (nowTimestamp > trialEndDay.getTime()) {
            sendData.status = false;
            res.send(sendData);
            return false;
          }
        } else {
          // 契約終了日を確認
          var nowTimestamp = (new Date()).getTime(),
            agreementEndDay = new Date(common.companySettings[siteKey].agreement_end_day);
          // 正しくは23時59分59秒なのでセットする
          agreementEndDay.setHours(23);
          agreementEndDay.setMinutes(59);
          agreementEndDay.setSeconds(59);
          if (nowTimestamp > agreementEndDay.getTime()) {
            sendData.status = false;
            res.send(sendData);
            return false;
          }
        }
        var core_settings = common.companySettings[siteKey].core_settings;
        var settings = common.widgetSettings[siteKey].style_settings;
        sendData['contract'] = core_settings;
        // ウィジェット表示タイミング設定が存在しない場合は「常に表示する」
        var showTimingSetting = 4;
        if(('showTiming' in settings)) {
          showTimingSetting = isNumeric(settings.showTiming);
        }
        // （チャットプランのみ）Web接客コード設定が存在しない場合は「表示しない」
        var showAccessId = 2;
        if(('showAccessId' in settings)) {
          showAccessId = isNumeric(settings.showAccessId);
        }
        // 吹き出しデザイン設定が存在しない場合は「BOX型（サイズ固定）」
        var chatMessageDesignType = 1;
        if(('chatMessageDesignType' in settings)) {
          chatMessageDesignType = isNumeric(settings.chatMessageDesignType);
        }
        // メッセージ表示時アニメーション設定が存在しない場合は「アニメーション無効」
        var chatMessageWithAnimation = 0;
        if(('chatMessageWithAnimation' in settings)) {
          chatMessageWithAnimation = isNumeric(settings.chatMessageWithAnimation);
        }

        // チャット本文コピー設定が存在しない場合は「コピー可」
        var chatMessageCopy = 0;
        if(('chatMessageCopy' in settings)) {
          chatMessageCopy = isNumeric(settings.chatMessageCopy);
        }

        //閉じるボタン設定が存在しないときは「閉じるボタン無効」
        var closeButtonSetting = 1;
        if(('closeButtonSetting' in settings)) {
          closeButtonSetting = isNumeric(settings.closeButtonSetting);
        }

        // 通常設定・高度設定が存在しない場合は「通常設定」
        var colorSettingType = 0;
        if(('colorSettingType' in settings)) {
          colorSettingType = isNumeric(settings.colorSettingType);
        }
        // 吹き出し文字色が存在しない場合はデフォルト色を設定
        var messageTextColor = "#333333";
        if(('messageTextColor' in settings)) {
          messageTextColor = settings.messageTextColor;
        }
        // その他文字色が存在しない場合はデフォルト色を設定
        var otherTextColor = "#666666";
        if(('otherTextColor' in settings)) {
          otherTextColor = settings.otherTextColor;
        }
        // ウィジェット枠線色が存在しない場合はデフォルト色を設定
        var widgetBorderColor = "#E8E7E0";
        if(('widgetBorderColor' in settings)) {
          widgetBorderColor = settings.widgetBorderColor;
        }
        // 吹き出し枠線色が存在しない場合はデフォルト色を設定
        var chatTalkBorderColor = "#C9C9C9";
        if(('chatTalkBorderColor' in settings)) {
          chatTalkBorderColor = settings.chatTalkBorderColor;
        }
        // ヘッダー背景色が存在しない場合はデフォルト色を設定
        var headerBackgroundColor = "#FFFFFF";
        if(('headerBackgroundColor' in settings)) {
          headerBackgroundColor = settings.headerBackgroundColor;
        }

        var spMaximizeSizeType = 1; // 余白を残して表示
        if(('spMaximizeSizeType' in settings)) {
          spMaximizeSizeType = settings.spMaximizeSizeType;
        }

        var headerTextSize = 0;
        var seTextSize = 0;
        var reTextSize = 0;

        switch(isNumeric(settings.widgetSizeType)) {
          case 1:
            headerTextSize = 14;
            seTextSize = 12;
            reTextSize = 12;
            break;
          case 2:
          case 3:
            headerTextSize = 15;
            seTextSize = 13;
            reTextSize = 13;
            break;
        }

        if(('headerTextSize' in settings)) {
          headerTextSize = settings.headerTextSize;
        }

        if(('seTextSize' in settings)) {
          seTextSize = settings.seTextSize;
        }

        if(('reTextSize' in settings)) {
          reTextSize = settings.reTextSize;
        }

        // 自動メッセージの見出し設定が存在しない場合は「表示する」
        var showAutomessageName = 1;
        if(('showAutomessageName' in settings)) {
          showAutomessageName = settings.showAutomessageName;
        }

        // 有人メッセージの見出し設定が存在しない場合は「企業名を表示する」
        var showOpName = isNumeric(settings.showName) ? isNumeric(settings.showName) : 2; // 企業名を表示する
        if(('showOpName' in settings)) {
          showOpName = isNumeric(settings.showOpName);
        }

        var displayStyleType = 2; // 最小化
        if(('displayStyleType' in settings)) {
          displayStyleType = isNumeric(settings.displayStyleType);
        }

        sendData['widget'] = {
          showTiming: showTimingSetting,
          display_type: isNumeric(common.companySettings[siteKey].display_type),
          displayStyleType: displayStyleType,
          showTime: isNumeric(settings.showTime),
          showName: isNumeric(settings.showName), // 入室・退室時の表示
          showAutomessageName: isNumeric(showAutomessageName),
          showOpName: isNumeric(showOpName),
          showPosition: isNumeric(settings.showPosition),
          showAccessId: isNumeric(showAccessId),
          //ウィジットサイズ対応
          widgetSizeType: isNumeric(settings.widgetSizeType),
          //ウィジットサイズ対応
          title: settings.title,
          showSubtitle: isNumeric(settings.showSubtitle),
          subTitle: settings.subTitle,
          showDescription: isNumeric(settings.showDescription),
          description: settings.description,

          /* カラー設定styat */
          //0.通常設定・高度設定
          colorSettingType: colorSettingType,
          //1.メインカラー
          mainColor: settings.mainColor,
          //2.タイトル文字色
          stringColor: settings.stringColor,
          //3.吹き出し文字色
          messageTextColor: messageTextColor,
          //4.その他文字色
          otherTextColor: otherTextColor,
          headerTextSize: headerTextSize,
          //5.ウィジェット枠線色
          widgetBorderColor: widgetBorderColor,
          //6.吹き出し枠線色
          chatTalkBorderColor: chatTalkBorderColor,
          //ヘッダー背景色
          headerBackgroundColor: headerBackgroundColor,
          //7.企業名文字色
          subTitleTextColor: settings.subTitleTextColor,
          //8.説明文文字色
          descriptionTextColor: settings.descriptionTextColor,
          //9.チャットエリア背景色
          chatTalkBackgroundColor: settings.chatTalkBackgroundColor,
          //10.企業名担当者名文字色
          cNameTextColor: settings.cNameTextColor,
          //11.企業側吹き出し文字色
          reTextColor: settings.reTextColor,
          reTextSize: reTextSize,
          //12.企業側吹き出し背景色
          reBackgroundColor: settings.reBackgroundColor,
          //13.企業側吹き出し枠線色
          reBorderColor: settings.reBorderColor,
          //15.訪問者側吹き出し文字色
          seTextColor: settings.seTextColor,
          seTextSize: seTextSize,
          //16.訪問者側吹き出し背景色
          seBackgroundColor: settings.seBackgroundColor,
          //17.訪問者側吹き出し枠線色
          seBorderColor: settings.seBorderColor,
          //19.メッセージエリア背景色
          chatMessageBackgroundColor: settings.chatMessageBackgroundColor,
          //20.メッセージBOX文字色
          messageBoxTextColor: settings.messageBoxTextColor,
          //21.メッセージBOX背景色
          messageBoxBackgroundColor: settings.messageBoxBackgroundColor,
          //22.メッセージBOX枠線色
          messageBoxBorderColor: settings.messageBoxBorderColor,
          //24.送信ボタン文字色
          chatSendBtnTextColor: settings.chatSendBtnTextColor,
          //25.送信ボタン背景色
          chatSendBtnBackgroundColor: settings.chatSendBtnBackgroundColor,
          //26.ウィジット内枠線色
          widgetInsideBorderColor: settings.widgetInsideBorderColor,
          /* カラー設定end */

          showMainImage: settings.showMainImage,
          mainImage: settings.mainImage,
          chatRadioBehavior: isNumeric(settings.chatRadioBehavior),
          chatTrigger: isNumeric(settings.chatTrigger),
          chatMessageDesignType: chatMessageDesignType,
          chatMessageWithAnimation: chatMessageWithAnimation,
          chatTrigger: isNumeric(settings.chatTrigger),
          radiusRatio: isNumeric(settings.radiusRatio),
          //背景の影
          boxShadow: isNumeric(settings.boxShadow),
          //最小化時デザイン対応
          minimizeDesignType: isNumeric(settings.minimizeDesignType),
          //閉じるボタン start
          closeButtonSetting: closeButtonSetting,
          closeButtonModeType: isNumeric(settings.closeButtonModeType),
          bannertext: settings.bannertext,
          //閉じるボタン end
          spShowFlg: isNumeric(settings.spShowFlg),
          spHeaderLightFlg: isNumeric(settings.spHeaderLightFlg),
          spAutoOpenFlg: isNumeric(settings.spAutoOpenFlg),
          spMaximizeSizeType: isNumeric(spMaximizeSizeType),
          chatMessageCopy: chatMessageCopy
        };

        var actionTypeList = [];
        // ウィジェット表示設定
        switch(Number(sendData.widget.showTime)) {
          case 1: // サイト訪問時
            if (('maxShowTime' in settings) && settings['maxShowTime']) {
              sendData.widget['maxShowTime'] = settings['maxShowTime'];
            }
            break;
          case 2: // 常に最大化しない　※ 旧IF
            sendData.widget['displayStyleType'] = 2; // 最小化 sendDataには後続処理で入れる
            sendData.widget['showTime'] = 5; // 初期表示のままにする
            break;
          case 3: // 常に最大化する　※ 旧IF
            sendData.widget['maxShowTime'] = 0;
            sendData.widget['displayStyleType'] = 1; // 最大化 sendDataには後続処理で入れる
            sendData.widget['showTime'] = 5; // 初期表示のままにする
            break;
          case 4: // ページ訪問時
            if (('maxShowTimePage' in settings) && settings['maxShowTimePage']) {
              sendData.widget['maxShowTime'] = settings['maxShowTimePage'];
            }
            break;
          case 5: // 初期表示のままにする
            sendData.widget['maxShowTime'] = 0;
            break;
        }

        // ウィジェット表示タイミング
        if ( Number(sendData.widget.showTiming) === 1 ) { // サイト訪問時
          if (('maxShowTimingSite' in settings) && settings['maxShowTimingSite']) {
            sendData.widget['maxShowTimingSite'] = settings['maxShowTimingSite'];
          }
        }
        else if ( Number(sendData.widget.showTiming) === 2 ) { // ページ訪問時
          if (('maxShowTimingPage' in settings) && settings['maxShowTimingPage']) {
            sendData.widget['maxShowTimingPage'] = settings['maxShowTimingPage'];
          }
        }
        else if ( Number(sendData.widget.showTiming) === 3
               || Number(sendData.widget.showTiming) === 4
               || Number(sendData.widget.showTiming) === 5) { // 初回オートメッセージ受信 or 常に表示 or 小さなバナー表示
          sendData.widget['maxShowTimingSite'] = 0;
          sendData.widget['maxShowTimingPage'] = 0;
        }

        // チャット
        if (core_settings.hasOwnProperty('chat') && core_settings['chat']) {
          actionTypeList.push('1');
          actionTypeList.push('2');
        }

        // 画面同期
        if (core_settings.hasOwnProperty('synclo') && core_settings['synclo'] || core_settings.hasOwnProperty('document') && core_settings['document']) {
          sendData['widget']['tel'] = settings.tel;
          sendData['widget']['content'] = "";
          if ( typeof(settings.content) === "string" ) {
            sendData['widget']['content'] = settings.content.replace(/\r\n/g, '<br>');
          }
          sendData['widget']['time_text'] = settings.timeText;
          sendData['widget']['display_time_flg'] = isNumeric(settings.displayTimeFlg);
        }

          var now = new Date();
          var nowDay = now.getDay();
          var dateParse = Date.parse(now);
          var date = now.getFullYear() + "/" + ("0"+(now.getMonth()+1)).slice(-2) +'/'+ ("0"+(now.getDate())).slice(-2) + " ";
          var today = ("0"+(now.getMonth()+1)).slice(-2) +'/'+ ("0"+(now.getDate())).slice(-2);
          for(var i=0; i<common.autoMessageSettings[siteKey].length; i++){
            if ( !(common.autoMessageSettings[siteKey][i].trigger_type in sendData['messages']) ) {
              sendData['messages'] = [];
            }
            for(var i2=0; i2<common.operationHourSettings[siteKey].length; i2++){
              if(common.operationHourSettings[siteKey][i2].active_flg == 1 && JSON.parse(common.autoMessageSettings[siteKey][i].activity).conditions[10] != null) {
                var jsonData = JSON.parse(common.autoMessageSettings[siteKey][i].activity);
                if(common.operationHourSettings[siteKey][i2].type === 1) {
                  jsonData.conditions[10][0].everyday = JSON.parse(common.operationHourSettings[siteKey][i2].time_settings).everyday;
                  jsonData.conditions[10][0].publicHolidayConditions = JSON.parse(common.operationHourSettings[siteKey][i2].time_settings).everyday.pub;
                  jsonData.conditions[10][0].now = now;
                  jsonData.conditions[10][0].nowDay = nowDay;
                  jsonData.conditions[10][0].dateParse = dateParse;
                  jsonData.conditions[10][0].date = date;
                  jsonData.conditions[10][0].today = today;
                }
                else {
                  jsonData.conditions[10][0].weekly = JSON.parse(common.operationHourSettings[siteKey][i2].time_settings).weekly;
                  jsonData.conditions[10][0].publicHolidayConditions = JSON.parse(common.operationHourSettings[siteKey][i2].time_settings).weekly.weekpub;
                  jsonData.conditions[10][0].now = now;
                  jsonData.conditions[10][0].nowDay = nowDay;
                  jsonData.conditions[10][0].dateParse = dateParse;
                  jsonData.conditions[10][0].date = date;
                  jsonData.conditions[10][0].today = today;
                }
                jsonData.conditions[10][0].publicHoliday = common.publicHolidaySettings[now.getFullYear()];
                jsonData.conditions[10][0].type = common.operationHourSettings[siteKey][i2].type;
                common.autoMessageSettings[siteKey][i].activity = JSON.stringify(jsonData);
              }
            }
            // ページ、参照元URL、発言内容、最初に訪れたページ、前のページの旧IF対応
            var activityObj = JSON.parse(common.autoMessageSettings[siteKey][i].activity),
                conditions = activityObj.conditions;
            Object.keys(conditions).forEach(function(index, elm, arr){
              if(index === "3") { // ページ
                var array = [],
                  condition = conditions[index];
                Object.keys(condition).forEach(function(key, value, arr2){
                  if(typeof(condition[value]['keyword']) !== 'undefined') {
                    var newSettings = {
                      targetName: 1,
                      keyword_contains: "",
                      keyword_contains_type: "1",
                      keyword_exclusions: "",
                      keyword_exclusions_type: "1",
                      stayPageCond: 1
                    };
                    newSettings.targetName = condition[value].targetName;
                    switch(Number(condition[value].stayPageCond)) {
                      case 1:
                        newSettings.keyword_contains = condition[value].keyword;
                        newSettings.stayPageCond = 1;
                        break;
                      case 2:
                        newSettings.keyword_contains = condition[value].keyword;
                        newSettings.stayPageCond = 2;
                        break;
                      case 3:
                        newSettings.keyword_exclusions = condition[value].keyword;
                        newSettings.stayPageCond = 1;
                        break;
                    }
                    array.push(newSettings);
                  } else {
                    array.push(condition[value]);
                  }
                });
                conditions[index] = array;
              }
              if(index === "5") { // 参照元URL
                var array = [],
                  condition = conditions[index];
                Object.keys(condition).forEach(function(key, value, arr2){
                  if(typeof(condition[value]['keyword']) !== 'undefined') {
                    var newSettings = {
                      keyword_contains: "",
                      keyword_contains_type: "1",
                      keyword_exclusions: "",
                      keyword_exclusions_type: "1",
                      referrerCond: 2
                    };
                    switch(Number(condition[value].referrerCond)) {
                      case 1:
                        newSettings.keyword_contains = condition[value].keyword;
                        newSettings.referrerCond = 1;
                        break;
                      case 2:
                        newSettings.keyword_contains = condition[value].keyword;
                        newSettings.referrerCond = 2;
                        break;
                      case 3:
                        newSettings.keyword_exclusions = condition[value].keyword;
                        newSettings.referrerCond = 1;
                        break;
                    }
                    array.push(newSettings);
                  } else {
                    array.push(condition[value]);
                  }
                });
                conditions[index] = array;
              }
              if(index === "7") { // 発言内容
                var array = [],
                  condition = conditions[index];
                Object.keys(condition).forEach(function(key, value, arr2){
                  if(typeof(condition[value]['speechContent']) !== 'undefined') {
                    var newSettings = {
                      keyword_contains: "",
                      keyword_contains_type: "1",
                      keyword_exclusions: "",
                      keyword_exclusions_type: "1",
                      speechContentCond: "1",
                      triggerTimeSec: 3,
                      speechTriggerCond: "1"
                    };
                    newSettings.speechContentCond = condition[value].speechContentCond;
                    newSettings.triggerTimeSec = condition[value].triggerTimeSec;
                    newSettings.speechTriggerCond = condition[value].speechTriggerCond;
                    switch(Number(condition[value].speechContentCond)) {
                      case 1:
                        newSettings.keyword_contains = condition[value].speechContent;
                        newSettings.speechContentCond = 1;
                        break;
                      case 2:
                        newSettings.keyword_contains = condition[value].speechContent;
                        newSettings.speechContentCond = 2;
                        break;
                      case 3:
                        newSettings.keyword_exclusions = condition[value].speechContent;
                        newSettings.speechContentCond = 1;
                        break;
                    }
                    array.push(newSettings);
                  } else {
                    array.push(condition[value]);
                  }
                });
                conditions[index] = array;
              }
              if(index === "8") { // 最初に訪れたページ
                var array = [],
                  condition = conditions[index];
                Object.keys(condition).forEach(function(key, value, arr2){
                  if(typeof(condition[value]['keyword']) !== 'undefined') {
                    var newSettings = {
                      targetName: 1,
                      keyword_contains: "",
                      keyword_contains_type: "1",
                      keyword_exclusions: "",
                      keyword_exclusions_type: "1",
                      stayPageCond: 1
                    };
                    newSettings.targetName = condition[value].targetName;
                    switch(Number(condition[value].stayPageCond)) {
                      case 1:
                        newSettings.keyword_contains = condition[value].keyword;
                        newSettings.stayPageCond = 1;
                        break;
                      case 2:
                        newSettings.keyword_contains = condition[value].keyword;
                        newSettings.stayPageCond = 2;
                        break;
                      case 3:
                        newSettings.keyword_exclusions = condition[value].keyword;
                        newSettings.stayPageCond = 1;
                        break;
                    }
                    array.push(newSettings);
                  } else {
                    array.push(condition[value]);
                  }
                });
                conditions[index] = array;
              }
              if(index === "9") { // 前のページ
                var array = [],
                  condition = conditions[index];
                Object.keys(condition).forEach(function(key, value, arr2){
                  if(typeof(condition[value]['keyword']) !== 'undefined') {
                    var newSettings = {
                      targetName: 1,
                      keyword_contains: "",
                      keyword_contains_type: "1",
                      keyword_exclusions: "",
                      keyword_exclusions_type: "1",
                      stayPageCond: 1
                    };
                    newSettings.targetName = condition[value].targetName;
                    switch(Number(condition[value].stayPageCond)) {
                      case 1:
                        newSettings.keyword_contains = condition[value].keyword;
                        newSettings.stayPageCond = 1;
                        break;
                      case 2:
                        newSettings.keyword_contains = condition[value].keyword;
                        newSettings.stayPageCond = 2;
                        break;
                      case 3:
                        newSettings.keyword_exclusions = condition[value].keyword;
                        newSettings.stayPageCond = 1;
                        break;
                    }
                    array.push(newSettings);
                  } else {
                    array.push(condition[value]);
                  }
                });
                conditions[index] = array;
              }
            });
            activityObj.conditions = conditions;
            sendData['messages'].push({
              "id": common.autoMessageSettings[siteKey][i].id,
              "sitekey": siteKey,
              "activity": activityObj,
              "action_type": isNumeric(common.autoMessageSettings[siteKey][i].action_type),
              "send_mail_flg": isNumeric(common.autoMessageSettings[siteKey][i].send_mail_flg),
              "scenario_id": isNumeric(common.autoMessageSettings[siteKey][i].t_chatbot_scenario_id)
            });
          }
          sendData.chat['in_flg'] = common.chatSettings[siteKey].in_flg;
          sendData.chat['initial_notification_message'] = common.chatSettings[siteKey].initial_notification_message;
          res.send(sendData);
      }
      else {
        var err = new Error(' Service Unavailable');
        err.status = 503;
        next(err);
        return false;
      }
    } catch (e) {
        var err = new Error(' Service Unavailable');
        err.status = 503;
        next(err);
        return false;
    }
    // res.render('index', { title: 'Settings' });
});

router.post("/reload/widgetSettings", function(req, res, next) {

  /* Cross-Origin */
  // http://stackoverflow.com/questions/18310394/no-access-control-allow-origin-node-apache-port-issue

  // Website you wish to allow to connect
  res.setHeader('Access-Control-Allow-Origin', '*');
  // Request methods you wish to allow
  res.setHeader('Access-Control-Allow-Methods', 'POST');
  // Request headers you wish to allow
  res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type');
  // Set to true if you need the website to include cookies in the requests sent
  // to the API (e.g. in case you use sessions)
  res.setHeader('Access-Control-Allow-Credentials', true);

  /* no-cache */
  // http://garafu.blogspot.jp/2013/06/ajax.html
  res.setHeader("Cache-Control", "no-cache");
  res.setHeader("Pragma", "no-cache");

  try {
    if(req.get('host').indexOf('127.0.0.1') === -1) {
      throw new ReferenceError('想定したURLでのコールではありません');
    }
    if (  !('body' in req) || (('body' in req) && !('sitekey' in req['body'])) ) {
      throw new Error('Forbidden');
    }
    common.reloadWidgetSettings(req['body']['sitekey']);
  } catch (e) {
    var err = new Error(' Service Unavailable');
    err.status = 503;
    next(err);
    return false;
  }
  // res.render('index', { title: 'Settings' });
});

router.post("/reload/autoMessages", function(req, res, next) {

  /* Cross-Origin */
  // http://stackoverflow.com/questions/18310394/no-access-control-allow-origin-node-apache-port-issue

  // Website you wish to allow to connect
  res.setHeader('Access-Control-Allow-Origin', '*');
  // Request methods you wish to allow
  res.setHeader('Access-Control-Allow-Methods', 'POST');
  // Request headers you wish to allow
  res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type');
  // Set to true if you need the website to include cookies in the requests sent
  // to the API (e.g. in case you use sessions)
  res.setHeader('Access-Control-Allow-Credentials', true);

  /* no-cache */
  // http://garafu.blogspot.jp/2013/06/ajax.html
  res.setHeader("Cache-Control", "no-cache");
  res.setHeader("Pragma", "no-cache");

  try {
    if(req.get('host').indexOf('127.0.0.1') === -1) {
      throw new ReferenceError('想定したURLでのコールではありません');
    }
    if (  !('body' in req) || (('body' in req) && !('sitekey' in req['body'])) ) {
      throw new Error('Forbidden');
    }
    common.reloadAutoMessageSettings(req['body']['sitekey']);
  } catch (e) {
    var err = new Error(' Service Unavailable');
    err.status = 503;
    next(err);
    return false;
  }
  // res.render('index', { title: 'Settings' });
});

router.post("/reload/operationHour", function(req, res, next) {

  /* Cross-Origin */
  // http://stackoverflow.com/questions/18310394/no-access-control-allow-origin-node-apache-port-issue

  // Website you wish to allow to connect
  res.setHeader('Access-Control-Allow-Origin', '*');
  // Request methods you wish to allow
  res.setHeader('Access-Control-Allow-Methods', 'POST');
  // Request headers you wish to allow
  res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type');
  // Set to true if you need the website to include cookies in the requests sent
  // to the API (e.g. in case you use sessions)
  res.setHeader('Access-Control-Allow-Credentials', true);

  /* no-cache */
  // http://garafu.blogspot.jp/2013/06/ajax.html
  res.setHeader("Cache-Control", "no-cache");
  res.setHeader("Pragma", "no-cache");

  try {
    if(req.get('host').indexOf('127.0.0.1') === -1) {
      throw new ReferenceError('想定したURLでのコールではありません');
    }
    if (  !('body' in req) || (('body' in req) && !('sitekey' in req['body'])) ) {
      throw new Error('Forbidden');
    }
    common.reloadOperationHourSettings(req['body']['sitekey']);
  } catch (e) {
    var err = new Error(' Service Unavailable');
    err.status = 503;
    next(err);
    return false;
  }
  // res.render('index', { title: 'Settings' });
});

router.post("/reload/chatSettings", function(req, res, next) {

  /* Cross-Origin */
  // http://stackoverflow.com/questions/18310394/no-access-control-allow-origin-node-apache-port-issue

  // Website you wish to allow to connect
  res.setHeader('Access-Control-Allow-Origin', '*');
  // Request methods you wish to allow
  res.setHeader('Access-Control-Allow-Methods', 'POST');
  // Request headers you wish to allow
  res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type');
  // Set to true if you need the website to include cookies in the requests sent
  // to the API (e.g. in case you use sessions)
  res.setHeader('Access-Control-Allow-Credentials', true);

  /* no-cache */
  // http://garafu.blogspot.jp/2013/06/ajax.html
  res.setHeader("Cache-Control", "no-cache");
  res.setHeader("Pragma", "no-cache");

  try {
    if(req.get('host').indexOf('127.0.0.1') === -1) {
      throw new ReferenceError('想定したURLでのコールではありません');
    }
    if (  !('body' in req) || (('body' in req) && !('sitekey' in req['body'])) ) {
      throw new Error('Forbidden');
    }
    common.reloadChatSettings(req['body']['sitekey']);
  } catch (e) {
    var err = new Error(' Service Unavailable');
    err.status = 503;
    next(err);
    return false;
  }
  // res.render('index', { title: 'Settings' });
});

/**
 * 10進数表記ののIPアドレスを2進数に変換
 * @params array sample: ['127', '0', '0', '0']
 * @return string sample: 01111111000000000000000000000000
 **/
function convertToBinaryNum(group){
  var ret = "";
  for (var i = 0; i < 4; i++) {
    var bit = "00000000" + parseInt(group[i], 10).toString(2);
    ret += bit.slice(-8);
  }
  return ret;
}

/**
 * 2進数表記ののIPアドレスを10進数に変換
 * @params array sample: 01111111000000000000000000000000
 * @return string sample: 127.0.0.1
 **/
function convertToIp(num){
  var ret = "";
  ret = parseInt(num.slice(0,8), 2) + ".";
  ret += parseInt(num.slice(8,16), 2) + ".";
  ret += parseInt(num.slice(16,24), 2) + ".";
  ret += parseInt(num.slice(24,32), 2);
  return ret;
}

/**
 * IPアドレスの範囲を取得
 **/
function getIpRange(ipAddress){
  var ip = ipAddress.split('/'),
      group = ip[0].split('.'),
      ipBit = "",
      minIpBit = "",
      maxIpBit = "",
      maxAddress = ip[0];

  // 入力値が空、IPアドレスをカンマ基準で配列にした際に４つじゃない場合
  // ネットワークのビット数が規定の数値(1~32)じゃない場合
  if ( ip === "" || group.length !== 4 ||
      (ip.length === 2 && String(ip[1]).match(/^([1-9]|[1-2][0-9]|3[0-2])$/) === null ) ) {
    return {min:'', max: ''}; // 空を返す
  }

  // 入力されたIPアドレスを2進数表記に変換し保持
  minIpBit = convertToBinaryNum(group);

  // IPアドレスのみの場合
  if ( ip.length === 1 ) {
    return {min: minIpBit, max: minIpBit};
  }
  for (var i = 0; i < 4; i++) {
    var bit = parseInt(group[i], 10).toString(2);
    if ( Number(ip[1]) >= ((i+1)*8) ) {
      ipBit += ("00000000" + bit).slice(-8);
    }
    else {
      var tmpIpBit = ("00000000" + bit).slice(-8);
      ipBit += (tmpIpBit.slice(0, Number(ip[1]) - (i*8)) + "11111111").slice(0, 8);
      break;
    }
  }
  maxIpBit = (ipBit + "11111111111111111111111111111111").slice(0, 32);

  return {min: minIpBit, max: maxIpBit};
}

module.exports = router;