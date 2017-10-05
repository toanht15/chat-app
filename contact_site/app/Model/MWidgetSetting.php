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
      'show_timing' => "showTiming",
      'max_show_timing_site' => "maxShowTimingSite",
      'max_show_timing_page' => "maxShowTimingPage",
      'show_time' => "showTime",
      'max_show_time' => "maxShowTime",
      'max_show_time_page' => "maxShowTimePage",
      'show_position' => "showPosition",
      //ウィジットサイズ対応
      'widget_size_type' => "widgetSizeType",
      //ウィジットサイズ対応
      'title' => "title",
      'show_subtitle' => "showSubtitle",
      'sub_title' => "subTitle",
      'description' => "description",

      /* カラー設定styat */
      //0.通常設定・高度設定
      'color_setting_type' => "colorSettingType",
      //1.メインカラー
      'main_color' => "mainColor",
      //2.タイトル文字色
      'string_color' => "stringColor",
      //3.吹き出し文字色
      'message_text_color' => "messageTextColor",
      //4.その他文字色
      'other_text_color' => "otherTextColor",
      //5.ウィジェット枠線色
      'widget_border_color' => "widgetBorderColor",
      //6.吹き出し枠線色
      'chat_talk_border_color' => "chatTalkBorderColor",
      //7.企業名文字色
      'sub_title_text_color' => "subTitleTextColor",
      //8.説明文文字色
      'description_text_color' => "descriptionTextColor",
      //9.チャットエリア背景色
      'chat_talk_background_color' => "chatTalkBackgroundColor",
      //10.企業名担当者名文字色
      'c_name_text_color' => "cNameTextColor",
      //11.企業側吹き出し文字色
      're_text_color' => "reTextColor",
      //12.企業側吹き出し背景色
      're_background_color' => "reBackgroundColor",
      //13.企業側吹き出し枠線色
      're_border_color' => "reBorderColor",
      //14.訪問者側吹き出し文字色
      'se_text_color' => "seTextColor",
      //15.訪問者側吹き出し背景色
      'se_background_color' => "seBackgroundColor",
      //16.訪問者側吹き出し枠線色
      'se_border_color' => "seBorderColor",
      //17.メッセージエリア背景色
      'chat_message_background_color' => "chatMessageBackgroundColor",
      //18.メッセージBOX文字色
      'message_box_text_color' => "messageBoxTextColor",
      //19.メッセージBOX背景色
      'message_box_background_color' => "messageBoxBackgroundColor",
      //20.メッセージBOX枠線色
      'message_box_border_color' => "messageBoxBorderColor",
      //21.送信ボタン文字色
      'chat_send_btn_text_color' => "chatSendBtnTextColor",
      //22.送信ボタン背景色
      'chat_send_btn_background_color' => "chatSendBtnBackgroundColor",
      //23.ウィジット内枠線色
      'widget_inside_border_color' => "widgetInsideBorderColor",
      /* カラー設定end */

        'show_main_image' => "showMainImage",
      'main_image' => "mainImage",
      'radius_ratio' => "radiusRatio",
      //背景の影
      'box_shadow' => "boxShadow",
      //背景の影
      //最小化時デザイン対応
      'minimize_design_type' => "minimizeDesignType",
      //最小化時デザイン対応
      //閉じるボタン対応
      'close_button_setting' => "closeButtonSetting",
      'close_button_mode_type' => "closeButtonModeType",
      'bannertext' => "bannertext",
      //閉じるボタン対応
      'tel' => "tel",
      'show_name' => "showName",
      'chat_message_design_type' => "chatMessageDesignType",
      'chat_message_with_animation' => "chatMessageWithAnimation",
      'show_description' => "showDescription",
      'display_time_flg' => "displayTimeFlg",
      'time_text' => "timeText",
      'chat_radio_behavior' => "chatRadioBehavior",
      'chat_trigger' => "chatTrigger",
      'sp_show_flg' => "spShowFlg",
      'sp_header_light_flg' => "spHeaderLightFlg",
      'sp_auto_open_flg' => "spAutoOpenFlg",
      'content' => "content"
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'show_timing' => [
            'numberRange' => [
                'rule' => '/^([1-4])$/',
                'message' => '選択肢から選び、設定してください'
            ]
        ],
        'max_show_timing_site' => [
            'isMaxShowTiming' => [
                'rule' => 'isMaxShowTiming',
                'message' => '数値を入力してください'
            ],
            'numberRange' => [
                'rule' => '/^(0[1-9]|[1-9]|[1-9][0-9]|[1-9][1-9][0-9]|[1-2][0-9][0-9][0-9]|3[0-5][0-9][0-9]|3600)$/',
                'message' => '１～３６００秒の間で設定してください'
            ]
        ],
        'max_show_timing_page' => [
            'isMaxShowTiming' => [
                'rule' => 'isMaxShowTiming',
                'message' => '数値を入力してください'
            ],
            'numberRange' => [
                'rule' => '/^(0[1-9]|[1-9]|[1-9][0-9]|[1-9][1-9][0-9]|[1-2][0-9][0-9][0-9]|3[0-5][0-9][0-9]|3600)$/',
                'message' => '１～３６００秒の間で設定してください'
            ]
        ],
        'show_time' => [
            'numberRange' => [
              'rule' => '/^([1-4])$/',
              'message' => '選択肢から選び、設定してください'
            ]
        ],
        'max_show_time' => [
            'isMaxShowTime' => [
              'rule' => 'isMaxShowTime',
              'message' => '数値を入力してください'
            ],
            'numberRange' => [
              'rule' => '/^(0[1-9]|[1-9]|[1-9][0-9]|[1-9][1-9][0-9]|[1-2][0-9][0-9][0-9]|3[0-5][0-9][0-9]|3600)$/',
              'message' => '１～３６００秒の間で設定してください'
            ],
            'isLargerThanMaxShowTiming' => [
                'rule' => 'isLargerThanMaxShowTiming',
                'message' => '表示するタイミングよりも値を大きくしてください'
            ]
        ],
        'max_show_time_page' => [
            'isMaxShowTime' => [
              'rule' => 'isMaxShowTime',
              'message' => '数値を入力してください'
            ],
            'numberRange' => [
                'rule' => '/^(0[1-9]|[1-9]|[1-9][0-9]|[1-9][1-9][0-9]|[1-2][0-9][0-9][0-9]|3[0-5][0-9][0-9]|3600)$/',
                'message' => '１～３６００秒の間で設定してください'
            ],
            'isLargerThanMaxShowTiming' => [
                'rule' => 'isLargerThanMaxShowTiming',
                'message' => '表示するタイミングよりも値を大きくしてください'
            ]
        ],
        'title' => [
            'maxLength' => [
                'rule' => ['maxLength', 12],
                'allowEmpty' => false,
                'message' => '１２文字以内で設定してください。'
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
        'string_color' => [
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
        //背景の影
        'box_shadow' => [
            'between' => [
                'rule' => ['between', 0, 10],
                'message' => '１～１０の間で設定してください'
            ]
        ],
        //背景の影
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

    public function isMaxShowTiming($value){
      if ( !isset($this->data['MWidgetSetting']['show_timing']) ) return false;
      switch (intval($this->data['MWidgetSetting']['show_timing'])) {
        case C_WIDGET_SHOW_TIMING_SITE:
          if ( isset($value['max_show_timing_site']) ) {
            return true;
          }
        case C_WIDGET_SHOW_TIMING_PAGE:
          if ( isset($value['max_show_timing_page']) ) {
            return true;
          }
      }
      return false;
    }

    /**
     *
     * @param $value
     * @return bool
     */
    public function isLargerThanMaxShowTiming ($value) {
      // 表示タイミング・最大化条件が共にページ訪問後だった場合
      if ($this->isShowTimingTypePage() && $this->isShowTimeTypePage()) {
        return intval($this->data['MWidgetSetting']['max_show_time_page']) > intval($this->data['MWidgetSetting']['max_show_timing_page']);
      // 表示タイミング・最大化条件が共にサイト訪問後だった場合
      } else if ($this->isShowTimingTypeSite() && $this->isShowTimeTypeSite()) {
        return intval($this->data['MWidgetSetting']['max_show_time']) > intval($this->data['MWidgetSetting']['max_show_timing_site']);
      }
      //それ以外の組み合わせ設定だった場合は比較不要のため、許容する
      return true;
    }

    public function isMaxShowTime($value){
      if ( !isset($this->data['MWidgetSetting']['show_time']) ) return false;
      switch (intval($this->data['MWidgetSetting']['show_time'])) {
        case C_WIDGET_AUTO_OPEN_TYPE_SITE:
          if ( isset($value['max_show_time']) ) {
            return true;
          }
        case C_WIDGET_AUTO_OPEN_TYPE_PAGE:
          if ( isset($value['max_show_time_page']) ) {
            return true;
          }
      }
      return false;
    }

    public function setImage($value=['main_image' => '']){
        if (isset($this->data['MWidgetSetting']['show_main_image']) && strcmp($this->data['MWidgetSetting']['show_main_image'], 1) === 0) {
            // 画像をギャラリーから選択や、アップロードをされていない場合
            if ( empty($value['main_image']) && empty($value['uploadImage']) ) {
                return false;
            }
        }
        return true;
    }

    private function isShowTimingTypeSite() {
      return intval($this->data['MWidgetSetting']['show_timing']) === C_WIDGET_SHOW_TIMING_SITE;
    }

    private function isShowTimingTypePage() {
      return intval($this->data['MWidgetSetting']['show_timing']) === C_WIDGET_SHOW_TIMING_PAGE;
    }

    private function isShowTimeTypeSite() {
      return intval($this->data['MWidgetSetting']['show_time']) === C_WIDGET_AUTO_OPEN_TYPE_SITE;
    }

    private function isShowTimeTypePage() {
      return intval($this->data['MWidgetSetting']['show_time']) === C_WIDGET_AUTO_OPEN_TYPE_PAGE;
    }
}

