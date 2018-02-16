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

  const MAIL_TYPE_CD = 'CS001';

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

  public function setSenarioRequiredData($mailType, $variables, $templateId, $chatLogs, $stayLog, $campaigns, $landscapeData = null) {
    parent::setRequiredData($templateId, $chatLogs, $stayLog, $campaigns, $landscapeData);
    $this->type = $mailType;
    $this->variables = $variables;
  }

  public function createMessageBody() {
    $this->readTemplate();
    $this->prepareScenarioMessageBlock();
    $this->body = str_replace(self::REPLACE_TARGET_SCENARIO_MESSAGE_BLOCK_DELIMITER, $this->scenarioMessageBlock, $this->template['MMailTemplate']['template']);
  }

  private function prepareScenarioMessageBlock() {
    switch($this->type) {
      case "1":
        $this->createMetaDataMessage();
        $this->createMessages();
        break;
      case "2":
        $this->createMetaDataMessage();
        $this->createVariablesMessageBlock();
        break;
      case "3":
        break;
    }
  }

  /**
   * @override
   */
  protected function createMetaDataMessage() {
    $this->scenarioMessageBlock  = "シナリオ実行ページタイトル：".$this->stayLog['THistoryStayLog']['title']."\n";
    $this->scenarioMessageBlock .= "シナリオ実行ページＵＲＬ　：".$this->stayLog['THistoryStayLog']['url']."\n";
    $this->scenarioMessageBlock .= "キャンペーン　　　　　　　：".$this->concatCampaign($this->stayLog['THistoryStayLog']['url'])."\n";
    if(!empty($this->landscapeData) && !empty($this->landscapeData['MLandscapeData']['org_name'])) {
      $this->scenarioMessageBlock .= "企業名　　　　　　　　　　：".$this->landscapeData['MLandscapeData']['org_name']."\n";
    }
  }

  private function createMessages() {
    foreach($this->chatLogs as $k => $v) {
      $this->scenarioMessageBlock .= $this->generateMessageBlockStr($v['THistoryChatLog'], $v['MUser'])."\n";
    }
  }

  private function createVariablesMessageBlock() {
    foreach($this->variables as $variableName => $value) {
      $this->scenarioMessageBlock .= $variableName."：".$value;
    }
  }
}