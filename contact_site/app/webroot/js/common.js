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

function addVariable(type,sendMessage,focusPosition){
  switch(type){
      case 1:
            if (sendMessage.value.length == 0) {
              sendMessage.value += "[] ";
            }
            else {
              sendMessage.value = sendMessage.value.substr(0, focusPosition) + "\n" + "[] " + sendMessage.value.substr(focusPosition,sendMessage.value.length);
            }
            var beforeScrollTop = $(sendMessage).scrollTop();
            sendMessage.focus();
            $(sendMessage).scrollTop(beforeScrollTop);
            // 開始と終了タブの真ん中にカーソルを配置する
            if (sendMessage.createTextRange) {;
              var range = sendMessage.createTextRange();
              range.move('character', focusPosition+4);
              range.select();
            } else if (sendMessage.setSelectionRange) {
              sendMessage.setSelectionRange(sendMessage.value.length, focusPosition+4);
            }
            break;
        case 2:
          if (sendMessage.value.length == 0) {
            sendMessage.value += "<telno></telno>";
            addPosition = 7;
          }
          else {
            sendMessage.value = sendMessage.value.substr(0, focusPosition) + "\n" + "<telno></telno>" + sendMessage.value.substr(focusPosition,sendMessage.value.length);
            addPosition = 8;
          }
          var beforeScrollTop = $(sendMessage).scrollTop();
          sendMessage.focus();
          $(sendMessage).scrollTop(beforeScrollTop);
          // 開始と終了タブの真ん中にカーソルを配置する
          if (sendMessage.createTextRange) {
            var range = sendMessage.createTextRange();
            range.move('character', focusPosition+addPosition);
            range.select();
          } else if (sendMessage.setSelectionRange) {
            sendMessage.setSelectionRange(sendMessage.value.length, focusPosition+addPosition);
          }
          break;
        case 3:
          if (sendMessage.value.length == 0) {
            sendMessage.value += '<a href="ここにURLを記載">リンクテキスト</a>';
            addPosition = 9;
          }
          else {
            sendMessage.value = sendMessage.value.substr(0, focusPosition) + "\n" + "<a href='ここにURLを記載'>リンクテキスト</a>" + sendMessage.value.substr(focusPosition,sendMessage.value.length);
            addPosition = 10;
          }
          var beforeScrollTop = $(sendMessage).scrollTop();
          sendMessage.focus();
          $(sendMessage).scrollTop(beforeScrollTop);
          // 開始と終了タブの真ん中にカーソルを配置する
          if (sendMessage.createTextRange) {
            var range = sendMessage.createTextRange();
            range.move('character', focusPosition+addPosition);
            range.select();
          } else if (sendMessage.setSelectionRange) {
            sendMessage.setSelectionRange(sendMessage.value.length, focusPosition+addPosition);
          }
          break;
        case 4:
          if (sendMessage.value.length == 0) {
            sendMessage.value += '<a href="ここにURLを記載" target="_blank">リンクテキスト</a>';
            addPosition = 9;
          }
          else {
            sendMessage.value = sendMessage.value.substr(0, focusPosition) + "\n" + "<a href='ここにURLを記載' target='_blank'>リンクテキスト</a>" + sendMessage.value.substr(focusPosition,sendMessage.value.length);
            addPosition = 10;
          }
          var beforeScrollTop = $(sendMessage).scrollTop();
          sendMessage.focus();
          $(sendMessage).scrollTop(beforeScrollTop);
          // 開始と終了タブの真ん中にカーソルを配置する
          if (sendMessage.createTextRange) {
            var range = sendMessage.createTextRange();
            range.move('character', focusPosition+addPosition);
            range.select();
          } else if (sendMessage.setSelectionRange) {
            sendMessage.setSelectionRange(sendMessage.value.length, focusPosition+addPosition);
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

function replaceVariable(str,isSmartphone,widgetSize){
  var linkReg = RegExp(/(http(s)?:\/\/[\w\-\.\/\?\=\&\;\,\#\:\%\!\(\)\<\>\"\u3000-\u30FE\u4E00-\u9FA0\uFF01-\uFFE3]+)/);
  var telnoTagReg = RegExp(/&lt;telno&gt;([\s\S]*?)&lt;\/telno&gt;/);
  var linkTabReg = RegExp(/<a ([\s\S]*?)>([\s\S]*?)<\/a>/);
  var imgTagReg = RegExp(/<img ([\s\S]*?)>/);
  var choiseImgTagReg = RegExp(/<label([\s\S]*?)><img ([\s\S]*?)>/);
  var unEscapeStr = unEscapeHTML(str);
  var className;

  //ウィジェットサイズが小の場合
  if(widgetSize === '1' || isSmartphone) {
    className = 'smallSizeImg';
  }
  //ウィジェットサイズが中の場合
  else if(widgetSize === '2') {
    className = 'middleSizeImg';
  }
  //ウィジェットサイズが大の場合
  else if(widgetSize === '3') {
    className = 'largeSizeImg';
  }
  //リアルタイムモニタ詳細画面の場合
  else if(widgetSize === '4') {
    className = 'detailImg';
  }
  //サイト訪問者からのimgタグの場合
  else if(widgetSize === '5') {
    return str;
  }

  // リンク
  var link = str.match(linkReg);
  var linkTab = unEscapeStr.match(linkTabReg);
  if ( link !== null || linkTab !== null) {
      if ( linkTab !== null) {
        if(link !== null) {
          var a = linkTab[0];
          //imgタグ有効化
          var img = unEscapeStr.match(imgTagReg);
          if(img !== null) {
            imgTag = "<div style='display:inline-block;width:100%;vertical-align:bottom;'><img "+img[1]+" class = "+className+"></div>";
            a = a.replace(img[0], imgTag);
          }
        }
        else {
          // ただの文字列にする
          var a = "<span class='link'>"+ linkTab[2] + "</span>";
        }
        str = unEscapeStr.replace(linkTab[0], a);
      }
      //URLのみのリンクの場合
      else {
        var url = link[0];
        var a = "<a href='" + url + "' target=\"_blank\">" + url + "</a>";
        //imgタグ有効化
        var img = unEscapeStr.match(imgTagReg);
        if(img !== null) {
          imgTag = "<div style='display:inline-block;width:100%;vertical-align:bottom;'><img "+img[1]+" class = "+className+"></div>";
          a = a.replace(img[0], imgTag);
        }
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
  //imgタグ有効化
  var img = unEscapeStr.match(imgTagReg);
  var choiseImg = unEscapeStr.match(choiseImgTagReg);
  //選択肢に画像を入れる場合
  if(img !== null && choiseImg !== null) {
    imgTag = "<label "+choiseImg[1]+"><div style='display:inline-block;width:100%;vertical-align:bottom;'><img "+img[1]+" class = "+className+"></div></label>";
    str = unEscapeStr.replace(choiseImg[0], imgTag);
  }
  if(img !== null && choiseImg === null) {
    imgTag = "<div style='display:inline-block;width:100%;vertical-align:bottom;'><img "+img[1]+" class = "+className+"></div>";
    str = unEscapeStr.replace(img[0], imgTag);
  }
  return str;
}