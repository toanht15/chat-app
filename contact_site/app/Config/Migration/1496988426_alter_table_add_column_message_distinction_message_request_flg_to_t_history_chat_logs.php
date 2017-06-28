<?php
class AlterTableAddColumnMessageDistinctionMessageRequestFlgToTHistoryChatLogs extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table_add_column_message_distinction_message_request_flg_to_t_history_chat_logs';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				't_history_chat_logs' => array(
					'message_distinction' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'after' => 'message_type'),
					'message_request_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'after' => 'message_distinction'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				't_history_chat_logs' => array('message_distinction', 'message_request_flg'),
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
