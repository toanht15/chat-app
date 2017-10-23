<?php
class AlterTableModifyMCompaniesCoreSettings extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table_modify_m_companies_core_settings';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'm_companies' => array(
					'core_settings' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8')
				)
      )
		),
		'down' => array(
			'alter_field' => array(
				'm_companies' => array(
					'core_settings' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'comment' => '仕様機能内容', 'charset' => 'utf8')
				)
			)
		)
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