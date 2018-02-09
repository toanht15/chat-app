<?php

define('APP_MODE_DEV', true);

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
// プレミアムプラン
define('C_CONTRACT_FULL_PLAN',           "{\"chat\": true, \"synclo\": true, \"document\": true, \"statistics\": true, \"chatLimitation\": true, \"exportHistory\": true, \"deleteHistory\": true, \"dictionaryCategory\": true, \"laCoBrowse\": false, \"hideRealtimeMonitor\": false, \"operatingHour\": true, \"refCompanyData\": false, \"freeInput\": true, \"cv\": true, \"autoMessageSendMail\": true, \"sendFile\": true, \"loginIpFilter\": true, \"importExcelAutoMessage\": true, \"operatorPresenceView\": true, \"monitorPollingMode\": false}");
// チャットベーシックプラン
define('C_CONTRACT_CHAT_BASIC_PLAN',     "{\"chat\": true, \"synclo\": false, \"document\": false, \"statistics\": false, \"chatLimitation\": false, \"exportHistory\": false, \"deleteHistory\": false, \"dictionaryCategory\": false, \"laCoBrowse\": false, \"hideRealtimeMonitor\": false, \"operatingHour\": false, \"refCompanyData\": false, \"freeInput\": false, \"cv\": false, \"autoMessageSendMail\": false, \"sendFile\": false, \"loginIpFilter\": false, \"importExcelAutoMessage\": false, \"operatorPresenceView\": false, \"monitorPollingMode\": false}");
// チャットスタンダードプラン
define('C_CONTRACT_CHAT_PLAN',           "{\"chat\": true, \"synclo\": false, \"document\": false, \"statistics\": true, \"chatLimitation\": true, \"exportHistory\": true, \"deleteHistory\": true, \"dictionaryCategory\": true, \"laCoBrowse\": false, \"hideRealtimeMonitor\": false, \"operatingHour\": true, \"refCompanyData\": false, \"freeInput\": true, \"cv\": true, \"autoMessageSendMail\": true, \"sendFile\": true, \"loginIpFilter\": true, \"importExcelAutoMessage\": true, \"operatorPresenceView\": true, \"monitorPollingMode\": false}");
// 画面同期プラン
define('C_CONTRACT_SCREEN_SHARING_PLAN', "{\"chat\": false, \"synclo\": true, \"document\": true, \"statistics\": false, \"chatLimitation\": false, \"exportHistory\": false, \"deleteHistory\": false, \"dictionaryCategory\": false, \"laCoBrowse\": false, \"hideRealtimeMonitor\": false, \"operatingHour\": true, \"refCompanyData\": false, \"freeInput\": false, \"cv\": false, \"autoMessageSendMail\": false, \"sendFile\": false, \"loginIpFilter\": true, \"importExcelAutoMessage\": false, \"operatorPresenceView\": true, \"monitorPollingMode\": false}");

//契約プランID
define('C_CONTRACT_FULL_PLAN_ID',"1");
define('C_CONTRACT_CHAT_PLAN_ID',"2");
define('C_CONTRACT_SCREEN_SHARING_ID',"3");
define('C_CONTRACT_CHAT_BASIC_PLAN_ID',"4");

//ML用アカウント
define('C_MEDIALINK_ACCOUNT', "ML用アカウント"); // ML用アカウント
define('C_MEDIALINK_PERMISSION_LEVEL', 99); // ML用パーミッション

//メールアドレス
define('C_MAGREEMENT_MAIL_ADDRESS', "@ml.jp"); //mcompanyアドレス

define('C_CROSS_DOMAIN_ADDRESS', "http://contact.sinclo"); //sinclo管理画面URL

define('C_DEFALT_MCOMPANY_KEY', 2); // template key

define('C_COMPANY_JS_TEMPLATE_FILE', "/var/www/sinclo/admin_site/corporate.js.template"); // 企業用JSファイル配置ディレクトリ
define('C_COMPANY_JS_FILE_DIR', "/var/www/sinclo/socket/webroot/client"); // 企業用JSファイル配置ディレクトリ

/* ユーザー権限（単体あり：C_AUTHORITY_%） */
$config['Authority'] = [
    C_AUTHORITY_ADMIN => "管理者",
    C_AUTHORITY_NORMAL => "一般"
];