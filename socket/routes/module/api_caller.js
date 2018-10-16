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
var request = require('request');
var moment = require('moment');
/* Private Variables */
var log4js = require('log4js'); // log4jsモジュール読み込み
log4js.configure('./log4js_setting.json'); // 設定ファイル読み込み
/* ================= */

module.exports = class APICaller {
  constructor (header, url, method, body) {
    this.header = header;
    this.url = url;
    this.method = method;
    this.body = body;
    this.logger = log4js.getLogger('cogmo');
  }

  get timeout () {
    return 10000;
  }

  get options () {
    return {
      uri: this.url,
      method: this.method,
      headers: this.header,
      timeout: this.timeout,
      json: this.body
    }
  }

  call () {
    return new Promise((resolve, reject) => {
      this.logger.info('REQUEST BODY : %s', JSON.stringify(this.body));
      request(this.options, (error, response, body) => {
        if(error || body.error) {
          reject(error);
        } else {
          resolve(body);
        }
      });
    });
  }

  processSave (insertData, stayLogsId, created) {
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
