<?php
class ChangeTHistroryChatLogsMessageSize extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'change_t_histrory_chat_logs_message_size';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				't_history_chat_logs' => array(
					'message' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 21800, 'collate' => 'utf8_general_ci', 'comment' => 'メッセージ', 'charset' => 'utf8'),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				't_history_chat_logs' => array(
					'message' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 500, 'collate' => 'utf8_general_ci', 'comment' => 'メッセージ', 'charset' => 'utf8'),
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
