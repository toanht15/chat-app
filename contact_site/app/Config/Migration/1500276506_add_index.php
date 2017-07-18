<?php
class AddIndex extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_index';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				't_conversation_count' => array(
					'visitors_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'conversation_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'indexes' => array(
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				't_history_chat_active_users' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					't_history_chat_logs_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
					'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
					'm_users_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				't_history_widget_displays' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
					'tab_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'key' => 'index'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'idx_t_history_widget_displays_created' => array('column' => 'created', 'unique' => 0),
						'idx_t_history_widget_displays_m_companies_id' => array('column' => 'm_companies_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				't_logins' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
					'm_users_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
					'ip_address' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 15, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'user_agent' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
			'create_field' => array(
				't_history_chat_logs' => array(
					'message_distinction' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'after' => 'message_type'),
					'message_request_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'key' => 'index', 'after' => 'message_distinction'),
					'indexes' => array(
						'idx_t_history_chat_logs_t_histories_id' => array('column' => 't_histories_id', 'unique' => 0),
						'idx_t_history_chat_logs_message_type' => array('column' => 'message_type', 'unique' => 0),
						'idx_t_history_chat_logs_message_request_flg' => array('column' => 'message_request_flg', 'unique' => 0),
						'idx_t_history_chat_logs_achievement_flg' => array('column' => 'achievement_flg', 'unique' => 0),
					),
				),
			),
			'alter_field' => array(
				't_history_chat_logs' => array(
					'message_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => 'メッセージ種別（1:訪問者から、2:企業側から）'),
					'achievement_flg' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '成果フラグ(1:有効, 2:無効, null: 指定なし)'),
				),
			),
			'drop_field' => array(
				't_history_chat_logs' => array('indexes' => array('t_histories_id_idx')),
			),
		),
		'down' => array(
			'drop_table' => array(
				't_conversation_count', 't_history_chat_active_users', 't_history_widget_displays', 't_logins'
			),
			'drop_field' => array(
				't_history_chat_logs' => array('message_distinction', 'message_request_flg', 'indexes' => array('idx_t_history_chat_logs_t_histories_id', 'idx_t_history_chat_logs_message_type', 'idx_t_history_chat_logs_message_request_flg', 'idx_t_history_chat_logs_achievement_flg')),
			),
			'alter_field' => array(
				't_history_chat_logs' => array(
					'message_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => 'メッセージ種別（1:訪問者から、2:企業側から）'),
					'achievement_flg' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '成果フラグ(1:有効, 2:無効, null: 指定なし)'),
				),
			),
			'create_field' => array(
				't_history_chat_logs' => array(
					'indexes' => array(
						't_histories_id_idx' => array('column' => 't_histories_id', 'unique' => 0),
					),
				),
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
