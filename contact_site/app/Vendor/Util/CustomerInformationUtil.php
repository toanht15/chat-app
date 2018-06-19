<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/06/19
 * Time: 11:40
 */

class CustomerInformationUtil
{
  protected static $interfaceMap = array(
    'company' => '企業名',
    'name' => '名前',
    'tel' => '電話番号',
    'mail' => 'メールアドレス',
    'memo' => 'メモ'
  );

  public static function convertOldIFData($data) {
    $newData = array();
    foreach($data as $k => $v) {
      if(!empty(self::$interfaceMap[$k])) {
        $newData  [self::$interfaceMap[$k]] = $v;
      } else {
        $newData[$k] = $v;
      }
    }
    return $newData;
  }
}