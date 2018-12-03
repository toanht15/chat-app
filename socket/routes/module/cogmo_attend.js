'use strict';
/* ======================= */
var APICaller = require('./api_caller');

// @see http://info-i.net/event-emitter-warning
var events = require('events');
var eventEmitter = new events.EventEmitter();
eventEmitter.setMaxListeners(50);

/**
 * Cogmo Attend API連携用クラス
 * 実装はEcmaScript 6準拠
 * @type {module.CogmoAttendAPICaller}
 */
module.exports = class CogmoAttendAPICaller extends APICaller {
  constructor() {
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
  }

  init (historyId, obj, created, emitter) {
    return new Promise((resolve, reject) => {
      let customerSendData = {
        tabId: obj.tabId,
        sincloSessionId: obj.sincloSessionId,
        chatId: null,
        messageType: 1,
        created: created,
        sort: created,
        ret: true,
        chatMessage: obj.chatMessage.replace('button_', '').replace('\n', ''),
        message: obj.chatMessage.replace('button_', '').replace('\n', ''),
        siteKey: obj.siteKey,
        matchAutoSpeech: true,
        isScenarioMessage: false
      };
      emitter.toSameUser('sendChatResult', customerSendData, obj.siteKey, obj.sincloSessionId);
      emitter.toCompany('sendChatResult', customerSendData, obj.siteKey);
      this.sessionId = 0;
      this.beforeContext = {};
      // セッションID発行のため一度呼び出す
      super.body = this._createJSONdata(this.sessionId, "", this.messageType.INITIALIZE, this.beforeContext);
      this._callApi().then(() => {
        resolve();
      }, (error) => {
        this.log.error('Cogmo Attend Api connection is failed');
        reject();
      });
    });
  }

  /**
   * Cogmo Attend APIにメッセージを送信する
   * @see this.messageType
   * @param type Cogmo Attend APIのメッセージ種別
   * @param text 送信するメッセージ
   * @returns {Promise}
   */
  sendTo(type, text) {
    switch (type) {
      case this.messageType.TEXT:
        return this._sendText(text);
      case this.messageType.PUSH_BUTTON:
        return this._sendPushButton(text);
      case this.messageType.FEEDBACK_YES:
        return this._sendFeedbackYes();
      case this.messageType.FEEDBACK_NO:
        return this._sendFeedbackNo();
    }
  }

  /**
   * サイト訪問者が送信したメッセージを保存する
   * @param historyId
   * @param stayLogsId
   * @param companiesId
   * @param visitorsId
   * @param msg
   * @param distinction
   * @param created
   */
  saveCustomerMessage(historyId, stayLogsId, companiesId, visitorsId, msg, distinction, created) {
    let insertData = {
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

  /**
   * CogmoAttend APIが返却したメッセージを保存する
   * @param historyId
   * @param stayLogsId
   * @param companiesId
   * @param visitorsId
   * @param msg
   * @param distinction
   * @param created
   */
  saveMessage(historyId, stayLogsId, companiesId, visitorsId, msg, distinction, created) {
    this.logger.info('SAVE message : %s(%s)', msg, this._getChatbotMessageType());
    let insertData = {
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

  /**
   * メソッドを呼び出した地点の前のデータがfeedbackかどうか
   * <code>this.sendTo</code>をコールした地点で状態が変わるため注意
   * @returns {boolean}
   */
  isFeedbackMessage() {
    if (this.beforeContext) {
      return this.beforeContext.feedback ? this.beforeContext.feedback : false;
    }
    return false;
  }

  /**
   * メソッドを呼び出した地点の前のデータが会話終了状態かどうか
   * <code>this.sendTo</code>をコールした地点で状態が変わるため注意
   * @returns {boolean}
   */
  isExitOnConversation() {
    if (this.beforeContext && this.beforeContext.system) {
      return this.beforeContext.system.branch_exited ? this.beforeContext.system.branch_exited : false;
    }
    return false;
  }

  /**
   * オペレータに切り替えする状態かどうか
   * @returns {boolean}
   */
  isSwitchingOperator() {
    if (this.beforeContext) {
      return this.opInsert;
    }
    return false;
  }

  /**
   * テキストタイプのメッセージをCogmo Attend APIに送信する
   * @param text 送信するテキストメッセージ
   * @private
   */
  _sendText(text) {
    super.body = this._createJSONdata(this.sessionId, text, this.messageType.TEXT, this.beforeContext);
    return this._callApi();
  }

  /**
   * ボタンタイプのメッセージをCogmo Attend APIに送信する
   * @param text 送信するテキストメッセージ（ボタンのラベル）
   * @private
   */
  _sendPushButton(text) {
    super.body = this._createJSONdata(this.sessionId, 'button_' + text, this.messageType.PUSH_BUTTON, this.beforeContext);
    return this._callApi();
  }

  /**
   * フィードバック（はい）のメッセージをCogmo Attend APIに送信する
   * @private
   */
  _sendFeedbackYes() {
    this._deleteFeedbackKey();
    super.body = this._createJSONdata(this.sessionId, 'button_はい<END>', this.messageType.FEEDBACK_YES, this.beforeContext);
    return this._callApi();
  }

  /**
   * フィードバック（いいえ）のメッセージをCogmo Attend APIに送信する
   * @private
   */
  _sendFeedbackNo() {
    this._deleteFeedbackKey();
    super.body = this._createJSONdata(this.sessionId, 'button_いいえ<END>', this.messageType.FEEDBACK_NO, this.beforeContext);
    return this._callApi();
  }

  /**
   * Cogmo Attend APIに送信するためのJSONデータを生成する
   * @param sid セッションID
   * @param text 送信するメッセージ
   * @param type 送信するメッセージタイプ
   * @param context 1つ前の状態のcontext
   * @returns {{input: {text: *}, context: *, alternate_intents: boolean, uuid: string, _ex: {sessionId: *, type: *, gaId: null}}}
   * @private
   */
  _createJSONdata(sid, text, type, context) {
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

  /**
   * Cogmo Attend APIを呼ぶ
   * @returns {Promise<any>}
   * @private
   */
  _callApi() {
    return new Promise(((resolve, reject) => {
      super.call().then((response) => {
        this.logger.debug('RESPONSE DATA : %s', JSON.stringify(response));
        if (response.sessionId) {
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

  /**
   * sincloのmessageTypeを返却
   * @returns {number}
   * @private
   */
  _getChatbotMessageType() {
    var isFeedback = this.isFeedbackMessage();
    //var isExitOnConversation = this.isExitOnConversation();
    if (isFeedback) {
      return 82;
    } else {
      return 81;
    }
  }

  /**
   * 1つ前のcontextからfeedbackのキーを削除する
   * @private
   */
  _deleteFeedbackKey() {
    if (this.beforeContext) {
      delete this.beforeContext.feedback;
    }
  }
};
