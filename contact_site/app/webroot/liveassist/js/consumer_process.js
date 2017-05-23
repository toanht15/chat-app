/**
 * Created by masashi_shimizu on 2017/05/19.
 */
$(function(){
    var startResponse = {};

    //AssistSDK-callbackハンドラ
    AssistSDK.onConnectionEstablished = function() {
        console.log("Connection Established");
    };

    AssistSDK.onScreenshareRequest = function() {
        //return boolean;
        console.log("Screenshare Request");
    };

    AssistSDK.onPushRequest = function(allow, deny) {
        var result = confirm("The agent wants to send you a document or image. Would you like to view it?");
        if (result) allow();
        else deny();
    }

    AssistSDK.onScreenshareRequest = function() {
        return true; //常に許可してみる
    };

    AssistSDK.onDocumentReceivedSuccess = function(sharedDocument) { console.log("*** shared item opened successfully: " + sharedDocument.id); sharedDocument.onClosed = function(actor) {
        alert("Shared document window has closed by " + actor + "."); };
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

    var start = function(shortcode) {
        var request = new XMLHttpRequest(); request.onreadystatechange = function() {
            if (request.readyState == 4) {
                if (request.status == 200) {
                    startResponse = JSON.parse(request.responseText);
                }
            }
        }
        $('#shortcodeField').html(shortcode);
        request.open('GET', 'https://sdk005.live-assist.jp/assistserver/shortcode/consumer?appkey=' + shortcode, true);
        request.send();
    }

    var request = new XMLHttpRequest(); request.onreadystatechange = function() {
        if (request.readyState == 4) { if (request.status == 200) {
            var shortcode = JSON.parse(request.responseText).shortCode;
            start(shortcode); }
        }
    }
    request.open('PUT', 'https://sdk005.live-assist.jp/assistserver/shortcode/create', true); request.send();
});
