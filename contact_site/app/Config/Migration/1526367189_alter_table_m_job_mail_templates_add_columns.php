<?php
class AlterTableMJobMailTemplatesAddColumns extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table_m_job_mail_templates_add_columns';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'm_job_mail_templates' => array(
					'send_mail_application_user_flg' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'after' => 'send_mail_ml_flg'),
					'send_mail_administrator_user_flg' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'after' => 'send_mail_application_user_flg'),
					'send_mail_sinclo_all_users_flg' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'after' => 'send_mail_administrator_user_flg'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'm_job_mail_templates' => array('send_mail_application_user_flg', 'send_mail_administrator_user_flg', 'send_mail_sinclo_all_users_flg'),
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
