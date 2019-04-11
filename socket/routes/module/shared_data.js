let activeOperator = {}, // 待機中オペレーター
    sincloCore = {}, // socketIDの管理
    connectList = {}, // socketIDをキーとした管理
    c_connectList = {}, // socketIDをキーとしたチャット管理
    vc_connectList = {}, // tabId: socketID
    doc_connectList = {'socketId': {}, 'timeout': {}}, // tabId: tabId
    // siteKeyをキーとした対応上限管理
    // scList = { 'siteKey': { user: { 'userId': '対応上限人数' },  cnt: { 'userId': '対応中人数' } } };
    scList = {},
    company = {
      info: {}, // siteKeyをキーとした企業側ユーザー人数管理
      user: {}, // socket.idをキーとした企業側ユーザー管理
      timeout: {} // userIdをキーとした企業側ユーザー管理
    }; // list.functionManager[siteKey][<accessId>_<socket.id>][<userObj>]

module.exports.activeOperator = activeOperator;
module.exports.sincloCore = sincloCore;
module.exports.connectList = connectList;
module.exports.c_connectList = c_connectList;
module.exports.vc_connectList = vc_connectList;
module.exports.doc_connectList = doc_connectList;
module.exports.scList = scList;
module.exports.company = company;