module.exports = class CommonUtility {

  constructor() {
  }

  static numPad(str) {
    return ('0' + str).slice(-2);
  }

  static calcTime(startTime, endTime) {
    const end = new Date(endTime),
        start = new Date(startTime);
    if (isNaN(start.getTime()) || isNaN(end.getTime())) return false;
    const req = parseInt((end.getTime() - start.getTime()) / 1000);
    const hour = parseInt(req / 3600);
    const min = parseInt((req / 60) % 60);
    const sec = req % 60;
    return _numPad(hour) + ':' + _numPad(min) + ':' + _numPad(sec); // 表示を更新
  }

  static fullDateTime(parse) {
    var d = (isset(parse)) ? new Date(Number(parse)) : new Date();
    return d.getFullYear() + this.numPad(d.getMonth() + 1) +
        this.numPad(d.getDate()) +
        this.numPad(d.getHours()) + this.numPad(d.getMinutes()) +
        this.numPad(d.getSeconds()) +
        this.numPad(Number(String(d.getMilliseconds()).slice(0, 2)));
  }

  static formatDateParse(parse) {
    const d = (isset(parse)) ? new Date(Number(parse)) : new Date();
    return d.getFullYear() + '/' + this.numPad(d.getMonth() + 1) + '/' +
        this.numPad(d.getDate()) + ' ' + this.numPad(d.getHours()) + ':' +
        this.numPad(d.getMinutes()) + ':' + this.numPad(d.getSeconds());
  }

  static makeUserId() {
    const d = new Date();
    return d.getFullYear() + ('0' + (d.getMonth() + 1)).slice(-2) +
        ('0' + d.getDate()).slice(-2) + d.getHours() + d.getMinutes() +
        d.getSeconds() + Math.floor(Math.random() * 1000);
  }

  static getNow() {
    const d = new Date();
    return '【' + d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds() +
        '】';
  }

  static isset(a) {
    if (a === null || a === '' || a === undefined || String(a) === 'null' ||
        String(a) === 'undefined') {
      return false;
    }
    if (typeof a === 'object') {
      var keys = Object.keys(a);
      return (Object.keys(a).length !== 0);
    }
    return true;
  }

  static calcTime(obj) {
    const now = new Date(),
        start = new Date(Number(obj.time)),
        req = parseInt((now.getTime() - start.getTime()) / 1000);
    return Number(req);
  }

  static objectSort(object) {
    //戻り値用新オブジェクト生成
    var sorted = {};
    //キーだけ格納し，ソートするための配列生成
    var array = [];
    //for in文を使用してオブジェクトのキーだけ配列に格納
    for (var key in object) {
      //指定された名前のプロパティがオブジェクトにあるかどうかチェック
      if (object.hasOwnProperty(key)) {
        //if条件がtrueならば，配列の最後にキーを追加する
        array.push(key);
      }
    }
    //配列のソート
    array.sort();
    //配列の逆ソート
    //array.reverse();

    //キーが入った配列の長さ分だけfor文を実行
    for (var i = 0; i < array.length; i++) {
      /*戻り値用のオブジェクトに
      新オブジェクト[配列内のキー] ＝ 引数のオブジェクト[配列内のキー]を入れる．
      配列はソート済みなので，ソートされたオブジェクトが出来上がる*/
      sorted[array[i]] = object[array[i]];
    }
    //戻り値にソート済みのオブジェクトを指定
    return sorted;
  }

  static trimFrame(str) {
    return str.replace('_frame', '');
  }

  static isNumber(n) {
    return RegExp(/^([+\-])?\d+(.\d+)?$/).test(n);
  }

  static extend(obj1, obj2) {
    for (const key in obj2) {
      if (obj2.hasOwnProperty(key)) {
        obj1[key] = obj2[key];
      }
    }
    return obj1;
  }

};