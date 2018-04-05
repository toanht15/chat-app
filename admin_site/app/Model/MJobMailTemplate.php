<?php
/**
 * MAdministratorsController controller.
 * ユーザーマスタ
 */

App::uses('AppModel', 'Model');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
class MJobMailTemplate extends AppModel {
  public $name = "MJobMailTemplate";

  /**
  * Validation rules
  *
  * @var array
  */
  //登録処理の場合
  public $validate = [
    'subject' => [
        'rule' => 'notEmpty',
        'required' => true,
        'message' => 'メールタイトルが未入力です',
    ],
    'mail_body' => [
        'rule' => 'notEmpty',
        'required' => true,
        'message' => 'メール本文が未入力です',
    ],
  ];
}
?>