<?php
App::uses('AppModel', 'Model');
/**
 * TResetPasswordInformation
 * PWDリマインダー管理テーブル
 *
 */
class TResetPasswordInformation extends AppModel {

  public $name = "TResetPasswordInformation";

  public $validate = [
    'mail_address' => [
      'notBlank' => [
        'rule' => 'notBlank',
        'message' => 'メールアドレスを入力してください'
      ],
      'email' => [
        'rule' => 'email',
        'message' => 'メールアドレスの形式が不正です'
      ]
    ]
  ];
}