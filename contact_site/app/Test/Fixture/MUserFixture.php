<?php
/**
 * MUser Fixture
 */
class MUserFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '?????ID'),
		'user_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => '?????', 'charset' => 'utf8'),
		'display_name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'mail_address' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'comment' => '???????', 'charset' => 'utf8'),
		'password' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => '?????', 'charset' => 'utf8'),
		'permission_level' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '?????'),
		'operation_list_columns' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => '??????????????????', 'charset' => 'utf8'),
		'history_list_columns' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => '???????????', 'charset' => 'utf8'),
		'del_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '?????'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '???'),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '???????'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '???'),
		'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '???????'),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '???'),
		'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '???????'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'm_companies_id' => array('column' => 'm_companies_id', 'unique' => 0),
			'm_companies_id_2' => array('column' => 'm_companies_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'm_companies_id' => 1,
			'user_name' => 'Lorem ipsum dolor sit amet',
			'display_name' => 'Lorem ipsum dolor sit amet',
			'mail_address' => 'Lorem ipsum dolor sit amet',
			'password' => 'Lorem ipsum dolor sit amet',
			'permission_level' => 1,
			'operation_list_columns' => 'Lorem ipsum dolor sit amet',
			'history_list_columns' => 'Lorem ipsum dolor sit amet',
			'del_flg' => 1,
			'created' => '2015-10-26 05:58:45',
			'created_user_id' => 1,
			'modified' => '2015-10-26 05:58:45',
			'modified_user_id' => 1,
			'deleted' => '2015-10-26 05:58:45',
			'deleted_user_id' => 1
		),
	);

}
