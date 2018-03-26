<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/03/06
 * Time: 14:47
 */

App::uses('ContractController', 'Controller');

class AddScenarioSampleShell extends AppShell
{
  public $uses = array('MCompany', 'TransactionManager');

  public function importAll() {
    try {
      $this->log("BEGIN AddScenarioSampleShell", 'refresh');
      $record = $this->MCompany->find('all',array(
        'conditions' => array(
          'NOT' => array(
            'del_flg' => 1
          )
        )
      ));
      $controller = new ContractController();
      $transaction = $this->TransactionManager->begin();
      foreach($record as $index => $company) {
        $this->log('==========================================', LOG_INFO, 'refresh');
        $this->log('TARGET : ' . $company['MCompany']['company_name'], LOG_INFO, 'refresh');
        $this->log('KEY    : ' . $company['MCompany']['company_key'], LOG_INFO, 'refresh');
        $this->log('PLAN   : ' . $this->convertContactTypesIdToString($company['MCompany']['m_contact_types_id']), LOG_INFO, 'refresh');
        $this->log('~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~', LOG_INFO, 'refresh');
        if(strcmp($company['MCompany']['company_key'], 'medialink') === 0) {
          $this->log('当社は既にこのサンプルが入ってるため入れません。','refresh');
        }
        $controller->addDefaultScenarioMessage($company['MCompany']['id'], $company['MCompany'], true);
        $this->log('RESULT: OK', 'refresh');
      }
    } catch(Exception $e) {
      $this->TransactionManager->rollback($transaction);
      $this->log('RESULT: NG!!!!', 'refresh');
      $this->log('==========================================', 'refresh');
    }
    $this->TransactionManager->commit($transaction);
    $this->log("END   AddScenarioSampleShell", 'refresh');
  }

  private function convertContactTypesIdToString($m_contact_types_id) {
    $val = "";
    switch($m_contact_types_id) {
      case C_CONTRACT_FULL_PLAN_ID:
        $val = 'フルプラン';
        break;
      case C_CONTRACT_CHAT_PLAN_ID:
        $val = 'スタンダードプラン';
        break;
      case C_CONTRACT_SCREEN_SHARING_ID:
        $val = 'シェアリングプラン';
        break;
      case C_CONTRACT_CHAT_BASIC_PLAN_ID:
        $val = 'ベーシックプラン';
        break;
    }
    return $val;
  }
}