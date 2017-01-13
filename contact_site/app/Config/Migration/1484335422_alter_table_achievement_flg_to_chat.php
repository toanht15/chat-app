<?php
class AlterTableAchievementFlgToChat extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table_achievement_flg_to_chat';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				't_history_chat_logs' => array(
					'achievement_flg' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '成果フラグ(1:有効, 2:無効, null: 指定なし)', 'after' => 'message_read_flg'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				't_history_chat_logs' => array('achievement_flg'),
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
