var popup = {
  const: {
    action: {
      alert: 1,
      confirm: 2,
    }
  },
  settings: {
    filesPath: null
  },
  getCss: function () {
    var css = '';
    css += '<div id="sincloPopup" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 99999999999999;">';
    css += '  <style>';
    css += '    #sincloPopupFrame {';
    css += '        border: 0.15em solid #ABABAB;';
    css += '        width: 30em;';
    css += '        opacity: 0;';
    css += '        background-color: #EDEDED;';
    css += '        color: #3C3C3C;';
    css += '        margin: auto;';
    css += '        position: absolute;';
    css += '        top: 0;';
    css += '        left: 0;';
    css += '        right: 0;';
    css += '        bottom: 0;';
    css += '        box-shadow: 0 35px 42px rgba(141, 141, 141, 0.8);';
    css += '        border-radius: 5px;';
    css += '        box-sizing: border-box;';
    css += '    }';
    css += '    #sincloPopBar {';
    css += '        height: 1.85em;';
    css += '        background: linear-gradient(#EDEDED, #D2D2D2);';
    css += '        border-bottom: 0.15em solid #989898;';
    css += '        border-radius: 5px 5px 0 0;';
    css += '    }';
    css += '    #sincloLogo {';
    css += '        padding: 1em;';
    css += '    }';
    css += '    #sincloMessage {';
    css += '        padding-right: 1em;';
    css += '    }';
    css += '    sinclo-h3 {';
    css += '        font-weight: bold;';
    css += '        display: block;';
    css += '        font-size: 1.2em;';
    css += '        height: 1.2em;';
    css += '        margin: 0.4em 0;';
    css += '    }';
    css += '    sinclo-div {';
    css += '        display: block;';
    css += '    }';
    css += '    sinclo-content {';
    css += '        display: block;';
    css += '        font-size: 0.9em;';
    css += '        margin: 0.5em 0;';
    css += '        line-height: 2em;';
    css += '    }';
    css += '    #sincloPopMain {';
    css += '        display: -ms-flexbox; display: -webkit-flex; display: flex;';
    css += '        min-height: calc(60px + 2em);';
    css += '    }';
    css += '    sinclo-div#sincloPopMain sinclo-div {';
    css += '        vertical-align: top;';
    css += '    }';
    css += '    #sincloPopAct {';
    css += '        width: 100%;';
    css += '        height: 2em;';
    css += '        text-align: center;';
    css += '        padding: 0.5em 0;';
    css += '        box-sizing: content-box;';
    css += '    }';
    css += '    #sincloPopAct sinclo-a {';
    css += '        background-color: #FFF;';
    css += '        padding: 5px 10px;';
    css += '        text-decoration: none;';
    css += '        border-radius: 5px;';
    css += '        border: 1px solid #959595;';
    css += '        margin: 10px;';
    css += '        font-size: 1em;';
    css += '        box-shadow: 0 0 2px rgba(75, 75, 75, 0.3);';
    css += '        font-weight: bold;';
    css += '    }';
    css += '    #sincloPopAct sinclo-a:hover {';
    css += '        cursor: pointer;';
    css += '    }';
    css += '    #sincloPopAct sinclo-a:hover,  #sincloPopAct sinclo-a:focus {';
    css += '        outline: none;';
    css += '    }';
    css += '    #sincloPopAct sinclo-a#sincloPopupOk {';
    css += '       background: linear-gradient(to top, #D5FAFF, #80BEEA, #D5FAFF);';
    css += '    }';
    css += '    #sincloPopAct sinclo-a#sincloPopupNg {';
    css += '       background-color: #FFF';
    css += '    }';
    css += '    #sincloPopAct sinclo-a#sincloPopupNg:hover, #sincloPopAct sinclo-a#sincloPopupNg:focus {';
    css += '       background-color: ##DCDCDC';
    css += '    }';
    css += '  </style>';
    return css;
  },
  getAction: function(type){
    var html = "";
    if ( type === popup.const.action.confirm ) {
      html += '      <sinclo-a href="javascript:void(0)" id="sincloPopupOk" onclick="popup.ok()">許可する</sinclo-a>';
      html += '      <sinclo-a href="javascript:void(0)" id="sincloPopupNg" onclick="popup.no()">許可しない</sinclo-a>';
    }
    else {
      html += '      <sinclo-a href="javascript:void(0)" id="sincloPopupOk" onclick="popup.ok()">閉じる</sinclo-a>';
    }
    return html;
  },
  set: function(title, content, type){
    if (isset(type) === false) {
      type = popup.const.action.confirm;
    }
    popup.remove();
    var html = '';
    html += this.getCss();
    html += '  <sinclo-div id="sincloPopupFrame">';
    html += '    <sinclo-div id="sincloPopBar">';
    html += '    </sinclo-div>';
    html += '    <sinclo-div id="sincloPopMain">';
    html += '      <sinclo-div id="sincloLogo"><img src="' + this.settings.filesPath + '/img/mark.png" width="60" height="60"></sinclo-div>';
    html += '      <sinclo-div id="sincloMessage">';
    html += '          <sinclo-h3>' + title + ':</sinclo-h3><sinclo-content>' + content + '</sinclo-content>';
    html += '      </sinclo-div>';
    html += '    </sinclo-div>';
    html += '    <sinclo-div id="sincloPopAct">';
    html += this.getAction(type);
    html += '    </sinclo-div>';
    html += '  </sinclo-div>';
    html += '</sinclo-div>';

    $("body").append(html);

    var height = 0;
    $("#sincloPopupFrame > sinclo-div").each(function(e){
console.log(this.offsetHeight);
      height += this.offsetHeight;
    });

    $("#sincloPopupFrame").height(height).css("opacity", 1);
  },
  remove: function(){
      var elm = document.getElementById('sincloPopup');
      if (elm) {
        elm.parentNode.removeChild(elm);
      }
  },
  ok: function(){ return true; },
  no: function(){ this.remove(); }
};

var isset = function(a){
  if ( a === null || a === '' || a === undefined || String(a) === "null" || String(a) === "undefined" ) {
     return false;
  }
  if ( typeof a === "object" ) {
    var keys = Object.keys(a);
    return ( Object.keys(a).length !== 0 );
  }
  return true;
}