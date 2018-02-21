<?php
class AddCreateMJobMailTemplatesAndMSystemMailTemplatesAndTSendSystemMailSchedules extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = '_add_create_m_job_mail_templates_and_m_system_mail_templates_and_t_send_system_mail_schedules';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'm_agreements' => array(
					'business_model' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'after' => 'm_companies_id'),
				),
			),
			'create_table' => array(
				'm_job_mail_templates' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
					'mail_type_cd' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '名称', 'charset' => 'utf8'),
					'days_after' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '日にち'),
					'time' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '時間'),
					'subject' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'メールタイトル', 'charset' => 'utf8'),
					'mail_body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'メール本文', 'charset' => 'utf8'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				'm_system_mail_templates' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
					'mail_type_cd' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'タイプ', 'charset' => 'utf8'),
					'subject' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '件名', 'charset' => 'utf8'),
					'mail_body' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '本文', 'charset' => 'utf8'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
				't_send_system_mail_schedules' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
					'sending_datetime' => array('type' => 'datetime', 'null' => false, 'default' => null, 'comment' => '日付、日時'),
					'subject' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'メールタイトル', 'charset' => 'utf8'),
					'mail_body' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'メール本文', 'charset' => 'utf8'),
					'mail_address' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'comment' => 'メールアドレス', 'charset' => 'utf8'),
					'send-mail_flg' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'メール送信フラグ'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'm_agreements' => array('business_model', 'trial_start_day', 'trial_end_day')
			),
			'drop_table' => array(
				'm_job_mail_templates', 'm_system_mail_templates', 't_send_system_mail_schedules'
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
