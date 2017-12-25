<?php
class AddNoticeFlg extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_notice_flg';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				't_history_chat_logs' => array(
					'notice_flg' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'after' => 'message_request_flg'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				't_history_chat_logs' => array('notice_flg'),
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
