<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/12/07
 * Time: 10:34
 */

App::uses('MailTemplateComponent', 'Controller/Component');

class AutoMessageMailTemplateComponent extends MailTemplateComponent {
  const SEND_NAME_CONSUMER = '訪問者';
  const SEND_NAME_CONSUMER_SCENARIO_HEARING = '訪問者（ヒアリング回答）';
  const SEND_NAME_CONSUMER_SCENARIO_SELECTION = '訪問者（選択肢回答）';
  const SEND_NAME_OPERATOR = 'オペレータ';
  const SEND_NAME_AUTO_MESSAGE = '自動応答';
  const SEND_NAME_SORRY_MESSAGE = '自動応答（sorry）';
  const SEND_NAME_AUTO_SPEECH_MESSAGE = '自動返信';
  const SEND_NAME_FILE_TRANSFER = 'ファイル送信';
  const SEND_NAME_FILE_RECEIVE = 'ファイル受信';
  const SEND_NAME_CLICK_LINK = 'リンククリック';
  const SEND_NAME_SCENARIO_TEXT = 'シナリオメッセージ（テキスト発言）';
  const SEND_NAME_SCENARIO_HEARING = 'シナリオメッセージ（ヒアリング）';
  const SEND_NAME_SCENARIO_SELECTION = 'シナリオメッセージ（選択肢）';
  const SEND_NAME_SCENARIO_ANSWER_BULK_HEARING = 'シナリオメッセージ（一括ヒアリング回答）';
  const SEND_NAME_SCENARIO_RETURN_BULK_HEARING = 'シナリオメッセージ（一括ヒアリング解析結果）';
  const SEND_NAME_SCENARIO_MODIFY_BULK_HEARING = 'シナリオメッセージ（一括ヒアリング内容修正）';
  const SEND_NAME_SCENARIO_HEARING_REINPUT = 'シナリオメッセージ(ヒアリング再回答)';

  const REPLACE_TARGET_AUTO_MESSAGE_BLOCK_DELIMITER = '##AUTO_MESSAGE_BLOCK##';

  const DATETIME_FORMAT = 'Y/m/d H:i:s';
  const MESSAGE_SEPARATOR = '=================================================================';
  const RECEIVE_FILE_MESSAGE_SEPARATOR = '--------------------------------------------------------------------------------------------------------';
  const CAMPAIGN_SEPARATOR = "｜";

  const MAIL_TYPE_CD = 'AM001';

  protected $templateId;
  protected $template;
  protected $chatLogs;
  protected $stayLog;
  protected $campaigns;
  protected $landscapeData;
  protected $customerInfo;

  private $autoMessageBlock;

  /**
   * AutoMessageMailTemplateComponent constructor.
   * @param $templateId 利用するテンプレートマスタのID
   * @param THistoryChatLog $chatLogs 出力対象のチャットログにMUsersをLEFT JOINしたエンティティ
   * @param THistoryStayLog $stayLogs 出力対象の滞在履歴
   * @param TCampaign $campaigns キャンペーン設定すべて（m_companies_id一致）
   * @param MLandscapeData $landscapeData 出力対象の企業情報
   */
  public function __construct() {

  }

  public function setRequiredData($templateId, $chatLogs, $stayLog, $campaigns, $landscapeData = null, $customerInfo = array()) {
    $this->templateId = $templateId;
    $this->chatLogs = $chatLogs;
    $this->stayLog = $stayLog;
    $this->campaigns = $campaigns;
    $this->landscapeData = $landscapeData;
    $this->customerInfo = $customerInfo;
  }

  public function createMessageBody() {
    $this->readTemplate();
    $this->prepareAutoMessageBlock();
    $this->body = str_replace(self::REPLACE_TARGET_AUTO_MESSAGE_BLOCK_DELIMITER, $this->autoMessageBlock, $this->template['MMailTemplate']['template']);
  }

  protected function readTemplate() {
    $mailTemplate = ClassRegistry::init('MMailTemplate');
    $this->template = $mailTemplate->findById($this->templateId);
  }

  protected function prepareAutoMessageBlock() {
    $this->createMetaDataMessage(true, null);
    $this->createAutoMessages();
  }

  /**
   * @param $isFullData
   * @param $withDownloadURL
   */
  protected function createMetaDataMessage($isFullData, $withDownloadURL) {
    $this->autoMessageBlock  = "チャット送信ページタイトル：".$this->stayLog['title']."\n";
    $this->autoMessageBlock .= "チャット送信ページＵＲＬ　：".$this->stayLog['url']."\n";
    $this->autoMessageBlock .= "キャンペーン　　　　　　　：".$this->concatCampaign($this->stayLog['campaignUrl'])."\n";
    if(!empty($this->landscapeData) && !empty($this->landscapeData['MLandscapeData']['org_name'])) {
    $this->autoMessageBlock .= "企業名　　　　　　　　　　：".$this->landscapeData['MLandscapeData']['org_name']."\n";
    }
    if(!empty($this->customerInfo) && count($this->customerInfo) > 0) {
      $this->autoMessageBlock .= "\n";
      foreach($this->customerInfo as $key => $value) {
        $this->autoMessageBlock .= $key."：".$value."\n";
      }
      $this->autoMessageBlock .= "\n";
    }
  }

  protected function createAutoMessages() {
    foreach($this->chatLogs as $k => $v) {
      $this->autoMessageBlock .= $this->generateMessageBlockStr($v['THistoryChatLog'], $v['MUser'])."\n";
    }
  }

  protected function concatCampaign($url) {
    $campaignParam = "";
    $tmp = mb_strstr($url, '?');
    if ( $tmp !== "" ) {
      foreach($this->campaigns as $k => $v){
        if ( strpos($tmp, $v['TCampaign']['parameter']) !== false ) {
          if ( $campaignParam !== "" ) {
            $campaignParam .= self::CAMPAIGN_SEPARATOR;
          }
          $campaignParam .= $v['TCampaign']['name'];
        }
      }
    }
    return $campaignParam;
  }

  protected function generateMessageBlockStr($chatLog, $user) {
    $message = "";
    switch($chatLog['message_type']) {
      case 1:
        $message = $this->generateConsumerMessageBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 2:
        $message = $this->generateOperatorMessageBlockStr($chatLog['created'],$user['display_name'],$chatLog['message']);
        break;
      case 3:
        $message = $this->generateAutoMessageBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 4:
        $message = $this->generateSorryMessageBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 5:
        $message = $this->generateAutoSpeechBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 6:
        $message = $this->generateFileSendBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 7:
        $message = $this->generateScenarioMessageBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 8:
        $message = $this->generateLinkClickBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 12:
        $message = $this->generateConsumerScenarioHearingMessageBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 13:
        $message = $this->generateConsumerScenarioSelectionMessageBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 19:
        $message = $this->generateFileReceiveBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 21:
        $message = $this->generateScenarioTextBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 22:
        $message = $this->generateScenarioHearingBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 23:
        $message = $this->generateScenarioSelectionBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 27:
        $message = $this->generateScenarioSendFileBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 30:
        $message = $this->generateScenarioAnswerBulkHearingBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 31:
        break;
      case 32:
        $message = $this->generateScenarioModifyBulkHearingBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 33:
        $message = $this->generateScenarioHearingBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 34:
        $message = $this->generateScenarioHearingBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 35:
        $message = $this->generateScenarioHearingBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 36:
        $message = $this->generateScenarioReInputHearingBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 37:
        $message = $this->generateScenarioReInputHearingBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 38:
        $message = $this->generateScenarioReInputHearingBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 39:
        $message = $this->generateScenarioReInputHearingBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 40:
        $message = $this->generateScenarioReturnBulkHearingBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 41:
        $message = $this->generateScenarioHearingBlockStr($chatLog['created'],$chatLog['message']['message']);
        break;
      case 42:
        $message = $this->generateScenarioHearingBlockStr($chatLog['created'],$chatLog['message']['message']);
        break;
      case 81:
        $message = $this->generateAutoMessageBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 82:
        $message = $this->generateAutoMessageBlockStr($chatLog['created'],$chatLog['message']);
        break;
      case 90:
        break;
      case 998:
        $message = $this->generateOperatorEnteredBlockStr($chatLog['created'],$user['display_name']);
        break;
      case 999:
        $message = $this->generateOperatorLeavedBlockStr($chatLog['created'],$user['display_name']);
        break;
      default:
        throw new InvalidArgumentException('不明なmessageType : ' . $chatLog);
    }
    return $message;
  }

  protected function generateConsumerMessageBlockStr($date, $content) {
    $message = self::MESSAGE_SEPARATOR."\n";
    $message .= $this->createMessageBlockHeader($date, self::SEND_NAME_CONSUMER);
    $message .= $this->createMessageContent($content);
    return $message;
  }

  protected function generateOperatorMessageBlockStr($date, $operatorName, $content) {
    $message = self::MESSAGE_SEPARATOR."\n";
    $message .= $this->createMessageBlockHeader($date, $operatorName);
    $message .= $this->createMessageContent($content);
    return $message;
  }

  protected function generateAutoMessageBlockStr($date, $content) {
    $message = self::MESSAGE_SEPARATOR."\n";
    $message .= $this->createMessageBlockHeader($date, self::SEND_NAME_AUTO_MESSAGE);
    $message .= $this->createMessageContent($content);
    return $message;
  }

  protected function generateFileSendBlockStr($date, $content) {
    $message = self::MESSAGE_SEPARATOR."\n";
    $message .= $this->createMessageBlockHeader($date, self::SEND_NAME_FILE_TRANSFER);
    $message .= $this->createFileTransferMessageContent($content);
    return $message;
  }

  protected function generateFileReceiveBlockStr($date, $content) {
    $obj = json_decode($content,TRUE);
    if(is_array($obj)) {
      $message = self::MESSAGE_SEPARATOR."\n";
      $message .= $this->createMessageBlockHeader($date, self::SEND_NAME_FILE_RECEIVE);
      $message .= $this->createReceiveFileMessageContent($content);
    } else {
      $message = self::MESSAGE_SEPARATOR."\n";
      $message .= $this->createMessageBlockHeader($date, self::SEND_NAME_FILE_RECEIVE);
      $message .= $this->createMessageContent("（".$content."）");
    }
    return $message;
  }

  protected function generateSorryMessageBlockStr($date, $content) {
    $message = self::MESSAGE_SEPARATOR."\n";
    $message .= $this->createMessageBlockHeader($date, self::SEND_NAME_SORRY_MESSAGE);
    $message .= $this->createMessageContent($content);
    return $message;
  }

  protected function generateAutoSpeechBlockStr($date, $content) {
    $message = self::MESSAGE_SEPARATOR."\n";
    $message .= $this->createMessageBlockHeader($date, self::SEND_NAME_AUTO_SPEECH_MESSAGE);
    $message .= $this->createMessageContent($content);
    return $message;
  }

  protected function generateLinkClickBlockStr($date, $content) {
    $message = self::MESSAGE_SEPARATOR."\n";
    $message .= $this->createMessageBlockHeader($date, self::SEND_NAME_CLICK_LINK);
    $message .= $this->createMessageContent("（「".$content."」をクリック）");
    return $message;
  }

  protected function generateConsumerScenarioHearingMessageBlockStr($date, $content) {
    $message = self::MESSAGE_SEPARATOR."\n";
    $message .= $this->createMessageBlockHeader($date, self::SEND_NAME_CONSUMER_SCENARIO_HEARING);
    $message .= $this->createMessageContent($content);
    return $message;
  }

  protected function generateConsumerScenarioSelectionMessageBlockStr($date, $content) {
    $message = self::MESSAGE_SEPARATOR."\n";
    $message .= $this->createMessageBlockHeader($date, self::SEND_NAME_CONSUMER_SCENARIO_SELECTION);
    $message .= $this->createMessageContent($content);
    return $message;
  }

  protected function generateScenarioTextBlockStr($date, $content) {
    $message = self::MESSAGE_SEPARATOR."\n";
    $message .= $this->createMessageBlockHeader($date, self::SEND_NAME_SCENARIO_TEXT);
    $message .= $this->createMessageContent($content);
    return $message;
  }

  protected function generateScenarioHearingBlockStr($date, $content) {
    $message = self::MESSAGE_SEPARATOR."\n";
    $message .= $this->createMessageBlockHeader($date, self::SEND_NAME_SCENARIO_HEARING);
    $message .= $this->createMessageContent($content);
    return $message;
  }

  protected function generateScenarioReInputHearingBlockStr($date, $content) {
    $message = self::MESSAGE_SEPARATOR."\n";
    $message .= $this->createMessageBlockHeader($date, self::SEND_NAME_SCENARIO_HEARING_REINPUT);
    $message .= $this->createMessageContent($content);
    return $message;
  }

  protected function generateScenarioSelectionBlockStr($date, $content) {
    $message = self::MESSAGE_SEPARATOR."\n";
    $message .= $this->createMessageBlockHeader($date, self::SEND_NAME_SCENARIO_SELECTION);
    $message .= $this->createMessageContent($content);
    return $message;
  }

  protected function generateScenarioSendFileBlockStr($date, $content) {
    $message = self::MESSAGE_SEPARATOR."\n";
    $message .= $this->createMessageBlockHeader($date, self::SEND_NAME_FILE_TRANSFER);
    $message .= $this->createFileTransferMessageContent($content);
    return $message;
  }

  protected function generateScenarioAnswerBulkHearingBlockStr($date, $content) {
    $message = self::MESSAGE_SEPARATOR."\n";
    $message .= $this->createMessageBlockHeader($date, self::SEND_NAME_SCENARIO_ANSWER_BULK_HEARING);
    $message .= $this->createMessageContent($content);
    return $message;
  }

  protected function generateScenarioReturnBulkHearingBlockStr($date, $content) {
    $message = self::MESSAGE_SEPARATOR."\n";
    $message .= $this->createMessageBlockHeader($date, self::SEND_NAME_SCENARIO_RETURN_BULK_HEARING);
    $json = json_decode($content, TRUE);
    foreach($json['target'] as $index => $object) {
      $message .= $object['label'].'：'.((!empty($json['message'][$object['inputType']])) ? $json['message'][$object['inputType']] : "（なし）")."\n";
    }
    return $message;
  }

  protected function generateScenarioModifyBulkHearingBlockStr($date, $content) {
    $message = self::MESSAGE_SEPARATOR."\n";
    $message .= $this->createMessageBlockHeader($date, self::SEND_NAME_SCENARIO_MODIFY_BULK_HEARING);
    $json = json_decode($content, TRUE);
    foreach($json as $variableName => $object) {
      $message .= $object['label'].'：'.($object['value'])."\n";
    }
    return $message;
  }

  protected function generateOperatorEnteredBlockStr($date, $operatorName) {
    $message = self::MESSAGE_SEPARATOR."\n";
    $message .= '入室日時：'.date(self::DATETIME_FORMAT, strtotime($date))."\n";
    $message .= '入室者　：'.$operatorName."\n";
    return $message;
  }

  protected function generateOperatorLeavedBlockStr($date, $operatorName) {
    $message = self::MESSAGE_SEPARATOR."\n";
    $message .= '退室日時：'.date(self::DATETIME_FORMAT, strtotime($date))."\n";
    $message .= '退室者　：'.$operatorName."\n";
    return $message;
  }

  private function generateScenarioMessageBlockStr($date, $content) {
    $message = self::MESSAGE_SEPARATOR."\n";
    $message .= $this->createMessageBlockHeader($date, self::SEND_NAME_AUTO_SPEECH_MESSAGE);
    $message .= $this->createMessageContent($content);
    return $message;
  }

  /**
   * @param $date
   * @param $name
   * @return string
   */
  protected function createMessageBlockHeader($date, $senderName) {
    $message = "";
    $message .= '送信日時：' . date(self::DATETIME_FORMAT, strtotime($date))."\n";
    $message .= '送信者　：'.$senderName."\n";
    $message .= '内容：'."\n";
    return $message;
  }

  protected function createMessageContent($content) {
    $message = "";
    $lines = $this->explodeContentByLine($content);
    foreach($lines as $line) {
      $message .= '　'.$line."\n";
    }
    return $message;
  }

  protected function createFileTransferMessageContent($content) {
    $message = "";
    $content = json_decode($content, TRUE);
    $message .= "　ファイル名【".$content['fileName']."】\n";
    return $message;
  }

  protected function createReceiveFileMessageContent($content) {
    $message = "";
    $content = json_decode($content, TRUE);
    $message .= "ダウンロードＵＲＬ：".$content['downloadUrl']."\n";
    $message .= "コメント：\n".$content['comment']."\n";
    return $message;
  }

  protected function explodeContentByLine($content) {
    return explode("\n", $content);
  }

}
