<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/12/07
 * Time: 10:35
 */

class MMailTemplate extends AppModel {

  public $name = 'MMailTemplate';

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