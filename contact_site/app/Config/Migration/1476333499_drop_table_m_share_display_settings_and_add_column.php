<?php
class DropTableMShareDisplaySettingsAndAddColumn extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'drop_table_m_share_display_settings_and_add_column';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'm_companies' => array(
					'exclude_params' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '除外パラメータ', 'charset' => 'utf8', 'after' => 'limit_users'),
					'exclude_ips' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '除外IPアドレス', 'charset' => 'utf8', 'after' => 'exclude_params'),
				),
			),
			'drop_field' => array(
				'm_users' => array('indexes' => array('m_companies_id_2')),
			),
			'drop_table' => array(
				'm_share_display_settings'
			),
		),
		'down' => array(
			'drop_field' => array(
				'm_companies' => array('exclude_params', 'exclude_ips'),
			),
			'create_field' => array(
				'm_users' => array(
					'indexes' => array(
						'm_companies_id_2' => array('column' => 'm_companies_id', 'unique' => 0),
					),
				),
			),
			'create_table' => array(
				'm_share_display_settings' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
					'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '企業ID'),
					'exclude_params' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '除外パラメータ', 'charset' => 'utf8'),
					'exclude_ips' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '除外IPアドレス', 'charset' => 'utf8'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
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
