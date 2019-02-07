<?php
class AlterTableMAgreementsAddColumnWebsite extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table_m_agreements_add_column_website';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'm_agreements' => array(
					'sector' => array('type' => 'string', 'null' => true, 'default' => '0', 'unsigned' => false, 'after' => 'business_model'),
					'website' => array('type' => 'string', 'null' => true, 'default' => '', 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8', 'after' => 'installation_url')
        )
      ),
		),
		'down' => array(
			'drop_field' => array(
				'm_agreements' => array('sector', 'website')
      )
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
