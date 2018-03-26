<script type="text/javascript">
'use strict';

sincloApp.service('LocalStorageService', function() {

  return {
    getData: function(storageKey) {
      var storageData = localStorage.getItem(storageKey);
      return JSON.parse(storageData);
    },
    /**
     * ローカルストレージのデータ取得
     * @param String storageKey ローカルストレージのキー
     * @param String key        取得したいJSONデータ内のキー
     */
    getItem: function(storageKey, key) {
      var storageData = localStorage.getItem(storageKey);
      var jsonData = JSON.parse(storageData);
      return jsonData[key];
    },
    /**
     * ローカルストレージのデータ設定
     * @param String storageKey ローカルストレージのキー
     * @param Object param      追加したいJSONデータ
     */
    setItem: function(storageKey, param) {
      var storageData = localStorage.getItem(storageKey);
      var jsonData = JSON.parse(storageData);
      angular.forEach(param, function(value) {
        jsonData[value[0]] = value[1];
      });
      localStorage.setItem(storageKey, JSON.stringify(jsonData));
    },
    /**
     * ローカルストレージのデータ一部削除
     * @param String storageKey ローカルストレージキー
     * @param String key        削除したいJSONデータ内のキー
     */
    removeItem: function(storageKey, key) {
      if (!!key) {
        var storageData = localStorage.getItem(storageKey);
        var jsonData = JSON.parse(storageData);
        delete jsonData[key];
        localStorage.setItem(storageKey, JSON.stringify(jsonData));
      }
    },
    /**
     * ローカルストレージのデータ削除
     * @param String storageKey ローカルストレージキー
     */
    remove: function(storageKey) {
      localStorage.removeItem(storageKey);
    }
  };
});

</script>
