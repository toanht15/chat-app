<script type="text/javascript">
var userAgentChk = (function(){
    function _pc_chk(ua){
      var os;
      // http://www9.plala.or.jp/oyoyon/html/script/platform.html
      if (ua.match(/Win(dows )?NT 10\.0/)) {
        os = "Windows 10"; // Windows 10 の処理
      }
      else if (ua.match(/Win(dows )?NT 6\.3/)) {
        os = "Windows 8.1"; // Windows 8.1 の処理
      }
      else if (ua.match(/Win(dows )?NT 6\.2/)) {
        os = "Windows 8"; // Windows 8 の処理
      }
      else if (ua.match(/Win(dows )?NT 6\.1/)) {
        os = "Windows 7"; // Windows 7 の処理
      }
      else if (ua.match(/Win(dows )?NT 6\.0/)) {
        os = "Windows Vista"; // Windows Vista の処理
      }
      else if (ua.match(/Win(dows )?NT 5\.2/)) {
        os = "Windows Server 2003";  // Windows Server 2003 の処理
      }
      else if (ua.match(/Win(dows )?(NT 5\.1|XP)/)) {
        os = "Windows XP"; // Windows XP の処理
      }
      else if (ua.match(/Win(dows)? (9x 4\.90|ME)/)) {
        os = "Windows ME"; // Windows ME の処理
      }
      else if (ua.match(/Win(dows )?(NT 5\.0|2000)/)) {
        os = "Windows 2000"; // Windows 2000 の処理
      }
      else if (ua.match(/Win(dows )?98/)) {
        os = "Windows 98"; // Windows 98 の処理
      }
      else if (ua.match(/Win(dows )?NT( 4\.0)?/)) {
        os = "Windows NT"; // Windows NT の処理
      }
      else if (ua.match(/Win(dows )?95/)) {
        os = "Windows 95"; // Windows 95 の処理
      }
      else if (ua.match(/Mac|PPC/)) {
        os = "Mac OS"; // Macintosh の処理
      }
      else if (ua.match(/Linux/)) {
        os = "Linux"; // Linux の処理
      }
      else if (ua.match(/^.*\s([A-Za-z]+BSD)/)) {
        os = RegExp.$1; // BSD 系の処理
      }
      else if (ua.match(/SunOS/)) {
        os = "Solaris"; // Solaris の処理
      }
      else if (ua.indexOf(/iPhone/) > 0) {
        os = "iPhone"; // iPhone の処理
      }
      else if (ua.indexOf(/iPad/) > 0) {
        os = "iPad"; // iPad の処理
      }
      else if (ua.indexOf(/iPod/) > 0) {
        os = "iPod"; // iPod の処理
      }
      else if (ua.indexOf(/Android/) > 0) {
        os = "Android"; // Android の処理
      }
      else {
        os = "unknown"; // 上記以外 OS の処理
      }
      return os;
    }

    // http://www.red.oit-net.jp/tatsuya/java/indexof.htm
    function _get_var(ua, myKey, myEnd){
      myStart = ua.indexOf( myKey ) + myKey.length;
      myEnd = ua.indexOf( myEnd, myStart );
      return " (var." + ua.substring( myStart, myEnd ) + ")";
    }

    function _browser_chk(ua){
      var name = 'unknown', ua = ua.toLowerCase();

      if (ua.indexOf("msie") != -1 ) {
          name = 'Internet Explorer' + _get_var(ua, "msie ", ";");
      } else if ( ua.indexOf('trident/7') != -1){
          name = 'IE' + _get_var(ua, "rv:", ")");
      } else if (ua.indexOf('edge') != -1 ) {
          name = 'Edge' + _get_var(ua, "edge/", ";");
      } else if (ua.indexOf('opera') != -1 ) {
          name = 'Opera' + _get_var(ua, "opera/", ";");
      } else if (ua.indexOf('opr') != -1){
          name = 'Opera' + _get_var(ua, "opr/", ";");
      } else if (ua.indexOf('chrome') != -1){
          name = 'Chrome' + _get_var(ua, "chrome/", " ");
      } else if (ua.indexOf('safari') != -1){
          name = 'Safari' + _get_var(ua, "version/", " ");
      } else if (ua.indexOf('firefox') != -1){
          name = 'Firefox' + _get_var(ua, "firefox/", ";");
      }
      return name;
    }

    return {
      init: function(ua){
        ua = ua + ";";
        return _pc_chk(ua) + ", " + _browser_chk(ua);
      }
    };

}());
</script>
