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
define('C_MATCH_RULE_COLOR_CODE', '/^#([0-9|a-f|A-F]{3}|[0-9|a-f|A-F]{6})$/');

// メッセージ種別
define('C_MESSAGE_TYPE_SUCCESS', 1); // 処理成功
define('C_MESSAGE_TYPE_ERROR', 2); // 処理失敗
define('C_MESSAGE_TYPE_ALERT', 3); // 処理失敗
// define('C_MESSAGE_TYPE_NOTICE', 3); // 通知（未実装）

// ユーザー権限（リストあり：$config['Authority']）
define('C_AUTHORITY_ADMIN', 1); // 管理者
define('C_AUTHORITY_NORMAL', 2); // 一般

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
