<?php
App::uses('AppModel', 'Model');
/**
 * TAutoMessage Model
 *
 * @property MCompanies $MCompanies
 */
class TAutoMessage extends AppModel {

  public $name = "TAutoMessage";

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
        'message' => '条件を設定してください'
      ]
    ],
    'action' => [
      'notBlank' => [
        'rule' => 'notBlank',
        'allowEmpty' => false,
        'message' => 'メッセージを入力してください'
      ],
      'maxLength' => [
        'rule' => ['maxLength', 4000],
        'allowEmpty' => false,
        'message' => 'メッセージは４０００文字以内で入力してください'
      ]
    ]
  ];

  /**
   * checkBeforeValidates
   * バリデーション実行前に、アクションタイプ別にルールを追加する
   * @param  String $actionType アクションタイプ
   */
  public function checkBeforeValidates($actionType, $bulkInsertMode = false) {
    if ($actionType == C_AUTO_ACTION_TYPE_SELECTSCENARIO) {
      $this->validator()->add('t_chatbot_scenario_id', 'checkScenario', array(
        'rule' => 'checkScenario',
        'required' => true,
        'message' => 'シナリオを選択してください'
      ));
      $this->validator()->remove('action');
    } else if($actionType == C_AUTO_ACTION_TYPE_CALL_AUTOMESSAGE && !$bulkInsertMode) {
      $this->validator()->add('call_automessage_id', 'checkCallAutoMessage', array(
        'rule' => 'checkCallAutoMessage',
        'required' => true,
        'message' => '呼出先を選択してください'
      ));
      $this->validator()->remove('action');
    } else if($actionType == C_AUTO_ACTION_TYPE_SELECTCHATDIAGRAM) {
      $this->validator()->add('t_chatbot_diagram_id', 'checkCallAutoMessage', array(
        'rule' => 'checkSelectChatDiagram',
        'required' => true,
        'message' => '呼出先を選択してください'
      ));
      $this->validator()->remove('action');
    }
  }

  public function checkActivity($json){
    $activity = json_decode($json['activity'], true);
    $type = (!empty($activity['conditionType'])) ? $activity['conditionType'] : "";
    $detail = (!empty($activity['conditions'])) ? $activity['conditions'] : "";

    // 条件が設定されていない場合
    if ( count($detail) === 0 ) return false;

    // 条件設定リストを取得
    $triggerList = Configure::read('outMessageTriggerList');
    $defaultList = [];

    // 条件ごと
    foreach( (array)$detail as $itemType => $items ){
      // 条件が見つからない場合
      if (!isset($triggerList[$itemType])) return false;
      // 初期条件が見つからない場合
      if (!isset($triggerList[$itemType]['default'])) return false;
      // 条件単位の設定が設定されていない場合
      if ( count($items) === 0 ) return false;
      // 条件設定リストをセット
      $defaultList = $triggerList[$itemType]['default'];
      // 条件単位の設定ごと
      foreach( (array)$items as $itemId => $item ){

        // 設定単位ごと
        foreach( (array)$defaultList as $field => $value ){

          // 値に配列が入っている
          if (isset($item[$field]) && is_array($item[$field])) {
            // 一つでもtrueが入っていればOK
            if (!array_search(true, $item[$field], true)) {
              return false;
            }
          }
          // キーが存在しない
          elseif (!isset($item[$field])) {
            // 曜日・日時の開始/終了時間のチェック
            if ((strcmp($field, "startTime") === 0 || strcmp($field, "endTime") === 0)) {
              // 時間を使用しなければスルー
              if ( isset($item['timeSetting']) && strcmp($item['timeSetting'], C_SELECT_CAN_NOT) === 0 ) {
                continue;
              }
            }

            if (strcmp($field, "visitCntMax") === 0) {
              continue;
            }
            return false;
          }
          // キーが存在し、配列以外の値が入っている
          else {

            // 曜日・日時の開始/終了時間のチェック
            if ((strcmp($field, "startTime") === 0 || strcmp($field, "endTime") === 0)) {
              if ( !preg_match(C_MATCH_RULE_TIME, $item[$field]) ) {
                return false;
              }
            }

            // 滞在時間の入力チェック
            if (strcmp($field, "stayTimeRange") === 0) {
              if ( !preg_match(C_MATCH_RULE_NUM_1, $item[$field]) ) {
                return false;
              }
            }
            // 訪問回数の入力チェック
            if (strcmp($field, "visitCnt") === 0) {
              if ( !preg_match(C_MATCH_RULE_NUM_2, $item[$field]) ) {
                return false;
              }
            }

            // 発言内容 => 自動返信までの間隔 の入力チェック
            if (strcmp($field, "triggerTimeSec") === 0) {
              if ( !preg_match(C_MATCH_RULE_NUM_3, $item[$field]) ) {
                return false;
              }
            }

            if (strcmp($field, "keyword_contains") === 0 || strcmp($field, "keyword_exclusions") === 0) {
              if(empty($item["keyword_contains"]) && empty($item["keyword_exclusions"])) {
                return false;
              } else {
                continue;
              }
            }

            // 値が未入力のものはエラー
            if ( gettype($item[$field]) == 'string ' && strcmp($item[$field], "") === 0 ) {
              return false;
            }
          }

        } // 設定単位ごと

      } // 条件単位の設定ごと
    } // 条件ごと

    // 条件が一つも入っていなかった場合
    if ( empty($defaultList) ) {
      return false;
    }
    return true;


  }

  /**
   * action_typeの設定状態を確認し、メッセージのバリデーションを行う
   * @param  Array $param actionのパラメーター
   * @return Boolean バリデーション結果
   */
  public function checkAction($param) {
    if ($this->data['TAutoMessage']['action_type'] != C_AUTO_ACTION_TYPE_SENDMESSAGE) {
      return true;
    }

    return !empty($param['action']);
  }

  /**
   * checkScenario
   * action_typeの設定状態を確認し、シナリオ設定のバリデーションを行う
   *
   * @param Array $param t_chatbot_scenario_idのパラメーター
   * @return Boolean バリデーション結果
   */
  public function checkScenario($param) {
    if ($this->data['TAutoMessage']['action_type'] != C_AUTO_ACTION_TYPE_SELECTSCENARIO) {
      return true;
    }
    return !empty($param['t_chatbot_scenario_id']);
  }

  /**
   * checkCallAutoMessage
   * action_typeの設定状態を確認し、オートメッセージ呼出のバリデーションを行う
   *
   * @param Array $param t_chatbot_scenario_idのパラメーター
   * @return Boolean バリデーション結果
   */
  public function checkCallAutoMessage($param) {
    if ($this->data['TAutoMessage']['action_type'] != C_AUTO_ACTION_TYPE_CALL_AUTOMESSAGE) {
      return true;
    }
    return !empty($param['call_automessage_id']);
  }

  public function checkSelectChatDiagram($param) {
    if ($this->data['TAutoMessage']['action_type'] != C_AUTO_ACTION_TYPE_SELECTCHATDIAGRAM) {
      return true;
    }
    return !empty($param['t_chatbot_diagram_id']);
  }

  public function isActiveNameUnique($param) {
    if(strcmp($this->data['TAutoMessage']['active_flg'], C_STATUS_UNAVAILABLE) === 0) {
      // 無効状態であればOK
      return true;
    }

    $validations = array(
      'conditions' => array(
        'name' => $param['name'],
        'm_companies_id' => $this->data['TAutoMessage']['m_companies_id'],
        'active_flg' => 0,
        'del_flg' => 0
      )
    );

    if($this->data['TAutoMessage']['id']) {
      $validations['conditions']['NOT'] = array();
      $validations['conditions']['NOT']['id'] = $this->data['TAutoMessage']['id'];
    }

    $count = $this->find('count', $validations);

    return $count === 0 ? true : false;
  }
}
