<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/12/07
 * Time: 10:35
 */

class MMailTransmissionSetting extends AppModel {

  public $name = 'MMailTransmissionSetting';

  /**
   * Validation rules
   *
   * @var array
   */
  public $validate = [
    'to_address' => [
      'validEmails' => [
        'rule' => 'isValidAllEmails',
        'message' => 'メールアドレスではない設定が含まれています。'
      ],
      'isNOTDuplicateEmails' => [
          'rule' => 'isNOTDuplicateEmails',
          'message' => '同一のメールアドレスは設定できません。'
      ]
    ],
    'subject' => [
      'maxLength' => [
        'rule' => ['maxLength', 100],
        'allowEmpty' => false,
        'message' => 'メールタイトルは１００文字以内で設定してください。'
      ],
      'prohibitedCharacters' => [
        'rule' => '/^(?!.*(<|>|&|"|\')).*$/',
        'message' => '<,>,&.",\'を含まずに設定してください。'
      ]
    ],
    'from_name' => [
      'maxLength' => [
        'rule' => ['maxLength', 100],
        'allowEmpty' => false,
        'message' => '差出人名は１００文字以内で設定してください。'
      ],
      'prohibitedCharacters' => [
        'rule' => '/^(?!.*(<|>|&|"|\')).*$/',
        'message' => '<,>,&.",\'を含まずに設定してください。'
      ]
    ]
  ];

  public function isValidAllEmails($toAddresses){
    $valid = true;
    foreach($toAddresses as $toAddress) {
      $explode = explode(',', $toAddress);
      foreach($explode as $mailAddress) {
        $valid = Validation::email($mailAddress);
        $match = preg_match('/^{{.+}}$/', $mailAddress);  // 変数かチェック
        if(!$valid && !$match) break;
      }
    }
    return $valid || $match;
  }

  public function isNOTDuplicateEmails($toAddresses) {
    $result = true;
    $array = [];
    foreach($toAddresses as $toAddress) {
      $explode = explode(',', $toAddress);
      foreach($explode as $mailAddress) {
        if($result) {
          if(count($array) !== 0) {
            $result = !array_key_exists($mailAddress, $array);
          }
          if(!$result) break;
          $array[$mailAddress] = "";
        }
      }
    }
    return $result;
  }
 }
