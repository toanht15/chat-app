<?php
class ChangeMLandscapeDataPrimary extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'change_m_landscape_data_primary';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
      'drop_field' => array(
        'm_landscape_data' => array('indexes' => array('PRIMARY')),
      ),
			'create_field' => array(
				'm_landscape_data' => array(
					'indexes' => array(
						'PRIMARY' => array('column' => 'ip_address', 'unique' => 1),
					),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'm_landscape_data' => array('indexes' => array('PRIMARY')),
      ),
			'create_field' => array(
				'm_landscape_data' => array(
					'indexes' => array(
						'PRIMARY' => array('column' => 'ip_address', 'unique' => 1),
					),
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
