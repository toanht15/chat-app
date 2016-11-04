<?php
class DropFieldDelFlg extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'drop_field_del_flg';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'drop_field' => array(
				't_histories' => array('del_flg'),
				't_history_stay_logs' => array('deleted', 'deleted_user_id'),
			),
		),
		'down' => array(
			'create_field' => array(
				't_histories' => array(
					'del_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '削除フラグ'),
				),
				't_history_stay_logs' => array(
					'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '削除日'),
					'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '削除実行ユーザ'),
				),
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
