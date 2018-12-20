<?php
App::uses('AppModel', 'Model');
/**
 * THistoryChatLogTime Model
 * チャットログ時間管理テーブル
 *
 */
class THistoryChatLogTime extends AppModel {

  const MIN_CHAT_LOG_TIME = 'minChatLogTime';
  const FIRST_CONSUMER_CHAT_LOG_TIME = 'firstConsumerChatLogTime';
  const FIRST_NOTICE_CHAT_LOG_TIME = 'firstNoticeChatLogTime';

  public $type = array(
    self::MIN_CHAT_LOG_TIME => 1,
    self::FIRST_CONSUMER_CHAT_LOG_TIME => 2,
    self::FIRST_NOTICE_CHAT_LOG_TIME => 3
  );

	public $name = 'THistoryChatLogTime';
	public $primaryKey = 't_history_chat_logs_id';

}
