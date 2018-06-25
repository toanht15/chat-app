<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/08/15
 * Time: 10:07
 */

$config['default'] = [
  'chat' => [
    'basic_with_scenario' => [
      'sc_flg' => 2,
      'in_flg' => 1,
      'sc_default_num' => 2,
      'outside_hours_sorry_message' => 'お問い合わせありがとうございます。
申し訳ございませんが、只今営業時間外でございます。
恐れ入りますが営業時間内に改めてお問い合わせください。
【営業時間】
平日9時～18時（土日祝日を除く）',
      'wating_call_sorry_message' => 'お問い合わせありがとうございます。
申し訳ございませんが、ただ今混み合っておりお答えすることができません。
しばらく経ってからチャット頂くか、下記よりお問い合わせください。
≪　お問い合わせフォーム等のURL　≫',
      'no_standby_sorry_message' => 'お問い合わせありがとうございます。
申し訳ございませんが、ただ今担当の者が席を外しております。
しばらく経ってから再度お問い合わせください。',
      'initial_notification_message' => ["0 " => ["seconds"=>"1","message"=>"ただいま担当の者を呼び出しておりますので、そのままでお待ちください。"]],
    ],
    'basic_without_scenario' => [
      'sc_flg' => 2,
      'in_flg' => 1,
      'sc_default_num' => 2,
      'outside_hours_sorry_message' => 'お問い合わせありがとうございます。
申し訳ございませんが、只今営業時間外となりますので、後ほど担当よりご連絡させて頂きます。

お客様のご連絡先（会社名、お名前、メールアドレス）はすべてご入力いただけましたでしょうか？

[] はい、すべて入力しました。
[] いいえ、入力していません。',
      'wating_call_sorry_message' => 'お問い合わせありがとうございます。
申し訳ございませんが、ただ今混み合っておりお答えすることができませんので、後ほど担当よりご連絡させて頂きます。

お客様のご連絡先（会社名、お名前、メールアドレス）はすべてご入力いただけましたでしょうか？

[] はい、すべて入力しました。
[] いいえ、入力していません。',
      'no_standby_sorry_message' => 'お問い合わせありがとうございます。
申し訳ございませんが、ただ今混み合っておりお答えすることができませんので、後ほど担当よりご連絡させて頂きます。

お客様のご連絡先（会社名、お名前、メールアドレス）はすべてご入力いただけましたでしょうか？

[] はい、すべて入力しました。
[] いいえ、入力していません。',
      'initial_notification_message' => ["0 " => ["seconds"=>"1","message"=>"ただいま担当の者を呼び出しておりますので、そのままでお待ちください。"]],
    ]
  ]
];