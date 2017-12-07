<?php
class AlterTableTHistoryChatLogsSendMailFlgTMailTransmissionLogId extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table_t_history_chat_logs_send_mail_flg_t_mail_transmission_log_id';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				't_history_chat_logs' => array(
					'send_mail_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'achievement_flg'),
					't_mail_transmission_log_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'after' => 'send_mail_flg'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				't_history_chat_logs' => array('send_mail_flg', 't_mail_transmission_log_id'),
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
