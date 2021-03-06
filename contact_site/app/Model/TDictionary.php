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
        'rule' => ['maxLength', 200],
        'allowEmpty' => false,
        'message' => '２００文字以内で設定してください'
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
