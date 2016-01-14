<?php
/* 定数定義 */
// Nodeサーバー
define('C_NODE_SERVER_ADDR', "//socket.localhost:8080");

// 表示設定種別
define('C_WIDGET_DISPLAY_CODE_SHOW', 1); // 常に表示する
define('C_WIDGET_DISPLAY_CODE_OPER', 2); // オペレーターが待機中の時のみ表示する
define('C_WIDGET_DISPLAY_CODE_HIDE', 3); // 表示しない

// 在籍/退席コード
define('C_OPERATOR_PASSIVE', 0); // 退席
define('C_OPERATOR_ACTIVE', 1); // 在籍

// 正規表現
define('C_MATCH_RULE_TEL', '/^\+?(\d|-)*$/'); // TEL

// メッセージ種別
define('C_MESSAGE_TYPE_SUCCESS', 1); // 処理成功
define('C_MESSAGE_TYPE_ERROR', 2); // 処理失敗
// define('C_MESSAGE_TYPE_NOTICE', 3); // 通知（未実装）

/* ユーザー権限 */
$config['Authority'] = [
    1 => "管理者",
    2 => "一般"
];

/* ウィジェット設定 － 表示設定種別 */
$config['WidgetDisplayType'] = [
    1 => "常に表示する",
    2 => "オペレーターが待機中の時のみ表示する",
    3 => "表示しない"
];
