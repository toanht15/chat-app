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
      'fileCheck' => [
        'rule' => 'fileCheck',
        'message' => 'PDFファイルを添付してください'
      ],
      'extension' => [
        'rule' => ['extension', ['pdf']],
        'allowEmpty' => true,
        'message' => 'PDFファイルを添付してください'
      ],
      'fileSize' => [
        'rule' => ['fileSize', '<=', '10MB'],
        'allowEmpty' => true,
        'message' => '10MB以上のファイルは添付できません'
      ],
    ],
    'overview' => [
      'maxLength' => [
        'rule' => ['maxLength', 300],
        'allowEmpty' => true,
        'message' => '概要を300文字以内で入力してください'
      ]
    ],
    'settings' => [
      'prohibitedCharacters' => [
         'rule' => '/^(?!.*(<|>|&|\')).*$/',
         'message' => '<,>,&,\'を含まずに設定してください。'
      ]
    ],
    'rotation' => [
      'prohibitedCharacters' => [
         'rule' => '/^(?!.*(<|>|&|"|\')).*$/',
         'message' => '<,>,&,\'を含まずに設定してください。'
      ]
    ],
    'manuscript' => [
      'prohibitedCharacters' => [
         'rule' => '/^(?!.*(<|>|&|\')).*$/',
         'message' => '<,>,&,",\'を含まずに設定してください。'
      ]
     ]
  ];

  // ファイルの存在チェック（登録時のみ）
  public function fileCheck($data) {
    if ( empty($data['files']) && empty($this->data['TDocument']['id']) ) { // 更新
      return false;
    }
    else {
      return true;
    }
  }


}
