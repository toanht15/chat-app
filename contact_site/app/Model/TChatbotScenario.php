<?php
App::uses('AppModel', 'Model');
/**
 * TChatbotScenario Model
 *
 * @property MCompanies $MCompanies
 */
class TChatbotScenario extends AppModel {

  public $name = "TChatbotScenario";

  public $validate = [
    'name' => [
      'maxLength' => [
        'rule' => ['maxLength', 50],
        'allowEmpty' => false,
        'message' => '名称を５０文字以内で入力してください'
      ]
    ],
    'activity' => [
      'checkActivity' => [
        'rule' => 'checkActivity',
        'allowEmpty' => false,
        'message' => 'アクションを設定してください'
      ]
    ]
  ];

  /**
   * アクションのバリデーションチェック
   * @param object $json アクション情報
   * @return boolean チェック結果
   */
  public function checkActivity($json){
    // TODO: 追加・更新時のバリデーションチェックを追加する
    return true;
  }
}
