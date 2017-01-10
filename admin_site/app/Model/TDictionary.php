<?php
App::uses('AppModel', 'Model');
/**
 * TDictionary Model
 *
 */
class TDictionary extends AppModel {

  public $name = 'TDictionary';

  /**
  * Validation rules
  *
  * @var array
  */
  //登録,更新処理の場合
  public $validate = [
    'word' => [
      'maxlength' => [
        'rule' => ['maxLength', 100],
        'allowEmpty' => false,
        'message' => '100文字以内で設定してください'
      ]
    ]
  ];
}