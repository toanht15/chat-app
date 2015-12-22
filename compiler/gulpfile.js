var gulp = require('gulp'),
    sass = require('gulp-sass'),
    cssnext = require('gulp-cssnext'),
    path = {
      css: '../contact_site/app/webroot/css/',
      scss: './scss/'
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

gulp.task('scss', function(){
  gulp.watch([path.scss + '**/*.scss'], ['scss-compile']);
});

