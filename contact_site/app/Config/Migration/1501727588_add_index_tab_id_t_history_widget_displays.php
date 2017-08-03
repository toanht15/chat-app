<?php
class AddIndexTTabIdTHistoryWidgetDisplays extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_index_t_tab_id_t_history_widget_displays';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				't_history_widget_displays' => array(
					'tab_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
			),
			'create_field' => array(
				't_history_widget_displays' => array(
					'indexes' => array(
						'idx_t_history_widget_displays_tab_id' => array('column' => 'tab_id', 'unique' => 0),
					),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				't_history_widget_displays' => array(
					'tab_id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
				),
			),
			'drop_field' => array(
				't_history_widget_displays' => array('indexes' => array('idx_t_history_widget_displays_tab_id')),
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
