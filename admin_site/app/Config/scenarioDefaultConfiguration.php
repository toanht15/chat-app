<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/03/25
 * Time: 0:28
 */

$config['default'] = array(
  'scenario' => array(
    1 => array(
      'name' => '【サンプル】資料請求（個別ヒアリング）',
      'activity' => array(
        "chatbotType" => "1",
        "scenarios" => array(
					"0" => array(
						"chatTextArea" => "2",
						"actionType" => "1",
						"messageIntervalTimeSec" => "2",
						"message" => "資料請求ですね。",
						"restore" => false
					),
					"1" => array(
						"chatTextArea" => "1",
						"actionType" => "2",
						"messageIntervalTimeSec" => "2",
						"hearings" => array(
							array(
								"variableName" => "会社名",
								"inputType" => "1",
								"uiType" => "1",
								"message" => "会社名を入力して下さい。",
								"required" => true,
								"errorMessage" => "",
								"inputLFType" => 2,
								"canRestore" => true
							),
							array(
								"variableName" => "名前",
								"inputType" => "1",
								"uiType" => "1",
								"message" => "お名前を入力して下さい。",
								"required" => true,
								"errorMessage" => "",
								"inputLFType" => 2,
								"canRestore" => true
							),
							array(
								"variableName" => "電話番号",
								"inputType" => "4",
								"uiType" => "1",
								"message" => "電話番号を入力して下さい。",
								"required" => true,
								"errorMessage" => "入力が正しく確認できませんでした。",
								"inputLFType" => 2,
								"canRestore" => true
							),
							array(
								"variableName" => "メールアドレス",
								"inputType" => "3",
								"uiType" => "1",
								"message" => "メールアドレスを入力して下さい。",
								"required" => true,
								"errorMessage" => "入力が正しく確認できませんでした。",
								"inputLFType" => 2,
								"canRestore" => true
							),
							array(
								"variableName" => "その他",
								"inputType" => "1",
								"uiType" => "2",
								"message" => "その他ご要望などがあれば記載ください。（特にない場合は「スキップ」ボタンを押してください）",
								"required" => false,
								"errorMessage" => "",
								"inputLFType" => 2,
								"canRestore" => true
							)
						),
						"errorMessage" => "入力が正しく確認できませんでした。",
						"isConfirm" => "1",
						"confirmMessage" => "会社名　　　　：{{会社名}}\nお名前　　　　：{{名前}}\n電話番号　　　：{{電話番号}}\nメールアドレス：{{メールアドレス}}\nその他ご要望　：{{その他}}\n\nでよろしいでしょうか？",
						"success" => "はい",
						"cancel" => "いいえ",
						"cv" => "1",
						"cvCondition" => 1,
						"restore" => true
					),
          "2" => array(
            "chatTextArea" => "2",
            "actionType" => "11",
            "messageIntervalTimeSec" => "2",
            "addCustomerInformations" => array(
              array(
                "variableName" => "会社名",
                "targetItemName" => "会社名"
              ),
              array(
                "variableName" => "名前",
                "targetItemName" => "名前"
              ),
              array(
                "variableName" => "電話番号",
                "targetItemName" => "電話番号"
              ),
              array(
                "variableName" => "メールアドレス",
                "targetItemName" => "メールアドレス"
              )
            ),
						"restore" => false
          ),
          "3" => array(
            "chatTextArea" => "2",
            "actionType" => "4",
            "messageIntervalTimeSec" => "2",
            "mailType" => "3",
            "mailTransmission" => array(
              'from_address' => '',
              'from_name' => '★★★自由に編集してください★★★',
              'to_address' => '{{メールアドレス}}',
              'subject' => '資料請求ありがとうございます'
            ), // FIXME
            "mailTemplate" => array(
              'mail_type_cd' => 'CS001',
              'template' => '--------------------------------------------------------------------------------------------------------
このメールは、資料請求の受け付け完了をお知らせする自動返信メールです。
本メールへの返信は受け付けておりませんのでご了承ください。
--------------------------------------------------------------------------------------------------------
{{会社名}}
{{名前}}様

この度は、資料請求ありがとうございます。

後ほど担当の者から資料をお送りさせて頂きますので少々お待ちください。


──以下お問い合わせいただきました内容です──

■会社名
{{会社名}}

■お名前
{{名前}}

■電話番号
{{電話番号}}

■メールアドレス
{{メールアドレス}}

■その他ご要望
{{その他}}

──────────ここまで─────────

※本メールは自動返信にてお届けしています。

──────────────────────────────

★★★署名を自由に編集してください★★★

──────────────────────────────'
            )
          ),
          "4" => array(
            "chatTextArea" => "2",
            "actionType" => "4",
            "messageIntervalTimeSec" => "2",
            "mailType" => "1",
            "mailTransmission" => array(
              'from_address' => '',
              'from_name' => 'sinclo（シンクロ）',
              'to_address' => '★★★貴社アドレスを設定して下さい★★★',
              'subject' => '資料請求の申し込みがありました'
            ), // FIXME
            "mailTemplate" => array(
              'mail_type_cd' => 'CS001',
              'template' => '※このメールはお客様の設定によりsincloから自動送信されました。

ご担当者様

sincloのシナリオ設定によりメールを送信致しました。
以下のメッセージ内容をご確認下さい。

##SCENARIO_ALL_MESSAGE_BLOCK##

------------------------------------------------------------------
このメールにお心当たりのない方は、誠に恐れ入りますが
下記連絡先までご連絡ください。
sinclo@medialink-ml.co.jp
------------------------------------------------------------------'
            )
          ),
          "5" => array(
            "chatTextArea" => "2",
            "actionType" => "1",
            "messageIntervalTimeSec" => "2",
            "message" => "{{名前}}様からの資料請求を受付いたしました。\n{{メールアドレス}}宛てに資料をお送りさせて頂きます。"
          ),
          "6" => array(
            "chatTextArea" => "2",
            "actionType" => "1",
            "messageIntervalTimeSec" => "2",
            "message" => "この度は、お問い合わせ頂き誠にありがとうございました。"
        	),
					"7" => array(
						"actionType" => "1",
						"messageIntervalTimeSec" => "2",
						"chatTextArea" => "2",
						"message" => "[] メニューに戻る"
					)
      	)
  	  ),
      'del_flg' => 0,
      'sort' => 1,
      'relation_auto_message_index' => 5
    ),
    2 => array(
      'name' => '【サンプル】資料請求（一括ヒアリング）',
      'activity' => array(
        "chatbotType" => "1",
        "scenarios" => array(
          "0" => array(
            "chatTextArea" => "2",
            "actionType" => "1",
            "messageIntervalTimeSec" => "2",
            "message" => "資料請求ですね。"
          ),
          "1" => array(
            "chatTextArea" => "1",
            "actionType" => "2",
            "messageIntervalTimeSec" => "2",
            "hearings" => array(
							0 => array (
								'variableName' => '顧客情報',
								'inputType' => '1',
								'uiType' => '2',
								'message' => 'お客様の会社名、お名前、電話番号、メールアドレスを入力して下さい。（メール署名をコピー＆ペーストで可）',
								'required' => true,
								'errorMessage' => '',
								'inputLFType' => 2,
							),
							1 => array (
								'variableName' => 'その他要望など',
								'inputType' => '1',
								'uiType' => '2',
								'message' => 'その他ご要望などございましたらこちらにご記入ください。（特にない方は「スキップ」ボタンを押してください。）',
								'required' => false,
								'errorMessage' => '',
								'inputLFType' => 2,
							)
            ),
            "errorMessage" => "入力が正しく確認できませんでした。",
            "isConfirm" => "1",
            "confirmMessage" => "お客様の会社名、お名前、電話番号、メールアドレスはすべて入力頂けましたか？",
            "success" => "はい、すべて入力しました",
            "cancel" => "いいえ、入力していません",
            "cv" => "1",
            "cvCondition" => 1,
						'restore' => true
          ),
          "2" => array(
            "chatTextArea" => "2",
            "actionType" => "11",
            "messageIntervalTimeSec" => "2",
            "addCustomerInformations" => array(
              0 => array(
                "variableName" => "顧客情報",
                "targetItemName" => "メモ"
              )
            )
          ),
          "3" => array(
            "chatTextArea" => "2",
            "actionType" => "4",
            "messageIntervalTimeSec" => "2",
            "mailType" => "1",
            "mailTransmission" => array(
              'from_address' => '',
              'from_name' => 'sinclo（シンクロ）',
              'to_address' => '★★★貴社アドレスを設定して下さい★★★',
              'subject' => '資料請求の申し込みがありました'
            ), // FIXME
            "mailTemplate" => array (
              'mail_type_cd' => 'CS001',
              'template' => '※このメールはお客様の設定によりsincloから自動送信されました。

ご担当者様

sincloのシナリオ設定によりメールを送信致しました。
以下のメッセージ内容をご確認下さい。

##SCENARIO_ALL_MESSAGE_BLOCK##

------------------------------------------------------------------
このメールにお心当たりのない方は、誠に恐れ入りますが
下記連絡先までご連絡ください。
sinclo@medialink-ml.co.jp
------------------------------------------------------------------'
            )
          ),
          "4" => array(
            "chatTextArea" => "2",
            "actionType" => "1",
            "messageIntervalTimeSec" => "2",
            "message" => "資料請求を受付いたしました。\nご入力いただきましたメールアドレス宛てに資料をお送りさせて頂きます。"
          ),
          "5" => array(
            "chatTextArea" => "2",
            "actionType" => "1",
            "messageIntervalTimeSec" => "2",
            "message" => "この度は、お問い合わせ頂き誠にありがとうございました。"
          ),
					"6" => array(
						"actionType" => "1",
						"messageIntervalTimeSec" => "2",
						"chatTextArea" => "2",
						"message" => "[] メニューに戻る"
					)
        )
      ),
      'del_flg' => 0,
      'sort' => 2
    ),
    3 => array(
      'name' => '【サンプル】来店予約 ',
      'activity' => array(
        "chatbotType" => "1",
        "scenarios" => array (
          "0" => array (
						'actionType' => '1',
						'messageIntervalTimeSec' => '2',
						'chatTextArea' => '2',
						'message' => 'ご来店予約ですね。',
						'restore' => true,
					),
          "1" => array (
          	'actionType' => '2',
      			'messageIntervalTimeSec' => '2',
      			'chatTextArea' => '1',
      			'hearings' => array (
							0 => array (
								'variableName' => '店舗',
								'uiType' => '4',
								'message' => 'まずご希望の店舗を選択してください。',
								'required' => true,
								'errorMessage' => '',
								'settings' => array (
									'options' => array (
										'池袋店',
										'池袋東口店',
										'上野店',
										'秋葉原・神田店',
										'新橋・銀座店',
										'品川店',
										'六本木店',
										'南青山店',
										'恵比寿・目黒店',
										'渋谷店',
										'新宿東口店',
										'新宿西口店',
										'神楽坂店',
										'三軒茶屋店',
										'北千住店',
										'錦糸町店',
										'赤羽店',
										'西葛西店',
										'町田店',
										'八王子店',
									),
									'disablePastDate' => true,
									'isSetDisableDate' => false,
									'isDisableDayOfWeek' => false,
									'isSetSpecificDate' => false,
									'isEnableAfterDate' => false,
									'enableAfterDate' => NULL,
									'dayOfWeekSetting' => array (
										false,
										false,
										false,
										false,
										false,
										false,
										false,
									),
									'setSpecificDateType' => '',
									'specificDateData' => array (
										'',
									),
									'language' => 1,
									'pulldownCustomDesign' => false,
									'calendarCustomDesign' => false,
									'customDesign' => array (
										'borderColor' => '#ABCD05',
										'backgroundColor' => '#FFFFFF',
										'textColor' => '#666666',
										'headerBackgroundColor' => '#ABCD05',
										'headerTextColor' => '#FFFFFF',
										'headerWeekdayBackgroundColor' => '#F6FAE6',
										'calendarBackgroundColor' => '#FFFFFF',
										'calendarTextColor' => '#666666',
										'saturdayColor' => '#666666',
										'sundayColor' => '#666666',
									),
								),
								'inputLFType' => 2,
								'canRestore' => true,
							),
							1 => array (
								'variableName' => '来店希望日',
								'uiType' => '5',
								'message' => 'ご希望日を選択してください。',
								'required' => true,
								'errorMessage' => '',
								'settings' => array (
									'options' => array (
										'',
									),
									'disablePastDate' => true,
									'isSetDisableDate' => false,
									'isDisableDayOfWeek' => false,
									'isSetSpecificDate' => false,
									'isEnableAfterDate' => false,
									'enableAfterDate' => 1,
									'dayOfWeekSetting' => array (
										false,
										false,
										false,
										false,
										false,
										false,
										false,
									),
									'setSpecificDateType' => '',
									'specificDateData' => array (
										'',
									),
									'language' => 1,
									'pulldownCustomDesign' => false,
									'calendarCustomDesign' => false,
									'customDesign' => array (
										'borderColor' => '#ABCD05',
										'backgroundColor' => '#FFFFFF',
										'textColor' => '#666666',
										'headerBackgroundColor' => '#ABCD05',
										'headerTextColor' => '#FFFFFF',
										'headerWeekdayBackgroundColor' => '#F6FAE6',
										'calendarBackgroundColor' => '#FFFFFF',
										'calendarTextColor' => '#666666',
										'saturdayColor' => '#666666',
										'sundayColor' => '#666666',
									),
								),
								'selectedTextColor' => 'black',
								'weekdayTextColor' => 'black',
								'inputLFType' => 2,
								'canRestore' => true,
							),
							2 => array (
								'variableName' => '時間帯',
								'uiType' => '4',
								'message' => 'ご希望の時間帯を選択してください。',
								'required' => true,
								'errorMessage' => '',
								'settings' => array (
									'options' => array (
										'11:00～11:30',
										'11:30～12:00',
										'12:00～12:30',
										'12:30～13:00',
										'13:00～13:30',
										'13:30～14:00',
										'14:00～14:30',
										'14:30～15:00',
										'15:00～15:30',
										'15:30～16:00',
										'16:00～16:30',
										'16:30～17:00',
										'17:00～17:30',
										'17:30～18:00',
										'18:00～18:30',
										'18:30～19:00',
									),
									'disablePastDate' => true,
									'isSetDisableDate' => false,
									'isDisableDayOfWeek' => false,
									'isSetSpecificDate' => false,
									'isEnableAfterDate' => false,
									'enableAfterDate' => NULL,
									'dayOfWeekSetting' => array (
										false,
										false,
										false,
										false,
										false,
										false,
										false,
									),
									'setSpecificDateType' => '',
									'specificDateData' => array (
										'',
									),
									'language' => 1,
									'pulldownCustomDesign' => false,
									'calendarCustomDesign' => false,
									'customDesign' => array (
										'borderColor' => '#ABCD05',
										'backgroundColor' => '#FFFFFF',
										'textColor' => '#666666',
										'headerBackgroundColor' => '#ABCD05',
										'headerTextColor' => '#FFFFFF',
										'headerWeekdayBackgroundColor' => '#F6FAE6',
										'calendarBackgroundColor' => '#FFFFFF',
										'calendarTextColor' => '#666666',
										'saturdayColor' => '#666666',
										'sundayColor' => '#666666',
									),
								),
								'inputLFType' => 2,
								'canRestore' => true,
							),
							3 => array (
								'variableName' => '名前',
								'inputType' => '1',
								'uiType' => '1',
								'message' => 'お客様のお名前を入力して下さい。',
								'required' => true,
								'errorMessage' => '',
								'inputLFType' => 2,
								'canRestore' => true,
							),
							4 => array (
								'variableName' => '電話番号',
								'inputType' => '4',
								'uiType' => '1',
								'message' => 'お客様の電話番号を入力して下さい。',
								'required' => true,
								'errorMessage' => '入力が正しく確認できませんでした。',
								'inputLFType' => 2,
								'canRestore' => true,
							),
						),
						'restore' => true,
						'isConfirm' => '1',
      			'confirmMessage' => '以下の内容でよろしいでしょうか？

店舗　　　：{{店舗}}
来店希望日：{{来店希望日}}
希望時間帯：{{時間帯}}
お名前　　：{{名前}}
電話番号　：{{電話番号}}',
      			'success' => 'はい',
      			'cancel' => 'いいえ',
      			'cv' => '1',
    			),
					"2" => array (
						"chatTextArea" => "2",
						"actionType" => "4",
						"messageIntervalTimeSec" => "2",
						"mailType" => "1",
						"mailTransmission" => array(
							'from_address' => '',
							'from_name' => 'sinclo（シンクロ）',
							'to_address' => '★★★貴社アドレスを設定して下さい★★★',
							'subject' => '来店予約'
						), // FIXME
						"mailTemplate" => array(
							'mail_type_cd' => 'CS001',
        			'template' => '※このメールはお客様の設定によりsincloから自動送信されました。

ご担当者様

sincloのシナリオ設定によりメールを送信致しました。
以下のメッセージ内容をご確認下さい。

##SCENARIO_ALL_MESSAGE_BLOCK##

------------------------------------------------------------------
このメールにお心当たりのない方は、誠に恐れ入りますが
下記連絡先までご連絡ください。
sinclo@medialink-ml.co.jp
------------------------------------------------------------------'
						)
					),
				  "3" => array (
						'actionType' => '1',
						'messageIntervalTimeSec' => '2',
						'chatTextArea' => '2',
						'message' => 'ご予約ありがとうございました。

[]メニューに戻る',
						'restore' => true,
					),
      	),
			),
			'del_flg' => 0,
			'sort' => 3,
			'relation_auto_message_index' => 11
		),
		4 => array(
			'name' => '【サンプル】会員登録・入会',
			'activity' => array(
				"chatbotType" => "1",
				"scenarios" => array(
					"0" => array (
						'chatTextArea' => '2',
						'actionType' => '1',
						'messageIntervalTimeSec' => '2',
						'message' => '会員登録（入会）ですね。',
						'restore' => false,
					),
					"1" => array (
						'messageIntervalTimeSec' => '2',
						'chatTextArea' => '1',
						'hearings' =>
							array (
								0 =>
									array (
										'variableName' => '会員コース',
										'uiType' => '3',
										'message' => '会員コースをお選びください。',
										'required' => true,
										'errorMessage' => '',
										'settings' =>
											array (
												'options' =>
													array (
														0 => 'Ａコース（月額9,800円）',
														1 => 'Ｂコース（月額12,800円）',
														2 => 'Ｃコース（月額19,800円）',
													),
												'disablePastDate' => true,
												'isSetDisableDate' => false,
												'isDisableDayOfWeek' => false,
												'isSetSpecificDate' => false,
												'isEnableAfterDate' => false,
												'enableAfterDate' => NULL,
												'dayOfWeekSetting' =>
													array (
														0 => false,
														1 => false,
														2 => false,
														3 => false,
														4 => false,
														5 => false,
														6 => false,
													),
												'setSpecificDateType' => '',
												'specificDateData' =>
													array (
														0 => '',
													),
												'language' => 1,
												'pulldownCustomDesign' => false,
												'calendarCustomDesign' => false,
												'customDesign' =>
													array (
														'borderColor' => '',
														'backgroundColor' => '#FFFFFF',
														'textColor' => '',
														'headerBackgroundColor' => '',
														'headerTextColor' => '#FFFFFF',
														'headerWeekdayBackgroundColor' => '',
														'calendarBackgroundColor' => '#FFFFFF',
														'calendarTextColor' => '',
														'saturdayColor' => '',
														'sundayColor' => '',
													),
											),
										'inputLFType' => 2,
									),
							),
						'restore' => false,
						'isConfirm' => '2',
						'confirmMessage' => '',
						'success' => '',
						'cancel' => '',
						'cv' => '2',
						'actionType' => '2',
					),
					"2" => array (
						'chatTextArea' => '2',
						'actionType' => '1',
						'messageIntervalTimeSec' => '2',
						'message' => '{{会員コース}}ですね。',
						'restore' => false,
					),
					"3" => array (
						'chatTextArea' => '2',
						'actionType' => '1',
						'messageIntervalTimeSec' => '2',
						'message' => '続いて、お客様情報をご入力いただきます。',
						'restore' => false,
					),
					"4" => array (
						'chatTextArea' => '1',
						'actionType' => '2',
						'messageIntervalTimeSec' => '2',
						'hearings' =>
							array (
								0 =>
									array (
										'variableName' => '名前',
										'inputType' => '1',
										'uiType' => '1',
										'message' => 'お客様のお名前をフルネーム（漢字）で入力して下さい。',
										'required' => true,
										'errorMessage' => '',
										'inputLFType' => 2,
									),
								1 =>
									array (
										'variableName' => 'カナ',
										'inputType' => '1',
										'uiType' => '1',
										'message' => 'お客様のお名前のフリガナを入力して下さい。',
										'required' => true,
										'errorMessage' => '',
										'inputLFType' => 2,
									),
								2 =>
									array (
										'variableName' => '生年月日',
										'inputType' => '2',
										'uiType' => '1',
										'message' => '生年月日を8桁で入力してください。（例：1990年1月1日生まれの場合 → 19900101）',
										'required' => true,
										'errorMessage' => '入力が正しく確認できませんでした。',
										'inputLFType' => 2,
									),
								3 =>
									array (
										'variableName' => '住所',
										'inputType' => '1',
										'uiType' => '2',
										'message' => '住所を郵便番号から入力してください。',
										'required' => true,
										'errorMessage' => '',
										'inputLFType' => 2,
									),
								4 =>
									array (
										'variableName' => '電話番号',
										'inputType' => '4',
										'uiType' => '1',
										'message' => '電話番号を入力してください。',
										'required' => true,
										'errorMessage' => '入力が正しく確認できませんでした。',
										'inputLFType' => 2,
									),
								5 =>
									array (
										'variableName' => 'メールアドレス',
										'inputType' => '3',
										'uiType' => '1',
										'message' => 'メールアドレスを入力してください。',
										'required' => true,
										'errorMessage' => '入力が正しく確認できませんでした。',
										'inputLFType' => 2,
									),
							),
						'errorMessage' => '入力が正しく確認できませんでした。',
						'isConfirm' => '1',
						'confirmMessage' => 'お名前　　　　：{{名前}}（{{カナ}}）
生年月日　　　：{{生年月日}}
住所　　　　　：{{住所}}
電話番号　　　：{{電話番号}}
メールアドレス：{{メールアドレス}}

でよろしいでしょうか？',
						'success' => 'はい',
						'cancel' => 'いいえ',
						'cv' => '1',
						'cvCondition' => 1,
						'restore' => true,
					),
					"5" => array(
						"chatTextArea" => "2",
						"actionType" => "11",
						"messageIntervalTimeSec" => "2",
						"addCustomerInformations" => array(
							0 => array(
								"variableName" => "名前",
								"targetItemName" => "名前"
							),
							1 => array(
								"variableName" => "電話番号",
								"targetItemName" => "電話番号"
							),
							2 => array(
								"variableName" => "メールアドレス",
								"targetItemName" => "メールアドレス"
							)
						)
					),
					"6" => array(
						"chatTextArea" => "2",
						"actionType" => "4",
						"messageIntervalTimeSec" => "2",
						"mailType" => "1",
						"mailTransmission" => array(
							'from_address' => '',
							'from_name' => 'sinclo（シンクロ）',
							'to_address' => '★★★貴社アドレスを設定して下さい★★★',
							'subject' => '会員登録がありました'
						), // FIXME
						"mailTemplate" => array(
							'mail_type_cd' => 'CS001',
							'template' => '※このメールはお客様の設定によりsincloから自動送信されました。

ご担当者様

sincloのシナリオ設定によりメールを送信致しました。
以下のメッセージ内容をご確認下さい。

##SCENARIO_ALL_MESSAGE_BLOCK##

------------------------------------------------------------------
このメールにお心当たりのない方は、誠に恐れ入りますが
下記連絡先までご連絡ください。
sinclo@medialink-ml.co.jp
------------------------------------------------------------------'
						)
					),
					"7" => array(
						"chatTextArea" => "2",
						"actionType" => "1",
						"messageIntervalTimeSec" => "2",
						"message" => "{{名前}}様からの会員登録（入会）を受付いたしました。\n\nこの度はご入会いただきありがとうございました。"
					),
					"8" => array (
						'actionType' => '1',
						'messageIntervalTimeSec' => '2',
						'chatTextArea' => '2',
						'message' => '[] メニューに戻る',
					),
				)
			),
			'del_flg' => 0,
			'sort' => 4,
			'relation_auto_message_index' => 8
		),
    5 => array(
      'name' => '【サンプル】アンケート',
      'activity' => array(
        "chatbotType" => "1",
        "scenarios" => array(
          "0" => array (
						'messageIntervalTimeSec' => '2',
						'chatTextArea' => '1',
						'hearings' =>
							array (
								0 =>
									array (
										'variableName' => '性別',
										'uiType' => '3',
										'message' => 'お客様の性別をお選びください。（１／７）',
										'required' => true,
										'errorMessage' => '',
										'settings' =>
											array (
												'options' =>
													array (
														0 => '男性',
														1 => '女性',
													),
												'disablePastDate' => true,
												'isSetDisableDate' => false,
												'isDisableDayOfWeek' => false,
												'isSetSpecificDate' => false,
												'isEnableAfterDate' => false,
												'enableAfterDate' => NULL,
												'dayOfWeekSetting' =>
													array (
														0 => false,
														1 => false,
														2 => false,
														3 => false,
														4 => false,
														5 => false,
														6 => false,
													),
												'setSpecificDateType' => '',
												'specificDateData' =>
													array (
														0 => '',
													),
												'language' => 1,
												'pulldownCustomDesign' => false,
												'calendarCustomDesign' => false,
												'customDesign' =>
													array (
														'borderColor' => '',
														'backgroundColor' => '#FFFFFF',
														'textColor' => '',
														'headerBackgroundColor' => '',
														'headerTextColor' => '#FFFFFF',
														'headerWeekdayBackgroundColor' => '',
														'calendarBackgroundColor' => '#FFFFFF',
														'calendarTextColor' => '',
														'saturdayColor' => '',
														'sundayColor' => '',
													),
											),
										'inputLFType' => 2,
										'canRestore' => true,
									),
								1 =>
									array (
										'variableName' => '年代',
										'uiType' => '4',
										'message' => 'お客様の年齢をお選びください。（２／７）',
										'required' => true,
										'errorMessage' => '',
										'settings' =>
											array (
												'options' =>
													array (
														0 => '10代',
														1 => '20代',
														2 => '30代',
														3 => '40代',
														4 => '50代',
														5 => '60代以上',
													),
												'disablePastDate' => true,
												'isSetDisableDate' => false,
												'isDisableDayOfWeek' => false,
												'isSetSpecificDate' => false,
												'isEnableAfterDate' => false,
												'enableAfterDate' => NULL,
												'dayOfWeekSetting' =>
													array (
														0 => false,
														1 => false,
														2 => false,
														3 => false,
														4 => false,
														5 => false,
														6 => false,
													),
												'setSpecificDateType' => '',
												'specificDateData' =>
													array (
														0 => '',
													),
												'language' => 1,
												'pulldownCustomDesign' => false,
												'calendarCustomDesign' => false,
												'customDesign' =>
													array (
														'borderColor' => '#ABCD05',
														'backgroundColor' => '#FFFFFF',
														'textColor' => '#666666',
														'headerBackgroundColor' => '#ABCD05',
														'headerTextColor' => '#FFFFFF',
														'headerWeekdayBackgroundColor' => '#F6FAE6',
														'calendarBackgroundColor' => '#FFFFFF',
														'calendarTextColor' => '#666666',
														'saturdayColor' => '#666666',
														'sundayColor' => '#666666',
													),
											),
										'inputLFType' => 2,
										'canRestore' => true,
									),
								2 =>
									array (
										'variableName' => '地域',
										'uiType' => '4',
										'message' => 'お客様のお住いのエリアをお選びください。（３／７）',
										'required' => true,
										'errorMessage' => '',
										'settings' =>
											array (
												'options' =>
													array (
														0 => '北海道',
														1 => '東北',
														2 => '関東',
														3 => '中部',
														4 => '近畿',
														5 => '中国',
														6 => '四国',
														7 => '九州',
													),
												'disablePastDate' => true,
												'isSetDisableDate' => false,
												'isDisableDayOfWeek' => false,
												'isSetSpecificDate' => false,
												'isEnableAfterDate' => false,
												'enableAfterDate' => NULL,
												'dayOfWeekSetting' =>
													array (
														0 => false,
														1 => false,
														2 => false,
														3 => false,
														4 => false,
														5 => false,
														6 => false,
													),
												'setSpecificDateType' => '',
												'specificDateData' =>
													array (
														0 => '',
													),
												'language' => 1,
												'pulldownCustomDesign' => false,
												'calendarCustomDesign' => false,
												'customDesign' =>
													array (
														'borderColor' => '#ABCD05',
														'backgroundColor' => '#FFFFFF',
														'textColor' => '#666666',
														'headerBackgroundColor' => '#ABCD05',
														'headerTextColor' => '#FFFFFF',
														'headerWeekdayBackgroundColor' => '#F6FAE6',
														'calendarBackgroundColor' => '#FFFFFF',
														'calendarTextColor' => '#666666',
														'saturdayColor' => '#666666',
														'sundayColor' => '#666666',
													),
											),
										'inputLFType' => 2,
										'canRestore' => true,
									),
								3 =>
									array (
										'variableName' => 'きっかけ',
										'uiType' => '3',
										'message' => '当サイトをどのようにして知りましたか。（４／７）',
										'required' => true,
										'errorMessage' => '',
										'settings' =>
											array (
												'options' =>
													array (
														0 => '検索エンジン',
														1 => 'インターネット広告',
														2 => 'メールマガジン',
														3 => 'ＳＮＳ・ブログ',
														4 => '比較サイト',
														5 => 'ご家族・知人・友人からの紹介',
														6 => 'その他',
													),
												'disablePastDate' => true,
												'isSetDisableDate' => false,
												'isDisableDayOfWeek' => false,
												'isSetSpecificDate' => false,
												'isEnableAfterDate' => false,
												'enableAfterDate' => NULL,
												'dayOfWeekSetting' =>
													array (
														0 => false,
														1 => false,
														2 => false,
														3 => false,
														4 => false,
														5 => false,
														6 => false,
													),
												'setSpecificDateType' => '',
												'specificDateData' =>
													array (
														0 => '',
													),
												'language' => 1,
												'pulldownCustomDesign' => false,
												'calendarCustomDesign' => false,
												'customDesign' =>
													array (
														'borderColor' => '#ABCD05',
														'backgroundColor' => '#FFFFFF',
														'textColor' => '#666666',
														'headerBackgroundColor' => '#ABCD05',
														'headerTextColor' => '#FFFFFF',
														'headerWeekdayBackgroundColor' => '#F6FAE6',
														'calendarBackgroundColor' => '#FFFFFF',
														'calendarTextColor' => '#666666',
														'saturdayColor' => '#666666',
														'sundayColor' => '#666666',
													),
											),
										'inputLFType' => 2,
										'canRestore' => true,
									),
								4 =>
									array (
										'variableName' => '頻度',
										'uiType' => '3',
										'message' => '当サイトにどれぐらいの頻度でアクセスしていますか。（５／７）',
										'required' => true,
										'errorMessage' => '',
										'settings' =>
											array (
												'options' =>
													array (
														0 => 'ほぼ毎日',
														1 => '１週間に２～３回程度',
														2 => '１週間に１回程度',
														3 => '１か月に２～３回程度',
														4 => '１か月に１回程度',
														5 => '２～３か月に１回程度',
														6 => 'それ以下',
													),
												'disablePastDate' => true,
												'isSetDisableDate' => false,
												'isDisableDayOfWeek' => false,
												'isSetSpecificDate' => false,
												'isEnableAfterDate' => false,
												'enableAfterDate' => NULL,
												'dayOfWeekSetting' =>
													array (
														0 => false,
														1 => false,
														2 => false,
														3 => false,
														4 => false,
														5 => false,
														6 => false,
													),
												'setSpecificDateType' => '',
												'specificDateData' =>
													array (
														0 => '',
													),
												'language' => 1,
												'pulldownCustomDesign' => false,
												'calendarCustomDesign' => false,
												'customDesign' =>
													array (
														'borderColor' => '#ABCD05',
														'backgroundColor' => '#FFFFFF',
														'textColor' => '#666666',
														'headerBackgroundColor' => '#ABCD05',
														'headerTextColor' => '#FFFFFF',
														'headerWeekdayBackgroundColor' => '#F6FAE6',
														'calendarBackgroundColor' => '#FFFFFF',
														'calendarTextColor' => '#666666',
														'saturdayColor' => '#666666',
														'sundayColor' => '#666666',
													),
											),
										'inputLFType' => 2,
										'canRestore' => true,
									),
								5 =>
									array (
										'variableName' => '満足度',
										'uiType' => '3',
										'message' => '当サイトについて、総合的にどのぐらい満足していますか。（６／７）',
										'required' => true,
										'errorMessage' => '',
										'settings' =>
											array (
												'options' =>
													array (
														0 => '満足',
														1 => 'やや満足',
														2 => 'やや不満',
														3 => '不満',
													),
												'disablePastDate' => true,
												'isSetDisableDate' => false,
												'isDisableDayOfWeek' => false,
												'isSetSpecificDate' => false,
												'isEnableAfterDate' => false,
												'enableAfterDate' => NULL,
												'dayOfWeekSetting' =>
													array (
														0 => false,
														1 => false,
														2 => false,
														3 => false,
														4 => false,
														5 => false,
														6 => false,
													),
												'setSpecificDateType' => '',
												'specificDateData' =>
													array (
														0 => '',
													),
												'language' => 1,
												'pulldownCustomDesign' => false,
												'calendarCustomDesign' => false,
												'customDesign' =>
													array (
														'borderColor' => '#ABCD05',
														'backgroundColor' => '#FFFFFF',
														'textColor' => '#666666',
														'headerBackgroundColor' => '#ABCD05',
														'headerTextColor' => '#FFFFFF',
														'headerWeekdayBackgroundColor' => '#F6FAE6',
														'calendarBackgroundColor' => '#FFFFFF',
														'calendarTextColor' => '#666666',
														'saturdayColor' => '#666666',
														'sundayColor' => '#666666',
													),
											),
										'inputLFType' => 2,
										'canRestore' => true,
									),
								6 =>
									array (
										'variableName' => 'フリー入力',
										'inputType' => '1',
										'uiType' => '2',
										'message' => '当サイトに対してご意見、ご要望などがございましたらご自由にご記入ください。（特にない場合は「スキップ」ボタンを押してください）（７／７）',
										'required' => false,
										'errorMessage' => '',
										'inputLFType' => 2,
										'canRestore' => true,
									),
							),
						'restore' => true,
						'isConfirm' => '2',
						'confirmMessage' => '',
						'success' => '',
						'cancel' => '',
						'cv' => '2',
						'actionType' => '2',
					),
          "1" => array (
						'chatTextArea' => '2',
						'actionType' => '1',
						'messageIntervalTimeSec' => '2',
						'message' => 'アンケートは以上です。ご協力ありがとうございました。',
						'restore' => false,
					),
					"2" => array (
						'actionType' => '1',
						'messageIntervalTimeSec' => '2',
						'chatTextArea' => '2',
						'message' => '[] メニューに戻る',
						'restore' => false,
					),
        )
      ),
      'del_flg' => 0,
      'sort' => 5,
			'relation_auto_message_index' => 9
    ),
    6 => array(
      'name' => '【サンプル】問い合わせフォーム',
      'activity' => array(
        "chatbotType" => "1",
        "scenarios" => array(
          "0" => array (
						'chatTextArea' => '2',
						'actionType' => '1',
						'messageIntervalTimeSec' => '1',
						'message' => 'その他のお問い合わせですね。',
						'restore' => false,
					),
          "1" => array (
						'chatTextArea' => '1',
						'actionType' => '2',
						'messageIntervalTimeSec' => '1',
						'hearings' =>
							array (
								0 =>
									array (
										'variableName' => '会社名',
										'inputType' => '1',
										'uiType' => '1',
										'message' => 'お客様の会社名を入力して下さい。',
										'required' => true,
										'errorMessage' => '',
										'inputLFType' => 2,
									),
								1 =>
									array (
										'variableName' => '名前',
										'inputType' => '1',
										'uiType' => '1',
										'message' => 'お名前を入力して下さい。',
										'required' => true,
										'errorMessage' => '',
										'inputLFType' => 2,
									),
								2 =>
									array (
										'variableName' => '電話番号',
										'inputType' => '4',
										'uiType' => '1',
										'message' => '電話番号を入力して下さい。',
										'required' => true,
										'errorMessage' => '入力が正しく確認できませんでした。',
										'inputLFType' => 2,
									),
								3 =>
									array (
										'variableName' => 'メールアドレス',
										'inputType' => '3',
										'uiType' => '1',
										'message' => 'メールアドレスを入力して下さい。',
										'required' => true,
										'errorMessage' => '入力が正しく確認できませんでした。',
										'inputLFType' => 2,
									),
								4 =>
									array (
										'variableName' => '問い合わせ内容',
										'inputType' => '1',
										'uiType' => '2',
										'message' => 'お問い合わせ内容を記入してください。',
										'required' => true,
										'errorMessage' => '',
										'inputLFType' => 2,
									),
							),
						'errorMessage' => '入力が正しく確認できませんでした。',
						'isConfirm' => '1',
						'confirmMessage' => '会社名　　　　　　：{{会社名}}
お名前　　　　　　：{{名前}}
電話番号　　　　　：{{電話番号}}
メールアドレス　　：{{メールアドレス}}
お問い合わせ内容　：{{問い合わせ内容}}

でよろしいでしょうか？',
						'success' => 'はい',
						'cancel' => 'いいえ',
						'cv' => '1',
						'cvCondition' => 1,
						'restore' => true,
					),
          "2" => array(
            "chatTextArea" => "2",
            "actionType" => "11",
            "messageIntervalTimeSec" => "1",
            "addCustomerInformations" => array(
              array(
                "variableName" => "会社名",
                "targetItemName" => "会社名"
              ),
              array(
                "variableName" => "名前",
                "targetItemName" => "名前"
              ),
              array(
                "variableName" => "電話番号",
                "targetItemName" => "電話番号"
              ),
              array(
                "variableName" => "メールアドレス",
                "targetItemName" => "メールアドレス"
              )
            )
          ),
          "3" => array(
            "chatTextArea" => "2",
            "actionType" => "4",
            "messageIntervalTimeSec" => "1",
            "mailType" => "3",
            "mailTransmission" => array(
              'from_address' => '',
              'from_name' => '★★★自由に編集してください★★★',
              'to_address' => '{{メールアドレス}}',
              'subject' => 'お問い合わせを受付いたしました'
            ), // FIXME
            "mailTemplate" => array(
              'mail_type_cd' => 'CS001',
              'template' => '--------------------------------------------------------------------
このメールは自動返信にてお届けしています。
本メールへの返信は受け付けておりませんのでご了承ください。
--------------------------------------------------------------------
{{会社名}}
{{名前}}様

このたびはお問い合わせを頂き、誠にありがとうございました。
下記の内容でお問い合わせを承りました。

---------------------------------------------------------------
■会社名
{{会社名}}

■お名前
{{名前}}

■電話番号
{{電話番号}}

■メールアドレス
{{メールアドレス}}

■お問い合わせ内容
{{問い合わせ内容}}
---------------------------------------------------------------


────────────────────────────

★★★署名を自由に編集してください★★★

────────────────────────────'
            )
          ),
          "4" => array(
            "chatTextArea" => "2",
            "actionType" => "4",
            "messageIntervalTimeSec" => "1",
            "mailType" => "1",
            "mailTransmission" => array(
              'from_address' => '',
              'from_name' => '{{会社名}}　{{名前}}',
              'to_address' => '★★★貴社アドレスを設定して下さい★★★',
              'subject' => 'お問い合わせ通知'
            ), // FIXME
            "mailTemplate" => array(
              'mail_type_cd' => 'CS001',
              'template' => '※このメールはお客様の設定によりsincloから自動送信されました。

ご担当者様

sincloのシナリオ設定によりメールを送信致しました。
以下のメッセージ内容をご確認下さい。

##SCENARIO_ALL_MESSAGE_BLOCK##

------------------------------------------------------------------
このメールにお心当たりのない方は、誠に恐れ入りますが
下記連絡先までご連絡ください。
sinclo@medialink-ml.co.jp
------------------------------------------------------------------'
            )
          ),
          "5" => array(
            "chatTextArea" => "2",
            "actionType" => "1",
            "messageIntervalTimeSec" => "1",
            "message" => "{{名前}}様からのお問い合わせを受付いたしました。"
          ),
          "6" => array(
            "chatTextArea" => "2",
            "actionType" => "1",
            "messageIntervalTimeSec" => "1",
            "message" => "この度は、お問い合わせ頂き誠にありがとうございました。"
          ),
					"7" => array (
						'actionType' => '1',
						'messageIntervalTimeSec' => '1',
						'chatTextArea' => '2',
						'message' => '[] メニューに戻る',
					),
        )
      ),
      'del_flg' => 0,
      'sort' => 6,
      'relation_auto_message_index' => 6
    )
  )
);
