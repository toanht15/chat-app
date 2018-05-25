var load = {
  flg: false,
  ev: function(fc){
    if ( !this.flg ) {
      fc();
      this.flg = true;
    }
  }
};

// input type=numberには数値のみ入力できるように
$(window).on('keydown', 'input[type="number"]', function(e){
  if ( (e.keyCode < 48 || e.keyCode > 57) && e.keyCode !== 46 && (window.event.keyCode==86 && window.event.ctrlKey==true) ) {
    return false;
  }
});

var getData = function(elm, key){
    var data = null;
    if (typeof elm.dataset === "object") {
        if ( key in elm.dataset ) {
            data = elm.dataset[key];
        }
    }
    // IE10用
    else if (elm.dataset === undefined) {
        if ( elm.getAttribute('data-' + key) ) {
            data = elm.getAttribute('data-' + key);
        }
    }
    return data;
}

function addVariable(type,sendMessage){
  switch(type){
        case 1:
            if (sendMessage.value.length > 0) {
                sendMessage.value += "\n";
            }
            sendMessage.value += "[] ";
            sendMessage.focus();
            break;
        case 2:
          if (sendMessage.value.length > 0) {
            sendMessage.value += "\n";
          }
          sendMessage.value += "<telno></telno>";
          sendMessage.focus();
          // 開始と終了タブの真ん中にカーソルを配置する
          if (sendMessage.createTextRange) {
            var range = sendMessage.createTextRange();
            range.move('character', sendMessage.value.length-8);
            range.select();
          } else if (sendMessage.setSelectionRange) {
            sendMessage.setSelectionRange(sendMessage.value.length, sendMessage.value.length-8);
          }
          break;
        case 3:
          if (sendMessage.value.length > 0) {
            sendMessage.value += "\n";
          }
          sendMessage.value += "<a href=ここにURL>ここにリンクテキスト</a>";
          sendMessage.focus();
          // 開始と終了タブの真ん中にカーソルを配置する
          if (sendMessage.createTextRange) {
            var range = sendMessage.createTextRange();
            range.move('character', sendMessage.value.length-14);
            range.select();
          } else if (sendMessage.setSelectionRange) {
            sendMessage.setSelectionRange(sendMessage.value.length, sendMessage.value.length-14);
          }
          break;
        case 4:
          if (sendMessage.value.length > 0) {
            sendMessage.value += "\n";
          }
          sendMessage.value += '<a href=ここにURL target="_blank">ここにリンクテキスト</a>';
          sendMessage.focus();
          // 開始と終了タブの真ん中にカーソルを配置する
          if (sendMessage.createTextRange) {
            var range = sendMessage.createTextRange();
            range.move('character', sendMessage.value.length-14);
            range.select();
          } else if (sendMessage.setSelectionRange) {
            sendMessage.setSelectionRange(sendMessage.value.length, sendMessage.value.length-14);
          }
          break;
    }
    return sendMessage;
}

function replaceVariable(str,isSmartphone){
  var linkReg = RegExp(/(http(s)?:\/\/[\w\-\.\/\?\=\&\;\,\#\:\%\!\(\)\<\>\"\u3000-\u30FE\u4E00-\u9FA0\uFF01-\uFFE3]+)/);
  var telnoTagReg = RegExp(/&lt;telno&gt;([\s\S]*?)&lt;\/telno&gt;/);
  var linkNewtabReg = RegExp(/&lt;a href=([\s\S]*?)target=&quot;_blank&quot;&gt;([\s\S]*?)&lt;\/a&gt;/);
  var linkMovingReg = RegExp(/&lt;a href=([\s\S]*?)&gt;([\s\S]*?)&lt;\/a&gt;/);
  // リンク
  var link = str.match(linkReg);
  var linkNewtab = str.match(linkNewtabReg);
  var linkMoving = str.match(linkMovingReg);
  if ( link !== null || linkNewtab !== null || linkMoving !== null) {
      //リンク（ページ遷移）
      if(linkMoving !== null) {
        var target = "";
        if(link !== null) {
          var a = "<a href='" + linkMoving[1] + "'" + target + ">" + linkMoving[2] + "</a>";
        }
        else {
          // ただの文字列にする
          var a = "<span class='link'>"+ linkMoving[2] + "</span>";
        }
        str = linkMoving[0].replace(linkMoving[0], a);
      }
      //リンク（新規ページ）
      if ( linkNewtab !== null) {
        var target = "target=_blank";
        if(link !== null) {
          var a = "<a href='" + linkNewtab[1] + "'" + target + ">" + linkNewtab[2] + "</a>";
        }
        else {
          // ただの文字列にする
          var a = "<span class='link'>"+ linkNewtab[2] + "</span>";
        }
        str = linkNewtab[1].replace(linkNewtab[1], a);
      }
      //URLのみのリンクの場合
      else {
        var target = "target=_blank";
        var url = link[0];
        var a = "<a href='" + url + "'" + target + ">" + url + "</a>";
        str = str.replace(url, a);
      }
  }
  // 電話番号（スマホのみリンク化）
  var tel = str.match(telnoTagReg);
  if( tel !== null ) {
    var telno = tel[1];
    if(isSmartphone) {
      // リンクとして有効化
      var a = "<a href='tel:" + telno + "'>" + telno + "</a>";
      str = str.replace(tel[0], a);
    } else {
      // ただの文字列にする
      var span = "<span class='telno'>" + telno + "</span>";
      str = str.replace(tel[0], span);
    }
  }
  return str;
}