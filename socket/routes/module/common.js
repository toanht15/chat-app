var database = require('../database');
/* EXPORT TARGET VARIABLES */
var companySettings = {},
    siteKeyIdMap = {},
    widgetSettings = {},
    autoMessageSettings = {},
    publicHolidaySettings = {},
    operationHourSettings = {},
    mysql = require('mysql'),
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
var syslogger = log4js.getLogger('system'); // リクエスト用のロガー取得
/* ================= */

function initialize(siteKey) {
  // コール順厳守
  loadWidgetSettings(siteKey);
  loadAutoMessageSettings(siteKey);
  loadOperatingHourSettings(siteKey);
  loadPublicHoliday();
}

function exports() {
  'use strict';
  module.exports.reloadSettings = initialize;
  module.exports.reloadWidgetSettings = loadWidgetSettings;
  module.exports.reloadAutoMessageSettings = loadAutoMessageSettings;
  module.exports.reloadOperationHourSettings = loadAutoMessageSettings;
  module.exports.companySettings = companySettings;
  module.exports.siteKeyIdMap = siteKeyIdMap;
  module.exports.widgetSettings = widgetSettings;
  module.exports.autoMessageSettings = autoMessageSettings;
  module.exports.publicHolidaySettings = publicHolidaySettings;
  module.exports.operationHourSettings = operationHourSettings;
  module.exports.mysql = mysql;
  module.exports.pool = pool;
}

function loadWidgetSettings(siteKey) {
  'use strict';
  var getWidgetSettingSql  = 'SELECT ws.*, com.id as m_companies_id, com.company_key, com.core_settings, com.exclude_ips FROM m_widget_settings AS ws';
  if(siteKey) {
    syslogger.info("loadWidgetSettings target : " + siteKey);
    getWidgetSettingSql += ' INNER JOIN (SELECT * FROM m_companies WHERE company_key = ? AND del_flg = 0 ) AS com  ON ( com.id = ws.m_companies_id )';
    getWidgetSettingSql += ' WHERE ws.del_flg = 0 ORDER BY id DESC LIMIT 1;';
    pool.query(getWidgetSettingSql, siteKey,
      function(err, row){
        if(err) {
          syslogger.error('Unable load Widget settings. siteKey : ' + siteKey);
          return;
        }
        if(row && row.length > 0) {
          if(companySettings[siteKey]) {
            companySettings[siteKey] = {};
          }
          if(widgetSettings[siteKey]) {
            widgetSettings[siteKey] = {};
          }
          companySettings[siteKey]['id'] = row[0].m_companies_id;
          siteKeyIdMap[row[0].m_companies_id] = siteKey;
          companySettings[siteKey]['core_settings'] = JSON.parse(row[0].core_settings);
          companySettings[siteKey]['exclude_ips'] = row[0].exclude_ips;
          widgetSettings[siteKey]['display_type'] = row[0].display_type;
          widgetSettings[siteKey]['style_settings'] = JSON.parse(row[0].style_settings);
          syslogger.info("Load Widget setting OK. siteKey : " + siteKey);
        }
      }
    );
  } else {
    // All
    getWidgetSettingSql += ' INNER JOIN (SELECT * FROM m_companies WHERE del_flg = 0 ) AS com  ON ( com.id = ws.m_companies_id )';
    getWidgetSettingSql += ' WHERE ws.del_flg = 0;';
    pool.query(getWidgetSettingSql,
      function(err, rows){
        if(err) {
          syslogger.error('Unable load ALL Widget settings.');
          return;
        }
        if(rows && rows.length > 0) {
          rows.forEach(function(row){
            var targetSiteKey = row.company_key;
            if(!companySettings[targetSiteKey]) {
              companySettings[targetSiteKey] = {};
            }
            if(!widgetSettings[targetSiteKey]) {
              widgetSettings[targetSiteKey] = {};
            }
            companySettings[targetSiteKey].id = row.m_companies_id;
            siteKeyIdMap[row.m_companies_id] = targetSiteKey;
            companySettings[targetSiteKey].core_settings = JSON.parse(row.core_settings);
            companySettings[targetSiteKey].exclude_ips = row.exclude_ips;
            widgetSettings[targetSiteKey].display_type = row.display_type;
            widgetSettings[targetSiteKey].style_settings = JSON.parse(row.style_settings);
          });
          syslogger.info('Load ALL Widget settings is successful.');
        }
      }
    );
  }
}

function loadAutoMessageSettings(siteKey) {
  'use strict';
  var getTriggerListSql  = "SELECT am.*, company_key FROM t_auto_messages AS am ";
  if(siteKey) {
    syslogger.info("loadAutoMessageSettings target : " + siteKey);
    getTriggerListSql += " INNER JOIN (SELECT * FROM m_companies WHERE company_key = ? AND del_flg = 0 ) AS com  ON ( com.id = am.m_companies_id )";
    getTriggerListSql += " WHERE am.active_flg = 0 AND am.del_flg = 0 AND am.action_type IN (?,?);";
    pool.query(getTriggerListSql, [siteKey, '1', '2'],
      function(err, rows){
        if(err) {
          syslogger.error('Unable load AutoMessage settings. siteKey : ' + siteKey);
          return;
        }
        if(rows && rows.length > 0) {
          autoMessageSettings[siteKey] = rows;
          syslogger.info("Load AutoMessage setting OK. siteKey : " + siteKey);
        }
      }
    );
  } else {
    // All
    getTriggerListSql += ' INNER JOIN (SELECT * FROM m_companies WHERE del_flg = 0 ) AS com  ON ( com.id = am.m_companies_id )';
    getTriggerListSql += ' WHERE am.active_flg = 0 AND am.del_flg = 0 AND am.action_type IN (?,?);';
    pool.query(getTriggerListSql, ['1', '2'],
      function(err, rows){
        if(err) {
          syslogger.error('Unable load ALL AutoMessage settings.');
          return;
        }
        if(rows && rows.length > 0) {
          rows.forEach(function(row){
            var targetSiteKey = row.company_key;
            if(!autoMessageSettings[targetSiteKey]) {
              autoMessageSettings[targetSiteKey] = [];
            }
            autoMessageSettings[targetSiteKey].push(row);
          });
          syslogger.info('Load ALL Auto Message settings is successful.');
        }
      }
    );
  }
}

function loadOperatingHourSettings(siteKey) {
  'use strict';
  var getOperatingHourSQL = "SELECT * FROM m_operating_hours where m_companies_id = ?;";
  if(siteKey) {
    syslogger.info("loadOperatingHourSettings target : " + siteKey);
    pool.query(getOperatingHourSQL, [siteKeyIdMap[siteKey] ? siteKeyIdMap[siteKey] : 0],
      function(err, row){
        if(err) {
          syslogger.error('Unable load Operating-hour settings. siteKey : ' + siteKey);
          return;
        }
        if(row && row.length > 0) {
          operationHourSettings[siteKey] = row;
          syslogger.info("Load Operating-hour setting OK. siteKey : " + siteKey);
        }
      }
    );
  } else {
    // All
    getOperatingHourSQL = "SELECT * FROM m_operating_hours;";
    pool.query(getOperatingHourSQL,
      function(err, rows){
        if(err) {
          syslogger.error('Unable load ALL Operating-hour settings.');
          return;
        }
        if(rows && rows.length > 0) {
          rows.forEach(function(row){
            var targetSiteKey = siteKeyIdMap[row.m_companies_id];
            if(!operationHourSettings[targetSiteKey]) {
              operationHourSettings[targetSiteKey] = {};
            }
            operationHourSettings[targetSiteKey] = row;
          });
          syslogger.info('Load ALL Operating-hour settings is successful.');
        }
      }
    );
  }
}

function loadPublicHoliday() {
  var getPublicHolidaySQL = "SELECT * FROM public_holidays;";
  publicHolidaySettings = {};
  pool.query(getPublicHolidaySQL,
    function(err, rows){
      if(err) {
        syslogger.error('Unable load ALL Operating-hour settings.');
        return;
      }
      if(rows && rows.length > 0) {
        rows.forEach(function(row){
          if(!publicHolidaySettings[row.year]) {
            publicHolidaySettings[row.year] = [];
          }
          publicHolidaySettings[row.year].push(row);
        });
        syslogger.info('Load ALL Operating-hour settings is successful.');
      }
    }
  );
}

if(Object.keys(companySettings).length === 0) {
  initialize();
}
exports();