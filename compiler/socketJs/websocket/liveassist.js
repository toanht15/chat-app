/**
 * Created by masashi_shimizu on 2017/05/30.
 */
var LaUtility = function() {
  this.API = {
    CREATE_SHORTCODE: {
      URL: window.sincloInfo.site.la + '/assistserver/shortcode/create',
      METHOD: 'PUT'
    },
    GET_SESSION: {
      URL: window.sincloInfo.site.la + '/assistserver/shortcode/consumer?appkey=',
      METHOD: 'GET'
    },
    STATUSCODE: {
      OK: 200
    },
    READYSTATE: {
      UNINITIALIZED: 0,
      LOADING: 1,
      LOADED: 2,
      INTERACTIVE: 3,
      COMPLETE: 4
    }
  };
  /**
   * variables
   */
  this.shortcode = '';
  this.sessionInfo = {};
  this.operatorId = '';
  this.errorCallback = function() {};

  /**
   * @return $.Diferred.promise()
   */
  this.initAndStart = function(operatorId) {
    this.operatorId = operatorId;
    this.initSDKCallbacks();
    return this.createShortCode().then(this.getSessionId.bind(this)).then(this.connect.bind(this));
  };

  this.initSDKCallbacks = function() {
    AssistSDK.onConnectionEstablished = function() {
      console.log("onConnectionEstablished");
    };

    AssistSDK.onInSupport = function() {
      console.log("onInSupport");
    };

    AssistSDK.onPushRequest = function() {
      console.log("onPushRequest");
    };

    AssistSDK.onAgentJoinedSession = function() {
      console.log("onAgentJoinedSession");
    };

    AssistSDK.onAgentJoinedCobrowse = function() {
      console.log("onAgentJoinedCobrowse");
    };

    AssistSDK.onAgentLeftCobrowse = function() {
      console.log("onAgentLeftCobrowse");
    };

    AssistSDK.onAgentLeftSession = function() {
      console.log("onAgentLeftSession");
    };

    AssistSDK.onEndSupport = function() {
      console.log("onEndSupport");
    };

    AssistSDK.onError = function(error) {
      console.log("!!!!!!!!!! ON ERROR !!!!!!!!!!!!!! " + JSON.stringify(error));
      this.errorCallback(error);
    };

    AssistSDK.onScreenshareRequest = function() {
      console.log("Screenshare Request");
      return true; //常に許可
    };
  };

  this.connect = function() {
    var sessionToken = this.sessionInfo['session-token'] ? this.sessionInfo['session-token'] : "undefined";
    var correlationId = this.sessionInfo.cid ? this.sessionInfo.cid : "undefined";
    AssistSDK.startSupport({
      cobrowseOnly: true,
      videoMode : "none",
      url : window.sincloInfo.site.la,
      sdkPath : window.sincloInfo.site.la + "/assistserver/sdk/web/consumer",
      sessionToken : sessionToken,
      correlationId : correlationId,
      allowedIframeOrigins : false,
      disableLogging : true
    });
  };

  this.disconnect = function() {
    AssistSDK.endSupport();
  }

  this.createShortCode = function () {
    var _self = this;
    var deferred = sincloJquery.Deferred();
    var API = _self.API;
    var request = new XMLHttpRequest();
    request.onreadystatechange = function() {
      if (request.readyState === API.READYSTATE.COMPLETE) {
        if (request.status === API.STATUSCODE.OK) {
          _self.shortcode = JSON.parse(request.responseText).shortCode;
          deferred.resolve(_self.shortcode);
        } else {
          deferred.reject();
        }
      }
    }
    request.open(API.CREATE_SHORTCODE.METHOD, API.CREATE_SHORTCODE.URL, true);
    request.send();
    return deferred.promise();
  };

  this.getSessionId = function (shortcode) {
    var _self = this;
    var deferred = sincloJquery.Deferred();
    var request = new XMLHttpRequest();
    var API = _self.API;
    request.onreadystatechange = function() {
      if (request.readyState == API.READYSTATE.COMPLETE) {
        if (request.status == API.STATUSCODE.OK) {
          _self.sessionInfo = JSON.parse(request.responseText);
          deferred.resolve();
        } else {
          deferred.reject(request.status);
        }
      }
    };
    request.open(API.GET_SESSION.METHOD, API.GET_SESSION.URL + shortcode, true);
    request.send();
    return deferred.promise();
  };

  this.setOnErrorCallback = function(fnc) {
    this.errorCallback = fnc;
  }

  return this;
};

laUtil = new LaUtility();
