/**
 * Created by masashi_shimizu on 2017/05/19.
 */
$(function(){
    var startResponse = {};

    function init() {
        createShortCode().then(function(shortcode){
            $('body').append('<span id="la-short-code" style="position:absolute; background-color: #F00; color: #FFF;">'+ shortcode +'</span>');
            console.log('Short Code: ' + shortcode);
            getSessionInfo(shortcode).then(function(){
                $('#la-short-code').css('background-color','#0F0');
                AssistSDK.startSupport({
                    // destination : "agent1",
                    // videoMode : "agentOnly",
                    sessionToken : startResponse['session-token'] ? startResponse['session-token'] : "undefined",
                    correlationId : startResponse.cid
                });
            },function(statuscode){
                $('#la-short-code').css('background-color','#aa0');
            });
        });
    }

    //AssistSDK-callbackハンドラ
    AssistSDK.onConnectionEstablished = function() {
        console.log("Connection Established");
    };

    AssistSDK.onPushRequest = function(allow, deny) {
        var result = confirm("The agent wants to send you a document or image. Would you like to view it?");
        if (result) allow();
        else deny();
    }

    AssistSDK.onScreenshareRequest = function() {
        console.log("Screenshare Request");
        return true; //常に許可してみる
    };

    AssistSDK.onDocumentReceivedSuccess = function(sharedDocument) {
        console.log("*** shared item opened successfully: " + sharedDocument.id);
        sharedDocument.onClosed = function(actor) {
            alert("Shared document window has closed by " + actor + ".");
        };
        console.log("Setting shared item " + sharedDocument.id + " to close in 15 secs.");
        setTimeout(function() {
            console.log("*** Closing shared item " + sharedDocument.id); sharedDocument.close();
        }, 15 * 1000);
    };

    AssistSDK.onDocumentReceivedError = function(sharedDocument) {
        console.log("*** shared item opened with error: " + sharedDocument.id);
        sharedDocument.onClosed = function(actor) {
            alert("Shared document error window has been closed by " + actor + ".");
        };
        setTimeout(function() { sharedDocument.close();}, 5 * 1000);
    };

    // iframeを有効にするために必要らしい
    //AssistIFrameSDK.init({allowedOrigins: '*'});

    $('#startSupport').on('click',function(){
        AssistSDK.startSupport({
            // destination : "agent1",
            // videoMode : "agentOnly",
            sessionToken : startResponse['session-token'] ? startResponse['session-token'] : "undefined",
            correlationId : startResponse.cid
        });
    });

    function createShortCode() {
        var deferred = $.Deferred();
        var request = new XMLHttpRequest();
        request.onreadystatechange = function() {
            if (request.readyState == 4) {
                if (request.status == 200) {
                    var shortcode = JSON.parse(request.responseText).shortCode;
                    deferred.resolve(shortcode);
                } else {
                    deferred.reject();
                }
            }
        }
        request.open('PUT', 'https://sdk005.live-assist.jp/assistserver/shortcode/create', true); request.send();
        return deferred.promise();
    }

    function getSessionInfo(shortcode) {
        var deferred = $.Deferred();
        var request = new XMLHttpRequest();
        request.onreadystatechange = function() {
            if (request.readyState == 4) {
                if (request.status == 200) {
                    startResponse = JSON.parse(request.responseText);
                    deferred.resolve();
                } else {
                    deferred.reject(request.status);
                }
            }
        }
        request.open('GET', 'https://sdk005.live-assist.jp/assistserver/shortcode/consumer?appkey=' + shortcode, true);
        request.send();
        return deferred.promise();
    }

    init();
});
