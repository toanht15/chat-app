/**
 * Created by masashi_shimizu on 2017/05/19.
 */
$(function() {
    // UI設定
    var remoteView = document.getElementById("remoteScreenView");
    var remoteVideo = document.getElementById("remoteVideoView");
    var previewVideo = document.getElementById("localVideoView");
    var qualityIndicator = document.getElementById("qualityIndicator");

    AssistAgentSDK.setRemoteView(remoteView);
    CallManager.setRemoteVideoElement(remoteVideo);
    CallManager.setLocalVideoElement(previewVideo);
    CallManager.setCallQualityIndicator(qualityIndicator);

    var assistServerSession = {};
    var config = {autoanswer : 'true', agentName: 'Bob' };
    config.username = 'agent';
    config.password = 'password';
    config.url = "https://sdk005.live-assist.jp";

    AssistAED.setConfig(config);

    $('#startScreenShare').on('click', function (){
        AssistAgentSDK.requestScreenShare();
    });

    $('#connect').on('click', function(){
        var request = new XMLHttpRequest(); request.onreadystatechange = function() {
            if (request.readyState == 4) {
                if (request.status == 200) {
                    var cid = JSON.parse(request.responseText).cid;
                    AssistAgentSDK.startSupport({
                        correlationId : cid,
                        sessionToken : assistServerSession.token,
                        url : "https://sdk005.live-assist.jp"
                    });
                }
            }
        };
        request.open("GET", "https://sdk005.live-assist.jp/assistserver/shortcode/agent?appkey=" + $('#shortcode').val(), true);
        request.send();
    });

    var request = new XMLHttpRequest(); request.onreadystatechange = function() {
        if (request.readyState == 4) {
            if (request.status == 200) {
                assistServerSession = JSON.parse(request.responseText);
            }
        }
    };
    request.open("POST", "https://sdk005.live-assist.jp/assistserver/agent", true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send("username=" + config.username + "&password="
        + config.password + "&type=create&targetServer=" + "aHR0cHM6Ly9zZGswMDUubGl2ZS1hc3Npc3QuanA6NDQz" //FIXME
        + "&name=" + config.agentName + "&text=" + config.agentText);
});