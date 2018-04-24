var gulp = require('gulp'),
  sass = require('gulp-sass'),
  cssnext = require('gulp-cssnext'),
  jade = require('gulp-jade'),
  gzip = require('gulp-gzip'),
  runSequence = require('run-sequence'),
  path = {
    css: '../contact_site/app/webroot/css/',
    cssFile: '../contact_site/app/webroot/css/*.css',
    scss: './scss/',
    adCss: '../admin_site/app/webroot/css/',
    adCssFile: '../admin_site/app/webroot/css/*.css',
    adScss: './admin_scss/',
    js: 'socketJs/websocket/*.js',
    minjs: '../socket/webroot/websocket/',
    minjsFile: '../socket/webroot/websocket/*.min.js',
    jade: '../socket/views/*.jade',
    outOfJs: '../socket/webroot/',
    outOfJsFile: '../socket/webroot/*.min.js',
    socketSass: '../socket/public/stylesheets/',
    outOfCssToSocket: '../socket/webroot/css/',
    outOfCssToSocketFile: '../socket/webroot/css/*.css'
  };

var uglify = require('gulp-uglify'),
  rename = require("gulp-rename"),
  uglifyOpt = {
    mangle: true,
    output:{
      comments: /^!/
    },
    compress: {
      drop_console: true
    }
  };

gulp.task('admin-scss-compile', function(){
  return gulp.src(path.adScss + '**/*.scss')
    .pipe(sass({outputStyle: 'expanded'}))
    .on('error', function(err) {
      console.log(err.message);
    })
    .pipe(cssnext())
    .pipe(gulp.dest(path.adCss));
});

gulp.task('compress-gzip-admin-css', function(){
  return gulp.src(path.adCssFile)
    .pipe(gzip({ gzipOptions: { level: 9 }, deleteMode: path.adCss }))
    .pipe(gulp.dest(path.adCss));
});

gulp.task('contact-scss-compile', function(){
  return gulp.src(path.scss + '**/*.scss')
    .pipe(sass({outputStyle: 'expanded'}))
    .on('error', function(err) {
      console.log(err.message);
    })
    .pipe(cssnext())
    .pipe(gulp.dest(path.css));
});

gulp.task('compress-gzip-contact-css', function(){
  return gulp.src(path.cssFile)
    .pipe(gzip({ gzipOptions: { level: 9 }, deleteMode: path.css }))
    .pipe(gulp.dest(path.css));
});

gulp.task('socket-sass-compile', function(){
  return gulp.src(path.socketSass + '**/*.sass')
    .pipe(sass({outputStyle: 'expanded'}))
    .on('error', function(err) {
      console.log(err.message);
    })
    .pipe(cssnext())
    .pipe(gulp.dest(path.outOfCssToSocket));
});

gulp.task('compress-gzip-socket-css', function(){
  return gulp.src(path.outOfCssToSocketFile)
    .pipe(gzip({ gzipOptions: { level: 9 }, deleteMode: path.outOfCssToSocket }))
    .pipe(gulp.dest(path.outOfCssToSocket));
});

gulp.task('minify-js', function(){
  return gulp.src(path.js)
    .pipe(uglify(uglifyOpt))
    .pipe(rename({
      extname: '.min.js'
    }))
    .pipe(gulp.dest(path.minjs));
});

gulp.task('minify-js-dev', function(){
  //console.logを表示した状態にする
  uglifyOpt.compress.drop_console = false;
  uglifyOpt.compress.drop_debugger = false;
  return gulp.src(path.js)
    .pipe(uglify(uglifyOpt))
    .pipe(rename({
      extname: '.min.js'
    }))
    .pipe(gulp.dest(path.minjs));
});

gulp.task('compress-gzip-js', function(){
  return gulp.src(path.minjsFile)
    .pipe(gzip({ gzipOptions: { level: 9 }, deleteMode: path.minjs }))
    .pipe(gulp.dest(path.minjs));
});

gulp.task('scss-compile', ['admin-scss-compile', 'compress-gzip-admin-css', 'contact-scss-compile', 'compress-gzip-contact-css', 'socket-sass-compile', 'compress-gzip-socket-css'] );

gulp.task('jade-compile', function(){
  return gulp.src(path.jade)
    .pipe(jade({
      extname: '.min.js'
    }))
    .pipe(gulp.dest(path.outOfJs));
});

gulp.task('compress-gzip-jade', function(){
  return gulp.src(path.outOfJsFile)
    .pipe(gzip({ gzipOptions: { level: 9 } }))
    .pipe(gulp.dest(path.outOfJs));
});

gulp.task('watch', function(){
  gulp.watch([path.adScss + '**/*.scss'], ['admin-scss-compile', 'compress-gzip-admin-css']);
  gulp.watch([path.scss + '**/*.scss'], ['contact-scss-compile', 'compress-gzip-contact-css']);
  gulp.watch([path.socketSass + '**/*.sass'], ['socket-sass-compile', 'compress-gzip-socket-css']);
  gulp.watch([path.js], ['minify-js-dev', 'compress-gzip-js']);
  gulp.watch([path.jade], ['jade-compile', 'compress-gzip-jade']);
});

gulp.task('js-minify', function(){
  runSequence('minify-js', 'compress-gzip-js');
});

gulp.task('js-minify-dev', function(){
  runSequence('minify-js-dev', 'compress-gzip-js');
});

gulp.task('dev', function(){
  runSequence('scss-compile', 'js-minify-dev', 'jade-compile', 'compress-gzip-jade', 'watch');
});

gulp.task('compile-all', ['scss-compile', 'js-minify', 'jade-compile']);
