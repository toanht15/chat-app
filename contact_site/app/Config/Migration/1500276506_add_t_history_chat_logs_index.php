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
			'create_field' => array(
				't_history_chat_logs' => array(
					'indexes' => array(
						'idx_t_history_chat_logs_t_histories_id' => array('column' => 't_histories_id', 'unique' => 0),
						'idx_t_history_chat_logs_message_type' => array('column' => 'message_type', 'unique' => 0),
						'idx_t_history_chat_logs_message_request_flg' => array('column' => 'message_request_flg', 'unique' => 0),
						'idx_t_history_chat_logs_achievement_flg' => array('column' => 'achievement_flg', 'unique' => 0),
					),
				),
			),
			'drop_field' => array(
				't_history_chat_logs' => array('indexes' => array('t_histories_id_idx')),
			),
		),
		'down' => array(
			'drop_field' => array(
				't_history_chat_logs' => array('indexes' => array('idx_t_history_chat_logs_t_histories_id', 'idx_t_history_chat_logs_message_type', 'idx_t_history_chat_logs_message_request_flg', 'idx_t_history_chat_logs_achievement_flg')),
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
