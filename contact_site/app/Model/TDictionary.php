<?php
App::uses('AppModel', 'Model');
/**
 * TDictionary Model
 *
 * @property TDictionary $TDirection
 */
class TDictionary extends AppModel {

	public $name = "TDictionary";


    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
      'word' => [
          'maxlength' => [
              'rule' => ['maxLength', 100],
              'allowEmpty' => false,
              'message' => '１００文字以内で設定してください'
          ]
      ],
      'type' => [
            'inList' => [
                  'rule' => ['inList', [C_AUTHORITY_ADMIN, C_AUTHORITY_NORMAL]],
                  'message' => '種類を選択してください'
            ]
      ],
    ];

}
