<?php
class AddInquiryNumberToTChatbotScenarios extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_inquiry_number_to_t_chatbot_scenarios';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				't_chatbot_scenarios' => array(
					'inquiry_number' => array('type' => 'integer', 'null' => true, 'default' => '1', 'unsigned' => false, 'comment' => 'メール連番', 'after' => 'sort'),
				)
			),
		),
		'down' => array(
			'drop_field' => array(
				't_chatbot_scenarios' => array('inquiry_number'),
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
