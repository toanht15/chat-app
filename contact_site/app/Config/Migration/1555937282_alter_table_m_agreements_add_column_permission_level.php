<?php

class AlterTableMAgreementsAddColumnPermissionLevel extends CakeMigration
{

  /**
   * Migration description
   *
   * @var string
   */
  public $description = 'alter_table_m_agreements_add_column_permission_level';

  /**
   * Actions to be performed
   *
   * @var array $migration
   */
  public $migration = array(
      'up' => array(
          'create_field' => array(
              'm_administrators' => array(
                  'permission_level' => array(
                      'type' => 'integer',
                      'null' => true,
                      'default' => '1',
                      'unsigned' => false,
                      'after' => 'password'
                  ),
              ),
          ),
      ),
      'down' => array(
          'drop_field' => array(
              'm_administrators' => array('permission_level'),
          ),
      ),
  );

  /**
   * Before migration callback
   *
   * @param string $direction Direction of migration process (up or down)
   * @return bool Should process continue
   */
  public function before($direction)
  {
    return true;
  }

  /**
   * After migration callback
   *
   * @param string $direction Direction of migration process (up or down)
   * @return bool Should process continue
   */
  public function after($direction)
  {
    return true;
  }
}
