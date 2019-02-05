<?php
class AlterTableMAgreementAddColumnSectorType extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table_m_agreement_add_column_sector_type';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'm_agreements' => array(
					'sector_type' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'after' => 'business_model'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'm_agreements' => array('sector_type'),
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
