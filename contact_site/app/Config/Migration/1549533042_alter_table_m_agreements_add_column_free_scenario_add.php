<?php
class AlterTableMAgreementsAddColumnFreeScenarioAdd extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table_m_agreements_add_column_free_scenario_add';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'm_agreements' => array(
					'free_scenario_add' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'after' => 'memo'),
        )
      ),
		),
		'down' => array(
			'drop_field' => array(
				'm_agreements' => array('free_scenario_add')
      )
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
