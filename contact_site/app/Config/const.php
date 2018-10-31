<?php

/* 定数定義 */
define('C_PATH_NODE_FILE_SERVER', C_NODE_SERVER_ADDR.C_NODE_SERVER_FILE_PORT); // Nodeサーバーの公開ファイルパス

// AWS: S3
define('C_AWS_S3_VERSION', 'latest'); // ウィジェット用参照先
define('C_AWS_S3_REGION', 'ap-northeast-1'); // ウィジェット用参照先
define('C_AWS_S3_STORAGE', 'STANDARD'); // ウィジェット用参照先
define('C_AWS_S3_HOSTNAME', 'https://s3-'.C_AWS_S3_REGION.'.amazonaws.com/'); // S3パス

// AWS: SES
define('C_AWS_SES_SMTP_SERVER_NAME', 'medialink-ml.co.jp');
define('C_AWS_SES_SMTP_PORT', '587');
define('C_AWS_SES_SMTP_USER_NAME', 'sinclo@medialink-ml.co.jp');
define('C_AWS_SES_SMTP_CREDENTIAL', 'Jwq28Gt5');

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
define('C_COMPANY_USE_LA_CO_BROWSE', 'laCoBrowse'); // ビデオチャット
define('C_COMPANY_USE_CHAT_LIMITER', 'chatLimitation'); // チャット同時対応数上限
define('C_COMPANY_USE_HISTORY_EXPORTING', 'exportHistory'); // 履歴エクスポート
define('C_COMPANY_USE_HISTORY_DELETE', 'deleteHistory'); // 履歴削除
define('C_COMPANY_USE_STATISTICS', 'statistics'); // 統計
define('C_COMPANY_USE_DICTIONARY_CATEGORY', 'dictionaryCategory'); // 定型文カテゴリ
define('C_COMPANY_USE_HIDE_REALTIME_MONITOR', 'hideRealtimeMonitor'); // リアルタイムモニター非表示
define('C_COMPANY_USE_OPERATING_HOUR', 'operatingHour'); // 営業時間
define('C_COMPANY_REF_COMPANY_DATA', 'refCompanyData'); // 企業情報参照（Landscape）
define('C_COMPANY_USE_FREE_INPUT', 'freeInput'); // 自由入力エリア
define('C_COMPANY_USE_CV', 'cv'); // CV
define('C_COMPANY_USE_AUTOMESSAGE_SEND_MAIL', 'autoMessageSendMail'); // メール送信（オートメッセージ）
define('C_COMPANY_USE_SEND_FILE', 'sendFile'); // ファイル送信
define('C_COMPANY_USE_SECURITY_LOGIN_IP_FILTER', 'loginIpFilter'); // セキュリティ設定（IPフィルタ）
define('C_COMPANY_USE_IMPORT_EXCEL_AUTO_MESSAGE', 'importExcelAutoMessage'); // オートメッセージインポート（発言内容のみ）
define('C_COMPANY_USE_OPERATOR_PRESENCE_VIEW', 'operatorPresenceView'); // オペレータ在席状況確認
define('C_COMPANY_USE_REALTIME_MONITOR_POLLING_MODE', 'monitorPollingMode'); // リアルタイムモニタの情報取得方法変更（ポーリング式）
define('C_COMPANY_USE_CHATBOT_SCENARIO', 'chatbotScenario');  // チャットボットシナリオ設定
define('C_COMPANY_USE_CAMPAIGN', 'campaign');  // キャンペーン設定
define('C_COMPANY_USE_CHATCALLMESSAGES', 'chatCallMessages');  // チャット呼出中メッセージ
define('C_COMPANY_USE_CUSTOMVARIABLES', 'customVariables');  // カスタム変数
define('C_COMPANY_USE_EDITCUSTOMERINFORMATIONS', 'editCustomerInformations');  // 訪問ユーザ情報
  define('C_COMPANY_USE_COGMO_ATTEND_API', 'useCogmoAttendApi');  // CogmoAttend連携

// リアルタイムモニタ - ポーリングモード定数
define('C_REALTIME_MONITOR_POLLING_MODE_INTERVAL_MSEC', 3000);

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
define('C_WIDGET_DISPLAY_CODE_TIME', 4); // 営業時間内のみ表示する

// 自動表示条件種別
define('C_WIDGET_AUTO_OPEN_TYPE_ON', 3); // 常に自動で最大化する　※ 現在利用なし（旧IF）
define('C_WIDGET_AUTO_OPEN_TYPE_SITE', 1); // サイト訪問後
define('C_WIDGET_AUTO_OPEN_TYPE_PAGE', 4); // ページ訪問後
define('C_WIDGET_AUTO_OPEN_TYPE_OFF', 2); // 常に最大化しない　※ 現在利用なし（旧IF）
define('C_WIDGET_AUTO_OPEN_TYPE_NONE', 5); // 初期表示のままにする

// 表示位置種別
define('C_WIDGET_POSITION_RIGHT_BOTTOM', 1); // 右下
define('C_WIDGET_POSITION_LEFT_BOTTOM', 2); // 左下

// 表示名種別
define('C_WIDGET_SHOW_NAME', 1); // 表示名
define('C_WIDGET_SHOW_COMP', 2); // 企業名
define('C_WIDGET_SHOW_NONE', 3); // 表示しない

// 表示名種別
define('C_WIDGET_SHOW_AUTOMESSAGE_COMP', 1); // 企業名
define('C_WIDGET_SHOW_AUTOMESSAGE_NONE', 2); // 表示しない

// チャットデザインタイプ
define('C_WIDGET_CHAT_MESSAGE_DESIGN_TYPE_BOX', 1); // BOX型
define('C_WIDGET_CHAT_MESSAGE_DESIGN_TYPE_BALLOON', 2); // 吹き出し型

// チャット本文コピー
define('C_WIDGET_CHAT_MESSAGE_CAN_COPY', 0); // コピーできる
define('C_WIDGET_CHAT_MESSAGE_CANT_COPY', 1); // コピーできない

// ラジオボタン選択時の動作種別
define('C_WIDGET_RADIO_CLICK_SEND', 1); // 文字列が送信される
define('C_WIDGET_RADIO_CLICK_TEXT', 2); // 文字列がテキストエリアに挿入される

// チャット送信のアクション種別
define('C_WIDGET_SEND_ACT_PUSH_KEY', 1); // キーアクション込み
define('C_WIDGET_SEND_ACT_PUSH_BTN', 2); // ボタンのみ

// チャット対応数制限
define('C_SC_ENABLED', 1); // 利用する
define('C_SC_DISABLED', 2); // 利用しない

// 初期ステータス
define('C_SC_AWAY', 0); // 離席中
define('C_SC_WAITING', 1); // 待機中

// チャット呼出中メッセージ
define('C_IN_ENABLED', 1); // 利用する
define('C_IN_DISABLED', 2); // 利用しない

// 営業時間設定
define('C_ACTIVE_ENABLED', 1); // 利用する
define('C_ACTIVE_DISABLED', 2); // 利用しない

// 営業時間設定
define('C_TYPE_EVERY', 1); // 毎日
define('C_TYPE_WEEKLY', 2); // 平日/週末

// チャット通知の種別
define('C_NOTIFICATION_TYPE_TITLE', 1); // タイトル
define('C_NOTIFICATION_TYPE_URL', 2); // URL

// 在籍/退席コード
define('C_OPERATOR_PASSIVE', 0); // 退席
define('C_OPERATOR_ACTIVE', 1); // 在籍

// 正規表現
define('C_MATCH_RULE_TEL', '/^\+?(\d{10,}|[\d-]{12,})/'); // TEL
define('C_MATCH_RULE_TIME', '/^(24:00|2[0-3]:[0-5][0-9]|[0-1]?[0-9]:[0-5][0-9])$/'); // 時間 H:i
define('C_MATCH_RULE_COLOR_CODE', '/^#([0-9|a-f|A-F]{3}|[0-9|a-f|A-F]{6})$/');
define('C_MATCH_RULE_IMAGE_FILE', '/.(png|jpg|jpeg)$/i');
define('C_MATCH_RULE_NUM_1', '/^(100|[0-9]{1,2})$/');
define('C_MATCH_RULE_NUM_2', '/^(100|[1-9][0-9]|[1-9]{1})$/');
define('C_MATCH_RULE_NUM_3', '/^(60|[1-5][0-9]|[1-9]{1})$/');
define('C_MATCH_RULE_TEXT', '/.+/'); // 1文字以上のテキスト
define('C_MATCH_RULE_NUMBER', '/^\d+$/');  // 1文字以上の数字
define('C_MATCH_RULE_EMAIL', '/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/'); // メールアドレス http://emailregex.com/

define('C_MATCH_INPUT_RULE_ALL', '/.*/');  // 入力制限なし
define('C_MATCH_INPUT_RULE_NUMBER', '/[\d]*/');  // 数字入力
define('C_MATCH_INPUT_RULE_EMAIL', '/[\w<>()[\]\\\.\-,;:@"]*/'); // メールアドレス入力(半角英数記号入力)
define('C_MATCH_INPUT_RULE_TEL', '/^\+?(\d|-)*/'); // 電話番号入力（半角英数と一部記号入力）

// メッセージ種別
define('C_MESSAGE_TYPE_SUCCESS', 1); // 処理成功
define('C_MESSAGE_TYPE_ERROR', 2); // 処理失敗
define('C_MESSAGE_TYPE_ALERT', 3); // 処理失敗
define('C_MESSAGE_OUT_OF_TERM_TRIAL', 4); // トライアル期間終了
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
define('C_AUTO_TRIGGER_OPERATING_HOURS', 4); // 営業時間
define('C_AUTO_TRIGGER_DAY_TIME',   5); // 曜日・時間
define('C_AUTO_TRIGGER_REFERRER',   6); // 参照元URL（リファラー）
define('C_AUTO_TRIGGER_SEARCH_KEY', 7); // 検索キーワード
define('C_AUTO_TRIGGER_SPEECH_CONTENT', 8); // 発言内容
define('C_AUTO_TRIGGER_STAY_PAGE_OF_FIRST', 9); // 最初の滞在ページ
define('C_AUTO_TRIGGER_STAY_PAGE_OF_PREVIOUS', 10); // 前のページ

// オートメッセージ機能－アクション種別コード
define('C_AUTO_ACTION_TYPE_SENDMESSAGE', 1); // チャットメッセージを送る
define('C_AUTO_ACTION_TYPE_SELECTSCENARIO', 2);  // シナリオを選択する

// オートメッセージ機能－ウィジェット種別コード
define('C_AUTO_WIDGET_TYPE_OPEN', 1); // 自動で最大化する
define('C_AUTO_WIDGET_TYPE_CLOSE', 2); // 自動で最大化しない

// オートメッセージ機能－cv種別コード
define('C_AUTO_CV_EFFECTIVENESS', 1); // cv登録する
define('C_AUTO_CV_DISABLED', 2); // cv登録しない

// オートメッセージ機能－テキストエリア種別コード
define('C_AUTO_WIDGET_TEXTAREA_OPEN', 1); // 自由入力可
define('C_AUTO_WIDGET_TEXTAREA_CLOSE', 2); // 自由入力不可

// シナリオ設定－アクション種別コード
define('C_SCENARIO_ACTION_TEXT', 1); // テキスト発言
define('C_SCENARIO_ACTION_HEARING', 2); // ヒアリング
define('C_SCENARIO_ACTION_SELECT_OPTION', 3); // 選択肢
define('C_SCENARIO_ACTION_SEND_MAIL', 4); // メール送信
define('C_SCENARIO_ACTION_CALL_SCENARIO', 5); // シナリオ呼び出し
define('C_SCENARIO_ACTION_EXTERNAL_API', 6); // 外部システム連携
define('C_SCENARIO_ACTION_SEND_FILE', 7); // ファイル送信
define('C_SCENARIO_ACTION_GET_ATTRIBUTE', 8); // 属性値取得
define('C_SCENARIO_ACTION_RECEIVE_FILE', 9); // ファイル受信
define('C_SCENARIO_ACTION_BRANCH_ON_CONDITION', 10); // 条件分岐
define('C_SCENARIO_ACTION_ADD_CUSTOMER_INFORMATION', 11); // 訪問ユーザ登録
define('C_SCENARIO_ACTION_BULK_HEARING', 12); // 一括ヒアリング

// シナリオ設定(ヒアリング)－入力タイプ種別コード
define('C_SCENARIO_INPUT_TYPE_TEXT', 1);
define('C_SCENARIO_INPUT_TYPE_NUMBER', 2);
define('C_SCENARIO_INPUT_TYPE_EMAIL', 3);
define('C_SCENARIO_INPUT_TYPE_TEL', 4);

// シナリオ設定(ヒアリング)－UIタイプ種別コード
define('C_SCENARIO_UI_TYPE_ONE_ROW_TEXT', 1);
define('C_SCENARIO_UI_TYPE_MULTIPLE_ROW_TEXT', 2);
define('C_SCENARIO_UI_TYPE_RADIO_BUTTON', 3);
define('C_SCENARIO_UI_TYPE_PULLDOWN', 4);
define('C_SCENARIO_UI_TYPE_CALENDAR', 5);

/* シナリオ設定(ヒアリング) - 改行設定 */
define('C_SCENARIO_INPUT_LF_TYPE_DISALLOW', 1);
define('C_SCENARIO_INPUT_LF_TYPE_ALLOW', 2);

/* シナリオ設定(ヒアリング) - メッセージ送信設定 */
define('C_SCENARIO_SEND_MESSAGE_BY_ENTER', 1);
define('C_SCENARIO_SEND_MESSAGE_BY_BUTTON', 2);

/* シナリオ設定(メール送信) - メール送信タイプ */
define('C_SCENARIO_MAIL_TYPE_ALL_MESSAGE', 1);
define('C_SCENARIO_MAIL_TYPE_VARIABLES', 2);
define('C_SCENARIO_MAIL_TYPE_CUSTOMIZE', 3);
/* シナリオ設定(外部連携) - 連携タイプ */
define('C_SCENARIO_EXTERNAL_TYPE_API', 1);
define('C_SCENARIO_EXTERNAL_TYPE_SCRIPT', 2);

/* シナリオ設定(外部連携) - メソッド種別 */
define('C_SCENARIO_METHOD_TYPE_GET', 1);
define('C_SCENARIO_METHOD_TYPE_POST', 2);

// シナリオ設定(属性値取得)－属性別
define('C_SCENARIO_ATTRIBUTE_TYPE_ID', 1);
define('C_SCENARIO_ATTRIBUTE_TYPE_NAME', 2);
define('C_SCENARIO_ATTRIBUTE_TYPE_SELECTOR', 3);

// シナリオ設定(ファイル受信)－ファイル形式
define('C_SCENARIO_RECEIVE_FILE_TYPE_BASIC', 1);
define('C_SCENARIO_RECEIVE_FILE_TYPE_EXTENDED', 2);

// シナリオ設定（条件分岐)−変数の値一致条件
define('C_SCENARIO_VARIABLE_CONDITION_IN', 1);
define('C_SCENARIO_VARIABLE_CONDITION_NOT_IN', 2);

// シナリオ設定（条件分岐)−実行するアクション
define('C_SCENARIO_PROCESS_ACTION_TYPE_SPEECH_TEXT', 1);
define('C_SCENARIO_PROCESS_ACTION_TYPE_CALL_SCENARIO', 2);
define('C_SCENARIO_PROCESS_ACTION_TYPE_TERMINATE', 3);
define('C_SCENARIO_PROCESS_ACTION_TYPE_NONE', 4);

// する/しない設定
define('C_SELECT_CAN', 1); // する
define('C_SELECT_CAN_NOT', 2); // しない

// TRUE/FALSE
define("C_CHECK_OFF", 0);
define("C_CHECK_ON", 1);

// 条件設定
define('C_COINCIDENT', 1); // 全て一致
define('C_SOME_EITHER', 2); // いずれかが一致

// チャット履歴画面
define('C_CHAT_HISTORY_SIDE', 1); // 横並び
define('C_CHAT_HISTORY_VERTICAL', 2); // 縦並び

// 有効/無効設定
define('C_STATUS_AVAILABLE', 0); // 有効
define('C_STATUS_UNAVAILABLE', 1); // 無効

// 成果
define('C_ACHIEVEMENT_CV', 0); // CV
define('C_ACHIEVEMENT_UNAVAILABLE', 1); // なし
define('C_ACHIEVEMENT_AVAILABLE', 2); // あり
define('C_ACHIEVEMENT_TERMINATE_SCENARIO', 3); // 途中離脱

// 種別
define('C_CHAT_AUTO', 1); // 自動応答
define('C_CHAT_MANUAL', 2); // 対応
define('C_CHAT_NOENTRY', 3); // 未入室
define('C_CHAT_SORRY', 4); // sorryメッセージ

// ダウンロード設定
define('C_YES', 1); // 可
define('C_IMPROPER', 2); // 不可

//ウィジット初期表示スタイル
define('C_WIDGET_DISPLAY_STYLE_TYPE_MAX', 1); // 最大化
define('C_WIDGET_DISPLAY_STYLE_TYPE_MIN', 2); // 最小化
define('C_WIDGET_DISPLAY_STYLE_TYPE_BANNER', 3); // 小さなバナー

//ウィジットサイズタイプ
define('C_WIDGET_SIZE_TYPE_SMALL', 1); // 小
define('C_WIDGET_SIZE_TYPE_MEDIUM', 2); // 中
define('C_WIDGET_SIZE_TYPE_LARGE', 3); // 大
define('C_WIDGET_SIZE_TYPE_MAXIMUM', 4); //最大

// ファイル送信設定タイプ
define('C_FILE_TRANSFER_SETTING_TYPE_BASIC', 1);
define('C_FILE_TRANSFER_SETTING_TYPE_EXTEND', 2);

/* カラー設定初期値styat */
//0.通常設定・高度設定
define('COLOR_SETTING_TYPE_OFF', 0);
define('COLOR_SETTING_TYPE_ON', 1);

//1.メインカラー
define('MAIN_COLOR', "#ABCD05");
//2.タイトル文字色
define('STRING_COLOR', "#FFFFFF");
//3.吹き出し文字色
define('MESSAGE_TEXT_COLOR', "#333333");
//4.その他文字色
define('OTHER_TEXT_COLOR', "#666666");
//5.ウィジェット枠線色
define('WIDGET_BORDER_COLOR', "#E8E7E0");
//6.吹き出し枠線色
define('CHAT_TALK_BORDER_COLOR', "#C9C9C9");
//6.ヘッダー背景色
define('HEADER_BACKGROUND_COLOR', "#FFFFFF");
//7.企業名文字色
define('SUB_TITLE_TEXT_COLOR', "#ABCD05");
//8.説明文文字色
define('DESCRIPTION_TEXT_COLOR', "#666666");
//9.チャットエリア背景色
define('CHAT_TALK_BACKGROUND_COLOR', "#FFFFFF");
//10.企業名担当者名文字色
define('C_NAME_TEXT_COLOR', "#ABCD05");
//11.企業側吹き出し文字色
define('RE_TEXT_COLOR', "#333333");
//12.企業側吹き出し背景色
define('RE_BACKGROUND_COLOR', "#FAF6E6");
//13.企業側吹き出し枠線色
define('RE_BORDER_COLOR', "#C9C9C9");
//15.訪問者側吹き出し文字色
define('SE_TEXT_COLOR', "#333333");
//16.訪問者側吹き出し背景色
define('SE_BACKGROUND_COLOR', "#FFFFFF");
//17.訪問者側吹き出し枠線色
define('SE_BORDER_COLOR', "#C9C9C9");
//19.メッセージエリア背景色
define('CHAT_MESSAGE_BACKGROUND_COLOR', "#FFFFFF");
//20.メッセージBOX文字色
define('MESSAGE_BOX_TEXT_COLOR', "#666666");
//21.メッセージBOX背景色
define('MESSAGE_BOX_BACKGROUND_COLOR', "#FFFFFF");
//22.メッセージBOX枠線色
define('MESSAGE_BOX_BORDER_COLOR', "#D4D4D4");
//24.送信ボタン文字色
define('CHAT_SEND_BTN_TEXT_COLOR', "#FFFFFF");
//25.送信ボタン背景色
define('CHAT_SEND_BTN_BACKGROUND_COLOR', "#ABCD05");
//26.ウィジット内枠線色
define('WIDGET_INSIDE_BORDER_COLOR', "#E8E7E0");
/* カラー設定初期値end */


//タイトル位置
define('WIDGET_TITLE_TOP_TYPE_LEFT', 1); //タイトル左寄せ
define('WIDGET_TITLE_TOP_TYPE_CENTER', 2); //タイトル中央寄せ
define('WIDGET_TITLE_NAME_TYPE_LEFT', 1); //企業名左寄せ
define('WIDGET_TITLE_NAME_TYPE_CENTER', 2); //企業名中央寄せ
define('WIDGET_TITLE_EXPLAIN_TYPE_LEFT', 1); //説明文左寄せ
define('WIDGET_TITLE_EXPLAIN_TYPE_CENTER', 2); //説明文中央寄せ


//最小化時のデザインタイプ
define('C_MINIMIZED_DESIGN_NO_SIMPLE', 1); // シンプル表示しない
define('C_MINIMIZED_DESIGN_SP_SIMPLE', 2); // スマホのみシンプル表示する
define('C_MINIMIZED_DESIGN_ALL_SIMPLE', 3); // すべての端末でシンプル表示する

//背景の影初期値
define('C_BOX_SHADOW', 0);//影なし

//閉じるボタン有効無効
define('C_CLOSE_BUTTON_SETTING_OFF', 1);//無効にする
define('C_CLOSE_BUTTON_SETTING_ON', 2);//有効にする

//小さなバナー表示
define('C_CLOSE_BUTTON_SETTING_MODE_TYPE_BANNER', 1);//小さなバナー表示
define('C_CLOSE_BUTTON_SETTING_MODE_TYPE_HIDDEN', 2);//非表示

//スマホ用、小さなバナー隠しパラメータ
define('C_SP_BANNER_POSITION', 1);//バナー表示位置
define('C_SP_WIDGET_VIEW_PATTERN', 1);//ウィジェット最大化最小化制御 (3,4は最小化に遷移しなくなる)

//バナーテキスト初期値
define('C_BANNER_TEXT', "チャットで相談");//バナー文言

//トライアルフラグ
define('C_TRIAL_FLG', 1);

//初期パスワード変更フラグ
define('C_NO_CHANGE_PASSWORD_FLG', 0); //変更してない
define('C_CHANGE_PASSWORD_FLG', 1); //変更した

//ビジネスモデル
define('C_FREE_B_TO_B', 1); // BtoB
define('C_FREE_B_TO_C', 2); // BtoC
define('C_FREE_BOTH', 3); // BtoB,BtoCどちらも

//メール種別
define('C_AFTER_FREE_APPLICATION_TO_CUSTOMER', 1); // 無料トライアル登録時 お客さん向けメール
define('C_AFTER_FREE_APPLICATION_TO_COMPANY', 2); // 無料トライアル登録時 会社向けメール
define('C_AFTER_FREE_PASSWORD_CHANGE_TO_COMPANY', 3); // 無料トライアル登録後初期パスワード変更 会社向けメール
define('C_AFTER_FREE_PASSWORD_CHANGE_TO_CUSTOMER', 4); // 無料トライアル登録後初期パスワード変更 お客さん向けメール
define('C_AFTER_APPLICATION_TO_CUSTOMER', 5); // いきなり契約登録時 お客さん向けメール
define('C_AFTER_APPLICATION_TO_COMPANY', 6); // いきなり契約登録時 会社向けメール
define('C_AFTER_PASSWORD_CHANGE_TO_CUSTOMER', 7); // いきなり契約登録後初期パスワード変更 お客さん向けメール
define('C_AFTER_PASSWORD_RESET_TO_CUSTOMER', 8); //パスワード変更 お客さん向けメール

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

/* ウィジェット設定 ー Web接客コード */
$config['widgetShowChoices'] = [
  C_SELECT_CAN => "表示する",
  C_SELECT_CAN_NOT => "表示しない"
];

/* ウィジェット設定 － 表示設定種別 */
$config['WidgetDisplayType'] = [
    1 => "常に表示する",
    4 => "営業時間内のみ表示する",
    2 => "オペレーターが待機中の時のみ表示する",
    3 => "表示しない"
];

/* ウィジェット設定 － 表示設定種別 */
$config['WidgetDisplayStyleType'] = [
  1 => "最大化して表示する",
  2 => "最小化して表示する",
  3 => "小さなバナーを表示する"
];

/* ウィジェット設定 － 表示位置種別 */
$config['widgetPositionType'] = [
    C_WIDGET_POSITION_RIGHT_BOTTOM => "右下",
    C_WIDGET_POSITION_LEFT_BOTTOM => "左下"
];

/* ウィジェット設定 ー Web接客コード */
$config['widgetShowAccessId'] = [
  C_SELECT_CAN => "表示する",
  C_SELECT_CAN_NOT => "表示しない<br>　<s>※ウェブ接客コード（4桁のID）を電話口でヒアリングすることで、リアルタイムモニター上で</s><br><s>　  相手を特定することができます。（電話口の相手のウェブ行動履歴や流入経路の把握が可能）</s>"
];

/* ウィジェット設定 － 担当者表示名種別 */
$config['widgetShowNameType'] = [
    C_WIDGET_SHOW_NAME => "担当者名を表示する<br>　<s>※ユーザーマスタの「表示名」に設定された名称を表示します</s>",
    C_WIDGET_SHOW_COMP => "企業名を表示する<br>　<s>※こちらの画面の「企業名」に設定された名称を表示します</s>"
];

/* ウィジェット設定 － 表示名種別 */
$config['widgetShowAutomessageNameType'] = [
  C_WIDGET_SHOW_AUTOMESSAGE_COMP => "企業名を表示する<br>　<s>※オートメッセージやSorryメッセージなど自動メッセージの吹き出しに企業名を表示します</s>",
  C_WIDGET_SHOW_AUTOMESSAGE_NONE => "表示しない"
];

/* ウィジェット設定 － 表示名種別 */
$config['widgetShowOpNameType'] = [
  C_WIDGET_SHOW_NAME => "担当者名を表示する<br>　<s>※ユーザーマスタの「表示名」に設定された名称を表示します</s>",
  C_WIDGET_SHOW_COMP => "企業名を表示する<br>　<s>※こちらの画面の「企業名」に設定された名称を表示します</s>",
  C_WIDGET_SHOW_NONE => "表示しない"
];

/* ウィジェット設定 － 吹き出しデザイン */
$config['chatMessageDesignType'] = [
  C_WIDGET_CHAT_MESSAGE_DESIGN_TYPE_BOX => "BOX型",
  C_WIDGET_CHAT_MESSAGE_DESIGN_TYPE_BALLOON => "吹き出し型"
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

$config['widgetSpMiximizeSizeType'] = [
  C_SELECT_CAN => "余白を残して表示する",
  C_SELECT_CAN_NOT => "画面いっぱいに表示する"
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
            "stayTimeCheckType" => 2,
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
            "visitCntCond" => 2
        ]
    ],
    // 発言内容
    C_AUTO_TRIGGER_SPEECH_CONTENT => [
        'label' => '発言内容',
        'createLimit' => [C_COINCIDENT => 1, C_SOME_EITHER => 1],
        'key' => 'speech_content',
        'default' => [
            "keyword_contains" => "",
            "keyword_contains_type" => "1",
            "keyword_exclusions" => "",
            "keyword_exclusions_type" => "1",
            "speechContentCond" => "1",
            "triggerTimeSec" => 2,
            "speechTriggerCond" => "2"
        ]
    ],
    // ページ
    C_AUTO_TRIGGER_STAY_PAGE => [
        'label' => 'ページ',
        'createLimit' => [C_COINCIDENT => 1, C_SOME_EITHER => 1],
        'key' => 'stay_page',
        'default' => [
            "targetName" => 2,
            "keyword_contains" => "",
            "keyword_contains_type" => "1",
            "keyword_exclusions" => "",
            "keyword_exclusions_type" => "1",
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
            "keyword_contains" => "",
            "keyword_contains_type" => "1",
            "keyword_exclusions" => "",
            "keyword_exclusions_type" => "1",
            "referrerCond" => 2
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
          "targetName" => 2,
          "keyword_contains" => "",
          "keyword_contains_type" => "1",
          "keyword_exclusions" => "",
          "keyword_exclusions_type" => "1",
          "stayPageCond" => 2
      ]
    ],
    // 前のページ
    C_AUTO_TRIGGER_STAY_PAGE_OF_PREVIOUS => [
      'label' => '前のページ',
      'createLimit' => [C_COINCIDENT => 1, C_SOME_EITHER => 1],
      'key' => 'stay_page_of_previous',
      'default' => [
          "targetName" => 2,
          "keyword_contains" => "",
          "keyword_contains_type" => "1",
          "keyword_exclusions" => "",
          "keyword_exclusions_type" => "1",
          "stayPageCond" => 2
      ]
    ],
    // 営業時間設定
    C_AUTO_TRIGGER_OPERATING_HOURS => [
      'label' => '営業時間',
      'createLimit' => [C_COINCIDENT => 1, C_SOME_EITHER => 1],
      'key' => 'operating_hours',
      'default' => [
         "operatingHoursTime" => 1
      ]
    ]
];

/* オートメッセージ － アクション種別 */
$config['outMessageActionType'] = [
    C_AUTO_ACTION_TYPE_SENDMESSAGE => "チャットメッセージを送る",
    C_AUTO_ACTION_TYPE_SELECTSCENARIO => "シナリオを選択する"
];

/* オートメッセージ － ウィジェット種別 */
$config['outMessageWidgetOpenType'] = [
    C_AUTO_WIDGET_TYPE_OPEN => "自動で最大化する",
    C_AUTO_WIDGET_TYPE_CLOSE => "自動で最大化しない"
];

/* オートメッセージ － 自由入力種別 */
$config['outMessageTextarea'] = [
    C_AUTO_WIDGET_TEXTAREA_OPEN => "ON（自由入力可）",
    C_AUTO_WIDGET_TEXTAREA_CLOSE => "OFF（自由入力不可）"
];

/* オートメッセージ － cv種別 */
$config['outMessageCvType'] = [
    C_AUTO_CV_EFFECTIVENESS => "する",
    C_AUTO_CV_DISABLED => "しない"
];

/* シナリオ設定 - アクション種別 */
$config['chatbotScenarioActionList'] = [
  // テキスト発言
  C_SCENARIO_ACTION_TEXT => [
    'label' => 'テキスト発言',
    'default' => [
      'messageIntervalTimeSec' => '2',
      'chatTextArea' => '2',
      'message' => ''
    ]
  ],
  // ヒアリング
  C_SCENARIO_ACTION_HEARING => [
    'label' => 'ヒアリング',
    'default' => [
      'messageIntervalTimeSec' => '2',
      'chatTextArea' => '1',
      'hearings' => [[
        'variableName' => '',
        'inputType' => C_SCENARIO_INPUT_TYPE_TEXT,
        'uiType' => '1',
        'message' => '',
        'required' => true,
        'errorMessage' => '',
        'settings' => [
          'options' => [""], // options for radio or pulldown
          'disablePastDate' => true,
          'isSetDisableDate' => false,
          'isDisableDayOfWeek' => false,
          'isSetSpecificDate' => false,
          'isEnableAfterDate' => false,
          'enableAfterDate' => null,
          'dayOfWeekSetting' => [
            0 => false, // sun
            1 => false, // mon
            2 => false, // tue
            3 => false, // wed
            4 => false, // thur
            5 => false, // fri
            6 => false, // sat
          ],
          'setSpecificDateType' => '',
          'specificDateData' => [""],
          'language' => 1, // 1: japanese, 2: english
          'pulldownCustomDesign' => false,
          'calendarCustomDesign' => false,
          'customDesign' => [
            'borderColor' => '',
            'backgroundColor' => '#FFFFFF',
            'textColor' => '',
            'headerBackgroundColor' => '',
            'headerTextColor' => '#FFFFFF',
            'headerWeekdayBackgroundColor' => '',
            'calendarBackgroundColor' => '#FFFFFF',
            'calendarTextColor' => '',
            'saturdayColor' => '',
            'sundayColor' => '',
          ]
        ]
      ]
      ],
      'restore' => true,
      'isConfirm' => '2',
      'confirmMessage' => '',
      'success' => '',
      'cancel' => '',
      'cv' => '2'
    ]
  ],
  // 選択肢
  C_SCENARIO_ACTION_SELECT_OPTION => [
    'label' => '選択肢',
    'default' => [
      'messageIntervalTimeSec' => '2',
      'chatTextArea' => '2',
      'selection' => [
        'variableName' => '',
        'options' => ['']
      ]
    ]
  ],
  // メール送信
  C_SCENARIO_ACTION_SEND_MAIL => [
    'label' => 'メール送信',
    'default' => [
      'messageIntervalTimeSec' => '2',
      'chatTextArea' => '2',
      'toAddress' => [''],
      'mailType' => C_SCENARIO_MAIL_TYPE_ALL_MESSAGE
    ]
  ],
  // シナリオ呼び出し
  C_SCENARIO_ACTION_CALL_SCENARIO => [
    'label' => 'シナリオ呼出',
    'default' => [
      'messageIntervalTimeSec' => '2',
      'chatTextArea' => '2',
      'scenarioId' => '',
      'executeNextAction' => '2'
    ]
  ],
  // 属性値取得
  C_SCENARIO_ACTION_GET_ATTRIBUTE => [
    'label' => '属性値取得',
    'default' => [
      'messageIntervalTimeSec' => '2',
      'chatTextArea' => '2',
      'getAttributes' => [[
        'variableName' => '',
        'type' => C_SCENARIO_ATTRIBUTE_TYPE_ID,
        'attributeValue' => '',
      ]]
    ]
  ],  // 外部システム連携
  C_SCENARIO_ACTION_EXTERNAL_API => [
    'label' => '外部連携',
    'default' => [
      'messageIntervalTimeSec' => '2',
      'chatTextArea' => '2',
      'externalType' => '1',
      'methodType' => '1',
      'requestHeaders' => [[
        'name' => '',
        'value' => ''
      ]],
      'requestBody' => '',
      'responseType' => '0',
      'responseBodyMaps' => [[
        'sourceKey' => '',
        'variableName' => ''
      ]]
    ]
  ],
  // ファイル送信
  C_SCENARIO_ACTION_SEND_FILE => [
    'label' => 'ファイル送信',
    'default' => [
      'messageIntervalTimeSec' => '2',
      'chatTextArea' => '2',
      'file' => ''
    ]
  ]
  ,
  // ファイル受信
  C_SCENARIO_ACTION_RECEIVE_FILE => [
    'label' => 'ファイル受信',
    'default' => [
      'chatTextArea' => '2',
      'dropAreaMessage' => 'ここにファイルをドロップ
してください',
      'receiveFileType' => '1',
      'errorMessage' => '選択されたファイルはアップロードすることができません。',
      'extendedReceiveFileExtensions' => '',
      'cancelEnabled' => false,
      'cancelLabel' => 'ファイル送信をキャンセルする'
    ]
  ]
  ,
  // 条件分岐
  C_SCENARIO_ACTION_BRANCH_ON_CONDITION => [
    'label' => '条件分岐',
    'default' => [
      'chatTextArea' => '2',
      'referenceVariable' => "",
      'conditionList' => [
        [
          "matchValue" => "",
          "matchValueType" => "1", // のいずれかを含む場合
          "actionType" => "1", //テキスト発言
          "action" => [
            "message" => ""
          ]
        ]
      ],
      'elseEnabled' => 0,
      'elseAction' => [
        "actionType" => "1",
        "action" => [
          "message" => ""
        ]
      ]
    ]
  ],  // 属性値取得
  C_SCENARIO_ACTION_ADD_CUSTOMER_INFORMATION => [
    'label' => '訪問ユーザ登録',
    'default' => [
      'messageIntervalTimeSec' => '2',
      'chatTextArea' => '2',
      'addCustomerInformations' => [
        [
          'variableName' => '',
          'targetId' => ""
        ]
      ]
    ]
  ],  // 一括ヒアリング
  C_SCENARIO_ACTION_BULK_HEARING => [
    'label' => '一括ヒアリング',
    'default' => [
      'messageIntervalTimeSec' => '2',
      'chatTextArea' => '2',
      'multipleHearing' => [
        [
          'variableName' => '',
          'inputType' => "1",
          'label' => "",
          'required' => true
        ]
      ]
    ]
  ]
];

/* シナリオ設定 - ヒアリング入力タイプ */
$config['chatbotScenarioInputType'] = [
  C_SCENARIO_INPUT_TYPE_TEXT => [
    'label' => '@text',
    'rule' => C_MATCH_RULE_TEXT,
    'inputRule' => C_MATCH_INPUT_RULE_ALL
  ],
  C_SCENARIO_INPUT_TYPE_NUMBER => [
    'label' => '@number',
    'rule' => C_MATCH_RULE_NUMBER,
    'inputRule' => C_MATCH_INPUT_RULE_NUMBER
  ],
  C_SCENARIO_INPUT_TYPE_EMAIL => [
    'label' => '@email',
    'rule' => C_MATCH_RULE_EMAIL,
    'inputRule' => C_MATCH_INPUT_RULE_EMAIL
  ],
  C_SCENARIO_INPUT_TYPE_TEL => [
    'label' => '@tel_number',
    'rule' => C_MATCH_RULE_TEL,
    'inputRule' => C_MATCH_INPUT_RULE_TEL
  ]
];

/* シナリオ設定 - 属性タイプ */
$config['chatbotScenarioAttributeType'] = [
  C_SCENARIO_ATTRIBUTE_TYPE_ID => [
    'label' => '@id',
    'rule' => C_MATCH_RULE_TEXT,
    'inputRule' => C_MATCH_INPUT_RULE_ALL
  ],
  C_SCENARIO_ATTRIBUTE_TYPE_NAME => [
    'label' => '@name',
    'rule' => C_MATCH_RULE_NUMBER,
    'inputRule' => C_MATCH_INPUT_RULE_NUMBER
  ],
  C_SCENARIO_ATTRIBUTE_TYPE_SELECTOR => [
    'label' => '@cssセレクタ',
    'rule' => C_MATCH_RULE_TEXT,
    'inputRule' => C_MATCH_INPUT_RULE_ALL
  ]
];

/* シナリオ設定 - メール送信タイプ */
$config['chatbotScenarioSendMailType'] = [
  C_SCENARIO_MAIL_TYPE_ALL_MESSAGE => [
    'label' => 'チャット内容をすべてメールする',
    'tooltip' => 'それまでのすべてのチャットやり取り内容すべてをメールします。'
  ],
  C_SCENARIO_MAIL_TYPE_VARIABLES => [
    'label' => '変数の値のみメールする',
    'tooltip' => 'ヒアリングおよび選択肢にて入力（または選択）された内容をメールします。'
  ],
  C_SCENARIO_MAIL_TYPE_CUSTOMIZE => [
    'label' => 'メール本文をカスタマイズする',
    'tooltip' => '自由にメール本文を編集することが可能です。<br>（変数の利用も可能です）'
  ]
];

/* シナリオ設定 - メール送信タイプ */
$config['chatbotScenarioReceiveFileTypeList'] = [
  C_SCENARIO_RECEIVE_FILE_TYPE_BASIC => [
    'label' => '一般的なファイルに限定',
    'annotation' => '※PDF（pdf）、PowerPoint（ppt, pptx）、JPEG（jpg）、PNG（png）、GIF（gif）に制限されます。',
    'tooltip' => '送信可能なファイル形式を、PDF（pdf）、PowerPoint（ppt, pptx）、JPEG（jpg）、PNG（png）、GIF（gif）に制限します。'
  ],
  C_SCENARIO_RECEIVE_FILE_TYPE_EXTENDED => [
    'label' => '拡張設定',
    'annotation' => '※送信可能なファイルの拡張子を指定します。複数の拡張子を指定する場合はカンマ（,）で区切ります。',
    'tooltip' => 'PDF（pdf）、PowerPoint（ppt, pptx）、JPEG（jpg）、PNG（png）、GIF（gif）に加え、下記に指定した拡張子のファイルを受信可能とします。<br>複数の拡張子を指定する必要がある場合は、カンマ区切りで設定します。'
  ]
];

/* シナリオ設定 - 外部連携のタイプ */
$config['chatbotScenarioExternalType'] = [
  C_SCENARIO_EXTERNAL_TYPE_API => 'API連携',
  C_SCENARIO_EXTERNAL_TYPE_SCRIPT => 'スクリプト'
];

/* シナリオ設定 - 外部連携のメソッド種別 */
$config['chatbotScenarioApiMethodType'] = [
  C_SCENARIO_METHOD_TYPE_GET => 'GET',
  C_SCENARIO_METHOD_TYPE_POST => 'POST'
];

/* シナリオ設定 - 外部システム連携のレスポンス種別 */
$config['chatbotScenarioApiResponseType'] = [
  0 => 'JSON'
];

/* シナリオ設定 - 条件分岐 - 実行するアクション */
$config['chatbotScenarioBranchOnConditionMatchValueType'] = [
  C_SCENARIO_VARIABLE_CONDITION_IN => [
    'label' => 'のいずれかを含む'
  ],
  C_SCENARIO_VARIABLE_CONDITION_NOT_IN => [
    'label' => 'のいずれも含まない場合'
  ]
];

/* シナリオ設定 - 条件分岐 - 実行するアクション */
$config['chatbotScenarioBranchOnConditionActionType'] = [
  C_SCENARIO_PROCESS_ACTION_TYPE_SPEECH_TEXT => [
    'label' => 'テキスト発言'
  ],
  C_SCENARIO_PROCESS_ACTION_TYPE_CALL_SCENARIO => [
    'label' => 'シナリオ呼出'
  ],
  C_SCENARIO_PROCESS_ACTION_TYPE_TERMINATE => [
    'label' => 'シナリオを終了'
  ],
  C_SCENARIO_PROCESS_ACTION_TYPE_NONE => [
    'label' => '次のアクションへ'
  ]
];

/* シナリオ設定 - 条件分岐 - 実行するアクション（上記を満たさない） */
  $config['chatbotScenarioBranchOnConditionElseActionType'] = [
  C_SCENARIO_PROCESS_ACTION_TYPE_SPEECH_TEXT => [
    'label' => 'テキスト発言'
  ],
  C_SCENARIO_PROCESS_ACTION_TYPE_CALL_SCENARIO => [
    'label' => 'シナリオ呼出'
  ],
  C_SCENARIO_PROCESS_ACTION_TYPE_TERMINATE => [
    'label' => 'シナリオを終了'
  ]
];

/* 成果種別 */
$config['achievementType'] = [
  C_ACHIEVEMENT_CV => "CV",
  C_ACHIEVEMENT_AVAILABLE => "有効",
  C_ACHIEVEMENT_UNAVAILABLE => "無効",
];

/* 成果種別（検索用） */
$config['achievementTypeForSearch'] = [
  C_ACHIEVEMENT_CV => "CV",
  C_ACHIEVEMENT_TERMINATE_SCENARIO => "途中離脱",
  C_ACHIEVEMENT_AVAILABLE => "有効",
  C_ACHIEVEMENT_UNAVAILABLE => "無効",
];

/* 種別 */
$config['chatType'] = [
  C_CHAT_AUTO => "自動返信",
  C_CHAT_MANUAL => "",
  C_CHAT_NOENTRY => "未入室",
  C_CHAT_SORRY => "拒否"
];

/* ファイル送信設定 - ファイル送信許可設定 */
/* 通常選択肢 */
$config['fileTransferSettingType'] = [
  C_FILE_TRANSFER_SETTING_TYPE_BASIC => "基本設定<br>　<s>※ 送信できるファイルはPDF（pdf）、PowerPoint（ppt, pptx）、JPEG（jpg）、PNG（png）、GIF（gif）に制限されます。</s>",
  C_FILE_TRANSFER_SETTING_TYPE_EXTEND => "拡張設定<br>　<s>※ 基本設定で送信できるファイルに加えて、指定したファイルの種類を許可します。</s>"
];

/* セキュリティ設定 - ログインIP許可設定 */
/* 通常選択肢 */
$config['securityEnableLoginIpFilterSetting'] = [
    0 => "利用しない", // FIXME 定数化
    1 => "ホワイトリスト登録する",
    2 => "ブラックリスト登録する"
];

/* 無料トライアル設定 － ビジネスモデル */
$config['businessModelType'] = [
    C_FREE_B_TO_B => "BtoB",
    C_FREE_B_TO_C => "BtoC",
    C_FREE_BOTH => "どちらも"
];
