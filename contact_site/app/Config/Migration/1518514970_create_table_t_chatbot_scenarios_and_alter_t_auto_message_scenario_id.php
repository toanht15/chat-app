<?php
class CreateTableTChatbotScenariosAndAlterTAutoMessageScenarioId extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
        public $description = 'Preview of migration';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
        public $migration = array(
                'up' => array(
                        'create_field' => array(
                                't_auto_messages' => array(
                                        't_chatbot_scenario_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => 'チャットボットシナリオID', 'after' => 'm_mail_template_id'),
                                )
                        ),
                        'create_table' => array(
                                't_chatbot_scenarios' => array(
                                        'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
                                        'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '企業ID'),
                                        'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'comment' => 'シナリオ名称', 'charset' => 'utf8'),
                                        'activity' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'シナリオ設定内容', 'charset' => 'utf8'),
                                        'del_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => '削除フラグ'),
                                        'sort' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'comment' => 'ソート順'),
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
                                )
                        )
                ),
                'down' => array(
                        'drop_field' => array(
                                't_auto_messages' => array('t_chatbot_scenario_id')
                        ),
                        'drop_table' => array(
                                't_chatbot_scenarios'
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
