var express = require('express');
var router = express.Router();

/* GET home page. */
router.get('/frame/:cond', function(req, res, next) {
  var cond = JSON.parse(decodeURIComponent(req.params.cond));
  res.render('frame', {
    title: "フレーム",
    params: JSON.stringify(cond.data),
    site: JSON.stringify(cond.site),
    img: cond.site.files + "/img/sync/"
  });
});

/* GET home page. */
router.get('/docFrame/:cond', function(req, res, next) {
  var cond = JSON.parse(decodeURIComponent(req.params.cond));
  res.render('docFrame', {
    title: "資料共有フレーム",
    params: JSON.stringify(cond.data),
    site: JSON.stringify(cond.site),
    img: cond.site.files + "/img/sync/"
  });
});

module.exports = router;
