<?php
App::uses('AppModel', 'Model');
/**
 * MChatNotification Model
 *
 */
class MChatNotification extends AppModel {

  public $name = 'MChatNotification';

  public $validate = [
    'keyword' => [
      'rule' => ['maxLength', 100],
      'allowEmpty' => false,
      'message' => 'キーワードは１００文字以内で設定してください。'
    ],
    'name' => [
      'rule' => ['maxLength', 100],
      'allowEmpty' => false,
      'message' => '通知名は１００文字以内で設定してください。'
    ]
  ];

}
