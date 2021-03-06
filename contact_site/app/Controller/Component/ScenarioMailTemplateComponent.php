<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/12/07
 * Time: 10:34
 */

App::uses('AutoMessageMailTemplateComponent', 'Controller/Component');

class ScenarioMailTemplateComponent extends AutoMessageMailTemplateComponent {

  const REPLACE_TARGET_SCENARIO_MESSAGE_BLOCK_DELIMITER = '##SCENARIO_ALL_MESSAGE_BLOCK##';

  const REPLACE_TARGET_SCENARIO_VARIABLES_BLOCK_DELIMITER = '##SCENARIO_VARIABLES_BLOCK##';
  const PREV_CHAT_HISTORY = '##PREV_CHAT_HISTORY##';
  const MAIL_INQUIRY_NUMBER = '##MAIL_INQUIRY_NUMBER##';

  const MAIL_TYPE_CD = 'CS001';

  const RECEIVE_FILE_VARIABLE_KEY = 's_sendfile_data';

  /**
   * @var Integer
   * 1: チャット内容を全てメールする
   * 2: 変数のみメールする
   * 3: カスタマイズする
   */
  private $type;
  private $variables;

  private $scenarioMessageBlock;

  public function __construct() {
    parent::__construct();
    $this->type = 0;
    $this->scenarioMessageBlock = "";
  }

  public function setSenarioRequiredData($mailType, $variables, $templateId, $chatLogs, $stayLog, $campaigns, $landscapeData = null, $customerInfo = array(), $mailInquiryNumber = 1) {
    parent::setRequiredData($templateId, $chatLogs, $stayLog, $campaigns, $landscapeData, $customerInfo, $mailInquiryNumber);
    $this->type = $mailType;
    $this->variables = $variables;
  }

  public function createMessageBody($withDownloadURL = false) {
    $this->readTemplate();
    $this->prepareScenarioMessageBlock($withDownloadURL);
    $this->setScenarioMessageBlock();
  }

  public function replaceVariables($message) {
    $message = $this->replaceScenarioVariables($message);
    $message = $this->replaceInquiryNumber($message);

    return $message;
  }

  public function replaceScenarioVariables($message)
  {
    foreach($this->variables as $variable => $value) {
      if(strcmp($variable, self::RECEIVE_FILE_VARIABLE_KEY) === 0) continue;

      if ($this->isJson($value)) {
        $data = json_decode($value, true);
        if (isset($data['message']) && isset($data['separator'])) {
          $value = trim($this->createHearingAnswerMessageContent($data['message'], $data['separator']), "\n");
        }
      }
      $qvariable = preg_quote($variable, '/');
      $message = preg_replace("/{{(".$qvariable.")\}}/", $value, $message);
    }
    return $message;
  }

  public function replaceInquiryNumber($message)
  {
    return str_replace(self::MAIL_INQUIRY_NUMBER, $this->mailInquiryNumber, $message);
  }


  private function prepareScenarioMessageBlock($withDownloadURL) {
    switch($this->type) {
      case "1":
        $this->createMetaDataMessage(true, $withDownloadURL);
        $this->createMessages();
        break;
      case "2":
        $this->createMetaDataMessage(true, $withDownloadURL);
        $this->createVariablesMessageBlock();
        break;
      case "3":
        $this->createMetaDataMessage(false, $withDownloadURL);
        $this->createMessages();
        break;
    }
  }

  private function setScenarioMessageBlock() {
    switch($this->type) {
      case "1":
        $this->body = str_replace(self::REPLACE_TARGET_SCENARIO_MESSAGE_BLOCK_DELIMITER, $this->scenarioMessageBlock, $this->template['MMailTemplate']['template']);
        break;
      case "2":
        $this->body = str_replace(self::REPLACE_TARGET_SCENARIO_VARIABLES_BLOCK_DELIMITER, $this->scenarioMessageBlock, $this->template['MMailTemplate']['template']);
        break;
      case "3":
        $this->body = $this->replaceVariables($this->template['MMailTemplate']['template']);
        $this->body = str_replace(self::PREV_CHAT_HISTORY, "\n" . $this->scenarioMessageBlock, $this->body);
        break;
    }
  }

  /**
   * @override
   * @param $isFullData
   * @param $withDownloadURL
   */
  protected function createMetaDataMessage($isFullData, $withDownloadURL) {
    if($isFullData) {
      $this->scenarioMessageBlock .= "シナリオ実行ページタイトル：".$this->stayLog['title']."\n";
      $this->scenarioMessageBlock .= "シナリオ実行ページＵＲＬ　：".$this->stayLog['url']."\n";
      $this->scenarioMessageBlock .= "キャンペーン　　　　　　　：".$this->concatCampaign($this->stayLog['campaignUrl'])."\n";
      if(!empty($this->landscapeData) && !empty($this->landscapeData['MLandscapeData']['org_name'])) {
        $this->scenarioMessageBlock .= "企業名　　　　　　　　　　：".$this->landscapeData['MLandscapeData']['org_name']."\n";
      }
      if(!empty($this->customerInfo) && count($this->customerInfo) > 0) {
        $this->scenarioMessageBlock .= "\n";
        foreach($this->customerInfo as $key => $value) {
          $this->scenarioMessageBlock .= $key."：".$value."\n";
        }
      }
    }
    if($withDownloadURL && !empty($this->variables[self::RECEIVE_FILE_VARIABLE_KEY])) {
      if(!empty($this->scenarioMessageBlock)) {
        $this->scenarioMessageBlock .= "\n";
      }
      $data = json_decode($this->variables[self::RECEIVE_FILE_VARIABLE_KEY], TRUE);
      foreach($data as $obj) {
        if(isset($obj['downloadUrl']) && isset($obj['comment'])) {
          $this->scenarioMessageBlock .= self::RECEIVE_FILE_MESSAGE_SEPARATOR."\n";
          $this->scenarioMessageBlock .= "ダウンロードＵＲＬ：".$obj['downloadUrl']."\n";
          $this->scenarioMessageBlock .= "コメント：\n".$obj['comment']."\n";
        } else if(isset($obj['canceled']) && isset($obj['message']) && $obj['canceled']) {
          $this->scenarioMessageBlock .= self::RECEIVE_FILE_MESSAGE_SEPARATOR."\n";
          $this->scenarioMessageBlock .= "ダウンロードＵＲＬ：（".$obj['message']."）\n";
        }
      }
      $this->scenarioMessageBlock .= self::RECEIVE_FILE_MESSAGE_SEPARATOR."\n\n";
    }
  }

  private function createMessages() {
    $canUseInquiryNumber = false;
    foreach($this->chatLogs as $k => $v) {
      if ($canUseInquiryNumber) {
        $this->scenarioMessageBlock .= str_replace(self::MAIL_INQUIRY_NUMBER, $this->mailInquiryNumber, $this->generateMessageBlockStr($v['THistoryChatLog'], $v['MUser']))."\n";
      } else {
        $this->scenarioMessageBlock .= $this->generateMessageBlockStr($v['THistoryChatLog'], $v['MUser'])."\n";
      }


    }
  }

  private function createVariablesMessageBlock() {
    $this->scenarioMessageBlock .= self::MESSAGE_SEPARATOR."\n";
    foreach ($this->variables as $variableName => $value) {
      if (strcmp($variableName, self::RECEIVE_FILE_VARIABLE_KEY) === 0) continue;
      if ($this->isJson($value)) {
        $data = json_decode($value, true);
        if (isset($data['message']) && isset($data['separator'])) {
          $value = trim($this->createHearingAnswerMessageContent($data['message'], $data['separator']), "\n");
        }
      }

      $this->scenarioMessageBlock .= $variableName . "：" . $value . "\n";
    }
  }

  private function isJson($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
  }
}
