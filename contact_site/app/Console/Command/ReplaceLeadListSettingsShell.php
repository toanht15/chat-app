<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/02/20
 * Time: 19:07
 * @property TAutoMessage $TAutoMessage
 */

class ReplaceLeadListSettingsShellShell //extends AppShell
{
  const LOG_INFO = 'batch-info';
  const LOG_ERROR = 'batch-error';

  const ACTION_TYPE_SEND_MESSAGE = "1";

  public $uses = ['TLeadList', 'TLeadListSetting', 'TChatbotScenario'];

  /**
   * @see https://qiita.com/colorrabbit/items/d302cc0eeec3adc18456
   */
  /*public function startup() {
    parent::startup();
  }*/

  /**
   * リードリストのデータを洗い替える
   */
  public function refreshLeadListSetting() {
    $leadHashSettings = [];
    $allData = $this->TChatbotScenario->find('all', [
      'conditions' => [
        'del_flg' => 0
      ]
    ]);
    try {
      $this->TChatbotSceanrio->begin();
      $this->TLeadListSetting->begin();
      $this->TLeadList->begin();

    } catch(Exception $e) {
      $this->TChatbotSceanrio->rollback();
      $this->TLeadListSetting->rollback();
      $this->TLeadList->rollback();
      $this->printLog('ERROR FOUND. message : '.$e->getMessage());
    }
    $this->TChatbotSceanrio->commit();
    $this->TLeadListSetting->commit();
    $this->TLeadList->commit();
    $this->printLog('FINISHED');
  }

  private function printLog($msg) {
    $this->log($msg, self::LOG_INFO);
    $this->out($msg);
  }
}