<?php
class CreateDb extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'create_db';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'm_chat_notifications' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
					'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '企業ID'),
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => '通知名', 'charset' => 'utf8'),
					'type' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '対象'),
					'keyword' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => 'ｷｰﾜｰﾄﾞ', 'charset' => 'utf8'),
					'image' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => 'アイコン画像', 'charset' => 'utf8'),
					'del_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '削除フラグ'),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '登録日'),
					'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '登録実行ユーザ'),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日'),
					'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '更新実行ユーザ'),
					'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '削除日'),
					'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '削除実行ユーザ'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'm_companies_id' => array('column' => 'm_companies_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'm_companies' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
					'company_key' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => '企業キー', 'charset' => 'utf8'),
					'company_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'comment' => '企業名', 'charset' => 'utf8'),
					'admin_mail_address' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => '管理者アドレス', 'charset' => 'utf8'),
					'admin_password' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => '管理者パスワード', 'charset' => 'utf8'),
					'm_contact_types_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '契約タイプ'),
					'limit_users' => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false, 'comment' => '契約ID数'),
					'core_settings' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'comment' => '仕様機能内容', 'charset' => 'utf8'),
					'del_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '削除フラグ'),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '登録日'),
					'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '登録実行ユーザ'),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日'),
					'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '更新実行ユーザ'),
					'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '削除日'),
					'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '削除実行ユーザ'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'm_customers' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
					'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '企業ID'),
					'visitors_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => 'ユーザーID', 'charset' => 'utf8'),
					'informations' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '情報', 'charset' => 'utf8'),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '登録日'),
					'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '登録実行ユーザ'),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日'),
					'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '更新実行ユーザ'),
					'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '削除日'),
					'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '削除実行ユーザ'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'm_users' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
					'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '企業マスタID'),
					'user_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => 'ユーザー名', 'charset' => 'utf8'),
					'display_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => '表示名', 'charset' => 'utf8'),
					'mail_address' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'comment' => 'メールアドレス', 'charset' => 'utf8'),
					'password' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => 'パスワード', 'charset' => 'utf8'),
					'permission_level' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '権限レベル'),
					'settings' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'その他個別設定', 'charset' => 'utf8'),
					'operation_list_columns' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => 'リアルタイムモニタ一覧表示項目リスト', 'charset' => 'utf8'),
					'history_list_columns' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => '履歴一覧表示項目リスト', 'charset' => 'utf8'),
					'session_rand_str' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => '多重ログイン防止用文字列', 'charset' => 'utf8'),
					'del_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '削除フラグ'),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '登録日'),
					'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '登録実行ユーザ'),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日'),
					'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '更新実行ユーザ'),
					'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '削除日'),
					'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '削除実行ユーザ'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'm_companies_id' => array('column' => 'm_companies_id', 'unique' => 0),
						'm_companies_id_2' => array('column' => 'm_companies_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'm_widget_settings' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
					'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '企業マスタID'),
					'display_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '表示種別'),
					'style_settings' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'スタイル設定', 'charset' => 'utf8'),
					'del_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '削除フラグ'),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '登録日'),
					'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '登録実行ユーザ'),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日'),
					'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '更新実行ユーザ'),
					'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '削除日'),
					'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '削除実行ユーザ'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'm_companies_id' => array('column' => 'm_companies_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				't_auto_messages' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
					'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '企業ID'),
					'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'comment' => 'オートメッセージ名称', 'charset' => 'utf8'),
					'trigger_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => 'トリガーの種類'),
					'activity' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'オートメッセージ設定内容', 'charset' => 'utf8'),
					'action_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => 'アクションの種類'),
					'active_flg' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '0:有効、1:無効'),
					'del_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '削除フラグ'),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '登録日'),
					'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '登録実行ユーザ'),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日'),
					'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '更新実行ユーザ'),
					'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '削除日'),
					'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '削除実行ユーザ'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				't_dictionaries' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
					'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '企業ID'),
					'm_users_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => 'ユーザーID'),
					'word' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '文章', 'charset' => 'utf8'),
					'type' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'タイプ'),
					'sort' => array('type' => 'integer', 'null' => true, 'default' => '999', 'unsigned' => false, 'comment' => 'ソート順'),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '登録日'),
					'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '登録実行ユーザ'),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日'),
					'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '更新実行ユーザ'),
					'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '削除日'),
					'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '削除実行ユーザ'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				't_histories' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
					'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '企業マスタID'),
					'visitors_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => '訪問者ID', 'charset' => 'utf8'),
					'ip_address' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 15, 'collate' => 'utf8_general_ci', 'comment' => 'IPアドレス', 'charset' => 'utf8'),
					'tab_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'comment' => 'タブID', 'charset' => 'utf8'),
					'user_agent' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'comment' => 'ユーザーエージェント', 'charset' => 'utf8'),
					'access_date' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'アクセス開始日時'),
					'out_date' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'アクセス終了日時'),
					'referrer_url' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'comment' => 'リファラー情報', 'charset' => 'utf8'),
					'del_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '削除フラグ'),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '登録日'),
					'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '登録実行ユーザ'),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日'),
					'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '更新実行ユーザ'),
					'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '削除日'),
					'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '削除実行ユーザ'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				't_history_chat_logs' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
					't_histories_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '履歴ID'),
					't_history_stay_logs_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '移動履歴TBLのID'),
					'visitors_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => '訪問者ID', 'charset' => 'utf8'),
					'm_users_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '対応ユーザーID'),
					'message' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 500, 'collate' => 'utf8_general_ci', 'comment' => 'メッセージ', 'charset' => 'utf8'),
					'message_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => 'メッセージ種別（1:訪問者から、2:企業側から）'),
					'message_read_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '既読フラグ'),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'length' => 2),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						't_histories_id' => array('column' => 't_histories_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				't_history_share_displays' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
					't_histories_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '履歴ID'),
					'm_users_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '対応ユーザーID'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						't_histories_id' => array('column' => 't_histories_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				't_history_stay_logs' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
					't_histories_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '履歴ID'),
					'title' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => 'ページタイトル', 'charset' => 'utf8'),
					'url' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'comment' => 'URL', 'charset' => 'utf8'),
					'stay_time' => array('type' => 'time', 'null' => false, 'default' => null, 'comment' => '滞在時間'),
					'del_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '削除フラグ'),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '登録日'),
					'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '登録実行ユーザ'),
					'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日'),
					'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '更新実行ユーザ'),
					'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '削除日'),
					'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '削除実行ユーザ'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'm_chat_notifications', 'm_companies', 'm_customers', 'm_users', 'm_widget_settings', 't_auto_messages', 't_dictionaries', 't_histories', 't_history_chat_logs', 't_history_share_displays', 't_history_stay_logs'
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		return true;
	}
}
