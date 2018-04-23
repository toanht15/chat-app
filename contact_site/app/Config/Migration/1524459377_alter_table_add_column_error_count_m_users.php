<?php
class AlterTableAddColumnErrorCountMUsers extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table_add_column_error_count_m_users';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'm_users' => array(
					'error_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'after' => 'del_flg')
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'm_users' => array('error_count'),
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
