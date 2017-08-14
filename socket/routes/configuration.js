var express = require('express');
var router = express.Router();

router.get('/reloadCompanyData',function(req, res, next){
  socket.getCompanyKey();
  res.header('Content-Type', 'text/plain; charset=utf-8');
  res.send('OK');
});

module.exports = router;