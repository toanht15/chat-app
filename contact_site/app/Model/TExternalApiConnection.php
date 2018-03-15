<?php
App::uses('AppModel', 'Model');
/**
 * TChatbotScenario Model
 *
 * @property MCompanies $MCompanies
 */
class TExternalApiConnection extends AppModel {

  public $name = "TExternalApiConnection";

  public $validate = [
    'url' => [
      'rule' => ['maxLength', 200],
      'allowEmpty' => false,
      'message' => '連携先URLを２００文字以内で入力してください'
    ],
    'response_body_maps' => [
      'checkResponseBody' => [
        'rule' => 'checkResponseBody',
        'allowEmpty' => false,
        'message' => 'レスポンスボディを設定してください'
      ]
    ]
  ];

  /**
   * レスポンスボディのバリデーションチェック
   * @param object $json 設定情報
   * @return boolean チェック結果
   */
  public function checkResponseBody($json){
    $maps = json_decode($json['response_body_maps'], true);
    if (empty($maps) || count($maps) === 0) {
      return false;
    }

    foreach ($maps as $key => $item) {
      if (empty($item['soucrKey']) || empty($item['variableName'])) {
        return false;
      }
    }

    return true;
  }
}
