<?php
/**
 * MCompany Fixture
 */
class MCompanyFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
		'company_key' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => '????', 'charset' => 'utf8'),
		'company_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'comment' => '???', 'charset' => 'utf8'),
		'admin_mail_address' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => '???????', 'charset' => 'utf8'),
		'admin_password' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => '????????', 'charset' => 'utf8'),
		'm_contact_types_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '?????'),
		'del_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '?????'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '???'),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '???????'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '???'),
		'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '???????'),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '???'),
		'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '???????'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
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
			'company_key' => 'Lorem ipsum dolor sit amet',
			'company_name' => 'Lorem ipsum dolor sit amet',
			'admin_mail_address' => 'Lorem ipsum dolor sit amet',
			'admin_password' => 'Lorem ipsum dolor sit amet',
			'm_contact_types_id' => 1,
			'del_flg' => 1,
			'created' => '2015-10-26 05:50:09',
			'created_user_id' => 1,
			'modified' => '2015-10-26 05:50:09',
			'modified_user_id' => 1,
			'deleted' => '2015-10-26 05:50:09',
			'deleted_user_id' => 1
		),
	);

}
