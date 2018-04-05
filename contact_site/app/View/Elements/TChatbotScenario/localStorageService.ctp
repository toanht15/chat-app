<script type="text/javascript">
'use strict';

sincloApp.service('LocalStorageService', function() {

  return {
    /**
     * ローカルストレージのデータ一括取得
     * @param String storageKey ローカルストレージのキー
     */
    getData: function(storageKey) {
      var storageData = localStorage.getItem(storageKey);
      return JSON.parse(storageData) || {};
    },
    /**
     * ローカルストレージのデータ取得
     * @param String storageKey ローカルストレージのキー
     * @param String key        取得したいJSONデータ内のキー
     */
    getItem: function(storageKey, key) {
      var storageData = localStorage.getItem(storageKey);
      var jsonData = JSON.parse(storageData) || {};
      return jsonData[key];
    },
    /**
     * ローカルストレージのデータ一括更新
     * @param String storageKey ローカルストレージのキー
     * @param Object data       保存したいJSONデータ
     */
    setData: function(storageKey, data) {
      localStorage.setItem(storageKey, data);
    },
    /**
     * ローカルストレージのデータ追加・更新
     * @param String storageKey ローカルストレージのキー
     * @param Object param      追加したいJSONデータ
     */
    setItem: function(storageKey, params) {
      var storageData = localStorage.getItem(storageKey);
      var jsonData = JSON.parse(storageData) || {};

      angular.forEach(params, function(param) {
        jsonData[param.key] = param.value;
      })
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
        var jsonData = JSON.parse(storageData) || {};
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
