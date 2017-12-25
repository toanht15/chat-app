<?php
class AddChatHistoryScreenFlg extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_chat_history_screen_flg';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'm_users' => array(
					'chat_history_screen_flg' => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false, 'after' => 'session_rand_str'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'm_users' => array('chat_history_screen_flg'),
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
