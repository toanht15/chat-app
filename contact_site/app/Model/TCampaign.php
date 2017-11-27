<?php
App::uses('AppModel','Model');
class TCampaign extends AppModel {

  public $name = "TCampaigns";

   /**
   * Validation rules
   *
   * @var array
   */
  public $validate = [
    'name' => [
      'maxlength' => [
        'rule' => ['maxLength', 100],
        'allowEmpty' => false,
        'message' => '１００文字以内で設定してください'
      ]
    ],
    'parameter' => [
      'maxlength' => [
        'rule' => ['maxLength', 100],
        'allowEmpty' => false,
        'message' => '１００文字以内で設定してください'
      ]
    ],
  ];


  /**
   * キャンペーン設定を取得
   * @return void
   * */
  public function getList(){
    $ret = $this->find('list',[
      "fields" => ["parameter", "name"],
      "conditions" => [
        "m_companies_id" => Configure::read('logged_company_id')
      ],
      'order' => array('sort'),
      "recursive" => -1
    ]);
    return $ret;
  }
}
?>