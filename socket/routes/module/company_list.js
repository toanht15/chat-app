var companyList = {};
var customerList = {};
var initialized = false;
var database = require('../database');
var CommonUtil = require('./class/util/common_utility');
// mysql
var mysql = require('mysql2'),
    pool = mysql.createPool({
      host: process.env.DB_HOST || 'localhost',
      user: process.env.DB_USER || 'root',
      password: process.env.DB_PASS || 'password',
      database: process.env.DB_NAME || 'sinclo_db'
    });

function getCompanyList(forceReload) {
  pool.query('select * from m_companies where del_flg = 0;',
      function(err, rows) {
        if (err !== null && err !== '') return false; // DB接続断対応
        var key = Object.keys(rows);
        for (var i = 0; key.length > i; i++) {
          var row = rows[key[i]];
          companyList[row.company_key] = row.id;
          laSessionCounter.setMaxCount(row.company_key, row.la_limit_users);
          functionManager.set(row.company_key, row.core_settings);
          if (!(row.company_key in customerList)) {
            console.log('new customerList : ' + row.company_key);
            customerList[row.company_key] = {};
          }
          if (initialized && !(row.company_key in common.companySettings)) {
            console.log('LOAD NEW COMPANY SETTINGS : ' + row.company_key);
            common.reloadSettings(row.company_key);
          }
        }
        initialized = true;
        if (forceReload) {
          common.reloadSettings();
        }
      });
}

// LiveAssistの同時セッション数管理用クラス
// laSessionCount = { 'siteKey' : {current: '利用中セッション数', max: '指定済みの最大数'} }
var LaSessionCounter = function() {
  var _key_currentCount = 'current';
  var _key_maxCount = 'max';
  var countList = {};
  var _initializeCountList = function(siteKey) {
    countList[siteKey] = {};
    countList[siteKey][_key_currentCount] = [];
    countList[siteKey][_key_maxCount] = 0;
    _printCurrentState(siteKey, '_initializeCountList');
  };
  var _getMaxCount = function(siteKey) {
    return (siteKey in countList && _key_maxCount in countList[siteKey]) ?
        countList[siteKey][_key_maxCount] :
        0;
  };
  var _getCurrentCount = function(siteKey) {
    return (siteKey in countList && _key_currentCount in countList[siteKey]) ?
        countList[siteKey][_key_currentCount].length :
        0;
  };
  var _printCurrentState = function(siteKey, functionName) {
    var current = _getCurrentCount(siteKey);
    var max = _getMaxCount(siteKey);
    console.log('LaSessionCounter::' + functionName + ' siteKey:' + siteKey +
        ' currentSessions:' + current + ' max:' + max);
  };
  return {
    setMaxCount: function(siteKey, maxCount) {
      if (!(siteKey in countList)) {
        _initializeCountList(siteKey);
      }
      countList[siteKey][_key_maxCount] = maxCount;
      _printCurrentState(siteKey, 'setMaxCount');
    },
    getMaxCount: function(siteKey) {
      return _getMaxCount(siteKey);
    },
    getCurrentCount: function(siteKey) {
      return _getCurrentCount(siteKey);
    },
    countUp: function(siteKey, tabId) { // サイト訪問者側のIDを入れる
      if (!this.currentCountExists(siteKey)) {
        // まずはゼロ代入
        this.initializeCurrentCount(siteKey);
      }
      if (!this.isLimit(siteKey)) {
        console.log('DEBUG2 : ' + JSON.stringify(countList[siteKey]));
        countList[siteKey][_key_currentCount].push(tabId);
      }
      _printCurrentState(siteKey, 'countUp');
    },
    countDown: function(siteKey, tabId) { // サイト訪問者側のIDを入れる
      if (countList[siteKey][_key_currentCount].length <= 0) return;
      countList[siteKey][_key_currentCount].some(function(v, i) {
        if (v === tabId) countList[siteKey][_key_currentCount].splice(i, 1);
      });
      _printCurrentState(siteKey, 'countDown');

    },
    initializeCurrentCount: function(siteKey) {
      countList[siteKey][_key_currentCount] = [];
    },
    currentCountExists: function(siteKey) {
      return (siteKey in countList) &&
          (_key_currentCount in countList[siteKey]) &&
          (typeof (countList[siteKey][_key_currentCount] === 'object'));
    },
    isLimit: function(siteKey) {
      var current = this.getCurrentCount(siteKey);
      var max = this.getMaxCount(siteKey);
      var result = (current >= max);
      if (result) {
        _printCurrentState(siteKey, 'isLimit');
      }
      return result;
    }
  };
};
var laSessionCounter = new LaSessionCounter();

/**
 * 機能有無管理クラス
 */
var CompanyFunctionManager = function() {
  var _list = {};

  return {
    keyList: {
      chat: 'chat',
      synclo: 'synclo',
      document: 'document',
      videochat: 'videochat',
      laCoBrowse: 'laCoBrowse',
      chatLimitation: 'chatLimitation',
      exportHistory: 'exportHistory',
      deleteHistory: 'deleteHistory',
      statistics: 'statistics',
      dictionaryCategory: 'dictionaryCategory',
      hideRealtimeMonitor: 'hideRealtimeMonitor',
      operatingHour: 'operatingHour',
      refCompanyData: 'refCompanyData',
      freeInput: 'freeInput',
      cv: 'cv',
      autoMessageSendMail: 'autoMessageSendMail',
      sendFile: 'sendFile',
      loginIpFilter: 'loginIpFilter',
      importExcelAutoMessage: 'importExcelAutoMessage',
      operatorPresenceView: 'operatorPresenceView',
      monitorPollingMode: 'monitorPollingMode',
      useCogmoAttendApi: 'useCogmoAttendApi'
    },
    set: function(companyKey, coreSettings) {
      try {
        if (typeof (coreSettings) === 'string') {
          _list[companyKey] = JSON.parse(coreSettings);
        } else {
          // object
          _list[companyKey] = coreSettings;
        }
      } catch (e) {
        console.log('Error while set functionList companyKey : ' + companyKey);
      }
    },
    isEnabled: function(companyKey, funcName) {
      var result = false;
      if (CommonUtil.isset(_list[companyKey]) &&
          CommonUtil.isset(_list[companyKey][funcName])) {
        result = _list[companyKey][funcName];
      }
      return result;
    }
  };
};
var functionManager = new CompanyFunctionManager();

module.exports.getCompanyList = getCompanyList;
module.exports.companyList = companyList;
module.exports.customerList = customerList;
module.exports.functionManager = functionManager;
module.exports.laSessionCounter = laSessionCounter;