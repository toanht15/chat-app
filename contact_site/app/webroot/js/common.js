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
          sendMessage.value += '<a href="ここにURL">ここにリンクテキスト</a>';
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
          sendMessage.value += '<a href="ここにURL" target="_blank">ここにリンクテキスト</a>';
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

function unEscapeHTML(str) {
  return str
    .replace(/(&lt;)/g, '<')
    .replace(/(&gt;)/g, '>')
    .replace(/(&quot;)/g, '"')
    .replace(/(&#39;)/g, "'")
    .replace(/(&amp;)/g, '&');
};

function replaceVariable(str,isSmartphone){
  var linkReg = RegExp(/(http(s)?:\/\/[\w\-\.\/\?\=\&\;\,\#\:\%\!\(\)\<\>\"\u3000-\u30FE\u4E00-\u9FA0\uFF01-\uFFE3]+)/);
  var telnoTagReg = RegExp(/&lt;telno&gt;([\s\S]*?)&lt;\/telno&gt;/);
  var linkTabReg = RegExp(/<a href="([\s\S]*?)">([\s\S]*?)<\/a>/);
  var unEscapeStr = unEscapeHTML(str);
  // リンク
  var link = str.match(linkReg);
  var linkTabReg = unEscapeStr.match(linkTabReg);
  if ( link !== null || linkTabReg !== null) {
      if ( linkTabReg !== null) {
        if(link !== null) {
          var a = "<a href=\"" + linkTabReg[1] + "\">" + linkTabReg[2] + "</a>";
        }
        else {
          // ただの文字列にする
          var a = "<span class='link'>"+ linkTabReg[2] + "</span>";
        }
        str = linkTabReg[1].replace(linkTabReg[1], a);
      }
      //URLのみのリンクの場合
      else {
        var url = link[0];
        var a = "<a href='" + url + "' target=\"_blank\">" + url + "</a>";
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