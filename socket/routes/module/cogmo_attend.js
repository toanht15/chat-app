'use strict';
/* ======================= */
var APICaller = require('./api_caller');

// @see http://info-i.net/event-emitter-warning
var events = require('events');
var eventEmitter = new events.EventEmitter();
eventEmitter.setMaxListeners(50);

module.exports = class CogmoAttendAPICaller extends APICaller {
  constructor () {
    super();
    this.systemUUID = 'c0d70609-e31e-4ba0-ac59-d26701f3402c';
    super.url = 'https://attend.cogmo.jp/api/v2/conversation/' + this.systemUUID;
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
    this._deleteFeedbackKey();
    super.body = this._createJSONdata(this.sessionId, 'button_はい', this.messageType.FEEDBACK_YES, this.beforeContext);
    return this._callApi();
  }

  sendFeedbackNo () {
    this._deleteFeedbackKey();
    super.body = this._createJSONdata(this.sessionId, 'button_いいえ', this.messageType.FEEDBACK_NO, this.beforeContext);
    return this._callApi();
  }

  _callApi () {
    return new Promise(((resolve, reject) => {
      super.call().then((response) => {
        this.logger.debug('RESPONSE DATA : %s', JSON.stringify(response));
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

    return this.processSave(insertData, stayLogsId, created);
  }

  saveMessage (historyId, stayLogsId, companiesId, visitorsId, msg, distinction, created) {
    this.logger.info('SAVE message : %s(%s)',msg,this._getChatbotMessageType());
    var insertData = {
      t_histories_id: historyId,
      m_companies_id: companiesId,
      visitors_id: visitorsId,
      m_users_id: null,
      message: msg,
      message_type: this._getChatbotMessageType(), // CogmoAttendから返却されたメッセージ
      message_distinction: distinction, //FIXME
      message_request_flg: 0,
      achievement_flg: null
    };

    return this.processSave(insertData, stayLogsId, created);
  }

  _getChatbotMessageType () {
    var isFeedback = this.isFeedbackMessage();
    //var isExitOnConversation = this.isExitOnConversation();
    if (isFeedback) {
      return 82;
    }  else {
      return 81;
    }
  }

  _deleteFeedbackKey () {
    if (this.beforeContext) {
      delete this.beforeContext.feedback;
    }
  }
};
