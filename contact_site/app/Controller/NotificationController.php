<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/12/07
 * Time: 11:51
 */

App::uses('HttpSocket', 'Network/Http');

class NotificationController extends AppController {
  const PARAM_ACCESS_TOKEN = 'accessToken';
  const PARAM_AUTO_MESSAGE_ID = 'autoMessageId';
  const PARAM_LAST_CHAT_LOG_ID = 'lastChatLogId';

  const PARAM_HISTORY_ID = 'userHistoryId';
  const PARAM_MAIL_TYPE = 'mailType';
  const PARAM_TRANSMISSION_ID = 'transmissionId';
  const PARAM_TEMPLATE_ID = 'templateId';
  const PARAM_IS_NEED_TO_ADD_DOWNLOAD_URL = 'withDownloadURL';
  const PARAM_VARIABLES = 'variables';

  public $components = ['AutoMessageMailTemplate', 'ScenarioMailTemplate', 'MailSender', 'Auth'];
  public $uses = ['TAutoMessage','MCustomer','TCustomerInformationSetting','TCampaign', 'THistory', 'THistoryChatLog', 'THistoryStayLog', 'MLandscapeData', 'MMailTransmissionSetting', 'TMailTransmissionLog', 'MCompany'];

  public function beforeFilter() {
    $this->Auth->allow('autoMessages','scenario', 'callExternalApi');
  }

  public function autoMessages() {
    Configure::write('debug', 0);

    $this->autoRender = false;
    $this->layout = "ajax";

    $jsonObj = $this->getRequestJSONData();
    try {
      $this->isValidAccessToken($jsonObj[self::PARAM_ACCESS_TOKEN]);
      $targetAutoMessage = $this->getTargetAutoMessageById($jsonObj[self::PARAM_AUTO_MESSAGE_ID]);
      if(empty($targetAutoMessage)) {
        throw new InvalidArgumentException('指定のAutoMessageId : '.$jsonObj[self::PARAM_AUTO_MESSAGE_ID].' のオートメッセージが存在しません');
      }
      $targetChatLog = $this->getTargetChatLogById($jsonObj[self::PARAM_LAST_CHAT_LOG_ID]);
      if(empty($targetAutoMessage)) {
        throw new InvalidArgumentException('指定のchatLogId : '.$jsonObj[self::PARAM_LAST_CHAT_LOG_ID].' のチャットログが存在しません');
      }
      $allChatLogs = $this->getAllChatLogsByEntity($targetChatLog);
      $targetHistory = $this->getTargetHistoryById($targetChatLog['THistoryChatLog']['t_histories_id']);
      $targetStayLog = $this->getTargetStayLogById($targetChatLog['THistoryChatLog']['t_history_stay_logs_id']);
      $campaign = $this->getAllCampaign($targetHistory['THistory']['m_companies_id']);
      $coreSettings = $this->getCoreSettingsById($targetHistory['THistory']['m_companies_id']);
      $targetLandscapeData = null;
      if(!empty($coreSettings) && array_key_exists(C_COMPANY_REF_COMPANY_DATA, $coreSettings) && $coreSettings[C_COMPANY_REF_COMPANY_DATA]) { //FIX : 企業マスタから取得必須
        $targetLandscapeData = $this->getTargetLandScapeDataByIpAddress($targetHistory['THistory']['ip_address']);
      }

      $customerInfo = $this->getTargetCustomerInfoByVisitorId($targetHistory['THistory']['m_companies_id'], $targetHistory['THistory']['visitors_id']);

      $component = new AutoMessageMailTemplateComponent();
      $component->setRequiredData($targetAutoMessage['TAutoMessage']['m_mail_template_id'], $allChatLogs, $targetStayLog, $campaign, $targetLandscapeData, $customerInfo);
      $component->createMessageBody();

      $transmission = $this->getTransmissionConfigById($targetAutoMessage['TAutoMessage']['m_mail_transmission_settings_id']);
      $sender = new MailSenderComponent(null, $targetHistory['THistory']['m_companies_id']);
      $sender->setFrom(MailSenderComponent::MAIL_SYSTEM_FROM_ADDRESS);
      $sender->setFromName($transmission['MMailTransmissionSetting']['from_name']);
      $sender->setTo($transmission['MMailTransmissionSetting']['to_address']);
      $sender->setSubject($transmission['MMailTransmissionSetting']['subject']);
      $sender->setBody($component->getBody());

      // 送信前にログを生成
      $this->TMailTransmissionLog->create();
      $this->TMailTransmissionLog->set([
          'm_companies_id' => $targetHistory['THistory']['m_companies_id'],
          'mail_type_cd' => AutoMessageMailTemplateComponent::MAIL_TYPE_CD,
          'from_address' => $sender->getFrom(),
          'from_name' => $transmission['MMailTransmissionSetting']['from_name'],
          'to_address' => $transmission['MMailTransmissionSetting']['to_address'],
          'subject' => $transmission['MMailTransmissionSetting']['subject'],
          'body' => $component->getBody(),
          'send_flg' => 0
      ]);
      $this->TMailTransmissionLog->save();
      $lastInsertId = $this->TMailTransmissionLog->getLastInsertId();

      $sender->send();

      // 送信ログを作る
      $now = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
      $this->TMailTransmissionLog->read(null, $lastInsertId);
      $this->TMailTransmissionLog->set([
        'send_flg' => 1,
        'sent_datetime' => $now->format("Y/m/d H:i:s")
      ]);
      $this->TMailTransmissionLog->save();

      // チャットログに送信履歴を付ける
      $this->THistoryChatLog->read(null, $jsonObj[self::PARAM_LAST_CHAT_LOG_ID]);
      $this->THistoryChatLog->set([
        'send_mail_flg' => 1,
        't_mail_transmission_logs_id' => $lastInsertId
      ]);
      $this->THistoryChatLog->save();

    } catch(Exception $e) {
      if(strpos($e->getMessage(), 'Invalid email') === 0) {
        $this->log('【MAIL_SEND_WARNING】メールアドレスが不正です。 エラーメッセージ: '.$e->getMessage().' エラー番号 '.$e->getCode().' パラメータ: '.json_encode($jsonObj), 'mail-api-error');
      } else {
        $this->log('【MAIL_SEND_ERROR】Notification/autoMessages呼び出し時にエラーが発生しました。 エラーメッセージ: '.$e->getMessage().' エラー番号 '.$e->getCode().' パラメータ: '.json_encode($jsonObj), 'mail-api-error');
      }
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

  public function scenario() {
    Configure::write('debug', 0);

    $this->autoRender = false;
    $this->layout = "ajax";

    $jsonObj = $this->getRequestJSONData();
    try {
      $this->isValidAccessToken($jsonObj[self::PARAM_ACCESS_TOKEN]);
      if(!isset($jsonObj[self::PARAM_IS_NEED_TO_ADD_DOWNLOAD_URL]) || empty($jsonObj[self::PARAM_IS_NEED_TO_ADD_DOWNLOAD_URL])) {
        $jsonObj[self::PARAM_IS_NEED_TO_ADD_DOWNLOAD_URL] = false;
      }
      $targetChatLog = $this->getTargetChatLogByHistoryId($jsonObj[self::PARAM_HISTORY_ID]);
      if(empty($targetChatLog)) {
        throw new InvalidArgumentException('指定のHistoryId : '.$jsonObj[self::PARAM_HISTORY_ID].' のチャットログが存在しません');
      }
      $allChatLogs = $this->getAllChatLogsByEntityHistoryId($targetChatLog);
      $targetHistory = $this->getTargetHistoryById($targetChatLog['THistoryChatLog']['t_histories_id']);
      $targetStayLog = $this->getTargetStayLogById($targetChatLog['THistoryChatLog']['t_history_stay_logs_id']);
      $campaign = $this->getAllCampaign($targetHistory['THistory']['m_companies_id']);
      $coreSettings = $this->getCoreSettingsById($targetHistory['THistory']['m_companies_id']);
      $targetLandscapeData = null;
      if(!empty($coreSettings) && array_key_exists(C_COMPANY_REF_COMPANY_DATA, $coreSettings) && $coreSettings[C_COMPANY_REF_COMPANY_DATA]) { //FIX : 企業マスタから取得必須
        $targetLandscapeData = $this->getTargetLandScapeDataByIpAddress($targetHistory['THistory']['ip_address']);
      }

      $customerInfo = $this->getTargetCustomerInfoByVisitorId($targetHistory['THistory']['m_companies_id'], $targetHistory['THistory']['visitors_id']);

      $component = new ScenarioMailTemplateComponent();
      $component->setSenarioRequiredData($jsonObj[self::PARAM_MAIL_TYPE], $jsonObj[self::PARAM_VARIABLES], $jsonObj[self::PARAM_TEMPLATE_ID], $allChatLogs, $targetStayLog, $campaign, $targetLandscapeData, $customerInfo);
      $component->createMessageBody($jsonObj[self::PARAM_IS_NEED_TO_ADD_DOWNLOAD_URL]);

      $transmission = $this->getTransmissionConfigById($jsonObj[self::PARAM_TRANSMISSION_ID]);
      $sender = new MailSenderComponent(null, $targetHistory['THistory']['m_companies_id']);
      $sender->setFrom(MailSenderComponent::MAIL_SYSTEM_FROM_ADDRESS);
      $sender->setFromName($component->replaceVariables($transmission['MMailTransmissionSetting']['from_name']));
      $sender->setTo($component->replaceVariables($transmission['MMailTransmissionSetting']['to_address']));
      $sender->setSubject($component->replaceVariables($transmission['MMailTransmissionSetting']['subject']));
      $sender->setBody($component->getBody());
      // 送信前にログを生成
      $this->TMailTransmissionLog->create();
      $this->TMailTransmissionLog->set(array(
        'm_companies_id' => $targetHistory['THistory']['m_companies_id'],
        'mail_type_cd' => ScenarioMailTemplateComponent::MAIL_TYPE_CD,
        'from_address' => $sender->getFrom(),
        'from_name' => $component->replaceVariables($transmission['MMailTransmissionSetting']['from_name']),
        'to_address' => $component->replaceVariables($transmission['MMailTransmissionSetting']['to_address']),
        'subject' => $component->replaceVariables($transmission['MMailTransmissionSetting']['subject']),
        'body' => $component->getBody(),
        'send_flg' => 0
      ));
      $this->TMailTransmissionLog->save();
      $lastInsertId = $this->TMailTransmissionLog->getLastInsertId();

      $sender->send();

      // 送信ログを作る
      $now = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
      $this->TMailTransmissionLog->read(null, $lastInsertId);
      $this->TMailTransmissionLog->set([
        'send_flg' => 1,
        'sent_datetime' => $now->format("Y/m/d H:i:s")
      ]);
      $this->TMailTransmissionLog->save();

    } catch(Exception $e) {
      if(strpos($e->getMessage(), 'Invalid email') === 0) {
        $this->log('【MAIL_SEND_WARNING】メールアドレスが不正です。 エラーメッセージ: '.$e->getMessage().' エラー番号 '.$e->getCode().' パラメータ: '.json_encode($jsonObj), 'mail-api-error');
      } else {
        $this->log('【MAIL_SEND_ERROR】Notification/scenario呼び出し時にエラーが発生しました。 エラーメッセージ: '.$e->getMessage().' エラー番号 '.$e->getCode().' パラメータ: '.json_encode($jsonObj), 'mail-api-error');
      }

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

  /**
   * 外部システム連携 API実行
   * @return JSON APIの実行結果（変数名と、API実行により取得した値）
   */
  public function callExternalApi() {
    Configure::write('debug', 0);
    $apiMethodTypeList = Configure::read('chatbotScenarioApiMethodType');

    $this->autoRender = false;
    $this->layout = 'ajax';
    $this->validatePostMethod();

    try {
      $apiParams = (isset($this->request->data['apiParams'])) ? $this->request->data['apiParams'] : '';
      $settings = json_decode($apiParams);

      // URIのパース
      $parsedUrl = parse_url($settings->url);

      // リクエストヘッダー
      $requestHeaders = [];
      foreach ($settings->requestHeaders as $key => $param) {
        if (!empty($param->name) && !empty($param->value)) {
          $requestHeaders[$param->name] = $param->value;
        }
      }

      // リクエストパラメーターの設定
      $HttpSocket = new HttpSocket();
      $request = [
        'method' => $apiMethodTypeList[$settings->methodType],
        'uri' => [
          'scheme' => $parsedUrl['scheme'],
          'host' => $parsedUrl['host'],
          'port' => $parsedUrl['port'],
          'user' => $parsedUrl['user'],
          'pass' => $parsedUrl['pass'],
          'path' => $parsedUrl['path'],
          'query' => $parsedUrl['query'],
          'fragment' => $parsedUrl['fragment'],
        ],
        'body' => $settings->requestBody,
        'header' => $requestHeaders
      ];

      // リクエストの内容をログ出力
      $this->log('【EXTERNAL_API_REQUEST】Notification/callExternalApi リクエスト '.json_encode($request), 'external-api-request');

      $response = $HttpSocket->request($request);

      // レスポンスの内容をログ出力
      $this->log('【EXTERNAL_API_RESPONSE】Notification/callExternalApi コード '.$response->code.' ボディ '.json_encode($response->body), 'external-api-response');

      $responseBodyList = [];
      if ($response->code == 200) {
        // 変換元キー名を元に、レスポンス内容から値を取得する
        $jsonData = json_decode($response->body);
        foreach ($settings->responseBodyMaps as $param) {
          $splitedKey = preg_split('/[.\[]/', $param->sourceKey);
          $resultData = array_reduce($splitedKey, function($carry, $item) {
            return is_array($carry) ? $carry[intval($item)] : $carry->$item;
          }, $jsonData);
          $resultData = !is_null($resultData) ? $resultData : $param->variableName;
          $responseBodyList[] = ['variableName' => $param->variableName, 'value' => $resultData];
        }
      } else {
        $this->log('【EXTERNAL_API_ERROR】Notification/callExternalApi 外部API呼び出し時にエラーが発生しました。 エラー番号 '.$response->code.' リクエスト '.json_encode($request).' レスポンス '.json_encode($response->body), 'external-api-error');
        $this->response->statusCode($response->code);
        return json_encode(array(
          'success' => false,
          'errorCode' => $response->code,
          'body' => $response->body
        ));
      }
    } catch(Exception $e) {
      $this->log('【EXTERNAL_API_ERROR】Notification/callExternalApi呼び出し時にエラーが発生しました。 エラーメッセージ: '.$e->getMessage().' エラー番号 '.$e->getCode().' パラメータ: '.json_encode($request), 'external-api-error');
      $this->response->statusCode($e->getCode());
      return json_encode(array(
        'success' => false,
        'errorCode' => $e->getCode()
      ));
    }
    $this->response->statusCode(200);
    return json_encode(array(
      'success' => true,
      'result' => $responseBodyList
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

  private function getTargetChatLogByHistoryId($historyId) {
    return $this->THistoryChatLog->findByTHistoriesId($historyId);
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

  private function getAllChatLogsByEntityHistoryId($chatLog) {
    return $this->THistoryChatLog->find('all', array(
      'alias' => 'THistoryChatLog',
      'fields' => array('THistoryChatLog.*','MUser.*'),
      'conditions' => array('THistoryChatLog.m_companies_id' => $chatLog['THistoryChatLog']['m_companies_id'],
        'THistoryChatLog.t_histories_id' => $chatLog['THistoryChatLog']['t_histories_id']
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

  private function getTargetCustomerInfoByVisitorId($m_companies_id, $visitor_id) {
    $customerInfo = $this->MCustomer->find('first', array(
      'conditions' => array(
        'm_companies_id' => $m_companies_id,
        'visitors_id' => $visitor_id
      )
    ));
    $customerInfoObj = json_decode($customerInfo['MCustomer']['informations'],TRUE);
    $settings = $this->getAllCustomerSettingsForNotifyMail($m_companies_id);
    $returnVal = array();
    foreach($settings as $index => $obj) {
      if(strcmp($obj['TCustomerInformationSetting']['show_send_mail_flg'], 1) === 0
        && isset($customerInfoObj[$obj['TCustomerInformationSetting']['item_name']])
        && strcmp($customerInfoObj[$obj['TCustomerInformationSetting']['item_name']], "") !== 0) {
        $returnVal[$obj['TCustomerInformationSetting']['item_name']] = $customerInfoObj[$obj['TCustomerInformationSetting']['item_name']];
      }
    }
    return $returnVal;
  }

  private function getAllCustomerSettingsForNotifyMail($m_companies_id) {
    return $this->TCustomerInformationSetting->find('all', array(
      'conditions' => array(
        'AND' => array('m_companies_id' => $m_companies_id, 'show_send_mail_flg' => 1),
        'NOT' => array('delete_flg' => 1)
      ),
      'order' => array('sort')
    ));
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

  private function getCoreSettingsById($m_companies_id) {
    $data = $this->MCompany->findById($m_companies_id);
    if(!empty($data)) {
      return json_decode($data['MCompany']['core_settings'], TRUE);
    } else {
      return [];
    }
  }

  private function getTransmissionConfigById($id) {
    return $this->MMailTransmissionSetting->findById($id);
  }

  /**
   * array_key_exists_recursive
   * 多次元配列中に指定したキーがあるか探索し、存在する場合はその値を返す
   * @param  Array $targetArray   探索対象の配列
   * @param  String $targetKey    検索したいキー
   * @return String               検索結果の値
   */
  private function array_key_exists_recursive($targetArray, $targetKey) {
    if (array_key_exists($targetKey, $targetArray)) {
      return $targetArray[$targetKey];
    }

    foreach ($targetArray as $key => $value) {
      if (is_array($value)) {
        $resultValue = $this->array_key_exists_recursive($value, $targetKey);
        if (!is_null($resultValue)) {
          return $resultValue;
        }
      }
    }
    return;
  }
}
