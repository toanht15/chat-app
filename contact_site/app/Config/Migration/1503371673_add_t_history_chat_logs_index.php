<?php
class AddTHistoryChatLogsIndex extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_t_history_chat_logs_index';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				't_history_chat_logs' => array(
					'created' => array('type' => 'datetime', 'null' => false, 'default' => '0000-00-00 00:00:00.00', 'length' => null, 'key' => 'primary'),
				),
			),
			'drop_field' => array(
				't_history_chat_logs' => array('indexes' => array('PRIMARY')),
			),
			'create_field' => array(
				't_history_chat_logs' => array(
					'indexes' => array(
						'idx_t_history_chat_logs_message_type_t_histories_id_m_users_id' => array('column' => array('message_type', 't_histories_id', 'm_users_id', 'message_distinction', 'created'), 'unique' => 0),
						'idx_t_history_chat_logs_achievement_flg_t_histories_id_users_id' => array('column' => array('achievement_flg', 't_histories_id', 'm_users_id'), 'unique' => 0),
						'idx_t_history_chat_logs_message_request_flg_t_histories_id' => array('column' => array('message_request_flg', 't_histories_id', 'message_distinction', 'created'), 'unique' => 0),
						'PRIMARY' => array('column' => array('id', 'created'), 'unique' => 1),
					),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				't_history_chat_logs' => array(
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'length' => 2),
				),
			),
			'create_field' => array(
				't_history_chat_logs' => array(
					'indexes' => array(
						'PRIMARY' => array(),
					),
				),
			),
			'drop_field' => array(
				't_history_chat_logs' => array('indexes' => array('idx_t_history_chat_logs_message_type_t_histories_id_m_users_id', 'idx_t_history_chat_logs_achievement_flg_t_histories_id_users_id', 'idx_t_history_chat_logs_message_request_flg_t_histories_id', 'PRIMARY')),
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
