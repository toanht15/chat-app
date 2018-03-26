<?php
class AlterFieldMAgreementsAddColumnMemo extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_field_m_agreements_add_column_memo';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'm_agreements' => array(
					'memo' => array('type' => 'text', 'null' => true, 'default' => null, 'unsigned' => false, 'after' => 'note'),
				),
			)
		),
		'down' => array(
			'drop_field' => array(
				'm_agreements' => array('memo')
			)
		)
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
