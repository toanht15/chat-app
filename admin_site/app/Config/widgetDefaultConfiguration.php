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
      "closeButtonSetting" => "2",
      /**
       * 小さなバナー表示有効無効
       */
      "closeButtonModeType" => "1",
      /**
       * バナーテキスト
       */
      "bannertext" => "お問い合わせはこちら",
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

      /* カラー設定styat */
      /**
       * 0.通常設定・高度設定
       */
      "colorSettingType" => "1",

      /**
       * 1.メインカラー
       */
      "mainColor" => "#ABCD05",

      /**
       * 2.タイトル文字色
       */
      "stringColor" => "#FFFFFF",

      /**
       * 3.吹き出し文字色
       */
      "messageTextColor" => "#333333",

      /**
       * 4.その他文字色
       */
      "otherTextColor" => "#666666",

      /**
       * 5.ウィジェット枠線色
       */
      "widgetBorderColor" => "#E8E7E0",

      /**
       * 6.吹き出し枠線色
       */
      "chatTalkBorderColor" => "#C9C9C9",

      /**
       * 7.企業名文字色
       */
      "subTitleTextColor" => "#ABCD05",

      /**
       * 8.説明文文字色
       */
      "descriptionTextColor" => "#666666",

      /**
       * 9.チャットエリア背景色
       */
      "chatTalkBackgroundColor" => "#FFFFFF",

      /**
       * 10.企業名担当者名文字色
       */
      "cNameTextColor" => "#ABCD05",

      /**
       * 11.企業側吹き出し文字色
       */
      "reTextColor" => "#333333",

      /**
       * 12.企業側吹き出し背景色
       */
      "reBackgroundColor" => "#F6FAE6",

      /**
       * 13.企業側吹き出し枠線色
       */
      "reBorderColor" => "none",

      /**
       * 15.訪問者側吹き出し文字色
       */
      "seTextColor" => "#333333",

      /**
       * 16.訪問者側吹き出し背景色
       */
      "seBackgroundColor" => "#E7E7E7",

      /**
       * 17.訪問者側吹き出し枠線色
       */
      "seBorderColor" => "none",

      /**
       * 19.メッセージエリア背景色
       */
      "chatMessageBackgroundColor" => "#FFFFFF",

      /**
       * 20.メッセージBOX文字色
       */
      "messageBoxTextColor" => "#666666",

      /**
       * 21.メッセージBOX背景色
       */
      "messageBoxBackgroundColor" => "#FFFFFF",

      /**
       * 22.メッセージBOX枠線色
       */
      "messageBoxBorderColor" => "#D4D4D4",

      /**
       * 24.送信ボタン文字色
       */
      "chatSendBtnTextColor" => "#FFFFFF",

      /**
       * 25.送信ボタン背景色
       */
      "chatSendBtnBackgroundColor" => "#ABCD05",

      /**
       * 26.ウィジット内枠線色
       */
      "widgetInsideBorderColor" => "#E8E7E0",

      /* カラー設定end */

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
       * 吹き出しデザイン
       * 1: BOX型、2: 吹き出し型
       */
      "chatMessageDesignType"=>"2",

      /**
       * メッセージ表示時アニメーション
       * 1: 有効、0: 無効
       */
      "chatMessageWithAnimation"=>"1",

      /**
       * チャット本文コピー
       * 0: コピーできる、1: コピーできない
       */
      "chatMessageCopy"=>"0",

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