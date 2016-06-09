<?php
/* 定数定義 */
define('APP_MODE_DEV', true);
// Nodeサーバー
if ( APP_MODE_DEV ) {
  define('C_NODE_SERVER_ADDR', "//socket.localhost"); // Dev
  define('C_NODE_SERVER_FILE_PORT', ":8080"); // Dev
  define('C_NODE_SERVER_WS_PORT', ":9090"); // Dev
}
else {
  define('C_NODE_SERVER_ADDR', "//ws1.sinclo.jp"); // 本番(今後動的になる)
  define('C_NODE_SERVER_FILE_PORT', ""); // Dev
  define('C_NODE_SERVER_WS_PORT', ""); // Dev
}

// 使用機能
define('C_COMPANY_USE_SYNCLO', 'synclo'); // 画面同期
define('C_COMPANY_USE_CHAT', 'chat'); // オペレーターが待機中の時のみ表示する

// 表示設定種別
define('C_WIDGET_DISPLAY_CODE_SHOW', 1); // 常に表示する
define('C_WIDGET_DISPLAY_CODE_OPER', 2); // オペレーターが待機中の時のみ表示する
define('C_WIDGET_DISPLAY_CODE_HIDE', 3); // 表示しない

// 表示位置種別
define('C_WIDGET_POSITION_RIGHT_BOTTOM', 1); // 右下
define('C_WIDGET_POSITION_LEFT_BOTTOM', 2); // 左下

// 在籍/退席コード
define('C_OPERATOR_PASSIVE', 0); // 退席
define('C_OPERATOR_ACTIVE', 1); // 在籍

// 正規表現
define('C_MATCH_RULE_TEL', '/^\+?(\d|-)*$/'); // TEL
define('C_MATCH_RULE_TIME', '/^(24:00|2[0-3]:[0-5][0-9]|[0-1]?[0-9]:[0-5][0-9])$/'); // 時間 H:i
define('C_MATCH_RULE_COLOR_CODE', '/^#([0-9|a-f|A-F]{3}|[0-9|a-f|A-F]{6})$/');
define('C_MATCH_RULE_IMAGE_FILE', '/^[a-zA-Z0-9_-]*.(png|jpg|git){1}$/i');

// メッセージ種別
define('C_MESSAGE_TYPE_SUCCESS', 1); // 処理成功
define('C_MESSAGE_TYPE_ERROR', 2); // 処理失敗
define('C_MESSAGE_TYPE_ALERT', 3); // 処理失敗
// define('C_MESSAGE_TYPE_NOTICE', 3); // 通知（未実装）

// ユーザー権限（リストあり：$config['Authority']）
define('C_AUTHORITY_ADMIN', 1); // 管理者
define('C_AUTHORITY_NORMAL', 2); // 一般

// オートメッセージ機能－トリガー種別コード
define('C_AUTO_TRIGGER_TYPE_BODYLOAD', 1); // 画面読み込み時

// オートメッセージ機能－トリガーリスト
define('C_AUTO_TRIGGER_STAY_TIME',  1); // 滞在時間
define('C_AUTO_TRIGGER_VISIT_CNT',  2); // 訪問回数
define('C_AUTO_TRIGGER_STAY_PAGE',  3); // ページ
define('C_AUTO_TRIGGER_DAY_TIME',   4); // 曜日・時間
define('C_AUTO_TRIGGER_REFERRER',   5); // 参照元URL（リファラー）
define('C_AUTO_TRIGGER_SEARCH_KEY', 6); // 検索キーワード

// オートメッセージ機能－アクション種別コード
define('C_AUTO_ACTION_TYPE_SENDMESSAGE', 1); // チャットメッセージを送る

// する/しない設定
define('C_SELECT_CAN', 1); // する
define('C_SELECT_CAN_NOT', 2); // しない

// 条件設定
define('C_COINCIDENT', 1); // 全て一致
define('C_SOME_EITHER', 2); // いずれかが一致

// 有効/無効設定
define('C_STATUS_AVAILABLE', 0); // 有効
define('C_STATUS_UNAVAILABLE', 1); // 無効

/* ユーザー権限（単体あり：C_AUTHORITY_%） */
$config['Authority'] = [
    C_AUTHORITY_ADMIN => "管理者",
    C_AUTHORITY_NORMAL => "一般"
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
    ]
];

/* オートメッセージ － アクション種別 */
$config['outMessageActionType'] = [
    C_AUTO_ACTION_TYPE_SENDMESSAGE => "チャットメッセージを送る"
];
