'use strict';

var database = require('../database');
/* EXPORT TARGET VARIABLES */
var mysql = require('mysql'),
  pool = mysql.createPool({
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASS || 'password',
    database: process.env.DB_NAME || 'sinclo_db'
  });
/* ======================= */
/* Private Variables */
var log4js = require('log4js'); // log4jsモジュール読み込み
log4js.configure('./log4js_setting.json'); // 設定ファイル読み込み
var cogmoLogger = log4js.getLogger('cogmo'); // リクエスト用のロガー取得
/* ================= */

var APICaller = require('./api_caller');

module.exports = class CogmoAttendAPICaller extends APICaller {
  constructor () {
    super();
    this.systemUUID = 'c0d70609-e31e-4ba0-ac59-d26701f3402c';
    super.url = 'https://attend.cogmo.jp/conversation/' + this.systemUUID;
    super.method = 'post';
    this.messageType = {
      INITIALIZE: 0,
      TEXT: 2,
      PUSH_BUTTON: 3,
      FEEDBACK_YES: 4,
      FEEDBACK_NO: 5
    };
    this.sessionId = 0;
    this.beforeContext = {};
    // セッションID発行のため一度呼び出す
    super.body = this._createJSONdata(this.sessionId, "", this.messageType.INITIALIZE, this.beforeContext);
    this._callApi();
  }

  _createJSONdata (sid, text, type, context) {
    let defaultJSONdata = {
      input: {
        text: text
      },
      context: context,
      alternate_intents: true,
      uuid: this.systemUUID,
      _ex: {
        sessionId: sid,
        type: type,
        gaId: null
      }
    };
    return defaultJSONdata;
  };

  sendText (text) {
    super.body = this._createJSONdata(this.sessionId, text, this.messageType.TEXT, this.beforeContext);
    return this._callApi();
  }

  sendPushButton (text) {
    super.body = this._createJSONdata(this.sessionId, 'button_' + text, this.messageType.PUSH_BUTTON, this.beforeContext);
    return this._callApi();
  }

  sendFeedbackYes () {
    super.body = this._createJSONdata(this.sessionId, 'button_はい', this.messageType.FEEDBACK_YES, this.beforeContext);
    return this._callApi();
  }

  sendFeedbackNo () {
    super.body = this._createJSONdata(this.sessionId, 'button_いいえ', this.messageType.FEEDBACK_NO, this.beforeContext);
    return this._callApi();
  }

  _callApi () {
    return new Promise(((resolve, reject) => {
      super.call().then((response) => {
        cogmoLogger.debug('RESPONSE DATA : %s', JSON.stringify(response));
        if(response.sessionId) {
          this.sessionId = response.sessionId;
        }
        this.beforeContext = response.context;
        this.opInsert = response.context.op_insert ? response.context.op_insert : false;
        resolve(response.output.text);
      }, (error) => {
        reject(error);
      });
    }));
  }

  isSwitchingOperator () {
    if(this.beforeContext) {
      return this.opInsert;
    }
    return false;
  }

  isFeedbackMessage () {
    if(this.beforeContext) {
      return this.beforeContext.feedback ? this.beforeContext.feedback : false;
    }
    return false;
  }

  isExitOnConversation () {
    if(this.beforeContext && this.beforeContext.system) {
      return this.beforeContext.system.branch_exited ? this.beforeContext.system.branch_exited : false;
    }
    return false;
  }

  saveCustomerMessage (historyId, stayLogsId, companiesId, visitorsId, msg, distinction, created) {
    var insertData = {
      t_histories_id: historyId,
      m_companies_id: companiesId,
      visitors_id: visitorsId,
      m_users_id: null,
      message: msg,
      message_type: 1, // サイト訪問者のメッセージ
      message_distinction: distinction, //FIXME
      message_request_flg: 0,
      achievement_flg: null
    };

    return this._processSave(insertData, stayLogsId, created);
  }

  saveMessage (historyId, stayLogsId, companiesId, visitorsId, msg, distinction, created) {
    var insertData = {
      t_histories_id: historyId,
      m_companies_id: companiesId,
      visitors_id: visitorsId,
      m_users_id: null,
      message: msg,
      message_type: this._getMessageType(), // CogmoAttendから返却されたメッセージ
      message_distinction: distinction, //FIXME
      message_request_flg: 0,
      achievement_flg: null
    };

    return this._processSave(insertData, stayLogsId, created);
  }

  _getMessageType () {
    var isFeedback = this.isFeedbackMessage();
    var isExitOnConversation = this.isFeedbackMessage();
    if (isExitOnConversation) {
      return 81;
    } else if (isFeedback) {
      return 82;
    } else {
      return 81;
    }
  }

  _processSave (insertData, stayLogsId, created) {
    return new Promise(((resolve, reject) => {
      pool.query('SELECT * FROM t_history_stay_logs WHERE t_histories_id = ? ORDER BY id DESC LIMIT 1;', insertData.t_histories_id, (err, rows) => {
        if (err !== null && err !== '') {
          reject(err);
        }
        if ( rows && rows[0] ) {
          insertData.t_history_stay_logs_id = stayLogsId ? stayLogsId : rows[0].id;
        }
        insertData.created = created ? new Date(created) : new Date();
        pool.query('INSERT INTO t_history_chat_logs SET ?', insertData, (error, results) => {
          if (error !== null && error !== '') {
            reject(error);
          }
          resolve({
            insertId: results.insertId,
            message: insertData.message,
            created: insertData.created
          });
        });
      });
    }));
  }
};
