<?php

App::uses('AppModel', 'Model');
/**
 * TCustomerInformationSettings Model
 *
*/
class TCustomerInformationSetting extends AppModel {

  public $name = 'TCustomerInformationSettings';

  public $validate = [
    'item_name' => [
      'maxLength' => [
        'rule' => ['maxLength', 50],
        'allowEmpty' => false,
        'message' => '項目名は５０文字以内で設定してください。'
      ],
      'prohibitedCharacters' => [
        'rule' => '/^(?!.*(<|>|&|"|\')).*$/',
        'message' => '<,>,&.",\'を含まずに設定してください。'
      ]
    ]
  ];
}
