<?php
class AddTHistoryChatLogsVisitorsIdIndex extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_t_history_chat_logs_visitors_id_index';

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
						'idx_t_history_chat_logs_m_companies_id_visitors_id' => array('column' => array('m_companies_id', 'visitors_id', 't_histories_id', 'created'), 'unique' => 0),
					),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				't_history_chat_logs' => array('indexes' => array('idx_t_history_chat_logs_m_companies_id_visitors_id')),
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
