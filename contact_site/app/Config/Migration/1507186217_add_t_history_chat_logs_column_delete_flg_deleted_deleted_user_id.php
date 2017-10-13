<?php
class AddTHistoryChatLogsColumnDeleteFlgDeletedDeletedUserId extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_t_history_chat_logs_column_delete_flg_deleted_deleted_user_id';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				't_history_chat_logs' => array(
					'delete_flg' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'after' => 'achievement_flg'),
					'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'after' => 'created'),
					'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'after' => 'deleted'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				't_history_chat_logs' => array('delete_flg', 'deleted', 'deleted_user_id')
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
