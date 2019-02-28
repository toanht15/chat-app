<?php
class AlterTableTAutoMessagesAddColumnCallAutoMessageId extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table_t_auto_messages_add_column_call_automessage_id';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				't_auto_messages' => array(
					'call_automessage_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'after' => 't_chatbot_scenario_id'),
        )
      ),
		),
		'down' => array(
			'drop_field' => array(
				'm_agreements' => array('free_scenario_add')
      )
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
