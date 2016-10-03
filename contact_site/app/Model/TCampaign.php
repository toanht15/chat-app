<?php
App::uses('AppModel','Model');
class TCampaign extends AppModel {

  public $name = "TCampaigns";

   /**
   * Validation rules
   *
   * @var array
   */
  public $validate = [
    'name' => [
      'maxlength' => [
        'rule' => ['maxLength', 100],
        'allowEmpty' => false,
        'message' => '１００文字以内で設定してください'
      ]
    ],
    'parameter' => [
      'maxlength' => [
        'rule' => ['maxLength', 100],
        'allowEmpty' => false,
        'message' => '１００文字以内で設定してください'
      ]
    ],
  ];
}
?>