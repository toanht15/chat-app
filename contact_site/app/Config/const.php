<?php
/* 定数定義 */
define('APP_MODE_DEV', true);
define('C_PATH_NODE_FILE_SERVER', C_NODE_SERVER_ADDR.C_NODE_SERVER_FILE_PORT); // Nodeサーバーの公開ファイルパス

// AWS: S3
define('C_AWS_S3_VERSION', 'latest'); // ウィジェット用参照先
define('C_AWS_S3_REGION', 'ap-northeast-1'); // ウィジェット用参照先
define('C_AWS_S3_STORAGE', 'STANDARD'); // ウィジェット用参照先
define('C_AWS_S3_HOSTNAME', 'https://s3-'.C_AWS_S3_REGION.'.amazonaws.com/'); // S3パス

// サムネイル用接頭辞
define('C_PREFIX_DOCUMENT', 'thumb_'); // 資料

// 画像関連
define('C_PATH_WIDGET_GALLERY_IMG', C_PATH_NODE_FILE_SERVER.'/img/widget/'); // ウィジェット用参照先
define('C_PATH_SYNC_TOOL_IMG', C_PATH_NODE_FILE_SERVER.'/img/sync/'); // 画面同期用参照先
define('C_PATH_WIDGET_CUSTOM_IMG', C_NODE_SERVER_ADDR.'/widget'); // ウィジェット用保存先
define('C_PATH_WIDGET_IMG_DIR', ROOT.DS.APP_DIR.DS.WEBROOT_DIR.DS.'files'); // ウィジェット用保存先
define('C_PATH_TMP_IMG_DIR', ROOT.DS.APP_DIR.DS.WEBROOT_DIR.DS.'files/tmp'); // ウィジェット用保存先

// タブステータス
define('C_WIDGET_TAB_STATUS_CODE_OPEN', 1); // ウィジェットが開いている状態
define('C_WIDGET_TAB_STATUS_CODE_CLOSE', 2); // ウィジェットが閉じている状態
define('C_WIDGET_TAB_STATUS_CODE_NONE', 3); // 非アクティブ状態
define('C_WIDGET_TAB_STATUS_CODE_DISABLE', 4); // ウィジェット非表示の状態
define('C_WIDGET_TAB_STATUS_CODE_OUT', 5); // ページ離脱状態

// 通知機能
define('C_PATH_NOTIFICATION_IMG_DIR', 'notification/'); // デスクトップ通知用画像参照先
define('C_PATH_NOTIFICATION_IMG_SAVE_DIR', ROOT.DS.APP_DIR.DS.WEBROOT_DIR.DS.'img'.DS.'notification'.DS); // デスクトップ通知用画像保存先

// 使用機能
define('C_COMPANY_USE_SYNCLO', 'synclo'); // 画面同期
define('C_COMPANY_USE_CHAT', 'chat'); // オペレーターが待機中の時のみ表示する
define('C_COMPANY_USE_DOCUMENT', 'document'); // オペレーターが待機中の時のみ表示する
define('C_COMPANY_USE_VIDEO_CHAT', 'videochat'); // ビデオチャット
define('C_COMPANY_USE_CHAT_LIMITER', 'chatLimitation'); // チャット同時対応数上限
define('C_COMPANY_USE_HISTORY_EXPORTING', 'exportHistory'); // 履歴エクスポート
define('C_COMPANY_USE_HISTORY_DELETE', 'deleteHistory'); // 履歴エクスポート
define('C_COMPANY_USE_STATISTICS', 'statistics'); // 統計
define('C_COMPANY_USE_DICTIONARY_CATEGORY', 'dictionaryCategory'); // 定型文カテゴリ


// 簡易メッセージ入力機能種別
define('C_DICTIONARY_TYPE_COMP', 1); // 企業で使用する
define('C_DICTIONARY_TYPE_PERSON', 2); // 個人で使用する

// 表示タイミング種別
define('C_WIDGET_SHOW_TIMING_SITE', 1);              // サイト訪問後
define('C_WIDGET_SHOW_TIMING_PAGE', 2);              // ページ訪問後
define('C_WIDGET_SHOW_TIMING_RECV_1ST_AUTO_MES', 3); // 初回オートメッセージ受信時
define('C_WIDGET_SHOW_TIMING_IMMEDIATELY', 4);       // すぐに表示

// 表示設定種別
define('C_WIDGET_DISPLAY_CODE_SHOW', 1); // 常に表示する
define('C_WIDGET_DISPLAY_CODE_OPER', 2); // オペレーターが待機中の時のみ表示する
define('C_WIDGET_DISPLAY_CODE_HIDE', 3); // 表示しない

// 自動表示条件種別
define('C_WIDGET_AUTO_OPEN_TYPE_ON', 3); // 常に自動で最大化する
define('C_WIDGET_AUTO_OPEN_TYPE_SITE', 1); // サイト訪問後
define('C_WIDGET_AUTO_OPEN_TYPE_PAGE', 4); // ページ訪問後
define('C_WIDGET_AUTO_OPEN_TYPE_OFF', 2); // 常に最大化しない

// 表示位置種別
define('C_WIDGET_POSITION_RIGHT_BOTTOM', 1); // 右下
define('C_WIDGET_POSITION_LEFT_BOTTOM', 2); // 左下

// 表示名種別
define('C_WIDGET_SHOW_NAME', 1); // 表示名
define('C_WIDGET_SHOW_COMP', 2); // 企業名

// チャットデザインタイプ
define('C_WIDGET_CHAT_MESSAGE_DESIGN_TYPE_BOX', 1); // BOX型（サイズ固定）
define('C_WIDGET_CHAT_MESSAGE_DESIGN_TYPE_BALLOON', 2); // 吹き出し型（サイズ可変）

// チャット本文コピー
define('C_WIDGET_CHAT_MESSAGE_CAN_COPY', 1); // コピーできる
define('C_WIDGET_CHAT_MESSAGE_CANT_COPY', 2); // コピーできない

// ラジオボタン選択時の動作種別
define('C_WIDGET_RADIO_CLICK_SEND', 1); // 文字列が送信される
define('C_WIDGET_RADIO_CLICK_TEXT', 2); // 文字列がテキストエリアに挿入される

// チャット送信のアクション種別
define('C_WIDGET_SEND_ACT_PUSH_KEY', 1); // キーアクション込み
define('C_WIDGET_SEND_ACT_PUSH_BTN', 2); // ボタンのみ

// チャット対応数制限
define('C_SC_ENABLED', 1); // 利用する
define('C_SC_DISABLED', 2); // 利用しない

// チャット通知の種別
define('C_NOTIFICATION_TYPE_TITLE', 1); // タイトル
define('C_NOTIFICATION_TYPE_URL', 2); // URL

// 在籍/退席コード
define('C_OPERATOR_PASSIVE', 0); // 退席
define('C_OPERATOR_ACTIVE', 1); // 在籍

// 正規表現
define('C_MATCH_RULE_TEL', '/^\+?(\d|-)*$/'); // TEL
define('C_MATCH_RULE_TIME', '/^(24:00|2[0-3]:[0-5][0-9]|[0-1]?[0-9]:[0-5][0-9])$/'); // 時間 H:i
define('C_MATCH_RULE_COLOR_CODE', '/^#([0-9|a-f|A-F]{3}|[0-9|a-f|A-F]{6})$/');
define('C_MATCH_RULE_IMAGE_FILE', '/.(png|jpg|jpeg)$/i');
define('C_MATCH_RULE_NUM_1', '/^(100|[0-9]{1,2})$/');
define('C_MATCH_RULE_NUM_2', '/^(100|[1-9][0-9]|[1-9]{1})$/');
define('C_MATCH_RULE_NUM_3', '/^(60|[1-5][0-9]|[1-9]{1})$/');

// メッセージ種別
define('C_MESSAGE_TYPE_SUCCESS', 1); // 処理成功
define('C_MESSAGE_TYPE_ERROR', 2); // 処理失敗
define('C_MESSAGE_TYPE_ALERT', 3); // 処理失敗
// define('C_MESSAGE_TYPE_NOTICE', 3); // 通知（未実装）

// ユーザー権限（リストあり：$config['Authority']）
define('C_AUTHORITY_ADMIN', 1); // 管理者
define('C_AUTHORITY_NORMAL', 2); // 一般
define('C_AUTHORITY_SUPER', 99); // ML管理者

// オートメッセージ機能－トリガー種別コード
define('C_AUTO_TRIGGER_TYPE_BODYLOAD', 1); // 画面読み込み時

// オートメッセージ機能－トリガーリスト
define('C_AUTO_TRIGGER_STAY_TIME',  1); // 滞在時間
define('C_AUTO_TRIGGER_VISIT_CNT',  2); // 訪問回数
define('C_AUTO_TRIGGER_STAY_PAGE',  3); // ページ
define('C_AUTO_TRIGGER_DAY_TIME',   4); // 曜日・時間
define('C_AUTO_TRIGGER_REFERRER',   5); // 参照元URL（リファラー）
define('C_AUTO_TRIGGER_SEARCH_KEY', 6); // 検索キーワード
define('C_AUTO_TRIGGER_SPEECH_CONTENT', 7); // 発言内容
define('C_AUTO_TRIGGER_STAY_PAGE_OF_FIRST', 8); // 最初の滞在ページ
define('C_AUTO_TRIGGER_STAY_PAGE_OF_PREVIOUS', 9); // 前のページ

// オートメッセージ機能－アクション種別コード
define('C_AUTO_ACTION_TYPE_SENDMESSAGE', 1); // チャットメッセージを送る

// オートメッセージ機能－ウィジェット種別コード
define('C_AUTO_WIDGET_TYPE_OPEN', 1); // 自動で最大化する
define('C_AUTO_WIDGET_TYPE_CLOSE', 2); // 自動で最大化しない

// する/しない設定
define('C_SELECT_CAN', 1); // する
define('C_SELECT_CAN_NOT', 2); // しない

// TRUE/FALSE
define("C_CHECK_OFF", 0);
define("C_CHECK_ON", 1);

// 条件設定
define('C_COINCIDENT', 1); // 全て一致
define('C_SOME_EITHER', 2); // いずれかが一致

// 有効/無効設定
define('C_STATUS_AVAILABLE', 0); // 有効
define('C_STATUS_UNAVAILABLE', 1); // 無効

// 成果
define('C_ACHIEVEMENT_UNAVAILABLE', 1); // なし
define('C_ACHIEVEMENT_AVAILABLE', 2); // あり

// ダウンロード設定
define('C_YES', 1); // 可
define('C_IMPROPER', 2); // 不可

//ウィジットサイズタイプ
define('C_WIDGET_SIZE_TYPE_SMALL', 1); // 小
define('C_WIDGET_SIZE_TYPE_MEDIUM', 2); // 中
define('C_WIDGET_SIZE_TYPE_LARGE', 3); // 大

//最小化時のデザインタイプ
define('C_MINIMIZED_DESIGN_NO_SIMPLE', 1); // シンプル表示しない
define('C_MINIMIZED_DESIGN_SP_SIMPLE', 2); // スマホのみシンプル表示する
define('C_MINIMIZED_DESIGN_ALL_SIMPLE', 3); // すべての端末でシンプル表示する

//背景の影初期値
define('C_BOX_SHADOW', 0);//影なし

/* ユーザー権限（単体あり：C_AUTHORITY_%） */
$config['Authority'] = [
    C_AUTHORITY_ADMIN => "管理者",
    C_AUTHORITY_NORMAL => "一般"
];

/* タブステータス(js-const用) */
$config['tabStatusList'] = [
    'open' => C_WIDGET_TAB_STATUS_CODE_OPEN,
    'close' => C_WIDGET_TAB_STATUS_CODE_CLOSE,
    'none' => C_WIDGET_TAB_STATUS_CODE_NONE,
    'disable' => C_WIDGET_TAB_STATUS_CODE_DISABLE
];

/* タブステータス(メッセージ用) */
$config['tabStatusStrList'] = [
    C_WIDGET_TAB_STATUS_CODE_OPEN => "ウィジェットが開いている状態",
    C_WIDGET_TAB_STATUS_CODE_CLOSE => "ウィジェットが閉じている状態",
    C_WIDGET_TAB_STATUS_CODE_NONE => "ウィジェットが非表示の状態",
    C_WIDGET_TAB_STATUS_CODE_DISABLE => "非アクティブ状態",
    C_WIDGET_TAB_STATUS_CODE_OUT => "ページ離脱"
];

/* タブステータス(通知用) */
$config['tabStatusNotificationMessageList'] = [
    C_WIDGET_TAB_STATUS_CODE_OPEN => "", // 表示しないため文言の指定もしない
    C_WIDGET_TAB_STATUS_CODE_CLOSE => "ウィジェットが閉じられています",
    C_WIDGET_TAB_STATUS_CODE_NONE => "ウィジェットが表示されていません",
    C_WIDGET_TAB_STATUS_CODE_DISABLE => "別の作業をしています",
    C_WIDGET_TAB_STATUS_CODE_OUT => "ページが閉じられました"
];

/* ユーザー権限（単体あり：C_AUTHORITY_%） */
$config['dictionaryType'] = [
    C_DICTIONARY_TYPE_COMP => "共有設定",
    C_DICTIONARY_TYPE_PERSON => "個人設定"
];

/* 通常選択肢 */
$config['normalChoices'] = [
    C_SELECT_CAN => "する",
    C_SELECT_CAN_NOT => "しない"
];

/* ウィジェット設定 － 表示設定種別 */
$config['WidgetDisplayType'] = [
    1 => "常に表示する",
    2 => "オペレーターが待機中の時のみ表示する",
    3 => "表示しない"
];

/* ウィジェット設定 － 表示位置種別 */
$config['widgetPositionType'] = [
    C_WIDGET_POSITION_RIGHT_BOTTOM => "右下",
    C_WIDGET_POSITION_LEFT_BOTTOM => "左下"
];

/* ウィジェット設定 － 担当者表示名種別 */
$config['widgetShowNameType'] = [
    C_WIDGET_SHOW_NAME => "担当者名を表示する<br>　<s>※ユーザーマスタの「表示名」に設定された名称を表示します</s>",
    C_WIDGET_SHOW_COMP=> "企業名を表示する<br>　<s>※こちらの画面の「企業名」に設定された名称を表示します</s>"
];

/* ウィジェット設定 － 吹き出しデザイン */
$config['chatMessageDesignType'] = [
  C_WIDGET_CHAT_MESSAGE_DESIGN_TYPE_BOX => "BOX型（サイズ固定）",
  C_WIDGET_CHAT_MESSAGE_DESIGN_TYPE_BALLOON => "吹き出し型（サイズ可変）"
];

/* ウィジェット設定 － チャット本文コピー */
$config['chatMessageCopy'] = [
  C_WIDGET_CHAT_MESSAGE_CAN_COPY => "コピー可<br>　<s>※サイト訪問者がチャット本文をコピー出来るようにします</s>",
  C_WIDGET_CHAT_MESSAGE_CANT_COPY => "コピー不可<br>　<s>※サイト訪問者がチャット本文をコピー出来ないようにします</s>"
];

/* ウィジェット設定 － ラジオボタン操作時の動作種別 */
$config['widgetRadioBtnBehaviorType'] = [
    C_WIDGET_RADIO_CLICK_SEND => "選択された文字列が即時送信されます",
    C_WIDGET_RADIO_CLICK_TEXT => "選択された文字列がテキストエリアに入力されます"
];

/* ウィジェット設定 － チャット送信アクション種別 */
$config['widgetSendActType'] = [
    C_WIDGET_SEND_ACT_PUSH_KEY => "送信ボタン及びEnterキー（スマホの場合改行ボタン）",
    C_WIDGET_SEND_ACT_PUSH_BTN => "送信ボタンのみ"
];

/* オートメッセージ － トリガー種別 */
$config['chatNotificationType'] = [
    C_NOTIFICATION_TYPE_TITLE => "タイトル",
    C_NOTIFICATION_TYPE_URL => "URL"
];

/* オートメッセージ － トリガー種別 */
$config['outMessageTriggerType'] = [
    C_AUTO_TRIGGER_TYPE_BODYLOAD => "画面読み込み時"
];

/* オートメッセージ － 条件設定 */
$config['outMessageIfType'] = [
    C_COINCIDENT => "全て一致",
    C_SOME_EITHER => "いずれかが一致"
];

/* オートメッセージ － 条件設定 */
$config['outMessageAvailableType'] = [
    C_STATUS_AVAILABLE => "有効",
    C_STATUS_UNAVAILABLE => "無効"
];

/* オートメッセージ － 条件リスト */
$config['outMessageTriggerList'] = [
    // 滞在時間
    C_AUTO_TRIGGER_STAY_TIME => [
        'label' => '滞在時間',
        // いずれも複数はNG固定で（sinclo.jsを書き直す必要がある為）
        'createLimit' => [C_COINCIDENT => 1, C_SOME_EITHER => 1],
        'key' => 'stay_time',
        'default' => [
            "stayTimeCheckType" => 1,
            "stayTimeType" => "1",
            "stayTimeRange" => 3
        ]
    ],
    // 訪問回数
    C_AUTO_TRIGGER_VISIT_CNT => [
        'label' => '訪問回数',
        'createLimit' => [C_COINCIDENT => 1, C_SOME_EITHER => 1],
        'key' => 'visit_cnt',
        'default' => [
            "visitCnt" => "",
            "visitCntCond" => 1
        ]
    ],
    // 発言内容
    C_AUTO_TRIGGER_SPEECH_CONTENT => [
        'label' => '発言内容',
        'createLimit' => [C_COINCIDENT => 1, C_SOME_EITHER => 1],
        'key' => 'speech_content',
        'default' => [
            "speechContent" => "",
            "speechContentCond" => "1",
            "triggerTimeSec" => 3,
            "speechTriggerCond" => "1"
        ]
    ],
    // ページ
    C_AUTO_TRIGGER_STAY_PAGE => [
        'label' => 'ページ',
        'createLimit' => [C_COINCIDENT => 1, C_SOME_EITHER => 1],
        'key' => 'stay_page',
        'default' => [
            "keyword" => "",
            "targetName" => 1,
            "stayPageCond" => 2
        ]
    ],
    // 曜日・時間
    C_AUTO_TRIGGER_DAY_TIME => [
        'label' => '曜日・時間',
        'createLimit' => [C_COINCIDENT => 1, C_SOME_EITHER => 7],
        'key' => 'day_time',
        'default' => [
            "day" => [
              "mon" => false, "tue" => false, "wed" => false, "thu" => false,
              "fri" => false, "sat" => false, "sun" => false
            ],
            "timeSetting" => C_SELECT_CAN,
            "startTime" => "09:00",
            "endTime" => "18:00",
        ]
    ],
    // 参照元URL（リファラー）
    C_AUTO_TRIGGER_REFERRER => [
        'label' => '参照元URL（リファラー）',
        'createLimit' => [C_COINCIDENT => 1, C_SOME_EITHER => 1],
        'key' => 'referrer',
        'default' => [
           "keyword" => "",
           "referrerCond" => "1"
        ]
    ],
    // 検索キーワード
    C_AUTO_TRIGGER_SEARCH_KEY => [
        'label' => '検索キーワード',
        'createLimit' => [C_COINCIDENT => 1, C_SOME_EITHER => 1],
        'key' => 'search_keyword',
        'default' => [
           "keyword" => "",
           "searchCond" => "1"
        ]
    ],
    // 最初に訪れたページ
    C_AUTO_TRIGGER_STAY_PAGE_OF_FIRST => [
      'label' => '最初に訪れたページ',
      'createLimit' => [C_COINCIDENT => 1, C_SOME_EITHER => 1],
      'key' => 'stay_page_of_first',
      'default' => [
          "keyword" => "",
          "targetName" => 1,
          "stayPageCond" => 2
      ]
    ],
    // 前のページ
    C_AUTO_TRIGGER_STAY_PAGE_OF_PREVIOUS => [
      'label' => '前のページ',
      'createLimit' => [C_COINCIDENT => 1, C_SOME_EITHER => 1],
      'key' => 'stay_page_of_previous',
      'default' => [
         "keyword" => "",
         "targetName" => 1,
         "stayPageCond" => 2
      ]
    ]
];

/* オートメッセージ － アクション種別 */
$config['outMessageActionType'] = [
    C_AUTO_ACTION_TYPE_SENDMESSAGE => "チャットメッセージを送る"
];

/* オートメッセージ － ウィジェット種別 */
$config['outMessageWidgetOpenType'] = [
    C_AUTO_WIDGET_TYPE_OPEN => "自動で最大化する",
    C_AUTO_WIDGET_TYPE_CLOSE => "自動で最大化しない"
];

/* 成果種別 */
$config['achievementType'] = [
  C_ACHIEVEMENT_UNAVAILABLE => "無効",
  C_ACHIEVEMENT_AVAILABLE => "有効"
];
