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
        'm_companies', 'm_chat_notifications', 'm_customers', 'm_users', 'm_widget_settings', 't_auto_messages', 't_dictionaries', 't_histories', 't_history_chat_logs', 't_history_share_displays', 't_history_stay_logs'
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
    $today = date('Y-m-d H:i:s');
    $MCompany = ClassRegistry::init('MCompany');
    $MUser = ClassRegistry::init('MUser');
    $MWidgetSetting = ClassRegistry::init('MWidgetSetting');
    if ($direction === 'up') {
        /* 企業情報 */
        $data['MCompany']['id'] = 1;
        $data['MCompany']['company_key'] = 'medialink';
        $data['MCompany']['company_name'] = 'メディアリンク株式会社';
        $data['MCompany']['admin_mail_address'] = 'hoge@gmail.com';
        //パスワード:12345678
        $data['MCompany']['admin_password'] = '6f364de0b69b7279a296c5b7075335ea00452009';
        $data['MCompany']['m_contact_types_id'] = 1;
        $data['MCompany']['limit_users'] = 99;
        $data['MCompany']['core_settings'] = '{"chat": true, "synclo": true,"videochat": true}';
        $data['MCompany']['del_flg'] = 0;
        $data['MCompany']['created'] = $today;
        $data['MCompany']['created_user_id'] = '1';
        $data['MCompany']['modified'] = $today;
        $data['MCompany']['modified_user_id'] = '1';
        $MCompany->create();
        if ($MCompany->save($data)) {
            $this->callback->out('MCompany table has been initialized');
        }

        /* ユーザー情報 */
        $data['MUser']['m_companies_id'] = 1;
        $data['MUser']['user_name'] = 'ほげ';
        $data['MUser']['display_name'] = 'ほげ';
        $data['MUser']['mail_address'] = 'hoge@gmail.com';
        //パスワード:12345678
        $data['MUser']['password'] = '6f364de0b69b7279a296c5b7075335ea00452009';
        $data['MUser']['permission_level'] = 1;
        $data['MUser']['settings'] = '{"sendPattarn":"false"}';
        $data['MUser']['del_flg'] = 0;
        $data['MUser']['created'] = $today;
        $data['MUser']['created_user_id'] = '1';
        $data['MUser']['modified'] = $today;
        $data['MUser']['modified_user_id'] = '1';
        $MUser->create();
        if ($MUser->save($data)) {
            $this->callback->out('MUser table has been initialized');
        }

        /* ウィジェット情報 */
        $data['MWidgetSetting']['m_companies_id'] = 1;
        $data['MWidgetSetting']['display_type'] = 1;
        $data['MWidgetSetting']['style_settings'] = '{"showTime":"4","maxShowTime":"2","showPosition":"2","title":"\\u3069\\u3061\\u3089\\u3082","showSubtitle":"1","subTitle":"\\u30e1\\u30c7\\u30a3\\u30a2\\u30ea\\u30f3\\u30af\\u682a\\u5f0f\\u4f1a\\u793e","showDescription":"2","mainColor":"#70B8A0","stringColor":"#FFFFFF","mainImage":"\\/\\/socket.localhost:8080\\/img\\/widget\\/op01.jpg","showMainImage":"1","radiusRatio":"10","tel":"030-3455-7700","displayTimeFlg":"2","content":"\\u3054\\u8a2a\\u554f\\u6709\\u96e3\\u3046\\u3054\\u3056\\u3044\\u307e\\u3059\\u3002\\r\\n\\r\\n\\u96fb\\u8a71\\u3067\\u306e\\u30b5\\u30dd\\u30fc\\u30c8\\u3082\\u53d7\\u3051\\u4ed8\\u3051\\u3066\\u304a\\u308a\\u307e\\u3059\\u3002","chatTrigger":"2","showName":"1"}';
        $data['MWidgetSetting']['del_flg'] = 0;
        $data['MWidgetSetting']['created'] = $today;
        $data['MWidgetSetting']['created_user_id'] = '1';
        $data['MWidgetSetting']['modified'] = $today;
        $data['MWidgetSetting']['modified_user_id'] = '1';
        $MWidgetSetting->create();
        if ($MWidgetSetting->save($data)) {
            $this->callback->out('MWidgetSetting table has been initialized');
        }
    }
    return true;
  }
}
