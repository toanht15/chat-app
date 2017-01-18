<?php

// 正規表現
define('C_MATCH_RULE_TEL', '/^\+?(\d|-)*$/'); // TEL
define('C_MATCH_RULE_TIME', '/^(24:00|2[0-3]:[0-5][0-9]|[0-1]?[0-9]:[0-5][0-9])$/'); // 時間 H:i
define('C_MATCH_RULE_COLOR_CODE', '/^#([0-9|a-f|A-F]{3}|[0-9|a-f|A-F]{6})$/');
define('C_MATCH_RULE_IMAGE_FILE', '/.(png|jpg|jpeg)$/i');
define('C_MATCH_RULE_NUM_1', '/^(100|[0-9]{1,2})$/');
define('C_MATCH_RULE_NUM_2', '/^(100|[1-9][0-9]|[1-9]{1})$/');

// メッセージ種別
define('C_MESSAGE_TYPE_SUCCESS', 1); // 処理成功
define('C_MESSAGE_TYPE_ERROR', 2); // 処理失敗
define('C_MESSAGE_TYPE_ALERT', 3); // 処理失敗
// define('C_MESSAGE_TYPE_NOTICE', 3); // 通知（未実装）

// ユーザー権限（リストあり：$config['Authority']）
define('C_AUTHORITY_ADMIN', 1); // 管理者
define('C_AUTHORITY_NORMAL', 2); // 一般
define('C_AUTHORITY_SUPER', 99); // ML管理者

//契約プラン
define('C_MAGREEMENT_FULL_PLUN', "'chat' => true,'synclo' => true,'document' => true"); // フルプラン
define('C_MAGREEMENT_CHAT_PLUN', "'chat' => true"); // チャットプラン
define('C_MAGREEMENT_SCREEN_SHARING_PLUN', "'synclo' => true"); // 画面動機プラン

//ML用アカウント
define('C_MEDIALINK_ACCOUNT', "ML用アカウント"); // ML用アカウント
define('C_MEDIALINK_PERMISSION_LEVEL', 99); // ML用パーミッション

//メールアドレス
define('C_MAGREEMENT_MAIL_ADDRESS', "@ml.jp"); //mcompanyアドレス

define('C_CROSS_DOMAIN_ADDRESS', "http://contact.sinclo"); //sinclo管理画面URL

define('C_DEFALT_MCOMPANY_KEY', 2); // template key

/* ユーザー権限（単体あり：C_AUTHORITY_%） */
$config['Authority'] = [
    C_AUTHORITY_ADMIN => "管理者",
    C_AUTHORITY_NORMAL => "一般"
];

>>>>>>> origin/develop
