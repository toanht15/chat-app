var database = require('../database');
/* EXPORT TARGET VARIABLES */
var mysql = require('mysql'),
  pool = mysql.createPool({
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASS || 'password',
    database: process.env.DB_NAME || 'sinclo_db'
  });
var request = require('request');
var moment = require('moment');
/* ======================= */
/* Private Variables */
var log4js = require('log4js'); // log4jsモジュール読み込み
log4js.configure('./log4js_setting.json'); // 設定ファイル読み込み
var lbcLogger = log4js.getLogger('lbc'); // リクエスト用のロガー取得
/* ================= */

module.exports = function(format, charset) {
  var mlLbcCode = "10102363864";
  var expireSec = 7776000000; // 90日
  var api = {
    lbc: {
      url: "https://cla.liveaccess.jp/api",
      method: "GET",
      key1: "BN7WjEygVK32UqSV",
      key2: ""
    }
  };
  var validate =  {
    ip: "^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}$",
    lbcCode: "^[0-9]+$"
  };
  var apiDBKeyMap = {
    'IP': 'ip_address',
    'LBC': 'lbc_code',
    'HoujinBangou_3.OrgCode': 'org_code',
    'HoujinBangou_4.HoujinBangou': 'houjin_bangou',
    'HoujinBangou_5.HoujinName': 'houjin_name',
    'HoujinBangou_6.HoujinAddress': 'houjin_address'
  };
  var apiOutkeyMap = {
    'LBC': 'lbcCode',
    'IP': 'ipAddress',
    'HoujinBangou_3.OrgCode': 'orgCode',
    'HoujinBangou_4.HoujinBangou': 'houjinBangou',
    'HoujinBangou_5.HoujinName': 'houjinName',
    'HoujinBangou_6.HoujinAddress': 'houjinAddress'
  };
  var dbColumns = [
    "lbc_code",
    "ip_address",
    "org_name",
    "org_zip_code",
    "org_address",
    "org_tel",
    "org_fax",
    "org_ipo_type",
    "org_date",
    "org_capital_code",
    "org_employees_code",
    "org_gross_code",
    "org_president",
    "org_industrial_category_m",
    "org_url",
    "houjin_bangou",
    "houjin_address",
    "updated"
  ];

  this.format = format;
  this.charset = charset;
  this.ip = "";
  this.dbData = {};
  this.apiData = {};

  this.getFrom = function(ip, callback) {
    try {
      setParam(ip);
      executeFindData(callback);
    } catch (e) {
      lbcLogger.error(e.message);
      callback({});
    }
  };

  /**
   *
   * @param ip
   * @param lbcCode
   */
  var setParam = function(ip) {
    if(ip) {
      validateIp(ip);
      setIp(ip);
    }
  };

  var executeFindData = function(callback) {
    if(this.ip) {
      getFromIp(callback);
    } else {
      throw new Error('必要なパラメータが不足しています。');
    }
  }

  var getFromIp = function(callback) {
    var baseData = {};
    var self = this;
    pool.query('select * from m_landscape_data where ip_address = ? order by updated desc', [this.ip], function(err, rows){
      if( err !== null && err !== '') {
        throw new Error('m_landscape_dataへのselect実行時にエラーが発生');
      }
      if(isArray(rows)) {
        rows.forEach(function(elm, index, arr){
          if(Object.keys(baseData).length === 0) {
            baseData = elm;
            return;
          }
          baseData['ip_address'] = baseData['ip_address'] + ',' + elm['ip_address'];
        });
        self.dbData = baseData;
      }
      if(isExpireDbData(baseData)) {
        lbcLogger.info("DB data is expired. update from LBC data. ipAddress : " + baseData['ip_address'] + " updated : " + baseData['updated']);
        getFromLbc(function(){
          saveToTable();
          callback(createOutputDataFromAPIData());
        });
      } else {
        callback(convertAllKeyToCamelcase(self.dbData));
      }
    });
  };

  var getFromLbc = function(callback) {
    var self = this;
    lbcLogger.info('call LBC api. ipAddress : ' + this.ip);
    var header = {};
    var options = {
      url: api.lbc.url,
      method: api.lbc.method,
      headers: header,
      json: true,
      timeout: 3000, // 3秒
      qs: {
        "key1": api.lbc.key1,
        "key2": api.lbc.key2,
        "format": "json",
        "charset": "utf8",
        "ipadr": self.ip.trim()
      }
    };

    request(options, function(error, response, body) {
      if(error) {
        throw new Error('API呼出時にエラーが発生しました。 error: ' + error);
      }
      if(typeof(body) === "string") {
        throw new Error('想定したメッセージbodyが返却されていない可能性があります。 body : ' + body);
      }
      lbcLogger.info('LBC api response body: ' + JSON.stringify(body));
      self.apiData = body;
      if("502".indexOf(self.apiData.status) >= 0) {
        throw new Error('API呼出時にLBCからエラーを取得しました。 body: ' + body);
      }
      callback();
    });
  };

  var createOutputDataFromAPIData = function() {
    var val = {};
    var defaultOutputData = {
      'lbcCode': '',
      'ipAddress': '',
      'orgName': '',
      'orgZipCode': '',
      'orgAddress': '',
      'orgTel': '',
      'orgFax': '',
      'orgIpoType': '',
      'orgDate': '',
      'orgCapitalCode': '',
      'orgEmployeesCode': '',
      'orgGrossCode': '',
      'orgPresident': '',
      'orgIndustrialCategoryM': '',
      'orgUrl': '',
      'houjinBangou': '',
      'houjinAddress': '',
      'updated': ''
    };
    if(this.apiData) {
      var convertedData = convertAllKeyToCamelcase(this.apiData);
      Object.keys(defaultOutputData).forEach(function(elm, idx, arr){
        val[elm] = convertedData[elm]
      });
    }
    return val;
  };

  var saveToTable = function() {
    if(this.apiData) {
      this.apiData['updated'] = moment().format('YYYY-MM-DD HH:mm:ss');
      var saveData = convertAllKeyToUnderscore(this.apiData);
      if(!Object.keys(this.dbData).length) {
        var insertQuery = "INSERT INTO m_landscape_data (";
        var valuesQuery = "VALUES (";
        Object.keys(saveData).forEach(function(elm, idx, arr){
          insertQuery += '`' + elm + '`, ';
          valuesQuery += '"' + saveData[elm] + '",';
        });
        insertQuery = insertQuery.slice(0, -2);
        valuesQuery = valuesQuery.slice(0, -1);
        insertQuery += ") " + valuesQuery + ");";
        pool.query(insertQuery, null, function(err, result){
          if( err !== null && err !== '') {
            throw new Error('m_landscape_dataへのinsert実行時にエラーが発生 message : ' + err);
          }
        });
      } else {
        var updateQuery = "UPDATE m_landscape_data set ";
        var valuesQuery = "";
        Object.keys(saveData).forEach(function(elm, idx, arr){
          valuesQuery += '`' + elm + '` = ' + '"' + saveData[elm] + '",';
        });
        valuesQuery = valuesQuery.slice(0, -1);
        updateQuery += valuesQuery + ' WHERE `ip_address` = "' + saveData['ip_address'] + '"';
        pool.query(updateQuery, null, function(err, result){
          if( err !== null && err !== '') {
            throw new Error('m_landscape_dataへのupdate実行時にエラーが発生 message : ' + err);
          }
        });
      }
    }
  };

  var convertAllKeyToUnderscore = function(obj) {
    var val = {};
    if (obj && typeof(obj) === 'object') {
      Object.keys(obj).forEach(function(elm, idx, arr){
        var columnName = "";
        if(apiDBKeyMap[elm]) {
          columnName = apiDBKeyMap[elm];
        } else {
          columnName = convertCamelCaseToUnderscore(elm);
        }
        if(dbColumns.indexOf(columnName) === -1) return;
        val[columnName] = obj[elm];
      });
    }
    return val;
  };

  var convertAllKeyToCamelcase = function(obj) {
    var val = {};
    if (obj && typeof(obj) === 'object') {
      Object.keys(obj).forEach(function(elm, idx, arr){
        if(apiOutkeyMap[elm]) {
          val[apiOutkeyMap[elm]] = obj[elm];
        } else {
          val[convertUnderscoreToCamelCase(elm)] = obj[elm];
        }
      });
    }
    return val;
  };

  var convertUnderscoreToCamelCase = function(str) {
    if(str) {
      str = str.charAt(0).toLowerCase() + str.slice(1);
      return str.replace(/[-_](.)/g, function(match, group1) {
        return group1.toUpperCase();
      });
    }
    return "";
  };

  var convertCamelCaseToUnderscore = function(str) {
    if(str) {
      return str.replace(/(?:^|\.?)([A-Z])/g, function (x,y){return "_" + y.toLowerCase()}).replace(/^_/, "");
    }
  };

  var validateIp = function(ip) {
    if(!(new RegExp(validate.ip).test(ip))) {
      throw new Error('IPアドレスの形式ではありません。 value: ' + ip);
    }
  };

  var isExpireDbData = function(data) {
    if(data && data['updated']) {
      var updatedDatetime = data['updated'];
      var now = moment().format('x');
      var updatedTimestamp = moment(data['updated']).format('x');
      return Number(now) - Number(updatedTimestamp) > expireSec; // ここはミリ秒での比較
    } else {
      // データが無いため有効期限切れとする
      return true;
    }
  };


  var setIp = function(ip) {
    this.ip = ip;
  }

  var setLbcCode = function(lbcCode) {
    this.lbcCode = lbcCode;
  }

  var isArray = function isArray(obj) {
    return Object.prototype.toString.call(obj) === '[object Array]';
  }
};