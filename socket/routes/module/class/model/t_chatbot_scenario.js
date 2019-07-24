'use strict';
const ModelBase = require('./model_base');
const CommonUtil = require('../util/common_utility');
const SharedData = require('../../shared_data');
const list = require('../../company_list');

module.exports = class TChatbotScenario extends ModelBase {

  constructor() {
    super();
  }

  getActivityByIdWithSiteKey(id, siteKey) {
    let result = {};
    let self = this;
    return new Promise((resolve) => {
      self.dbPool.query(
          'select activity from t_chatbot_scenarios where m_companies_id = ? and id = ?;',
          [list.companyList[siteKey], id], (err, row) => {
            if (err !== null && err !== '') {
              CommonUtil.errorLog('getActivityByIdWithSiteKey にてエラー：' + err);
            } else if (row.length !== 0) {
              let obj = JSON.parse(row[0].activity);
              result = {id: id, activity: obj};
            }
            resolve(result);
          });
    });
  }
};