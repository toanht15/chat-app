<?php
class CreateTableTCustomVariablesAndTCustomerInformationSettings extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'create_table_t_custom_variables_and_t_customer_information_settings';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				't_custom_variables' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
					'm_companies_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '企業ID'),
					'variable_name' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'comment' => '変数名', 'charset' => 'utf8'),
					'type' => array('type' => 'integer', 'null' => false, 'default' => '3', 'length' => 2, 'unsigned' => false, 'comment' => 'タイプ'),
					'attribute_value' => array('type' => 'string', 'null' => false, 'length' => 500, 'collate' => 'utf8_general_ci', 'comment' => '属性値', 'charset' => 'utf8'),
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
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				't_customer_information_settings' => array(
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
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				't_custom_variables', 't_customer_information_settings'
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
