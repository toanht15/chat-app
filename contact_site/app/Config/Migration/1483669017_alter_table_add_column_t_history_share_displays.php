<?php
class AlterTableAddColumnTHistoryShareDisplays extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table_add_column_t_history_share_displays';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				't_history_share_displays' => array(
					'start_time' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '同期開始日時', 'after' => 'm_users_id'),
					'finish_time' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '同期終了日時', 'after' => 'start_time'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				't_history_share_displays' => array('start_time', 'finish_time'),
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
