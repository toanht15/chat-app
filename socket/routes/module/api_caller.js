'use strict';

var request = require('request');
var moment = require('moment');

module.exports = class APICaller {
  constructor (header, url, method, body) {
    this.header = header;
    this.url = url;
    this.method = method;
    this.body = body;
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
      request(this.options, (error, response, body) => {
        if(error || body.error) {
          reject(error);
        } else {
          resolve(body);
        }
      });
    });
  }

};
