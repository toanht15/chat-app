<?php
class AddColumnSortTAutoMessagesAndTDocuments extends CakeMigration {

  /**
   * Migration description
   *
   * @var string
   */
  public $description = 'add_column_sort_t_auto_messages_and_t_documents';

  /**
   * Actions to be performed
   *
   * @var array $migration
   */
  public $migration = array(
      'up' => array(
          'create_field' => array(
              't_auto_messages' => array(
                  'sort' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => 'ソート順', 'after' => 'del_flg'),
              ),
              't_documents' => array(
                  'sort' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => 'ソート順', 'after' => 'del_flg'),
              ),
          )
      ),
      'down' => array(
          'drop_field' => array(
              't_auto_messages' => array('sort'),
              't_documents' => array('sort')
          ),
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