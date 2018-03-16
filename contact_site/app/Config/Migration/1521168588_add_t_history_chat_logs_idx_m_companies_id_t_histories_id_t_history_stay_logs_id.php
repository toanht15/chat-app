<?php
class AddTHistoryChatLogsIdxMCompaniesIdTHistoriesIdTHistoryStayLogsId extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_t_history_chat_logs_idx_m_companies_id_t_histories_id_t_history_stay_logs_id';

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
						'idx_m_companies_id_t_histories_id_t_history_stay_logs_id' => array('column' => array('m_companies_id', 't_histories_id', 't_history_stay_logs_id', 'message_type', 'notice_flg', 'created', 'message_read_flg', 'achievement_flg'), 'unique' => 0),
					),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				't_history_chat_logs' => array('indexes' => array('idx_m_companies_id_t_histories_id_t_history_stay_logs_id')),
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
