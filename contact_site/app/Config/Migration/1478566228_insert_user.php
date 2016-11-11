<?php
class InsertUser extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
  public $description = 'insert_user';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
  public $migration = array(
    'up' => array(
    ),
    'down' => array(
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
    $MAdministrator = ClassRegistry::init('MAdministrator');
    if ($direction === 'up') {
         /* ユーザー情報 */
        $data['MAdministrator']['user_name'] = 'テスト';
        $data['MAdministrator']['mail_address'] = 'defalt.com';
        $data['MAdministrator']['password'] = 'b5fc0ef78be07f3d428666baf0ee51fc353f0a15';
        $MAdministrator->create();

        if ($MAdministrator->save($data, false)) {
            $this->callback->out('MAdministrator table has been initialized');
        }
    }
    if ($direction === 'down') {
      /* ユーザー情報 */
      $conditions = array( "1 = 1" ); //dummyの条件
      $MAdministrator->deleteAll( $conditions, false); //MAdministratorの全削除
    }
    return true;
  }
}
