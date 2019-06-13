<?php

class AlterTableMAgreementsAddColumnCvValue extends CakeMigration
{

  /**
   * Migration description
   *
   * @var string
   */
  public $description = 'alter_table_m_agreements_add_column_cv_value';

  /**
   * Actions to be performed
   *
   * @var array $migration
   */
  public $migration = array(
      'up' => array(
          'create_field' => array(
              'm_agreements' => array(
                  'cv_value' => array(
                      'type' => 'integer',
                      'null' => true,
                      'default' => '0',
                      'unsigned' => false,
                      'comment' => 'CV1件あたりの金額',
                      'after' => 'agreement_end_day'
                  ),
              ),
          )
      ),
      'down' => array(
          'drop_field' => array(
              'm_agreements' => array('cv_value'),
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
