<?php
class CreateTableTHistoryChatLogTimesAndIndexes extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'create_table_t_history_chat_log_times_and_indexes';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				't_history_chat_log_times' => array(
					't_history_chat_logs_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'unsigned' => false),
					't_histories_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 11, 'unsigned' => false),
					'type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'unsigned' => false),
					'datetime' => array('type' => 'datetime', 'null' => false, 'default' => null, 'length' => 2),
//					'indexes' => array(
//						'PRIMARY' => array('column' => array('t_history_chat_logs_id', 't_histories_id', 'type'), 'unique' => 1),
//						'idx_t_history_chat_log_times_type_t_histories_id_datetime' => array('column' => array('type', 't_histories_id', 'datetime'), 'unique' => 0),
//					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB', 'comment' => 'チャット履歴時間管理テーブル'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				't_history_chat_log_times'
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
