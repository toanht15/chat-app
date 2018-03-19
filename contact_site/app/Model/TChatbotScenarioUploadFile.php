<?php
App::uses('AppModel', 'Model');
/**
 * TChatbotScenario Model
 *
 * @property MCompanies $MCompanies
 */
class TChatbotScenarioUploadFile extends AppModel {

  public $name = "TChatbotScenarioUploadFile";

  public $validate = [
    'file_path' => [
      'rule' => 'notBlank',
      'allowEmpty' => false,
      'message' => 'ファイルパスを設定してください'
    ],
    'file_name' => [
      'rule' => 'notBlank',
      'allowEmpty' => false,
      'message' => 'ファイル名を設定してください'
    ],
    'file_size' => [
      'rule' => 'notBlank',
      'allowEmpty' => false,
      'message' => 'ファイルサイズを設定してください'
    ]
  ];
}
