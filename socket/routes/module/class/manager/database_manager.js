'use strict';
var DBConnector = require('../util/db_connector_util');

module.exports = class DatabaseManager {

  constructor() {
    this.dbPool = DBConnector.getPool();
  }

};