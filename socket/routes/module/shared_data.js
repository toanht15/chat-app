var activeOperator, // 待機中オペレーター
    sincloCore, // socketIDの管理
    connectList, // socketIDをキーとした管理
    c_connectList, // socketIDをキーとしたチャット管理
    vc_connectList, // tabId: socketID
    doc_connectList, // tabId: tabId
    // siteKeyをキーとした対応上限管理
    // scList = { 'siteKey': { user: { 'userId': '対応上限人数' },  cnt: { 'userId': '対応中人数' } } };
    scList,
    company; // list.functionManager[siteKey][<accessId>_<socket.id>][<userObj>]

if (!activeOperator) {
  activeOperator = {};
}

if (!sincloCore) {
  sincloCore = {};
}

if (!connectList) {
  connectList = {};
}

if (!c_connectList) {
  c_connectList = {};
}

if (!vc_connectList) {
  vc_connectList = {};
}

if (!doc_connectList) {
  doc_connectList = {};
}

if (!scList) {
  scList = {};
}

if (!company) {
  company = {
    info: {},
    user: {},
    timeout: {}
  };
}

var SharedObject = (function() {
  this.activeOperator = activeOperator;
  this.sincloCore = sincloCore;
  this.connectList = connectList;
  this.c_connectList = c_connectList;
  this.vc_connectList = vc_connectList;
  this.doc_connectList = doc_connectList;
  this.scList = scList;
  this.company = company;

  this.getSessionId = (siteKey, tabId, key) => {
    if ((siteKey in this.sincloCore) &&
        (tabId in this.sincloCore[siteKey]) &&
        (key in this.sincloCore[siteKey][tabId])) {
      return this.sincloCore[siteKey][tabId][key];
    }
  };

  return this;
})();

module.exports = SharedObject;

