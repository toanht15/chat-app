<?php
class CreateTableTChatbotScenarioSendFiles extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'create_table_t_chatbot_scenario_send_files';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				't_external_api_connections' => array(
					'del_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '削除フラグ', 'after' => 'response_body_maps'),
				),
			),
			'create_table' => array(
				't_chatbot_scenario_send_files' => array(
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
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
			'alter_field' => array(
			),
		),
		'down' => array(
			'drop_field' => array(
				't_external_api_connections' => array('del_flg'),
			),
			'drop_table' => array(
				't_chatbot_scenario_send_files'
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
