<?php
App::uses('AppModel', 'Model');
/**
 * TCustomVariables Model
 *
*/
class TCustomVariables extends AppModel {

	public $name = 'TCustomVariables';

	public $validate = [
			'keyword' => [
					'rule' => ['maxLength', 100],
					'allowEmpty' => false,
					'message' => 'キーワードは１００文字以内で設定してください。'
			],
			'name' => [
					'rule' => ['maxLength', 100],
					'allowEmpty' => false,
					'message' => '通知名は１００文字以内で設定してください。'
			]
	];

}
