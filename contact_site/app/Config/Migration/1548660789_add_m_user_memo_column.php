<?php
class AddMUserMemoColumn extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_m_user_memo_column';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'm_users' => array(
					'memo' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'chat_history_screen_flg'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'm_users' => array('memo')
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
