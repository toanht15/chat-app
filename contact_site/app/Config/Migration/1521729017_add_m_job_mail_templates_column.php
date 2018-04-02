<?php
class AddMJobMailTemplatesColumn extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_m_job_mail_templates_column';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'm_job_mail_templates' => array(
					'value_type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'after' => 'mail_type_cd'),
					'value' => array('type' => 'integer', 'null' => false, 'default' => '9', 'unsigned' => false, 'after' => 'value_type'),
					'sender' => array('type' => 'string', 'null' => false, 'default' => '（sinclo）', 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8','after' => 'time'),
					'agreement_flg' => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false, 'after' => 'mail_body'),
					'send_mail_ml_flg' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'after' => 'agreement_flg')
				),
				'm_system_mail_templates' => array(
					'sender' => array('type' => 'string', 'null' => false, 'default' => '（sinclo）', 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8','after' => 'mail_type_cd'),
				),
			),
			'drop_field' => array(
					'm_job_mail_templates' => array('days_after')
			),
		),
		'down' => array(
			'drop_field' => array(
				'm_job_mail_templates' => array('value_type','value','sender','agreement_flg','send_mail_ml_flg'),
				'm_system_mail_templates' => array('sender')
			),
			'create_field' => array(
				'm_job_mail_templates' => array(
					'days_after' => array('type' => 'integer', 'null' => false, 'default' => '9', 'unsigned' => false, 'after' => 'value_type')
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
