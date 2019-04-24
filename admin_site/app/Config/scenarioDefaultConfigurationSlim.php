<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/03/25
 * Time: 0:28
 */

$config['default'] = array(
  'scenario_slim' => array(
    1 => array(
      'name' => '【サンプル】資料請求（一括ヒアリング）',
      'activity' => array(
        "chatbotType" => "1",
        "scenarios" => array(
          "0" => array(
            "chatTextArea" => "1",
            "actionType" => "1",
            "messageIntervalTimeSec" => "2",
            "message" => "お客様情報（会社名、お名前、電話番号、メールアドレス）を入力して下さい。

※普段お使いのメール署名をそのままコピー＆ペーストして頂く形でもご利用頂けます。"
          ),
          "1" => array(
            "chatTextArea" => "1",
            "actionType" => "12",
            "messageIntervalTimeSec" => "2",
            "multipleHearings" => array(
              0 => array(
                'required' => true,
                'inputType' => '1',
                'label' => '会社名',
                'variableName' => '会社名'
              ),
              1 => array(
                'required' => true,
                'inputType' => '2',
                'label' => '名前',
                'variableName' => '名前'
              ),
              2 => array(
                'required' => true,
                'inputType' => '7',
                'label' => '電話番号',
                'variableName' => '電話番号'
              ),
              3 => array(
                'required' => true,
                'inputType' => '10',
                'label' => 'メールアドレス',
                'variableName' => 'メールアドレス'
              )
            ),
            "isConfirm" => "0",
            "cv" => "1",
            "cvCondition" => 1,
            'restore' => false
          ),
          "2" => array(
            "chatTextArea" => "1",
            "actionType" => "2",
            "messageIntervalTimeSec" => "2",
            "hearings" => array(
              array(
                "variableName" => "その他",
                "inputType" => "1",
                "uiType" => "2",
                "message" => "その他ご要望などがあれば記載ください。（特にない場合は「スキップ」ボタンを押してください）",
                "required" => false,
                "errorMessage" => "",
                "inputLFType" => 2,
                "restore" => true
              )
            ),
            "errorMessage" => "",
            "isConfirm" => "0",
            "confirmMessage" => "",
            "cv" => "1",
            "cvCondition" => 1,
            "restore" => false
          ),
          "3" => array(
            "chatTextArea" => "2",
            "actionType" => "4",
            "messageIntervalTimeSec" => "2",
            "mailType" => "3",
            "mailTransmission" => array(
              'from_address' => 'no-reply@cv-agaru.com',
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
              'from_address' => 'no-reply@cv-agaru.com',
              'from_name' => 'コンバージョンあがるくん',
              'to_address' => '★★★貴社アドレスを設定して下さい★★★',
              'subject' => '資料請求の申し込みがありました'
            ), // FIXME
            "mailTemplate" => array(
              'mail_type_cd' => 'CS001',
              'template' => '※このメールはお客様の設定によりコンバージョンあがるくんから自動送信されました。

ご担当者様

コンバージョンあがるくんによりメールを送信致しました。
以下のメッセージ内容をご確認下さい。

##SCENARIO_ALL_MESSAGE_BLOCK##'
            )
          ),
          "5" => array(
            "chatTextArea" => "2",
            "actionType" => "1",
            "messageIntervalTimeSec" => "2",
            "message" => "{{名前}}様からの資料請求を受付いたしました。
{{メールアドレス}}宛てに資料をお送りさせて頂きます。"
          ),
          "6" => array(
            "chatTextArea" => "2",
            "actionType" => "1",
            "messageIntervalTimeSec" => "2",
            "message" => "この度は、お問い合わせ頂き誠にありがとうございました。"
          ),
          "7" => array(
            "actionType" => "2",
            "messageIntervalTimeSec" => "2",
            "chatTextArea" => "1",
            "hearings" => array(
              array(
                "variableName" => "メニューに戻る",
                "uiType" => "7",
                "message" => "",
                "required" => true,
                "settings" => array(
                  "options" => array(
                    "メニューに戻る"
                  ),
                  "customDesign" => array(
                    "buttonBackgroundColor" => "#F6FAE6",
                    "buttonTextColor" => "#007AFF",
                    "buttonActiveColor" => "#D5E682",
                    "buttonBorderColor" => "#E3E3E3",
                    "messageAlign" => "2",
                    "buttonAlign" => "2"
                  )
                )
              )
            ),
            "restore" => false,
            "isConfirm" => "2",
            "confirmMessage" => "",
            "success" => "",
            "cancel" => "",
            "cv" => "2"
          )
        )
      ),
      'del_flg' => 0,
      'sort' => 1,
      'relation_auto_message_index' => 7
    ),
    2 => array(
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
            "actionType" => "4",
            "messageIntervalTimeSec" => "2",
            "mailType" => "3",
            "mailTransmission" => array(
              'from_address' => 'no-reply@cv-agaru.com',
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
          "3" => array(
            "chatTextArea" => "2",
            "actionType" => "4",
            "messageIntervalTimeSec" => "2",
            "mailType" => "1",
            "mailTransmission" => array(
              'from_address' => 'no-reply@cv-agaru.com',
              'from_name' => 'コンバージョンあがるくん',
              'to_address' => '★★★貴社アドレスを設定して下さい★★★',
              'subject' => '資料請求の申し込みがありました'
            ), // FIXME
            "mailTemplate" => array(
              'mail_type_cd' => 'CS001',
              'template' => '※このメールはコンバージョンあがるくんから自動送信されました。

ご担当者様

コンバージョンあがるくんによりメールを送信致しました。
以下のメッセージ内容をご確認下さい。

##SCENARIO_ALL_MESSAGE_BLOCK##'
            )
          ),
          "4" => array(
            "chatTextArea" => "2",
            "actionType" => "1",
            "messageIntervalTimeSec" => "2",
            "message" => "{{名前}}様からの資料請求を受付いたしました。\n{{メールアドレス}}宛てに資料をお送りさせて頂きます。"
          ),
          "5" => array(
            "chatTextArea" => "2",
            "actionType" => "1",
            "messageIntervalTimeSec" => "2",
            "message" => "この度は、お問い合わせ頂き誠にありがとうございました。"
          ),
          "6" => array(
            "actionType" => "2",
            "messageIntervalTimeSec" => "2",
            "chatTextArea" => "1",
            "hearings" => array(
              array(
                "variableName" => "メニューに戻る",
                "uiType" => "7",
                "message" => "",
                "required" => true,
                "settings" => array(
                  "options" => array(
                    "メニューに戻る"
                  ),
                  "customDesign" => array(
                    "buttonBackgroundColor" => "#F6FAE6",
                    "buttonTextColor" => "#007AFF",
                    "buttonActiveColor" => "#D5E682",
                    "buttonBorderColor" => "#E3E3E3",
                    "messageAlign" => "2",
                    "buttonAlign" => "2"
                  )
                )
              )
            ),
            "restore" => false,
            "isConfirm" => "2",
            "confirmMessage" => "",
            "success" => "",
            "cancel" => "",
            "cv" => "2"
          )
        )
      ),
      'del_flg' => 0,
      'sort' => 2,
      'relation_auto_message_index' => 8
    ),
    3 => array(
      'name' => '【サンプル】来店予約 ',
      'activity' => array(
        "chatbotType" => "1",
        "scenarios" => array(
          "0" => array(
            'actionType' => '1',
            'messageIntervalTimeSec' => '2',
            'chatTextArea' => '2',
            'message' => 'ご来店予約ですね。',
            'restore' => true,
          ),
          "1" => array(
            'actionType' => '2',
            'messageIntervalTimeSec' => '2',
            'chatTextArea' => '1',
            'hearings' => array(
              0 => array(
                "variableName" => "コース",
                "uiType" => "6",
                "message" => "ご希望のコースを選択してください。",
                "required" => true,
                "errorMessage" => "",
                "settings" => array(
                  "options" => array(
                    ""
                  ),
                  "disablePastDate" => true,
                  "isSetDisableDate" => false,
                  "isDisableDayOfWeek" => false,
                  "isSetSpecificDate" => false,
                  "isEnableAfterDate" => false,
                  "enableAfterDate" => null,
                  "isDisableAfterData" => false,
                  "dayOfWeekSetting" => array(
                    false,
                    false,
                    false,
                    false,
                    false,
                    false,
                    false
                  ),
                  "setSpecificDateType" => "",
                  "specificDateData" => array(
                    ""
                  ),
                  "language" => 1,
                  "pulldownCustomDesign" => false,
                  "calendarCustomDesign" => false,
                  "carouselCustomDesign" => false,
                  "buttonUICustomDesign" => false,
                  "checkboxCustomDesign" => false,
                  "radioCustomDesign" => false,
                  "balloonStyle" => "1",
                  "lineUpStyle" => "1",
                  "carouselPattern" => "2",
                  "arrowType" => "4",
                  "titlePosition" => "1",
                  "subTitlePosition" => "1",
                  "outCarouselNoneBorder" => false,
                  "inCarouselNoneBorder" => false,
                  "outButtonUINoneBorder" => true,
                  "checkboxNoneBorder" => false,
                  "radioNoneBorder" => false,
                  "aspectRatio" => 1.1542857142857,
                  "checkboxSeparator" => "1",
                  "customDesign" => array(
                    "borderColor" => "#ABCD05",
                    "backgroundColor" => "#FFFFFF",
                    "textColor" => "#666666",
                    "headerBackgroundColor" => "#ABCD05",
                    "headerTextColor" => "#FFFFFF",
                    "headerWeekdayBackgroundColor" => "#F6FAE6",
                    "calendarBackgroundColor" => "#FFFFFF",
                    "calendarTextColor" => "#666666",
                    "saturdayColor" => "#666666",
                    "sundayColor" => "#666666",
                    "titleColor" => "#333333",
                    "subTitleColor" => "#333333",
                    "arrowColor" => "#ABCD05",
                    "titleFontSize" => 14,
                    "subTitleFontSize" => 13,
                    "outBorderColor" => "#E8E7E0",
                    "inBorderColor" => "#E8E7E0",
                    "messageAlign" => "2",
                    "buttonBackgroundColor" => "#F6FAE6",
                    "buttonTextColor" => "#007AFF",
                    "buttonAlign" => "2",
                    "buttonActiveColor" => "#D5E682",
                    "buttonBorderColor" => "#E3E3E3",
                    "buttonUIBackgroundColor" => "#333333",
                    "buttonUITextAlign" => "2",
                    "buttonUITextColor" => "#F6FAE6",
                    "buttonUIActiveColor" => "#D5E682",
                    "buttonUIBorderColor" => "#E3E3E3",
                    "checkboxBackgroundColor" => "#FFFFFF",
                    "checkboxActiveColor" => "#FFFFFF",
                    "checkboxBorderColor" => "#ABCD05",
                    "checkboxCheckmarkColor" => "#ABCD05",
                    "radioBackgroundColor" => "#FFFFFF",
                    "radioActiveColor" => "#ABCD05",
                    "radioBorderColor" => "#999"
                  ),
                  "images" => array(
                    array(
                      "title" => "バナナ",
                      "subTitle" => "サンプルです。サンプルです。サンプルです。サンプルです。サンプルです。サンプルです。サンプルです。",
                      "answer" => "バナナ",
                      "url" => "banana.png",
                      "isUploading" => false
                    ),
                    array(
                      "title" => "りんご",
                      "subTitle" => "サンプルです。サンプルです。サンプルです。サンプルです。サンプルです。サンプルです。サンプルです。",
                      "answer" => "りんご",
                      "url" => "apple.png",
                      "isUploading" => false
                    ),
                    array(
                      "title" => "オレンジ",
                      "subTitle" => "サンプルです。サンプルです。サンプルです。サンプルです。サンプルです。サンプルです。サンプルです。",
                      "answer" => "オレンジ",
                      "url" => "orange.png",
                      "isUploading" => false
                    )
                  )
                ),
                "inputLFType" => 2,
                "canRestore" => true
              ),
              1 => array(
                'variableName' => '店舗',
                'uiType' => '4',
                'message' => '続いて、ご希望の店舗を選択してください。',
                'required' => true,
                'errorMessage' => '',
                'settings' => array(
                  'options' => array(
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
                  'enableAfterDate' => null,
                  'dayOfWeekSetting' => array(
                    false,
                    false,
                    false,
                    false,
                    false,
                    false,
                    false,
                  ),
                  'setSpecificDateType' => '',
                  'specificDateData' => array(
                    '',
                  ),
                  'language' => 1,
                  'pulldownCustomDesign' => false,
                  'calendarCustomDesign' => false,
                  'customDesign' => array(
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
              2 => array(
                'variableName' => '来店希望日',
                'uiType' => '5',
                'message' => 'ご希望日を選択してください。',
                'required' => true,
                'errorMessage' => '',
                'settings' => array(
                  'options' => array(
                    '',
                  ),
                  'disablePastDate' => true,
                  'isSetDisableDate' => false,
                  'isDisableDayOfWeek' => false,
                  'isSetSpecificDate' => false,
                  'isEnableAfterDate' => false,
                  'enableAfterDate' => 1,
                  'dayOfWeekSetting' => array(
                    false,
                    false,
                    false,
                    false,
                    false,
                    false,
                    false,
                  ),
                  'setSpecificDateType' => '',
                  'specificDateData' => array(
                    '',
                  ),
                  'language' => 1,
                  'pulldownCustomDesign' => false,
                  'calendarCustomDesign' => false,
                  'customDesign' => array(
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
              3 => array(
                'variableName' => '時間帯',
                'uiType' => '4',
                'message' => 'ご希望の時間帯を選択してください。',
                'required' => true,
                'errorMessage' => '',
                'settings' => array(
                  'options' => array(
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
                  'enableAfterDate' => null,
                  'dayOfWeekSetting' => array(
                    false,
                    false,
                    false,
                    false,
                    false,
                    false,
                    false,
                  ),
                  'setSpecificDateType' => '',
                  'specificDateData' => array(
                    '',
                  ),
                  'language' => 1,
                  'pulldownCustomDesign' => false,
                  'calendarCustomDesign' => false,
                  'customDesign' => array(
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
              4 => array(
                'variableName' => '名前',
                'inputType' => '1',
                'uiType' => '1',
                'message' => 'お客様のお名前を入力して下さい。',
                'required' => true,
                'errorMessage' => '',
                'inputLFType' => 2,
                'canRestore' => true,
              ),
              5 => array(
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
          "2" => array(
            "chatTextArea" => "2",
            "actionType" => "4",
            "messageIntervalTimeSec" => "2",
            "mailType" => "1",
            "mailTransmission" => array(
              'from_address' => 'no-reply@cv-agaru.com',
              'from_name' => 'コンバージョンあがるくん',
              'to_address' => '★★★貴社アドレスを設定して下さい★★★',
              'subject' => '来店予約'
            ), // FIXME
            "mailTemplate" => array(
              'mail_type_cd' => 'CS001',
              'template' => '※このメールはコンバージョンあがるくんから自動送信されました。

ご担当者様

コンバージョンあがるくんによりメールを送信致しました。
以下のメッセージ内容をご確認下さい。

##SCENARIO_ALL_MESSAGE_BLOCK##'
            )
          ),
          "3" => array(
            "actionType" => "2",
            "messageIntervalTimeSec" => "2",
            "chatTextArea" => "1",
            "hearings" => array(
              array(
                "variableName" => "メニューに戻る",
                "uiType" => "7",
                "message" => "ご予約ありがとうございました。",
                "required" => true,
                "settings" => array(
                  "options" => array(
                    "メニューに戻る"
                  ),
                  "customDesign" => array(
                    "buttonBackgroundColor" => "#F6FAE6",
                    "buttonTextColor" => "#007AFF",
                    "buttonActiveColor" => "#D5E682",
                    "buttonBorderColor" => "#E3E3E3",
                    "messageAlign" => "2",
                    "buttonAlign" => "2"
                  )
                )
              )
            ),
            "restore" => false,
            "isConfirm" => "2",
            "confirmMessage" => "",
            "success" => "",
            "cancel" => "",
            "cv" => "2"
          ),
        ),
      ),
      'del_flg' => 0,
      'sort' => 3,
      'relation_auto_message_index' => 9
    ),
    4 => array(
      'name' => '【サンプル】会員登録・入会',
      'activity' => array(
        "chatbotType" => "1",
        "scenarios" => array(
          "0" => array(
            'chatTextArea' => '2',
            'actionType' => '1',
            'messageIntervalTimeSec' => '2',
            'message' => '会員登録（入会）ですね。',
            'restore' => false,
          ),
          "1" => array(
            'messageIntervalTimeSec' => '2',
            'chatTextArea' => '1',
            'actionType' => '2',
            'hearings' =>
              array(
                0 =>
                  array(
                    'variableName' => '会員コース',
                    'uiType' => '7',
                    'message' => '会員コースをお選びください。',
                    'required' => true,
                    'errorMessage' => '',
                    'settings' =>
                      array(
                        'options' =>
                          array(
                            0 => 'Ａコース（月額9,800円）',
                            1 => 'Ｂコース（月額12,800円）',
                            2 => 'Ｃコース（月額19,800円）',
                          ),
                        'customDesign' =>
                          array(
                            "buttonBackgroundColor" => "#F6FAE6",
                            "buttonTextColor" => "#007AFF",
                            "buttonActiveColor" => "#D5E682",
                            "buttonBorderColor" => "#FFFFFF",
                            "messageAlign" => "2",
                            "buttonAlign" => "2"
                          ),
                        'isCustomDesign' => true
                      ),
                    'inputLFType' => 2,
                  ),
                1 =>
                  array(
                    'variableName' => '名前',
                    'inputType' => '1',
                    'uiType' => '1',
                    'message' => "続いて、お客様情報をご入力いただきます。\n\nお客様のお名前をフルネーム（漢字）で入力して下さい。",
                    'required' => true,
                    'errorMessage' => '',
                    'inputLFType' => 2,
                  ),
                2 =>
                  array(
                    'variableName' => 'カナ',
                    'inputType' => '1',
                    'uiType' => '1',
                    'message' => 'お客様のお名前のフリガナを入力して下さい。',
                    'required' => true,
                    'errorMessage' => '',
                    'inputLFType' => 2,
                  ),
                3 =>
                  array(
                    "variableName" => "生年月日（年）",
                    "uiType" => "4",
                    "message" => "お客様の生年月日を入力していただきます。\n※このあと残り３つの質問がございます。\n\nはじめに西暦からお選びください。",
                    "required" => true,
                    "errorMessage" => "",
                    "inputLFType" => 2,
                    "settings" => array(
                      "options" => array(
                        "1920年",
                        "1921年",
                        "1922年",
                        "1923年",
                        "1924年",
                        "1925年",
                        "1926年",
                        "1927年",
                        "1928年",
                        "1929年",
                        "1930年",
                        "1931年",
                        "1932年",
                        "1933年",
                        "1934年",
                        "1935年",
                        "1936年",
                        "1937年",
                        "1938年",
                        "1939年",
                        "1940年",
                        "1941年",
                        "1942年",
                        "1943年",
                        "1944年",
                        "1945年",
                        "1946年",
                        "1947年",
                        "1948年",
                        "1949年",
                        "1950年",
                        "1951年",
                        "1952年",
                        "1953年",
                        "1954年",
                        "1955年",
                        "1956年",
                        "1957年",
                        "1958年",
                        "1959年",
                        "1960年",
                        "1961年",
                        "1962年",
                        "1963年",
                        "1964年",
                        "1965年",
                        "1966年",
                        "1967年",
                        "1968年",
                        "1969年",
                        "1970年",
                        "1971年",
                        "1972年",
                        "1973年",
                        "1974年",
                        "1975年",
                        "1976年",
                        "1977年",
                        "1978年",
                        "1979年",
                        "1980年",
                        "1981年",
                        "1982年",
                        "1983年",
                        "1984年",
                        "1985年",
                        "1986年",
                        "1987年",
                        "1988年",
                        "1989年",
                        "1990年",
                        "1991年",
                        "1992年",
                        "1993年",
                        "1994年",
                        "1995年",
                        "1996年",
                        "1997年",
                        "1998年",
                        "1999年",
                        "2000年",
                        "2001年",
                        "2002年",
                        "2003年",
                        "2004年",
                        "2005年",
                        "2006年",
                        "2007年",
                        "2008年",
                        "2009年",
                        "2010年",
                        "2011年",
                        "2012年",
                        "2013年",
                        "2014年",
                        "2015年",
                        "2016年",
                        "2017年",
                        "2018年",
                        "2019年"
                      ),
                      "disablePastDate" => true,
                      "isSetDisableDate" => false,
                      "isDisableDayOfWeek" => false,
                      "isSetSpecificDate" => false,
                      "isEnableAfterDate" => false,
                      "enableAfterDate" => null,
                      "isDisableAfterData" => false,
                      "dayOfWeekSetting" => array(
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false
                      ),
                      "setSpecificDateType" => "",
                      "specificDateData" => array(
                        ""
                      ),
                      "language" => 1,
                      "pulldownCustomDesign" => false,
                      "calendarCustomDesign" => false,
                      "carouselCustomDesign" => false,
                      "buttonUICustomDesign" => false,
                      "checkboxCustomDesign" => false,
                      "radioCustomDesign" => false,
                      "balloonStyle" => "1",
                      "lineUpStyle" => "1",
                      "carouselPattern" => "2",
                      "arrowType" => "4",
                      "titlePosition" => "1",
                      "subTitlePosition" => "1",
                      "outCarouselNoneBorder" => false,
                      "inCarouselNoneBorder" => false,
                      "outButtonUINoneBorder" => true,
                      "checkboxNoneBorder" => false,
                      "radioNoneBorder" => false,
                      "aspectRatio" => null,
                      "checkboxSeparator" => "1",
                      "customDesign" => array(
                        "borderColor" => "#ABCD05",
                        "backgroundColor" => "#FFFFFF",
                        "textColor" => "#666666",
                        "headerBackgroundColor" => "#ABCD05",
                        "headerTextColor" => "#FFFFFF",
                        "headerWeekdayBackgroundColor" => "#F6FAE6",
                        "calendarBackgroundColor" => "#FFFFFF",
                        "calendarTextColor" => "#666666",
                        "saturdayColor" => "#666666",
                        "sundayColor" => "#666666",
                        "titleColor" => "#333333",
                        "subTitleColor" => "#333333",
                        "arrowColor" => "#ABCD05",
                        "titleFontSize" => 14,
                        "subTitleFontSize" => 13,
                        "outBorderColor" => "#E8E7E0",
                        "inBorderColor" => "#E8E7E0",
                        "messageAlign" => "2",
                        "buttonBackgroundColor" => "#F6FAE6",
                        "buttonTextColor" => "#007AFF",
                        "buttonAlign" => "2",
                        "buttonActiveColor" => "#D5E682",
                        "buttonBorderColor" => "#E3E3E3",
                        "buttonUIBackgroundColor" => "#333333",
                        "buttonUITextAlign" => "2",
                        "buttonUITextColor" => "#F6FAE6",
                        "buttonUIActiveColor" => "#D5E682",
                        "buttonUIBorderColor" => "#E3E3E3",
                        "checkboxBackgroundColor" => "#FFFFFF",
                        "checkboxActiveColor" => "#FFFFFF",
                        "checkboxBorderColor" => "#ABCD05",
                        "checkboxCheckmarkColor" => "#ABCD05",
                        "radioBackgroundColor" => "#FFFFFF",
                        "radioActiveColor" => "#ABCD05",
                        "radioBorderColor" => "#999"
                      ),
                      "images" => array(
                        array(
                          "title" => "",
                          "subTitle" => "",
                          "answer" => "",
                          "url" => ""
                        )
                      )
                    ),
                    "canRestore" => true
                  ),
                4 =>
                  array(
                    "variableName" => "生年月日（月）",
                    "uiType" => "4",
                    "message" => "続けて月をお選びください。",
                    "required" => true,
                    "errorMessage" => "",
                    "settings" => array(
                      "options" => array(
                        "1月",
                        "2月",
                        "3月",
                        "4月",
                        "5月",
                        "6月",
                        "7月",
                        "8月",
                        "9月",
                        "10月",
                        "11月",
                        "12月"
                      ),
                      "disablePastDate" => true,
                      "isSetDisableDate" => false,
                      "isDisableDayOfWeek" => false,
                      "isSetSpecificDate" => false,
                      "isEnableAfterDate" => false,
                      "enableAfterDate" => null,
                      "isDisableAfterData" => false,
                      "dayOfWeekSetting" => array(
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false
                      ),
                      "setSpecificDateType" => "",
                      "specificDateData" => array(
                        ""
                      ),
                      "language" => 1,
                      "pulldownCustomDesign" => false,
                      "calendarCustomDesign" => false,
                      "carouselCustomDesign" => false,
                      "buttonUICustomDesign" => false,
                      "checkboxCustomDesign" => false,
                      "radioCustomDesign" => false,
                      "balloonStyle" => "1",
                      "lineUpStyle" => "1",
                      "carouselPattern" => "2",
                      "arrowType" => "4",
                      "titlePosition" => "1",
                      "subTitlePosition" => "1",
                      "outCarouselNoneBorder" => false,
                      "inCarouselNoneBorder" => false,
                      "outButtonUINoneBorder" => true,
                      "checkboxNoneBorder" => false,
                      "radioNoneBorder" => false,
                      "aspectRatio" => null,
                      "checkboxSeparator" => "1",
                      "customDesign" => array(
                        "borderColor" => "#ABCD05",
                        "backgroundColor" => "#FFFFFF",
                        "textColor" => "#666666",
                        "headerBackgroundColor" => "#ABCD05",
                        "headerTextColor" => "#FFFFFF",
                        "headerWeekdayBackgroundColor" => "#F6FAE6",
                        "calendarBackgroundColor" => "#FFFFFF",
                        "calendarTextColor" => "#666666",
                        "saturdayColor" => "#666666",
                        "sundayColor" => "#666666",
                        "titleColor" => "#333333",
                        "subTitleColor" => "#333333",
                        "arrowColor" => "#ABCD05",
                        "titleFontSize" => 14,
                        "subTitleFontSize" => 13,
                        "outBorderColor" => "#E8E7E0",
                        "inBorderColor" => "#E8E7E0",
                        "messageAlign" => "2",
                        "buttonBackgroundColor" => "#F6FAE6",
                        "buttonTextColor" => "#007AFF",
                        "buttonAlign" => "2",
                        "buttonActiveColor" => "#D5E682",
                        "buttonBorderColor" => "#E3E3E3",
                        "buttonUIBackgroundColor" => "#333333",
                        "buttonUITextAlign" => "2",
                        "buttonUITextColor" => "#F6FAE6",
                        "buttonUIActiveColor" => "#D5E682",
                        "buttonUIBorderColor" => "#E3E3E3",
                        "checkboxBackgroundColor" => "#FFFFFF",
                        "checkboxActiveColor" => "#FFFFFF",
                        "checkboxBorderColor" => "#ABCD05",
                        "checkboxCheckmarkColor" => "#ABCD05",
                        "radioBackgroundColor" => "#FFFFFF",
                        "radioActiveColor" => "#ABCD05",
                        "radioBorderColor" => "#999"
                      ),
                      "images" => array(
                        array(
                          "title" => "",
                          "subTitle" => "",
                          "answer" => "",
                          "url" => ""
                        )
                      )
                    ),
                    "inputLFType" => 2,
                    "canRestore" => true
                  ),
                5 =>
                  array(
                    "variableName" => "生年月日（日）",
                    "uiType" => "4",
                    "message" => "最後に日にちを選択してください。",
                    "required" => true,
                    "errorMessage" => "",
                    "settings" => array(
                      "options" => array(
                        "1日",
                        "2日",
                        "3日",
                        "4日",
                        "5日",
                        "6日",
                        "7日",
                        "8日",
                        "9日",
                        "10日",
                        "11日",
                        "12日",
                        "13日",
                        "14日",
                        "15日",
                        "16日",
                        "17日",
                        "18日",
                        "19日",
                        "20日",
                        "21日",
                        "22日",
                        "23日",
                        "24日",
                        "25日",
                        "26日",
                        "27日",
                        "28日",
                        "29日",
                        "30日",
                        "31日"
                      ),
                      "disablePastDate" => true,
                      "isSetDisableDate" => false,
                      "isDisableDayOfWeek" => false,
                      "isSetSpecificDate" => false,
                      "isEnableAfterDate" => false,
                      "enableAfterDate" => null,
                      "isDisableAfterData" => false,
                      "dayOfWeekSetting" => array(
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false
                      ),
                      "setSpecificDateType" => "",
                      "specificDateData" => array(
                        ""
                      ),
                      "language" => 1,
                      "pulldownCustomDesign" => false,
                      "calendarCustomDesign" => false,
                      "carouselCustomDesign" => false,
                      "buttonUICustomDesign" => false,
                      "checkboxCustomDesign" => false,
                      "radioCustomDesign" => false,
                      "balloonStyle" => "1",
                      "lineUpStyle" => "1",
                      "carouselPattern" => "2",
                      "arrowType" => "4",
                      "titlePosition" => "1",
                      "subTitlePosition" => "1",
                      "outCarouselNoneBorder" => false,
                      "inCarouselNoneBorder" => false,
                      "outButtonUINoneBorder" => true,
                      "checkboxNoneBorder" => false,
                      "radioNoneBorder" => false,
                      "aspectRatio" => null,
                      "checkboxSeparator" => "1",
                      "customDesign" => array(
                        "borderColor" => "#ABCD05",
                        "backgroundColor" => "#FFFFFF",
                        "textColor" => "#666666",
                        "headerBackgroundColor" => "#ABCD05",
                        "headerTextColor" => "#FFFFFF",
                        "headerWeekdayBackgroundColor" => "#F6FAE6",
                        "calendarBackgroundColor" => "#FFFFFF",
                        "calendarTextColor" => "#666666",
                        "saturdayColor" => "#666666",
                        "sundayColor" => "#666666",
                        "titleColor" => "#333333",
                        "subTitleColor" => "#333333",
                        "arrowColor" => "#ABCD05",
                        "titleFontSize" => 14,
                        "subTitleFontSize" => 13,
                        "outBorderColor" => "#E8E7E0",
                        "inBorderColor" => "#E8E7E0",
                        "messageAlign" => "2",
                        "buttonBackgroundColor" => "#F6FAE6",
                        "buttonTextColor" => "#007AFF",
                        "buttonAlign" => "2",
                        "buttonActiveColor" => "#D5E682",
                        "buttonBorderColor" => "#E3E3E3",
                        "buttonUIBackgroundColor" => "#333333",
                        "buttonUITextAlign" => "2",
                        "buttonUITextColor" => "#F6FAE6",
                        "buttonUIActiveColor" => "#D5E682",
                        "buttonUIBorderColor" => "#E3E3E3",
                        "checkboxBackgroundColor" => "#FFFFFF",
                        "checkboxActiveColor" => "#FFFFFF",
                        "checkboxBorderColor" => "#ABCD05",
                        "checkboxCheckmarkColor" => "#ABCD05",
                        "radioBackgroundColor" => "#FFFFFF",
                        "radioActiveColor" => "#ABCD05",
                        "radioBorderColor" => "#999"
                      ),
                      "images" => array(
                        array(
                          "title" => "",
                          "subTitle" => "",
                          "answer" => "",
                          "url" => ""
                        )
                      )
                    ),
                    "inputLFType" => 2,
                    "canRestore" => true
                  ),
                6 =>
                  array(
                    "variableName" => "住所",
                    "inputType" => "1",
                    "uiType" => "2",
                    "message" => "住所を郵便番号から入力してください。\n※質問は残り２つです。",
                    "required" => true,
                    "errorMessage" => "",
                    "inputLFType" => 2,
                    "canRestore" => true
                  ),
                7 =>
                  array(
                    "variableName" => "電話番号",
                    "inputType" => "4",
                    "uiType" => "1",
                    "message" => "電話番号を入力してください。",
                    "required" => true,
                    "errorMessage" => "入力が正しく確認できませんでした。",
                    "inputLFType" => 2,
                    "canRestore" => true
                  ),
                8 =>
                  array(
                    'variableName' => 'メールアドレス',
                    'inputType' => '3',
                    'uiType' => '1',
                    'message' => 'メールアドレスを入力してください。',
                    'required' => true,
                    'errorMessage' => '入力が正しく確認できませんでした。',
                    'inputLFType' => 2,
                  ),
              ),
            "errorMessage" => "入力が正しく確認できませんでした。",
            "isConfirm" => "1",
            "confirmMessage" => "お名前　　　　：{{名前}}（{{カナ}}）\n生年月日　　　：{{生年月日（年）}}{{生年月日（月）}}{{生年月日（日）}}\n住所　　　　　：{{住所}}\n電話番号　　　：{{電話番号}}\nメールアドレス：{{メールアドレス}}\n\nでよろしいでしょうか？",
            "success" => "はい",
            "cancel" => "いいえ",
            "cv" => "1",
            "cvCondition" => 1,
            "restore" => true
          ),
          "2" => array(
            "chatTextArea" => "2",
            "actionType" => "4",
            "messageIntervalTimeSec" => "2",
            "mailType" => "1",
            "mailTransmission" => array(
              'from_address' => 'no-reply@cv-agaru.com',
              'from_name' => 'コンバージョンあがるくん',
              'to_address' => '★★★貴社アドレスを設定して下さい★★★',
              'subject' => '会員登録がありました'
            ), // FIXME
            "mailTemplate" => array(
              'mail_type_cd' => 'CS001',
              'template' => '※このメールはコンバージョンあがるくんから自動送信されました。

ご担当者様

コンバージョンあがるくんによりメールを送信致しました。
以下のメッセージ内容をご確認下さい。

##SCENARIO_ALL_MESSAGE_BLOCK##'
            )
          ),
          "3" => array(
            "chatTextArea" => "2",
            "actionType" => "1",
            "messageIntervalTimeSec" => "2",
            "message" => "{{名前}}様からの会員登録（入会）を受付いたしました。\n\nこの度はご入会いただきありがとうございました。"
          ),
          "4" => array(
            "actionType" => "2",
            "messageIntervalTimeSec" => "2",
            "chatTextArea" => "1",
            "hearings" => array(
              array(
                "variableName" => "メニューに戻る",
                "uiType" => "7",
                "message" => "",
                "required" => true,
                "settings" => array(
                  "options" => array(
                    "メニューに戻る"
                  ),
                  "customDesign" => array(
                    "buttonBackgroundColor" => "#F6FAE6",
                    "buttonTextColor" => "#007AFF",
                    "buttonActiveColor" => "#D5E682",
                    "buttonBorderColor" => "#E3E3E3",
                    "messageAlign" => "2",
                    "buttonAlign" => "2"
                  )
                )
              )
            ),
            "restore" => false,
            "isConfirm" => "2",
            "confirmMessage" => "",
            "success" => "",
            "cancel" => "",
            "cv" => "2"
          )
        )
      ),
      'del_flg' => 0,
      'sort' => 4,
      'relation_auto_message_index' => 10
    ),
    5 => array(
      'name' => '【サンプル】アンケート',
      'activity' => array(
        "chatbotType" => "1",
        "scenarios" => array(
          "0" => array(
            'messageIntervalTimeSec' => '2',
            'chatTextArea' => '1',
            'hearings' =>
              array(
                0 =>
                  array(
                    "variableName" => "性別",
                    "uiType" => "7",
                    "message" => "お客様の性別をお選びください。（１／７）",
                    "required" => true,
                    "errorMessage" => "",
                    "settings" => array(
                      "options" => array(
                        "男性",
                        "女性"
                      ),
                      "disablePastDate" => true,
                      "isSetDisableDate" => false,
                      "isDisableDayOfWeek" => false,
                      "isSetSpecificDate" => false,
                      "isEnableAfterDate" => false,
                      "enableAfterDate" => null,
                      "dayOfWeekSetting" => array(
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false
                      ),
                      "setSpecificDateType" => "",
                      "specificDateData" => array(
                        ""
                      ),
                      "language" => 1,
                      "pulldownCustomDesign" => false,
                      "calendarCustomDesign" => false,
                      "customDesign" => array(
                        "borderColor" => "#ABCD05",
                        "backgroundColor" => "#FFFFFF",
                        "textColor" => "#666666",
                        "headerBackgroundColor" => "#ABCD05",
                        "headerTextColor" => "#FFFFFF",
                        "headerWeekdayBackgroundColor" => "#F6FAE6",
                        "calendarBackgroundColor" => "#FFFFFF",
                        "calendarTextColor" => "#666666",
                        "saturdayColor" => "#666666",
                        "sundayColor" => "#666666",
                        "messageAlign" => "2",
                        "buttonBackgroundColor" => "#F6FAE6",
                        "buttonTextColor" => "#007AFF",
                        "buttonAlign" => "2",
                        "buttonActiveColor" => "#D5E682",
                        "buttonBorderColor" => "#FFFFFF"
                      ),
                      "isCustomDesign" => true
                    ),
                    "inputLFType" => 2,
                    "canRestore" => true
                  ),
                1 =>
                  array(
                    "variableName" => "年代",
                    "uiType" => "4",
                    "message" => "お客様の年齢をお選びください。（２／７）",
                    "required" => true,
                    "errorMessage" => "",
                    "settings" => array(
                      "options" => array(
                        "10代",
                        "20代",
                        "30代",
                        "40代",
                        "50代",
                        "60代以上"
                      ),
                      "disablePastDate" => true,
                      "isSetDisableDate" => false,
                      "isDisableDayOfWeek" => false,
                      "isSetSpecificDate" => false,
                      "isEnableAfterDate" => false,
                      "enableAfterDate" => null,
                      "dayOfWeekSetting" => array(
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false
                      ),
                      "setSpecificDateType" => "",
                      "specificDateData" => array(
                        ""
                      ),
                      "language" => 1,
                      "pulldownCustomDesign" => false,
                      "calendarCustomDesign" => false,
                      "customDesign" => array(
                        "borderColor" => "#ABCD05",
                        "backgroundColor" => "#FFFFFF",
                        "textColor" => "#666666",
                        "headerBackgroundColor" => "#ABCD05",
                        "headerTextColor" => "#FFFFFF",
                        "headerWeekdayBackgroundColor" => "#F6FAE6",
                        "calendarBackgroundColor" => "#FFFFFF",
                        "calendarTextColor" => "#666666",
                        "saturdayColor" => "#666666",
                        "sundayColor" => "#666666",
                        "messageAlign" => "2",
                        "buttonBackgroundColor" => "#F6FAE6",
                        "buttonTextColor" => "#007AFF",
                        "buttonAlign" => "2",
                        "buttonActiveColor" => "#D5E682",
                        "buttonBorderColor" => "#E3E3E3",
                        "titleColor" => "#333333",
                        "subTitleColor" => "#333333",
                        "arrowColor" => "#ABCD05",
                        "outBorderColor" => "#E8E7E0",
                        "inBorderColor" => "#E8E7E0",
                        "titleFontSize" => 14,
                        "subTitleFontSize" => 13,
                        "buttonUIBackgroundColor" => "#333333",
                        "buttonUITextColor" => "#F6FAE6",
                        "buttonUITextAlign" => "2",
                        "buttonUIActiveColor" => "#D5E682",
                        "buttonUIBorderColor" => "#E3E3E3",
                        "checkboxBackgroundColor" => "#FFFFFF",
                        "checkboxActiveColor" => "#FFFFFF",
                        "checkboxBorderColor" => "#ABCD05",
                        "checkboxCheckmarkColor" => "#ABCD05",
                        "radioBackgroundColor" => "#FFFFFF",
                        "radioActiveColor" => "#ABCD05",
                        "radioBorderColor" => "#999"
                      )
                    ),
                    "inputLFType" => 2,
                    "canRestore" => true
                  ),
                2 =>
                  array(
                    "variableName" => "地域",
                    "uiType" => "4",
                    "message" => "お客様のお住いのエリアをお選びください。（３／７）",
                    "required" => true,
                    "errorMessage" => "",
                    "settings" => array(
                      "options" => array(
                        "北海道",
                        "東北",
                        "関東",
                        "中部",
                        "近畿",
                        "中国",
                        "四国",
                        "九州"
                      ),
                      "disablePastDate" => true,
                      "isSetDisableDate" => false,
                      "isDisableDayOfWeek" => false,
                      "isSetSpecificDate" => false,
                      "isEnableAfterDate" => false,
                      "enableAfterDate" => null,
                      "dayOfWeekSetting" => array(
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false
                      ),
                      "setSpecificDateType" => "",
                      "specificDateData" => array(
                        ""
                      ),
                      "language" => 1,
                      "pulldownCustomDesign" => false,
                      "calendarCustomDesign" => false,
                      "customDesign" => array(
                        "borderColor" => "#ABCD05",
                        "backgroundColor" => "#FFFFFF",
                        "textColor" => "#666666",
                        "headerBackgroundColor" => "#ABCD05",
                        "headerTextColor" => "#FFFFFF",
                        "headerWeekdayBackgroundColor" => "#F6FAE6",
                        "calendarBackgroundColor" => "#FFFFFF",
                        "calendarTextColor" => "#666666",
                        "saturdayColor" => "#666666",
                        "sundayColor" => "#666666",
                        "messageAlign" => "2",
                        "buttonBackgroundColor" => "#F6FAE6",
                        "buttonTextColor" => "#007AFF",
                        "buttonAlign" => "2",
                        "buttonActiveColor" => "#D5E682",
                        "buttonBorderColor" => "#E3E3E3",
                        "titleColor" => "#333333",
                        "subTitleColor" => "#333333",
                        "arrowColor" => "#ABCD05",
                        "outBorderColor" => "#E8E7E0",
                        "inBorderColor" => "#E8E7E0",
                        "titleFontSize" => 14,
                        "subTitleFontSize" => 13,
                        "buttonUIBackgroundColor" => "#333333",
                        "buttonUITextColor" => "#F6FAE6",
                        "buttonUITextAlign" => "2",
                        "buttonUIActiveColor" => "#D5E682",
                        "buttonUIBorderColor" => "#E3E3E3",
                        "checkboxBackgroundColor" => "#FFFFFF",
                        "checkboxActiveColor" => "#FFFFFF",
                        "checkboxBorderColor" => "#ABCD05",
                        "checkboxCheckmarkColor" => "#ABCD05",
                        "radioBackgroundColor" => "#FFFFFF",
                        "radioActiveColor" => "#ABCD05",
                        "radioBorderColor" => "#999"
                      )
                    ),
                    "inputLFType" => 2,
                    "canRestore" => true
                  ),
                3 =>
                  array(
                    "variableName" => "きっかけ",
                    "uiType" => "9",
                    "message" => "当サイトを知った理由をすべて教えてください。（４／７）",
                    "required" => true,
                    "errorMessage" => "",
                    "settings" => array(
                      "options" => array(
                        "検索エンジン",
                        "インターネット広告",
                        "メールマガジン",
                        "ＳＮＳ・ブログ",
                        "比較サイト",
                        "ご家族・知人・友人からの紹介",
                        "その他"
                      ),
                      "disablePastDate" => true,
                      "isSetDisableDate" => false,
                      "isDisableDayOfWeek" => false,
                      "isSetSpecificDate" => false,
                      "isEnableAfterDate" => false,
                      "enableAfterDate" => null,
                      "isDisableAfterData" => false,
                      "dayOfWeekSetting" => array(
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false
                      ),
                      "setSpecificDateType" => "",
                      "specificDateData" => array(
                        ""
                      ),
                      "language" => 1,
                      "pulldownCustomDesign" => false,
                      "calendarCustomDesign" => false,
                      "carouselCustomDesign" => false,
                      "buttonUICustomDesign" => false,
                      "checkboxCustomDesign" => false,
                      "radioCustomDesign" => false,
                      "balloonStyle" => "1",
                      "lineUpStyle" => "1",
                      "carouselPattern" => "2",
                      "arrowType" => "4",
                      "titlePosition" => "1",
                      "subTitlePosition" => "1",
                      "outCarouselNoneBorder" => false,
                      "inCarouselNoneBorder" => false,
                      "outButtonUINoneBorder" => true,
                      "checkboxNoneBorder" => false,
                      "radioNoneBorder" => false,
                      "aspectRatio" => null,
                      "checkboxSeparator" => "3",
                      "checkboxStyle" => "1",
                      "customDesign" => array(
                        "checkboxBackgroundColor" => "#FFFFFF",
                        "checkboxActiveColor" => "#FFFFFF",
                        "checkboxBorderColor" => "#ABCD05",
                        "checkboxCheckmarkColor" => "#ABCD05",
                        "checkboxActiveTextColor" => "#333333",
                        "checkboxTextColor" => "#333333",
                        "checkboxSelectionDistance" => 4,
                        "checkboxEntireBackgroundColor" => "#D5E682",
                        "checkboxEntireActiveColor" => "#ABCD05"
                      ),
                      "images" => array(
                        array(
                          "title" => "",
                          "subTitle" => "",
                          "answer" => "",
                          "url" => ""
                        )
                      )
                    ),
                    "inputLFType" => 2,
                    "canRestore" => true
                  ),
                4 =>
                  array(
                    "variableName" => "頻度",
                    "uiType" => "3",
                    "message" => "当サイトにどれぐらいの頻度でアクセスしていますか。（５／７）",
                    "required" => true,
                    "errorMessage" => "",
                    "settings" => array(
                      "options" => array(
                        "ほぼ毎日",
                        "１週間に２～３回程度",
                        "１週間に１回程度",
                        "１か月に２～３回程度",
                        "１か月に１回程度",
                        "２～３か月に１回程度",
                        "それ以下"
                      ),
                      "disablePastDate" => true,
                      "isSetDisableDate" => false,
                      "isDisableDayOfWeek" => false,
                      "isSetSpecificDate" => false,
                      "isEnableAfterDate" => false,
                      "enableAfterDate" => null,
                      "dayOfWeekSetting" => array(
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false
                      ),
                      "setSpecificDateType" => "",
                      "specificDateData" => array(
                        ""
                      ),
                      "language" => 1,
                      "pulldownCustomDesign" => false,
                      "calendarCustomDesign" => false,
                      "radioStyle" => "1",
                      "customDesign" => array(
                        "radioBackgroundColor" => "#FFFFFF",
                        "radioActiveColor" => "#ABCD05",
                        "radioBorderColor" => "#ABCD05",
                        "radioTextColor" => "#333333",
                        "radioActiveTextColor" => "#333333",
                        "radioSelectionDistance" => 4,
                        "radioEntireBackgroundColor" => "#D5E682",
                        "radioEntireActiveColor" => "#ABCD05"
                      ),
                      "radioCustomDesign" => false
                    ),
                    "inputLFType" => 2,
                    "canRestore" => true
                  ),
                5 =>
                  array(
                    "variableName" => "満足度",
                    "uiType" => "7",
                    "message" => "当サイトについて、総合的にどのぐらい満足していますか。（６／７）",
                    "required" => true,
                    "errorMessage" => "",
                    "settings" => array(
                      "options" => array(
                        "満足",
                        "やや満足",
                        "やや不満",
                        "不満"
                      ),
                      "disablePastDate" => true,
                      "isSetDisableDate" => false,
                      "isDisableDayOfWeek" => false,
                      "isSetSpecificDate" => false,
                      "isEnableAfterDate" => false,
                      "enableAfterDate" => null,
                      "dayOfWeekSetting" => array(
                        false,
                        false,
                        false,
                        false,
                        false,
                        false,
                        false
                      ),
                      "setSpecificDateType" => "",
                      "specificDateData" => array(
                        ""
                      ),
                      "language" => 1,
                      "pulldownCustomDesign" => false,
                      "calendarCustomDesign" => false,
                      "customDesign" => array(
                        "borderColor" => "#ABCD05",
                        "backgroundColor" => "#FFFFFF",
                        "textColor" => "#666666",
                        "headerBackgroundColor" => "#ABCD05",
                        "headerTextColor" => "#FFFFFF",
                        "headerWeekdayBackgroundColor" => "#F6FAE6",
                        "calendarBackgroundColor" => "#FFFFFF",
                        "calendarTextColor" => "#666666",
                        "saturdayColor" => "#666666",
                        "sundayColor" => "#666666",
                        "messageAlign" => "1",
                        "buttonBackgroundColor" => "#F6FAE6",
                        "buttonTextColor" => "#007AFF",
                        "buttonAlign" => "2",
                        "buttonActiveColor" => "#D5E682",
                        "buttonBorderColor" => "#FFFFFF"
                      ),
                      "isCustomDesign" => true
                    ),
                    "inputLFType" => 2,
                    "canRestore" => true
                  ),
                6 =>
                  array(
                    "variableName" => "フリー入力",
                    "inputType" => "1",
                    "uiType" => "2",
                    "message" => "当サイトに対してご意見、ご要望などがございましたらご自由にご記入ください。（特にない場合は「スキップ」ボタンを押してください）（７／７）",
                    "required" => false,
                    "errorMessage" => "",
                    "inputLFType" => 2,
                    "canRestore" => true
                  ,
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
          "1" => array(
            "chatTextArea" => "2",
            "actionType" => "4",
            "messageIntervalTimeSec" => "2",
            "mailType" => "1",
            "mailTransmission" => array(
              'from_address' => 'no-reply@cv-agaru.com',
              'from_name' => 'コンバージョンあがるくん',
              'to_address' => '★★★貴社アドレスを設定して下さい★★★',
              'subject' => 'アンケート結果'
            ), // FIXME
            "mailTemplate" => array(
              'mail_type_cd' => 'CS001',
              'template' => '※このメールはコンバージョンあがるくんから自動送信されました。

ご担当者様

コンバージョンあがるくんによりメールを送信致しました。
以下のメッセージ内容をご確認下さい。

##SCENARIO_ALL_MESSAGE_BLOCK##'
            )
          ),
          "2" => array(
            'chatTextArea' => '2',
            'actionType' => '1',
            'messageIntervalTimeSec' => '2',
            'message' => 'アンケートは以上です。ご協力ありがとうございました。',
            'restore' => false,
          ),
          "3" => array(
            "actionType" => "2",
            "messageIntervalTimeSec" => "2",
            "chatTextArea" => "1",
            "hearings" => array(
              array(
                "variableName" => "戻る",
                "uiType" => "7",
                "message" => "",
                "required" => true,
                "settings" => array(
                  "options" => array(
                    "メニューに戻る"
                  ),
                  "customDesign" => array(
                    "buttonBackgroundColor" => "#F6FAE6",
                    "buttonTextColor" => "#007AFF",
                    "buttonActiveColor" => "#D5E682",
                    "buttonBorderColor" => "#E3E3E3",
                    "messageAlign" => "2",
                    "buttonAlign" => "2"
                  )
                )
              )
            ),
            "restore" => false,
            "isConfirm" => "2",
            "confirmMessage" => "",
            "success" => "",
            "cancel" => "",
            "cv" => "2"
          )
        )
      ),
      'del_flg' => 0,
      'sort' => 5,
      'relation_auto_message_index' => 11
    ),
    6 => array(
      'name' => '【サンプル】問い合わせフォーム',
      'activity' => array(
        "chatbotType" => "1",
        "scenarios" => array(
          "0" => array(
            "actionType" => "2",
            "messageIntervalTimeSec" => "2",
            "chatTextArea" => "1",
            "hearings" => array(
              array(
                "variableName" => "問い合わせ内容",
                "inputType" => 1,
                "uiType" => "2",
                "message" => "お問い合わせですね。\nどのようなお問い合わせでしょうか？",
                "required" => true,
                "errorMessage" => "",
                "inputLFType" => 2
              )
            ),
            "restore" => false,
            "isConfirm" => "2",
            "confirmMessage" => "",
            "success" => "",
            "cancel" => "",
            "cv" => "2"
          ),
          "1" => array(
            "actionType" => "1",
            "messageIntervalTimeSec" => "2",
            "chatTextArea" => "2",
            "message" => "ただいま担当の者が不在にしておりますので、改めてこちらからご連絡させて頂きます。",
            "restore" => false
          ),
          "2" => array(
            "actionType" => "1",
            "messageIntervalTimeSec" => "2",
            "chatTextArea" => "2",
            "message" => "お客様の会社名、お名前、電話番号、メールアドレスを入力して下さい。\n\n※普段お使いのメール署名をそのままコピー＆ペーストして頂く形でもご利用頂けます。",
            "restore" => false
          ),
          "3" => array(
            "actionType" => "12",
            "messageIntervalTimeSec" => "2",
            "chatTextArea" => "2",
            "multipleHearings" => array(
              array(
                "variableName" => "会社名",
                "inputType" => "1",
                "label" => "会社名",
                "required" => true
              ),
              array(
                "variableName" => "名前",
                "inputType" => "2",
                "label" => "名前",
                "required" => true
              ),
              array(
                "variableName" => "部署名",
                "inputType" => "5",
                "label" => "部署名",
                "required" => false
              ),
              array(
                "variableName" => "電話番号",
                "inputType" => "7",
                "label" => "電話番号",
                "required" => true
              ),
              array(
                "variableName" => "メールアドレス",
                "inputType" => "10",
                "label" => "メールアドレス",
                "required" => true
              )
            ),
            "restore" => false
          ),
          "4" => array(
            "actionType" => "2",
            "messageIntervalTimeSec" => "2",
            "chatTextArea" => "1",
            "hearings" => array(
              array(
                "variableName" => "追記事項",
                "inputType" => 1,
                "uiType" => "2",
                "message" => "他に追記されることがございましたら、こちらにご記入ください。\n※ 特にない場合は「なし」とご記入ください",
                "required" => true,
                "errorMessage" => "",
                "inputLFType" => 2
              )
            ),
            "restore" => false,
            "isConfirm" => "2",
            "confirmMessage" => "",
            "success" => "",
            "cancel" => "",
            "cv" => "1"
          ),
          "5" => array(
            "actionType" => "1",
            "messageIntervalTimeSec" => "2",
            "chatTextArea" => "2",
            "message" => "{{名前}}様からのお問い合わせを受付いたしました。",
            "restore" => false
          ),
          "6" => array(
            "actionType" => "4",
            "messageIntervalTimeSec" => "2",
            "chatTextArea" => "2",
            "mailType" => "3",
            "mailTransmission" => array(
              'from_address' => 'no-reply@cv-agaru.com',
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

■部署名
{{部署名}}

■お名前
{{名前}}

■電話番号
{{電話番号}}

■メールアドレス
{{メールアドレス}}

■お問い合わせ内容
{{問い合わせ内容}}

■追記事項
{{追記事項}}
---------------------------------------------------------------

────────────────────────────

★★★署名を自由に編集してください★★★

────────────────────────────'
            )
          ),
          "7" => array(
            "actionType" => "4",
            "messageIntervalTimeSec" => "2",
            "chatTextArea" => "2",
            "mailType" => "1",
            "mailTransmission" => array(
              'from_address' => 'no-reply@cv-agaru.com',
              'from_name' => 'コンバージョンあがるくん',
              'to_address' => '★★★貴社アドレスを設定して下さい★★★',
              'subject' => 'お問い合わせ通知'
            ), // FIXME
            "mailTemplate" => array(
              'mail_type_cd' => 'CS001',
              'template' => '※このメールはコンバージョンあがるくんから自動送信されました。

ご担当者様

コンバージョンあがるくんによりメールを送信致しました。
以下のメッセージ内容をご確認下さい。

##SCENARIO_ALL_MESSAGE_BLOCK##'
            )
          ),
          "8" => array(
            "actionType" => "1",
            "messageIntervalTimeSec" => "2",
            "chatTextArea" => "2",
            "message" => "この度は、お問い合わせ頂き誠にありがとうございました。",
            "restore" => false
          ),
          "9" => array(
            "actionType" => "2",
            "messageIntervalTimeSec" => "2",
            "chatTextArea" => "1",
            "hearings" => array(
              array(
                "variableName" => "メニューに戻る",
                "uiType" => "7",
                "message" => "",
                "required" => true,
                "errorMessage" => "",
                "settings" => array(
                  "options" => array(
                    "メニューに戻る"
                  ),
                  "disablePastDate" => true,
                  "isSetDisableDate" => false,
                  "isDisableDayOfWeek" => false,
                  "isSetSpecificDate" => false,
                  "isEnableAfterDate" => false,
                  "enableAfterDate" => null,
                  "isDisableAfterData" => false,
                  "dayOfWeekSetting" => array(
                    false,
                    false,
                    false,
                    false,
                    false,
                    false,
                    false
                  ),
                  "setSpecificDateType" => "",
                  "specificDateData" => array(
                    ""
                  ),
                  "language" => 1,
                  "pulldownCustomDesign" => false,
                  "calendarCustomDesign" => false,
                  "carouselCustomDesign" => false,
                  "buttonUICustomDesign" => false,
                  "checkboxCustomDesign" => false,
                  "radioCustomDesign" => false,
                  "balloonStyle" => "1",
                  "lineUpStyle" => "1",
                  "carouselPattern" => "2",
                  "arrowType" => "4",
                  "titlePosition" => "1",
                  "subTitlePosition" => "1",
                  "outCarouselNoneBorder" => false,
                  "inCarouselNoneBorder" => false,
                  "outButtonUINoneBorder" => true,
                  "checkboxNoneBorder" => false,
                  "checkboxNoneBackground" => true,
                  "checkboxStyle" => "1",
                  "radioNoneBorder" => false,
                  "radioNoneBackground" => true,
                  "radioStyle" => "1",
                  "aspectRatio" => null,
                  "checkboxSeparator" => "1",
                  "customDesign" => array(
                    "borderColor" => "#ABCD05",
                    "backgroundColor" => "#FFFFFF",
                    "textColor" => "#666666",
                    "headerBackgroundColor" => "#ABCD05",
                    "headerTextColor" => "#FFFFFF",
                    "headerWeekdayBackgroundColor" => "#F6FAE6",
                    "calendarBackgroundColor" => "#FFFFFF",
                    "calendarTextColor" => "#666666",
                    "saturdayColor" => "#666666",
                    "sundayColor" => "#666666",
                    "titleColor" => "#333333",
                    "subTitleColor" => "#333333",
                    "arrowColor" => "#ABCD05",
                    "titleFontSize" => 14,
                    "subTitleFontSize" => 13,
                    "outBorderColor" => "#E8E7E0",
                    "inBorderColor" => "#E8E7E0",
                    "messageAlign" => "2",
                    "buttonBackgroundColor" => "#F6FAE6",
                    "buttonTextColor" => "#007AFF",
                    "buttonAlign" => "2",
                    "buttonActiveColor" => "#D5E682",
                    "buttonBorderColor" => "#E3E3E3",
                    "buttonUIBackgroundColor" => "#333333",
                    "buttonUITextAlign" => "2",
                    "buttonUITextColor" => "#F6FAE6",
                    "buttonUIActiveColor" => "#D5E682",
                    "buttonUIBorderColor" => "#E3E3E3",
                    "checkboxEntireBackgroundColor" => "#D5E682",
                    "checkboxEntireActiveColor" => "#ABCD05",
                    "checkboxSelectionDistance" => 4,
                    "checkboxBackgroundColor" => "#FFFFFF",
                    "checkboxActiveColor" => "#FFFFFF",
                    "checkboxBorderColor" => "#ABCD05",
                    "checkboxCheckmarkColor" => "#ABCD05",
                    "checkboxTextColor" => "#333333",
                    "checkboxActiveTextColor" => "#333333",
                    "radioBackgroundColor" => "#FFFFFF",
                    "radioEntireBackgroundColor" => "#D5E682",
                    "radioEntireActiveColor" => "#ABCD05",
                    "radioActiveColor" => "#ABCD05",
                    "radioSelectionDistance" => 4,
                    "radioBorderColor" => "#ABCD05",
                    "radioTextColor" => "#333333",
                    "radioActiveTextColor" => "#333333"
                  ),
                  "images" => array(
                    array(
                      "title" => "",
                      "subTitle" => "",
                      "answer" => "",
                      "url" => ""
                    )
                  )
                ),
                "inputLFType" => 2
              )
            ),
            "restore" => false,
            "isConfirm" => "2",
            "confirmMessage" => "",
            "success" => "",
            "cancel" => "",
            "cv" => "2"
          )
        )
      ),
      'del_flg' => 0,
      'sort' => 6,
      'relation_auto_message_index' => 12
    )
  )
);
