<?php
class CreateTableTHistoryDiagramLogs extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'create_table_t_history_diagram_logs';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				't_history_diagram_logs' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
					't_history_chat_logs_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
					't_chatbot_diagrams_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'source_node_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 45, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'target_node_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 45, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'del_flg' => array('type' => 'boolean', 'null' => true, 'default' => null),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB', 'comment' => 'チャットツリー履歴の管理テーブル'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				't_history_diagram_logs'
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
