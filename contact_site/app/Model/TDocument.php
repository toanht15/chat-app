<?php
App::uses('AppModel', 'Model');
/**
 * TDocument Model
 *
 */
class TDocument extends AppModel {

  public $name = 'TDocument';

  public $validate = [
    'name' => [
      'maxLength' => [
        'rule' => ['maxLength', 30],
        'allowEmpty' => false,
        'message' => '資料名を30文字以内で入力してください'
      ]
    ],
    'overview' => [
      'maxLength' => [
        'rule' => ['maxLength', 300],
        'allowEmpty' => false,
        'message' => '概要を300文字以内で入力してください'
      ]
    ]
  ];
}
