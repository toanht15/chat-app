<?php
App::uses('AppModel', 'Model');
/**
 * MOperatingHours Model
 * チャット設定テーブル
 *
 */
class MOperatingHour extends AppModel {

  public $name = 'MOperatingHour';

  public $validate = [
    'startTime0' => [
        'maxLength' => [
            'rule' => ['maxLength', 5],
            'allowEmpty' => false,
            'message' => '条件を設定してください。'
        ]
    ]
  ];
}

