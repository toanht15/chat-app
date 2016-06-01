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
			]
		]
	];

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

					if (isset($item[$field]) && is_array($item[$field])) {
						if (!array_search(true, $item[$field], true)) {
							return false;
						}
					}
					elseif (!isset($item[$field])) {
						// 曜日・日時の開始/終了時間のチェック
						if ((strcmp($field, "startTime") === 0 || strcmp($field, "endTime") === 0)) {
							// 時間を使用しなければスルー
							if ( isset($item['timeSetting']) && strcmp($item['timeSetting'], C_SELECT_CAN_NOT) === 0 ) {
								continue;
							}
							return false;
						}
					}
					else {

						// 曜日・日時の開始/終了時間のチェック
						if ((strcmp($field, "startTime") === 0 || strcmp($field, "endTime") === 0)) {
							if ( !preg_match(C_MATCH_RULE_TIME, $item[$field]) ) {
								return false;
							}
						}

						// 値が未入力のものはエラー
						if ( strcmp($item[$field], "") === 0 ) {
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

}
