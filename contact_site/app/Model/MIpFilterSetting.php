<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/01/10
 * Time: 10:19
 */

class MIpFilterSetting extends AppModel
{

  public $name = 'MIpFilterSetting';

  /**
   * Validation rules
   *
   * @var array
   */
  public $validate = [
    'exclude_params' => [
      'rule' => 'checkParams',
      'message' => '有効なパラメーターを指定してください'
    ],
    'ips' => [
      'checkIps' => [
        'rule' => 'checkIps',
        'message' => '有効なIPアドレスを指定してください'
      ],
      'activeAndRequire' => [
        'rule' => 'activeAndRequire',
        'message' => '設定を有効にする場合は指定が必要です'
      ]
    ]
  ];

  public function activeAndRequire($v) {
    $data = $this->data['MIpFilterSetting'];
    $active = strcmp($data['active_flg'], "1") === 0;
    if($active) {
      return Validation::notBlank($v['ips']);
    } else {
      return true;
    }
  }

  static function checkIps($v){
    if ( !empty($v['ips']) ) {
      $list = explode("\n", $v['ips']);
      foreach( $list as $val ){
        if ( empty($val) ) { continue; }
        if ( !preg_match('/^(25\d|2[0-4]\d|1\d{2}|\d{1,2}).(25\d|2[0-4]\d|1\d{2}|\d{1,2}).(25\d|2[0-4]\d|1\d{2}|\d{1,2}).(25\d|2[0-4]\d|1\d{2}|\d{1,2})(\/3[0-2]|\/[1|2]\d|\/\d)?$/', trim($val)) ) {
          return false;
        }
      }
    }
    return true;
  }

}