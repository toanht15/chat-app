<?php
App::uses('AppModel', 'Model');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
/**
 * AgreementList Model
 *
 */
class MAgreementList extends AppModel {

  public $name = 'MAgreementList';
  //アソシエーション
  public $belongsTo = ['MCompany' =>
    ['className' => 'Mcompany',
      'conditions' => '',
      'order' => '',
      'dependent' => true,
      'foreignKey' => 'm_companies_id'
    ]
  ];

  /**
  * Validation rules
  *
  * @var array
  */
  //バリデーション
  public $validate = [
    'admin_password' => [
      'minLength' => [
        'rule' => ['between', 6, 12],
        'allowEmpty' => false,
        'message' => 'パスワードは６～１２文字の間で設定してください。'
      ],
      'alphaNumeric' => [
        'rule' => 'alphaNumeric',
        'message' => 'パスワードは半角英数字で設定してください。'
      ]
    ],
  ];

  public $updateValidate = [
    'admin_password' => [
      'minLength' => [
        'rule' => ['between', 6, 12],
        'allowEmpty' => false,
        'message' => 'パスワードは６～１２文字の間で設定してください。'
      ],
      'alphaNumeric' => [
        'rule' => 'alphaNumeric',
        'message' => 'パスワードは半角英数字で設定してください。'
      ]
    ],
  ];
}