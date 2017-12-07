<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/12/07
 * Time: 11:51
 */

class NotificationController extends AppController {
  const PARAM_ACCESS_TOKEN = 'accessToken';
  const PARAM_AUTO_MESSAGE_ID = 'autoMessageId';
  const PARAM_LAST_CHAT_LOG_ID = 'lastChatLogId';

  public $components = ['AutoMessageMailTemplate', 'MailSender', 'Auth'];
  public $uses = ['TAutoMessage','TCampaign', 'THistory', 'THistoryChatLog', 'THistoryStayLog', 'MLandscapeData', 'MMailTransmissionSetting'];

  public function beforeFilter() {
    $this->Auth->allow('autoMessages');
  }

  public function autoMessages() {
    Configure::write('debug', 0);

    $this->autoRender = false;
    $this->layout = "ajax";

    $jsonObj = $this->getRequestJSONData();
    try {
      $this->isValidAccessToken($jsonObj[self::PARAM_ACCESS_TOKEN]);
      $targetAutoMessage = $this->getTargetAutoMessageById($jsonObj[self::PARAM_AUTO_MESSAGE_ID]);
      $targetChatLog = $this->getTargetChatLogById($jsonObj[self::PARAM_LAST_CHAT_LOG_ID]);
      $allChatLogs = $this->getAllChatLogsByEntity($targetChatLog);
      $targetHistory = $this->getTargetHistoryById($targetChatLog['THistoryChatLog']['t_histories_id']);
      $targetStayLog = $this->getTargetStayLogById($targetChatLog['THistoryChatLog']['t_history_stay_logs_id']);
      $campaign = $this->getAllCampaign($targetHistory['THistory']['m_companies_id']);
      $targetLandscapeData = null;
      if(true) { //FIX : 企業マスタから取得必須
        $targetLandscapeData = $this->getTargetLandScapeDataByIpAddress($targetHistory['THistory']['ip_address']);
      }
      $component = new AutoMessageMailTemplateComponent();
      $component->setRequiredData($targetAutoMessage['TAutoMessage']['m_mail_template_id'], $allChatLogs, $targetStayLog, $campaign, $targetLandscapeData);
      $component->createMessageBody();

      $transmission = $this->getTransmissionConfigById($targetAutoMessage['TAutoMessage']['m_mail_transmission_settings_id']);
      $sender = new MailSenderComponent();
      $sender->setFromName($transmission['MMailTransmissionSetting']['from_name']);
      $sender->setTo($transmission['MMailTransmissionSetting']['to_address']);
      $sender->setSubject($transmission['MMailTransmissionSetting']['subject']);
      $sender->setBody($component->getBody());
      $sender->send();

    } catch(Exception $e) {
      $this->log('Notification/autoMessages呼び出し時にエラーが発生しました。 エラーメッセージ: '.$e->getMessage().' エラー番号 '.$e->getCode(), 'api-error');
      $this->response->statusCode($e->getCode());
      return json_encode(array(
          'success' => false,
          'errorCode' => $e->getCode()
      ));
    }
    $this->response->statusCode(200);
    return json_encode(array(
        'success' => true
    ));
  }

  private function getTargetAutoMessageById($id) {
    return $this->TAutoMessage->findById($id);
  }

  /**
   * @param $id
   */
  private function getTargetChatLogById($id) {
    return $this->THistoryChatLog->findById($id);
  }

  private function getAllChatLogsByEntity($chatLog) {
    return $this->THistoryChatLog->find('all', array(
      'alias' => 'THistoryChatLog',
      'fields' => array('THistoryChatLog.*','MUser.*'),
      'conditions' => array('THistoryChatLog.m_companies_id' => $chatLog['THistoryChatLog']['m_companies_id'],
                            'THistoryChatLog.t_histories_id' => $chatLog['THistoryChatLog']['t_histories_id'],
                            'THistoryChatLog.id <= ' => $chatLog['THistoryChatLog']['id']
      ),
      'joins' => array(array(
          'type' => 'LEFT',
          'table' => 'm_users',
          'alias' => 'MUser',
          'fields' => ['id', 'display_name'],
          'conditions' => 'THistoryChatLog.m_users_id = MUser.id'
      )
      ),
      'order' => array('THistoryChatLog.created')
    ));
  }

  private function getTargetHistoryById($id) {
    return $this->THistory->findById($id);
  }

  /**
   * @param $id
   */
  private function getTargetStayLogById($id) {
    return $this->THistoryStayLog->findById($id);
  }

  private function getTargetLandScapeDataByIpAddress($ip) {
    return $this->MLandscapeData->findByIpAddress($ip);
  }

  private function getAllCampaign($m_companies_id) {
    return $this->TCampaign->find('all', array(
       'conditions' => array('TCampaign.m_companies_id' => $m_companies_id),
       'order' => array('sort')
    ));
  }

  private function getTransmissionConfigById($id) {
    return $this->MMailTransmissionSetting->findById($id);
  }
}