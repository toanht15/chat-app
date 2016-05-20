var loading = {
  flg: false,
  ev: function(fc){
    if ( !this.flg ) {
      fc();
      this.flg = true;
    }
  }
};

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
