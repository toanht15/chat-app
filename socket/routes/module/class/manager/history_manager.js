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

  timeUpdate(historyId, obj, time, callback) {
    return new Promise((resolve, reject) => {
      var insertStayData = {
            t_histories_id: historyId,
            title: ('title' in obj) ? obj.title : '',
            url: ('url' in obj) ? obj.url : '',
            stay_time: '',
            created: time,
            modified: time
          },
          self = this;

      self.dbPool.query(
          'SELECT * FROM t_history_stay_logs WHERE t_histories_id = ? ORDER BY id DESC LIMIT 1;',
          historyId,
          function(err, rows) {
            if (err !== null && err !== '') {
              reject(null);
              return false;
            } // DB接続断対応
            if (CommonUtil.isset(rows) && CommonUtil.isset(rows[0])) {
              // UPDATE
              var stayTime = CommonUtil.calcTime(rows[0].created, time);
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
                [time, time, historyId],
                function(error, results, fields) {
                }
            );

            if (insertStayData.url === '' || insertStayData.url ===
                rows[0].url) {
              resolve(rows[0].id);
              return;
            }
            pool.query('INSERT INTO t_history_stay_logs SET ?', insertStayData,
                function(error, results, fields) {
                  resolve(results.insertId);
                }
            );
          }
      );
    });
  }
};