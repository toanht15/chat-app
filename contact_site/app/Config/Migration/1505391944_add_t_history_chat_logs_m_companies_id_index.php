<?php
class AddTHistoryChatLogsMCompaniesIdIndex extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_t_history_chat_logs_m_companies_id_index';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'drop_field' => array(
				't_history_chat_logs' => array('indexes' => array('idx_t_history_chat_logs_message_type_t_histories_id_m_users_id', 'idx_t_history_chat_logs_achievement_flg_t_histories_id_users_id', 'idx_t_history_chat_logs_message_request_flg_t_histories_id')),
			),
			'create_field' => array(
				't_history_chat_logs' => array(
					'indexes' => array(
						'idx_t_history_chat_logs_request_flg_companies_id_users_id' => array('column' => array('message_request_flg', 'm_companies_id', 'm_users_id', 't_histories_id', 'message_distinction', 'created'), 'unique' => 0),
						'idx_t_history_chat_logs_message_type_companies_id_users_id' => array('column' => array('message_type', 'm_companies_id', 'm_users_id', 't_histories_id', 'message_distinction', 'created'), 'unique' => 0),
						'idx_t_history_chat_logs_achievement_flg_companies_id' => array('column' => array('achievement_flg', 'm_companies_id', 't_histories_id'), 'unique' => 0),
						'idx_t_history_chat_logs_request_flg_companies_id' => array('column' => array('message_request_flg', 'm_companies_id', 't_histories_id', 'message_distinction', 'created'), 'unique' => 0),
						'idx_t_history_chat_logs_message_type_companies_id' => array('column' => array('message_type', 'm_companies_id', 't_histories_id', 'message_distinction', 'created'), 'unique' => 0),
						'idx_t_history_chat_logs_achievement_flg_companies_id_users_id' => array('column' => array('achievement_flg', 'm_companies_id', 'm_users_id', 't_histories_id'), 'unique' => 0),
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
				),
			),
		),
		'down' => array(
			'create_field' => array(
				't_history_chat_logs' => array(
					'indexes' => array(
						'idx_t_history_chat_logs_message_type_t_histories_id_m_users_id' => array('column' => array('message_type', 't_histories_id', 'm_users_id', 'message_distinction', 'created'), 'unique' => 0),
						'idx_t_history_chat_logs_achievement_flg_t_histories_id_users_id' => array('column' => array('achievement_flg', 't_histories_id', 'm_users_id'), 'unique' => 0),
						'idx_t_history_chat_logs_message_request_flg_t_histories_id' => array('column' => array('message_request_flg', 't_histories_id', 'message_distinction', 'created'), 'unique' => 0),
					),
				),
			),
			'drop_field' => array(
				't_history_chat_logs' => array('indexes' => array('idx_t_history_chat_logs_request_flg_companies_id_users_id', 'idx_t_history_chat_logs_message_type_companies_id_users_id', 'idx_t_history_chat_logs_achievement_flg_companies_id', 'idx_t_history_chat_logs_request_flg_companies_id', 'idx_t_history_chat_logs_message_type_companies_id', 'idx_t_history_chat_logs_achievement_flg_companies_id_users_id')),
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
