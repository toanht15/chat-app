<?php
class AlterTableTAutoMessagesMMailTransmissionIdMMailTemplateId extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table_t_auto_messages_m_mail_transmission_id_m_mail_template_id';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				't_auto_messages' => array(
					'm_mail_transmission_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'after' => 'action_type'),
					'm_mail_template_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'after' => 'm_mail_transmission_id'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				't_auto_messages' => array('m_mail_transmission_id', 'm_mail_template_id'),
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
