<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/08/09
 * Time: 10:07
 */
$config['default'] = [
	'autoMessages_with_scenario' => [
		[
			'name' => '【サンプル】初回訪問3秒後',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"1" => [
						[
							'stayTimeCheckType' => '2',
							'stayTimeType' => '1',
							'stayTimeRange' => '3'
						]
					],
					"2" => [
						[
							'visitCnt' => '1',
							'visitCntCond' => '1'
						]
					]
				],
				'widgetOpen' => 1,
				'message' => 'ご訪問誠にありがとうございます。',
				"chatTextarea" => 1,
				"cv" => 2
			],
			'action_type' => 1,
			'active_type' => 0,
			'sort' => 1
		],
		[
			'name' => '【サンプル】2回目以降訪問3秒後',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"1" => [
						[
							'stayTimeCheckType' => '2',
							'stayTimeType' => '1',
							'stayTimeRange' => '3'
						]
					],
					"2" => [
						[
							'visitCnt' => '2',
							'visitCntCond' => '2'
						]
					]
				],
				'widgetOpen' => 1,
				'message' => '再訪問ありがとうございます。',
				"chatTextarea" => 1,
				"cv" => 2
			],
			'action_type' => 1,
			'active_type' => 0,
			'sort' => 2
		],
		[
			'name' => '【サンプル】ページ滞在5秒後',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"1" => [
						[
							'stayTimeCheckType' => '1',
							'stayTimeType' => '1',
							'stayTimeRange' => '5'
						]
					]
				],
				'widgetOpen' => 1,
				'message' => 'なにかお困りのことはございませんか？
[] sinclo（シンクロ）について
[] ○○について
[] 無料トライアルに申し込む
[] 資料請求
[] 来店予約
[] 会員登録・入会
[] アンケートに答える
[] その他',
				"chatTextarea" => 1,
				"cv" => 2
			],
			'action_type' => 1,
			'active_type' => 0,
			'sort' => 3
		],
		[
			'name' => '【サンプル】（発言内容）sinclo（シンクロ）について',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"7" => [
						[
							"keyword_contains" => "sinclo（シンクロ）について",
							"keyword_contains_type" => "1",
							"keyword_exclusions" => "",
							"keyword_exclusions_type" => "1",
							"speechContentCond" => "1",
							"triggerTimeSec" => 2,
							"speechTriggerCond" => "2"
						]
					]
				],
				'widgetOpen' => 1,
				"message" => "sinclo（シンクロ）はコンタクトセンターシステムメーカーであるメディアリンクが長年培った技術力とノウハウを活かした100%自社開発（国産）のチャットボットツール（特許取得済み）です。
<img src=\"https://sinclo.medialink-ml.co.jp/lp/images/index/features_photo01.jpg\" alt=\"sinclo（シンクロ）\" style=\"display:block;margin-left:auto;margin-right:auto;width:250px;height:auto;margin-top:10px;margin-bottom:10px\">
「売上にインパクトを与えるコミュニケーションのあり方」を熟知している当社だからこそ、本当に効果のあるチャットボットツールを自信をもってご提供いたします。
--------------------------------------------------
[] 無料トライアルに申し込む
[] 資料請求
--------------------------------------------------
[] メニューに戻る",
				"chatTextarea" => 2,
				"cv" => 2
			],
			'action_type' => 1,
			'active_type' => 0,
			'sort' => 4
		],
		[
			'name' => '【サンプル】（発言内容）○○について',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"7" => [
						[
							"keyword_contains" => "○○について",
							"keyword_contains_type" => "1",
							"keyword_exclusions" => "",
							"keyword_exclusions_type" => "1",
							"speechContentCond" => "1",
							"triggerTimeSec" => 2,
							"speechTriggerCond" => "2"
						]
					]
				],
				'widgetOpen' => 1,
				"message" => "○○についてですね。
○○は・・・

--------------------------------------------------
[] メニューに戻る",
				"chatTextarea" => 2,
				"cv" => 2
			],
			'action_type' => 1,
			'active_type' => 0,
			'sort' => 5
		],
		[
			'name' => '【サンプル】（発言内容）資料請求',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"7" => [
						[
							"keyword_contains" => "資料請求",
							"keyword_contains_type" => "1",
							"keyword_exclusions" => "",
							"keyword_exclusions_type" => "1",
							"speechContentCond" => "1",
							"triggerTimeSec" => 2,
							"speechTriggerCond" => "2"
						]
					]
				],
				"widgetOpen" => 1,
				"message" => "",
				"chatTextarea" => 1,
				"cv" => 2
			],
			'action_type' => 2,
			't_chatbot_scenario_id' => 0,
			'active_type' => 0,
			'sort' => 7
		],
		[
			'name' => '【サンプル】（発言内容）その他',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"7" => [
						[
							"keyword_contains" => "その他",
							"keyword_contains_type" => "1",
							"keyword_exclusions" => "",
							"keyword_exclusions_type" => "1",
							"speechContentCond" => "1",
							"triggerTimeSec" => 2,
							"speechTriggerCond" => "2"
						]
					]
				],
				"widgetOpen" => 1,
				"message" => "",
				"chatTextarea" => 1,
				"cv" => 2
			],
			'action_type' => 2,
			't_chatbot_scenario_id' => 0,
			'active_type' => 0,
			'sort' => 11
		],
		[
			'name' => '【サンプル】（発言内容）メニューに戻る',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"7" => [
						[
							"keyword_contains" => "メニューに戻る",
							"keyword_contains_type" => "1",
							"keyword_exclusions" => "",
							"keyword_exclusions_type" => "1",
							"speechContentCond" => "1",
							"triggerTimeSec" => 2,
							"speechTriggerCond" => "2"
						]
					]
				],
				'widgetOpen' => 1,
				'message' => 'なにかお困りのことはございませんか？
[] sinclo（シンクロ）について
[] ○○について
[] 無料トライアルに申し込む
[] 資料請求
[] 来店予約
[] 会員登録・入会
[] アンケートに答える
[] その他',
				"chatTextarea" => 1,
				"cv" => 2
			],
			'action_type' => 1,
			'active_type' => 0,
			'sort' => 12
		],
		[
			'name' => '【サンプル】（発言内容）会員登録・入会',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"7" => [
						[
							"keyword_contains" => "会員登録・入会",
							"keyword_contains_type" => "1",
							"keyword_exclusions" => "",
							"keyword_exclusions_type" => "1",
							"speechContentCond" => "1",
							"triggerTimeSec" => 2,
							"speechTriggerCond" => "2"
						]
					]
				],
				"widgetOpen" => 2,
				"message" => "",
				"chatTextarea" => 1,
				"cv" => 2
			],
			'action_type' => 2,
			't_chatbot_scenario_id' => 0,
			'active_type' => 0,
			'sort' => 9
		],
		[
			'name' => '【サンプル】（発言内容）アンケートに答える',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"7" => [
						[
							"keyword_contains" => "アンケートに答える",
							"keyword_contains_type" => "1",
							"keyword_exclusions" => "",
							"keyword_exclusions_type" => "1",
							"speechContentCond" => "1",
							"triggerTimeSec" => 2,
							"speechTriggerCond" => "2"
						]
					]
				],
				"widgetOpen" => 2,
				"message" => "",
				"chatTextarea" => 1,
				"cv" => 2
			],
			'action_type' => 2,
			't_chatbot_scenario_id' => 0,
			'active_type' => 0,
			'sort' => 10
		],
		[
			'name' => '【サンプル】（発言内容）無料トライアルに申し込む',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"7" => [
						[
							"keyword_contains" => "無料トライアルに申し込む",
							"keyword_contains_type" => "1",
							"keyword_exclusions" => "",
							"keyword_exclusions_type" => "1",
							"speechContentCond" => "1",
							"triggerTimeSec" => 2,
							"speechTriggerCond" => "2"
						]
					]
				],
				"widgetOpen" => 2,
				"message" => "無料トライアルをご希望ですね。
下記ページからお申し込みください。

<a href=\"https://sinclo.medialink-ml.co.jp/lp/trial.php\" target=\"_blank\"style=\"display: inline-block;width:290px;text-align:center;font-weight: bold;text-decoration: none;background: #ABCD05;color:#FFFFFF;padding:10px;border-radius: 5px;\">無料トライアル申し込み</a>",
				"chatTextarea" => 2,
				"cv" => 2
			],
			'action_type' => 1,
			'active_type' => 0,
			'sort' => 6
		],
		[
			'name' => '【サンプル】（発言内容）来店予約',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"7" => [
						[
							"keyword_contains" => "来店予約",
							"keyword_contains_type" => "1",
							"keyword_exclusions" => "",
							"keyword_exclusions_type" => "1",
							"speechContentCond" => "1",
							"triggerTimeSec" => 2,
							"speechTriggerCond" => "2"
						]
					]
				],
				"widgetOpen" => 2,
				"message" => "",
				"chatTextarea" => 1,
				"cv" => 2
			],
			'action_type' => 2,
			't_chatbot_scenario_id' => 0,
			'active_type' => 0,
			'sort' => 8
		]
	],
	'autoMessages_without_scenario' => [
		[
			'name' => '初回訪問3秒後',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"1" => [
						[
							'stayTimeCheckType' => '2',
							'stayTimeType' => '1',
							'stayTimeRange' => '3'
						]
					],
					"2" => [
						[
							'visitCnt' => '1',
							'visitCntCond' => '1'
						]
					]
				],
				'widgetOpen' => 1,
				'message' => 'ご訪問誠にありがとうございます。',
				"chatTextarea" => 2,
				"cv" => 2
			],
			'action_type' => 1,
			'active_type' => 0,
			'sort' => 1
		],
		[
			'name' => '2回目以降訪問3秒後',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"1" => [
						[
							'stayTimeCheckType' => '2',
							'stayTimeType' => '1',
							'stayTimeRange' => '3'
						]
					],
					"2" => [
						[
							'visitCnt' => '2',
							'visitCntCond' => '2'
						]
					]
				],
				'widgetOpen' => 1,
				'message' => '再訪問ありがとうございます。',
				"chatTextarea" => 2,
				"cv" => 2
			],
			'action_type' => 1,
			'active_type' => 0,
			'sort' => 2
		],
		[
			'name' => 'ページ滞在10秒後',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"1" => [
						[
							'stayTimeCheckType' => '1',
							'stayTimeType' => '1',
							'stayTimeRange' => '10'
						]
					]
				],
				'widgetOpen' => 1,
				'message' => 'なにかお困りのことはございませんか？
[] Aについて
[] Bについて
[] 資料請求
[] その他',
				"chatTextarea" => 1,
				"cv" => 2
			],
			'action_type' => 1,
			'active_type' => 0,
			'sort' => 3
		],
		[
			'name' => '発言内容（Aについて）',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"7" => [
						[
							"keyword_contains" => "Aについて",
							"keyword_contains_type" => "1",
							"keyword_exclusions" => "",
							"keyword_exclusions_type" => "1",
							"speechContentCond" => "1",
							"triggerTimeSec" => 2,
							"speechTriggerCond" => "2"
						]
					]
				],
				'widgetOpen' => 1,
				"message" => "Aについてですね。
Aは・・・

--------------------------------------------------
[] メニューに戻る",
				"chatTextarea" => 2,
				"cv" => 2
			],
			'action_type' => 1,
			'active_type' => 0,
			'sort' => 4
		],
		[
			'name' => '発言内容（Bについて）',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"7" => [
						[
							"keyword_contains" => "Bについて",
							"keyword_contains_type" => "1",
							"keyword_exclusions" => "",
							"keyword_exclusions_type" => "1",
							"speechContentCond" => "1",
							"triggerTimeSec" => 2,
							"speechTriggerCond" => "2"
						]
					]
				],
				'widgetOpen' => 1,
				"message" => "Bについてですね。
Bは・・・

--------------------------------------------------
[] メニューに戻る",
				"chatTextarea" => 2,
				"cv" => 2
			],
			'action_type' => 1,
			'active_type' => 0,
			'sort' => 5
		],
		[
			'name' => '発言内容（資料請求）',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"7" => [
						[
							"keyword_contains" => "資料請求",
							"keyword_contains_type" => "1",
							"keyword_exclusions" => "",
							"keyword_exclusions_type" => "1",
							"speechContentCond" => "1",
							"triggerTimeSec" => 2,
							"speechTriggerCond" => "2"
						]
					]
				],
				"widgetOpen" => 1,
				"message" => "資料請求ですね。
それではこちらに会社名とお名前、メールアドレスを入力して下さい。

※普段お使いのメール署名をコピー＆ペーストして頂く形で構いません",
				"chatTextarea" => 1,
				"cv" => 2
			],
			'action_type' => 1,
			't_chatbot_scenario_id' => 0,
			'active_type' => 0,
			'sort' => 6
		],
		[
			'name' => '発言内容（その他）',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"7" => [
						[
							"keyword_contains" => "その他",
							"keyword_contains_type" => "1",
							"keyword_exclusions" => "",
							"keyword_exclusions_type" => "1",
							"speechContentCond" => "1",
							"triggerTimeSec" => 2,
							"speechTriggerCond" => "2"
						]
					]
				],
				"widgetOpen" => 1,
				"message" => "その他のお問い合わせですね。
どのようなお問い合わせでしょうか？",
				"chatTextarea" => 1,
				"cv" => 2
			],
			'action_type' => 1,
			'active_type' => 0,
			'sort' => 7
		],
		[
			'name' => '発言内容（メニューに戻る）',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"7" => [
						[
							"keyword_contains" => "メニューに戻る",
							"keyword_contains_type" => "1",
							"keyword_exclusions" => "",
							"keyword_exclusions_type" => "1",
							"speechContentCond" => "1",
							"triggerTimeSec" => 2,
							"speechTriggerCond" => "2"
						]
					]
				],
				'widgetOpen' => 1,
				"message" => "なにかお困りのことはございませんか？
[] Aについて
[] Bについて
[] 資料請求
[] その他",
				"chatTextarea" => 1,
				"cv" => 2
			],
			'action_type' => 1,
			'active_type' => 0,
			'sort' => 8
		],
		[
			'name' => '発言内容（はい、すべて入力しました。）',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"7" => [
						[
							"keyword_contains" => "はい、すべて入力しました。",
							"keyword_contains_type" => "1",
							"keyword_exclusions" => "",
							"keyword_exclusions_type" => "1",
							"speechContentCond" => "1",
							"triggerTimeSec" => 2,
							"speechTriggerCond" => "2"
						]
					]
				],
				'widgetOpen' => 1,
				"message" => "ありがとうございます。

担当の者から改めてご連絡させていただきます。

この度はお問い合わせ誠にありがとうございました！

--------------------------------------------------
[] メニューに戻る",
				"chatTextarea" => 2,
				"cv" => 1
			],
			'action_type' => 1,
			'active_type' => 0,
			'sort' => 9
		],
		[
			'name' => '発言内容（いいえ、入力していません。）',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"7" => [
						[
							"keyword_contains" => "いいえ、入力していません。",
							"keyword_contains_type" => "1",
							"keyword_exclusions" => "",
							"keyword_exclusions_type" => "1",
							"speechContentCond" => "1",
							"triggerTimeSec" => 2,
							"speechTriggerCond" => "2"
						]
					]
				],
				'widgetOpen' => 1,
				"message" => "こちらからの連絡を希望しますか？

[] はい、連絡を希望します。
[] いいえ、結構です。",
				"chatTextarea" => 2,
				"cv" => 2
			],
			'action_type' => 1,
			'active_type' => 0,
			'sort' => 10
		],
		[
			'name' => '発言内容（はい、連絡を希望します。）',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"7" => [
						[
							"keyword_contains" => "はい、連絡を希望します。",
							"keyword_contains_type" => "1",
							"keyword_exclusions" => "",
							"keyword_exclusions_type" => "1",
							"speechContentCond" => "1",
							"triggerTimeSec" => 2,
							"speechTriggerCond" => "2"
						]
					]
				],
				'widgetOpen' => 1,
				"message" => "それではこちらに、
・会社名（必須）
・お名前（必須）
・メールアドレス（必須）
・電話番号（任意）
を記入してください。

後ほど担当の者からご連絡させていただきます。",
				"chatTextarea" => 1,
				"cv" => 2
			],
			'action_type' => 1,
			'active_type' => 0,
			'sort' => 11
		],
		[
			'name' => '発言内容（いいえ、結構です。）',
			'trigger_type' => 0,
			'activity' => [
				'conditionType' => 1,
				'conditions' => [
					"7" => [
						[
							"keyword_contains" => "いいえ、結構です。",
							"keyword_contains_type" => "1",
							"keyword_exclusions" => "",
							"keyword_exclusions_type" => "1",
							"speechContentCond" => "1",
							"triggerTimeSec" => 2,
							"speechTriggerCond" => "2"
						]
					]
				],
				'widgetOpen' => 1,
				"message" => "承知いたしました。

それでは●時～●時の間に再度お問い合わせ頂くか、下記フォームからお問い合わせください。
●フォームのURLを記載●

--------------------------------------------------
[] メニューに戻る",
				"chatTextarea" => 2,
				"cv" => 2
			],
			'action_type' => 1,
			'active_type' => 0,
			'sort' => 12
		],
	]
];
