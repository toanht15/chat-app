<?php
App::uses('AppModel', 'Model');
/**
 * MWidgetSetting Model
 *
 * @property MCompanies $MCompanies
 */
class MWidgetSetting extends AppModel {

    public $name = "MWidgetSetting";

    public $styleColumns = [
      'show_time' => "showTime",
      'max_show_time' => "maxShowTime",
      'show_position' => "showPosition",
      'title' => "title",
      'sub_title' => "subTitle",
      'description' => "description",
      'main_color' => "mainColor",
      'show_main_image' => "showMainImage",
      'main_image' => "mainImage",
      'radius_ratio' => "radiusRatio",
      'tel' => "tel",
      'show_subtitle' => "showSubtitle",
      'show_description' => "showDescription",
      'display_time_flg' => "displayTimeFlg",
      'time_text' => "timeText",
      'content' => "content"
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'max_show_time' => [
            'numeric' => [
              'rule' => 'numeric',
              'allowEmpty' => false,
              'message' => '数値を入力してください'
            ],
            'numberRange' => [
              'rule' => '/^(0[1-9]|[1-9]|[1-5][0-9]|60)$/',
              'allowEmpty' => false,
              'message' => '１～６０秒の間で設定してください'
            ]
        ],
        'sub_title' => [
            'maxLength' => [
              'rule' => ['maxLength', 15],
              'allowEmpty' => false,
              'message' => '１５文字以内で設定してください'
            ]
        ],
        'description' => [
            'maxLength' => [
              'rule' => ['maxLength', 15],
              'allowEmpty' => false,
              'message' => '１５文字以内で設定してください'
            ]
        ],
        'main_color' => [
            'colorcode' => [
              'rule' => C_MATCH_RULE_COLOR_CODE,
              'allowEmpty' => false,
              'message' => '３ケタ、もしくは６ケタの１６進数を使用して設定してください'
            ]
        ],
        'main_image' => [ // ギャラリーから選択
            'setImage' => [
              'rule' => 'setImage',
              'message' => '画像を選択してください'
            ],
        ],
        'uploadImage' => [
            // ルール：extension => pathinfoを使用して拡張子を検証
            'extension' => [
                'rule' => [ 'extension', [ 'jpg', 'jpeg', 'png'] ],
                'allowEmpty' => true,
                'message' => ["無効なファイル形式です"]
            ],
            // ルール：mimeType =>
            // finfo_file(もしくは、mime_content_type)でファイルのmimeを検証 (2.2 以降)
            // 2.5 以降 - MIMEタイプを正規表現(文字列)で設定可能に
            // 'mimetype' => [
            //     'rule' => [ 'mimeType', ['image/jpeg', 'image/png'] ],
            //     'message' => ["無効なファイル形式です"]
            // ],
            // ルール：fileSize => filesizeでファイルサイズを検証(2GBまで)  (2.3 以降)
            'size' => [
                'maxFileSize' => [
                    'rule' => [ 'fileSize', '<=', '2MB'],  // 2M以下
                    'allowEmpty' => true,
                    'message' => [ 'ファイルサイズが大きすぎます']
                ],
                'minFileSize' => [
                    'rule' => ['fileSize', '>',  0],
                    'allowEmpty' => true,
                    'message' => ['無効なファイルです']
                ],
            ],
        ],
        'radius_ratio' => [
            'between' => [
              'rule' => ['between', 1, 15],
              'message' => '１～１５の間で設定してください'
            ]
        ],
        'title' => [
            'maxLength' => [
                'rule' => ['maxLength', 12],
                'allowEmpty' => false,
                'message' => '１２文字以内で設定してください。'
            ]
        ],
        'tel' => [
            'tel' => [
                'rule' => ['custom', C_MATCH_RULE_TEL],
                'allowEmpty' => false,
                'message' => '半角数字とプラスとハイフンのみ使用できます'
            ],
            'maxlength' => [
                'rule' => ['maxLength', 13],
                'allowEmpty' => true,
                'message' => '１３文字以内で設定してください。'
            ]
        ],
        'content' => [
            'maxlength' => [
                'rule' => ['maxLength', 100],
                'allowEmpty' => true,
                'message' => '１００文字以内で設定してください。'
            ]
        ],
        'time_text' => [
            'maxlength' => [
                'rule' => ['maxLength', 15],
                'allowEmpty' => false,
                'message' => '１５文字以内で設定してください。'
            ]
        ]
    ];

    public function setImage($value=['main_image' => '']){
        if (isset($this->data['MWidgetSetting']['show_main_image']) && strcmp($this->data['MWidgetSetting']['show_main_image'], 1) === 0) {
            // 画像をギャラリーから選択や、アップロードをされていない場合
            if ( empty($value['main_image']) && empty($value['uploadImage']) ) {
                return false;
            }
        }
        return true;
    }

}

