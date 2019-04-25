'use strict';
const DatabaseManager = require('./database_manager');
const CommonUtil = require('../util/common_utility');
const SharedData = require('../../shared_data');
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

  upsertCustomerInfo(obj) {
    return new Promise((resolve, reject) => {
      if (CommonUtil.isset(obj.customVariables)) {
        var customVariables = obj.customVariables;
        var found = false;
        var customerInfo = (CommonUtil.isset(
            SharedData.sincloCore[obj.siteKey][obj.sincloSessionId]) &&
            CommonUtil.isset(
                SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].customerInfo)) ?
            SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].customerInfo :
            {};
        if (Object.keys(customVariables).length !== 0) {
          Object.keys(customVariables).forEach(function(elm, index, arr) {
            if (CommonUtil.isset(customVariables[elm]) &&
                customVariables[elm] !==
                customerInfo[elm]) {
              found = true;
            }
          });
        }
        if (found) {
          DBConnector.getPool().query(
              'SELECT * from m_customers where m_companies_id = ? AND visitors_id = ? order by id desc',
              [list.companyList[obj.siteKey], obj.userId], function(err, row) {
                if (err !== null && err !== '') {
                  reject(false);
                  return false;
                } // DB接続断対応

                var currentData = {};
                if (CommonUtil.isset(row) && CommonUtil.isset(row[0])) {
                  currentData = JSON.parse(row[0].informations);
                  Object.keys(customVariables).
                      forEach(function(key, idx, array) {
                        if (CommonUtil.isset(customVariables[key]) &&
                            customVariables[key] !==
                            '') {
                          currentData[key] = customVariables[key];
                        }
                      });
                  DBConnector.getPool().query(
                      'UPDATE m_customers set informations = ? where id = ? ',
                      [JSON.stringify(currentData), row[0].id],
                      function(err, result) {
                        SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].customerInfo = currentData;
                        resolve({
                          userId: obj.userId,
                          data: JSON.stringify(currentData)
                        });
                      });
                } else {
                  DBConnector.getPool().query(
                      'INSERT INTO m_customers VALUES (NULL, ?, ?, ?, now(), 0, NULL, NULL, NULL, NULL)',
                      [
                        list.companyList[obj.siteKey],
                        obj.userId,
                        JSON.stringify(customVariables)],
                      function(err, result) {
                        SharedData.sincloCore[obj.siteKey][obj.sincloSessionId].customerInfo = customVariables;
                        resolve({
                          userId: obj.userId,
                          data: JSON.stringify(customVariables)
                        });
                      });
                }
              });
        } else {
          resolve(false);
        }
      } else {
        resolve(false);
      }
    });
  }

};