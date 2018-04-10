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
            "message" => "資料請求ですね。"
          ),
          "1" => array(
            "chatTextArea" => "1",
            "actionType" => "2",
            "messageIntervalTimeSec" => "2",
            "hearings" => array(
              array(
                "variableName" => "会社名",
                "inputType" => "1",
                "message" => "会社名を入力して下さい。",
                "inputLFType" => "1"
              ),
              array(
                "variableName" => "名前",
                "inputType" => "1",
                "message" => "お名前を入力して下さい。",
                "inputLFType" => "1"
              ),
              array(
                "variableName" => "電話番号",
                "inputType" => "4",
                "message" => "電話番号を入力して下さい。",
                "inputLFType" => "1"
              ),
              array(
                "variableName" => "メールアドレス",
                "inputType" => "3",
                "message" => "メールアドレスを入力して下さい。",
                "inputLFType" => "1"
              ),
              array(
                "variableName" => "その他",
                "inputType" => "1",
                "message" => "その他ご要望などがあれば記載ください。（特にない場合は「なし」と入力してください）",
                "inputLFType" => "2"
              )
            ),
            "errorMessage" => "入力が正しく確認できませんでした。",
            "isConfirm" => "1",
            "confirmMessage" => "会社名　　　　：{{会社名}}\nお名前　　　　：{{名前}}\n電話番号　　　：{{電話番号}}\nメールアドレス：{{メールアドレス}}\nその他ご要望　：{{その他}}\n\nでよろしいでしょうか？",
            "success" => "はい",
            "cancel" => "いいえ",
            "cv" => "1",
            "cvCondition" => 1
          ),
          "2" => array(
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
              array(
                "variableName" => "顧客情報",
                "inputType" => "1",
                "message" => "お客様の会社名、お名前、電話番号、メールアドレスを入力して下さい。（メール署名をコピー＆ペーストで可）",
                "inputLFType" => "2"
              )
            ),
            "errorMessage" => "入力が正しく確認できませんでした。",
            "isConfirm" => "1",
            "confirmMessage" => "お客様の会社名、お名前、電話番号、メールアドレスはすべて入力頂けましたか？",
            "success" => "はい、すべて入力しました",
            "cancel" => "いいえ、入力していません",
            "cv" => "1",
            "cvCondition" => 1
          ),
          "2" => array(
            "chatTextArea" => "1",
            "actionType" => "2",
            "messageIntervalTimeSec" => "2",
            "hearings" => array(
              array(
                "variableName" => "その他要望など",
                "inputType" => "1",
                "message" => "その他ご要望などございましたらこちらにご記入ください。（特にない方は「なし」と入力してください。）",
                "inputLFType" => "2"
              )
            ),
            "errorMessage" => "入力が確認できませんでした。",
            "isConfirm" => "2",
            "confirmMessage" => "",
            "success" => "",
            "cancel" => "",
            "cv" => "2",
            "cvCondition" => 1
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
          )
        )
      ),
      'del_flg' => 0,
      'sort' => 2
    ),
    3 => array(
      'name' => '【サンプル】会員登録・入会 ',
      'activity' => array(
        "chatbotType" => "1",
        "scenarios" => array(
          "0" => array(
            "chatTextArea" => "2",
            "actionType" => "1",
            "messageIntervalTimeSec" => "2",
            "message" => "会員登録（入会）ですね。"
          ),
          "1" => array(
            "chatTextArea" => "2",
            "actionType" => "3",
            "messageIntervalTimeSec" => "2",
            "selection" => array(
              "variableName" => "会員コース",
              "options" => array(
                "Ａコース（月額9,800円）",
                "Ｂコース（月額12,800円）",
                "Ｃコース（月額19,800円）"
              )
            ),
            "message" => "会員コースをお選びください。"
          ),
          "2" => array(
            "chatTextArea" => "2",
            "actionType" => "1",
            "messageIntervalTimeSec" => "2",
            "message" => "{{会員コース}}ですね。"
          ),
          "3" => array(
            "chatTextArea" => "2",
            "actionType" => "1",
            "messageIntervalTimeSec" => "2",
            "message" => "続いて、お客様情報をご入力いただきます。"
          ),
          "4" => array(
            "chatTextArea" => "1",
            "actionType" => "2",
            "messageIntervalTimeSec" => "2",
            "hearings" => array(
              array(
                "variableName" => "名前",
                "inputType" => "1",
                "message" => "お客様のお名前をフルネーム（漢字）で入力して下さい。",
                "inputLFType" => "1"
              ),
              array(
                "variableName" => "カナ",
                "inputType" => "1",
                "message" => "お客様のお名前のフリガナを入力して下さい。",
                "inputLFType" => "1"
              ),
              array(
                "variableName" => "生年月日",
                "inputType" => "2",
                "message" => "生年月日を8桁で入力してください。（例：1990年1月1日生まれの場合 → 19900101）",
                "inputLFType" => "1"
              ),
              array(
                "variableName" => "住所",
                "inputType" => "1",
                "message" => "住所を郵便番号から入力してください。",
                "inputLFType" => "2"
              ),
              array(
                "variableName" => "電話番号",
                "inputType" => "4",
                "message" => "電話番号を入力してください。",
                "inputLFType" => "1"
              ),
              array(
                "variableName" => "メールアドレス",
                "inputType" => "3",
                "message" => "メールアドレスを入力してください。",
                "inputLFType" => "1"
              )
            ),
            "errorMessage" => "入力が正しく確認できませんでした。",
            "isConfirm" => "1",
            "confirmMessage" => "お名前　　　　：{{名前}}（{{カナ}}）\n生年月日　　　：{{生年月日}}\n住所　　　　　：{{住所}}\n電話番号　　　：{{電話番号}}\nメールアドレス：{{メールアドレス}}\n\nでよろしいでしょうか？",
            "success" => "はい",
            "cancel" => "いいえ",
            "cv" => "2",
            "cvCondition" => 1
          ),
          "5" => array(
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
          "6" => array(
            "chatTextArea" => "2",
            "actionType" => "1",
            "messageIntervalTimeSec" => "2",
            "message" => "{{名前}}様からの会員登録（入会）を受付いたしました。\n\nこの度はご入会いただきありがとうございました。"
          )
        )
      ),
      'del_flg' => 0,
      'sort' => 3
    ),
    4 => array(
      'name' => '【サンプル】アンケート',
      'activity' => array(
        "chatbotType" => "1",
        "scenarios" => array(
          "0" => array(
            "chatTextArea" => "2",
            "actionType" => "3",
            "messageIntervalTimeSec" => "2",
            "selection" => array(
              "variableName" => "性別",
              "options" => array(
                "男性",
                "女性"
              )
            ),
            "message" => "お客様の性別をお選びください。（１／７）"
          ),
          "1" => array(
            "chatTextArea" => "2",
            "actionType" => "3",
            "messageIntervalTimeSec" => "2",
            "selection" => array(
              "variableName" => "年代",
              "options" => array(
                "10代",
                "20代",
                "30代",
                "40代",
                "50代",
                "60代以上"
              )
            ),
            "message" => "お客様の年齢をお選びください。（２／７）"
          ),
          "2" => array(
            "chatTextArea" => "2",
            "actionType" => "3",
            "messageIntervalTimeSec" => "2",
            "selection" => array(
              "variableName" => "地域",
              "options" => array(
                "北海道",
                "東北（青森/岩手/秋田/宮城/山形/福島）",
                "関東（茨城/栃木/群馬/埼玉/千葉/東京/神奈川）",
                "中部（新潟/富山/石川/福井/山梨/長野/岐阜/静岡/愛知）",
                "近畿（三重/滋賀/奈良/和歌山/京都/大阪/兵庫）",
                "中国（岡山/広島/鳥取/島根/山口）",
                "四国（香川/徳島/愛媛/高知）",
                "九州（福岡/佐賀/長崎/大分/熊本/宮崎/鹿児島/沖縄）"
              )
            ),
            "message" => "お客様のお住いのエリアをお選びください。（３／７）"
          ),
          "3" => array(
            "chatTextArea" => "2",
            "actionType" => "3",
            "messageIntervalTimeSec" => "2",
            "selection" => array(
              "variableName" => "きっかけ",
              "options" => array(
                "検索エンジン",
                "インターネット広告",
                "メールマガジン",
                "ＳＮＳ・ブログ",
                "比較サイト",
                "ご家族・知人・友人からの紹介",
                "その他"
              )
            ),
            "message" => "当サイトをどのようにして知りましたか。（４／７）"
          ),
          "4" => array(
            "chatTextArea" => "2",
            "actionType" => "3",
            "messageIntervalTimeSec" => "2",
            "selection" => array(
              "variableName" => "頻度",
              "options" => array(
                "ほぼ毎日",
                "１週間に２～３回程度",
                "１週間に１回程度",
                "１か月に２～３回程度",
                "１か月に１回程度",
                "２～３か月に１回程度",
                "それ以下"
              )
            ),
            "message" => "当サイトにどれぐらいの頻度でアクセスしていますか。（５／７）"
          ),
          "5" => array(
            "chatTextArea" => "2",
            "actionType" => "3",
            "messageIntervalTimeSec" => "2",
            "selection" => array(
              "variableName" => "満足度",
              "options" => array(
                "満足",
                "やや満足",
                "やや不満",
                "不満"
              )
            ),
            "message" => "当サイトについて、総合的にどのぐらい満足していますか。（６／７）"
          ),
          "6" => array(
            "chatTextArea" => "1",
            "actionType" => "2",
            "messageIntervalTimeSec" => "2",
            "hearings" => array(
              array(
                "variableName" => "フリー入力",
                "inputType" => "1",
                "message" => "当サイトに対してご意見、ご要望などがございましたらご自由にご記入ください。（特にない場合は「なし」とご記入ください）（７／７）",
                "inputLFType" => "2"
              )
            ),
            "errorMessage" => "入力が確認できませんでした。",
            "isConfirm" => "2",
            "confirmMessage" => "",
            "success" => "",
            "cancel" => "",
            "cv" => "2",
            "cvCondition" => 1
          ),
          "7" => array(
            "chatTextArea" => "2",
            "actionType" => "1",
            "messageIntervalTimeSec" => "2",
            "message" => "アンケートは以上です。ご協力ありがとうございました。"
          )
        )
      ),
      'del_flg' => 0,
      'sort' => 4

    ),
    5 => array(
      'name' => '【サンプル】問い合わせフォーム',
      'activity' => array(
        "chatbotType" => "1",
        "scenarios" => array(
          "0" => array(
            "chatTextArea" => "2",
            "actionType" => "1",
            "messageIntervalTimeSec" => "1",
            "message" => "その他のお問い合わせですね。"
          ),
          "1" => array(
            "chatTextArea" => "1",
            "actionType" => "2",
            "messageIntervalTimeSec" => "1",
            "hearings" => array(
              array(
                "variableName" => "会社名",
                "inputType" => "1",
                "message" => "お客様の会社名を入力して下さい。",
                "inputLFType" => "1"
              ),
              array(
                "variableName" => "名前",
                "inputType" => "1",
                "message" => "お名前を入力して下さい。",
                "inputLFType" => "1"
              ),
              array(
                "variableName" => "電話番号",
                "inputType" => "4",
                "message" => "電話番号を入力して下さい。",
                "inputLFType" => "1"
              ),
              array(
                "variableName" => "メールアドレス",
                "inputType" => "3",
                "message" => "メールアドレスを入力して下さい。",
                "inputLFType" => "1"
              ),
              array(
                "variableName" => "問い合わせ内容",
                "inputType" => "1",
                "message" => "お問い合わせ内容を記入してください。",
                "inputLFType" => "2"
              )
            ),
            "errorMessage" => "入力が正しく確認できませんでした。",
            "isConfirm" => "1",
            "confirmMessage" => "会社名　　　　　　：{{会社名}}\nお名前　　　　　　：{{名前}}\n電話番号　　　　　：{{電話番号}}\nメールアドレス　　：{{メールアドレス}}\nお問い合わせ内容　：{{問い合わせ内容}}\n\nでよろしいでしょうか？",
            "success" => "はい",
            "cancel" => "いいえ",
            "cv" => "1",
            "cvCondition" => 1
          ),
          "2" => array(
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
          "3" => array(
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
          "4" => array(
            "chatTextArea" => "2",
            "actionType" => "1",
            "messageIntervalTimeSec" => "1",
            "message" => "{{名前}}様からのお問い合わせを受付いたしました。"
          ),
          "5" => array(
            "chatTextArea" => "2",
            "actionType" => "1",
            "messageIntervalTimeSec" => "1",
            "message" => "この度は、お問い合わせ頂き誠にありがとうございました。"
          )
        )
      ),
      'del_flg' => 0,
      'sort' => 5,
      'relation_auto_message_index' => 6
    )
  )
);