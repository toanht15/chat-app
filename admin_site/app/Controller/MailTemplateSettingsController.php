<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/08/08
 * Time: 12:09
 */

App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
class MailTemplateSettingsController extends AppController
{
  public $uses = ['MCompany', 'MJobMailTemplate','MAgreements', 'MUser', 'MWidgetSetting', 'MChatSetting', 'TAutoMessages', 'TDictionaries', 'TDictionaryCategory', 'MMailTemplate', 'TransactionManager'];

  public $paginate = [
    'MCompany' => [
      'order' => ['MCompany.id' => 'asc'],
      'fields' => ['*'],
      'limit' => 1000,
      'joins' => [
        [
          'type' => 'left',
          'table' => 'm_agreements',
          'alias' => 'MAgreement',
          'conditions' => [
            'MAgreement.m_companies_id = MCompany.id',
          ],
        ],
        [
          'type' => 'left',
          'table' => '(SELECT id,m_companies_id,mail_address,password FROM m_users WHERE del_flg != 1 AND permission_level = 99 GROUP BY m_companies_id)',
          'alias' => 'AdminUser',
          'conditions' => [
            'AdminUser.m_companies_id = MCompany.id',
          ],
        ],
        [
          'type' => 'inner',
          'table' => '(SELECT id,m_companies_id,mail_address,password,count(m_companies_id) AS user_account FROM  m_users WHERE del_flg != 1 AND permission_level != 99 GROUP BY m_companies_id)',
          'alias' => 'MUser',
          'conditions' => [
            'MUser.m_companies_id = MCompany.id',
          ],
        ],
      ],
      'conditions' => [
          'MCompany.del_flg != ' => 1,
      ],
    ]
  ];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', 'サイトキー管理');
    $this->Auth->allow(['index','add','edit','remoteSaveForm','deleteMailInfo']);
    header('Access-Control-Allow-Origin: *');
  }

  /**
   * 初期画面
   * @return void
   */
  public function index() {
    $this->set('title_for_layout', 'サイトキー管理');
    $data = $this->MJobMailTemplate->find('all');
    $this->set('mailInfo', $data);
    //$this->set('companyList', $this->paginate('MCompany'));
  }

  public function add() {
    Configure::write('debug', 0);
    $this->set('title_for_layout', 'メール登録');

    if( $this->request->is('post') ) {
      $this->autoRender = false;
      $this->layout = "ajax";
      $data = $this->getParams();

      try {
        $this->processTransaction($data['MJobMailTemplate']);
      } catch(Exception $e) {
        $this->log("Exception Occured : ".$e->getMessage(), LOG_WARNING);
        $this->log($e->getTraceAsString(),LOG_WARNING);
        $this->response->statusCode(400);
        return json_encode([
          'success' => false,
          'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
      }
    }
  }

  private function getParams() {
    return $this->request->data;
  }

  private function processTransaction($mailInfo) {
    try {
      $transaction = $this->TransactionManager->begin();
      $this->createMailInfo($mailInfo);
    } catch (Exception $e) {
      $this->TransactionManager->rollback($transaction);
      throw $e;
    }
    $this->TransactionManager->commit($transaction);
  }

  private function createMailInfo($mailInfo) {
    $errors = [];
    $tmpData = [
        "mail_type_cd" => $mailInfo["mail_type_cd"],
        "subject" => $mailInfo["subject"],
        "mail_body" => $mailInfo["mail_body"],
        "days_after" => $mailInfo["days_after"],
        "time" => $mailInfo["time"]
    ];
    $this->MJobMailTemplate->create();
    $this->MJobMailTemplate->set($tmpData);
    if(!$this->MJobMailTemplate->validates()) {
      $this->log('validation2!',LOG_DEBUG);
      $this->MJobMailTemplate->rollback();
      // 画面に返す
      $errors = $this->MJobMailTemplate->validationErrors;
      $this->log('errors',LOG_DEBUG);
      $this->log($errors,LOG_DEBUG);
      //return $errors;
      throw new Exception("MJobMailTemplate validation error");
    }
    else {
      $this->MJobMailTemplate->save();
    }
  }

  /* *
   * 更新画面
   * @param id
   * @return void
   * */
  public function edit($id)
  {
    if ($this->request->is('post') || $this->request->is('put')) {
      $mailInfo = $this->MJobMailTemplate->read(null, $id);
      $transactions = $this->TransactionManager->begin();
      $saveData = $this->request->data;
      $this->MJobMailTemplate->set($saveData);
      $this->MJobMailTemplate->save();
      $this->MJobMailTemplate->commit();
    }
    else {
      $editData = $this->MJobMailTemplate->read(null, $id);
      $this->set('id', $editData['MJobMailTemplate']['id']);//削除に必要なもの
      $this->request->data = $editData;
    }
  }

  public function deleteMailInfo() {
    $this->autoRender = false;
    $this->layout = "ajax";
    if($this->request->is('post')) {

      $transaction = $this->TransactionManager->begin();
      $id = $this->request->data['id'];

      try {
        //物理削除
        $this->MJobMailTemplate->delete($id);

      } catch(Exception $e) {
        $this->TransactionManager->rollback($transaction);
        $this->log("Delete Exception Occured : ".$e->getMessage(), LOG_WARNING);
      }
      $this->TransactionManager->commit($transaction);
    }
    $this->autoRender = false;
  }
}