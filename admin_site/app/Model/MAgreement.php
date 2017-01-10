<?php
App::uses('AppModel', 'Model');
/**
 * AgreementList Model
 *
 */
class MAgreement extends AppModel {

  public $name = 'MAgreement';
  //アソシエーション
  public $belongsTo = ['MCompany' =>
    ['className' => 'M_company',
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
  public $validate = [
    'application_day' => [
      'rule' => ['date', 'ymd'],
      'message' => '日付を設定してください'
    ],
    'agreement_start_day' => [
      'rule' => ['date', 'ymd'],
      'message' => '日付を設定してください'
    ],
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