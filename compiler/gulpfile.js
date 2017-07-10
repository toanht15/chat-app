var gulp = require('gulp'),
    sass = require('gulp-sass'),
    cssnext = require('gulp-cssnext'),
    jade = require('gulp-jade'),
    path = {
      css: '../contact_site/app/webroot/css/',
      scss: './scss/',
      adCss: '../admin_site/app/webroot/css/',
      adScss: './admin_scss/',
      js: 'socketJs/websocket/*.js',
      minjs: '../socket/webroot/websocket/',
      jade: '../socket/views/*.jade',
      outOfJs: '../socket/webroot/',
      socketSass: '../socket/public/stylesheets/',
      outOfCssToSocket: '../socket/webroot/css/'
    };

var uglify = require('gulp-uglify'),
    rename = require("gulp-rename"),
    uglifyOpt = {
      mangle: true,
      comments: false,
      compress: {
        drop_console: false
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

gulp.task('contact-scss-compile', function(){
  return gulp.src(path.scss + '**/*.scss')
    .pipe(sass({outputStyle: 'expanded'}))
    .on('error', function(err) {
      console.log(err.message);
    })
    .pipe(cssnext())
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

gulp.task('js-minify', function(){
  return gulp.src(path.js)
    .pipe(uglify(uglifyOpt))
    .pipe(rename({
      extname: '.min.js'
    }))
    .pipe(gulp.dest(path.minjs));
});

gulp.task('scss-compile', ['admin-scss-compile','contact-scss-compile','socket-sass-compile'] );

gulp.task('jade-compile', function(){
  return gulp.src(path.jade)
    .pipe(jade({
      extname: '.min.js'
    }))
    .pipe(gulp.dest(path.outOfJs));
});

gulp.task('watch', function(){
  gulp.watch([path.adScss + '**/*.scss'], ['admin-scss-compile']);
  gulp.watch([path.scss + '**/*.scss'], ['contact-scss-compile']);
  gulp.watch([path.socketSass + '**/*.sass'], ['socket-sass-compile']);
  gulp.watch([path.js], ['js-minify']);
  gulp.watch([path.jade], ['jade-compile']);
});

gulp.task('dev', ['scss-compile', 'js-minify', 'jade-compile', 'watch']);
