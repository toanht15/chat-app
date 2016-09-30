<?php
class CreateTableCampaigns extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
  public $description = 'create_table_campaigns';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
  public $migration = array(
    'up' => array(
      'create_table' => array(
        't_campaigns' => array(
          'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
          'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '企業ID'),
          'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => 'キャンペーン名', 'charset' => 'utf8'),
          'parameter' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'utf8_general_ci', 'comment' => 'URLパラメータ', 'charset' => 'utf8'),
          'comment' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'comment' => 'コメント', 'charset' => 'utf8'),
          'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '登録日'),
          'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '登録実行ユーザ'),
          'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '更新日'),
          'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '更新実行ユーザ'),
          'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => '削除日'),
          'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '削除実行ユーザ'),
          'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
          ),
          'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
        ),
      ),
    ),
    'down' => array(
      'drop_table' => array(
        't_campaigns'
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
