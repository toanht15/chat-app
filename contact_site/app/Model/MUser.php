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
	public $validate = array(
		'user_name' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 50),
				'allowEmpty' => false,
				'message' => 'ユーザー名は５０文字以内で設定してください。'
			)
		),
		'display_name' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 10),
				'allowEmpty' => false,
				'message' => '表示名は１０文字以内で設定してください。'
			)
		),
		'mail_address' => array(
			'email' => array(
				'rule' => 'email',
				'message' => 'メールアドレスの形式が不正です。'
			)
		),
		'new_password' => array(
			'minLength' => array(
				'rule' => array('between', 6, 12),
				'allowEmpty' => false,
				'message' => 'パスワードは６～１２文字の間で設定してください。'
			),
			'alphaNumeric' => array(
				'rule' => 'alphaNumeric',
				'message' => 'パスワードは英数字で設定してください。'
			)
		),
		'permission_level' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'message' => '権限レベルを選択してください。'
			)
		),
		// 'created' => array(
		// 	'datetime' => array(
		// 		'rule' => array('datetime')
		// 	)
		// ),
		// 'created_user_id' => array(
		// 	'numeric' => array(
		// 		'rule' => array('numeric')
		// 	)
		// ),
		// 'modified' => array(
		// 	'datetime' => array(
		// 		'rule' => array('datetime')
		// 	)
		// ),
		// 'modified_user_id' => array(
		// 	'numeric' => array(
		// 		'rule' => array('numeric')
		// 	)
		// )
	);

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

	public function makePassword($str){
		$passwordHasher = new SimplePasswordHasher();
		return $passwordHasher->hash($str);
	}
}
