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
      ],
      'isActiveNameUnique' => [
        'rule' => 'isActiveNameUnique',
        'message' => '既に同じ名称が有効設定に存在します。'
      ]
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
      if (!is_string($action['actionType'])) {
        return false;
      }

      if ($action['actionType'] == C_SCENARIO_ACTION_TEXT) {
        // テキスト入力
        if (empty($action['message'])) {
          return false;
        }
      } else
      if ($action['actionType'] == C_SCENARIO_ACTION_HEARING) {
        // ヒアリング
        foreach ($action['hearings'] as $key => $item) {
          if ($item['uiType'] === '1' || $item['uiType'] === '2') {
            if (empty($item['variableName']) || empty($item['message'])) {
              return false;
            }
          } else {
            if (empty($item['variableName'])) {
              return false;
            }
          }
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
      } else
      if ($action['actionType'] == C_SCENARIO_ACTION_CALL_SCENARIO) {
        // シナリオ呼び出し
        if (empty($action['tChatbotScenarioId'])) {
          return false;
        }
      } else
      if ($action['actionType'] == C_SCENARIO_ACTION_EXTERNAL_API) {
        // 外部システム連携
        if ($action['externalType'] == C_SCENARIO_EXTERNAL_TYPE_API && empty($action['tExternalApiConnectionId'])) {
          return false;
        } else
        if ($action['externalType'] == C_SCENARIO_EXTERNAL_TYPE_SCRIPT && empty($action['externalScript'])) {
          return false;
        }
      }
    }

    return true;
  }

  public function isActiveNameUnique($param) {
    $validations = array(
      'conditions' => array(
        'name' => $param['name'],
        'm_companies_id' => $this->data['TChatbotScenario']['m_companies_id'],
        'del_flg' => 0
      )
    );

    if($this->data['TChatbotScenario']['id']) {
      $validations['conditions']['NOT'] = array();
      $validations['conditions']['NOT']['id'] = $this->data['TChatbotScenario']['id'];
    }

    $count = $this->find('count', $validations);

    return $count === 0 ? true : false;
  }
}
