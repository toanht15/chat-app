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
		'company_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '企業マスタID', 'charset' => 'utf8'),
		'business_model' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'application_day' => array('type' => 'date', 'null' => false, 'default' => null, 'comment' => '申込日'),
		'trial_start_day' => array('type' => 'date', 'null' => true, 'default' => null, 'comment' => 'トライアル開始日'),
		'trial_end_day' => array('type' => 'date', 'null' => true, 'default' => null, 'comment' => 'トライアル終了日'),
		'agreement_start_day' => array('type' => 'date', 'null' => true, 'default' => null, 'comment' => '契約開始日'),
		'agreement_end_day' => array('type' => 'date', 'null' => true, 'default' => null, 'comment' => '契約終了日'),
		'application_department' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => '申し込み情報部署名', 'charset' => 'utf8'),
		'application_position' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => '申し込み情報役職名', 'charset' => 'utf8'),
		'application_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => '申し込み情報名前', 'charset' => 'utf8'),
		'application_mail_address' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '申込み時メールアドレス', 'charset' => 'utf8'),
		'administrator_department' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => '管理者情報部署名', 'charset' => 'utf8'),
		'administrator_position' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => '管理者情報役職名', 'charset' => 'utf8'),
		'administrator_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => '管理者情報名前', 'charset' => 'utf8'),
		'administrator_mail_address' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '管理者情報メールアドレス', 'charset' => 'utf8'),
		'installation_site_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => '設置サイト名', 'charset' => 'utf8'),
		'installation_url' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'comment' => '設置サイトURL', 'charset' => 'utf8'),
		'admin_password' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => 'スーパー管理者用パスワード', 'charset' => 'utf8'),
		'telephone_number' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => '電話番号', 'charset' => 'utf8'),
		'note' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '備考', 'charset' => 'utf8'),
		'memo' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
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

	public $m_chat_settings = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '企業ID'),
		'sc_flg' => array('type' => 'integer', 'null' => true, 'default' => '2', 'unsigned' => false, 'comment' => 'sc(Simultaneous correspondence).　チャットの同時対応数の設定. 1:有効, 2:無効'),
		'in_flg' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'sc_default_num' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'チャットの基本同時対応数'),
		'outside_hours_sorry_message' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'wating_call_sorry_message' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'no_standby_sorry_message' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'sorry_message' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'sorryメッセージ', 'charset' => 'utf8'),
		'initial_notification_message' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'sc_login_default_status' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => 'ログイン後初期ステータス'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_companies = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'company_key' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => '企業キー', 'charset' => 'utf8'),
		'company_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'comment' => '企業名', 'charset' => 'utf8'),
		'm_contact_types_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '契約タイプ'),
		'limit_users' => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false, 'comment' => '契約ID数'),
		'la_limit_users' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false),
		'exclude_params' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '除外パラメータ', 'charset' => 'utf8'),
		'exclude_ips' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '除外IPアドレス', 'charset' => 'utf8'),
		'core_settings' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'keep_history_days' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 5, 'unsigned' => false, 'comment' => '履歴保持期間（0は無制限）'),
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

	public $m_file_transfer_settings = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 2, 'unsigned' => false),
		'allow_extensions' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'updated' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'updated_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_ip_filter_settings = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'active_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '0：無効、１：有効'),
		'filter_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 2, 'unsigned' => false, 'comment' => '１：ホワイトリスト形式、２：ブラックリスト形式'),
		'ips' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_job_mail_templates = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'mail_type_cd' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '名称', 'charset' => 'utf8'),
		'value_type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'value' => array('type' => 'integer', 'null' => false, 'default' => '9', 'unsigned' => false),
		'time' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '時間'),
		'sender' => array('type' => 'string', 'null' => false, 'default' => '（sinclo）', 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'subject' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'メールタイトル', 'charset' => 'utf8'),
		'mail_body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'メール本文', 'charset' => 'utf8'),
		'agreement_flg' => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false),
		'send_mail_ml_flg' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'send_mail_application_user_flg' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'send_mail_administrator_user_flg' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'send_mail_sinclo_all_users_flg' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_landscape_data = array(
		'lbc_code' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'ip_address' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 15, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_zip_code' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 8, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_address' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_tel' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 13, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_fax' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 13, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_ipo_type' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_date' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_capital_code' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_employees_code' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_gross_code' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_president' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_industrial_category_m' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_url' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'houjin_bangou' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'houjin_address' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'updated' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => array('lbc_code', 'ip_address'), 'unique' => 1),
			'idx_ip_address_lbc_code_org_name' => array('column' => array('ip_address', 'lbc_code', 'org_name'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_mail_templates = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'mail_type_cd' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 5, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'template' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_mail_transmission_settings = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'from_address' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'from_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'to_address' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'to_name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'cc_address' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'cc_name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'bcc_address' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'bcc_name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'subject' => array('type' => 'string', 'null' => true, 'default' => 'no title', 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_operating_hours = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'time_settings' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'active_flg' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'type' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_system_mail_templates = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'mail_type_cd' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'タイプ', 'charset' => 'utf8'),
		'sender' => array('type' => 'string', 'null' => false, 'default' => '（sinclo）', 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'subject' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '件名', 'charset' => 'utf8'),
		'mail_body' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '本文', 'charset' => 'utf8'),
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
		'change_password_flg' => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false),
		'permission_level' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '権限レベル'),
		'settings' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'その他個別設定', 'charset' => 'utf8'),
		'operation_list_columns' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 500, 'collate' => 'utf8_general_ci', 'comment' => 'リアルタイムモニタ一覧表示項目リスト', 'charset' => 'utf8'),
		'history_list_columns' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => '履歴一覧表示項目リスト', 'charset' => 'utf8'),
		'session_rand_str' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => '多重ログイン防止用文字列', 'charset' => 'utf8'),
		'chat_history_screen_flg' => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false),
		'del_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '削除フラグ'),
		'error_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'locked_datetime' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ロックした日時'),
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

	public $public_holidays = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'year' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'month' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'day' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
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
		'send_mail_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'm_mail_transmission_settings_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false),
		'm_mail_template_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false),
		't_chatbot_scenario_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'チャットボットシナリオID'),
		'active_flg' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '0:有効、1:無効'),
		'del_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '削除フラグ'),
		'sort' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => 'ソート順'),
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
		'sort' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => 'ソート順'),
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

	public $t_chatbot_scenario_send_files = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '企業ID'),
		'download_url' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'ダウンロード用URL', 'charset' => 'utf8'),
		'file_path' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '取得先パス', 'charset' => 'utf8'),
		'file_name' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 200, 'collate' => 'utf8_general_ci', 'comment' => 'ファイル名', 'charset' => 'utf8'),
		'file_size' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => 'ファイルサイズ'),
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

	public $t_chatbot_scenarios = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '企業ID'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'comment' => 'シナリオ名称', 'charset' => 'utf8'),
		'activity' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'シナリオ設定内容', 'charset' => 'utf8'),
		'del_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '削除フラグ'),
		'sort' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => 'ソート順'),
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

	public $t_conversation_count = array(
		'visitors_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'conversation_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_custom_variables = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'm_companies_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '企業ID'),
		'variable_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '変数名', 'charset' => 'utf8'),
		'type' => array('type' => 'integer', 'null' => false, 'default' => '3', 'length' => 2, 'unsigned' => false, 'comment' => 'タイプ'),
		'attribute_value' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 500, 'collate' => 'utf8_general_ci', 'comment' => '属性値', 'charset' => 'utf8'),
		'comment' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント', 'charset' => 'utf8'),
		'delete_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
		'sort' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => 'ソート順'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '作成日'),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '作成ユーザーID'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日'),
		'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '更新ユーザーID'),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '削除日時'),
		'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '削除ユーザーID'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_customer_information_settings = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'm_companies_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '企業ID
'),
		'item_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '項目名', 'charset' => 'utf8'),
		'input_type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 2, 'unsigned' => false, 'comment' => '入力タイプ
'),
		'input_option' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '入力タイプのオプション', 'charset' => 'utf8'),
		'show_realtime_monitor_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'リアルタイムモニタ表示可否'),
		'show_send_mail_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'メール送信時本文記載'),
		'sync_custom_variable_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => 'カスタム変数自動登録'),
		't_custom_variables_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '同期カスタム変数ID'),
		'comment' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'コメント', 'charset' => 'utf8'),
		'delete_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => '削除フラグ'),
		'sort' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'ソート順'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '作成日'),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '作製ユーザーID'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日'),
		'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '更新ユーザーID'),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '削除日時'),
		'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '削除ユーザーID'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_dictionaries = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '企業ID'),
		'm_users_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => 'ユーザーID'),
		'm_category_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false),
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

	public $t_dictionary_categories = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '企業ID'),
		'category_name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'カテゴリー名', 'charset' => 'utf8'),
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
		'settings' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'document info', 'charset' => 'utf8'),
		'del_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '削除フラグ'),
		'sort' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => 'ソート順'),
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

	public $t_external_api_connections = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '企業ID'),
		'url' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '連携先URL', 'charset' => 'utf8'),
		'method_type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'メソッド種別'),
		'request_headers' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'リクエストヘッダー情報', 'charset' => 'utf8'),
		'request_body' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'リクエストボディ情報', 'charset' => 'utf8'),
		'response_type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => 'レスポンスタイプ種別'),
		'response_body_maps' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'レスポンスボディからのデータ取得情報', 'charset' => 'utf8'),
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
			'company_access_date' => array('column' => array('m_companies_id', 'access_date'), 'unique' => 0),
			'company_tab_id' => array('column' => array('m_companies_id', 'tab_id'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_access_counts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'year' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false),
		'month' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'day' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'hour' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'access_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'company' => array('column' => array('m_companies_id', 'year', 'access_count', 'month', 'day', 'hour'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_chat_active_users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		't_history_chat_logs_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'm_users_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'idx_t_history_chat_logs_id_m_users_id' => array('column' => array('t_history_chat_logs_id', 'm_users_id'), 'unique' => 0),
			'idx_m_companies_id_users_id_chat_logs_id' => array('column' => array('m_companies_id', 'id', 'm_users_id', 't_history_chat_logs_id'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_chat_log_times = array(
		't_history_chat_logs_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		't_histories_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'unsigned' => false, 'key' => 'primary'),
		'datetime' => array('type' => 'datetime', 'null' => false, 'default' => null, 'length' => 2),
		'indexes' => array(
			'PRIMARY' => array('column' => array('t_history_chat_logs_id', 't_histories_id', 'type'), 'unique' => 1),
			'idx_t_history_chat_log_times_type_t_histories_id_datetime' => array('column' => array('type', 't_histories_id', 'datetime'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB', 'comment' => 'チャット履歴時間管理テーブル')
	);

	public $t_history_chat_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		't_histories_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '履歴ID'),
		't_history_stay_logs_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '移動履歴TBLのID'),
		'm_companies_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'visitors_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => '訪問者ID', 'charset' => 'utf8'),
		'm_users_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '対応ユーザーID'),
		'message' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 21800, 'collate' => 'utf8_general_ci', 'comment' => 'メッセージ', 'charset' => 'utf8'),
		'message_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => 'メッセージ種別（1:訪問者から、2:企業側から）'),
		'message_distinction' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'message_request_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'key' => 'index'),
		'notice_flg' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'message_read_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '既読フラグ'),
		'achievement_flg' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '成果フラグ(1:有効, 2:無効, null: 指定なし)'),
		'send_mail_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		't_mail_transmission_logs_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'delete_flg' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'hide_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'length' => 2),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			't_histories_id_idx' => array('column' => 't_histories_id', 'unique' => 0),
			'idx_t_history_chat_logs_message_type' => array('column' => 'message_type', 'unique' => 0),
			'idx_t_history_chat_logs_message_request_flg' => array('column' => 'message_request_flg', 'unique' => 0),
			'idx_t_history_chat_logs_achievement_flg' => array('column' => 'achievement_flg', 'unique' => 0),
			'idx_t_history_chat_logs_request_flg_companies_id_users_id' => array('column' => array('message_request_flg', 'm_companies_id', 'm_users_id', 't_histories_id', 'message_distinction', 'created'), 'unique' => 0),
			'idx_t_history_chat_logs_message_type_companies_id_users_id' => array('column' => array('message_type', 'm_companies_id', 'm_users_id', 't_histories_id', 'message_distinction', 'created'), 'unique' => 0),
			'idx_t_history_chat_logs_achievement_flg_companies_id' => array('column' => array('achievement_flg', 'm_companies_id', 't_histories_id'), 'unique' => 0),
			'idx_t_history_chat_logs_request_flg_companies_id' => array('column' => array('message_request_flg', 'm_companies_id', 't_histories_id', 'message_distinction', 'created'), 'unique' => 0),
			'idx_t_history_chat_logs_achievement_flg_companies_id_users_id' => array('column' => array('achievement_flg', 'm_companies_id', 'm_users_id', 't_histories_id'), 'unique' => 0),
			'idx_t_history_chat_logs_m_companies_id_visitors_id' => array('column' => array('m_companies_id', 'visitors_id', 't_histories_id', 'created'), 'unique' => 0),
			'idx_m_companies_id_t_histories_id_t_history_stay_logs_id' => array('column' => array('m_companies_id', 't_histories_id', 't_history_stay_logs_id', 'message_type', 'notice_flg', 'created', 'message_read_flg', 'achievement_flg'), 'unique' => 0),
			'idx_t_history_chat_logs_m_companies_id_t_histories_id_created' => array('column' => array('m_companies_id', 't_histories_id', 'created'), 'unique' => 0),
			'idx_t_history_chat_logs_m_companies_id_message_type_created' => array('column' => array('m_companies_id', 'message_type', 'created'), 'unique' => 0),
			't_history_chat_logs_mcid_mt_nf_c_thid_index' => array('column' => array('m_companies_id', 't_histories_id', 'message_type', 'notice_flg', 'created'), 'unique' => 0),
			't_history_chat_logs_mcid_thid_mt_c_index' => array('column' => array('m_companies_id', 't_histories_id', 'message_type', 'created'), 'unique' => 0),
			't_history_chat_logs_mcid_thid_mt_md_index' => array('column' => array('m_companies_id', 't_histories_id', 'message_type', 'message_distinction'), 'unique' => 0),
			'idx_m_companies_id_message_type_notice_flg' => array('column' => array('m_companies_id', 't_histories_id', 'message_type', 'notice_flg'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_link_count_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		't_histories_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		't_history_stay_logs_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'link_url' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'idx_m_companies_id' => array('column' => 'm_companies_id', 'unique' => 0),
			'idx_m_companies_id_t_histories_id' => array('column' => array('m_companies_id', 't_histories_id'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_link_counts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'year' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'unsigned' => false),
		'month' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'day' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'hour' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'link_count' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'idx_m_companies_id_year' => array('column' => array('m_companies_id', 'year'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_share_displays = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		't_histories_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '履歴ID'),
		'm_users_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '対応ユーザーID'),
		'start_time' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '同期開始日時'),
		'finish_time' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '同期終了日時'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			't_histories_id' => array('column' => 't_histories_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_stay_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		't_histories_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '履歴ID'),
		'title' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'ページタイトル', 'charset' => 'utf8'),
		'url' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'URL', 'charset' => 'utf8'),
		'stay_time' => array('type' => 'time', 'null' => false, 'default' => null, 'comment' => '滞在時間'),
		'del_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '削除フラグ'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '登録日'),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '登録実行ユーザ'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日'),
		'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '更新実行ユーザ'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			't_histories_id_idx' => array('column' => 't_histories_id', 'unique' => 0),
			'idx_t_history_stay_logs_title_url' => array('column' => array('title', 'url'), 'unique' => 0, 'length' => array('url' => '255')),
			'idx_t_history_stay_logs_title' => array('column' => 'title', 'unique' => 0),
			'idx_t_history_stay_logs_url' => array('column' => 'url', 'unique' => 0, 'length' => array('url' => '255'))
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_widget_close_counts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'year' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false),
		'month' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'day' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'hour' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'widget_close_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'company' => array('column' => array('m_companies_id', 'year', 'widget_close_count', 'month', 'day', 'hour'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_widget_counts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'year' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false),
		'month' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'day' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'hour' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'widget_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'company' => array('column' => array('m_companies_id', 'year', 'widget_count', 'month', 'day', 'hour'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_widget_displays = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'tab_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'idx_t_history_widget_displays_tab_id' => array('column' => 'tab_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_widget_minimize_counts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'year' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false),
		'month' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'day' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'hour' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'widget_minimize_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'company' => array('column' => array('m_companies_id', 'year', 'widget_minimize_count', 'month', 'day', 'hour'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_lead_list_settings = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'list_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'list_parameter' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'created_user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'company_index' => array('column' => 'm_companies_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB', 'comment' => 'リードリストの設定を管理するテーブル')
	);

	public $t_lead_lists = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		't_lead_list_settings_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		't_chatbot_scenarios_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'lead_informations' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'landing_page' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'lead_regist_page' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'user_agent' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'SEARCH_INDEX' => array('column' => array('m_companies_id', 'created', 't_lead_list_settings_id'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB', 'comment' => '登録されたリードリストを管理するテーブル')
	);

	public $t_logins = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'm_users_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'ip_address' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 15, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'user_agent' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'idx_t_logins_m_companies_id_created_users_id' => array('column' => array('m_companies_id', 'created', 'm_users_id'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_mail_transmission_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'mail_type_cd' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 5, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'from_address' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'from_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'to_address' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'to_name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'cc_address' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'cc_name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'bcc_address' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'bcc_name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'subject' => array('type' => 'string', 'null' => true, 'default' => 'no title', 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'send_flg' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'sent_datetime' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_receive_visitor_files = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'レコードID'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true),
		't_histories_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true),
		'visitors_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'uuid' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 64, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'saved_file_key' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'download_url' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'file_path' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'file_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'file_size' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true),
		'comment' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_reset_password_informations = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'm_users_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'mail_address' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'parameter' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 32, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'authentication_code' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 6, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'delete_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '0:無効、1:有効'),
		'expire' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB', 'comment' => 'PWDリマインダー設定の管理テーブル')
	);

	public $t_send_system_mail_schedules = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'sending_datetime' => array('type' => 'datetime', 'null' => false, 'default' => null, 'comment' => '日付、日時'),
		'subject' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'メールタイトル', 'charset' => 'utf8'),
		'mail_body' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'メール本文', 'charset' => 'utf8'),
		'mail_address' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'comment' => 'メールアドレス', 'charset' => 'utf8'),
		'send-mail_flg' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'メール送信フラグ'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_upload_transfer_files = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'saved_file_key' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'download_url' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'file_path' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'file_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'file_size' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'download_flg' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'downloaded' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

}
