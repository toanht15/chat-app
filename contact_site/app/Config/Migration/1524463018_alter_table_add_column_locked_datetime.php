<?php
class AlterTableAddColumnLockedDatetime extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table_add_column_locked_datetime';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'm_users' => array(
					'locked_datetime' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ロックした日時', 'after' => 'error_count'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'm_users' => array('locked_datetime'),
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
