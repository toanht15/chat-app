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
      ],
      'prohibitedCharacters' => [
         'rule' => '/^(?!.*(<|>|&|"|\')).*$/',
         'message' => '<,>,&.",\'を含まずに設定してください。'
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
      'name' => [
        'prohibitedCharacters' => [
          'rule' => '/^(?!.*(<|>|&|"|\')).*$/',
          'message' => '<,>,&.",\'を含まずに設定してください。'
        ]
      ],
      'type' => [
        'prohibitedCharacters' => [
          'rule' => '/^(?!.*(<|>|&|"|\')).*$/',
          'message' => '<,>,&.",\'を含まずに設定してください。'
        ]
      ],
      'tmp_name' => [
        'prohibitedCharacters' => [
          'rule' => '/^(?!.*(<|>|&|"|\')).*$/',
          'message' => '<,>,&.",\'を含まずに設定してください。'
        ]
      ],
      'error' => [
        'prohibitedCharacters' => [
          'rule' => '/^(?!.*(<|>|&|"|\')).*$/',
          'message' => '<,>,&.",\'を含まずに設定してください。'
        ]
      ],
      'size' => [
        'prohibitedCharacters' => [
          'rule' => '/^(?!.*(<|>|&|"|\')).*$/',
          'message' => '<,>,&.",\'を含まずに設定してください。'
        ]
      ]
    ],
    'overview' => [
      'maxLength' => [
        'rule' => ['maxLength', 300],
        'allowEmpty' => true,
        'message' => '概要を300文字以内で入力してください'
      ],
      'prohibitedCharacters' => [
         'rule' => '/^(?!.*(<|>|&|"|\')).*$/',
         'message' => '<,>,&.",\'を含まずに設定してください。'
      ]
    ],
    'settings' => [
      'prohibitedCharacters' => [
         'rule' => '/^(?!.*(<|>|"|\')).*$/',
         'message' => '<,>,&.",\'を含まずに設定してください。'
      ]
    ],
    'rotation' => [
      'prohibitedCharacters' => [
         'rule' => '/^(?!.*(<|>|&|"|\')).*$/',
         'message' => '<,>,&.",\'を含まずに設定してください。'
      ]
    ],
    'download_flg' => [
      'prohibitedCharacters' => [
         'rule' => '/^(?!.*(<|>|&|"|\')).*$/',
         'message' => '<,>,&.",\'を含まずに設定してください。'
      ]
     ],
    'pagenation_flg' => [
       'prohibitedCharacters' => [
          'rule' => '/^(?!.*(<|>|&|"|\')).*$/',
          'message' => '<,>,&.",\'を含まずに設定してください。'
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
