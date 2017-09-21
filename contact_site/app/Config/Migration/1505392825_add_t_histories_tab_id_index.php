<?php
class AddTHistoriesTabIdIndex extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_t_histories_tab_id_index';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				't_histories' => array(
					'indexes' => array(
						'company_tab_id' => array('column' => array('m_companies_id', 'tab_id'), 'unique' => 0),
					),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				't_histories' => array('indexes' => array('company_tab_id')),
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
