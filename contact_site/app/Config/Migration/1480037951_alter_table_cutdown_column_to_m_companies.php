<?php
class AlterTableCutdownColumnToMCompanies extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table_cutdown_column_to_m_companies';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'drop_field' => array(
				'm_companies' => array('admin_mail_address', 'admin_password'),
			),
		),
		'down' => array(
			'create_field' => array(
				'm_companies' => array(
					'admin_mail_address' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => '管理者アドレス', 'charset' => 'utf8'),
					'admin_password' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => '管理者パスワード', 'charset' => 'utf8'),
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
