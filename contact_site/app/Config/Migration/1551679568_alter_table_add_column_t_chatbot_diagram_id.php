<?php
class AlterTableAddColumnTChatbotDiagramId extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table_add_column_t_chatbot_diagram_id';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
      'create_field' => array(
        't_auto_messages' => array(
          't_chatbot_diagram_id' => array('type' => 'integer', 'null' => true, 'default' => 0, 'length' => 11, 'comment' => 'チャットツリーID', 'after' => 'call_automessage_id'),
        ),
      ),
		),
		'down' => array(
      'drop_field' => array(
        't_auto_messages' => array('t_chatbot_diagram_id'),
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
