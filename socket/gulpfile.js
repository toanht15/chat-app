var gulp = require('gulp'),
    webserver = require('gulp-webserver'),
    jade = require('gulp-jade'),
    plumber = require('gulp-plumber'),
    express = require('gulp-express'),
    sass = require('gulp-sass'),
    cssnext = require('gulp-cssnext'),
    path = {
      css: '../contact_site/app/webroot/css/',
      scss: '../compiler/scss/'
    };

//Webサーバー
gulp.task('webserver', function() {
  gulp.src('./webroot') //Webサーバーで表示するサイトのルートディレクトリを指定
    .pipe(webserver({
      host: process.env.DEV_HOST || 'socket.localhost',
      port: process.env.DEV_PORT || 8080,
      livereload: { enable: true, port: 35730 }, //ライブリロードを有効に
      //directoryListing: true //ディレクトリ一覧を表示するか。オプションもあり
    }));

    express.run({
        file: './app.js'
    });
});

gulp.task('jade-compile', function() {
  gulp.src('./views/*.jade')
    .pipe(plumber())
    .pipe(jade({pretty:true}))
    .pipe(gulp.dest('./webroot/'))
});

gulp.task('scss-compile', function(){
  gulp.src(path.scss + "**/*.scss")
    .pipe(sass({outputStyle: 'expanded'}))
    .on('error', function(err) {
      console.log(err.message);
    })
    .pipe(cssnext())
    .pipe(gulp.dest(path.css))
});

gulp.task('watch', function(){
	gulp.watch(['./views/*.jade'], ['jade-compile']);
	gulp.watch([path.scss + "*.scss"], ['scss-compile']);
	gulp.watch(['./routes/*.js']);
});

/**
 * デフォルトタスク
 *
 * コマンド'gulp'で実行される
 */
gulp.task('serve', ['scss-compile', 'jade-compile', 'webserver', 'watch']);
gulp.task('default', ['webserver','watch']);
