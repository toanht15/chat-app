<?php
/**
 * MAdministratorsController controller.
 * 契約登録、更新
 */
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
class AgreementsController extends AppController {

  /* *
  * 一覧画面
  * @return void
  * */
  public function index() {
  }

    /* *
  * jsファイル追加
  * @return void
  * */
  public function add() {
    $this->autoRender = FALSE;
    $name = $this->name;
    // 作成するファイル名の指定
    $file_name ="C:/Project/sinclo/socket/webroot/client/{$name}.js";
    // ファイルの存在確認
    if( !file_exists($file_name) ){
      // ファイル作成
      touch( $file_name );
    }else{
      // すでにファイルが存在する為エラーとする
      echo('Warning - ファイルが存在しています。 file name:['.$file_name.']');
      exit();
    }

    // ファイルのパーティションの変更
    chmod( $file_name, 0666 );
    echo('Info - ファイル作成完了。 file name:['.$file_name.']');

    $fp = fopen("C:/Project/sinclo/socket/webroot/client/{$name}.js", 'w');
    fwrite($fp,
      "<!--
        // 'use strict';
        var info;
        (function(){
          info = {
            dataset: {},
            site: {
              key: $name,
              socket: 'http://socket.localhost:9090',
              files: 'http://socket.localhost:8080',
              webcam_view: 'http://socket.localhost:8080/webcam.html'
            }
          };
          var b = document.getElementsByTagName('body')[0],
          l = [
            info.site.files + '/websocket/jquery-2.2.0.min.js',
            info.site.socket + '/socket.io/socket.io.js',
            info.site.files + '/websocket/common.min.js',
            info.site.files + '/websocket/sinclo.min.js'
          ],
          i = 0,
          createElm = function (u){
            var s = document.createElement('script');
            s.type = 'text/javascript';
            s.src = u;
            s.charset='UTF-8';
            b.appendChild(s);
            i ++;
            s.onload = function(){
            if ( l[i] !== undefined ) createElm(l[i]);
            }
          };

          createElm(l[i]);

        }());
      //-->
    ");
    fclose($fp);

    $this->render('/Agreements/index');
  }
}