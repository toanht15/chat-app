'use strict';

const express = require('express');
const router = express.Router();
const uuid = require('node-uuid');
const CommonUtil = require('./module/class/util/common_utility');
const SharedData = require('./module/shared_data');
const SCChecker = require('./module/class/checker/SCChecker');

router.options('/*', function(req, res) {
  // Website you wish to allow to connect
  res.setHeader('Access-Control-Allow-Origin', '*');
  // Request methods you wish to allow
  res.setHeader('Access-Control-Allow-Methods', 'POST');
  // Request headers you wish to allow
  res.setHeader('Access-Control-Allow-Headers',
      'X-Requested-With,Content-Type');
  // Set to true if you need the website to include cookies in the requests sent
  // to the API (e.g. in case you use sessions)
  res.setHeader('Access-Control-Allow-Credentials', true);

  res.sendStatus(200);
});

router.post('/auth/customer', function(req, res, next) {

  /* Cross-Origin */
  // http://stackoverflow.com/questions/18310394/no-access-control-allow-origin-node-apache-port-issue

  // Website you wish to allow to connect
  res.setHeader('Access-Control-Allow-Origin', '*');
  // Request methods you wish to allow
  res.setHeader('Access-Control-Allow-Methods', 'POST');
  // Request headers you wish to allow
  res.setHeader('Access-Control-Allow-Headers',
      'X-Requested-With,Content-Type');
  // Set to true if you need the website to include cookies in the requests sent
  // to the API (e.g. in case you use sessions)
  res.setHeader('Access-Control-Allow-Credentials', true);

  /* no-cache */
  // http://garafu.blogspot.jp/2013/06/ajax.html
  res.setHeader('Cache-Control', 'no-cache');
  res.setHeader('Pragma', 'no-cache');

  let d = {
    siteKey: '',
    type: 'customer',
    tabId: '',
    sincloSessionId: '',
    token: '',
    data: {
      userId: '',
      accessId: '',
      firstConnection: true,
      forceFirstConnect: false,
      inactiveReconnect: false
    }
  };
  let reqData = req.body;
  d = Object.assign(d, reqData);

  let send = d.data;

  if (d.type !== 'admin') {
    if (CommonUtil.isKeyExists(d, d.siteKey + '.' + d.tabId)
        && !CommonUtil.isKeyExists(d,
            d.siteKey + '.' + d.tabId + '.timeoutTimer')) {
      console.log('tabId is duplicate. change firstConnection flg' + d.tabId);
      d.data.firstConnection = true;
    }
  }

  if (CommonUtil.isset(d.tabId)
      &&
      CommonUtil.isKeyExists(d, d.siteKey + '.' + d.tabId + '.timeoutTimer')) {
    const currentSincloSessionId = SharedData.sincloCore[d.siteKey][d.tabId].sincloSessionId;
    if (currentSincloSessionId) {
      const oldSessionId = SharedData.sincloCore[d.siteKey][d.tabId].sessionId;
      let sincloSession = SharedData.sincloCore[d.siteKey][currentSincloSessionId];
      if (CommonUtil.isset(sincloSession)) {
        let sessionIds = sincloSession.sessionIds;
        delete sessionIds[oldSessionId];
        if (currentSincloSessionId !== d.sincloSessionId &&
            Object.keys(sessionIds).length === 0) {
          delete SharedData.sincloCore[d.siteKey][currentSincloSessionId];
        }
      } else {
        console.log('currentSincloSession : ' + currentSincloSessionId +
            ' is null.');
      }
    }
  }

  if (!CommonUtil.isset(d.userId)) {
    send.userId - CommonUtil.makeUserId();
  }

  if (d.data.forceFirstConnect
      || (!d.data.inactiveReconnect
          && (!CommonUtil.isset(d.sincloSessionId)
              || !CommonUtil.isKeyExists(SharedData.sincloCore,
                  d.siteKey + '.' + d.sincloSessionId)
              || !CommonUtil.isKeyExists(d,
                  d.siteKey + '.' + d.sincloSessionId + '.sessionIds')
          )
      )
  ) {
    send.sincloSessionId = uuid.v4;
    send.sincloSessionIdIsNew = true;
    d.data.firstConnection = true;
  } else {
    send.sincloSessionIdIsnew = false;
  }

  if (d.data.firstConnection || !CommonUtil.isset(d.data.accessId)) {
    send.accessId = CommonUtil.makeAccessId();
  }

  if (!CommonUtil.isset(d.token)) {
    send.token = d.token;
  }

  // ページ表示開始時間
  let datetime = new Date();
  send.pagetime = Date.parse(datetime);

  // アクセス開始フラグ
  if (d.data.firstConnection) {
    send.time = send.pagetime;
  }

  send.ipAddress = req.headers['x-forwarded-for'] ||
      req.connection.remoteAddress;

  if (d.siteKey) {
    // socket.joinは別途やる
    const checker = new SCChecker();
    checker.widgetCheck(d, function(ret) {
      send.activeOperatorCnt = checker.getOperatorCnt(d.siteKey);
      send.widget = ret.opFlg;
      send.opFlg = true;
      send.inactiveReconnect = Boolean(d.data.inactiveReconnect);
      if (ret.opFlg === false) {
        send.opFlg = false;
        send.opFlg = false;
        if (CommonUtil.isKeyExists(res, 'tabId')
            && CommonUtil.isKeyExists(SharedData.sincloCore,
                d.siteKey + '.' + d.tabId + '.' + 'chat')) {
          send.opFlg = true;
        }
      }
      // socket.joinは後でやる
      res.json(send);
    });
  } else {
    res.status(400);
    res.json({result: false});
  }
});

router.post('/auth/info', function(req, res, next) {

  /* Cross-Origin */
  // http://stackoverflow.com/questions/18310394/no-access-control-allow-origin-node-apache-port-issue

  // Website you wish to allow to connect
  res.setHeader('Access-Control-Allow-Origin', '*');
  // Request methods you wish to allow
  res.setHeader('Access-Control-Allow-Methods', 'POST');
  // Request headers you wish to allow
  res.setHeader('Access-Control-Allow-Headers',
      'X-Requested-With,content-type');
  // Set to true if you need the website to include cookies in the requests sent
  // to the API (e.g. in case you use sessions)
  res.setHeader('Access-Control-Allow-Credentials', true);

  /* no-cache */
  // http://garafu.blogspot.jp/2013/06/ajax.html
  res.setHeader('Cache-Control', 'no-cache');
  res.setHeader('Pragma', 'no-cache');

  let d = {
    siteKey: '',
    type: 'user',
    tabId: '',
    sincloSessionId: '',
    token: '',
    accessId: '',
    chat: null,
    url: '',
    connectToken: '',
    customVariables: '',
    confirm: false,
    widget: false,
    prevList: [],
    userAgent: '',
    time: 0,
    ipAddress: '',
    referrer: ''
  };

  let reqData = req.body;

  d = Object.assign(d, reqData);

});

module.exports = router;