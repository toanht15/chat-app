var  load,
     loading; // 共通関数

loading = {
  load: {
    id: "loadingImg",
    flg: false,
    timer: null,
    loadingHtml: function(){
      var html  = "";
      html += "<style type='text/css'>";
      html += "sinclo-loading-div {";
      html += "  background: none;";
      html += "  position: relative;";
      html += "  width: 200px;";
      html += "  height: 200px;";
      html += "}";
      html += "@-webkit-keyframes sinclo-loading-css {";
      html += "  0% {";
      html += "    opacity: 1;";
      html += "    -ms-transform: scale(1.5);";
      html += "    -moz-transform: scale(1.5);";
      html += "    -webkit-transform: scale(1.5);";
      html += "    -o-transform: scale(1.5);";
      html += "    transform: scale(1.5);";
      html += "  }";
      html += "  100% {";
      html += "    opacity: 0.1;";
      html += "    -ms-transform: scale(1);";
      html += "    -moz-transform: scale(1);";
      html += "    -webkit-transform: scale(1);";
      html += "    -o-transform: scale(1);";
      html += "    transform: scale(1);";
      html += "  }";
      html += "}";
      html += "@-webkit-keyframes sinclo-loading-css {";
      html += "  0% {";
      html += "    opacity: 1;";
      html += "    -ms-transform: scale(1.5);";
      html += "    -moz-transform: scale(1.5);";
      html += "    -webkit-transform: scale(1.5);";
      html += "    -o-transform: scale(1.5);";
      html += "    transform: scale(1.5);";
      html += "  }";
      html += "  100% {";
      html += "    opacity: 0.1;";
      html += "    -ms-transform: scale(1);";
      html += "    -moz-transform: scale(1);";
      html += "    -webkit-transform: scale(1);";
      html += "    -o-transform: scale(1);";
      html += "    transform: scale(1);";
      html += "  }";
      html += "}";
      html += "@-moz-keyframes sinclo-loading-css {";
      html += "  0% {";
      html += "    opacity: 1;";
      html += "    -ms-transform: scale(1.5);";
      html += "    -moz-transform: scale(1.5);";
      html += "    -webkit-transform: scale(1.5);";
      html += "    -o-transform: scale(1.5);";
      html += "    transform: scale(1.5);";
      html += "  }";
      html += "  100% {";
      html += "    opacity: 0.1;";
      html += "    -ms-transform: scale(1);";
      html += "    -moz-transform: scale(1);";
      html += "    -webkit-transform: scale(1);";
      html += "    -o-transform: scale(1);";
      html += "    transform: scale(1);";
      html += "  }";
      html += "}";
      html += "@-ms-keyframes sinclo-loading-css {";
      html += "  0% {";
      html += "    opacity: 1;";
      html += "    -ms-transform: scale(1.5);";
      html += "    -moz-transform: scale(1.5);";
      html += "    -webkit-transform: scale(1.5);";
      html += "    -o-transform: scale(1.5);";
      html += "    transform: scale(1.5);";
      html += "  }";
      html += "  100% {";
      html += "    opacity: 0.1;";
      html += "    -ms-transform: scale(1);";
      html += "    -moz-transform: scale(1);";
      html += "    -webkit-transform: scale(1);";
      html += "    -o-transform: scale(1);";
      html += "    transform: scale(1);";
      html += "  }";
      html += "}";
      html += "@-moz-keyframes sinclo-loading-css {";
      html += "  0% {";
      html += "    opacity: 1;";
      html += "    -ms-transform: scale(1.5);";
      html += "    -moz-transform: scale(1.5);";
      html += "    -webkit-transform: scale(1.5);";
      html += "    -o-transform: scale(1.5);";
      html += "    transform: scale(1.5);";
      html += "  }";
      html += "  100% {";
      html += "    opacity: 0.1;";
      html += "    -ms-transform: scale(1);";
      html += "    -moz-transform: scale(1);";
      html += "    -webkit-transform: scale(1);";
      html += "    -o-transform: scale(1);";
      html += "    transform: scale(1);";
      html += "  }";
      html += "}";
      html += "@-webkit-keyframes sinclo-loading-css {";
      html += "  0% {";
      html += "    opacity: 1;";
      html += "    -ms-transform: scale(1.5);";
      html += "    -moz-transform: scale(1.5);";
      html += "    -webkit-transform: scale(1.5);";
      html += "    -o-transform: scale(1.5);";
      html += "    transform: scale(1.5);";
      html += "  }";
      html += "  100% {";
      html += "    opacity: 0.1;";
      html += "    -ms-transform: scale(1);";
      html += "    -moz-transform: scale(1);";
      html += "    -webkit-transform: scale(1);";
      html += "    -o-transform: scale(1);";
      html += "    transform: scale(1);";
      html += "  }";
      html += "}";
      html += "@-o-keyframes sinclo-loading-css {";
      html += "  0% {";
      html += "    opacity: 1;";
      html += "    -ms-transform: scale(1.5);";
      html += "    -moz-transform: scale(1.5);";
      html += "    -webkit-transform: scale(1.5);";
      html += "    -o-transform: scale(1.5);";
      html += "    transform: scale(1.5);";
      html += "  }";
      html += "  100% {";
      html += "    opacity: 0.1;";
      html += "    -ms-transform: scale(1);";
      html += "    -moz-transform: scale(1);";
      html += "    -webkit-transform: scale(1);";
      html += "    -o-transform: scale(1);";
      html += "    transform: scale(1);";
      html += "  }";
      html += "}";
      html += "@keyframes sinclo-loading-css {";
      html += "  0% {";
      html += "    opacity: 1;";
      html += "    -ms-transform: scale(1.5);";
      html += "    -moz-transform: scale(1.5);";
      html += "    -webkit-transform: scale(1.5);";
      html += "    -o-transform: scale(1.5);";
      html += "    transform: scale(1.5);";
      html += "  }";
      html += "  100% {";
      html += "    opacity: 0.1;";
      html += "    -ms-transform: scale(1);";
      html += "    -moz-transform: scale(1);";
      html += "    -webkit-transform: scale(1);";
      html += "    -o-transform: scale(1);";
      html += "    transform: scale(1);";
      html += "  }";
      html += "}";
      html += ".uil-spin-css > sinclo-loading-div {";
      html += "  width: 24px;";
      html += "  height: 24px;";
      html += "  margin-left: 4px;";
      html += "  margin-top: 4px;";
      html += "  position: absolute;";
      html += "}";
      html += ".uil-spin-css > sinclo-loading-div > sinclo-loading-div-child {";
      html += "  width: 100%;";
      html += "  height: 100%;";
      html += "  border-radius: 15px;";
      html += "  background: #b2d251;";
      html += "}";
      html += ".uil-spin-css > sinclo-loading-div:nth-of-type(1) > sinclo-loading-div-child {";
      html += "  -ms-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -moz-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -webkit-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -o-animation: sinclo-loading-css 1s linear infinite;";
      html += "  animation: sinclo-loading-css 1s linear infinite;";
      html += "  -ms-animation-delay: 0s;";
      html += "  -moz-animation-delay: 0s;";
      html += "  -webkit-animation-delay: 0s;";
      html += "  -o-animation-delay: 0s;";
      html += "  animation-delay: 0s;";
      html += "}";
      html += ".uil-spin-css > sinclo-loading-div:nth-of-type(1) {";
      html += "  -ms-transform: translate(84px, 84px) rotate(45deg) translate(70px, 0);";
      html += "  -moz-transform: translate(84px, 84px) rotate(45deg) translate(70px, 0);";
      html += "  -webkit-transform: translate(84px, 84px) rotate(45deg) translate(70px, 0);";
      html += "  -o-transform: translate(84px, 84px) rotate(45deg) translate(70px, 0);";
      html += "  transform: translate(84px, 84px) rotate(45deg) translate(70px, 0);";
      html += "}";
      html += ".uil-spin-css > sinclo-loading-div:nth-of-type(2) > sinclo-loading-div-child {";
      html += "  -ms-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -moz-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -webkit-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -o-animation: sinclo-loading-css 1s linear infinite;";
      html += "  animation: sinclo-loading-css 1s linear infinite;";
      html += "  -ms-animation-delay: 0.12s;";
      html += "  -moz-animation-delay: 0.12s;";
      html += "  -webkit-animation-delay: 0.12s;";
      html += "  -o-animation-delay: 0.12s;";
      html += "  animation-delay: 0.12s;";
      html += "}";
      html += ".uil-spin-css > sinclo-loading-div:nth-of-type(2) {";
      html += "  -ms-transform: translate(84px, 84px) rotate(90deg) translate(70px, 0);";
      html += "  -moz-transform: translate(84px, 84px) rotate(90deg) translate(70px, 0);";
      html += "  -webkit-transform: translate(84px, 84px) rotate(90deg) translate(70px, 0);";
      html += "  -o-transform: translate(84px, 84px) rotate(90deg) translate(70px, 0);";
      html += "  transform: translate(84px, 84px) rotate(90deg) translate(70px, 0);";
      html += "}";
      html += ".uil-spin-css > sinclo-loading-div:nth-of-type(3) > sinclo-loading-div-child {";
      html += "  -ms-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -moz-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -webkit-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -o-animation: sinclo-loading-css 1s linear infinite;";
      html += "  animation: sinclo-loading-css 1s linear infinite;";
      html += "  -ms-animation-delay: 0.25s;";
      html += "  -moz-animation-delay: 0.25s;";
      html += "  -webkit-animation-delay: 0.25s;";
      html += "  -o-animation-delay: 0.25s;";
      html += "  animation-delay: 0.25s;";
      html += "}";
      html += ".uil-spin-css > sinclo-loading-div:nth-of-type(3) {";
      html += "  -ms-transform: translate(84px, 84px) rotate(135deg) translate(70px, 0);";
      html += "  -moz-transform: translate(84px, 84px) rotate(135deg) translate(70px, 0);";
      html += "  -webkit-transform: translate(84px, 84px) rotate(135deg) translate(70px, 0);";
      html += "  -o-transform: translate(84px, 84px) rotate(135deg) translate(70px, 0);";
      html += "  transform: translate(84px, 84px) rotate(135deg) translate(70px, 0);";
      html += "}";
      html += ".uil-spin-css > sinclo-loading-div:nth-of-type(4) > sinclo-loading-div-child {";
      html += "  -ms-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -moz-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -webkit-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -o-animation: sinclo-loading-css 1s linear infinite;";
      html += "  animation: sinclo-loading-css 1s linear infinite;";
      html += "  -ms-animation-delay: 0.37s;";
      html += "  -moz-animation-delay: 0.37s;";
      html += "  -webkit-animation-delay: 0.37s;";
      html += "  -o-animation-delay: 0.37s;";
      html += "  animation-delay: 0.37s;";
      html += "}";
      html += ".uil-spin-css > sinclo-loading-div:nth-of-type(4) {";
      html += "  -ms-transform: translate(84px, 84px) rotate(180deg) translate(70px, 0);";
      html += "  -moz-transform: translate(84px, 84px) rotate(180deg) translate(70px, 0);";
      html += "  -webkit-transform: translate(84px, 84px) rotate(180deg) translate(70px, 0);";
      html += "  -o-transform: translate(84px, 84px) rotate(180deg) translate(70px, 0);";
      html += "  transform: translate(84px, 84px) rotate(180deg) translate(70px, 0);";
      html += "}";
      html += ".uil-spin-css > sinclo-loading-div:nth-of-type(5) > sinclo-loading-div-child {";
      html += "  -ms-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -moz-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -webkit-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -o-animation: sinclo-loading-css 1s linear infinite;";
      html += "  animation: sinclo-loading-css 1s linear infinite;";
      html += "  -ms-animation-delay: 0.5s;";
      html += "  -moz-animation-delay: 0.5s;";
      html += "  -webkit-animation-delay: 0.5s;";
      html += "  -o-animation-delay: 0.5s;";
      html += "  animation-delay: 0.5s;";
      html += "}";
      html += ".uil-spin-css > sinclo-loading-div:nth-of-type(5) {";
      html += "  -ms-transform: translate(84px, 84px) rotate(225deg) translate(70px, 0);";
      html += "  -moz-transform: translate(84px, 84px) rotate(225deg) translate(70px, 0);";
      html += "  -webkit-transform: translate(84px, 84px) rotate(225deg) translate(70px, 0);";
      html += "  -o-transform: translate(84px, 84px) rotate(225deg) translate(70px, 0);";
      html += "  transform: translate(84px, 84px) rotate(225deg) translate(70px, 0);";
      html += "}";
      html += ".uil-spin-css > sinclo-loading-div:nth-of-type(6) > sinclo-loading-div-child {";
      html += "  -ms-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -moz-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -webkit-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -o-animation: sinclo-loading-css 1s linear infinite;";
      html += "  animation: sinclo-loading-css 1s linear infinite;";
      html += "  -ms-animation-delay: 0.62s;";
      html += "  -moz-animation-delay: 0.62s;";
      html += "  -webkit-animation-delay: 0.62s;";
      html += "  -o-animation-delay: 0.62s;";
      html += "  animation-delay: 0.62s;";
      html += "}";
      html += ".uil-spin-css > sinclo-loading-div:nth-of-type(6) {";
      html += "  -ms-transform: translate(84px, 84px) rotate(270deg) translate(70px, 0);";
      html += "  -moz-transform: translate(84px, 84px) rotate(270deg) translate(70px, 0);";
      html += "  -webkit-transform: translate(84px, 84px) rotate(270deg) translate(70px, 0);";
      html += "  -o-transform: translate(84px, 84px) rotate(270deg) translate(70px, 0);";
      html += "  transform: translate(84px, 84px) rotate(270deg) translate(70px, 0);";
      html += "}";
      html += ".uil-spin-css > sinclo-loading-div:nth-of-type(7) > sinclo-loading-div-child {";
      html += "  -ms-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -moz-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -webkit-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -o-animation: sinclo-loading-css 1s linear infinite;";
      html += "  animation: sinclo-loading-css 1s linear infinite;";
      html += "  -ms-animation-delay: 0.75s;";
      html += "  -moz-animation-delay: 0.75s;";
      html += "  -webkit-animation-delay: 0.75s;";
      html += "  -o-animation-delay: 0.75s;";
      html += "  animation-delay: 0.75s;";
      html += "}";
      html += ".uil-spin-css > sinclo-loading-div:nth-of-type(7) {";
      html += "  -ms-transform: translate(84px, 84px) rotate(315deg) translate(70px, 0);";
      html += "  -moz-transform: translate(84px, 84px) rotate(315deg) translate(70px, 0);";
      html += "  -webkit-transform: translate(84px, 84px) rotate(315deg) translate(70px, 0);";
      html += "  -o-transform: translate(84px, 84px) rotate(315deg) translate(70px, 0);";
      html += "  transform: translate(84px, 84px) rotate(315deg) translate(70px, 0);";
      html += "}";
      html += ".uil-spin-css > sinclo-loading-div:nth-of-type(8) > sinclo-loading-div-child {";
      html += "  -ms-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -moz-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -webkit-animation: sinclo-loading-css 1s linear infinite;";
      html += "  -o-animation: sinclo-loading-css 1s linear infinite;";
      html += "  animation: sinclo-loading-css 1s linear infinite;";
      html += "  -ms-animation-delay: 0.87s;";
      html += "  -moz-animation-delay: 0.87s;";
      html += "  -webkit-animation-delay: 0.87s;";
      html += "  -o-animation-delay: 0.87s;";
      html += "  animation-delay: 0.87s;";
      html += "}";
      html += ".uil-spin-css > sinclo-loading-div:nth-of-type(8) {";
      html += "  -ms-transform: translate(84px, 84px) rotate(360deg) translate(70px, 0);";
      html += "  -moz-transform: translate(84px, 84px) rotate(360deg) translate(70px, 0);";
      html += "  -webkit-transform: translate(84px, 84px) rotate(360deg) translate(70px, 0);";
      html += "  -o-transform: translate(84px, 84px) rotate(360deg) translate(70px, 0);";
      html += "  transform: translate(84px, 84px) rotate(360deg) translate(70px, 0);";
      html += "}";
      html += "sinclo-loading-area, sinclo-loading-div, sinclo-loading-div-child {";
      html += "    display: block;";
      html += "}";
      html += "sinclo-loading-area {";
      html += "  position: absolute;";
      html += "  top: 50%;";
      html += "  left: 50%;";
      html += "  width: 150px;";
      html += "  height: 150px;";
      html += "  margin-left: -75px;";
      html += "  margin-top: -75px;";
      html += "}";
      html += ".uil-spin-css > sinclo-loading-span {";
      html += "  font-family: 'メイリオ','ＭＳ Ｐ明朝',細明朝体,serif;";
      html += "  position: absolute;";
      html += "  top: 50%;";
      html += "  left: 0;";
      html += "  right: 0;";
      html += "  color: #b2d251;";
      html += "  font-size: 17px;";
      html += "  text-align: center;";
      html += "  font-weight: bold;";
      html += "  margin-top: -0.5em;";
      html += "}";
      html += "</style>";
      html += "<sinclo-loading-area>";
      html += "  <sinclo-loading-div class='uil-spin-css' style='-webkit-transform:scale(0.8)'>";
      html += "    <sinclo-loading-div><sinclo-loading-div-child></sinclo-loading-div-child></sinclo-loading-div>";
      html += "    <sinclo-loading-div><sinclo-loading-div-child></sinclo-loading-div-child></sinclo-loading-div>";
      html += "    <sinclo-loading-div><sinclo-loading-div-child></sinclo-loading-div-child></sinclo-loading-div>";
      html += "    <sinclo-loading-div><sinclo-loading-div-child></sinclo-loading-div-child></sinclo-loading-div>";
      html += "    <sinclo-loading-div><sinclo-loading-div-child></sinclo-loading-div-child></sinclo-loading-div>";
      html += "    <sinclo-loading-div><sinclo-loading-div-child></sinclo-loading-div-child></sinclo-loading-div>";
      html += "    <sinclo-loading-div><sinclo-loading-div-child></sinclo-loading-div-child></sinclo-loading-div>";
      html += "    <sinclo-loading-div><sinclo-loading-div-child></sinclo-loading-div-child></sinclo-loading-div>";
      html += "    <sinclo-loading-span>Loading...</sinclo-loading-span>";
      html += "  </sinclo-loading-div>";
      html += "</sinclo-loading-area>";
      return html;
    },
    start:  function(){
      window.clearTimeout(this.timer);
      var div = document.createElement('div');
      div.id = this.id;
      div.style.cssText = "position: fixed; top: 0; left: 0; bottom: 0; right: 0; background-color: rgba(68,68,68,0.7); z-index: 99999";
      var  html = this.loadingHtml();
      div.innerHTML = html;
      document.body.appendChild(div);
      this.flg = true; // 一度接続済みというフラグを持たせる
      this.timer = window.setTimeout(function(){
        common.load.finish();
      }, 300000);
    },
    finish: function(){
      window.clearTimeout(this.timer);
      if ( document.getElementById(this.id) ) {
        var target = document.getElementById(this.id);
        target.parentNode.removeChild(target);
        if ( document.getElementById(this.id) ) {
          this.finish();
        }
      }
    }
  }
};