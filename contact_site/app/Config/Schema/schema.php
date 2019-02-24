<?php 
class AppSchema extends CakeSchema {

	public function before($event = array()) {
		return true;
	}

	public function after($event = array()) {
	}

	public $m_administrators = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    'user_name' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'length' => 100,
      'collate' => 'utf8_general_ci',
      'comment' => 'ãƒ¦ãƒ¼ã‚¶ãƒ¼å',
      'charset' => 'utf8'
    ),
    'mail_address' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'length' => 200,
      'collate' => 'utf8_general_ci',
      'comment' => 'ãƒ¡ãƒ¼ãƒ«ã‚¢ãƒ‰ãƒ¬ã‚¹',
      'charset' => 'utf8'
    ),
    'password' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'length' => 100,
      'collate' => 'utf8_general_ci',
      'comment' => 'ãƒ‘ã‚¹ãƒ¯ãƒ¼ãƒ‰',
      'charset' => 'utf8'
    ),
    'del_flg' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'å‰Šé™¤ãƒ•ãƒ©ã‚°'
    ),
    'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ç™»éŒ²æ—¥'),
    'created_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ç™»éŒ²å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'æ›´æ–°æ—¥'),
    'modified_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'æ›´æ–°å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'å‰Šé™¤æ—¥'),
    'deleted_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'å‰Šé™¤å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_agreements = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    'm_companies_id' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ä¼æ¥­ãƒžã‚¹ã‚¿ID'
    ),
    'company_name' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'ä¼æ¥­ãƒžã‚¹ã‚¿ID',
      'charset' => 'utf8'
    ),
		'business_model' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
    'sector' => array(
      'type' => 'string',
      'null' => true,
      'default' => '0',
      'collate' => 'utf8_general_ci',
      'charset' => 'utf8'
    ),
    'application_day' => array('type' => 'date', 'null' => false, 'default' => null, 'comment' => 'ç”³è¾¼æ—¥'),
    'trial_start_day' => array(
      'type' => 'date',
      'null' => true,
      'default' => null,
      'comment' => 'ãƒˆãƒ©ã‚¤ã‚¢ãƒ«é–‹å§‹æ—¥'
    ),
    'trial_end_day' => array(
      'type' => 'date',
      'null' => true,
      'default' => null,
      'comment' => 'ãƒˆãƒ©ã‚¤ã‚¢ãƒ«çµ‚äº†æ—¥'
    ),
    'agreement_start_day' => array('type' => 'date', 'null' => true, 'default' => null, 'comment' => 'å¥‘ç´„é–‹å§‹æ—¥'),
    'agreement_end_day' => array('type' => 'date', 'null' => true, 'default' => null, 'comment' => 'å¥‘ç´„çµ‚äº†æ—¥'),
    'application_department' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 20,
      'collate' => 'utf8_general_ci',
      'comment' => 'ç”³ã—è¾¼ã¿æƒ…å ±éƒ¨ç½²å',
      'charset' => 'utf8'
    ),
    'application_position' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'length' => 20,
      'collate' => 'utf8_general_ci',
      'comment' => 'ç”³ã—è¾¼ã¿æƒ…å ±å½¹è·å',
      'charset' => 'utf8'
    ),
    'application_name' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 20,
      'collate' => 'utf8_general_ci',
      'comment' => 'ç”³ã—è¾¼ã¿æƒ…å ±åå‰',
      'charset' => 'utf8'
    ),
    'application_mail_address' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'ç”³è¾¼ã¿æ™‚ãƒ¡ãƒ¼ãƒ«ã‚¢ãƒ‰ãƒ¬ã‚¹',
      'charset' => 'utf8'
    ),
    'administrator_department' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 20,
      'collate' => 'utf8_general_ci',
      'comment' => 'ç®¡ç†è€…æƒ…å ±éƒ¨ç½²å',
      'charset' => 'utf8'
    ),
    'administrator_position' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 20,
      'collate' => 'utf8_general_ci',
      'comment' => 'ç®¡ç†è€…æƒ…å ±å½¹è·å',
      'charset' => 'utf8'
    ),
    'administrator_name' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 20,
      'collate' => 'utf8_general_ci',
      'comment' => 'ç®¡ç†è€…æƒ…å ±åå‰',
      'charset' => 'utf8'
    ),
    'administrator_mail_address' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'ç®¡ç†è€…æƒ…å ±ãƒ¡ãƒ¼ãƒ«ã‚¢ãƒ‰ãƒ¬ã‚¹',
      'charset' => 'utf8'
    ),
    'installation_site_name' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 100,
      'collate' => 'utf8_general_ci',
      'comment' => 'è¨­ç½®ã‚µã‚¤ãƒˆå',
      'charset' => 'utf8'
    ),
    'installation_url' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 200,
      'collate' => 'utf8_general_ci',
      'comment' => 'è¨­ç½®ã‚µã‚¤ãƒˆURL',
      'charset' => 'utf8'
    ),
    'website' => array(
      'type' => 'string',
      'null' => true,
      'length' => 200,
      'collate' => 'utf8_general_ci',
      'charset' => 'utf8'
    ),
    'admin_password' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'length' => 100,
      'collate' => 'utf8_general_ci',
      'comment' => 'ã‚¹ãƒ¼ãƒ‘ãƒ¼ç®¡ç†è€…ç”¨ãƒ‘ã‚¹ãƒ¯ãƒ¼ãƒ‰',
      'charset' => 'utf8'
    ),
    'telephone_number' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 20,
      'collate' => 'utf8_general_ci',
      'comment' => 'é›»è©±ç•ªå·',
      'charset' => 'utf8'
    ),
    'note' => array(
      'type' => 'text',
      'null' => true,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'å‚™è€ƒ',
      'charset' => 'utf8'
    ),
		'memo' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
    'free_scenario_add' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false),
    'del_flg' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'å‰Šé™¤ãƒ•ãƒ©ã‚°'
    ),
    'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ç™»éŒ²æ—¥'),
    'created_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ç™»éŒ²å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'æ›´æ–°æ—¥'),
    'modified_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'æ›´æ–°å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'å‰Šé™¤æ—¥'),
    'deleted_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'å‰Šé™¤å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_chat_notifications = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    'm_companies_id' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'key' => 'index',
      'comment' => 'ä¼æ¥­ID'
    ),
    'name' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'length' => 100,
      'collate' => 'utf8_general_ci',
      'comment' => 'é€šçŸ¥å',
      'charset' => 'utf8'
    ),
    'type' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'comment' => 'å¯¾è±¡'
    ),
    'keyword' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 100,
      'collate' => 'utf8_general_ci',
      'comment' => 'ï½·ï½°ï¾œï½°ï¾„ï¾ž',
      'charset' => 'utf8'
    ),
    'image' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'length' => 100,
      'collate' => 'utf8_general_ci',
      'comment' => 'ã‚¢ã‚¤ã‚³ãƒ³ç”»åƒ',
      'charset' => 'utf8'
    ),
    'del_flg' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'å‰Šé™¤ãƒ•ãƒ©ã‚°'
    ),
    'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ç™»éŒ²æ—¥'),
    'created_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ç™»éŒ²å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'æ›´æ–°æ—¥'),
    'modified_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'æ›´æ–°å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'å‰Šé™¤æ—¥'),
    'deleted_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'å‰Šé™¤å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'm_companies_id' => array('column' => 'm_companies_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_chat_settings = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    'm_companies_id' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ä¼æ¥­ID'
    ),
    'sc_flg' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '2',
      'unsigned' => false,
      'comment' => 'sc(Simultaneous correspondence).ã€€ãƒãƒ£ãƒƒãƒˆã®åŒæ™‚å¯¾å¿œæ•°ã®è¨­å®š. 1:æœ‰åŠ¹, 2:ç„¡åŠ¹'
    ),
		'in_flg' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
    'sc_default_num' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ãƒãƒ£ãƒƒãƒˆã®åŸºæœ¬åŒæ™‚å¯¾å¿œæ•°'
    ),
		'outside_hours_sorry_message' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'wating_call_sorry_message' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'no_standby_sorry_message' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
    'sorry_message' => array(
      'type' => 'text',
      'null' => true,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'sorryãƒ¡ãƒƒã‚»ãƒ¼ã‚¸',
      'charset' => 'utf8'
    ),
		'initial_notification_message' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
    'sc_login_default_status' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'ãƒ­ã‚°ã‚¤ãƒ³å¾ŒåˆæœŸã‚¹ãƒ†ãƒ¼ã‚¿ã‚¹'
    ),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_companies = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    'company_key' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 100,
      'collate' => 'utf8_general_ci',
      'comment' => 'ä¼æ¥­ã‚­ãƒ¼',
      'charset' => 'utf8'
    ),
    'company_name' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'length' => 200,
      'collate' => 'utf8_general_ci',
      'comment' => 'ä¼æ¥­å',
      'charset' => 'utf8'
    ),
    'm_contact_types_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'å¥‘ç´„ã‚¿ã‚¤ãƒ—'
    ),
    'limit_users' => array(
      'type' => 'integer',
      'null' => false,
      'default' => '1',
      'unsigned' => false,
      'comment' => 'å¥‘ç´„IDæ•°'
    ),
		'la_limit_users' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false),
		'core_settings' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
    'keep_history_days' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '0',
      'length' => 5,
      'unsigned' => false,
      'comment' => 'å±¥æ­´ä¿æŒæœŸé–“ï¼ˆ0ã¯ç„¡åˆ¶é™ï¼‰'
    ),
    'trial_flg' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'è©¦ç”¨ãƒ•ãƒ©ã‚°'
    ),
    'del_flg' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'å‰Šé™¤ãƒ•ãƒ©ã‚°'
    ),
    'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ç™»éŒ²æ—¥'),
    'created_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ç™»éŒ²å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'æ›´æ–°æ—¥'),
    'modified_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'æ›´æ–°å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'å‰Šé™¤æ—¥'),
    'deleted_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'å‰Šé™¤å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'exclude_ips' => array(
      'type' => 'text',
      'null' => true,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'charset' => 'utf8'
    ),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_customers = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    'm_companies_id' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'key' => 'index',
      'comment' => 'ä¼æ¥­ID'
    ),
    'visitors_id' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 20,
      'collate' => 'utf8_general_ci',
      'comment' => 'ãƒ¦ãƒ¼ã‚¶ãƒ¼ID',
      'charset' => 'utf8'
    ),
    'informations' => array(
      'type' => 'text',
      'null' => false,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'æƒ…å ±',
      'charset' => 'utf8'
    ),
    'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ç™»éŒ²æ—¥'),
    'created_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ç™»éŒ²å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'æ›´æ–°æ—¥'),
    'modified_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'æ›´æ–°å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'å‰Šé™¤æ—¥'),
    'deleted_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'å‰Šé™¤å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'm_companies_id_idx' => array('column' => array('m_companies_id', 'visitors_id'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_document_tags = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    'm_companies_id' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ä¼æ¥­ãƒžã‚¹ã‚¿ID'
    ),
    'name' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'length' => 20,
      'collate' => 'utf8_general_ci',
      'comment' => 'ã‚¿ã‚°',
      'charset' => 'utf8'
    ),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_file_transfer_settings = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'type' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 2, 'unsigned' => false),
		'allow_extensions' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'updated' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'updated_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_ip_filter_settings = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
    'active_flg' => array(
      'type' => 'boolean',
      'null' => false,
      'default' => '0',
      'comment' => '0ï¼šç„¡åŠ¹ã€ï¼‘ï¼šæœ‰åŠ¹'
    ),
    'filter_type' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'length' => 2,
      'unsigned' => false,
      'comment' => 'ï¼‘ï¼šãƒ›ãƒ¯ã‚¤ãƒˆãƒªã‚¹ãƒˆå½¢å¼ã€ï¼’ï¼šãƒ–ãƒ©ãƒƒã‚¯ãƒªã‚¹ãƒˆå½¢å¼'
    ),
		'ips' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_job_mail_templates = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    'mail_type_cd' => array(
      'type' => 'text',
      'null' => false,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'åç§°',
      'charset' => 'utf8'
    ),
		'value_type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'value' => array('type' => 'integer', 'null' => false, 'default' => '9', 'unsigned' => false),
    'time' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'comment' => 'æ™‚é–“'
    ),
    'sender' => array(
      'type' => 'string',
      'null' => false,
      'default' => 'ï¼ˆsincloï¼‰',
      'length' => 200,
      'collate' => 'utf8_general_ci',
      'charset' => 'utf8'
    ),
    'subject' => array(
      'type' => 'text',
      'null' => false,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'ãƒ¡ãƒ¼ãƒ«ã‚¿ã‚¤ãƒˆãƒ«',
      'charset' => 'utf8'
    ),
    'mail_body' => array(
      'type' => 'text',
      'null' => true,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'ãƒ¡ãƒ¼ãƒ«æœ¬æ–‡',
      'charset' => 'utf8'
    ),
		'agreement_flg' => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false),
		'send_mail_ml_flg' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'send_mail_application_user_flg' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'send_mail_administrator_user_flg' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'send_mail_sinclo_all_users_flg' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_landscape_data = array(
		'lbc_code' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 11, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'ip_address' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 15, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_zip_code' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 8, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_address' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_tel' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 13, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_fax' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 13, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_ipo_type' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_date' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_capital_code' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_employees_code' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_gross_code' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_president' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_industrial_category_m' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'org_url' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'houjin_bangou' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'houjin_address' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'updated' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'ip_address', 'unique' => 1),
			'idx_ip_address_lbc_code_org_name' => array('column' => array('ip_address', 'lbc_code', 'org_name'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_mail_templates = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'mail_type_cd' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 5, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'template' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_mail_transmission_settings = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'from_address' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'from_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'to_address' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'to_name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'cc_address' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'cc_name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'bcc_address' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'bcc_name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'subject' => array('type' => 'string', 'null' => true, 'default' => 'no title', 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_operating_hours = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'time_settings' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'active_flg' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'type' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_system_mail_templates = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    'mail_type_cd' => array(
      'type' => 'text',
      'null' => false,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'ã‚¿ã‚¤ãƒ—',
      'charset' => 'utf8'
    ),
    'sender' => array(
      'type' => 'string',
      'null' => false,
      'default' => 'ï¼ˆsincloï¼‰',
      'length' => 200,
      'collate' => 'utf8_general_ci',
      'charset' => 'utf8'
    ),
    'subject' => array(
      'type' => 'text',
      'null' => false,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'ä»¶å',
      'charset' => 'utf8'
    ),
    'mail_body' => array(
      'type' => 'text',
      'null' => false,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'æœ¬æ–‡',
      'charset' => 'utf8'
    ),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    'm_companies_id' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'key' => 'index',
      'comment' => 'ä¼æ¥­ãƒžã‚¹ã‚¿ID'
    ),
    'user_name' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'length' => 100,
      'collate' => 'utf8_general_ci',
      'comment' => 'ãƒ¦ãƒ¼ã‚¶ãƒ¼å',
      'charset' => 'utf8'
    ),
    'display_name' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 100,
      'collate' => 'utf8_general_ci',
      'comment' => 'è¡¨ç¤ºå',
      'charset' => 'utf8'
    ),
    'mail_address' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'length' => 200,
      'collate' => 'utf8_general_ci',
      'comment' => 'ãƒ¡ãƒ¼ãƒ«ã‚¢ãƒ‰ãƒ¬ã‚¹',
      'charset' => 'utf8'
    ),
    'password' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'length' => 100,
      'collate' => 'utf8_general_ci',
      'comment' => 'ãƒ‘ã‚¹ãƒ¯ãƒ¼ãƒ‰',
      'charset' => 'utf8'
    ),
		'change_password_flg' => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false),
    'permission_level' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'æ¨©é™ãƒ¬ãƒ™ãƒ«'
    ),
    'settings' => array(
      'type' => 'text',
      'null' => true,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'ãã®ä»–å€‹åˆ¥è¨­å®š',
      'charset' => 'utf8'
    ),
    'operation_list_columns' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 500,
      'collate' => 'utf8_general_ci',
      'comment' => 'ãƒªã‚¢ãƒ«ã‚¿ã‚¤ãƒ ãƒ¢ãƒ‹ã‚¿ä¸€è¦§è¡¨ç¤ºé …ç›®ãƒªã‚¹ãƒˆ',
      'charset' => 'utf8'
    ),
    'history_list_columns' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 100,
      'collate' => 'utf8_general_ci',
      'comment' => 'å±¥æ­´ä¸€è¦§è¡¨ç¤ºé …ç›®ãƒªã‚¹ãƒˆ',
      'charset' => 'utf8'
    ),
    'session_rand_str' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 20,
      'collate' => 'utf8_general_ci',
      'comment' => 'å¤šé‡ãƒ­ã‚°ã‚¤ãƒ³é˜²æ­¢ç”¨æ–‡å­—åˆ—',
      'charset' => 'utf8'
    ),
		'chat_history_screen_flg' => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false),
		'memo' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
    'del_flg' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'å‰Šé™¤ãƒ•ãƒ©ã‚°'
    ),
		'error_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
    'locked_datetime' => array(
      'type' => 'datetime',
      'null' => true,
      'default' => null,
      'comment' => 'ãƒ­ãƒƒã‚¯ã—ãŸæ—¥æ™‚'
    ),
    'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ç™»éŒ²æ—¥'),
    'created_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ç™»éŒ²å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'æ›´æ–°æ—¥'),
    'modified_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'æ›´æ–°å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'å‰Šé™¤æ—¥'),
    'deleted_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'å‰Šé™¤å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'm_companies_id' => array('column' => 'm_companies_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $m_widget_settings = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    'm_companies_id' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'key' => 'index',
      'comment' => 'ä¼æ¥­ãƒžã‚¹ã‚¿ID'
    ),
    'display_type' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'comment' => 'è¡¨ç¤ºç¨®åˆ¥'
    ),
    'style_settings' => array(
      'type' => 'text',
      'null' => true,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'ã‚¹ã‚¿ã‚¤ãƒ«è¨­å®š',
      'charset' => 'utf8'
    ),
    'del_flg' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'å‰Šé™¤ãƒ•ãƒ©ã‚°'
    ),
    'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ç™»éŒ²æ—¥'),
    'created_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ç™»éŒ²å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'æ›´æ–°æ—¥'),
    'modified_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'æ›´æ–°å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'å‰Šé™¤æ—¥'),
    'deleted_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'å‰Šé™¤å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'm_companies_id' => array('column' => 'm_companies_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $public_holidays = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'year' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'month' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'day' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $schema_migrations = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'class' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'type' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_auto_messages = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    'm_companies_id' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ä¼æ¥­ID'
    ),
    'name' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 50,
      'collate' => 'utf8_general_ci',
      'comment' => 'ã‚ªãƒ¼ãƒˆãƒ¡ãƒƒã‚»ãƒ¼ã‚¸åç§°',
      'charset' => 'utf8'
    ),
    'trigger_type' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ãƒˆãƒªã‚¬ãƒ¼ã®ç¨®é¡ž'
    ),
    'activity' => array(
      'type' => 'text',
      'null' => true,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'ã‚ªãƒ¼ãƒˆãƒ¡ãƒƒã‚»ãƒ¼ã‚¸è¨­å®šå†…å®¹',
      'charset' => 'utf8'
    ),
    'action_type' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ã‚¢ã‚¯ã‚·ãƒ§ãƒ³ã®ç¨®é¡ž'
    ),
		'send_mail_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'm_mail_transmission_settings_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false),
		'm_mail_template_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false),
    't_chatbot_scenario_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ãƒãƒ£ãƒƒãƒˆãƒœãƒƒãƒˆã‚·ãƒŠãƒªã‚ªID'
    ),
    'active_flg' => array(
      'type' => 'integer',
      'null' => false,
      'default' => '0',
      'unsigned' => false,
      'comment' => '0:æœ‰åŠ¹ã€1:ç„¡åŠ¹'
    ),
    'del_flg' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'å‰Šé™¤ãƒ•ãƒ©ã‚°'
    ),
    'sort' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'ã‚½ãƒ¼ãƒˆé †'
    ),
    'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ç™»éŒ²æ—¥'),
    'created_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ç™»éŒ²å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'æ›´æ–°æ—¥'),
    'modified_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'æ›´æ–°å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'å‰Šé™¤æ—¥'),
    'deleted_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'å‰Šé™¤å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_campaigns = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    'm_companies_id' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ä¼æ¥­ID'
    ),
    'name' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 100,
      'collate' => 'utf8_general_ci',
      'comment' => 'ã‚­ãƒ£ãƒ³ãƒšãƒ¼ãƒ³å',
      'charset' => 'utf8'
    ),
    'parameter' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 100,
      'collate' => 'utf8_general_ci',
      'comment' => 'URLãƒ‘ãƒ©ãƒ¡ãƒ¼ã‚¿',
      'charset' => 'utf8'
    ),
    'comment' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 300,
      'collate' => 'utf8_general_ci',
      'comment' => 'ã‚³ãƒ¡ãƒ³ãƒˆ',
      'charset' => 'utf8'
    ),
    'sort' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'ã‚½ãƒ¼ãƒˆé †'
    ),
    'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ç™»éŒ²æ—¥'),
    'created_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ç™»éŒ²å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'æ›´æ–°æ—¥'),
    'modified_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'æ›´æ–°å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'å‰Šé™¤æ—¥'),
    'deleted_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'å‰Šé™¤å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

  public $t_chatbot_diagrams = array(
    'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
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
      'PRIMARY' => array('column' => 'id', 'unique' => 1)
    ),
    'tableParameters' => array(
      'charset' => 'utf8',
      'collate' => 'utf8_general_ci',
      'engine' => 'InnoDB',
      'comment' => 'チャットツリー管理テーブル'
    )
  );

	public $t_chatbot_scenario_send_files = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    'm_companies_id' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ä¼æ¥­ID'
    ),
    'download_url' => array(
      'type' => 'text',
      'null' => true,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'ãƒ€ã‚¦ãƒ³ãƒ­ãƒ¼ãƒ‰ç”¨URL',
      'charset' => 'utf8'
    ),
    'file_path' => array(
      'type' => 'text',
      'null' => true,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'å–å¾—å…ˆãƒ‘ã‚¹',
      'charset' => 'utf8'
    ),
    'file_name' => array(
      'type' => 'string',
      'null' => false,
      'default' => '0',
      'length' => 200,
      'collate' => 'utf8_general_ci',
      'comment' => 'ãƒ•ã‚¡ã‚¤ãƒ«å',
      'charset' => 'utf8'
    ),
    'file_size' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ãƒ•ã‚¡ã‚¤ãƒ«ã‚µã‚¤ã‚º'
    ),
    'del_flg' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'å‰Šé™¤ãƒ•ãƒ©ã‚°'
    ),
    'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ç™»éŒ²æ—¥'),
    'created_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ç™»éŒ²å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'æ›´æ–°æ—¥'),
    'modified_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'æ›´æ–°å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'å‰Šé™¤æ—¥'),
    'deleted_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'å‰Šé™¤å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_chatbot_scenarios = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    'm_companies_id' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ä¼æ¥­ID'
    ),
    'name' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 50,
      'collate' => 'utf8_general_ci',
      'comment' => 'ã‚·ãƒŠãƒªã‚ªåç§°',
      'charset' => 'utf8'
    ),
    'activity' => array(
      'type' => 'text',
      'null' => true,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'ã‚·ãƒŠãƒªã‚ªè¨­å®šå†…å®¹',
      'charset' => 'utf8'
    ),
    'del_flg' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'å‰Šé™¤ãƒ•ãƒ©ã‚°'
    ),
    'sort' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'ã‚½ãƒ¼ãƒˆé †'
    ),
    'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ç™»éŒ²æ—¥'),
    'created_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ç™»éŒ²å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'æ›´æ–°æ—¥'),
    'modified_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'æ›´æ–°å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'å‰Šé™¤æ—¥'),
    'deleted_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'å‰Šé™¤å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_conversation_count = array(
		'visitors_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'conversation_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_custom_variables = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    'm_companies_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ä¼æ¥­ID'
    ),
    'variable_name' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'å¤‰æ•°å',
      'charset' => 'utf8'
    ),
    'type' => array(
      'type' => 'integer',
      'null' => false,
      'default' => '3',
      'length' => 2,
      'unsigned' => false,
      'comment' => 'ã‚¿ã‚¤ãƒ—'
    ),
    'attribute_value' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'length' => 500,
      'collate' => 'utf8_general_ci',
      'comment' => 'å±žæ€§å€¤',
      'charset' => 'utf8'
    ),
    'comment' => array(
      'type' => 'text',
      'null' => true,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'ã‚³ãƒ¡ãƒ³ãƒˆ',
      'charset' => 'utf8'
    ),
    'delete_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => 'å‰Šé™¤ãƒ•ãƒ©ã‚°'),
    'sort' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'ã‚½ãƒ¼ãƒˆé †'
    ),
    'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ä½œæˆæ—¥'),
    'created_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ä½œæˆãƒ¦ãƒ¼ã‚¶ãƒ¼ID'
    ),
    'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'æ›´æ–°æ—¥'),
    'modified_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'æ›´æ–°ãƒ¦ãƒ¼ã‚¶ãƒ¼ID'
    ),
    'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'å‰Šé™¤æ—¥æ™‚'),
    'deleted_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'å‰Šé™¤ãƒ¦ãƒ¼ã‚¶ãƒ¼ID'
    ),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_customer_information_settings = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    'm_companies_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ä¼æ¥­ID
'
    ),
    'item_name' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'é …ç›®å',
      'charset' => 'utf8'
    ),
    'input_type' => array(
      'type' => 'integer',
      'null' => false,
      'default' => '1',
      'length' => 2,
      'unsigned' => false,
      'comment' => 'å…¥åŠ›ã‚¿ã‚¤ãƒ—
'
    ),
    'input_option' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'å…¥åŠ›ã‚¿ã‚¤ãƒ—ã®ã‚ªãƒ—ã‚·ãƒ§ãƒ³',
      'charset' => 'utf8'
    ),
    'show_realtime_monitor_flg' => array(
      'type' => 'boolean',
      'null' => false,
      'default' => '0',
      'comment' => 'ãƒªã‚¢ãƒ«ã‚¿ã‚¤ãƒ ãƒ¢ãƒ‹ã‚¿è¡¨ç¤ºå¯å¦'
    ),
    'show_send_mail_flg' => array(
      'type' => 'boolean',
      'null' => false,
      'default' => '0',
      'comment' => 'ãƒ¡ãƒ¼ãƒ«é€ä¿¡æ™‚æœ¬æ–‡è¨˜è¼‰'
    ),
    'sync_custom_variable_flg' => array(
      'type' => 'boolean',
      'null' => false,
      'default' => '0',
      'comment' => 'ã‚«ã‚¹ã‚¿ãƒ å¤‰æ•°è‡ªå‹•ç™»éŒ²'
    ),
    't_custom_variables_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'åŒæœŸã‚«ã‚¹ã‚¿ãƒ å¤‰æ•°ID'
    ),
    'comment' => array(
      'type' => 'text',
      'null' => true,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'ã‚³ãƒ¡ãƒ³ãƒˆ',
      'charset' => 'utf8'
    ),
    'delete_flg' => array('type' => 'boolean', 'null' => false, 'default' => null, 'comment' => 'å‰Šé™¤ãƒ•ãƒ©ã‚°'),
    'sort' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ã‚½ãƒ¼ãƒˆé †'
    ),
    'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ä½œæˆæ—¥'),
    'created_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ä½œè£½ãƒ¦ãƒ¼ã‚¶ãƒ¼ID'
    ),
    'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'æ›´æ–°æ—¥'),
    'modified_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'æ›´æ–°ãƒ¦ãƒ¼ã‚¶ãƒ¼ID'
    ),
    'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'å‰Šé™¤æ—¥æ™‚'),
    'deleted_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'å‰Šé™¤ãƒ¦ãƒ¼ã‚¶ãƒ¼ID'
    ),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_dictionaries = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    'm_companies_id' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ä¼æ¥­ID'
    ),
    'm_users_id' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ãƒ¦ãƒ¼ã‚¶ãƒ¼ID'
    ),
		'm_category_id' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false),
    'word' => array(
      'type' => 'text',
      'null' => true,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'æ–‡ç« ',
      'charset' => 'utf8'
    ),
    'type' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ã‚¿ã‚¤ãƒ—'
    ),
    'sort' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '999',
      'unsigned' => false,
      'comment' => 'ã‚½ãƒ¼ãƒˆé †'
    ),
    'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ç™»éŒ²æ—¥'),
    'created_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ç™»éŒ²å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'æ›´æ–°æ—¥'),
    'modified_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'æ›´æ–°å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'å‰Šé™¤æ—¥'),
    'deleted_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'å‰Šé™¤å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_dictionary_categories = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    'm_companies_id' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ä¼æ¥­ID'
    ),
    'category_name' => array(
      'type' => 'text',
      'null' => true,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'ã‚«ãƒ†ã‚´ãƒªãƒ¼å',
      'charset' => 'utf8'
    ),
    'sort' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '999',
      'unsigned' => false,
      'comment' => 'ã‚½ãƒ¼ãƒˆé †'
    ),
    'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ç™»éŒ²æ—¥'),
    'created_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ç™»éŒ²å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'æ›´æ–°æ—¥'),
    'modified_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'æ›´æ–°å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'å‰Šé™¤æ—¥'),
    'deleted_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'å‰Šé™¤å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_documents = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    'm_companies_id' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ä¼æ¥­ãƒžã‚¹ã‚¿ID'
    ),
    'name' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'length' => 30,
      'collate' => 'utf8_general_ci',
      'comment' => 'è³‡æ–™å',
      'charset' => 'utf8'
    ),
    'overview' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'length' => 300,
      'collate' => 'utf8_general_ci',
      'comment' => 'æ¦‚è¦',
      'charset' => 'utf8'
    ),
    'tag' => array(
      'type' => 'text',
      'null' => false,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'ã‚¿ã‚°',
      'charset' => 'utf8'
    ),
    'file_name' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'length' => 200,
      'collate' => 'utf8_general_ci',
      'comment' => 'è³‡æ–™ãƒ•ã‚¡ã‚¤ãƒ«å',
      'charset' => 'utf8'
    ),
    'manuscript' => array(
      'type' => 'text',
      'null' => false,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'åŽŸç¨¿',
      'charset' => 'utf8'
    ),
    'download_flg' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'ãƒ€ã‚¦ãƒ³ãƒ­ãƒ¼ãƒ‰ãƒ•ãƒ©ã‚°'
    ),
    'pagenation_flg' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'ãƒšãƒ¼ã‚¸æ•°è¡¨ç¤ºãƒ•ãƒ©ã‚°'
    ),
    'password' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'length' => 100,
      'collate' => 'utf8_general_ci',
      'comment' => 'ãƒ‘ã‚¹ãƒ¯ãƒ¼ãƒ‰',
      'charset' => 'utf8'
    ),
		'settings' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => 'document info', 'charset' => 'utf8'),
    'del_flg' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'å‰Šé™¤ãƒ•ãƒ©ã‚°'
    ),
    'sort' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'ã‚½ãƒ¼ãƒˆé †'
    ),
    'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ç™»éŒ²æ—¥'),
    'created_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ç™»éŒ²å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'æ›´æ–°æ—¥'),
    'modified_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'æ›´æ–°å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'å‰Šé™¤æ—¥'),
    'deleted_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'å‰Šé™¤å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_external_api_connections = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    'm_companies_id' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ä¼æ¥­ID'
    ),
    'url' => array(
      'type' => 'text',
      'null' => false,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'é€£æºå…ˆURL',
      'charset' => 'utf8'
    ),
    'method_type' => array(
      'type' => 'integer',
      'null' => false,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'ãƒ¡ã‚½ãƒƒãƒ‰ç¨®åˆ¥'
    ),
    'request_headers' => array(
      'type' => 'text',
      'null' => false,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'ãƒªã‚¯ã‚¨ã‚¹ãƒˆãƒ˜ãƒƒãƒ€ãƒ¼æƒ…å ±',
      'charset' => 'utf8'
    ),
    'request_body' => array(
      'type' => 'text',
      'null' => false,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'ãƒªã‚¯ã‚¨ã‚¹ãƒˆãƒœãƒ‡ã‚£æƒ…å ±',
      'charset' => 'utf8'
    ),
    'response_type' => array(
      'type' => 'integer',
      'null' => false,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'ãƒ¬ã‚¹ãƒãƒ³ã‚¹ã‚¿ã‚¤ãƒ—ç¨®åˆ¥'
    ),
    'response_body_maps' => array(
      'type' => 'text',
      'null' => false,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'ãƒ¬ã‚¹ãƒãƒ³ã‚¹ãƒœãƒ‡ã‚£ã‹ã‚‰ã®ãƒ‡ãƒ¼ã‚¿å–å¾—æƒ…å ±',
      'charset' => 'utf8'
    ),
    'del_flg' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'å‰Šé™¤ãƒ•ãƒ©ã‚°'
    ),
    'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ç™»éŒ²æ—¥'),
    'created_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ç™»éŒ²å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'æ›´æ–°æ—¥'),
    'modified_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'æ›´æ–°å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'å‰Šé™¤æ—¥'),
    'deleted_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'å‰Šé™¤å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_histories = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    'm_companies_id' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'key' => 'index',
      'comment' => 'ä¼æ¥­ãƒžã‚¹ã‚¿ID'
    ),
    'visitors_id' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'length' => 20,
      'collate' => 'utf8_general_ci',
      'comment' => 'è¨ªå•è€…ID',
      'charset' => 'utf8'
    ),
    'ip_address' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 15,
      'collate' => 'utf8_general_ci',
      'comment' => 'IPã‚¢ãƒ‰ãƒ¬ã‚¹',
      'charset' => 'utf8'
    ),
    'tab_id' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'length' => 50,
      'collate' => 'utf8_general_ci',
      'comment' => 'ã‚¿ãƒ–ID',
      'charset' => 'utf8'
    ),
    'user_agent' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 300,
      'collate' => 'utf8_general_ci',
      'comment' => 'ãƒ¦ãƒ¼ã‚¶ãƒ¼ã‚¨ãƒ¼ã‚¸ã‚§ãƒ³ãƒˆ',
      'charset' => 'utf8'
    ),
    'access_date' => array(
      'type' => 'datetime',
      'null' => true,
      'default' => null,
      'comment' => 'ã‚¢ã‚¯ã‚»ã‚¹é–‹å§‹æ—¥æ™‚'
    ),
    'out_date' => array(
      'type' => 'datetime',
      'null' => true,
      'default' => null,
      'comment' => 'ã‚¢ã‚¯ã‚»ã‚¹çµ‚äº†æ—¥æ™‚'
    ),
    'referrer_url' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 300,
      'collate' => 'utf8_general_ci',
      'comment' => 'ãƒªãƒ•ã‚¡ãƒ©ãƒ¼æƒ…å ±',
      'charset' => 'utf8'
    ),
    'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ç™»éŒ²æ—¥'),
    'created_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ç™»éŒ²å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'æ›´æ–°æ—¥'),
    'modified_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'æ›´æ–°å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'å‰Šé™¤æ—¥'),
    'deleted_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'å‰Šé™¤å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'm_companies_idx' => array('column' => 'm_companies_id', 'unique' => 0),
			'company_visitor' => array('column' => array('m_companies_id', 'visitors_id'), 'unique' => 0),
			'company_ip' => array('column' => array('m_companies_id', 'ip_address'), 'unique' => 0),
			'company_access_date' => array('column' => array('m_companies_id', 'access_date'), 'unique' => 0),
			'company_tab_id' => array('column' => array('m_companies_id', 'tab_id'), 'unique' => 0),
			't_histories_m_companies_id_access_date_id_index' => array('column' => array('m_companies_id', 'access_date', 'id'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_access_counts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'year' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false),
		'month' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'day' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'hour' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'access_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'company' => array('column' => array('m_companies_id', 'year', 'access_count', 'month', 'day', 'hour'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_chat_active_users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		't_history_chat_logs_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'm_users_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'idx_t_history_chat_logs_id_m_users_id' => array('column' => array('t_history_chat_logs_id', 'm_users_id'), 'unique' => 0),
			'idx_m_companies_id_users_id_chat_logs_id' => array('column' => array('m_companies_id', 'id', 'm_users_id', 't_history_chat_logs_id'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_chat_log_times = array(
    't_history_chat_logs_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
    't_histories_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
    'type' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'unsigned' => false),
		'datetime' => array('type' => 'datetime', 'null' => false, 'default' => null, 'length' => 2),
		'indexes' => array(),
    'tableParameters' => array(
      'charset' => 'utf8',
      'collate' => 'utf8_general_ci',
      'engine' => 'InnoDB',
      'comment' => 'ãƒãƒ£ãƒƒãƒˆå±¥æ­´æ™‚é–“ç®¡ç†ãƒ†ãƒ¼ãƒ–ãƒ«'
    )
	);

	public $t_history_chat_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    't_histories_id' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'key' => 'index',
      'comment' => 'å±¥æ­´ID'
    ),
    't_history_stay_logs_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ç§»å‹•å±¥æ­´TBLã®ID'
    ),
		'm_companies_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
    'visitors_id' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'length' => 20,
      'collate' => 'utf8_general_ci',
      'comment' => 'è¨ªå•è€…ID',
      'charset' => 'utf8'
    ),
    'm_users_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'å¯¾å¿œãƒ¦ãƒ¼ã‚¶ãƒ¼ID'
    ),
    'message' => array(
      'type' => 'string',
      'null' => true,
      'default' => null,
      'length' => 21800,
      'collate' => 'utf8_general_ci',
      'comment' => 'ãƒ¡ãƒƒã‚»ãƒ¼ã‚¸',
      'charset' => 'utf8'
    ),
    'message_type' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'key' => 'index',
      'comment' => 'ãƒ¡ãƒƒã‚»ãƒ¼ã‚¸ç¨®åˆ¥ï¼ˆ1:è¨ªå•è€…ã‹ã‚‰ã€2:ä¼æ¥­å´ã‹ã‚‰ï¼‰'
    ),
		'message_distinction' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'message_request_flg' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => false, 'key' => 'index'),
		'notice_flg' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
    'message_read_flg' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'æ—¢èª­ãƒ•ãƒ©ã‚°'
    ),
    'achievement_flg' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'key' => 'index',
      'comment' => 'æˆæžœãƒ•ãƒ©ã‚°(1:æœ‰åŠ¹, 2:ç„¡åŠ¹, null: æŒ‡å®šãªã—)'
    ),
		'send_mail_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		't_mail_transmission_logs_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'delete_flg' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false),
		'hide_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'length' => 2),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'deleted_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			't_histories_id_idx' => array('column' => 't_histories_id', 'unique' => 0),
			'idx_t_history_chat_logs_message_type' => array('column' => 'message_type', 'unique' => 0),
			'idx_t_history_chat_logs_message_request_flg' => array('column' => 'message_request_flg', 'unique' => 0),
			'idx_t_history_chat_logs_achievement_flg' => array('column' => 'achievement_flg', 'unique' => 0),
			'idx_t_history_chat_logs_request_flg_companies_id_users_id' => array('column' => array('message_request_flg', 'm_companies_id', 'm_users_id', 't_histories_id', 'message_distinction', 'created'), 'unique' => 0),
			'idx_t_history_chat_logs_message_type_companies_id_users_id' => array('column' => array('message_type', 'm_companies_id', 'm_users_id', 't_histories_id', 'message_distinction', 'created'), 'unique' => 0),
			'idx_t_history_chat_logs_achievement_flg_companies_id' => array('column' => array('achievement_flg', 'm_companies_id', 't_histories_id'), 'unique' => 0),
			'idx_t_history_chat_logs_request_flg_companies_id' => array('column' => array('message_request_flg', 'm_companies_id', 't_histories_id', 'message_distinction', 'created'), 'unique' => 0),
			'idx_t_history_chat_logs_achievement_flg_companies_id_users_id' => array('column' => array('achievement_flg', 'm_companies_id', 'm_users_id', 't_histories_id'), 'unique' => 0),
			'idx_t_history_chat_logs_m_companies_id_visitors_id' => array('column' => array('m_companies_id', 'visitors_id', 't_histories_id', 'created'), 'unique' => 0),
			'idx_m_companies_id_t_histories_id_t_history_stay_logs_id' => array('column' => array('m_companies_id', 't_histories_id', 't_history_stay_logs_id', 'message_type', 'notice_flg', 'created', 'message_read_flg', 'achievement_flg'), 'unique' => 0),
			'idx_m_companies_id_message_type_notice_flg' => array('column' => array('m_companies_id', 'message_type', 'notice_flg'), 'unique' => 0),
			'idx_t_history_chat_logs_m_companies_id_t_histories_id_created' => array('column' => array('m_companies_id', 't_histories_id', 'created'), 'unique' => 0),
			'idx_t_history_chat_logs_m_companies_id_message_type_created' => array('column' => array('m_companies_id', 'message_type', 'created'), 'unique' => 0),
			't_history_chat_logs_mcid_mt_nf_c_thid_index' => array('column' => array('m_companies_id', 't_histories_id', 'message_type', 'notice_flg', 'created'), 'unique' => 0),
			't_history_chat_logs_mcid_thid_mt_c_index' => array('column' => array('m_companies_id', 't_histories_id', 'message_type', 'created'), 'unique' => 0),
			't_history_chat_logs_mcid_thid_mt_md_index' => array('column' => array('m_companies_id', 't_histories_id', 'message_type', 'message_distinction'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_link_count_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		't_histories_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		't_history_stay_logs_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'link_url' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'idx_m_companies_id' => array('column' => 'm_companies_id', 'unique' => 0),
			'idx_m_companies_id_t_histories_id' => array('column' => array('m_companies_id', 't_histories_id'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_link_counts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'year' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'unsigned' => false),
		'month' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'day' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'hour' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'link_count' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'idx_m_companies_id_year' => array('column' => array('m_companies_id', 'year'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_share_displays = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    't_histories_id' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'key' => 'index',
      'comment' => 'å±¥æ­´ID'
    ),
    'm_users_id' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'comment' => 'å¯¾å¿œãƒ¦ãƒ¼ã‚¶ãƒ¼ID'
    ),
    'start_time' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'åŒæœŸé–‹å§‹æ—¥æ™‚'),
    'finish_time' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'åŒæœŸçµ‚äº†æ—¥æ™‚'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			't_histories_id' => array('column' => 't_histories_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_stay_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    't_histories_id' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => false,
      'key' => 'index',
      'comment' => 'å±¥æ­´ID'
    ),
    'title' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'length' => 100,
      'key' => 'index',
      'collate' => 'utf8_general_ci',
      'comment' => 'ãƒšãƒ¼ã‚¸ã‚¿ã‚¤ãƒˆãƒ«',
      'charset' => 'utf8'
    ),
		'url' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'URL', 'charset' => 'utf8'),
    'stay_time' => array('type' => 'time', 'null' => false, 'default' => null, 'comment' => 'æ»žåœ¨æ™‚é–“'),
    'del_flg' => array(
      'type' => 'integer',
      'null' => true,
      'default' => '0',
      'unsigned' => false,
      'comment' => 'å‰Šé™¤ãƒ•ãƒ©ã‚°'
    ),
    'created' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'ç™»éŒ²æ—¥'),
    'created_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ç™»éŒ²å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
    'modified' => array('type' => 'datetime', 'null' => true, 'default' => null, 'comment' => 'æ›´æ–°æ—¥'),
    'modified_user_id' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'æ›´æ–°å®Ÿè¡Œãƒ¦ãƒ¼ã‚¶'
    ),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			't_histories_id_idx' => array('column' => 't_histories_id', 'unique' => 0),
			'idx_t_history_stay_logs_title_url' => array('column' => array('title', 'url'), 'unique' => 0, 'length' => array('url' => '255')),
			'idx_t_history_stay_logs_title' => array('column' => 'title', 'unique' => 0),
			'idx_t_history_stay_logs_url' => array('column' => 'url', 'unique' => 0, 'length' => array('url' => '255'))
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_widget_close_counts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'year' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false),
		'month' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'day' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'hour' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'widget_close_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'company' => array('column' => array('m_companies_id', 'year', 'widget_close_count', 'month', 'day', 'hour'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_widget_counts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'year' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false),
		'month' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'day' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'hour' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'widget_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'company' => array('column' => array('m_companies_id', 'year', 'widget_count', 'month', 'day', 'hour'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_widget_displays = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'tab_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'idx_t_history_widget_displays_tab_id' => array('column' => 'tab_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_history_widget_minimize_counts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'year' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 4, 'unsigned' => false),
		'month' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'day' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'hour' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'widget_minimize_count' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'company' => array('column' => array('m_companies_id', 'year', 'widget_minimize_count', 'month', 'day', 'hour'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_lead_list_settings = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'list_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'list_parameter' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'created_user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'company_index' => array('column' => 'm_companies_id', 'unique' => 0)
		),
    'tableParameters' => array(
      'charset' => 'utf8',
      'collate' => 'utf8_general_ci',
      'engine' => 'InnoDB',
      'comment' => 'ãƒªãƒ¼ãƒ‰ãƒªã‚¹ãƒˆã®è¨­å®šã‚’ç®¡ç†ã™ã‚‹ãƒ†ãƒ¼ãƒ–ãƒ«'
    )
	);

	public $t_lead_lists = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		't_lead_list_settings_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		't_chatbot_scenarios_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'lead_informations' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'landing_page' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'lead_regist_page' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'user_agent' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'SEARCH_INDEX' => array('column' => array('m_companies_id', 'created', 't_lead_list_settings_id'), 'unique' => 0)
		),
    'tableParameters' => array(
      'charset' => 'utf8',
      'collate' => 'utf8_general_ci',
      'engine' => 'InnoDB',
      'comment' => 'ç™»éŒ²ã•ã‚ŒãŸãƒªãƒ¼ãƒ‰ãƒªã‚¹ãƒˆã‚’ç®¡ç†ã™ã‚‹ãƒ†ãƒ¼ãƒ–ãƒ«'
    )
	);

	public $t_logins = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'm_users_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'ip_address' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 15, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'user_agent' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'idx_t_logins_m_companies_id_created_users_id' => array('column' => array('m_companies_id', 'created', 'm_users_id'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_mail_transmission_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'mail_type_cd' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 5, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'from_address' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'from_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'to_address' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'to_name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'cc_address' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'cc_name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'bcc_address' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'bcc_name' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'subject' => array('type' => 'string', 'null' => true, 'default' => 'no title', 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'body' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'send_flg' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'sent_datetime' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_receive_visitor_files = array(
    'id' => array(
      'type' => 'integer',
      'null' => false,
      'default' => null,
      'unsigned' => true,
      'key' => 'primary',
      'comment' => 'ãƒ¬ã‚³ãƒ¼ãƒ‰ID'
    ),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true),
		't_histories_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true),
		'visitors_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'uuid' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 64, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'saved_file_key' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'download_url' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'file_path' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'file_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'file_size' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true),
		'comment' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_reset_password_informations = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'm_users_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'mail_address' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'parameter' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 32, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'authentication_code' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 6, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
    'delete_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '0:ç„¡åŠ¹ã€1:æœ‰åŠ¹'),
		'expire' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
    'tableParameters' => array(
      'charset' => 'utf8',
      'collate' => 'utf8_general_ci',
      'engine' => 'InnoDB',
      'comment' => 'PWDãƒªãƒžã‚¤ãƒ³ãƒ€ãƒ¼è¨­å®šã®ç®¡ç†ãƒ†ãƒ¼ãƒ–ãƒ«'
    )
	);

	public $t_send_system_mail_schedules = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary', 'comment' => 'ID'),
    'sending_datetime' => array(
      'type' => 'datetime',
      'null' => false,
      'default' => null,
      'comment' => 'æ—¥ä»˜ã€æ—¥æ™‚'
    ),
    'subject' => array(
      'type' => 'text',
      'null' => false,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'ãƒ¡ãƒ¼ãƒ«ã‚¿ã‚¤ãƒˆãƒ«',
      'charset' => 'utf8'
    ),
    'mail_body' => array(
      'type' => 'text',
      'null' => false,
      'default' => null,
      'collate' => 'utf8_general_ci',
      'comment' => 'ãƒ¡ãƒ¼ãƒ«æœ¬æ–‡',
      'charset' => 'utf8'
    ),
    'mail_address' => array(
      'type' => 'string',
      'null' => false,
      'default' => null,
      'length' => 200,
      'collate' => 'utf8_general_ci',
      'comment' => 'ãƒ¡ãƒ¼ãƒ«ã‚¢ãƒ‰ãƒ¬ã‚¹',
      'charset' => 'utf8'
    ),
    'send-mail_flg' => array(
      'type' => 'integer',
      'null' => true,
      'default' => null,
      'unsigned' => false,
      'comment' => 'ãƒ¡ãƒ¼ãƒ«é€ä¿¡ãƒ•ãƒ©ã‚°'
    ),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $t_upload_transfer_files = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'saved_file_key' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'download_url' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 300, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'file_path' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'file_name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 200, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'file_size' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'download_flg' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'downloaded' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created_user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false),
		'deleted' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

}
