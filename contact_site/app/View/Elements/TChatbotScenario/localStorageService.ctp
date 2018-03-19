<script type="text/javascript">
'use strict';

sincloApp.factory('LocalStorageService', function() {

  var storageData = localStorage.getItem('chatbotVariables');
  var jsonData = {};
  if(typeof storageData !== 'undefined' && storageData !== null && storageData !== "") {
    jsonData = JSON.parse(storageData);
  }

  return {
    _data: jsonData,
    save: function() {
      var data = JSON.stringify(this._data);
      localStorage.setItem('chatbotVariables', data);
    },
    load: function() {
      var storageData = localStorage.getItem('chatbotVariables');
      if(typeof storageData !== 'undefined' && storageData !== null && storageData !== "") {
        this._data = JSON.parse(storageData);
      }
    },
    getItem: function(key) {
      return this._data[key];
    },
    setItem: function(key, value) {
      this._data[key] = value.toString(); // 文字列に変換して LocalStorage に格納する
      this.save();
    }
  };
});

</script>
