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
      'type' => [
          'validType' => [
              'rule' => 'validType',
              'message' => '不正な設定です。'
          ]
      ],
      'allow_extensions' => [
//          'validNOTBlankAndType' => [
//            'rule' => 'validNOTBlankAndType',
//            'message' => '拡張設定の場合は指定が必要です。'
//          ],
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

}