'use strict';

const express = require('express');
const router = express.Router();
const uuid = require('node-uuid');
const CommonUtil = require('./module/class/util/common_utility');
const SCChecker = require('./module/class/checker/SCChecker');
const CustomerInfoManager = require(
    './module/class/manager/customer_info_manager');
const list = require('./module/company_list');
const SharedData = require('./module/shared_data');
const LandscapeAPI = require('./module/landscape');
const HistoryManager = require('./module/class/manager/history_manager');

// socket.joinは別途やる
var checker = new SCChecker();

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
              || !CommonUtil.isKeyExists(SharedData.sincloCore,
                  d.siteKey + '.' + d.sincloSessionId + '.sessionIds')
          )
      )
  ) {
    send.sincloSessionId = uuid.v4();
    send.sincloSessionIdIsNew = true;
    d.data.firstConnection = true;
  } else {
    send.sincloSessionIdIsnew = false;
  }

  if (d.data.firstConnection || !CommonUtil.isset(d.data.accessId)) {
    send.accessId = CommonUtil.makeAccessId();
  }

  if (CommonUtil.isset(d.token)) {
    send.token = d.token;
  }

  // ページ表示開始時間
  let datetime = new Date();
  send.pagetime = Date.parse(datetime);

  // アクセス開始フラグ
  if (d.data.firstConnection) {
    send.time = send.pagetime;
  }

  send.ipAddress = '127.0.0.1' ||
      req.headers['x-forwarded-for'] ||
      req.connection.remoteAddress;

  if (d.siteKey) {
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
    userId: '',
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
  let obj = Object.assign(d, reqData);
  if (CommonUtil.isKeyExists(obj, 'prevList') &&
      CommonUtil.isset(obj['prevList'])) {
    obj.prev = obj.prevList;
  }

  if (!CommonUtil.isset(SharedData.sincloCore[obj.siteKey])) {
    SharedData.sincloCore[obj.siteKey] = {};
  }
  if (!CommonUtil.isset(SharedData.sincloCore[obj.siteKey][obj.tabId])) {
    SharedData.sincloCore[obj.siteKey][obj.tabId] = {
      sincloSessionId: null,
      sessionId: null,
      subWindow: false,
      chatUnreadCnt: 0
    };
  }
  if (CommonUtil.isset(obj.sincloSessionId) &&
      !CommonUtil.isset(
          SharedData.sincloCore[obj.siteKey][obj.sincloSessionId])) {
    SharedData.sincloCore[obj.siteKey][obj.sincloSessionId] = {
      sessionIds: {},
      autoMessages: {},
      scenario: {},
      diagram: []
    };
  }
  if ('timeoutTimer' in SharedData.sincloCore[obj.siteKey][obj.tabId]) {
    clearTimeout(SharedData.sincloCore[obj.siteKey][obj.tabId].timeoutTimer);
    SharedData.sincloCore[obj.siteKey][obj.tabId].timeoutTimer = null;
  }

  var oldSessionId = SharedData.sincloCore[obj.siteKey][obj.tabId].sessionId;
  if (oldSessionId && CommonUtil.isset(obj.sincloSessionId)) {
    var sessionIds = SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].sessionIds;
    console.log('delete id : ' + oldSessionId);
    delete sessionIds[oldSessionId];
    console.log('remains : ' + Object.keys(sessionIds).length);
    /* FIXME
    Object.keys(sessionIds).forEach(function(key) {
      if (!CommonUtil.isset(io.sockets.connected[key])) {
        console.log('delete not exist sessionId : ' + key);
        delete sessionIds[key];
        console.log('remains : ' + Object.keys(sessionIds).length);
        var keys = Object.keys(list.customerList[obj.siteKey]);
        if (keys && keys.length > 0) {
          keys.forEach(function(customerListId) {
            if (customerListId.indexOf(key) >= 0) {
              console.log(
                  'delete not exist list.customerList : ' + customerListId);
              delete list.customerListId[info.siteKey][customerListId];
            }
          });
        }
      }
    });
     */
  }

  SharedData.connectList[obj.socketId] = {
    siteKey: obj.siteKey,
    tabId: obj.tabId,
    userId: null,
    sincloSessionId: obj.sincloSessionId
  };
  SharedData.sincloCore[obj.siteKey][obj.tabId].sessionId = obj.socketId;
  if (CommonUtil.isset(obj.sincloSessionId)) {
    SharedData.sincloCore[obj.siteKey][obj.tabId].sincloSessionId = obj.sincloSessionId;
    SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].sessionIds[obj.socketId] = obj.socketId;
  }
  if (CommonUtil.isset(obj.tmpAutoMessages)) {
    try {
      Object.keys(obj.tmpAutoMessages).
          forEach(function(automessageKey, index, array) {
            if (typeof (obj.tmpAutoMessages[automessageKey]['created']) ===
                'string') {
              obj.tmpAutoMessages[automessageKey]['created'] = new Date(
                  obj.tmpAutoMessages[automessageKey]['created']);
            }
            if (CommonUtil.isset(
                SharedData.sincloCore[obj.siteKey][obj.sincloSessionId]) &&
                !CommonUtil.isset(
                    SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].autoMessages)) {
              SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].autoMessages = {};
            }
            SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].autoMessages[automessageKey] = obj.tmpAutoMessages[automessageKey];
          });
    } catch (e) {

    }
  }
  if (CommonUtil.isset(obj.tmpDiagramMessages)) {
    try {
      Object.keys(obj.tmpDiagramMessages).
          forEach(function(diagramKey, index, array) {
            if (typeof (obj.tmpDiagramMessages[diagramKey]['created']) ===
                'string') {
              obj.tmpDiagramMessages[diagramKey]['created'] = new Date(
                  obj.tmpDiagramMessages[diagramKey]['created']);
            }
            if (CommonUtil.isset(
                SharedData.sincloCore[obj.siteKey][obj.sincloSessionId]) &&
                !CommonUtil.isset(
                    SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].diagram)) {
              SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].diagram = [];
            }
            SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].diagram.push(
                obj.tmpDiagramMessages[diagramKey]);
          });
    } catch (e) {

    }
  }

  if (!SharedData.getSessionId(obj.siteKey, obj.tabId, 'parentTabId')) {
    SharedData.connectList[obj.socketId] = {
      siteKey: obj.siteKey,
      tabId: obj.tabId,
      userId: obj.userId,
      sincloSessionId: obj.sincloSessionId
    };

    // 履歴作成
    //db.addHistory(obj, socket);
    // カスタム情報自動登録
    //db.upsertCustomerInfo(obj, socket, function(result) {
    // IPアドレスの取得
    if (!(('ipAddress' in obj) && CommonUtil.isset(obj.ipAddress))) {
      obj.ipAddress = getIp(socket);
    }

    var getCompanyInfoFromApi = function(obj, ip, callback) {
      if (list.functionManager.isEnabled(obj.siteKey,
          list.functionManager.keyList.refCompanyData)) {
        var api = new LandscapeAPI('json', 'utf8');
        api.getFrom(ip, callback);
      } else {
        deblogger.debug('refCompanyData is false. siteKey : ' + obj.siteKey);
        callback({});
      }
    };

    getCompanyInfoFromApi(obj, obj.ipAddress, function(data) {
      try {
        if (data) {
          var response = data;
          obj.orgName = response.orgName;
          obj.lbcCode = response.lbcCode;
          SharedData.sincloCore[obj.siteKey][obj.tabId].orgName = obj.orgName;
          SharedData.sincloCore[obj.siteKey][obj.tabId].lbcCode = obj.lbcCode;
          if (CommonUtil.isset(
              list.customerList[obj.siteKey][obj.accessId + '_' +
              obj.ipAddress +
              '_' + obj.socketId])) {
            list.customerList[obj.siteKey][obj.accessId + '_' +
            obj.ipAddress +
            '_' + obj.socketId]['orgName'] = obj.orgName;
            list.customerList[obj.siteKey][obj.accessId + '_' +
            obj.ipAddress +
            '_' + obj.socketId]['lbcCode'] = obj.lbcCode;
          }
        }

        obj.term = CommonUtil.timeCalculator(obj);
        if (SharedData.getSessionId(obj.siteKey, obj.tabId, 'chat')) {
          obj.chat = SharedData.getSessionId(obj.siteKey, obj.tabId, 'chat');
        }

        var afterGetInformationProcess = function() {
          list.customerList[obj.siteKey][obj.accessId + '_' + obj.ipAddress +
          '@@' +
          obj.socketId] = obj;
          let mergedObj = Object.assign(
              SharedData.sincloCore[obj.siteKey][obj.tabId], obj);
          if (CommonUtil.isset(
              SharedData.sincloCore[obj.siteKey][obj.sincloSessionId])) {
            SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].customerInfo = obj.customerInfo;
          }

          let history = new HistoryManager();
          history.getChatHistory(obj).then((addedObj) => {
            res.json(obj);
          });
        };

        if (CommonUtil.isset(SharedData.company.info[obj.siteKey]) &&
            Object.keys(SharedData.company.info[obj.siteKey]).length > 0) {
          let customerApi = new CustomerInfoManager();
          customerApi.getInfo(obj.userId, obj.siteKey).then((information) => {
            obj.customerInfo = information;
            afterGetInformationProcess();
          });
        } else {
          afterGetInformationProcess();
        }
      } catch (e) {
        console.log(
            'getCompanyInfoFromApiのcallbackでエラー : ' + data + ' message : ' +
            e.message);
      }
    });
  }
});

module.exports = router;