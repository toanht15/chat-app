<?php
App::uses('AppModel', 'Model');
/**
 * MUser Model
 *
 * @property MCompanies $MCompanies
 */
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
class MUser extends AppModel {

    public $name = "MUser";

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'user_name' => [
            'maxLength' => [
                'rule' => ['maxLength', 50],
                'allowEmpty' => false,
                'message' => 'ユーザー名は５０文字以内で設定してください。'
            ],
            'prohibitedCharacters' => [
                'rule' => '/^(?!.*(<|>|&|"|\')).*$/',
                'message' => '<,>,&.",\'を含まずに設定してください。'
            ]
        ],
        'display_name' => [
            'maxLength' => [
                'rule' => ['maxLength', 10],
                'allowEmpty' => false,
                'message' => '表示名は１０文字以内で設定してください。'
            ],
            'prohibitedCharacters' => [
                'rule' => '/^(?!.*(<|>|&|"|\')).*$/',
                'message' => '<,>,&.",\'を含まずに設定してください。'
            ]
        ],
        'mail_address' => [
            'email' => [
                'rule' => 'email',
                'message' => 'メールアドレスの形式が不正です。'
            ],
            'isUniqueChk' => [
                'rule' => 'isUniqueChk',
                'message' => '既に登録されているアドレスです。'
            ],
        ],
        'new_password' => [
            'minLength' => [
                'rule' => ['between', 6, 12],
                'allowEmpty' => false,
                'message' => 'パスワードは６～１２文字の間で設定してください。'
            ],
            'checkLargeAlphabet' => [
                'rule' =>  '/[A-Z]/',//半角英大小文字、数字のみ
                'message' => 'パスワードは英大小文字、数字を含んで設定してください。'
            ],
            'checkSmallAlphabet' => [
                'rule' =>  '/[a-z]/',//半角英大小文字、数字のみ
                'message' => 'パスワードはパスワードは英大小文字、数字を含んで設定してください。'
            ],
            'checkNumber' => [
                'rule' =>  '/[0-9]/',//半角英大小文字、数字のみ
                'message' => 'パスワードは英大小文字、数字を含んで設定してください。'
            ],
            'checkOverlapMail' => [
              'rule' => 'notOverlapMail',
              'message' => 'メールアドレスを含めずに設定してください'
            ],
        ],
        'permission_level' => [
            'notBlank' => [
                'rule' => 'notBlank',
                'message' => '権限レベルを選択してください。'
            ],
        ],
        'sc_num' => [
          'range' => [
            'rule' => ['range', -1, 100],
            'allowEmpty' => false,
            'message' => '０～９９以内で設定してください。'
          ]
        ],
        'settings' => [
            'prohibitedCharacters' => [
                'rule' => '/^(?!.*(<|>|&|\')).*$/',
                'message' => '<,>,&,\'を含まずに設定してください。'
            ]
        ]
    ];

    public $updateValidate = [
        'user_name' => [
            'maxLength' => [
                'rule' => ['maxLength', 50],
                'allowEmpty' => false,
                'message' => 'ユーザー名は５０文字以内で設定してください。'
            ],
            'prohibitedCharacters' => [
                'rule' => '/^(?!.*(<|>|&|"|\')).*$/',
                'message' => '<,>,&.",\'を含まずに設定してください。'
            ]
        ],
        'display_name' => [
            'maxLength' => [
                'rule' => ['maxLength', 10],
                'allowEmpty' => false,
                'message' => '表示名は１０文字以内で設定してください。'
            ],
            'prohibitedCharacters' => [
                'rule' => '/^(?!.*(<|>|&|"|\')).*$/',
                'message' => '<,>,&.",\'を含まずに設定してください。'
            ]
        ],
        'mail_address' => [
            'email' => [
                'rule' => 'email',
                'message' => 'メールアドレスの形式が不正です。'
            ],
        ],
        'new_password' => [
            'minLength' => [
                'rule' => ['between', 6, 12],
                'allowEmpty' => false,
                'message' => 'パスワードは６～１２文字の間で設定してください。'
            ],
            'checkLargeAlphabet' => [
                'rule' =>  '/[A-Z]/',//半角英大小文字、数字のみ
                'message' => 'パスワードは英大小文字、数字を含んで設定してください。'
            ],
            'checkSmallAlphabet' => [
                'rule' =>  '/[a-z]/',//半角英大小文字、数字のみ
                'message' => 'パスワードはパスワードは英大小文字、数字を含んで設定してください。'
            ],
            'checkNumber' => [
                'rule' =>  '/[0-9]/',//半角英大小文字、数字のみ
                'message' => 'パスワードは英大小文字、数字を含んで設定してください。'
            ],
            'checkOverlapMail' => [
              'rule' => 'notOverlapMail',
              'message' => 'メールアドレスを含めずに設定してください'
            ],
        ],
        'permission_level' => [
            'notBlank' => [
                'rule' => 'notBlank',
                'message' => '権限レベルを選択してください。'
            ],
            'prohibitedCharacters' => [
                'rule' => '/^(?!.*(<|>|&|"|\')).*$/',
                'message' => '<,>,&.",\'を含まずに設定してください。'
            ]
        ],
        'current_password' => [
            'checkCurrentPw' => [
                'rule' => 'isCurrentPw',
                'allowEmpty' => false,
                'message' => '現在のパスワードが一致しません。'
            ],
        ],
        'confirm_password' => [
            'checkConfirmPw' => [
                'rule' => 'canMatchConfirmPw',
                'allowEmpty' => false,
                'message' => '新しいパスワードが一致しません。'
            ]
        ],
        'settings' => [
            'prohibitedCharacters' => [
                'rule' => '/^(?!.*(<|>|&|\')).*$/',
                'message' => '<,>,&,\'を含まずに設定してください。'
            ]
        ]
    ];

    public function isCurrentPw($currentPw){
        $data = $this->data['MUser'];
        if ( empty($currentPw['current_password']) ) return false;

        $params = [
            'fields' => '*',
            'conditions' => [
                'id' => $data['id'],
                'del_flg' => 0,
                'password' => $this->makePassword($currentPw['current_password'])
            ],
            'limit' => 1,
            'recursive' => -1
        ];
        $ret = $this->find('all', $params);
        if ( !empty($ret) ) {
            return true;
        }
        return false;
    }

    public function notOverlapMail(){
      $data = $this->data['MUser'];
      if ( !empty($data['new_password'])) {
        if(strpos($data['new_password'],(substr($data['mail_address'],0,strpos($data['mail_address'],'@')))) === false){
          return true;
        }
        return false;
      }
    }

    public function canMatchConfirmPw(){
        $data = $this->data['MUser'];
        if ( !empty($data['new_password']) && !empty($data['confirm_password']) ) {
            if ( strcmp($data['new_password'], $data['confirm_password']) === 0 ) {
                return true;
            }
        }
        return false;
    }

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'MCompany' => array(
            'className' => 'MCompany',
            'foreignKey' => 'm_companies_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    public function beforeSave($options = []) {
        if ( empty($this->data['MUser']) ) return true;
        $data = $this->data['MUser'];
        if ( !empty($data['new_password']) ) {
            $data['password'] = $this->makePassword($data['new_password']);
        }
        $this->data['MUser'] = $data;
        return true;
    }

    public function passwordHash($pass) {
      if ( !empty($pass) ) {
        $data = $this->makePassword($pass);
      }
      $password = $data;
      return $password;
    }

    public function makePassword($str){
      $passwordHasher = new SimplePasswordHasher();
      return $passwordHasher->hash($str);
    }


    public function isUniqueChk($str){
      $str[$this->name . '.del_flg'] = 0;
      if ( !empty($this->id) ) {
        $str[$this->name . '.id !='] = $this->id;
      }
      $ret = $this->find('all', ['fields' => $this->name . '.*', 'conditions' => $str, 'recursive' => -1]);
      if ( !empty($ret) ) {
          return false;
      }
      else {
          return true;
      }
    }

    /**
     * getUser ユーザーの情報を単一か複数取得する
     * @param $id int(default:null) nullの場合は所属ユーザーを全て取得
     * @return array 単一ユーザーの場合は 'first', 所属ユーザーの場合は 'all' の結果
     * */
    public function getUser($id = null){
      $conditions = [
        'fields' => [
          'id',
          'display_name',
          'settings'
        ],
        'conditions' => [
          'm_companies_id' => Configure::read('logged_company_id'),
          'permission_level != ' => C_AUTHORITY_SUPER,
          'del_flg' => 0
        ],
        'recursive' => -1
      ];

      if ( !empty($id) ) {
        $conditions['conditions']['id'] = $id;
        return $this->find('first', $conditions);
      }
      else {
        return $this->find('all', $conditions);
      }

    }


}
