<?php
App::uses('AppModel', 'Model');
/**
 * TDictionaryCategory Model
 *
 * @property TDictionaryCategory $TDirection
 */
class TDictionaryCategory extends AppModel {

  public $name = "TDictionaryCategory";


  /**
   * Validation rules
   *
   * @var array
   */
  public $validate = [
    'category_name' => [
      'maxlength' => [
        'rule' => ['maxLength', 100],
        'allowEmpty' => false,
        'message' => '１００文字以内で設定してください'
      ]
    ]
  ];

  function setQuery($sql){
    $this->query($sql);
    //影響を受けた行を取得する
    $affected = $this->getAffectedRows();
    return $affected;
  }

}
