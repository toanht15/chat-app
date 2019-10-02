var express = require('express');
var path = require('path');
var favicon = require('serve-favicon');
var logger = require('morgan');
var cookieParser = require('cookie-parser');
var bodyParser = require('body-parser');

var routes = require('./routes/index');
var users = require('./routes/users');
var settings = require('./routes/settings');
var api = require('./routes/api');
if (process.env.WS_PORT) {
  console.log('process.env.WS_PORT is set');
  var socket = require('./routes/module/socket_ctrl');
} else {
  console.log('process.env.WS_PORT is undefined.');
}
var CommonUtil = require('./routes/module/class/util/common_utility');

// Timezone
process.env.TZ = 'Asia/Tokyo';

const protectCfg = {
  production: process.env.NODE_ENV === 'production', // if production is false, detailed error messages are exposed to the client
  clientRetrySecs: 1, // Client-Retry header, in seconds (0 to disable) [default 1]
  sampleInterval: 50, // sample rate, milliseconds [default 5]
  maxEventLoopDelay: 2000, // maximum detected delay between event loop ticks [default 42]
  maxHeapUsedBytes: 0, // maximum heap used threshold (0 to disable) [default 0]
  maxRssBytes: 0, // maximum rss size threshold (0 to disable) [default 0]
  errorPropagationMode: true // dictate behavior: take over the response
                              // or propagate an error to the framework [default false]
};

process.on('uncaughtException', function(err) {
  CommonUtil.errorLog(err);
  CommonUtil.errorLog(err.stack);
});

const app = express();
const protect = require('overload-protection')('express', protectCfg);
app.use(protect);

// view engine setup
app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'jade');

// uncomment after placing your favicon in /public
//app.use(favicon(path.join(__dirname, 'public', 'favicon.ico')));
app.use(logger('dev'));
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: false }));
app.use(cookieParser());
app.use(require('node-sass-middleware')({
  src: path.join(__dirname, 'public'),
  dest: path.join(__dirname, 'public'),
  indentedSyntax: true,
  sourceMap: true
}));
app.use(express.static(path.join(__dirname, 'webroot')));

app.use('/', routes);
app.use('/users', users);
app.use('/settings', settings);
app.use('/api', api);

// catch 404 and forward to error handler
app.use(function(req, res, next) {
  var err = new Error('Not Found');
  err.status = 404;
  next(err);
});
// error handlers

// development error handler
// will print stacktrace
if (app.get('env') === 'development') {
  app.use(function(err, req, res, next) {
    res.status(err.status || 500);
    res.render('error', {
      message: err.message,
      error: err
    });
  });
}

// production error handler
// no stacktraces leaked to user
app.use(function(err, req, res, next) {
  res.status(err.status || 500);
  res.render('error', {
    message: err.message,
    error: {}
  });
});

module.exports = app;
