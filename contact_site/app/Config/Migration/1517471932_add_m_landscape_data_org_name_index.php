<?php
class AddMLandscapeDataOrgNameIndex extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_m_landscape_data_org_name_index';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'm_landscape_data' => array(
					'indexes' => array(
						'idx_ip_address_lbc_code_org_name' => array('column' => array('ip_address', 'lbc_code', 'org_name'), 'unique' => 0),
					),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'm_landscape_data' => array('indexes' => array('idx_ip_address_lbc_code_org_name')),
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
