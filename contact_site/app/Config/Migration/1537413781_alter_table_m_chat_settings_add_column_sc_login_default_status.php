<?php
class AlterTableMChatSettingsAddColumnScLoginDefaultStatus extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table_m_chat_settings_add_column_sc_login_default_status';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'm_chat_settings' => array(
					'sc_login_default_status' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => 'ログイン後初期ステータス', 'after' => 'initial_notification_message'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'm_chat_settings' => array('sc_login_default_status'),
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
