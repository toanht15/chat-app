<?php
App::uses('AppModel', 'Model');
/**
 * MChatSetting Model
 * チャット設定テーブル
 *
 */
class MChatSetting extends AppModel {

  public $name = 'MChatSetting';

  public $validate = [
    'sc_default_num' => [
      'range' => [
        'rule' => ['range', -1, 100],
        'allowEmpty' => false,
        'message' => '０～９９以内で設定してください。'
      ]
    ],
    'sorry_message' => [
      'maxLength' => [
        'rule' => ['maxLength', 300],
        'allowEmpty' => false,
        'message' => 'Sorryメッセージは３００文字以内で設定してください。'
      ]
    ]
  ];

}

