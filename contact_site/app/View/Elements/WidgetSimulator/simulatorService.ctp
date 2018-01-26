<script type="text/javascript">
'use strict';

sincloApp.service('SimulatorService', function() {
  //thisを変数にいれておく
  var self = this;

  // viewからウィジェット設定を取得する
  var json = JSON.parse(document.getElementById('TChatbotScenarioWidgetSettings').value);
  this.widgetSettings = [];
  for (var item in json) {
    this.widgetSettings[item] = json[item];
  }
  this.widgetSettings.show_name = <?=C_WIDGET_SHOW_COMP?>; // 表示名を企業名に固定する

  return {
    get: function(key) {
      return self.widgetSettings[key];
    },
    createMessage: function(val) {
      if (val === '') return;

      var strings = val.split('\n');
      var radioCnt = 1;
      var linkReg = RegExp(/(http(s)?:\/\/[\w\-\.\/\?\=\,\#\:\%\!\(\)\<\>\"\u3000-\u30FE\u4E00-\u9FA0\uFF01-\uFFE3]+)/);
      var telnoTagReg = RegExp(/&lt;telno&gt;([\s\S]*?)&lt;\/telno&gt;/);
      var htmlTagReg = RegExp(/<\/?("[^"]*"|'[^']*'|[^'">])*>/g)
      var radioName = "sinclo-radio0";
      var content = "";

      for (var i = 0; strings.length > i; i++) {
        var str = escape_html(strings[i]);

        // リンク
        var link = str.match(linkReg);
        if ( link !== null ) {
            var url = link[0];
            var a = "<a href='" + url + "' target='_blank'>" + url + "</a>";
            str = str.replace(url, a);
        }
        // ラジオボタン
        var radio = str.indexOf('[]');
        if ( radio > -1 ) {
            var value = str.slice(radio+2);
            var name = value.replace(htmlTagReg, '');
            str = "<span class='sinclo-radio'><input type='radio' name='" + radioName + "' id='" + radioName + "-" + i + "' class='sinclo-chat-radio' value='" + name + "'>";
            str += "<label for='" + radioName + "-" + i + "'>" + value + "</label></span>";
        }
        // 電話番号（スマホのみリンク化）
        var tel = str.match(telnoTagReg);
        if( tel !== null ) {
          var telno = tel[1];
          // if(isSmartphone) {
          //   // リンクとして有効化
          //   var a = "<a href='tel:" + telno + "'>" + telno + "</a>";
          //   str = str.replace(tel[0], a);
          // } else {
            // ただの文字列にする
            var span = "<span class='telno'>" + telno + "</span>";
            str = str.replace(tel[0], span);
          // }
        }
        content += str + "\n";
      }

      return content;
    },
    makeFaintColor: function() {
      var defColor = "#F1F5C8";
      //仕様変更、常に高度な設定が当たっている状態とする
      defColor = self.widgetSettings.re_background_color;
//       if(self.widgetSettings..color_setting_type === '1'){
//         defColor = self.widgetSettings..re_background_color;
//       }
//       else{
//         if ( self.widgetSettings..main_color.indexOf("#") >= 0 ) {
//           var code = self.widgetSettings..main_color.substr(1), r,g,b;
//           if (code.length === 3) {
//             r = String(code.substr(0,1)) + String(code.substr(0,1));
//             g = String(code.substr(1,1)) + String(code.substr(1,1));
//             b = String(code.substr(2)) + String(code.substr(2));
//           }
//           else {
//             r = String(code.substr(0,2));
//             g = String(code.substr(2,2));
//             b = String(code.substr(4));
//           }
//           var balloonR = String(Math.floor(255 - (255 - parseInt(r,16)) * 0.1));
//           var balloonG = String(Math.floor(255 - (255 - parseInt(g,16)) * 0.1));
//           var balloonB = String(Math.floor(255 - (255 - parseInt(b,16)) * 0.1));
//           defColor = 'rgb(' + balloonR  + ', ' +  balloonG  + ', ' +  balloonB + ')';
//         }
//       }
      return defColor;
    },
    getTalkBorderColor: function(chk) {
      var defColor = "#E8E7E0";
      //仕様変更、常に高度な設定が当たっている状態とする
      if(chk === 're'){
        defColor = self.widgetSettings.re_border_color;
      }
      else{
        defColor = self.widgetSettings.se_border_color;
      }
//       if(self.widgetSettings..color_setting_type === '1'){
//         if(chk === 're'){
//           defColor = self.widgetSettings..re_border_color;
//         }
//         else{
//           defColor = self.widgetSettings..se_border_color;
//         }
//       }
//       else{
//         defColor = self.widgetSettings.chat_talk_border_color;
//       }
      return defColor;
    }
  };
});

</script>
