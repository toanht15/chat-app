<?php
class CreateTableTReceiveVisitorFiles extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'create_table_t_receive_visitor_files';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				't_receive_visitor_files' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'レコードID'),
					'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true),
					't_histories_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true),
					'visitors_id' => array('type' => 'string', 'null' => false, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'uuid' => array('type' => 'string', 'null' => false, 'length' => 64, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'saved_file_key' => array('type' => 'string', 'null' => false, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'download_url' => array('type' => 'string', 'null' => false, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'file_path' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'file_name' => array('type' => 'string', 'null' => false, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'file_size' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true),
					'comment' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
					'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				't_receive_visitor_files'
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
