<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/08/09
 * Time: 10:07
 */
$config['default'] = [
  'autoMessages' => [
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
        'message' => 'ご訪問誠にありがとうございます。'
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
        'message' => '再訪問ありがとうございます。'
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
      'active_type' => 1,
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
      'active_type' => 1,
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
        "message" => "",
        "chatTextarea" => 1,
        "cv" => 2
      ],
      'action_type' => 2,
      't_chatbot_scenario_id' => 0,
      'active_type' => 1,
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
        "message" => "",
        "chatTextarea" => 1,
        "cv" => 2
      ],
      'action_type' => 2,
      't_chatbot_scenario_id' => 0,
      'active_type' => 1,
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
        "chatTextarea" => 2,
        "cv" => 2
      ],
      'action_type' => 1,
      'active_type' => 1,
      'sort' => 8
    ],
  ]
];