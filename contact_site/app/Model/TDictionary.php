<?php
App::uses('AppModel', 'Model');
/**
 * TDictionary Model
 *
 * @property TDictionary $TDirection
 */
class TDictionary extends AppModel {

  public $name = "TDictionary";


  /**
   * Validation rules
   *
   * @var array
   */
  public $validate = [
    'word' => [
      'maxlength' => [
        'rule' => ['maxLength', 100],
        'allowEmpty' => false,
        'message' => '１００文字以内で設定してください'
      ]
    ],
    'sort' => [
      'numeric' => [
          'rule' => 'numeric',
          'allowEmpty' => true,
          'message' => '1～999までの整数値で入力してください'
      ],
      'naturalNumber' => [
          'rule' => 'naturalNumber',
          'allowEmpty' => true,
          'message' => '1～999までの整数値で入力してください'
      ],
      'range' => [
          'rule' => ['range', 0, 999],
          'allowEmpty' => true,
          'message' => '1～999までの整数値で設定してください'
      ]
    ],
    'type' => [
      'inList' => [
          'rule' => ['inList', [C_AUTHORITY_ADMIN, C_AUTHORITY_NORMAL]],
          'message' => '使用範囲を選択してください'
      ]
    ],
  ];

}
