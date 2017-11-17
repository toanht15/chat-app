<?php
class AddMChatSettingsPluralSorryMessages extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_m_chat_settings_plural_sorry_messages';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'm_chat_settings' => array(
					'outside_hours_sorry_message' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'sc_default_num'),
					'wating_call_sorry_message' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'outside_hours_sorry_message'),
					'no_standby_sorry_message' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'wating_call_sorry_message'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'm_chat_settings' => array('outside_hours_sorry_message', 'wating_call_sorry_message', 'no_standby_sorry_message'),
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
