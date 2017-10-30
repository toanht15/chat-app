<?php
class AddColumnSortTCampaignsCakeMigration extends CakeMigration {

  /**
   * Migration description
   *
   * @var string
   */
  public $description = 'add_column_sort_t_campaigns';

  /**
   * Actions to be performed
   *
   * @var array $migration
   */
  public $migration = array(
      'up' => array(
          'create_field' => array(
              't_campaigns' => array(
                  'sort' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => 'ソート順', 'after' => 'comment'),
              )
          )
      ),
      'down' => array(
          'drop_field' => array(
              't_campaigns' => array('sort'),
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