<?php
class CreateTableMLandscapeData extends CakeMigration {

  /**
   * Migration description
   *
   * @var string
   */
  public $description = 'create_table_m_landscape_data';

  /**
   * Actions to be performed
   *
   * @var array $migration
   */
  public $migration = array(
      'up' => array(
          'create_table' => array(
              'm_landscape_data' => array(
                  'lbc_code' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 11, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
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
                      'PRIMARY' => array('column' => array('lbc_code', 'ip_address'), 'unique' => 1),
                  ),
                  'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB'),
              )
          )
      ),
      'down' => array(
          'drop_table' => array(
              'm_landscape_data'
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