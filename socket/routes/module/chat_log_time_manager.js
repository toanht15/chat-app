var database = require('../database');
/* EXPORT TARGET VARIABLES */
var DBConnector = require('./class/util/db_connector_util');
var moment = require('moment');

module.exports = class ChatLogTimeManager {
  static saveTime(chatLogId, historyId, type, definedTime) {
    if (!definedTime) {
      // 現在時刻を入れる
      definedTime = moment().format('YYYY-MM-DD HH:mm:ss.SS');
    }
    this.existsRecord(chatLogId, historyId, type).then((result) => {
      if (!result) {
        DBConnector.getPool().
            query('INSERT INTO t_history_chat_log_times VALUES (?, ?, ?, ?);',
            [chatLogId, historyId, type, definedTime], (err, result) => {
              if (err) {
                throw new Error(
                    't_history_chat_log_timesへの書き込みに失敗しました。 error: ' + err);
              }
            });
      }
    });
  }

  static existsRecord(chatLogId, historyId, type) {
    return new Promise((resolve, reject) => {
      let typeQuery = 'type = ?';
      if (type === 1) {
        typeQuery = '(' + typeQuery + ' or type = 2)';
      }
      DBConnector.getPool().query(
          'SELECT * from t_history_chat_log_times where t_histories_id = ? and ' +
          typeQuery, [historyId, type], (error, rows) => {
            if (error) {
              reject(error);
              return;
            }
            resolve(rows.length !== 0);
          });
    });
  }
};
