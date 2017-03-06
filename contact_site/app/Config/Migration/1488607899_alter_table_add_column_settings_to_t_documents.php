<?php
class AlterTableAddColumnSettingsToTDocuments extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table_add_column_settings_to_t_documents';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				't_documents' => array(
					'settings' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'document info', 'charset' => 'utf8', 'after' => 'password'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				't_documents' => array('settings'),
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
