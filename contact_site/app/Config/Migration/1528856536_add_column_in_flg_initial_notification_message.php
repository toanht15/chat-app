<?php
class AddColumnInFlgInitialNotificationMessage extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = '_add_column_in_flg_initial_notification_message';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'm_chat_settings' => array(
					'in_flg' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'after' => 'sc_flg'),
					'initial_notification_message' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'sorry_message'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'm_chat_settings' => array('in_flg', 'initial_notification_message')
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
