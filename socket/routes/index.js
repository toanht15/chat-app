var express = require('express');
var router = express.Router();

/* GET home page. */
router.get('/main', function(req, res, next) {
  res.render('index', {
    title: "メインページ"
  });
});

router.get('/sub', function(req, res, next) {
  res.render('sub', {
    title: "サブページ"
  });
});


module.exports = router;
