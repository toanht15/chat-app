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
			'alter_field' => array(
				't_history_chat_active_users' => array(
					't_history_chat_logs_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
				),
			),
			'create_field' => array(
				't_history_chat_active_users' => array(
					'indexes' => array(
						'idx_t_history_chat_logs_id_m_users_id' => array('column' => array('t_history_chat_logs_id', 'm_users_id'), 'unique' => 0),
					),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				't_history_chat_active_users' => array(
					't_history_chat_logs_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
				),
			),
			'drop_field' => array(
				't_history_chat_active_users' => array('indexes' => array('idx_t_history_chat_logs_id_m_users_id')),
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
