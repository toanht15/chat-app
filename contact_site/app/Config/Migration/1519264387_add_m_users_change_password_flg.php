<?php
class AddMUsersChangePasswordFlg extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_m_users_change_password_flg';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'm_users' => array(
					'change_password_flg' => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false, 'after' => 'password'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'm_users' => array('change_password_flg'),
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
