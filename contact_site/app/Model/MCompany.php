<?php
App::uses('AppModel', 'Model');
/**
 * MCompany Model
 *
 */
class MCompany extends AppModel {

	public $name = 'MCompany';

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';

  public $validate = [
    'exclude_params' => [
      'rule' => 'checkParams',
      'message' => '有効なパラメーターを指定してください'
    ],
    'exclude_ips' => [
      'rule' => 'checkIps',
      'message' => '有効なIPアドレスを指定してください'
    ]
  ];

  static function checkParams($v){
    if ( !empty($v['exclude_params']) ) {
      $list = explode(PHP_EOL, $v['exclude_params']);
      foreach( $list as $val ){
        if ( empty($val) ) { continue; }
        if ( preg_match('/[\?|\=|\&]/', $val) ) {
          return false;
        }
      }
    }
    return true;
  }

  static function checkIps($v){
    if ( !empty($v['exclude_ips']) ) {
      $list = explode(PHP_EOL, $v['exclude_ips']);
      foreach( $list as $val ){
        if ( empty($val) ) { continue; }
        if ( !preg_match('/^(25\d|2[0-4]\d|1\d{2}|\d{1,2}).(25\d|2[0-4]\d|1\d{2}|\d{1,2}).(25\d|2[0-4]\d|1\d{2}|\d{1,2}).(25\d|2[0-4]\d|1\d{2}|\d{1,2})(\/3[0-2]|\/[1|2]\d|\/\d)?$/', $val) ) {
          return false;
        }
      }
    }
    return true;
  }

  public function getExcludeList($id) {
    $ret = $this->read(null, $id);
    $paramList = [];
    $ipList = [];
    if ( !empty($ret['MCompany']['exclude_params']) ) {
      $paramList = explode(PHP_EOL, trim($ret['MCompany']['exclude_params']));
    }
    if ( !empty($ret['MCompany']['exclude_ips']) ) {
      $ipList = explode(PHP_EOL, trim($ret['MCompany']['exclude_ips']));
    }

    return ['params' => $paramList, 'ips' => $ipList];
  }

  /**
   * Cidrで指定されている範囲を算出
   * http://stackoverflow.com/questions/4931721/getting-list-ips-from-cidr-notation-in-php
   * */
  public function cidrToRange($cidr) {
    $range = array();
    $cidr = explode('/', $cidr);
    $range[0] = long2ip((ip2long($cidr[0])) & ((-1 << (32 - (int)$cidr[1]))));
    $range[1] = long2ip((ip2long($cidr[0])) + pow(2, (32 - (int)$cidr[1])) - 1);
    return $range;
  }

}
