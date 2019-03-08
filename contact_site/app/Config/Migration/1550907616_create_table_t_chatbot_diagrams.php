<?php

class CreateTableTChatbotDiagrams extends CakeMigration
{

  /**
   * Migration description
   *
   * @var string
   */
  public $description = 'create_table_t_chatbot_diagrams';

  /**
   * Actions to be performed
   *
   * @var array $migration
   */
  public $migration = array(
    'up' => array(
      'create_table' => array(
        't_chatbot_diagrams' => array(
          'id' => array(
            'type' => 'integer',
            'null' => false,
            'default' => null,
            'unsigned' => false,
            'key' => 'primary'
          ),
          'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
          'name' => array(
            'type' => 'string',
            'null' => true,
            'default' => null,
            'length' => 50,
            'collate' => 'utf8_general_ci',
            'charset' => 'utf8'
          ),
          'activity' => array(
            'type' => 'text',
            'null' => true,
            'default' => null,
            'collate' => 'utf8_general_ci',
            'charset' => 'utf8'
          ),
          'del_flg' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
          'sort' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false),
          'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
          'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
          'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
          'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false),
          'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null),
          'daleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
          'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
          ),
          'tableParameters' => array(
            'charset' => 'utf8',
            'collate' => 'utf8_general_ci',
            'engine' => 'InnoDB',
            'comment' => 'チャットツリー管理テーブル'
          ),
        ),
      ),
    ),
    'down' => array(
      'drop_table' => array(
        't_chatbot_diagrams'
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
