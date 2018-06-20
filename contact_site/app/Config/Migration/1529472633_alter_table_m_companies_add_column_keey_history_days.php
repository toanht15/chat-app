<?php
class AlterTableMCompaniesAddColumnKeeyHistoryDays extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table_m_companies_add_column_keey_history_days';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'm_companies' => array(
					'keep_history_days' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 5, 'unsigned' => false, 'comment' => '履歴保持期間（0は無制限）', 'after' => 'core_settings'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'm_companies' => array('keep_history_days'),
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
