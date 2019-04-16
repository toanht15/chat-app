'use strict';
const DatabaseManager = require('./database_manager');
const CommonUtil = require('../util/common_utility');
const list = require('../../company_list');

module.exports = class CustomerInfoManager extends DatabaseManager {

  constructor() {
    super();
  }

  getInfo(siteKey, visitorId) {
    return new Promise((resolve, reject) => {
      this.dbPool.query(
          'SELECT informations FROM m_customers WHERE m_companies_id = ? AND visitors_id = ? order by id desc LIMIT 1;',
          [list.companyList[siteKey], visitorId], function(err, row) {
            if (err !== null && err !== '') reject(null); // DB接続断対応
            if (CommonUtil.isset(row) && CommonUtil.isset(row[0]) &&
                CommonUtil.isset(row[0].informations)) {
              resolve(JSON.parse(row[0].informations));
            } else {
              resolve(null);
            }
          });
    });
  }

};