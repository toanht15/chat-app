<?php
class AddTHistoryChatActiveUsersIndex extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_t_history_chat_active_users_index';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				't_history_chat_active_users' => array(
					'indexes' => array(
						'idx_m_companies_id_users_id_chat_logs_id' => array('column' => array('m_companies_id', 'id', 'm_users_id', 't_history_chat_logs_id'), 'unique' => 0),
					),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				't_history_chat_active_users' => array('indexes' => array('idx_m_companies_id_users_id_chat_logs_id')),
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
