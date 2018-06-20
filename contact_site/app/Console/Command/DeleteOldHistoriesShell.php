<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/02/20
 * Time: 19:07
 * @param THistory $THistory
 * @param THistoryChatLogs $THistoryChatLogs
 * @param THistoryStayLogs $THistoryStayLogs
 * @param TransactionManager $TransactionManager
 */

App::uses('ComponentCollection', 'Controller'); //これが大事
App::uses('AmazonComponent', 'Controller/Component');
class DeleteOldHistoriesShell extends AppShell
{
  const LOG_INFO = 'batch-info';
  const LOG_ERROR = 'batch-error';

  const DEFAULT_KEEP_DAYS = 7;

  public $uses = array('MCompany', 'THistory', 'THistoryChatLog', 'THistoryStayLog', 'THistoryShareDisplay', 'TReceiveVisitorFile', 'TransactionManager');

  private $awsComponent;

  private $targetCompanies;
  private $deleteTargetHistoryIds;
  private $manager;

  /**
   * MailSenderComponent.phpの呼び出し
   * @see https://qiita.com/colorrabbit/items/d302cc0eeec3adc18456
   */
  public function startup() {
    parent::startup();
    $collection = new ComponentCollection(); //これが大事です。
    $this->awsComponent = new AmazonComponent($collection);
    $this->deleteTargetHistoryIds = array();
  }

  public function processDeleteJob() {
    try {
      $this->getDeleteTargetCompanies();
      if(count($this->targetCompanies) > 0) {
        $this->beginTransaction();
        foreach($this->targetCompanies as $index => $data) {
          $this->log('=======================================', self::LOG_INFO);
          $this->log('DELETE TARGET COMPANY NAME : '.$data['MCompany']['company_name'], self::LOG_INFO);
          $this->log('KEEP HISTORY DAYS : '.$data['MCompany']['keep_history_days'], self::LOG_INFO);
          $this->log('=======================================', self::LOG_INFO);
          $this->getDeleteTargetHistories($data['MCompany']);
          $this->deleteChatLog($data['MCompany']);
          $this->deleteStayLog();
          $this->deleteShareDisplayLog();
          $this->deleteHistory($data['MCompany']);
          $this->deleteReceiveFile($data['MCompany']);
        }
        $this->commitTransaction();
        $this->log('TARGET IDS : '.var_export($this->deleteTargetHistoryIds, TRUE), self::LOG_INFO);
      } else {
        $this->log('target companies is not found.', self::LOG_INFO);
      }
    } catch(Exception $e) {
      $this->rollbackTransaction();
    }
  }

  private function getDeleteTargetCompanies() {
    $this->targetCompanies = $this->MCompany->find('all',array(
      'conditions' => array(
        'AND' => array('del_flg' => 0, 'keep_history_days > ' => 0)
      )
    ));
  }

  private function getDeleteTargetHistories($d) {
    $deleteFromDatetime = $this->getDeleteFromTime($d['keep_history_days']);
    $this->log('TARGET DELETE FROM DATE TIME access_date <= '.$deleteFromDatetime, self::LOG_INFO);
    $this->deleteTargetHistoryIds = $this->THistory->find('list', array(
      'conditions' => array('access_date <= ' => $deleteFromDatetime)
    ));
  }

  private function deleteChatLog($d) {
    $this->THistoryChatLog->deleteAll(array(
      'm_companies_id' => $d['id'],
      't_histories_id' => array_keys($this->deleteTargetHistoryIds)
    ));
  }

  private function deleteStayLog() {
    $this->THistoryStayLog->deleteAll(array(
      't_histories_id' => array_keys($this->deleteTargetHistoryIds)
    ));
  }

  private function deleteShareDisplayLog() {
    $this->THistoryShareDisplay->deleteAll(array(
      't_histories_id' => array_keys($this->deleteTargetHistoryIds)
    ));
  }

  private function deleteReceiveFile($d) {
    $target = $this->TReceiveVisitorFile->find('all', array(
      'm_companies_id' => $d['id'],
      'created <= ' => $this->getDeleteFromTime($d['keep_history_days'])
    ));
    foreach($target as $index => $data) {
      $this->log('deleting receiving file : '.$data['TReceiveVisitorFile']['saved_file_key'], self::LOG_INFO);
      $this->awsComponent->removeObject('receivedFile/'.$data['TReceiveVisitorFile']['saved_file_key']);
      $this->TReceiveVisitorFile->delete($data['TReceiveVisitorFile']['id']);
    }
  }

  private function deleteHistory($d) {
    $this->THistory->deleteAll(array(
      'id' => array_keys($this->deleteTargetHistoryIds),
      'm_companies_id' => $d['id']
    ));
  }

  private function getDeleteFromTime($keepDays = self::DEFAULT_KEEP_DAYS) {
    return date('Y-m-d 23:59:59', strtotime('-'.(intval($keepDays) + 1).' day'));
  }

  private function beginTransaction() {
    $this->manager = $this->TransactionManager->begin();
  }

  private function commitTransaction() {
    $this->TransactionManager->commitTransaction($this->manager);
  }

  private function rollbackTransaction() {
    $this->TransactionManager->rollbackTransaction($this->manager);
  }
}