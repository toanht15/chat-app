<?php
class AlterTableTHistoryChatLogsAddIndex extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'alter_table_t_history_chat_logs_add_index';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
			  't_histories' => array(
			    'indexes' => array(
            't_histories_m_companies_id_access_date_id_index' => array('column' => array('m_companies_id', 'access_date', 'id'), 'unique' => 0)
          )
        ),
        't_history_chat_logs' => array(
					'indexes' => array(
            'idx_t_history_chat_logs_m_companies_id_t_histories_id_created' => array('column' => array('m_companies_id', 't_histories_id', 'created'), 'unique' => 0),
            'idx_t_history_chat_logs_m_companies_id_message_type_created' => array('column' => array('m_companies_id', 'message_type', 'created'), 'unique' => 0),
						't_history_chat_logs_mcid_mt_nf_c_thid_index' => array('column' => array('m_companies_id', 't_histories_id', 'message_type', 'notice_flg', 'created'), 'unique' => 0),
						't_history_chat_logs_mcid_thid_mt_c_index' => array('column' => array('m_companies_id', 't_histories_id', 'message_type', 'created'), 'unique' => 0),
             't_history_chat_logs_mcid_thid_mt_md_index' => array('column' => array('m_companies_id', 't_histories_id', 'message_type', 'message_distinction'), 'unique' => 0),
					),
				),
			),
      'drop_field' => array(
        't_history_chat_logs' => array('indexes' => array('idx_t_history_chat_logs_message_type_companies_id', 'idx_m_companies_id_message_type_notice_flg')),
      ),
		),
		'down' => array(
			'drop_field' => array(
				't_history_chat_logs' => array('indexes' => array('idx_t_history_chat_logs_m_companies_id_t_histories_id_created', 'idx_t_history_chat_logs_m_companies_id_message_type_created', 't_history_chat_logs_mcid_mt_nf_c_thid_index', 't_history_chat_logs_mcid_thid_mt_c_index')),
        't_histories' => array('indexes' => array('t_histories_m_companies_id_access_date_id_index')),
			),
      'create_field' => array(
        't_history_chat_logs' => array(
          'idx_m_companies_id_message_type_notice_flg' => array('column' => array('m_companies_id', 't_histories_id', 'message_type', 'notice_flg'), 'unique' => 0),
          'idx_t_history_chat_logs_message_type_companies_id' => array('column' => array('message_type', 'm_companies_id', 't_histories_id', 'message_distinction', 'created'), 'unique' => 0),
        ),
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
