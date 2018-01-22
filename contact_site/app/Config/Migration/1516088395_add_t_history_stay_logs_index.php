<?php
class AddTHistoryStayLogsIndex extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_t_history_stay_logs_index';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				't_history_stay_logs' => array(
					'title' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'ページタイトル', 'charset' => 'utf8'),
					'url' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'URL', 'charset' => 'utf8'),
				),
			),
			'create_field' => array(
				't_history_stay_logs' => array(
					'indexes' => array(
						'idx_t_history_stay_logs_title_url' => array('column' => array('title', 'url'), 'unique' => 0, 'length' => array('url' => '255')),
						'idx_t_history_stay_logs_title' => array('column' => 'title', 'unique' => 0),
						'idx_t_history_stay_logs_url' => array('column' => 'url', 'unique' => 0, 'length' => array('url' => '255')),
					),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				't_history_stay_logs' => array(
					'title' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => 'ページタイトル', 'charset' => 'utf8'),
					'url' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'comment' => 'URL', 'charset' => 'utf8'),
				),
			),
			'drop_field' => array(
				't_history_stay_logs' => array('indexes' => array('idx_t_history_stay_logs_title_url', 'idx_t_history_stay_logs_title', 'idx_t_history_stay_logs_url')),
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
