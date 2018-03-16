<?php
App::uses('AppModel', 'Model');
/**
 * TChatbotScenario Model
 *
 * @property MCompanies $MCompanies
 */
class TExternalApiConnection extends AppModel {

  public $name = "TExternalApiConnection";

  public $validate = [
    'url' => [
      'rule' => ['maxLength', 200],
      'allowEmpty' => false,
      'message' => '連携先URLを２００文字以内で入力してください'
    ]
  ];
}
