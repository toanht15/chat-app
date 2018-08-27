<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/02/20
 * Time: 19:07
 * @property TAutoMessage $TAutoMessage
 */

class ReplaceAutoMessageConfigurationsShell extends AppShell
{
  const LOG_INFO = 'batch-info';
  const LOG_ERROR = 'batch-error';

  const ACTION_TYPE_SEND_MESSAGE = "1";

  public $uses = array('TAutoMessage');

  /**
   * MailSenderComponent.phpの呼び出し
   * @see https://qiita.com/colorrabbit/items/d302cc0eeec3adc18456
   */
  public function startup() {
    parent::startup();
  }

  /**
   * オートメッセージの
   */
  public function deleteEmptyRowsBeforeSelection() {
    $allData = $this->TAutoMessage->find('all', array(
      'conditions' => array('del_flg' => 0)
    ));
    try {
      $this->TAutoMessage->begin();
      foreach ($allData as $index => $data) {
        if (strcmp($data['TAutoMessage']['action_type'], self::ACTION_TYPE_SEND_MESSAGE) === 0) {
          $this->printLog("====================================================");
          $this->printLog('target automessage id : ' . $data['TAutoMessage']['id'] . ' companies_id : ' . $data['TAutoMessage']['m_companies_id'] . ' name : ' . $data['TAutoMessage']['name']);
          $jsonObj = json_decode($data['TAutoMessage']['activity'], TRUE);
          $targetMessage = $jsonObj['message'];
          $this->printLog("BEFORE:\n" . $targetMessage);
          if(preg_match('/\n\n\[\]/',$targetMessage)) {
            $targetMessage = preg_replace('/\n\n\[\]/', "\n[]", $targetMessage);
            $this->printLog("AFTER:\n" . $targetMessage);
            $jsonObj['message'] = $targetMessage;
            $data['TAutoMessage']['activity'] = json_encode($jsonObj);
            $this->TAutoMessage->create();
            $this->TAutoMessage->set($data);
            $this->TAutoMessage->save();
          } else {
            $this->printLog('message is not match target characters.');
          }
        } else {
          $this->printLog('automessage id : ' . $data['TAutoMessage']['id'] . ' ignored.');
        }
      }
    } catch(Exception $e) {
      $this->TAutoMessage->rollback();
      $this->printLog('ERROR FOUND. message : '.$e->getMessage());
    }
    $this->TAutoMessage->commit();
    $this->printLog('FINISHED');
  }

  private function printLog($msg) {
    $this->log($msg, self::LOG_INFO);
    $this->out($msg);
  }
}