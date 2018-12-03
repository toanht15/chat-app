<?php
class UpdateTableEnterRoomLeaveRoomMessageType extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'update_table_enter_room_leave_room_message_type';

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
    $targetTable = 'THistoryChatLog';
    $THistoryChatLog = ClassRegistry::init($targetTable);
    if(strcmp($direction, 'up') === 0) {
      $THistoryChatLog->updateAll(array(
          'message_type' => 998
        ),
        array(
          'message_type' => 98
        )
      );
      $THistoryChatLog->updateAll(array(
          'message_type' => 999
        ),
        array(
          'message_type' => 99
        )
      );
      return true;
    } else if(strcmp($direction, 'down') === 0) {
      $THistoryChatLog->updateAll(array(
        'message_type' => 98
      ),
        array(
          'message_type' => 998
        )
      );
      $THistoryChatLog->updateAll(array(
        'message_type' => 99
      ),
        array(
          'message_type' => 999
        )
      );
      return true;
    }
  }
}
