/**
 * Created by masashi_shimizu on 2017/05/19.
 */
$(function() {
      var val = getUrlVars();

      /**
       * URL解析して、クエリ文字列を返す
       * @returns {Array} クエリ文字列
       */
      function getUrlVars()
      {
        var vars = [], max = 0, hash = "", array = "";
        var url = window.location.search;

        //?を取り除くため、1から始める。複数のクエリ文字列に対応するため、&で区切る
        hash  = url.slice(1).split('&');
        max = hash.length;
        for (var i = 0; i < max; i++) {
          array = hash[i].split('=');    //keyと値に分割。
          vars.push(array[0]);    //末尾にクエリ文字列のkeyを挿入。
          vars[array[0]] = array[1];    //先ほど確保したkeyに、値を代入。
        }

        return vars;
      }

    // UI設定
    var remoteView = document.getElementById("remoteScreenView");
    var remoteViewContainer = document.getElementById("remoteScreenViewContainer");
    var remoteVideo = document.getElementById("remoteVideoView");
    var previewVideo = document.getElementById("localVideoView");
    var qualityIndicator = document.getElementById("qualityIndicator");
    var formContainer = document.getElementById("formContainer");

    var assistServerSession = {};
    var config = {autoanswer : 'true', agentName: 'Bob' };

    // AssistAgentSDK.setRemoteViewCallBack(function(width, height) {
    //     var remoteaspect = height / width;
    //     var localaspect = remoteView.offsetHeight / remoteView.offsetWidth;
    //     if (localaspect < remoteaspect) {
    //         height = Math.min(height, remoteView.offsetHeight);
    //         width = height / remoteaspect;
    //     } else {
    //         width = Math.min(width, remoteView.offsetWidth);
    //         height = width * remoteaspect;
    //     }
    //     remoteView.style.height = height + 'px';
    //     remoteView.style.width = width + 'px';
    // });

    AssistAgentSDK.setOnErrorCallback(function (error) {
        debug("ERROR :" + JSON.stringify(error));
    });

    function init() {
        setUI();
        initializeConfiguration();
        setConfiguration();
        setAssistAgentCallbacks();
        getAgentSessionInfo().then(function(){console.log('getSessionID OK');});
    }

    function setUI() {
        AssistAgentSDK.setRemoteView(remoteView);
        CallManager.setRemoteVideoElement(remoteVideo);
        CallManager.setLocalVideoElement(previewVideo);
        CallManager.setCallQualityIndicator(qualityIndicator);
    }

    function initializeConfiguration() {
        config.autoanswer = 'true';
        config.agentName = 'Bob';
        config.username = 'agent';
        config.password = 'password';
        config.url = "https://sdk005.live-assist.jp";
        config.additionalAttribute = {
            "AED2.metadata": {
                "role": "agent",
                "permissions": {
                    "viewable": ["banking", "claims", "default"],
                    "interactive": ["claims", "default"]
                },
                "agent-data-1": "agent-value-1",
                "agent-data-2": "agent-value-2"
            }
        };
    }

    function setConfiguration() {
        AssistAED.setConfig(config);
    }

    function setAssistAgentCallbacks() {
        AssistAgentSDK.setFormCallBack(function(formElement) {
            if (formElement) {
                formContainer.appendChild(formElement);
            }
        });

        AssistAgentSDK.setScreenShareActiveCallback(function() {
            debug("setScreenShareActive");
        });

        AssistAgentSDK.setRemoteViewCallBack(function (x, y) {
            remoteX = x;
            remoteY = y;
            var containerHeight = remoteViewContainer.offsetHeight;
            var containerWidth = remoteViewContainer.offsetWidth;
            var containerAspect = containerHeight / containerWidth;
            var remoteAspect = y / x;

            var height;
            var width;

            if (containerHeight == 0 || containerWidth == 0)
            {
                return;
            }
            if (containerAspect < remoteAspect) {
                // Container aspect is taller than the remote view aspect
                height = Math.min(y, containerHeight);
                width = height * (x / y);
            } else {
                // Container aspect is wider than (or the same as) the remote view aspect
                width = Math.min(x, containerWidth);
                height = width * (y / x);
            }
            remoteView.style.height = height + "px";
            remoteView.style.width = width + "px";
        });
    }

    function getCorrelationId (shortcode) {
        var d = new $.Deferred();
        var request = new XMLHttpRequest();
        request.onreadystatechange = function () {
            if (request.readyState == 4) {
                if (request.status == 200) {
                    var cid = JSON.parse(request.responseText).cid;
                    d.resolve(cid);
                } else {
                    d.reject(request.status);
                }
            }
        };
        request.open("GET", "https://sdk005.live-assist.jp/assistserver/shortcode/agent?appkey=" + shortcode, true);
        request.send();
        return d.promise();
    }

    function getAgentSessionInfo() {
        var d = new $.Deferred();
        var request = new XMLHttpRequest();
        request.onreadystatechange = function () {
            if (request.readyState == 4) {
                if (request.status == 200) {
                    assistServerSession = JSON.parse(request.responseText);
                    d.resolve();
                } else {
                    d.reject();
                }
            }
        };
        request.open("POST", "https://sdk005.live-assist.jp/assistserver/agent", true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send("username=" + config.username + "&password="
            + config.password + "&type=create&targetServer=" + "aHR0cHM6Ly9zZGswMDUubGl2ZS1hc3Npc3QuanA6NDQz" //FIXME
            + "&name=" + config.agentName + "&text=" + config.agentText);
        return d.promise();
    }

    function debug(message) {
        $('#debugMessage').append('<span>' + message + '</span>');
    }

    $('#startScreenShare').on('click', function (){
        AssistAgentSDK.requestScreenShare();
    });

    $('#startRemoteControl').on('click', function(){
        try {
            AssistAgentSDK.controlSelected();
        } catch (e) {
            if (e instanceof AssistAgentSDK.OffAssistPagesException) {
                alert('Page not Live Assist enabled');
            }
        }
    });

    $('#startPaint').on('click', function(){
        try {
            AssistAgentSDK.drawSelected();
        } catch (e) {
            if (e instanceof AssistAgentSDK.OffAssistPagesException) {
                alert('Page not Live Assist enabled');
            }
        }
    });

    $('#connect').on('click', function(){
        getCorrelationId($('#shortcode').val()).then(function(cid){
            AssistAgentSDK.startSupport({
                correlationId: cid,
                sessionToken: assistServerSession.token,
                url: "https://sdk005.live-assist.jp",
                additionalAttribute: config.additionalAttribute
            });
        });
    });

    init();
    if(val['k']) {
      getCorrelationId(val['k']).then(function(cid){
        AssistAgentSDK.startSupport({
          correlationId: cid,
          sessionToken: assistServerSession.token,
          url: "https://sdk005.live-assist.jp",
          additionalAttribute: config.additionalAttribute
        })
      }).then(AssistAgentSDK.requestScreenShare());
    }
});