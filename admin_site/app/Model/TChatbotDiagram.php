<?php
App::uses('AppModel', 'Model');

class TChatbotDiagram extends AppModel
{

  public $name = "TChatbotDiagram";

  /**
   * Validation rules
   *
   * @var array
   */
  public $validate = array(
    'name' => array(
      'maxlength' => array(
        'rule' => array('maxLength', 50),
        'allowEmpty' => false,
        'message' => '５０文字以内で設定してください'
      )
    ),
    'activity' => array(
      'notBlank' => array(
        'rule' => array('notBlank'),
        'allowEmpty' => false,
        'message' => 'チャットツリーを設定してください'
      )
    )
  );


  /**
   * チャットツリー設定一覧を取得
   * @return void
   * */
  public function getList()
  {
    $ret = $this->find('all', array(
      "conditions" => array(
        "m_companies_id" => Configure::read('logged_company_id')
      ),
      'order' => array('sort'),
      "recursive" => -1
    ));
    return $ret;
  }
}

?>
