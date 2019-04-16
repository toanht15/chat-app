const DatabaseManager = require('./database_manager');
const CommonUtil = require('../util/common_utility');
const list = require('../../company_list');

module.exports = class HistoryManager extends DatabaseManager {

  constructor() {
    super();
  }

  addHistory(obj) {
    return new Promise((resolve, reject) => {
      if (CommonUtil.isset(obj.tabId) && CommonUtil.isset(obj.siteKey)) {
        if (!CommonUtil.isset(list.companyList[obj.siteKey]) ||
            obj.subWindow) return false;
        var siteId = list.companyList[obj.siteKey];
        this.dbPool.query(
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
                timeUpdate(rows[0].id, obj, now, function(stayLogsId) {
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

                this.dbPool.query('INSERT INTO t_histories SET ?', insertData,
                    function(error, results, fields) {
                      if (error &&
                          (error !== null && error !== '')) return false; // DB接続断対応
                      var historyId = results.insertId;
                      if (CommonUtil.isset(obj.sincloSessionId)) {
                        SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].historyId = historyId;
                      }
                      SharedData.sincloCore[obj.siteKey][obj.tabId].historyId = historyId;
                      timeUpdate(historyId, obj, now, function(stayLogsId) {
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
};