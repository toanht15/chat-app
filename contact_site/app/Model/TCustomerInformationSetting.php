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
        'rule' => ['maxLength', 100],
        'allowEmpty' => false,
        'message' => '項目名は１００文字以内で設定してください。'
    ]
  ];
}
