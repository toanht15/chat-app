<?php
class AlterTableAddColumnHideFlgOnTHistoryChatLogs extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table_add_column_hide_flg_on_t_history_chat_logs';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				't_history_chat_logs' => array(
					'hide_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'delete_flg'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				't_history_chat_logs' => array('hide_flg'),
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
