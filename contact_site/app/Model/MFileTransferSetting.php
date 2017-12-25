<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/12/25
 * Time: 12:09
 */

class MFileTransferSetting extends AppModel
{

  public $name = 'MFileTransferSetting';

  /**
   * Validation rules
   *
   * @var array
   */
  public $validate = [
      'type' => [
          'validType' => [
            'rule' => 'validType',
            'message' => '不正な設定です。'
          ]
      ],
      'allow_extensions' => [
          'isAlplaNumeric' => [
            'rule' => 'isAlplaNumeric',
            'message' => 'ドットを含めない拡張子をカンマ区切りで設定して下さい。'
          ],
          'validCharacter' => [
            'rule' => 'validCharacter',
            'message' => '利用できない文字が含まれています。'
          ]
      ]
  ];

  public function isAlplaNumeric($val) {
    $splited = explode(",", $val['allow_extensions']);
    foreach ($splited as $k => $v) {
      if(!Validation::alphaNumeric($v)) {
        return false;
      }
    }
    return true;
  }

  public function validType($type) {
    $data = intval($type['type']);
    return 1 <= $data && $data <= 2;
  }

  // 拡張子設定にファイル名に利用できない文字が混ざっていないかチェック
  // https://support.microsoft.com/ja-jp/help/879749
  public function validCharacter($allow_extensions) {
    $result = preg_match('/[\/\\<>\*\?\"\|\:\;\.]/', $allow_extensions['allow_extensions']);
    return $result === 0; // 含まない
  }
}