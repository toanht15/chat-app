<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/01/15
 * Time: 16:05
 */

App::uses('ExcelParserComponent', 'Controller/Component');

class AutoMessageExcelParserComponent extends ExcelParserComponent
{
  const CONTENT_BOL = "BOL";
  const CONTENT_EOL = "EOL";

  const ROW_NAME = 'A';
  const ROW_CONTAINS = 'B';
  const ROW_CONTAINS_TYPE = 'C';
  const ROW_EXCLUSIONS = 'D';
  const ROW_EXCLUSIONS_TYPE = 'E';
  const ROW_KEYWORD_CONDITION = 'F';
  const ROW_TRIGGER_TIME_SEC = 'G';
  const ROW_TRIGGER_CONDITION = 'H';
  const ROW_MESSAGE = 'I';
  const ROW_FREE_INPUT = 'J';
  const ROW_CV = 'K';
  const ROW_ACTIVE = 'L';

  private $containsTypeMap;
  private $exclusionsTypeMap;
  private $keywordConditionMap;
  private $triggerConditionMap;
  private $triggerFreeInputMap;
  private $triggerCVMap;
  private $activeFlgMap;

  public function __construct($filePath)
  {
    parent::__construct($filePath);
    $this->readSettingMapFromConfig();
  }

  public function getImportData() {
    $this->readData();
    $this->setActiveSheet(0);
  }

  public function toArray() {
    $importData = [];
    $isNextHeader = false;
    $isNextContent = false;
    foreach($this->dataArray as $index => $row) {
      if($isNextContent) {
        // データ取得処理
        if($this->isEOLrow($row)) break; // EOL行であればbreak
        if(empty($row[self::ROW_NAME])) continue;
        // バリデーションは呼び出し元で実行すること
        array_push($importData,[
          'rowNum' => $index+1,
          'name' => $row[self::ROW_NAME],
          'keyword_contains' => !empty(trim($row[self::ROW_CONTAINS])) ? trim($row[self::ROW_CONTAINS]) : "",
          'keyword_contains_type' => $this->containsTypeMap[$row[self::ROW_CONTAINS_TYPE]],
          'keyword_exclusions' => !empty(trim($row[self::ROW_EXCLUSIONS])) ? trim($row[self::ROW_EXCLUSIONS]) : "",
          'keyword_exclusions_type' => $this->exclusionsTypeMap[$row[self::ROW_EXCLUSIONS_TYPE]],
          'speechContentCond' => $this->keywordConditionMap[$row[self::ROW_KEYWORD_CONDITION]],
          'triggerTimeSec' => intval($row[self::ROW_TRIGGER_TIME_SEC]),
          'speechTriggerCond' => $this->triggerConditionMap[$row[self::ROW_TRIGGER_CONDITION]],
          'action' => $this->convertSpecialChars($row[self::ROW_MESSAGE]),
          'chat_textarea' => $this->triggerFreeInputMap[$row[self::ROW_FREE_INPUT]],
          'cv' => $this->triggerCVMap[$row[self::ROW_CV]],
          // FIXME メール送信対応
          'send_mail_flg' => 0,
          'active_flg' => $this->activeFlgMap[$row[self::ROW_ACTIVE]]
        ]);
      } else if(!$isNextContent && $this->isBOLrow($row)) {
        //BOLの文字列があったら次の行がヘッダー
        $isNextHeader = true;
      } else if($isNextHeader) {
        // ヘッダーの次の行から取り込みデータ
        $isNextHeader = false;
        $isNextContent = true;
      }
    }
    return $importData;
  }

  /**
   * Excelで利用する各設定項目を設定ファイル等から読み込む
   * 注意：Excelのフォーマットで指定した項目と完全一致する必要がある。
   */
  private function readSettingMapFromConfig() {
    $this->containsTypeMap = [
      'すべて含む' => 1,
      'いずれかを含む' => 2
    ];

    $this->exclusionsTypeMap = [
        'すべて含まない' => 1,
        'いずれかを含まない' => 2
    ];

    $this->keywordConditionMap = [
      '完全一致' => 1,
      '部分一致' => 2
    ];

    $this->triggerConditionMap = [
      "１回のみ有効" => 1,
      "何度でも有効" => 2
    ];

    $this->triggerFreeInputMap = [
      Configure::read('outMessageTextarea')[1] => 1,
      Configure::read('outMessageTextarea')[2] => 2
    ];
    $this->triggerCVMap = [
      Configure::read('outMessageCvType')[1] => 1,
      Configure::read('outMessageCvType')[2] => 2
    ];
    $this->activeFlgMap = [
      '有効' => 0,
      '無効' => 1
    ];
  }

  private function isBOLrow($row) {
    return strcmp($this->t($row['A']), self::CONTENT_BOL) === 0;
  }

  private function isEOLrow($row) {
    return strcmp($this->t($row['A']), self::CONTENT_EOL) === 0;
  }

  private function t($str) {
    return trim($str);
  }

  private function convertSpecialChars($action) {
      $matches = [];
      if ( preg_match_all("/\[\[(.*?)\]\]/", $action, $matches) ) {
        $matchPattern = $matches[0];
        $matchChar = $matches[1];
        foreach($matchPattern as $index => $pattern) {
          $action = preg_replace("/".preg_quote($pattern,"/")."/", "[] ".$matchChar[$index], $action);
        }
      }
      if ( preg_match_all('/<<(.*?)>>/', $action, $matches)) {
        $matchPattern = $matches[0];
        $matchChar = $matches[1];
        foreach($matchPattern as $index => $pattern) {
          $action = preg_replace("/".preg_quote($pattern,"/")."/", "<telno>".$matchChar[$index]."</telno>", $action);
        }
      }
      return $action;
  }
}