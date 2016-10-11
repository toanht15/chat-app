<?php
class CreateTableShareDisplaySettings extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
  public $description = '_create_table_share_display_settings';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
  public $migration = array(
    'up' => array(
      'create_table' => array(
        'm_share_display_settings' => array(
          'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
          'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '企業ID'),
          'exclude_params' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '除外パラメータ', 'charset' => 'utf8'),
          'exclude_ips' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '除外IPアドレス', 'charset' => 'utf8'),
          'indexes' => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
          ),
          'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
        ),
      ),
    ),
    'down' => array(
      'drop_table' => array(
        'm_share_display_settings'
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
    $MShareDisplaySetting = ClassRegistry::init('MShareDisplaySetting');
    if ($direction === 'up') {
         /* ユーザー情報 */
        $data['MShareDisplaySetting']['id'] = 1;
        $data['MShareDisplaySetting']['m_companies_id'] = 1;
        $MShareDisplaySetting->create();

        if ($MShareDisplaySetting->save($data, false)) {
            $this->callback->out('MShareDisplaySetting table has been initialized');
        }
    }
    return true;
  }
}
