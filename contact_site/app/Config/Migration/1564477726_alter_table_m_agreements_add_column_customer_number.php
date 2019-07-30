<?php
class AlterTableMAgreementsAddColumnCustomerNumber extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table_m_agreements_add_column_customer_number';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'm_agreements' => array(
					'customer_number' => array('type' => 'string', 'null' => true, 'default' => '', 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'company_name'),
				),
			)
		),
		'down' => array(
			'drop_field' => array(
				'm_agreements' => array('customer_number'),
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
