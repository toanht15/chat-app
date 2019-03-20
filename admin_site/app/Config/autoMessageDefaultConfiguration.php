<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/08/09
 * Time: 10:07
 */
$config['default'] = array(
  'autoMessages_with_scenario' => array(
    array(
      'name' => '【サンプル】最初のあいさつ（初回訪問／3秒後）',
      'trigger_type' => 0,
      'activity' => array(
        "conditionType" => 1,
        "conditions" => array(
          "1" => array(
            array(
              "stayTimeCheckType" => "2",
              "stayTimeType" => "1",
              "stayTimeRange" => "3"
            )
          ),
          "2" => array(
            array(
              "visitCnt" => "1",
              "visitCntCond" => "1"
            )
          )
        ),
        "widgetOpen" => 1,
        "message" => "（No.1：最初の挨拶のサンプルです。）\n\n<div class=\"free-block\" style=\"color:#333333\"><span style=\"font-weight:bold;font-size:11pt\">はじめまして♪</span></div>\nsinclo（シンクロ）に興味を持っていただきありがとうございます。\n\n<div class=”free-block” style=\"color:#333333\">sincloを利用することで、このようなチャットボットを<span style=\"color:#00A0C1;font-weight:bold;font-size:11pt\"> 簡単に自社サイトに導入</span></div>することができます。",
        "chatTextarea" => 1,
        "cv" => 2
      ),
      'action_type' => 1,
      'active_type' => 0,
      'sort' => 1
    ),
    array(
      'name' => '【サンプル】最初のあいさつ（2回目以降／3秒後）',
      'trigger_type' => 0,
      'activity' => array(
        "conditionType" => 1,
        "conditions" => array(
          "1" => array(
            array(
              "stayTimeCheckType" => "2",
              "stayTimeType" => "1",
              "stayTimeRange" => "3"
            )
          ),
          "2" => array(
            array(
              "visitCnt" => "2",
              "visitCntCond" => "2"
            )
          )
        ),
        "widgetOpen" => 1,
        "message" => "（No.2：最初の挨拶のサンプルです。）\n\n<div class=\"free-block\" style=\"color:#333333\"><span style=\"font-weight:bold;font-size:11pt\">おかえりなさいませ♪</span></div>\n再訪問ありがとうございます。\n\n<div class=”free-block” style=\"color:#333333\">sincloを利用することで、このようなチャットボットを<span style=\"color:#00A0C1;font-weight:bold;font-size:11pt\"> 簡単に自社サイトに導入</span></div>することができます。",
        "chatTextarea" => 1,
        "cv" => 2
      ),
      'action_type' => 1,
      'active_type' => 0,
      'sort' => 2
    ),
    array(
      'name' => '【サンプル】メインメニュー（ページ滞在5秒後）',
      'trigger_type' => 0,
      'activity' => array(
        "conditionType" => 1,
        "conditions" => array(
          "1" => array(
            array(
              "stayTimeCheckType" => "1",
              "stayTimeType" => "1",
              "stayTimeRange" => "5"
            )
          )
        ),
        "widgetOpen" => 1,
        "message" => "（No.3：メインメニューのサンプルです。）\n\nここでsincloの設定方法を学習することができます。\n[] 基本を覚える\n[] 画像を使った設定サンプル\n[] リンクを使った設定サンプル\n[] 資料請求のサンプル\n[] 来店予約のサンプル\n[] 会員登録・入会のサンプル\n[] アンケートのサンプル",
        "chatTextarea" => 1,
        "cv" => 2
      ),
      'action_type' => 1,
      'active_type' => 0,
      'sort' => 3
    ),
    array(
      'name' => '【サンプル】（発言内容）基本を覚える',
      'trigger_type' => 0,
      'activity' => array(
        "conditionType" => 1,
        "conditions" => array(
          "7" => array(
            array(
              "keyword_contains" => "基本を覚える",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "speechContentCond" => "1",
              "triggerTimeSec" => 2,
              "speechTriggerCond" => "2"
            )
          )
        ),
        "widgetOpen" => 1,
        "message" => "（No.4：基本を覚える）\n\nこのようにユーザー操作（ユーザーの発言）に応じて自動返信（オートリプライ）することが可能です。\n\nこのメッセージは、トリガー設定の「２．条件詳細設定」にて「発言内容」を指定し、対象とするキーワードに「基本を覚える」と設定することで実現しています。\n\n※実際の設定内容はトリガー設定のNo.4を確認してください。\n\n----------------------------------------------\n[] メニューに戻る",
        "chatTextarea" => 2,
        "cv" => 2
      ),
      'action_type' => 1,
      'active_type' => 0,
      'sort' => 4
    ),
    array(
      'name' => '【サンプル】（発言内容）画像を使った設定サンプル',
      'trigger_type' => 0,
      'activity' => array(
        "conditionType" => 1,
        "conditions" => array(
          "7" => array(
            array(
              "keyword_contains" => "画像を使った設定サンプル",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "speechContentCond" => "1",
              "triggerTimeSec" => 2,
              "speechTriggerCond" => "2"
            )
          )
        ),
        "widgetOpen" => 1,
        "message" => "（No.5：画像を使った設定サンプルです。）\n\nsinclo（シンクロ）はコンタクトセンターシステムメーカーであるメディアリンクが長年培った技術力とノウハウを活かした100%自社開発（国産）のチャットボットツール（特許取得済み）です。\n<img src=\"https://sinclo.medialink-ml.co.jp/lp/images/index/features_photo01.jpg\" alt=\"sinclo（シンクロ）\" style=\"display:block;margin-left:auto;margin-right:auto;width:250px;height:auto;margin-top:10px;margin-bottom:10px\">\n「売上にインパクトを与えるコミュニケーションのあり方」を熟知している当社だからこそ、本当に効果のあるチャットボットツールを自信をもってご提供いたします。\n----------------------------------------------\n[] リンクを使った設定サンプル\n[] 資料請求のサンプル\n----------------------------------------------\n[] メニューに戻る",
        "chatTextarea" => 2,
        "cv" => 2
      ),
      'action_type' => 1,
      'active_type' => 0,
      'sort' => 5
    ),
    array(
      'name' => '【サンプル】（発言内容）リンクを使った設定サンプル',
      'trigger_type' => 0,
      'activity' => array(
        "conditionType" => 1,
        "conditions" => array(
          "7" => array(
            array(
              "keyword_contains" => "リンクを使った設定サンプル",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "speechContentCond" => "1",
              "triggerTimeSec" => 2,
              "speechTriggerCond" => "2"
            )
          )
        ),
        "widgetOpen" => 2,
        "message" => "（No.6：リンクを使った設定サンプルです。）\n\n無料トライアルをご希望ですね。\n下記ページからお申し込みください。\n\n<a href=\"https://sinclo.medialink-ml.co.jp/lp/trial.php\" target=\"_blank\"style=\"display:inline-block;width:290px;text-align:center;font-weight:bold;text-decoration:none;background:#ABCD05;color:#FFFFFF;padding:10px;border-radius:5px;\">無料トライアル申し込み</a>",
        "chatTextarea" => 2,
        "cv" => 2
      ),
      'action_type' => 1,
      'active_type' => 0,
      'sort' => 6
    ),
    array(
      'name' => '【サンプル】（発言内容）資料請求のサンプル',
      'trigger_type' => 0,
      'activity' => array(
        "conditionType" => 1,
        "conditions" => array(
          "7" => array(
            array(
              "keyword_contains" => "資料請求のサンプル",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "speechContentCond" => "1",
              "triggerTimeSec" => 2,
              "speechTriggerCond" => "2"
            )
          )
        ),
        "widgetOpen" => 1,
        "message" => "（No.7：資料請求のサンプル）\n\n資料請求ですね。\n\nそれでは、こちらにお客様のご連絡先（会社名やお名前、メールアドレスなど）を入力していただきます。\n\nまずは入力方法を下記からお選びください。\n※普段お使いのメール署名をそのままコピー＆ペーストする場合は【一括入力】をお選びください。\n[] 連絡先をまとめて入力する（一括入力）\n[] 連絡先を１つずつ入力する（個別入力）",
        "chatTextarea" => 2,
        "cv" => 2
      ),
      'action_type' => 1,
      'message' => '',
      'active_type' => 0,
      'sort' => 7
    ),
    array(
      'name' => '【サンプル】（発言内容）連絡先をまとめて入力する（一括入力）',
      'trigger_type' => 0,
      'activity' => array(
        "conditionType" => 1,
        "conditions" => array(
          "7" => array(
            array(
              "keyword_contains" => "連絡先をまとめて入力する（一括入力）",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "speechContentCond" => "1",
              "triggerTimeSec" => 2,
              "speechTriggerCond" => "2"
            )
          )
        ),
        "widgetOpen" => 1,
        "message" => "",
        "chatTextarea" => 1,
        "cv" => 2
      ),
      'action_type' => 2,
      't_chatbot_scenario_id' => 0,
      'active_type' => 0,
      'sort' => 8
    ),
    array(
      'name' => '【サンプル】（発言内容）連絡先を１つずつ入力する（個別入力）',
      'trigger_type' => 0,
      'activity' => array(
        'conditionType' => 1,
        'conditions' => array(
          "7" => array(
            array(
              "keyword_contains" => "連絡先を１つずつ入力する（個別入力）",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "speechContentCond" => "1",
              "triggerTimeSec" => 2,
              "speechTriggerCond" => "2"
            )
          )
        ),
        "widgetOpen" => 1,
        "message" => "",
        "chatTextarea" => 1,
        "cv" => 2
      ),
      'action_type' => 2,
      't_chatbot_scenario_id' => 0,
      'active_type' => 0,
      'sort' => 9
    ),
    array(
      'name' => '【サンプル】（発言内容）来店予約のサンプル',
      'trigger_type' => 0,
      'activity' => array(
        "conditionType" => 1,
        "conditions" => array(
          "7" => array(
            array(
              "keyword_contains" => "来店予約のサンプル",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "speechContentCond" => "1",
              "triggerTimeSec" => 2,
              "speechTriggerCond" => "2"
            )
          )
        ),
        "widgetOpen" => 2,
        "message" => "",
        "chatTextarea" => 1,
        "cv" => 2
      ),
      'action_type' => 2,
      't_chatbot_scenario_id' => 0,
      'active_type' => 0,
      'sort' => 10
    ),
    array(
      'name' => '【サンプル】（発言内容）会員登録・入会のサンプル',
      'trigger_type' => 0,
      'activity' => array(
        "conditionType" => 1,
        "conditions" => array(
          "7" => array(
            array(
              "keyword_contains" => "会員登録・入会のサンプル",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "speechContentCond" => "1",
              "triggerTimeSec" => 2,
              "speechTriggerCond" => "2"
            )
          )
        ),
        "widgetOpen" => 2,
        "message" => "",
        "chatTextarea" => 1,
        "cv" => 2
      ),
      'action_type' => 2,
      't_chatbot_scenario_id' => 0,
      'active_type' => 0,
      'sort' => 11
    ),
    array(
      'name' => '【サンプル】（発言内容）アンケートのサンプル',
      'trigger_type' => 0,
      'activity' => array(
        "conditionType" => 1,
        "conditions" => array(
          "7" => array(
            array(
              "keyword_contains" => "アンケートのサンプル",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "speechContentCond" => "1",
              "triggerTimeSec" => 2,
              "speechTriggerCond" => "2"
            )
          )
        ),
        "widgetOpen" => 2,
        "message" => "",
        "chatTextarea" => 1,
        "cv" => 2
      ),
      'action_type' => 2,
      't_chatbot_scenario_id' => 0,
      'active_type' => 0,
      'sort' => 12
    ),
    array(
      'name' => '【サンプル】（発言内容）メニューに戻る',
      'trigger_type' => 0,
      'activity' => array(
        "conditionType" => 1,
        "conditions" => array(
          "7" => array(
            array(
              "keyword_contains" => "メニューに戻る",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "speechContentCond" => "1",
              "triggerTimeSec" => 2,
              "speechTriggerCond" => "2"
            )
          )
        ),
        "widgetOpen" => 1,
        "message" => "",
        "chatTextarea" => 1,
        "cv" => 2
      ),
      'action_type' => 3,
      'active_type' => 0,
      'target_automessage_index' => 2,
      'sort' => 13
    )
  ),
  'autoMessages_without_scenario' => array(
    array(
      'name' => '初回訪問3秒後',
      'trigger_type' => 0,
      'activity' => array(
        'conditionType' => 1,
        'conditions' => array(
          "1" => array(
            array(
              'stayTimeCheckType' => '2',
              'stayTimeType' => '1',
              'stayTimeRange' => '3'
            )
          ),
          "2" => array(
            array(
              'visitCnt' => '1',
              'visitCntCond' => '1'
            )
          )
        ),
        'widgetOpen' => 1,
        'message' => 'ご訪問誠にありがとうございます。',
        "chatTextarea" => 2,
        "cv" => 2
      ),
      'action_type' => 1,
      'active_type' => 0,
      'sort' => 1
    ),
    array(
      'name' => '2回目以降訪問3秒後',
      'trigger_type' => 0,
      'activity' => array(
        'conditionType' => 1,
        'conditions' => array(
          "1" => array(
            array(
              'stayTimeCheckType' => '2',
              'stayTimeType' => '1',
              'stayTimeRange' => '3'
            )
          ),
          "2" => array(
            array(
              'visitCnt' => '2',
              'visitCntCond' => '2'
            )
          )
        ),
        'widgetOpen' => 1,
        'message' => '再訪問ありがとうございます。',
        "chatTextarea" => 2,
        "cv" => 2
      ),
      'action_type' => 1,
      'active_type' => 0,
      'sort' => 2
    ),
    array(
      'name' => 'ページ滞在10秒後',
      'trigger_type' => 0,
      'activity' => array(
        'conditionType' => 1,
        'conditions' => array(
          "1" => array(
            array(
              'stayTimeCheckType' => '1',
              'stayTimeType' => '1',
              'stayTimeRange' => '10'
            )
          )
        ),
        'widgetOpen' => 1,
        'message' => 'なにかお困りのことはございませんか？
[] Aについて
[] Bについて
[] 資料請求
[] その他',
        "chatTextarea" => 1,
        "cv" => 2
      ),
      'action_type' => 1,
      'active_type' => 0,
      'sort' => 3
    ),
    array(
      'name' => '発言内容（Aについて）',
      'trigger_type' => 0,
      'activity' => array(
        'conditionType' => 1,
        'conditions' => array(
          "7" => array(
            array(
              "keyword_contains" => "Aについて",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "speechContentCond" => "1",
              "triggerTimeSec" => 2,
              "speechTriggerCond" => "2"
            )
          )
        ),
        'widgetOpen' => 1,
        "message" => "Aについてですね。
Aは・・・

--------------------------------------------------
[] メニューに戻る",
        "chatTextarea" => 2,
        "cv" => 2
      ),
      'action_type' => 1,
      'active_type' => 0,
      'sort' => 4
    ),
    array(
      'name' => '発言内容（Bについて）',
      'trigger_type' => 0,
      'activity' => array(
        'conditionType' => 1,
        'conditions' => array(
          "7" => array(
            array(
              "keyword_contains" => "Bについて",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "speechContentCond" => "1",
              "triggerTimeSec" => 2,
              "speechTriggerCond" => "2"
            )
          )
        ),
        'widgetOpen' => 1,
        "message" => "Bについてですね。
Bは・・・

--------------------------------------------------
[] メニューに戻る",
        "chatTextarea" => 2,
        "cv" => 2
      ),
      'action_type' => 1,
      'active_type' => 0,
      'sort' => 5
    ),
    array(
      'name' => '発言内容（資料請求）',
      'trigger_type' => 0,
      'activity' => array(
        'conditionType' => 1,
        'conditions' => array(
          "7" => array(
            array(
              "keyword_contains" => "資料請求",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "speechContentCond" => "1",
              "triggerTimeSec" => 2,
              "speechTriggerCond" => "2"
            )
          )
        ),
        "widgetOpen" => 1,
        "message" => "資料請求ですね。
それではこちらに会社名とお名前、メールアドレスを入力して下さい。

※普段お使いのメール署名をコピー＆ペーストして頂く形で構いません",
        "chatTextarea" => 1,
        "cv" => 2
      ),
      'action_type' => 1,
      't_chatbot_scenario_id' => 0,
      'active_type' => 0,
      'sort' => 6
    ),
    array(
      'name' => '発言内容（その他）',
      'trigger_type' => 0,
      'activity' => array(
        'conditionType' => 1,
        'conditions' => array(
          "7" => array(
            array(
              "keyword_contains" => "その他",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "speechContentCond" => "1",
              "triggerTimeSec" => 2,
              "speechTriggerCond" => "2"
            )
          )
        ),
        "widgetOpen" => 1,
        "message" => "その他のお問い合わせですね。
どのようなお問い合わせでしょうか？",
        "chatTextarea" => 1,
        "cv" => 2
      ),
      'action_type' => 1,
      'active_type' => 0,
      'sort' => 7
    ),
    array(
      'name' => '発言内容（メニューに戻る）',
      'trigger_type' => 0,
      'activity' => array(
        'conditionType' => 1,
        'conditions' => array(
          "7" => array(
            array(
              "keyword_contains" => "メニューに戻る",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "speechContentCond" => "1",
              "triggerTimeSec" => 2,
              "speechTriggerCond" => "2"
            )
          )
        ),
        'widgetOpen' => 1,
        "message" => "なにかお困りのことはございませんか？
[] Aについて
[] Bについて
[] 資料請求
[] その他",
        "chatTextarea" => 1,
        "cv" => 2
      ),
      'action_type' => 1,
      'active_type' => 0,
      'sort' => 8
    ),
    array(
      'name' => '発言内容（はい、すべて入力しました。）',
      'trigger_type' => 0,
      'activity' => array(
        'conditionType' => 1,
        'conditions' => array(
          "7" => array(
            array(
              "keyword_contains" => "はい、すべて入力しました。",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "speechContentCond" => "1",
              "triggerTimeSec" => 2,
              "speechTriggerCond" => "2"
            )
          )
        ),
        'widgetOpen' => 1,
        "message" => "ありがとうございます。

担当の者から改めてご連絡させていただきます。

この度はお問い合わせ誠にありがとうございました！

--------------------------------------------------
[] メニューに戻る",
        "chatTextarea" => 2,
        "cv" => 1
      ),
      'action_type' => 1,
      'active_type' => 0,
      'sort' => 9
    ),
    array(
      'name' => '発言内容（いいえ、入力していません。）',
      'trigger_type' => 0,
      'activity' => array(
        'conditionType' => 1,
        'conditions' => array(
          "7" => array(
            array(
              "keyword_contains" => "いいえ、入力していません。",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "speechContentCond" => "1",
              "triggerTimeSec" => 2,
              "speechTriggerCond" => "2"
            )
          )
        ),
        'widgetOpen' => 1,
        "message" => "こちらからの連絡を希望しますか？

[] はい、連絡を希望します。
[] いいえ、結構です。",
        "chatTextarea" => 2,
        "cv" => 2
      ),
      'action_type' => 1,
      'active_type' => 0,
      'sort' => 10
    ),
    array(
      'name' => '発言内容（はい、連絡を希望します。）',
      'trigger_type' => 0,
      'activity' => array(
        'conditionType' => 1,
        'conditions' => array(
          "7" => array(
            array(
              "keyword_contains" => "はい、連絡を希望します。",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "speechContentCond" => "1",
              "triggerTimeSec" => 2,
              "speechTriggerCond" => "2"
            )
          )
        ),
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
      ),
      'action_type' => 1,
      'active_type' => 0,
      'sort' => 11
    ),
    array(
      'name' => '発言内容（いいえ、結構です。）',
      'trigger_type' => 0,
      'activity' => array(
        'conditionType' => 1,
        'conditions' => array(
          "7" => array(
            array(
              "keyword_contains" => "いいえ、結構です。",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "speechContentCond" => "1",
              "triggerTimeSec" => 2,
              "speechTriggerCond" => "2"
            )
          )
        ),
        'widgetOpen' => 1,
        "message" => "承知いたしました。

それでは●時～●時の間に再度お問い合わせ頂くか、下記フォームからお問い合わせください。
●フォームのURLを記載●

--------------------------------------------------
[] メニューに戻る",
        "chatTextarea" => 2,
        "cv" => 2
      ),
      'action_type' => 1,
      'active_type' => 0,
      'sort' => 12
    ),
  )
);
