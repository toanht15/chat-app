<?php
App::uses('AppModel', 'Model');
/**
 * TCustomVariables Model
 *
*/
class TCustomVariable extends AppModel {

  public $name = 'TCustomVariables';

  public $validate = [
      'variable_name' => [
          'rule' => ['maxLength', 100],
          'allowEmpty' => false,
          'message' => '変数名は１００文字以内で設定してください。'
      ],
      'attribute_value' => [
          'rule' => ['maxLength', 100],
          'allowEmpty' => false,
          'message' => 'CSSセレクタは１００文字以内で設定してください。'
      ]
  ];

}
