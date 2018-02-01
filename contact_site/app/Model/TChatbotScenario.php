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
        'message' => 'シナリオを設定してください'
      ]
    ],
    'action' => [
      'notBlank' => [
        'rule' => 'notBlank',
        'allowEmpty' => false,
        'message' => 'メッセージ間隔を入力してください'
      ]
    ]
  ];

  /**
   * シナリオのバリデーションチェック
   * @param object $json アクション情報
   * @return boolean チェック結果
   */
  public function checkActivity($json){
    // TODO: 追加・更新時のバリデーションチェックを追加する
    // $activity = json_decode($json['activity'], true);
    return true;
  }
}
