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
  const FIRST_ROW = 5;
  const SECOND_ROW = 6;

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
      T_SETTING_ON  => 'する',
      T_SETTING_OFF => 'しない'
    ];

    $this->activeFlgMap = [
      T_ACTIVE_ON  => '有効',
      T_ACTIVE_OFF => '無効'
    ];

    $this->widgetOpenMap = [
      T_WIDGET_OPEN_ON  => '自動で最大化する',
      T_WIDGET_OPEN_OFF => '自動で最大化しない'
    ];

    $this->conditionTypeMap = [
      T_CONDITION_ALL_MATCH => 'すべて一致',
      T_CONDITION_ONE_MATCH => 'いずれかが一致'
    ];

    $this->chatTextAreaMap = [
      T_TEXTAREA_OPEN => Configure::read('outMessageTextarea')[1],
      T_TEXTAREA_CLOSE => Configure::read('outMessageTextarea')[2]
    ];

    $this->triggerCVMap = [
      T_AUTO_CV_ON => Configure::read('outMessageCvType')[1],
      T_AUTO_CV_OFF => Configure::read('outMessageCvType')[2]
    ];

    $this->sendMailFlgMap = [
      T_SEND_MAIL_ON => 'する',
      T_SEND_MAIL_OFF => 'しない'
    ];

    $this->stayTimeCheckTypeMap = [
      T_STAY_TIME_SITE => 'サイト',
      T_STAY_TIME_PAGE => 'ページ'
    ];

    $this->stayTimeTypeMap = [
      T_STAY_TIME_SECOND => '秒',
      T_STAY_TIME_MIN    => '分',
      T_STAY_TIME_HOUR   => '時'
    ];

    $this->visitCntCondMap = [
      T_VISIT_COUNT_RANGE     => '以上',
      T_VISIT_COUNT_EQUAL     => 'に一致する場合',
      T_VISIT_COUNT_MORE_THAN => '以上の場合',
      T_VISIT_COUNT_LESS_THAN => '未満の場合'
    ];

    $this->targetNameMap = [
      T_TARGET_PAGE => 'ページ',
      T_TARGET_URL  => 'URL',
    ];

    $this->kWDContainTypeMap = [
      T_CONDITION_ALL_MATCH => 'をすべて含む',
      T_CONDITION_ONE_MATCH => 'のいずれかを含む',
    ];

    $this->kWDExclusionTypeMap = [
      T_CONDITION_ALL_MATCH => 'をすべて含む',
      T_CONDITION_ONE_MATCH => 'のいずれかを含む',
    ];

    $this->stayPageCondTypeMap = [
      T_STAY_PAGE_ALL_MATCH  => '完全一致',
      T_STAY_PAGE_PART_MATCH => '部分一致',
      T_STAY_PAGE_NOT_MATCH  => '不一致'
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
      T_SPEECH_ONE_TIME => '１回のみ有効',
      T_SPEECH_ANY_TIME => '何度でも有効',
    ];

    $this->businessHourMap = [
      T_IN_BUSINESS_HOUR  => '営業時間内',
      T_OUT_BUSINESS_HOUR => '営業時間外'
    ];

    $this->actionTypeMap = [
      T_ACTION_CALL_CHAT_TREE => 'チャットツリーを呼び出す',
      T_ACTION_CALL_TRIGGER   => '別のトリガーを呼び出す',
      T_ACTION_CALL_SCENARIO  => 'シナリオを呼び出す',
      T_ACTION_SEND_MESSSAGE  => 'チャットメッセージを送る'
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
    $dataMap = array_combine(range(self::FIRST_ROW, count($data) + 4), $data);
    foreach ($dataMap as $row => $value) {
      $json = json_decode($value['TAutoMessage']['activity'], true);
      // name
      $this->currentSheet->setCellValue('C' . $row, $value['TAutoMessage']['name']);
      // active_flg
      $this->currentSheet->setCellValue('B' . $row, $this->activeFlgMap[$value['TAutoMessage']['active_flg']]);
      // conditionType
      $this->currentSheet->setCellValue('D' . $row, $this->conditionTypeMap[$json['conditionType']]);
      // action type
      if ($value['TAutoMessage']['action_type'] == T_ACTION_CALL_SCENARIO) {
        // select scenarios
        $this->writeScenarioData($json, $row, $value);
      } else if($value['TAutoMessage']['action_type'] == T_ACTION_CALL_TRIGGER) {
        // call automessage
        $this->writeCallAutomessageData($json, $row, $value);
      } else if($value['TAutoMessage']['action_type'] == T_ACTION_CALL_CHAT_TREE) {
        // call diagram
        $this->writeCallDiagramData($json, $row, $value);
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
      //訪問者の端末
      $this->writeVisitorDevicetData($json, $row);
    }
  }

  public function generateTemplate($endRow)
  {
    // create first row
    $this->setRowDataValidation(self::FIRST_ROW);
    $this->setRowConditionalFormat(self::FIRST_ROW);
    // copy style first row to other row
    foreach (range(self::SECOND_ROW, $endRow) as $row) {
      $this->setRowConditionalFormat($row);
      $this->setRowDataValidation($row);
    }
    $this->copyRowStyle(self::FIRST_ROW, $endRow);
  }

  /**
   * @param $beginColumn
   * @param $row
   * @param $condition
   */
  public function setColumnConditionalFormat($beginColumn, $row, $condition)
  {
    $operator = '=';
    if ($beginColumn == 'BG' || $beginColumn == 'BS' || $beginColumn == 'BU' || $beginColumn == 'BV') {
      $operator = '<>';
    }
    $conditionCol   = ($beginColumn == 'BG' || $beginColumn == 'BS' || $beginColumn == 'BU' || $beginColumn == 'BV') ? 'BF' : $beginColumn;
    $condition      = '$' . $conditionCol . $row . ' '.$operator.' "' . $condition . '"';
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
    $this->setColumnConditionalFormat('E', $row, $this->isSettingMap[T_SETTING_OFF]);
    $this->setColumnConditionalFormat('I', $row, $this->isSettingMap[T_SETTING_OFF]);
    $this->setColumnConditionalFormat('L', $row, $this->isSettingMap[T_SETTING_OFF]);
    $this->setColumnConditionalFormat('S', $row, $this->isSettingMap[T_SETTING_OFF]);
    $this->setColumnConditionalFormat('U', $row, $this->isSettingMap[T_SETTING_OFF]);
    $this->setColumnConditionalFormat('Y', $row, $this->isSettingMap[T_SETTING_OFF]);
    $this->setColumnConditionalFormat('AE', $row, $this->isSettingMap[T_SETTING_OFF]);
    $this->setColumnConditionalFormat('AH', $row, $this->isSettingMap[T_SETTING_OFF]);
    $this->setColumnConditionalFormat('AP', $row, $this->isSettingMap[T_SETTING_OFF]);
    $this->setColumnConditionalFormat('AW', $row, $this->isSettingMap[T_SETTING_OFF]);
    $this->setColumnConditionalFormat('BD', $row, $this->isSettingMap[T_SETTING_OFF]);
    $this->setColumnConditionalFormat('BG', $row, $this->actionTypeMap[T_ACTION_SEND_MESSSAGE]);
    $this->setColumnConditionalFormat('BS', $row, $this->actionTypeMap[T_ACTION_CALL_SCENARIO]);
    $this->setColumnConditionalFormat('BU', $row, $this->actionTypeMap[T_ACTION_CALL_TRIGGER]);
    $this->setColumnConditionalFormat('BV', $row, $this->actionTypeMap[T_ACTION_CALL_CHAT_TREE]);
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
        break;
      case 'I':
        $targetArray = ['I', 'J', 'K'];
        break;
      case 'L':
        $targetArray = ['L', 'M', 'N', 'O', 'P', 'Q', 'R'];
        break;
      case 'S':
        $targetArray = ['S', 'T'];
        break;
      case 'U':
        $targetArray = ['U', 'V', 'W', 'X'];
        break;
      case 'Y':
        $targetArray = ['Y', 'Z', 'AA', 'AB', 'AC', 'AD'];
        break;
      case 'AE':
        $targetArray = ['AE', 'AF', 'AG'];
        break;
      case 'AH':
        $targetArray = ['AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO'];
        break;
      case 'AP':
        $targetArray = ['AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV'];
        break;
      case 'AW':
        $targetArray = ['AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC'];
        break;
      case 'BD':
        $targetArray = ['BD', 'BE'];
        break;
      case 'BG':
        $targetArray = ['BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR'];
        break;
      case 'BS':
        $targetArray = ['BS', 'BT'];
        break;
      case 'BU':
        $targetArray = ['BU'];
        break;
      case 'BV':
        $targetArray = ['BV','BW'];
        break;
      default:
        $targetArray = [];
        break;
    }
    $this->setMultiColumnConditionStyle($targetArray, $index, $conditionalStyles);
  }

  /**
   * @param $row
   */
  public function setRowDataValidation($row)
  {

    $this->setCellDataValidation('B', $row, $this->activeFlgMap[T_ACTIVE_OFF], $this->activeFlgMap[T_ACTIVE_OFF] . ', ' . $this->activeFlgMap[T_ACTIVE_ON]);
    $this->setCellDataValidation('D', $row, $this->conditionTypeMap[T_CONDITION_ALL_MATCH], $this->conditionTypeMap[T_CONDITION_ALL_MATCH] . ', ' . $this->conditionTypeMap[T_CONDITION_ONE_MATCH]);
    // 滞在時間
    $this->setCellDataValidation('E', $row, $this->isSettingMap[T_SETTING_OFF], $this->isSettingMap[T_SETTING_OFF] . ', ' . $this->isSettingMap[T_SETTING_ON]);
    $this->setCellDataValidation('F', $row, "", $this->stayTimeCheckTypeMap[T_STAY_TIME_PAGE] . ', ' . $this->stayTimeCheckTypeMap[T_STAY_TIME_SITE]);
    $this->setCellDataValidation('G', $row, "", $this->stayTimeTypeMap[T_STAY_TIME_SECOND] . ', ' . $this->stayTimeTypeMap[T_STAY_TIME_MIN] . ', ' . $this->stayTimeTypeMap[T_STAY_TIME_HOUR]);
    // 訪問回数
    $this->setCellDataValidation('I', $row, $this->isSettingMap[T_SETTING_OFF], $this->isSettingMap[T_SETTING_OFF] . ', ' . $this->isSettingMap[T_SETTING_ON]);
    $this->currentSheet->getComment('J'. $row)->setWidth("400px");
    $this->currentSheet->getComment('J'. $row)->setHeight("100px");
    $this->currentSheet->getComment('J'. $row)->getText()->createTextRun('条件が以上の場合は「OO ~ OO」形式で入力してください。例：1 ~ 10');
    $this->setCellDataValidation('K', $row, "", $this->visitCntCondMap[T_VISIT_COUNT_RANGE] . ', ' . $this->visitCntCondMap[T_VISIT_COUNT_EQUAL] . ', ' . $this->visitCntCondMap[T_VISIT_COUNT_MORE_THAN] . ', ' . $this->visitCntCondMap[T_VISIT_COUNT_LESS_THAN]);
    // ページ
    $this->setCellDataValidation('L', $row, $this->isSettingMap[T_SETTING_OFF], $this->isSettingMap[T_SETTING_OFF] . ', ' . $this->isSettingMap[T_SETTING_ON]);
    $this->setCellDataValidation('M', $row, "", $this->targetNameMap[T_TARGET_PAGE] . ', ' . $this->targetNameMap[T_TARGET_URL]);
    $this->setCellDataValidation('O', $row, "", $this->kWDContainTypeMap[T_CONDITION_ALL_MATCH] . ', ' . $this->kWDContainTypeMap[T_CONDITION_ONE_MATCH]);
    $this->setCellDataValidation('Q', $row, "", $this->kWDContainTypeMap[T_CONDITION_ALL_MATCH] . ', ' . $this->kWDContainTypeMap[T_CONDITION_ONE_MATCH]);
    $this->setCellDataValidation('R', $row, "", $this->stayPageCondTypeMap[T_STAY_PAGE_ALL_MATCH] . ', ' . $this->stayPageCondTypeMap[T_STAY_PAGE_PART_MATCH]);
    // 営業時間
    $this->setCellDataValidation('S', $row, $this->isSettingMap[T_SETTING_OFF], $this->isSettingMap[T_SETTING_OFF] . ', ' . $this->isSettingMap[T_SETTING_ON]);
    $this->setCellDataValidation('T', $row, "", $this->businessHourMap[T_IN_BUSINESS_HOUR] . ', ' . $this->businessHourMap[T_OUT_BUSINESS_HOUR]);
    // 曜日・時間
    $this->setCellDataValidation('U', $row, $this->isSettingMap[T_SETTING_OFF], $this->isSettingMap[T_SETTING_OFF] . ', ' . $this->isSettingMap[T_SETTING_ON]);
    // 参照元URL（リファラー）
    $this->setCellDataValidation('Y', $row, $this->isSettingMap[T_SETTING_OFF], $this->isSettingMap[T_SETTING_OFF] . ', ' . $this->isSettingMap[T_SETTING_ON]);
    $this->setCellDataValidation('AA', $row, "", $this->kWDContainTypeMap[T_CONDITION_ALL_MATCH] . ', ' . $this->kWDContainTypeMap[T_CONDITION_ONE_MATCH]);
    $this->setCellDataValidation('AC', $row, "", $this->kWDContainTypeMap[T_CONDITION_ALL_MATCH] . ', ' . $this->kWDContainTypeMap[T_CONDITION_ONE_MATCH]);
    $this->setCellDataValidation('AD', $row, "", $this->stayPageCondTypeMap[T_STAY_PAGE_ALL_MATCH] . ', ' . $this->stayPageCondTypeMap[T_STAY_PAGE_PART_MATCH]);
    // 検索キーワード
    $this->setCellDataValidation('AE', $row, $this->isSettingMap[T_SETTING_OFF], $this->isSettingMap[T_SETTING_OFF] . ', ' . $this->isSettingMap[T_SETTING_ON]);
    $this->setCellDataValidation('AG', $row, "", $this->stayPageCondTypeMap[T_STAY_PAGE_ALL_MATCH] . ', ' . $this->stayPageCondTypeMap[T_STAY_PAGE_PART_MATCH] . ', ' . $this->stayPageCondTypeMap[T_STAY_PAGE_NOT_MATCH]);
    // 発言内容
    $this->setCellDataValidation('AH', $row, $this->isSettingMap[T_SETTING_OFF], $this->isSettingMap[T_SETTING_OFF] . ', ' . $this->isSettingMap[T_SETTING_ON]);
    $this->setCellDataValidation('AJ', $row, "", $this->kWDContainTypeMap[T_CONDITION_ALL_MATCH] . ', ' . $this->kWDContainTypeMap[T_CONDITION_ONE_MATCH]);
    $this->setCellDataValidation('AM', $row, "", $this->stayPageCondTypeMap[T_STAY_PAGE_ALL_MATCH] . ', ' . $this->stayPageCondTypeMap[T_STAY_PAGE_PART_MATCH]);
    $this->setCellDataValidation('AL', $row, "", $this->kWDContainTypeMap[T_CONDITION_ALL_MATCH] . ', ' . $this->kWDContainTypeMap[T_CONDITION_ONE_MATCH]);
    $this->setCellDataValidation('AO', $row, "", $this->speechTriggerCondMap[T_SPEECH_ONE_TIME] . ', ' . $this->speechTriggerCondMap[T_SPEECH_ANY_TIME]);
    // 最初に訪れたページ
    $this->setCellDataValidation('AP', $row, $this->isSettingMap[T_SETTING_OFF], $this->isSettingMap[T_SETTING_OFF] . ', ' . $this->isSettingMap[T_SETTING_ON]);
    $this->setCellDataValidation('AQ', $row, "", $this->targetNameMap[T_TARGET_PAGE] . ', ' . $this->targetNameMap[T_TARGET_URL]);
    $this->setCellDataValidation('AS', $row, "", $this->kWDContainTypeMap[T_CONDITION_ALL_MATCH] . ', ' . $this->kWDContainTypeMap[T_CONDITION_ONE_MATCH]);
    $this->setCellDataValidation('AU', $row, "", $this->kWDContainTypeMap[T_CONDITION_ALL_MATCH] . ', ' . $this->kWDContainTypeMap[T_CONDITION_ONE_MATCH]);
    $this->setCellDataValidation('AV', $row, "", $this->stayPageCondTypeMap[T_STAY_PAGE_ALL_MATCH] . ', ' . $this->stayPageCondTypeMap[T_STAY_PAGE_PART_MATCH]);
    // 前のページ
    $this->setCellDataValidation('AW', $row, $this->isSettingMap[T_SETTING_OFF], $this->isSettingMap[T_SETTING_OFF] . ', ' . $this->isSettingMap[T_SETTING_ON]);
    $this->setCellDataValidation('AX', $row, "", $this->targetNameMap[T_TARGET_PAGE] . ', ' . $this->targetNameMap[T_TARGET_URL]);
    $this->setCellDataValidation('AZ', $row, "", $this->kWDContainTypeMap[T_CONDITION_ALL_MATCH] . ', ' . $this->kWDContainTypeMap[T_CONDITION_ONE_MATCH]);
    $this->setCellDataValidation('BB', $row, "", $this->kWDContainTypeMap[T_CONDITION_ALL_MATCH] . ', ' . $this->kWDContainTypeMap[T_CONDITION_ONE_MATCH]);
    $this->setCellDataValidation('BC', $row, "", $this->stayPageCondTypeMap[T_STAY_PAGE_ALL_MATCH] . ', ' . $this->stayPageCondTypeMap[T_STAY_PAGE_PART_MATCH]);

    // 訪問者の端末
    $this->setCellDataValidation('BD', $row, $this->isSettingMap[T_SETTING_OFF], $this->isSettingMap[T_SETTING_OFF] . ', ' . $this->isSettingMap[T_SETTING_ON]);
    $this->currentSheet->getComment('BE'. $row)->setWidth("250px");
    $this->currentSheet->getComment('BE'. $row)->setHeight("100px");
    $this->currentSheet->getComment('BE'. $row)->getText()->createTextRun('PC, スマートフォン, タブレットを入力してください。区切りは「,]です。');

    // 実行設定
    $this->setCellDataValidation('BF', $row, $this->actionTypeMap[T_ACTION_SEND_MESSSAGE], $this->actionTypeMap[T_ACTION_SEND_MESSSAGE] . ', ' . $this->actionTypeMap[T_ACTION_CALL_SCENARIO]);
    $this->setCellDataValidation('BG', $row, $this->widgetOpenMap[T_WIDGET_OPEN_ON], $this->widgetOpenMap[T_WIDGET_OPEN_ON] . ', ' . $this->widgetOpenMap[T_WIDGET_OPEN_OFF]);
    $this->setCellDataValidation('BI', $row, $this->chatTextAreaMap[T_TEXTAREA_OPEN], $this->chatTextAreaMap[T_TEXTAREA_OPEN] . ', ' . $this->chatTextAreaMap[T_TEXTAREA_CLOSE]);
    $this->setCellDataValidation('BJ', $row, $this->isSettingMap[T_SETTING_OFF], $this->isSettingMap[T_SETTING_OFF] . ', ' . $this->isSettingMap[T_SETTING_ON]);
    $this->setCellDataValidation('BK', $row, $this->isSettingMap[T_SETTING_OFF], $this->isSettingMap[T_SETTING_OFF] . ', ' . $this->isSettingMap[T_SETTING_ON]);

    $this->setCellDataValidation('BT', $row, "", $this->widgetOpenMap[T_WIDGET_OPEN_ON] . ', ' . $this->widgetOpenMap[T_WIDGET_OPEN_OFF]);

    $this->setCellDataValidation('BW', $row, "", $this->widgetOpenMap[T_WIDGET_OPEN_ON] . ', ' . $this->widgetOpenMap[T_WIDGET_OPEN_OFF]);
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
   * @param $endRow
   */
  public function copyRowStyle($baseRow, $endRow)
  {
    // column BA ~ BW
    foreach (range('A', 'W') as $column) {
      $this->currentSheet->duplicateStyle($this->currentSheet->getStyle('B' . $column . $baseRow), 'B' . $column . self::SECOND_ROW . ':' . 'B' . $column . $endRow);
    }

    // column A ~ AZ
    foreach (range('A', 'Z') as $column) {
      $this->currentSheet->duplicateStyle($this->currentSheet->getStyle($column . $baseRow), $column . self::SECOND_ROW . ':' . $column . $endRow);
      $this->currentSheet->duplicateStyle($this->currentSheet->getStyle('A' . $column . $baseRow), 'A' . $column . self::SECOND_ROW . ':' . 'A' . $column . $endRow);
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
      $this->currentSheet->setCellValue('E' . $row, $this->isSettingMap[T_SETTING_ON]);
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
      $this->currentSheet->setCellValue('I' . $row, $this->isSettingMap[T_SETTING_ON]);
      if ($json['conditions'][2][0]['visitCntCond'] == '4') {
        // 範囲設定
        $this->currentSheet->setCellValue('J' . $row, $json['conditions'][2][0]['visitCnt'] . ' ~ ' . $json['conditions'][2][0]['visitCntMax']);
      } else {
        $this->currentSheet->setCellValue('J' . $row, $json['conditions'][2][0]['visitCnt']);
      }
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
      $this->currentSheet->setCellValue('L' . $row, $this->isSettingMap[T_SETTING_ON]);
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
      $this->currentSheet->setCellValue('U' . $row, $this->isSettingMap[T_SETTING_ON]);
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
      $this->currentSheet->setCellValue('Y' . $row, $this->isSettingMap[T_SETTING_ON]);
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
      $this->currentSheet->setCellValue('AE' . $row, $this->isSettingMap[T_SETTING_ON]);
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
      $this->currentSheet->setCellValue('AH' . $row, $this->isSettingMap[T_SETTING_ON]);
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
      $this->currentSheet->setCellValue('AP' . $row, $this->isSettingMap[T_SETTING_ON]);
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
      $this->currentSheet->setCellValue('AW' . $row, $this->isSettingMap[T_SETTING_ON]);
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
      $this->currentSheet->setCellValue('S' . $row, $this->isSettingMap[T_SETTING_ON]);
      $this->currentSheet->setCellValue('T' . $row, $this->businessHourMap[$json['conditions'][10][0]['operatingHoursTime']]);
    }
  }

  /**
   * 訪問者の端末
   * @param $json
   * @param $row
   */
  private function writeVisitorDevicetData($json, $row)
  {
    if (isset($json['conditions'][11])) {
      $this->currentSheet->setCellValue('BD' . $row, $this->isSettingMap[T_SETTING_ON]);
      $deviceList = '';
      if ($json['conditions'][11][0]['pc']) {
        $deviceList .= 'PC, ';
      }

      if ($json['conditions'][11][0]['smartphone']) {
        $deviceList .= 'スマートフォン, ';
      }

      if ($json['conditions'][11][0]['tablet']) {
        $deviceList .= 'タブレット';
      }

      $this->currentSheet->setCellValue('BE' . $row, trim($deviceList, ', '));
    }
  }

  /**
   * @param $json
   * @param $row
   * @param $value
   */
  private function writeScenarioData($json, $row, $value)
  {
    $this->currentSheet->setCellValue('BF' . $row, $this->actionTypeMap[T_ACTION_CALL_SCENARIO]);
    $this->currentSheet->setCellValue('BI' . $row, $this->chatTextAreaMap[$json['chatTextarea']]);
    $this->currentSheet->setCellValue('BS' . $row, $value['TChatbotScenario']['name']);
    $this->currentSheet->setCellValue('BT' . $row, $this->widgetOpenMap[$json['widgetOpen']]);
  }

  /**
   * @param $json
   * @param $row
   * @param $value
   */
  private function writeCallAutomessageData($json, $row, $value)
  {
    $this->currentSheet->setCellValue('BF' . $row, $this->actionTypeMap[T_ACTION_CALL_TRIGGER]);
    $this->currentSheet->setCellValue('BU' . $row, $value['CalledAutoMessage']['name']);
  }

  /**
   * @param $json
   * @param $row
   * @param $value
   */
  private function writeCallDiagramData($json, $row, $value)
  {
    $this->currentSheet->setCellValue('BF' . $row, $this->actionTypeMap[T_ACTION_CALL_CHAT_TREE]);
    $this->currentSheet->setCellValue('BV' . $row, $value['TChatbotDiagram']['name']);
    $this->currentSheet->setCellValue('BW' . $row, $this->widgetOpenMap[$json['widgetOpen']]);
  }

  /**
   * @param $json
   * @param $row
   * @param $value
   */
  private function writeSendMessageData($json, $row, $value)
  {
    $this->currentSheet->setCellValue('BF' . $row, $this->actionTypeMap[T_ACTION_SEND_MESSSAGE]);
    $this->currentSheet->setCellValue('BG' . $row, $this->widgetOpenMap[$json['widgetOpen']]);
    $this->currentSheet->setCellValue('BH' . $row, $json['message']);
    $this->currentSheet->setCellValue('BI' . $row, $this->chatTextAreaMap[$json['chatTextarea']]);
    $this->currentSheet->setCellValue('BJ' . $row, $this->triggerCVMap[$json['cv']]);
    $this->currentSheet->setCellValue('BK' . $row, $this->sendMailFlgMap[$value['TAutoMessage']['send_mail_flg']]);
    // get mail information
    $mailTransmission = ClassRegistry::init('MMailTransmissionSetting');
    $transmissionData = $mailTransmission->findById($value['TAutoMessage']['m_mail_transmission_settings_id']);
    if (!empty($transmissionData)) {
      $splitedMailAddresses = explode(',', $transmissionData['MMailTransmissionSetting']['to_address']);
      $this->currentSheet->setCellValue('BL' . $row, !empty($splitedMailAddresses[0]) ? $splitedMailAddresses[0] : "");
      $this->currentSheet->setCellValue('BM' . $row, !empty($splitedMailAddresses[1]) ? $splitedMailAddresses[1] : "");
      $this->currentSheet->setCellValue('BN' . $row, !empty($splitedMailAddresses[2]) ? $splitedMailAddresses[2] : "");
      $this->currentSheet->setCellValue('BO' . $row, !empty($splitedMailAddresses[3]) ? $splitedMailAddresses[3] : "");
      $this->currentSheet->setCellValue('BP' . $row, !empty($splitedMailAddresses[4]) ? $splitedMailAddresses[4] : "");
      $this->currentSheet->setCellValue('BQ' . $row, !empty($transmissionData['MMailTransmissionSetting']['subject']) ? $transmissionData['MMailTransmissionSetting']['subject'] : "");
      $this->currentSheet->setCellValue('BR' . $row, !empty($transmissionData['MMailTransmissionSetting']['from_name']) ? $transmissionData['MMailTransmissionSetting']['from_name'] : "");
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
