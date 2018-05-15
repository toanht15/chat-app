<?php
class AlterTableMAgreementAddColumnApplicationPosition extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table_m_agreement_add_column_application_position';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'm_agreements' => array(
					'application_position' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'comment' => '申し込み情報役職名', 'charset' => 'utf8', 'after' => 'application_department'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'm_agreements' => array('application_position'),
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
