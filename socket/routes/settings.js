var express = require('express');
var router = express.Router();
var database = require('./database');

// mysql
var mysql = require('mysql'),
    pool = mysql.createPool({
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASS || 'password',
    database: process.env.DB_NAME || 'sinclo_db'
});

var getWidgetSettingSql  = "SELECT ws.*, com.core_settings, com.exclude_ips FROM m_widget_settings AS ws";
    getWidgetSettingSql += " INNER JOIN (SELECT * FROM m_companies WHERE company_key = ? AND del_flg = 0 ) AS com  ON ( com.id = ws.m_companies_id )";
    getWidgetSettingSql += " WHERE ws.del_flg = 0 ORDER BY id DESC LIMIT 1;";

var getTriggerListSql  = "SELECT am.* FROM t_auto_messages AS am ";
    getTriggerListSql += " INNER JOIN (SELECT * FROM m_companies WHERE company_key = ? AND del_flg = 0 ) AS com  ON ( com.id = am.m_companies_id )";
    getTriggerListSql += " WHERE am.active_flg = 0 AND am.del_flg = 0 AND am.action_type IN (?);";

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
    var sendData = { status: true, widget: {}, messages: {}, contract: {}};
    pool.query(getWidgetSettingSql, siteKey,
        function(err, rows){
            function isNumeric(str){
                var num = Number(str);
                if (isNaN(num)){
                  num = 0;
                }
                return num;
            }

            try {
              // IPアドレス制限
              if ( Number(accessType) === 1 && rows.length > 0 && ('exclude_ips' in rows[0]) && rows[0].exclude_ips !== "" && rows[0].exclude_ips !== null && rows[0].exclude_ips !== undefined ) {
                var ips = rows[0].exclude_ips.split("\r\n");
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
              if ( rows.length > 0 && 'style_settings' in rows[0] ) {
                var core_settings = JSON.parse(rows[0].core_settings);
                var settings = JSON.parse(rows[0].style_settings);
                sendData['contract'] = core_settings;
                // ウィジェット表示タイミング設定が存在しない場合は「常に表示する」
                var showTimingSetting = 4;
                if(('showTiming' in settings)) {
                  showTimingSetting = isNumeric(settings.showTiming);
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

                sendData['widget'] = {
                  showTiming: showTimingSetting,
                  display_type: isNumeric(rows[0].display_type),
                  showTime: isNumeric(settings.showTime),
                  showName: isNumeric(settings.showName),
                  showPosition: isNumeric(settings.showPosition),
                  //ウィジットサイズ対応
                  widgetSizeType: isNumeric(settings.widgetSizeType),
                  //ウィジットサイズ対応
                  title: settings.title,
                  showSubtitle: isNumeric(settings.showSubtitle),
                  subTitle: settings.subTitle,
                  showDescription: isNumeric(settings.showDescription),
                  description: settings.description,
                  mainColor: settings.mainColor,
                  stringColor: settings.stringColor,
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
                  closeButtonSetting: isNumeric(settings.closeButtonSetting),
                  closeButtonModeType: isNumeric(settings.closeButtonModeType),
                  bannertext: isNumeric(settings.bannertext),
                  //閉じるボタン end
                  spShowFlg: isNumeric(settings.spShowFlg),
                  //最大時のシンプル表示
                  spHeaderLightFlg: isNumeric(settings.spHeaderLightFlg),
                  spAutoOpenFlg: isNumeric(settings.spAutoOpenFlg)
                };

                actionTypeList = [];
                // ウィジェット表示設定
                if ( Number(sendData.widget.showTime) === 1 ) { // サイト訪問時
                  if (('maxShowTime' in settings) && settings['maxShowTime']) {
                    sendData.widget['maxShowTime'] = settings['maxShowTime'];
                  }
                }
                else if ( Number(sendData.widget.showTime) === 4 ) { // ページ訪問時
                  if (('maxShowTimePage' in settings) && settings['maxShowTimePage']) {
                    sendData.widget['maxShowTime'] = settings['maxShowTimePage'];
                  }
                }
                else if ( Number(sendData.widget.showTime) === 3 ) { // 常に最大化
                  sendData.widget['maxShowTime'] = 0;
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
                       || Number(sendData.widget.showTiming) === 4 ) { // 初回オートメッセージ受信 or 常に表示
                  sendData.widget['maxShowTimingSite'] = 0;
                  sendData.widget['maxShowTimingPage'] = 0;
                }

                // チャット
                if (core_settings.hasOwnProperty('chat') && core_settings['chat']) {
                  actionTypeList.push('1');
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

                pool.query(getTriggerListSql, [siteKey, actionTypeList.join(",")],
                  function(err, rows){
                    for(var i=0; i<rows.length; i++){
                      if ( !(rows[i].trigger_type in sendData['messages']) ) {
                          sendData['messages'] = [];
                      }
                      sendData['messages'].push({
                        "id": rows[i].id,
                        "sitekey": siteKey,
                        "activity": JSON.parse(rows[i].activity),
                        "action_type": isNumeric(rows[i].action_type),
                      });
                    }
                    res.send(sendData);
                  }
                );
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
        }
    );

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
