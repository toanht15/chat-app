var gulp = require('gulp'),
  sass = require('gulp-sass'),
  cssnext = require('gulp-cssnext'),
  jade = require('gulp-jade'),
  gzip = require('gulp-gzip'),
  sourcemaps = require('gulp-sourcemaps'),
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
    .pipe(sourcemaps.init())
    .pipe(gulp.dest(path.minjs))
    .pipe(uglify(uglifyOpt))
    .on('error', function(e){console.log(e);})
    .pipe(rename({
      extname: '.min.js'
    }))
    .pipe(sourcemaps.write('maps'))
    .pipe(gulp.dest(path.minjs));
});

gulp.task('compress-gzip-js', function(){
  return gulp.src(path.minjsFile)
    .pipe(gzip({ gzipOptions: { level: 9 }, deleteMode: path.minjs }))
    .pipe(gulp.dest(path.minjs));
});

gulp.task('scss-compile',
    gulp.series('admin-scss-compile', 'compress-gzip-admin-css',
        'contact-scss-compile', 'compress-gzip-contact-css',
        'socket-sass-compile', 'compress-gzip-socket-css'));

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
  gulp.watch([path.adScss + '**/*.scss'],
      gulp.task(gulp.series('admin-scss-compile', 'compress-gzip-admin-css')));
  gulp.watch([path.scss + '**/*.scss'], gulp.task(
      gulp.series('contact-scss-compile', 'compress-gzip-contact-css')));
  gulp.watch([path.socketSass + '**/*.sass'], gulp.task(
      gulp.series('socket-sass-compile', 'compress-gzip-socket-css')));
  gulp.watch([path.js], gulp.task('js-minify-dev'));
  gulp.watch([path.jade],
      gulp.task(gulp.series('jade-compile', 'compress-gzip-jade')));
});

gulp.task('js-minify', gulp.series('minify-js', 'compress-gzip-js'));

gulp.task('js-minify-dev', gulp.series('minify-js-dev', 'compress-gzip-js'));

gulp.task('dev', gulp.series('scss-compile', 'js-minify-dev', 'jade-compile',
    'compress-gzip-jade', 'watch'));

gulp.task('compile-all',
    gulp.series('scss-compile', 'js-minify', 'jade-compile'));
