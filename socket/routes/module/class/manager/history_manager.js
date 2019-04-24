const DatabaseManager = require('./database_manager');
const CommonUtil = require('../util/common_utility');
const list = require('../../company_list');
const SharedData = require('../../shared_data');

module.exports = class HistoryManager extends DatabaseManager {

  constructor() {
    super();
  }

  addHistory(obj) {
    let self = this;
    return new Promise((resolve, reject) => {
      if (CommonUtil.isset(obj.tabId) && CommonUtil.isset(obj.siteKey)) {
        if (!CommonUtil.isset(list.companyList[obj.siteKey]) ||
            obj.subWindow) return false;
        var siteId = list.companyList[obj.siteKey];
        self.dbPool.query(
            'SELECT * FROM t_histories WHERE m_companies_id = ? AND tab_id = ? AND visitors_id = ? ORDER BY id DESC LIMIT 1;',
            [siteId, obj.sincloSessionId || obj.tabId, obj.userId],
            function(err, rows) {
              if (err !== null && err !== '') reject(null); // DB接続断対応
              var now = CommonUtil.formatDateParse();

              if (CommonUtil.isset(rows) && CommonUtil.isset(rows[0])) {
                if (CommonUtil.isset(obj.sincloSessionId)) {
                  SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].historyId = rows[0].id;
                }
                SharedData.sincloCore[obj.siteKey][obj.tabId].historyId = rows[0].id;
                self.timeUpdate(rows[0].id, obj, now).
                    then(function(stayLogsId) {
                      obj.historyId = rows[0].id;
                      obj.stayLogsId = stayLogsId;
                      resolve(obj);
                    });
              } else {
                //insert
                var insertData = {
                  m_companies_id: siteId,
                  visitors_id: obj.userId,
                  tab_id: obj.sincloSessionId || obj.tabId,
                  ip_address: obj.ipAddress,
                  user_agent: obj.userAgent,
                  access_date: CommonUtil.formatDateParse(obj.time),
                  referrer_url: obj.referrer,
                  created: now,
                  modified: now
                };

                self.dbPool.query('INSERT INTO t_histories SET ?', insertData,
                    function(error, results, fields) {
                      if (error &&
                          (error !== null && error !== '')) return false; // DB接続断対応
                      var historyId = results.insertId;
                      if (CommonUtil.isset(obj.sincloSessionId)) {
                        SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].historyId = historyId;
                      }
                      SharedData.sincloCore[obj.siteKey][obj.tabId].historyId = historyId;
                      self.timeUpdate(historyId, obj, now).
                          then(function(stayLogsId) {
                            obj.historyId = historyId;
                            obj.stayLogsId = stayLogsId;
                            resolve(obj);
                          });
                    }
                );
              }
            });
      }
    });
  }

  getChatHistory(obj) { // 最初にデータを取得するとき
    let self = this;
    return new Promise((resolve, reject) => {
      var chatData = {historyId: null, messages: []};
      var historyId = SharedData.getSessionId(obj.siteKey, obj.sincloSessionId,
          'historyId');
      if (historyId) {
        chatData.historyId = historyId;
        if (CommonUtil.isset(
            SharedData.getSessionId(obj.siteKey, obj.tabId, 'historyId'))) {
          SharedData.sincloCore[obj.siteKey][obj.tabId]['historyId'] = historyId;
        }

        var sql = 'SELECT';
        sql += ' chat.id, chat.id as chatId, chat.message, chat.message_type as messageType, chat.message_distinction as messageDistinction,chat.achievement_flg as achievementFlg,chat.delete_flg as deleteFlg, chat.visitors_id as visitorsId,chat.m_users_id as userId, mu.display_name as userName, chat.message_read_flg as messageReadFlg,chat.notice_flg as noticeFlg, chat.hide_flg, chat.created ';
        sql += 'FROM t_history_chat_logs AS chat ';
        sql += 'LEFT JOIN m_users AS mu ON ( mu.id = chat.m_users_id ) ';
        sql += 'WHERE t_histories_id = ? AND chat.hide_flg = 0 ORDER BY created';

        self.dbPool.query(sql, [chatData.historyId], function(err, rows) {
          if (err !== null && err !== '') reject(null); // DB接続断対応
          var messages = (CommonUtil.isset(rows)) ? rows : [];
          var setList = {};
          for (var i = 0; i < messages.length; i++) {
            var chatMessageDate = messages[i].created;
            chatMessageDate = new Date(chatMessageDate);
            messages[i].sort = CommonUtil.fullDateTime(chatMessageDate);
            // if ( ('userName' in messages[i]) && obj.showName !== 1 ) {
            //   delete messages[i].userName;
            // }
            setList[CommonUtil.fullDateTime(chatMessageDate)] = messages[i];
          }
          var autoMessages = [];
          if (obj.sincloSessionId in SharedData.sincloCore[obj.siteKey] &&
              'autoMessages' in
              SharedData.sincloCore[obj.siteKey][obj.sincloSessionId]) {
            var autoMessageObj = SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].autoMessages;
            console.log(JSON.stringify(autoMessageObj));
            try {
              Object.keys(autoMessageObj).
                  forEach(function(automessageKey, index, array) {
                    autoMessages.push(autoMessageObj[automessageKey]);
                  });
            } catch (e) {

            }
          }
          for (var j = 0; j < autoMessages.length; j++) {
            if (CommonUtil.isset(autoMessages[j].sort)) {
              setList[autoMessages[j].sort + '_'] = autoMessages[j];
            }
          }
          var scenarioMessages = [];
          if (obj.sincloSessionId in SharedData.sincloCore[obj.siteKey] &&
              'scenario' in
              SharedData.sincloCore[obj.siteKey][obj.sincloSessionId]) {
            var scenariosObj = SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].scenario;
            console.log(JSON.stringify(scenariosObj));
            Object.keys(scenariosObj).forEach(function(scenarioId, index, arr) {
              var scenarioObj = scenariosObj[Number(scenarioId)];
              Object.keys(scenarioObj).
                  forEach(function(sequenceId, index2, arr2) {
                    var sequenceObj = scenarioObj[sequenceId];
                    Object.keys(sequenceObj).
                        forEach(function(categoryId, idx, array) {
                          scenarioMessages.push(sequenceObj[categoryId]);
                        });
                  });
            });
          }
          for (var k = 0; k < scenarioMessages.length; k++) {
            if (CommonUtil.isset(scenarioMessages[k].sort)) {
              setList[scenarioMessages[k].sort + '_'] = scenarioMessages[k];
            }
          }
          var diagram = [];
          if (obj.sincloSessionId in SharedData.sincloCore[obj.siteKey] &&
              'diagram' in
              SharedData.sincloCore[obj.siteKey][obj.sincloSessionId]) {
            var diagramArray = SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].diagram;
            try {
              diagram = SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].diagram;
            } catch (e) {

            }
          }
          console.log(JSON.stringify(diagram));
          for (var l = 0; l < diagram.length; l++) {
            if (CommonUtil.isset(diagram[l].sort)) {
              setList[diagram[l].sort + '_'] = diagram[l];
            }
          }
          chatData.messages = CommonUtil.objectSort(setList);
          obj.chat = chatData;
          console.log(chatData);
          resolve(obj);
        });
      } else {
        var setList = {};
        obj.chat = chatData;
        var autoMessages = [];
        if (obj.sincloSessionId in SharedData.sincloCore[obj.siteKey] &&
            'autoMessages' in
            SharedData.sincloCore[obj.siteKey][obj.sincloSessionId]) {
          var autoMessageObj = SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].autoMessages;
          console.log(JSON.stringify(autoMessageObj));
          try {
            Object.keys(autoMessageObj).
                forEach(function(automessageKey, index, array) {
                  autoMessages.push(autoMessageObj[automessageKey]);
                });
          } catch (e) {

          }
        }
        for (var j = 0; j < autoMessages.length; j++) {
          if (CommonUtil.isset(autoMessages[j].sort)) {
            setList[autoMessages[j].sort + '_'] = autoMessages[j];
          }
        }
        var scenarioMessages = [];
        if (obj.sincloSessionId in SharedData.sincloCore[obj.siteKey] &&
            'scenario' in
            SharedData.sincloCore[obj.siteKey][obj.sincloSessionId]) {
          var scenariosObj = SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].scenario;
          console.log(JSON.stringify(scenariosObj));
          Object.keys(scenariosObj).forEach(function(scenarioId, index, arr) {
            var scenarioObj = scenariosObj[Number(scenarioId)];
            Object.keys(scenarioObj).
                forEach(function(sequenceId, index2, arr2) {
                  var sequenceObj = scenarioObj[sequenceId];
                  Object.keys(sequenceObj).
                      forEach(function(categoryId, idx, array) {
                        scenarioMessages.push(sequenceObj[categoryId]);
                      });
                });
          });
        }
        for (var k = 0; k < scenarioMessages.length; k++) {
          if (CommonUtil.isset(scenarioMessages[k].sort)) {
            setList[scenarioMessages[k].sort + '_'] = scenarioMessages[k];
          }
        }
        var diagram = [];
        if (obj.sincloSessionId in SharedData.sincloCore[obj.siteKey] &&
            'diagram' in
            SharedData.sincloCore[obj.siteKey][obj.sincloSessionId]) {
          var diagramArray = SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].diagram;
          try {
            diagram = SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].diagram;
          } catch (e) {

          }
        }
        console.log(JSON.stringify(diagram));
        for (var l = 0; l < diagram.length; l++) {
          if (CommonUtil.isset(diagram[l].sort)) {
            setList[diagram[l].sort + '_'] = diagram[l];
          }
        }
        chatData.messages = CommonUtil.objectSort(setList);
        obj.chat = chatData;
        console.log(chatData);
        resolve(obj);
      }
    });
  }

  timeUpdate(historyId, obj, time) {
    let self = this;
    return new Promise(async (resolve, reject) => {
      let prevArray = obj.prev;
      let result = 0;
      for (let i = 0; i < prevArray.length; i++) {
        result = await this.processTimeUpdate(self, historyId, prevArray, i);
      }
      resolve(result);
    });
  }

  processTimeUpdate(self, historyId, prevArray, index) {
    return new Promise((resolve) => {
      let prevData = prevArray[index];
      let lastIndex = prevArray.length - 1;
      let accessTime = CommonUtil.formatDateParse(prevData.accessTime);
      let insertStayData = {
        t_histories_id: historyId,
        title: ('title' in prevData) ? prevData.title : '',
        url: ('url' in prevData) ? prevData.url : '',
        stay_time: '',
        created: accessTime,
        modified: accessTime
      };

      self.dbPool.query(
          'SELECT * FROM t_history_stay_logs WHERE t_histories_id = ? ORDER BY id DESC LIMIT 1;',
          historyId,
          function(err, rows) {
            if (err !== null && err !== '') {
              resolve(null);
              return false;
            } // DB接続断対応
            if (CommonUtil.isset(rows) && CommonUtil.isset(rows[0])) {
              // UPDATE
              var stayTime = CommonUtil.getDiffTime(
                  CommonUtil.formatDateParse(rows[0].created), accessTime);
              self.dbPool.query(
                  'UPDATE t_history_stay_logs SET stay_time = ? WHERE id = ?',
                  [stayTime, rows[0].id],
                  function(error, results, fields) {
                  }
              );
            } else {
              rows[0] = {url: null};
            }
            self.dbPool.query(
                'UPDATE t_histories SET out_date = ?, modified = ? WHERE id = ?',
                [accessTime, accessTime, historyId],
                function(error, results, fields) {
                }
            );

            if (insertStayData.url === '' || insertStayData.url ===
                rows[0].url) {
              resolve(rows[0].id);
              return;
            }
            self.dbPool.query('INSERT INTO t_history_stay_logs SET ?',
                insertStayData,
                function(error, results, fields) {
                  resolve(results.insertId);
                }
            );
          }
      );
    });
  }

  incrementAccessCount(companiesId, datetime) {
    let self = this;
    let targetDate = datetime.replace(/\//g, '-');
    self.dbPool.query('SELECT * from t_history_access_counts\n' +
        'WHERE\n' +
        '  m_companies_id = ?\n' +
        'AND\n' +
        '  year = DATE_FORMAT(?, \'%Y\')\n' +
        'AND\n' +
        '  month = DATE_FORMAT(?, \'%m\')\n' +
        'AND\n' +
        '  day = DATE_FORMAT(?, \'%d\')\n' +
        'AND\n' +
        '  hour = DATE_FORMAT(?, \'%H\')',
        [companiesId, targetDate, targetDate, targetDate, targetDate],
        function(err, row) {
          if (row.length !== 0) {
            self.dbPool.query('UPDATE  t_history_access_counts\n' +
                'SET\n' +
                '  access_count = access_count+1\n' +
                'WHERE\n' +
                '  m_companies_id = ?\n' +
                'AND\n' +
                '  year = DATE_FORMAT(?, \'%Y\')\n' +
                'AND\n' +
                '  month = DATE_FORMAT(?, \'%m\')\n' +
                'AND\n' +
                '  day = DATE_FORMAT(?, \'%d\')\n' +
                'AND\n' +
                '  hour = DATE_FORMAT(?, \'%H\')',
                [companiesId, targetDate, targetDate, targetDate, targetDate],
                function(err, result) {
                });
          } else {
            self.dbPool.query(
                'INSERT INTO t_history_access_counts(m_companies_id,year,month,day,hour,access_count)\n' +
                'VALUES(?,DATE_FORMAT(?, \'%Y\'),DATE_FORMAT(?, \'%m\'),DATE_FORMAT(?, \'%d\'),DATE_FORMAT(?, \'%H\'),1)',
                [companiesId, targetDate, targetDate, targetDate, targetDate],
                function(err, result) {
                });
          }
        });
  }
};