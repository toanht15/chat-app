var gulp = require('gulp'),
    sass = require('gulp-sass'),
    cssnext = require('gulp-cssnext'),
    path = {
      css: '../contact_site/app/webroot/css/',
      scss: './scss/',
      js: 'socketJs/websocket/*.js',
      minjs: '../socket/webroot/websocket/'
    };

var uglify = require('gulp-uglify'),
    rename = require("gulp-rename"),
    uglifyOpt = {
      mangle: true,
      comments: false
    };

gulp.task('scss-compile', function(){
  return gulp.src(path.scss + '**/*.scss')
    .pipe(sass({outputStyle: 'expanded'}))
    .on('error', function(err) {
      console.log(err.message);
    })
    .pipe(cssnext())
    .pipe(gulp.dest(path.css))
});

gulp.task('js-minify', function(){
  return gulp.src(path.js)
    .pipe(uglify(uglifyOpt))
    .pipe(rename({
      extname: '.min.js'
    }))
    .pipe(gulp.dest(path.minjs))
});

gulp.task('watch', function(){
  gulp.watch([path.scss + '**/*.scss'], ['scss-compile']);
  gulp.watch([path.js], ['js-minify']);
});

gulp.task('dev', ['scss-compile', 'js-minify', 'watch']);
