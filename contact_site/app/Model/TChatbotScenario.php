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
      'rule' => ['maxLength', 50],
      'allowEmpty' => false,
      'message' => '名称を５０文字以内で入力してください'
    ],
    'activity' => [
      'checkActivity' => [
        'rule' => 'checkActivity',
        'allowEmpty' => false,
        'message' => 'シナリオを設定してください'
      ]
    ],
    'messageIntervalTimeSec' => [
      'notBlank' => [
        'rule' => 'notBlank',
        'message' => 'メッセージ間隔を入力してください',
      ],
      'alphaNumeric' => [
        'rule' => 'numeric',
        'message' => '数字で入力してください'
      ]
    ]
  ];

  /**
   * シナリオのバリデーションチェック
   * @param object $json アクション情報
   * @return boolean チェック結果
   */
  public function checkActivity($json){
    $activity = json_decode($json['activity'], true);
    if (empty($activity['chatbotType'])) {
      return false;
    }

    if (count($activity['scenarios']) === 0) {
      return false;
    }
    foreach ($activity['scenarios'] as $key => $action) {
      if ($action['actionType'] == C_SCENARIO_ACTION_TEXT) {
        // テキスト入力
        if (empty($action['message'])) {
          return false;
        }
      } else
      if ($action['actionType'] == C_SCENARIO_ACTION_HEARING) {
        // ヒアリング
        foreach ($action['hearings'] as $key => $item) {
          if (empty($item['variableName']) || empty($item['message'])) {
            return false;
          }
        }
        if (empty($action['errorMessage'])) {
          return false;
        }
        if ($action['isConfirm'] == 1 && (empty($action['confirmMessage'] || empty($action['succes'] || empty($action['cancel']))))) {
          return false;
        }
      } else
      if ($action['actionType'] == C_SCENARIO_ACTION_SELECT_OPTION) {
        // 選択肢
        foreach ($action['selection']['options'] as $key => $item) {
          if (empty($item)) {
            return false;
          }
        }
      } else
      if ($action['actionType'] == C_SCENARIO_ACTION_SEND_MAIL) {
        // メール送信
        if (empty($action['mailType'])) {
          return false;
        }
      }
    }

    return true;
  }
}
