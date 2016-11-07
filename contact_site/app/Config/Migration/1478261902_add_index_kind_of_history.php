<?php
class AddIndexKindOfHistory extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_index_kind_of_history';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'm_customers' => array(
					'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '企業ID'),
				),
				't_histories' => array(
					'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '企業マスタID'),
				),
				't_history_stay_logs' => array(
					't_histories_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '履歴ID'),
				),
			),
			'create_field' => array(
				'm_customers' => array(
					'indexes' => array(
						'm_companies_id_idx' => array('column' => array('m_companies_id', 'visitors_id'), 'unique' => 0),
					),
				),
				't_histories' => array(
					'indexes' => array(
						'm_companies_idx' => array('column' => 'm_companies_id', 'unique' => 0),
						'company_visitor' => array('column' => array('m_companies_id', 'visitors_id'), 'unique' => 0),
						'company_ip' => array('column' => array('m_companies_id', 'ip_address'), 'unique' => 0),
						'company_access_date' => array('column' => array('m_companies_id', 'access_date'), 'unique' => 0),
					),
				),
				't_history_chat_logs' => array(
					'indexes' => array(
						't_histories_id_idx' => array('column' => 't_histories_id', 'unique' => 0),
					),
				),
				't_history_stay_logs' => array(
					'indexes' => array(
						't_histories_id_idx' => array('column' => 't_histories_id', 'unique' => 0),
					),
				),
			),
		),
		'down' => array(
			'create_field' => array(
        'm_companies' => array(
          'exclude_params' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '除外パラメータ', 'charset' => 'utf8'),
          'exclude_ips' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '除外IPアドレス', 'charset' => 'utf8'),
        ),
				't_history_chat_logs' => array(
					'indexes' => array(
						't_histories_id' => array('column' => 't_histories_id', 'unique' => 0),
					),
				),
			),
			'alter_field' => array(
				'm_customers' => array(
					'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '企業ID'),
				),
				't_histories' => array(
					'm_companies_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '企業マスタID'),
				),
				't_history_stay_logs' => array(
					't_histories_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '履歴ID'),
				),
			),
			'drop_field' => array(
				'm_customers' => array('indexes' => array('m_companies_id_idx')),
				't_histories' => array('indexes' => array('m_companies_idx', 'company_visitor', 'company_ip', 'company_access_date')),
				't_history_chat_logs' => array('indexes' => array('t_histories_id_idx')),
				't_history_stay_logs' => array('indexes' => array('t_histories_id_idx')),
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
