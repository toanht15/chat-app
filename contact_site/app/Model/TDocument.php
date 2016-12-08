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
    'files' => [
      'allowEmpty' => [
        'rule' => 'allowEmpty',
        'required' => 'create',
        'message' => 'PDFファイルを添付してください'
      ],
      'extension' => [
        'rule' => ['extension', ['pdf']],
        'allowEmpty' => false,
        'message' => 'PDFファイルを添付してください'
      ],
      'fileSize' => [
        'rule' => ['fileSize', '<=', '1GB'],
        'allowEmpty' => false,
        'message' => '10MB以上のファイルは添付できません'
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
