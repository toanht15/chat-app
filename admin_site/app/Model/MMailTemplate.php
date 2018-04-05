<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/12/07
 * Time: 10:35
 */

class MMailTemplate extends AppModel {

  public $name = 'MMailTemplate';

  /**
   * Validation rules
   *
   * @var array
   */
  public $validate = [
    'template' => [
      'notBlank' => [
        'rule' => 'notBlank',
        'allowEmpty' => false,
        'message' => 'テンプレートを設定してください'
      ]
    ]
  ];
}
