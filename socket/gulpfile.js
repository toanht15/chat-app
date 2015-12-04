var gulp = require('gulp'),
    webserver = require('gulp-webserver'),
    jade = require('gulp-jade'),
    plumber = require('gulp-plumber'),
    express = require('gulp-express');

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

gulp.task('watch', function(){
	gulp.watch(['./views/*.jade'], ['jade-compile']);
	gulp.watch(['./routes/*.js']);
});

/**
 * デフォルトタスク
 *
 * コマンド'gulp'で実行される
 */
gulp.task('serve', ['jade-compile', 'webserver', 'watch']);
gulp.task('default', ['webserver','watch']);