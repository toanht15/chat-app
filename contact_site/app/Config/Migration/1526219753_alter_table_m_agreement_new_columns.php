<?php
class AlterTableMAgreementNewColumns extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table_m_agreement_new_columns';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'm_agreements' => array(
					'company_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '企業マスタID', 'charset' => 'utf8', 'after' => 'm_companies_id'),
					'application_mail_address' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'comment' => '申込み時メールアドレス', 'charset' => 'utf8', 'after' => 'application_name'),
					'administrator_mail_address' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'comment' => '管理者情報メールアドレス', 'charset' => 'utf8', 'after' => 'administrator_name'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'm_agreements' => array('company_name', 'application_mail_address', 'administrator_mail_address'),
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
