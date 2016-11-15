<?php
class AlterTableAddColumnTrialFlgToMCompanies extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
  public $description = 'alter_table_add_column_trial_flg_to_m_companies';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
  public $migration = array(
    'up' => array(
      'create_field' => array(
        'm_companies' => array(
          'trial_flg' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '試用フラグ', 'after' => 'core_settings'),
        ),
      ),
    ),
    'down' => array(
      'drop_field' => array(
        'm_companies' => array('trial_flg'),
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
