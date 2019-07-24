'use strict';
const DatabaseManager = require('../manager/database_manager');
const CommonUtil = require('../util/common_utility');

module.exports = class ModelBase extends DatabaseManager {
  constructor() {
    super();
  }
};