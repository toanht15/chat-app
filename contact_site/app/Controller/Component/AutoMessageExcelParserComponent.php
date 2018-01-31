<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/01/15
 * Time: 16:05
 */

App::uses('AutoMessageException','Lib/Error');
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
  const ROW_MAIL_ADDRESS_1 = 'M';
  const ROW_MAIL_ADDRESS_2 = 'N';
  const ROW_MAIL_ADDRESS_3 = 'O';
  const ROW_MAIL_ADDRESS_4 = 'P';
  const ROW_MAIL_ADDRESS_5 = 'Q';
  const ROW_MAIL_TITLE = 'R';
  const ROW_MAIL_FROM_NAME = 'S';

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
    $errorFound = false;
    foreach($this->dataArray as $index => $row) {
      if($isNextContent) {
        // データ取得処理
        if($this->isEOLrow($row)) break; // EOL行であればbreak
        if(empty($row[self::ROW_NAME]) || $this->isSampleDataRow($index)) continue; // 名称が空であればその行は読み込まない
        // バリデーションは呼び出し元で実行すること
        $errors = $this->rowValidate($row);
        if(!empty($errors)) {
          $errorFound = true;
          $errorArray[$index] = $errors;
        } else if(!empty($row[self::ROW_NAME])) {
          array_push($importData, [
            'rowNum' => $index,
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
            'send_mail_flg' => $this->isSendMailFlgActive($row),
            'mail_address_1' => !empty(trim($row[self::ROW_MAIL_ADDRESS_1])) ? trim($row[self::ROW_MAIL_ADDRESS_1]) : "",
            'mail_address_2' => !empty(trim($row[self::ROW_MAIL_ADDRESS_2])) ? trim($row[self::ROW_MAIL_ADDRESS_2]) : "",
            'mail_address_3' => !empty(trim($row[self::ROW_MAIL_ADDRESS_3])) ? trim($row[self::ROW_MAIL_ADDRESS_3]) : "",
            'mail_address_4' => !empty(trim($row[self::ROW_MAIL_ADDRESS_4])) ? trim($row[self::ROW_MAIL_ADDRESS_4]) : "",
            'mail_address_5' => !empty(trim($row[self::ROW_MAIL_ADDRESS_5])) ? trim($row[self::ROW_MAIL_ADDRESS_5]) : "",
            'mail_subject' => !empty(trim($row[self::ROW_MAIL_TITLE])) ? trim($row[self::ROW_MAIL_TITLE]) : "",
            'mail_from_name' => !empty(trim($row[self::ROW_MAIL_FROM_NAME])) ? trim($row[self::ROW_MAIL_FROM_NAME]) : "",
            'active_flg' => $this->activeFlgMap[$row[self::ROW_ACTIVE]]
          ]);
        }
      } else if(!$isNextContent && $this->isBOLrow($row)) {
        //BOLの文字列があったら次の行がヘッダー
        $isNextHeader = true;
      } else if($isNextHeader) {
        // ヘッダーの次の行から取り込みデータ
        $isNextHeader = false;
        $isNextContent = true;
      }
    }
    if($errorFound) {
      $exception = new AutoMessageException("Excelデータバリデーションエラー", 200);
      $exception->setErrors($errorArray);
      throw $exception;
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
    return strcmp($this->t($row['T']), self::CONTENT_BOL) === 0;
  }

  private function isEOLrow($row) {
    return strcmp($this->t($row['T']), self::CONTENT_EOL) === 0;
  }

  private function isSampleDataRow($index) {
    return strcmp($index, 9) === 0 || strcmp($index, 10) === 0;
  }

  private function t($str) {
    return trim($str);
  }

  private function isSendMailFlgActive($row) {
    $return = 0;

    // メールアドレス設定が１つでもあれば有効とする
    if(!empty($row[self::ROW_MAIL_ADDRESS_1])
      || !empty($row[self::ROW_MAIL_ADDRESS_2])
      || !empty($row[self::ROW_MAIL_ADDRESS_3])
      || !empty($row[self::ROW_MAIL_ADDRESS_4])
      || !empty($row[self::ROW_MAIL_ADDRESS_5])) {
      $return = 1;
    }
    return $return;
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

  private function rowValidate($data) {
    $errors = [];
    if(!Validation::maxLength($data[self::ROW_NAME], 50)) {
      $this->addError($errors, self::ROW_NAME,'５０文字以内で入力してください');
    }
    if(empty($data[self::ROW_CONTAINS]) && empty($data[self::ROW_EXCLUSIONS])) {
      $this->addError($errors, self::ROW_CONTAINS,'キーワードはいずれかの指定が必須です');
    }
    if(empty($this->containsTypeMap[$data[self::ROW_CONTAINS_TYPE]])) {
      $this->addError($errors, self::ROW_CONTAINS_TYPE,'すべて含む／いずれかを含む のいずれかの指定のみ可能です');
    }
    if(empty($this->exclusionsTypeMap[$data[self::ROW_EXCLUSIONS_TYPE]])) {
      $this->addError($errors, self::ROW_EXCLUSIONS_TYPE,'すべて含まない／いずれかを含まない のいずれかの指定のみ可能です');
    }
    if(empty($this->keywordConditionMap[$data[self::ROW_KEYWORD_CONDITION]])) {
      $this->addError($errors, self::ROW_KEYWORD_CONDITION,'完全一致／部分一致 のいずれかの指定のみ可能です');
    }
    if(!Validation::range($data[self::ROW_TRIGGER_TIME_SEC], 0, 61)) {
      $this->addError($errors, self::ROW_TRIGGER_TIME_SEC,'1から60までの数値指定のみ可能です');
    }
    if(empty($this->triggerConditionMap[$data[self::ROW_TRIGGER_CONDITION]])) {
      $this->addError($errors, self::ROW_TRIGGER_CONDITION,'１回のみ有効／何度でも有効 のいずれかの指定のみ可能です');
    }
    if(empty($data[self::ROW_MESSAGE])) {
      $this->addError($errors, self::ROW_MESSAGE,'メッセージの指定は必須です');
    }
    if(empty($this->triggerFreeInputMap[$data[self::ROW_FREE_INPUT]])) {
      $this->addError($errors, self::ROW_KEYWORD_CONDITION,'ON（自由入力可）／OFF（自由入力不可） のいずれかの指定のみ可能です');
    }
    if(empty($this->triggerCVMap[$data[self::ROW_CV]])) {
      $this->addError($errors, self::ROW_CV,'する／しない のいずれかの指定のみ可能です');
    }
    if(empty($this->activeFlgMap[$data[self::ROW_ACTIVE]]) && @$this->activeFlgMap[$data[self::ROW_ACTIVE]] !== 0) {
      $this->addError($errors, self::ROW_ACTIVE,'有効／無効 のいずれかの指定のみ可能です');
    }
    if(!empty($data[self::ROW_MAIL_ADDRESS_1]) && !Validation::email($data[self::ROW_MAIL_ADDRESS_1])) {
      $this->addError($errors, self::ROW_MAIL_ADDRESS_1,'メールアドレスのみ指定可能です');
    }
    if(!empty($data[self::ROW_MAIL_ADDRESS_2]) && !Validation::email($data[self::ROW_MAIL_ADDRESS_2])) {
      $this->addError($errors, self::ROW_MAIL_ADDRESS_2,'メールアドレスのみ指定可能です');
    }
    if(!empty($data[self::ROW_MAIL_ADDRESS_3]) && !Validation::email($data[self::ROW_MAIL_ADDRESS_3])) {
      $this->addError($errors, self::ROW_MAIL_ADDRESS_3,'メールアドレスのみ指定可能です');
    }
    if(!empty($data[self::ROW_MAIL_ADDRESS_4]) && !Validation::email($data[self::ROW_MAIL_ADDRESS_4])) {
      $this->addError($errors, self::ROW_MAIL_ADDRESS_4,'メールアドレスのみ指定可能です');
    }
    if(!empty($data[self::ROW_MAIL_ADDRESS_5]) && !Validation::email($data[self::ROW_MAIL_ADDRESS_5])) {
      $this->addError($errors, self::ROW_MAIL_ADDRESS_5,'メールアドレスのみ指定可能です');
    }
    if(!empty($data[self::ROW_MAIL_ADDRESS_5]) && !Validation::email($data[self::ROW_MAIL_ADDRESS_5])) {
      $this->addError($errors, self::ROW_MAIL_ADDRESS_5,'メールアドレスのみ指定可能です');
    }
    if($this->isSendMailFlgActive($data) && empty($data[self::ROW_MAIL_TITLE])) {
      $this->addError($errors, self::ROW_MAIL_TITLE,'メールタイトルの指定は必須です');
    }
    if($this->isSendMailFlgActive($data) && !Validation::maxLength($data[self::ROW_MAIL_TITLE], 100)) {
      $this->addError($errors, self::ROW_MAIL_TITLE,'メールタイトルは１００文字以内で設定してください');
    }
    if($this->isSendMailFlgActive($data) && empty($data[self::ROW_MAIL_FROM_NAME])) {
      $this->addError($errors, self::ROW_MAIL_FROM_NAME,'差出人名の指定は必須です');
    }
    if($this->isSendMailFlgActive($data) && !Validation::maxLength($data[self::ROW_MAIL_FROM_NAME], 100)) {
      $this->addError($errors, self::ROW_MAIL_FROM_NAME,'差出人名は１００文字以内で設定してください');
    }
    return $errors;
  }

  /**
   * @param $errors
   */
  private function addError(&$errors, $type, $message)
  {
    if (empty($errors[$type])) $errors[$type] = [];
    array_push($errors[$type], $message);
  }
}