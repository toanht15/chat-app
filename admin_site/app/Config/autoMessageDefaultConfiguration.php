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
        'message' => 'ただいまスタッフが待機中です！
ご不明な点などございましたら、お気軽にご質問ください。'
      ],
      'action_type' => 1,
      'active_type' => 0,
      'sort' => 3
    ],
    [
      'name' => 'ページ滞在20秒後',
      'trigger_type' => 0,
      'activity' => [
        'conditionType' => 1,
        'conditions' => [
          "1" => [
            [
              'stayTimeCheckType' => '1',
              'stayTimeType' => '1',
              'stayTimeRange' => '20'
            ]
          ]
        ],
        'widgetOpen' => 1,
        'message' => '★現在お電話でもお問合せ受付中★
オペレータに相談してみませんか？お気軽にお問合せください！
【電話受付窓口】
xx-xxx-xxx（平日9:00-18:00）
[] 電話で相談する
[] まずはチャットで相談'
      ],
      'action_type' => 1,
      'active_type' => 1,
      'sort' => 4
    ],

    [
      'name' => 'ページ滞在30秒後',
      'trigger_type' => 0,
      'activity' => [
        'conditionType' => 1,
        'conditions' => [
          "1" => [
            [
              'stayTimeCheckType' => '1',
              'stayTimeType' => '1',
              'stayTimeRange' => '30'
            ]
          ]
        ],
        'widgetOpen' => 1,
        'message' => '資料送付をご希望の際は、会社名・お名前・連絡先（メールアドレス）をご記入ください。'
      ],
      'action_type' => 1,
      'active_type' => 1,
      'sort' => 5
    ],
    [
      'name' => 'ページ滞在2分後',
      'trigger_type' => 0,
      'activity' => [
        'conditionType' => 1,
        'conditions' => [
          "1" => [
            [
              'stayTimeCheckType' => '1',
              'stayTimeType' => '2',
              'stayTimeRange' => '2'
            ]
          ]
        ],
        'widgetOpen' => 1,
        'message' => 'なにかご不明な点はございませんか？下記より選択してください。
[] ●●について
[] ●●について
[] ●●について
[] その他'
      ],
      'action_type' => 1,
      'active_type' => 1,
      'sort' => 6
    ],
  ]
];