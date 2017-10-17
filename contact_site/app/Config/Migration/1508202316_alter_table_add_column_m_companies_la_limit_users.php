<?php
class AlterTableAddColumnMCompaniesLaLimitUsers extends CakeMigration {

  /**
   * Migration description
   *
   * @var string
   */
  public $description = 'alter_table_add_column_m_companies_la_limit_users';

  /**
   * Actions to be performed
   *
   * @var array $migration
   */
  public $migration = array(
    'up' => array(
      'create_field' => array(
        'm_companies' => array(
          'la_limit_users' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'after' => 'limit_users'),
        )
      )
    ),
    'down' => array(
      'drop_field' => array(
        'm_companies' => array('la_limit_users'),
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