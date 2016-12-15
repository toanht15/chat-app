<?php 
class AppSchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $m_administrators = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'user_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => 'ユーザー名', 'charset' => 'utf8'),
		'mail_address' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'comment' => 'メールアドレス', 'charset' => 'utf8'),
		'password' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => 'パスワード', 'charset' => 'utf8'),
		'del_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '削除フラグ'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '登録日'),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '登録実行ユーザ'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日'),
		'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '更新実行ユーザ'),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '削除日'),
		'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '削除実行ユーザ'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_agreements = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '企業マスタID'),
		'application_day' => array('type' => 'date', 'null' => false, 'default' => null, 'comment' => '申込日'),
		'agreement_start_day' => array('type' => 'date', 'null' => false, 'default' => null, 'comment' => '契約開始日'),
		'agreement_end_day' => array('type' => 'date', 'null' => false, 'default' => null, 'comment' => '契約終了日'),
		'application_department' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => '申し込み情報部署名', 'charset' => 'utf8'),
		'application_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => '申し込み情報名前', 'charset' => 'utf8'),
		'administrator_department' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => '管理者情報部署名', 'charset' => 'utf8'),
		'administrator_position' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => '管理者情報役職名', 'charset' => 'utf8'),
		'administrator_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => '管理者情報名前', 'charset' => 'utf8'),
		'installation_site_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => '設置サイト名', 'charset' => 'utf8'),
		'installation_url' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'comment' => '設置サイトURL', 'charset' => 'utf8'),
		'admin_password' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => 'スーパー管理者用パスワード', 'charset' => 'utf8'),
		'telephone_number' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => '電話番号', 'charset' => 'utf8'),
		'note' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '備考', 'charset' => 'utf8'),
		'del_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '削除フラグ'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '登録日'),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '登録実行ユーザ'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日'),
		'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '更新実行ユーザ'),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '削除日'),
		'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '削除実行ユーザ'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_chat_notifications = array(
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
			'm_companies_id' => array('column' => 'm_companies_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_companies = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'company_key' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => '企業キー', 'charset' => 'utf8'),
		'company_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'comment' => '企業名', 'charset' => 'utf8'),
		'm_contact_types_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '契約タイプ'),
		'limit_users' => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false, 'comment' => '契約ID数'),
		'exclude_params' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '除外パラメータ', 'charset' => 'utf8'),
		'exclude_ips' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '除外IPアドレス', 'charset' => 'utf8'),
		'core_settings' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'comment' => '仕様機能内容', 'charset' => 'utf8'),
		'trial_flg' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '試用フラグ'),
		'del_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '削除フラグ'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '登録日'),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '登録実行ユーザ'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日'),
		'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '更新実行ユーザ'),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '削除日'),
		'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '削除実行ユーザ'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_customers = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '企業ID'),
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
			'm_companies_id_idx' => array('column' => array('m_companies_id', 'visitors_id'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_document_tags = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '企業マスタID'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => 'タグ', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '企業マスタID'),
		'user_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => 'ユーザー名', 'charset' => 'utf8'),
		'display_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => '表示名', 'charset' => 'utf8'),
		'mail_address' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'comment' => 'メールアドレス', 'charset' => 'utf8'),
		'password' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => 'パスワード', 'charset' => 'utf8'),
		'permission_level' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '権限レベル'),
		'settings' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'その他個別設定', 'charset' => 'utf8'),
		'operation_list_columns' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 500, 'collate' => 'utf8_general_ci', 'comment' => 'リアルタイムモニタ一覧表示項目リスト', 'charset' => 'utf8'),
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
			'm_companies_id' => array('column' => 'm_companies_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_widget_settings = array(
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
			'm_companies_id' => array('column' => 'm_companies_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $schema_migrations = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'class' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'type' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_auto_messages = array(
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
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_campaigns = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '企業ID'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => 'キャンペーン名', 'charset' => 'utf8'),
		'parameter' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => 'URLパラメータ', 'charset' => 'utf8'),
		'comment' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'comment' => 'コメント', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '登録日'),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '登録実行ユーザ'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日'),
		'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '更新実行ユーザ'),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '削除日'),
		'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '削除実行ユーザ'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_dictionaries = array(
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
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_documents = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '企業マスタID'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 30, 'collate' => 'utf8_general_ci', 'comment' => '資料名', 'charset' => 'utf8'),
		'overview' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'comment' => '概要', 'charset' => 'utf8'),
		'tag' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'タグ', 'charset' => 'utf8'),
		'file_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'comment' => '資料ファイル名', 'charset' => 'utf8'),
		'manuscript' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '原稿', 'charset' => 'utf8'),
		'download_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => 'ダウンロードフラグ'),
		'pagenation_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => 'ページ数表示フラグ'),
		'password' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => 'パスワード', 'charset' => 'utf8'),
		'del_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '削除フラグ'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '登録日'),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '登録実行ユーザ'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日'),
		'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '更新実行ユーザ'),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '削除日'),
		'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '削除実行ユーザ'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_histories = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '企業マスタID'),
		'visitors_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => '訪問者ID', 'charset' => 'utf8'),
		'ip_address' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 15, 'collate' => 'utf8_general_ci', 'comment' => 'IPアドレス', 'charset' => 'utf8'),
		'tab_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'comment' => 'タブID', 'charset' => 'utf8'),
		'user_agent' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'comment' => 'ユーザーエージェント', 'charset' => 'utf8'),
		'access_date' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'アクセス開始日時'),
		'out_date' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'アクセス終了日時'),
		'referrer_url' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'comment' => 'リファラー情報', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '登録日'),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '登録実行ユーザ'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日'),
		'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '更新実行ユーザ'),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '削除日'),
		'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '削除実行ユーザ'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'm_companies_idx' => array('column' => 'm_companies_id', 'unique' => 0),
			'company_visitor' => array('column' => array('m_companies_id', 'visitors_id'), 'unique' => 0),
			'company_ip' => array('column' => array('m_companies_id', 'ip_address'), 'unique' => 0),
			'company_access_date' => array('column' => array('m_companies_id', 'access_date'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_chat_logs = array(
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
			't_histories_id_idx' => array('column' => 't_histories_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_share_displays = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		't_histories_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '履歴ID'),
		'm_users_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '対応ユーザーID'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			't_histories_id' => array('column' => 't_histories_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_stay_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		't_histories_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '履歴ID'),
		'title' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => 'ページタイトル', 'charset' => 'utf8'),
		'url' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'comment' => 'URL', 'charset' => 'utf8'),
		'stay_time' => array('type' => 'time', 'null' => false, 'default' => null, 'comment' => '滞在時間'),
		'del_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '削除フラグ'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '登録日'),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '登録実行ユーザ'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日'),
		'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '更新実行ユーザ'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			't_histories_id_idx' => array('column' => 't_histories_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

}
