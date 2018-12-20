<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/01/15
 * Time: 16:05
 */

App::uses('AutoMessageException','Lib/Error');
App::uses('ExcelParserComponent', 'Controller/Component');

class AutoMessageExcelExportComponent extends ExcelParserComponent
{
  const PLUS_ROW = 25;

  private $activeFlgMap;
  private $conditionTypeMap;
  private $widgetOpenMap;
  private $chatTextAreaMap;
  private $triggerCVMap;
  private $sendMailFlgMap;
  private $stayTimeCheckTypeMap;
  private $stayTimeTypeMap;
  private $visitCntCondMap;
  private $targetNameMap;
  private $kWDContainTypeMap;
  private $kWDExclusionTypeMap;
  private $stayPageCondTypeMap;
  private $exportWeekdayMap;
  private $speechTriggerCondMap;
  private $businessHourMap;
  private $isSettingMap;
  private $actionTypeMap;
  
  public function __construct($filePath)
  {
    parent::__construct($filePath);
    $this->readSettingMapFromConfig();
  }

  public function getImportData() {
    $this->readData();
    $this->setActiveSheet(0);
  }

  private function readSettingMapFromConfig() {
    $this->isSettingMap = [
      1 => 'する',
      2 => 'しない'
    ];

    $this->activeFlgMap = [
      0 => '有効',
      1 => '無効'
    ];

    $this->widgetOpenMap = [
      1 => '自動で最大化する',
      2 => '自動で最大化しない'
    ];

    $this->conditionTypeMap = [
      1 => 'すべて一致',
      2 => 'いずれかが一致'
    ];

    $this->chatTextAreaMap = [
      1 => Configure::read('outMessageTextarea')[1],
      2 => Configure::read('outMessageTextarea')[2]
    ];

    $this->triggerCVMap = [
      1 => Configure::read('outMessageCvType')[1],
      2 => Configure::read('outMessageCvType')[2]
    ];

    $this->sendMailFlgMap = [
      1 => 'する',
      0 => 'しない'
    ];

    $this->stayTimeCheckTypeMap = [
      1 => 'サイト',
      2 => 'ページ'
    ];

    $this->stayTimeTypeMap = [
      1 => '秒',
      2 => '分',
      3 => '時'
    ];

    $this->visitCntCondMap = [
      1 => '一致',
      2 => '以上',
      3 => '未満'
    ];

    $this->targetNameMap = [
      1 => 'ページ',
      2 => 'URL',
    ];

    $this->kWDContainTypeMap = [
      1 => 'をすぺて含む',
      2 => 'のいずれかを含む',
    ];

    $this->kWDExclusionTypeMap = [
      1 => 'をすぺて含む',
      2 => 'のいずれかを含む',
    ];

    $this->stayPageCondTypeMap = [
      1 => '完全一致',
      2 => '部分一致',
      3 => '不一致'
    ];

    $this->exportWeekdayMap = [
      'mon' => '月',
      'tue' => '火',
      'wed' => '水',
      'thu' => '木',
      'fri' => '金',
      'sat' => '土',
      'sun' => '日',
    ];

    $this->speechTriggerCondMap = [
      1 => '１回のみ有効',
      2 => '何度でも有効',
    ];

    $this->businessHourMap = [
      1 => '営業時間内',
      2 => '営業時間外'
    ];

    $this->actionTypeMap = [
      2 => 'シナリオを選択する',
      1 => 'チャットメッセージを送る'
    ];
  }

  /**
   * @param $data
   */
  public function export($data)
  {
    $maxRow = count($data) + self::PLUS_ROW;
    $this->generateTemplate($maxRow);
    $this->writeData($data);
    $this->exportData();
  }

  /**
   * @param $data
   */
  public function writeData($data)
  {
    $dataMap = array_combine(range(5, count($data) + 4), $data);
    foreach ($dataMap as $row => $value) {
      $json = json_decode($value['TAutoMessage']['activity'], true);
      // name
      $this->currentSheet->setCellValue('C' . $row, $value['TAutoMessage']['name']);
      // active_flg
      $this->currentSheet->setCellValue('B' . $row, $this->activeFlgMap[$value['TAutoMessage']['active_flg']]);
      // conditionType
      $this->currentSheet->setCellValue('D' . $row, $this->conditionTypeMap[$json['conditionType']]);
      // action type
      if ($value['TAutoMessage']['action_type'] == 2) {
        // select scenarios
        $this->writeScenarioData($json, $row, $value);
      } else {
        // send message
        $this->writeSendMessageData($json, $row, $value);
      }
      // 滞在時間
      $this->writeStayTimeData($json, $row);
      // 訪問回数
      $this->writeVisitCountData($json, $row);
      // URL
      $this->writeURLData($json, $row);
      // 曜日・時間
      $this->writeWeekdayData($json, $row);
      // 参照元URL
      $this->writeRefferURLData($json, $row);
      // 検索キーワード
      $this->writeSearchKeywordData($json, $row);
      // 発言内容
      $this->writeSpeechContentData($json, $row);
      // 最初に訪れたページ
      $this->writeFirstVisitPageData($json, $row);
      // 前のページ
      $this->writePreviousPageData($json, $row);
      // 営業時間
      $this->writeBusinessHourData($json, $row);
    }
  }

  public function generateTemplate($endRow)
  {
    // create first row
    $this->setRowDataValidation(5);
    $this->setRowConditionalFormat(5);
    // copy style first row to other row
    foreach (range(6, $endRow) as $row) {
      $this->setRowConditionalFormat($row);
      $this->copyRowStyle(5, $row);
      $this->setRowDataValidation($row);
    }
  }

  /**
   * @param $beginColumn
   * @param $row
   * @param $condition
   */
  public function setColumnConditionalFormat($beginColumn, $row, $condition)
  {
    $conditionCol   = ($beginColumn == 'BE' || $beginColumn == 'BQ') ? 'BD' : $beginColumn;
    $condition      = '$' . $conditionCol . $row . ' = "' . $condition . '"';
    $objConditional = new PHPExcel_Style_Conditional();
    $objConditional->setConditionType(PHPExcel_Style_Conditional::CONDITION_EXPRESSION);
    $objConditional->addCondition($condition);
    $objConditional->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getEndColor()->setARGB('FFBFBFBF');

    $conditionalStyles = $this->currentSheet->getStyle($beginColumn . $row)->getConditionalStyles();
    array_push($conditionalStyles, $objConditional);
    $this->setConditionStyle($beginColumn, $conditionalStyles, $row);
  }

  /**
   * @param $row
   */
  public function setRowConditionalFormat($row)
  {
    $this->setColumnConditionalFormat('E', $row, $this->isSettingMap[2]);
    $this->setColumnConditionalFormat('I', $row, $this->isSettingMap[2]);
    $this->setColumnConditionalFormat('L', $row, $this->isSettingMap[2]);
    $this->setColumnConditionalFormat('S', $row, $this->isSettingMap[2]);
    $this->setColumnConditionalFormat('U', $row, $this->isSettingMap[2]);
    $this->setColumnConditionalFormat('Y', $row, $this->isSettingMap[2]);
    $this->setColumnConditionalFormat('AE', $row, $this->isSettingMap[2]);
    $this->setColumnConditionalFormat('AH', $row, $this->isSettingMap[2]);
    $this->setColumnConditionalFormat('AP', $row, $this->isSettingMap[2]);
    $this->setColumnConditionalFormat('AW', $row, $this->isSettingMap[2]);
    $this->setColumnConditionalFormat('BE', $row, $this->actionTypeMap[2]);
    $this->setColumnConditionalFormat('BQ', $row, $this->actionTypeMap[1]);
  }

  /**
   * @param $beginColumn
   * @param $conditionalStyles
   * @param $index
   */
  public function setConditionStyle($beginColumn, $conditionalStyles, $index)
  {
    switch ($beginColumn) {
      case 'E':
        $targetArray = ['E', 'F', 'G', 'H'];
        $this->setMultiColumnConditionStyle($targetArray, $index, $conditionalStyles);
        break;
      case 'I':
        $targetArray = ['I', 'J', 'K'];
        $this->setMultiColumnConditionStyle($targetArray, $index, $conditionalStyles);
        break;
      case 'L':
        $targetArray = ['L', 'M', 'N', 'O', 'P', 'Q', 'R'];
        $this->setMultiColumnConditionStyle($targetArray, $index, $conditionalStyles);
        break;
      case 'S':
        $targetArray = ['S', 'T'];
        $this->setMultiColumnConditionStyle($targetArray, $index, $conditionalStyles);
        break;
      case 'U':
        $targetArray = ['U', 'V', 'W', 'X'];
        $this->setMultiColumnConditionStyle($targetArray, $index, $conditionalStyles);
        break;
      case 'Y':
        $targetArray = ['Y', 'Z', 'AA', 'AB', 'AC', 'AD'];
        $this->setMultiColumnConditionStyle($targetArray, $index, $conditionalStyles);
        break;
      case 'AE':
        $targetArray = ['AE', 'AF', 'AG'];
        $this->setMultiColumnConditionStyle($targetArray, $index, $conditionalStyles);
        break;
      case 'AH':
        $targetArray = ['AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO'];
        $this->setMultiColumnConditionStyle($targetArray, $index, $conditionalStyles);
        break;
      case 'AP':
        $targetArray = ['AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV'];
        $this->setMultiColumnConditionStyle($targetArray, $index, $conditionalStyles);
        break;
      case 'AW':
        $targetArray = ['AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC'];
        $this->setMultiColumnConditionStyle($targetArray, $index, $conditionalStyles);
        break;
      case 'BE':
        $targetArray = ['BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP'];
        $this->setMultiColumnConditionStyle($targetArray, $index, $conditionalStyles);
        break;
      case 'BQ':
        $targetArray = ['BQ', 'BR'];
        $this->setMultiColumnConditionStyle($targetArray, $index, $conditionalStyles);
        break;
      default:
        break;
    }
  }

  /**
   * @param $row
   */
  public function setRowDataValidation($row)
  {

    $this->setCellDataValidation('B', $row, $this->activeFlgMap[1], $this->activeFlgMap[1] . ', ' . $this->activeFlgMap[0]);
    $this->setCellDataValidation('D', $row, $this->conditionTypeMap[1], $this->conditionTypeMap[1] . ', ' . $this->conditionTypeMap[2]);
    // 滞在時間
    $this->setCellDataValidation('E', $row, $this->isSettingMap[2], $this->isSettingMap[2] . ', ' . $this->isSettingMap[1]);
    $this->setCellDataValidation('F', $row, "", $this->stayTimeCheckTypeMap[1] . ', ' . $this->stayTimeCheckTypeMap[2]);
    $this->setCellDataValidation('G', $row, "", $this->stayTimeTypeMap[1] . ', ' . $this->stayTimeTypeMap[2] . ', ' . $this->stayTimeTypeMap[3]);
    // 訪問回数
    $this->setCellDataValidation('I', $row, $this->isSettingMap[2], $this->isSettingMap[2] . ', ' . $this->isSettingMap[1]);
    $this->setCellDataValidation('K', $row, "", $this->visitCntCondMap[1] . ', ' . $this->visitCntCondMap[2] . ', ' . $this->visitCntCondMap[3]);
    // ページ
    $this->setCellDataValidation('L', $row, $this->isSettingMap[2], $this->isSettingMap[2] . ', ' . $this->isSettingMap[1]);
    $this->setCellDataValidation('M', $row, "", $this->targetNameMap[1] . ', ' . $this->targetNameMap[2]);
    $this->setCellDataValidation('O', $row, "", $this->kWDContainTypeMap[1] . ', ' . $this->kWDContainTypeMap[2]);
    $this->setCellDataValidation('Q', $row, "", $this->kWDContainTypeMap[1] . ', ' . $this->kWDContainTypeMap[2]);
    $this->setCellDataValidation('R', $row, "", $this->stayPageCondTypeMap[1] . ', ' . $this->stayPageCondTypeMap[2]);
    // 営業時間
    $this->setCellDataValidation('S', $row, $this->isSettingMap[2], $this->isSettingMap[2] . ', ' . $this->isSettingMap[1]);
    $this->setCellDataValidation('T', $row, "", $this->businessHourMap[1] . ', ' . $this->businessHourMap[2]);
    // 曜日・時間
    $this->setCellDataValidation('U', $row, $this->isSettingMap[2], $this->isSettingMap[2] . ', ' . $this->isSettingMap[1]);
    // 参照元URL（リファラー）
    $this->setCellDataValidation('Y', $row, $this->isSettingMap[2], $this->isSettingMap[2] . ', ' . $this->isSettingMap[1]);
    $this->setCellDataValidation('AA', $row, "", $this->kWDContainTypeMap[1] . ', ' . $this->kWDContainTypeMap[2]);
    $this->setCellDataValidation('AC', $row, "", $this->kWDContainTypeMap[1] . ', ' . $this->kWDContainTypeMap[2]);
    $this->setCellDataValidation('AD', $row, "", $this->stayPageCondTypeMap[1] . ', ' . $this->stayPageCondTypeMap[2]);
    // 検索キーワード
    $this->setCellDataValidation('AE', $row, $this->isSettingMap[2], $this->isSettingMap[2] . ', ' . $this->isSettingMap[1]);
    $this->setCellDataValidation('AG', $row, "", $this->stayPageCondTypeMap[1] . ', ' . $this->stayPageCondTypeMap[2] . ', ' . $this->stayPageCondTypeMap[3]);
    // 発言内容
    $this->setCellDataValidation('AH', $row, $this->isSettingMap[2], $this->isSettingMap[2] . ', ' . $this->isSettingMap[1]);
    $this->setCellDataValidation('AJ', $row, "", $this->kWDContainTypeMap[1] . ', ' . $this->kWDContainTypeMap[2]);
    $this->setCellDataValidation('AM', $row, "", $this->stayPageCondTypeMap[1] . ', ' . $this->stayPageCondTypeMap[2]);
    $this->setCellDataValidation('AL', $row, "", $this->kWDContainTypeMap[1] . ', ' . $this->kWDContainTypeMap[2]);
    $this->setCellDataValidation('AO', $row, "", $this->speechTriggerCondMap[1] . ', ' . $this->speechTriggerCondMap[2]);
    // 最初に訪れたページ
    $this->setCellDataValidation('AP', $row, $this->isSettingMap[2], $this->isSettingMap[2] . ', ' . $this->isSettingMap[1]);
    $this->setCellDataValidation('AQ', $row, "", $this->targetNameMap[1] . ', ' . $this->targetNameMap[2]);
    $this->setCellDataValidation('AS', $row, "", $this->kWDContainTypeMap[1] . ', ' . $this->kWDContainTypeMap[2]);
    $this->setCellDataValidation('AU', $row, "", $this->kWDContainTypeMap[1] . ', ' . $this->kWDContainTypeMap[2]);
    $this->setCellDataValidation('AV', $row, "", $this->stayPageCondTypeMap[1] . ', ' . $this->stayPageCondTypeMap[2]);
    // 前のページ
    $this->setCellDataValidation('AW', $row, $this->isSettingMap[2], $this->isSettingMap[2] . ', ' . $this->isSettingMap[1]);
    $this->setCellDataValidation('AX', $row, "", $this->targetNameMap[1] . ', ' . $this->targetNameMap[2]);
    $this->setCellDataValidation('AZ', $row, "", $this->kWDContainTypeMap[1] . ', ' . $this->kWDContainTypeMap[2]);
    $this->setCellDataValidation('BB', $row, "", $this->kWDContainTypeMap[1] . ', ' . $this->kWDContainTypeMap[2]);
    $this->setCellDataValidation('BC', $row, "", $this->stayPageCondTypeMap[1] . ', ' . $this->stayPageCondTypeMap[2]);

    // 実行設定
    $this->setCellDataValidation('BD', $row, $this->actionTypeMap[1], $this->actionTypeMap[1] . ', ' . $this->actionTypeMap[2]);
    $this->setCellDataValidation('BE', $row, $this->widgetOpenMap[1], $this->widgetOpenMap[1] . ', ' . $this->widgetOpenMap[2]);
    $this->setCellDataValidation('BG', $row, $this->chatTextAreaMap[1], $this->chatTextAreaMap[1] . ', ' . $this->chatTextAreaMap[2]);
    $this->setCellDataValidation('BH', $row, $this->isSettingMap[2], $this->isSettingMap[2] . ', ' . $this->isSettingMap[1]);
    $this->setCellDataValidation('BI', $row, $this->isSettingMap[2], $this->isSettingMap[2] . ', ' . $this->isSettingMap[1]);

    $this->setCellDataValidation('BR', $row, "", $this->widgetOpenMap[1] . ', ' . $this->widgetOpenMap[2]);
  }

  /**
   * @param $column
   * @param $row
   * @param $default
   * @param $options
   */
  public function setCellDataValidation($column, $row, $default, $options)
  {
    $this->currentSheet->setCellValue($column . $row, $default);
    $objValidation = $this->currentSheet->getCell($column . $row)->getDataValidation();
    $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
    $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
    $objValidation->setAllowBlank(false);
    $objValidation->setShowInputMessage(true);
    $objValidation->setShowErrorMessage(true);
    $objValidation->setShowDropDown(true);
    $objValidation->setFormula1('"' . $options . '"');
  }

  /**
   * @param $baseRow
   * @param $targetRow
   */
  public function copyRowStyle($baseRow, $targetRow)
  {
    foreach (range('A', 'R') as $column) {
      $this->currentSheet->duplicateStyle($this->currentSheet->getStyle('B' . $column . $baseRow), 'B' . $column . $targetRow);
    }

    foreach (range('A', 'Z') as $column) {
      $this->currentSheet->duplicateStyle($this->currentSheet->getStyle($column . $baseRow), $column . $targetRow);
      $this->currentSheet->duplicateStyle($this->currentSheet->getStyle('A' . $column . $baseRow), 'A' . $column . $targetRow);
    }
  }

  /**
   * 滞在時間
   * @param $json
   * @param $row
   */
  private function writeStayTimeData($json, $row)
  {
    if (isset($json['conditions'][1])) {
      $this->currentSheet->setCellValue('E' . $row, $this->isSettingMap[1]);
      $this->currentSheet->setCellValue('F' . $row, $this->stayTimeCheckTypeMap[$json['conditions'][1][0]['stayTimeCheckType']]);
      $this->currentSheet->setCellValue('G' . $row, $this->stayTimeTypeMap[$json['conditions'][1][0]['stayTimeType']]);
      $this->currentSheet->setCellValue('H' . $row, $json['conditions'][1][0]['stayTimeRange']);
    }
  }

  /**
   * 訪問回数
   * @param $json
   * @param $row
   */
  private function writeVisitCountData($json, $row)
  {
    if (isset($json['conditions'][2])) {
      $this->currentSheet->setCellValue('I' . $row, $this->isSettingMap[1]);
      $this->currentSheet->setCellValue('J' . $row, $json['conditions'][2][0]['visitCnt']);
      $this->currentSheet->setCellValue('K' . $row, $this->visitCntCondMap[$json['conditions'][2][0]['visitCntCond']]);
    }
  }

  /**
   * URL
   * @param $json
   * @param $row
   */
  private function writeURLData($json, $row)
  {
    if (isset($json['conditions'][3])) {
      $this->currentSheet->setCellValue('L' . $row, $this->isSettingMap[1]);
      $this->currentSheet->setCellValue('M' . $row, $this->targetNameMap[$json['conditions'][3][0]['targetName']]);
      $this->currentSheet->setCellValue('N' . $row, $json['conditions'][3][0]['keyword_contains']);
      $this->currentSheet->setCellValue('O' . $row, $this->kWDContainTypeMap[$json['conditions'][3][0]['keyword_contains_type']]);
      $this->currentSheet->setCellValue('P' . $row, $json['conditions'][3][0]['keyword_exclusions']);
      $this->currentSheet->setCellValue('Q' . $row, $this->kWDContainTypeMap[$json['conditions'][3][0]['keyword_exclusions_type']]);
      $this->currentSheet->setCellValue('R' . $row, $this->stayPageCondTypeMap[$json['conditions'][3][0]['stayPageCond']]);
    }
  }

  /**
   * 曜日・時間
   * @param $json
   * @param $row
   */
  private function writeWeekdayData($json, $row)
  {
    if (isset($json['conditions'][4])) {
      $this->currentSheet->setCellValue('U' . $row, $this->isSettingMap[1]);
      $weekDays = '';
      foreach ($json['conditions'][4][0]['day'] as $day => $val) {
        if ($val) {
          $weekDays = $weekDays . $this->exportWeekdayMap[$day] . ', ';
        }
      }

      $this->currentSheet->setCellValue('V' . $row, $weekDays);
      if ($json['conditions'][4][0]['timeSetting'] == 1) {
        $this->currentSheet->setCellValue('W' . $row, $json['conditions'][4][0]['startTime']);
        $this->currentSheet->setCellValue('X' . $row, $json['conditions'][4][0]['endTime']);
      }
    }
  }

  /**
   * @param $json
   * @param $row
   */
  private function writeRefferURLData($json, $row)
  {
    if (isset($json['conditions'][5])) {
      $this->currentSheet->setCellValue('Y' . $row, $this->isSettingMap[1]);
      $this->currentSheet->setCellValue('Z' . $row, $json['conditions'][5][0]['keyword_contains']);
      $this->currentSheet->setCellValue('AA' . $row, $this->kWDContainTypeMap[$json['conditions'][5][0]['keyword_contains_type']]);
      $this->currentSheet->setCellValue('AB' . $row, $json['conditions'][5][0]['keyword_exclusions']);
      $this->currentSheet->setCellValue('AC' . $row, $this->kWDContainTypeMap[$json['conditions'][5][0]['keyword_exclusions_type']]);
      $this->currentSheet->setCellValue('AD' . $row, $this->stayPageCondTypeMap[$json['conditions'][5][0]['referrerCond']]);
    }
  }

  /**
   * @param $json
   * @param $row
   */
  private function writeSearchKeywordData($json, $row)
  {
    if (isset($json['conditions'][6])) {
      $this->currentSheet->setCellValue('AE' . $row, $this->isSettingMap[1]);
      $this->currentSheet->setCellValue('AF' . $row, $json['conditions'][6][0]['keyword']);
      $this->currentSheet->setCellValue('AG' . $row, $this->stayPageCondTypeMap[$json['conditions'][6][0]['searchCond']]);
    }
  }

  /**
   * @param $json
   * @param $row
   */
  private function writeSpeechContentData($json, $row)
  {
    if (isset($json['conditions'][7])) {
      $this->currentSheet->setCellValue('AH' . $row, $this->isSettingMap[1]);
      $this->currentSheet->setCellValue('AI' . $row, $json['conditions'][7][0]['keyword_contains']);
      $this->currentSheet->setCellValue('AJ' . $row, $this->kWDContainTypeMap[$json['conditions'][7][0]['keyword_contains_type']]);
      $this->currentSheet->setCellValue('AK' . $row, $json['conditions'][7][0]['keyword_exclusions']);
      $this->currentSheet->setCellValue('AL' . $row, $this->kWDContainTypeMap[$json['conditions'][7][0]['keyword_exclusions_type']]);
      $this->currentSheet->setCellValue('AM' . $row, $this->stayPageCondTypeMap[$json['conditions'][7][0]['speechContentCond']]);
      $this->currentSheet->setCellValue('AN' . $row, $json['conditions'][7][0]['triggerTimeSec']);
      $this->currentSheet->setCellValue('AO' . $row, $this->speechTriggerCondMap[$json['conditions'][7][0]['speechTriggerCond']]);
    }
  }

  /**
   * @param $json
   * @param $row
   */
  private function writeFirstVisitPageData($json, $row)
  {
    if (isset($json['conditions'][8])) {
      $this->currentSheet->setCellValue('AP' . $row, $this->isSettingMap[1]);
      $this->currentSheet->setCellValue('AQ' . $row, $this->targetNameMap[$json['conditions'][8][0]['targetName']]);
      $this->currentSheet->setCellValue('AR' . $row, $json['conditions'][8][0]['keyword_contains']);
      $this->currentSheet->setCellValue('AS' . $row, $this->kWDContainTypeMap[$json['conditions'][8][0]['keyword_contains_type']]);
      $this->currentSheet->setCellValue('AT' . $row, $json['conditions'][8][0]['keyword_exclusions']);
      $this->currentSheet->setCellValue('AU' . $row, $this->kWDContainTypeMap[$json['conditions'][8][0]['keyword_exclusions_type']]);
      $this->currentSheet->setCellValue('AV' . $row, $this->stayPageCondTypeMap[$json['conditions'][8][0]['stayPageCond']]);
    }
  }

  /**
   * @param $json
   * @param $row
   */
  private function writePreviousPageData($json, $row)
  {
    if (isset($json['conditions'][9])) {
      $this->currentSheet->setCellValue('AW' . $row, $this->isSettingMap[1]);
      $this->currentSheet->setCellValue('AX' . $row, $this->targetNameMap[$json['conditions'][9][0]['targetName']]);
      $this->currentSheet->setCellValue('AY' . $row, $json['conditions'][9][0]['keyword_contains']);
      $this->currentSheet->setCellValue('AZ' . $row, $this->kWDContainTypeMap[$json['conditions'][9][0]['keyword_contains_type']]);
      $this->currentSheet->setCellValue('BA' . $row, $json['conditions'][9][0]['keyword_exclusions']);
      $this->currentSheet->setCellValue('BB' . $row, $this->kWDContainTypeMap[$json['conditions'][9][0]['keyword_exclusions_type']]);
      $this->currentSheet->setCellValue('BC' . $row, $this->stayPageCondTypeMap[$json['conditions'][9][0]['stayPageCond']]);
    }
  }

  /**
   * @param $json
   * @param $row
   */
  private function writeBusinessHourData($json, $row)
  {
    if (isset($json['conditions'][10])) {
      $this->currentSheet->setCellValue('S' . $row, $this->isSettingMap[1]);
      $this->currentSheet->setCellValue('T' . $row, $this->businessHourMap[$json['conditions'][10][0]['operatingHoursTime']]);
    }
  }

  /**
   * @param $json
   * @param $row
   * @param $value
   */
  private function writeScenarioData($json, $row, $value)
  {
    $this->currentSheet->setCellValue('BD' . $row, $this->actionTypeMap[2]);
    $this->currentSheet->setCellValue('BQ' . $row, $value['TChatbotScenario']['name']);
    $this->currentSheet->setCellValue('BR' . $row, $this->widgetOpenMap[$json['widgetOpen']]);
  }

  /**
   * @param $json
   * @param $row
   * @param $value
   */
  private function writeSendMessageData($json, $row, $value)
  {
    $this->currentSheet->setCellValue('BD' . $row, $this->actionTypeMap[1]);
    $this->currentSheet->setCellValue('BE' . $row, $this->widgetOpenMap[$json['widgetOpen']]);
    $this->currentSheet->setCellValue('BF' . $row, $json['message']);
    $this->currentSheet->setCellValue('BG' . $row, $this->chatTextAreaMap[$json['chatTextarea']]);
    $this->currentSheet->setCellValue('BH' . $row, $this->triggerCVMap[$json['cv']]);
    $this->currentSheet->setCellValue('BI' . $row, $this->sendMailFlgMap[$value['TAutoMessage']['send_mail_flg']]);
    // get mail information
    $mailTransmission = ClassRegistry::init('MMailTransmissionSetting');
    $transmissionData = $mailTransmission->findById($value['TAutoMessage']['m_mail_transmission_settings_id']);
    if (!empty($transmissionData)) {
      $splitedMailAddresses = explode(',', $transmissionData['MMailTransmissionSetting']['to_address']);
      $this->currentSheet->setCellValue('BJ' . $row, !empty($splitedMailAddresses[0]) ? $splitedMailAddresses[0] : "");
      $this->currentSheet->setCellValue('BK' . $row, !empty($splitedMailAddresses[1]) ? $splitedMailAddresses[1] : "");
      $this->currentSheet->setCellValue('BL' . $row, !empty($splitedMailAddresses[2]) ? $splitedMailAddresses[2] : "");
      $this->currentSheet->setCellValue('BM' . $row, !empty($splitedMailAddresses[3]) ? $splitedMailAddresses[3] : "");
      $this->currentSheet->setCellValue('BN' . $row, !empty($splitedMailAddresses[4]) ? $splitedMailAddresses[4] : "");
      $this->currentSheet->setCellValue('BO' . $row, !empty($transmissionData['MMailTransmissionSetting']['subject']) ? $transmissionData['MMailTransmissionSetting']['subject'] : "");
      $this->currentSheet->setCellValue('BP' . $row, !empty($transmissionData['MMailTransmissionSetting']['from_name']) ? $transmissionData['MMailTransmissionSetting']['from_name'] : "");
    }
  }

  /**
   * @param $targetArray
   * @param $row
   * @param $conditionalStyles
   */
  private function setMultiColumnConditionStyle($targetArray, $row, $conditionalStyles)
  {
    foreach ($targetArray as $column) {
      $this->currentSheet->getStyle($column . $row)->setConditionalStyles($conditionalStyles);
    }
  }
}