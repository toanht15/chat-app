<?php
App::uses('AppModel', 'Model');
/**
 * THistory Model
 *
 * @property MCompanies $MCompanies
 */
class THistory extends AppModel {

	public $name = "THistory";

	public $validate = array(
    'year' => array(
        'rule'    => array( 'date', 'y'),
        'message' => '年を選択してください',
        'allowEmpty' => false             // 空白許可
    ),
     'month' => array(
     'rule'    => array( 'date', 'ym'),
     'message' => '年月を選択してください',
     'allowEmpty' => false             // 空白許可
    ),
     'day' => array(
     'rule' => array( 'date', 'ymd'),
     'message' => '年月を選択してください',
     'allowEmpty' => false             // 空白許可
    ),
);

}
