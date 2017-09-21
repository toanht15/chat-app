<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/08/08
 * Time: 12:55
 */
$config['default'] = [
  'widget' => [
    'common' => [
      /**
       * 表示する条件
       */
      "display_type" => "2",

      /**
       * 表示タイミング（チャット契約時は3を選択できない）
       */
      "showTiming" => "4",

      /**
       * 最大化する条件
       */
      "showTime" => "1",

      /**
       * 最大表示時間
       */
      "maxShowTime" => "3",

      /**
       * 表示位置
       */
      "showPosition" => "1",

      //ウィジットサイズ対応
      /**
       * ウィジットサイズ
       */
      "widgetSizeType" => "2",
      //ウィジットサイズ対応

      //最小化時デザイン対応
      /**
       * 最小化時のデザイン
       */
      "minimizeDesignType" => "1",
      //最小化時デザイン対応

      //閉じるボタン対応
      /**
       * 閉じるボタン有効無効
       */
      "closeButtonSetting" => "1",
      /**
       * 小さなバナー表示有効無効
       */
      "closeButtonModeType" => "2",
      /**
       * バナーテキスト
       */
      "bannertext" => "チャットで相談",
      //閉じるボタン対応

      /**
       * トップタイトル
       */
      "title" => "タイトルを入力して下さい",

      /**
       * 企業名を表示する
       */
      "showSubtitle" => "1",

      /**
       * 企業名
       */
      "subTitle" => "企業名を入力して下さい",

      /**
       * 説明文を表示する
       */
      "showDescription" => "1",

      /**
       * 説明文
       */
      "description" => "説明文を入力して下さい",

      /**
       * メインカラー
       */
      "mainColor" => "#ABCD05",

      /**
       * 文字色
       */
      "stringColor" => "#FFFFFF",

        /**
         * 画像の設定
         */
      "mainImage" => C_NODE_SERVER_ADDR.C_NODE_SERVER_FILE_PORT."/img/widget/op02.jpg",
      /**
       * 画像を表示する
       */
      "showMainImage"=>"1",

      /**
       * 角丸
       */
      "radiusRatio"=>"8",

      /**
       * 背景の影
       */
      "boxShadow"=>"5",
    ],
    "chat" => [
      /**
       * ラジオボタン選択動作
       */
      "chatRadioBehavior"=>"1",

      /**
       * 消費者側送信アクション
       */
      "chatTrigger"=>"1",

      /**
       * 担当者表示
       */
      "showName"=>"2",

      /**
       * スマートフォンウィジェット表示
       */
      "spShowFlg"=>"1",

      /**
       * シンプル表示
       */
      "spHeaderLightFlg"=>"1",

      /**
       * 自動最大化の制御
       */
      "spAutoOpenFlg"=>"1"
    ],
    "sharing" => [
      /**
       * 電話番号
       */
      "tel"=>"03-1234-5678",

    /**
     * 受付時間を表示する
     */
    "displayTimeFlg"=>"1",

    /**
     * 受付時間
     */
    "timeText"=>"平日9:00-18:00",

    /**
     * ウィジェット本文
     * ※ 文字列内にインデントのための半角文字列の挿入厳禁！
     */
    "content"=>"無料相談はこちらの番号まで！

係りの者に下記番号をお伝えください。

分かりやすいと大変好評な
次世代型オンラインサポートを
是非ご体感ください。"
    /** ここまで */
    ]
  ]
];