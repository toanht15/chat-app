'use strict';
const ModelBase = require('./model_base');
const CommonUtil = require('../util/common_utility');
const SharedData = require('../../shared_data');
const list = require('../../company_list');

module.exports = class TChatbotDiagram extends ModelBase {

  constructor() {
    super();
  }

  getActivityByIdWithSiteKey(id, siteKey) {
    let result = {};
    let self = this;
    return new Promise((resolve) => {
      self.dbPool.query(
          'select activity from t_chatbot_diagrams where m_companies_id = ? and id = ?;',
          [list.companyList[siteKey], id],
          function(err, row) {
            if (err !== null && err !== '') {

            } else if (row.length !== 0) {
              result = JSON.parse(row[0].activity);
              // そのままのデータだとクライアント側の処理のパフォーマンスが悪いため
              // UUIDでデータを参照できるよう加工する
              var sendObj = {};
              for (let i = 0; i < result.cells.length; i++) {
                let cell = result.cells[i];
                sendObj[cell['id']] = {
                  id: cell['id'],
                  parent: cell['parent'],
                  type: cell['type'],
                  embeds: cell['embeds'],
                  attrs: cell['attrs']
                };
              }
              result = {id: id, activity: sendObj};
            }
            resolve(result);
          });
    });
  }
};