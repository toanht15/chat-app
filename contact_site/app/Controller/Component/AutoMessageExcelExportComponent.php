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

  const PLUS_ROW = 25;

  private $containsTypeMap;
  private $exclusionsTypeMap;
  private $keywordConditionMap;
  private $triggerConditionMap;
  private $triggerFreeInputMap;
  private $triggerCVMap;
  private $activeFlgMap;

  private $exportActiveFlgMap;
  private $exportConditionTypeMap;
  private $exportWidgetOpenMap;
  private $exportChatTextAreaMap;
  private $exportTriggerCVMap;
  private $exportSendMailFlgMap;
  private $exportStayTimeCheckTypeMap;
  private $exportStayTimeTypeMap;
  private $exportVisitCntCondMap;
  private $exportTargetNameMap;
  private $exportKWDContainTypeMap;
  private $exportKWDExclusionTypeMap;
  private $exportStayPageCondTypeMap;
  private $exportWeekdayMap;
  private $exportSpeechTriggerCondMap;
  private $exportBusinessHourMap;

  public function __construct($filePath)
  {
    parent::__construct($filePath);
    $this->readSettingMapFromConfig();
  }

  public function getImportData() {
    $this->readData();
    $this->setActiveSheet(0);
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
        'すべて含む' => 1,
        'いずれかを含む' => 2
    ];

    $this->keywordConditionMap = [
      '完全一致' => "1",
      '部分一致' => "2"
    ];

    $this->triggerConditionMap = [
      "１回のみ有効" => "1",
      "何度でも有効" => "2"
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

    $this->exportActiveFlgMap = [
      0 => '有効',
      1 => '無効'
    ];

    $this->exportWidgetOpenMap = [
      1 => '自動で最大化する',
      2 => '自動で最大化しない'
    ];

    $this->exportConditionTypeMap = [
      1 => 'すべて一致',
      2 => 'いずれかが一致'
    ];

    $this->exportChatTextAreaMap = [
      1 => Configure::read('outMessageTextarea')[1],
      2 => Configure::read('outMessageTextarea')[2]
    ];

    $this->exportTriggerCVMap = [
      1 => Configure::read('outMessageCvType')[1],
      2 => Configure::read('outMessageCvType')[2]
    ];

    $this->exportSendMailFlgMap = [
      1 => 'する',
      0 => 'しない'
    ];

    $this->exportStayTimeCheckTypeMap = [
      1 => 'サイト',
      2 => 'ページ'
    ];

    $this->exportStayTimeTypeMap = [
      1 => '秒',
      2 => '分',
      3 => '時'
    ];

    $this->exportVisitCntCondMap = [
      1 => '一致',
      2 => '以上',
      3 => '未満'
    ];

    $this->exportTargetNameMap = [
      1 => 'ページ',
      2 => 'URL',
    ];

    $this->exportKWDContainTypeMap = [
      1 => 'をすぺて含む',
      2 => 'のいずれかを含む',
    ];

    $this->exportKWDExclusionTypeMap = [
      1 => 'をすぺて含む',
      2 => 'のいずれかを含む',
    ];

    $this->exportStayPageCondTypeMap = [
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

    $this->exportSpeechTriggerCondMap = [
      1 => '１回のみ有効',
      2 => '何度でも有効',
    ];

    $this->exportBusinessHourMap = [
      1 => '営業時間内',
      2 => '営業時間外'
    ];
  }

  public function export($data)
  {
    $maxRow = count($data) + self::PLUS_ROW;
    $this->generateTemplate($maxRow);
    $this->writeData($data);
    $this->exportData();
  }

  public function writeData($data)
  {
    $dataMap = array_combine(range(5, count($data) + 4), $data);
    foreach ($dataMap as $row => $value) {
      $json = json_decode($value['TAutoMessage']['activity'], true);
      // name
      $this->phpExcel->getActiveSheet()->setCellValue('C' . $row, $value['TAutoMessage']['name']);
      // active_flg
      $this->phpExcel->getActiveSheet()->setCellValue('B' . $row, $this->exportActiveFlgMap[$value['TAutoMessage']['active_flg']]);
      // conditionType
      $this->phpExcel->getActiveSheet()->setCellValue('D' . $row, $this->exportConditionTypeMap[$json['conditionType']]);
      // action type
      if ($value['TAutoMessage']['action_type'] == 2) {
        // select scenarios
        $this->phpExcel->getActiveSheet()->setCellValue('BD' . $row, 'シナリオを選択する');
        $this->phpExcel->getActiveSheet()->setCellValue('BQ' . $row, $value['TChatbotScenario']['name']);
        $this->phpExcel->getActiveSheet()->setCellValue('BR' . $row, $this->exportWidgetOpenMap[$json['widgetOpen']]);
      } else {
        // send message
        $this->phpExcel->getActiveSheet()->setCellValue('BD' . $row, 'チャットメッセージを送る');
        $this->phpExcel->getActiveSheet()->setCellValue('BE' . $row, $this->exportWidgetOpenMap[$json['widgetOpen']]);
        $this->phpExcel->getActiveSheet()->setCellValue('BF' . $row, $json['message']);
        $this->phpExcel->getActiveSheet()->setCellValue('BG' . $row, $this->exportChatTextAreaMap[$json['chatTextarea']]);
        $this->phpExcel->getActiveSheet()->setCellValue('BH' . $row, $this->exportTriggerCVMap[$json['cv']]);
        $this->phpExcel->getActiveSheet()->setCellValue('BI' . $row, $this->exportSendMailFlgMap[$value['TAutoMessage']['send_mail_flg']]);
        // get mail information
        $mailTransmission = ClassRegistry::init('MMailTransmissionSetting');
        $transmissionData = $mailTransmission->findById($value['TAutoMessage']['m_mail_transmission_settings_id']);
        if (!empty($transmissionData)) {
          $splitedMailAddresses = explode(',', $transmissionData['MMailTransmissionSetting']['to_address']);
          $this->phpExcel->getActiveSheet()->setCellValue('BJ' . $row, !empty($splitedMailAddresses[0]) ? $splitedMailAddresses[0] : "");
          $this->phpExcel->getActiveSheet()->setCellValue('BK' . $row, !empty($splitedMailAddresses[1]) ? $splitedMailAddresses[1] : "");
          $this->phpExcel->getActiveSheet()->setCellValue('BL' . $row, !empty($splitedMailAddresses[2]) ? $splitedMailAddresses[2] : "");
          $this->phpExcel->getActiveSheet()->setCellValue('BM' . $row, !empty($splitedMailAddresses[3]) ? $splitedMailAddresses[3] : "");
          $this->phpExcel->getActiveSheet()->setCellValue('BN' . $row, !empty($splitedMailAddresses[4]) ? $splitedMailAddresses[4] : "");
          $this->phpExcel->getActiveSheet()->setCellValue('BO' . $row, !empty($transmissionData['MMailTransmissionSetting']['subject']) ? $transmissionData['MMailTransmissionSetting']['subject'] : "");
          $this->phpExcel->getActiveSheet()->setCellValue('BP' . $row, !empty($transmissionData['MMailTransmissionSetting']['from_name']) ? $transmissionData['MMailTransmissionSetting']['from_name'] : "");
        }
      }

      // 滞在時間
      if (isset($json['conditions'][1])) {
        $this->phpExcel->getActiveSheet()->setCellValue('E' . $row, 'する');
        $this->phpExcel->getActiveSheet()->setCellValue('F' . $row, $this->exportStayTimeCheckTypeMap[$json['conditions'][1][0]['stayTimeCheckType']]);
        $this->phpExcel->getActiveSheet()->setCellValue('G' . $row, $this->exportStayTimeTypeMap[$json['conditions'][1][0]['stayTimeType']]);
        $this->phpExcel->getActiveSheet()->setCellValue('H' . $row, $json['conditions'][1][0]['stayTimeRange']);
      }

      // 訪問回数
      if (isset($json['conditions'][2])) {
        $this->phpExcel->getActiveSheet()->setCellValue('I' . $row, 'する');
        $this->phpExcel->getActiveSheet()->setCellValue('J' . $row, $json['conditions'][2][0]['visitCnt']);
        $this->phpExcel->getActiveSheet()->setCellValue('K' . $row, $this->exportVisitCntCondMap[$json['conditions'][2][0]['visitCntCond']]);
      }

      // URL
      if (isset($json['conditions'][3])) {
        $this->phpExcel->getActiveSheet()->setCellValue('L' . $row, 'する');
        $this->phpExcel->getActiveSheet()->setCellValue('M' . $row, $this->exportTargetNameMap[$json['conditions'][3][0]['targetName']]);
        $this->phpExcel->getActiveSheet()->setCellValue('N' . $row, $json['conditions'][3][0]['keyword_contains']);
        $this->phpExcel->getActiveSheet()->setCellValue('O' . $row, $this->exportKWDContainTypeMap[$json['conditions'][3][0]['keyword_contains_type']]);
        $this->phpExcel->getActiveSheet()->setCellValue('P' . $row, $json['conditions'][3][0]['keyword_exclusions']);
        $this->phpExcel->getActiveSheet()->setCellValue('Q' . $row, $this->exportKWDContainTypeMap[$json['conditions'][3][0]['keyword_exclusions_type']]);
        $this->phpExcel->getActiveSheet()->setCellValue('R' . $row, $this->exportStayPageCondTypeMap[$json['conditions'][3][0]['stayPageCond']]);
      }

      // 曜日・時間
      if (isset($json['conditions'][4])) {
        $this->phpExcel->getActiveSheet()->setCellValue('U' . $row, 'する');
        $weekDays = '';
        foreach ($json['conditions'][4][0]['day'] as $day => $val) {
          if ($val) {
            $weekDays = $weekDays . $this->exportWeekdayMap[$day] . ', ';
          }
        }

        $this->phpExcel->getActiveSheet()->setCellValue('V' . $row, $weekDays);
        if ($json['conditions'][4][0]['timeSetting'] == 1) {
          $this->phpExcel->getActiveSheet()->setCellValue('W' . $row, $json['conditions'][4][0]['startTime']);
          $this->phpExcel->getActiveSheet()->setCellValue('X' . $row, $json['conditions'][4][0]['endTime']);
        }
      }

      // 参照元URL
      if (isset($json['conditions'][5])) {
        $this->phpExcel->getActiveSheet()->setCellValue('Y' . $row, 'する');
        $this->phpExcel->getActiveSheet()->setCellValue('Z' . $row, $json['conditions'][5][0]['keyword_contains']);
        $this->phpExcel->getActiveSheet()->setCellValue('AA' . $row, $this->exportKWDContainTypeMap[$json['conditions'][5][0]['keyword_contains_type']]);
        $this->phpExcel->getActiveSheet()->setCellValue('AB' . $row, $json['conditions'][5][0]['keyword_exclusions']);
        $this->phpExcel->getActiveSheet()->setCellValue('AC' . $row, $this->exportKWDContainTypeMap[$json['conditions'][5][0]['keyword_exclusions_type']]);
        $this->phpExcel->getActiveSheet()->setCellValue('AD' . $row, $this->exportStayPageCondTypeMap[$json['conditions'][5][0]['referrerCond']]);
      }

      // 検索キーワード
      if (isset($json['conditions'][6])) {
        $this->phpExcel->getActiveSheet()->setCellValue('AE' . $row, 'する');
        $this->phpExcel->getActiveSheet()->setCellValue('AF' . $row, $json['conditions'][6][0]['keyword']);
        $this->phpExcel->getActiveSheet()->setCellValue('AG' . $row, $this->exportStayPageCondTypeMap[$json['conditions'][6][0]['searchCond']]);
      }

      // 発言内容
      if (isset($json['conditions'][7])) {
        $this->phpExcel->getActiveSheet()->setCellValue('AH' . $row, 'する');
        $this->phpExcel->getActiveSheet()->setCellValue('AI' . $row, $json['conditions'][7][0]['keyword_contains']);
        $this->phpExcel->getActiveSheet()->setCellValue('AJ' . $row, $this->exportKWDContainTypeMap[$json['conditions'][7][0]['keyword_contains_type']]);
        $this->phpExcel->getActiveSheet()->setCellValue('AK' . $row, $json['conditions'][7][0]['keyword_exclusions']);
        $this->phpExcel->getActiveSheet()->setCellValue('AL' . $row, $this->exportKWDContainTypeMap[$json['conditions'][7][0]['keyword_exclusions_type']]);
        $this->phpExcel->getActiveSheet()->setCellValue('AM' . $row, $this->exportStayPageCondTypeMap[$json['conditions'][7][0]['speechContentCond']]);
        $this->phpExcel->getActiveSheet()->setCellValue('AN' . $row, $json['conditions'][7][0]['triggerTimeSec']);
        $this->phpExcel->getActiveSheet()->setCellValue('AO' . $row, $this->exportSpeechTriggerCondMap[$json['conditions'][7][0]['speechTriggerCond']]);
      }

      // 最初に訪れたページ
      if (isset($json['conditions'][8])) {
        $this->phpExcel->getActiveSheet()->setCellValue('AP' . $row, 'する');
        $this->phpExcel->getActiveSheet()->setCellValue('AQ' . $row, $this->exportTargetNameMap[$json['conditions'][8][0]['targetName']]);
        $this->phpExcel->getActiveSheet()->setCellValue('AR' . $row, $json['conditions'][8][0]['keyword_contains']);
        $this->phpExcel->getActiveSheet()->setCellValue('AS' . $row, $this->exportKWDContainTypeMap[$json['conditions'][8][0]['keyword_contains_type']]);
        $this->phpExcel->getActiveSheet()->setCellValue('AT' . $row, $json['conditions'][8][0]['keyword_exclusions']);
        $this->phpExcel->getActiveSheet()->setCellValue('AU' . $row, $this->exportKWDContainTypeMap[$json['conditions'][8][0]['keyword_exclusions_type']]);
        $this->phpExcel->getActiveSheet()->setCellValue('AV' . $row, $this->exportStayPageCondTypeMap[$json['conditions'][8][0]['stayPageCond']]);
      }

      // 前のページ
      if (isset($json['conditions'][9])) {
        $this->phpExcel->getActiveSheet()->setCellValue('AW' . $row, 'する');
        $this->phpExcel->getActiveSheet()->setCellValue('AX' . $row, $this->exportTargetNameMap[$json['conditions'][9][0]['targetName']]);
        $this->phpExcel->getActiveSheet()->setCellValue('AY' . $row, $json['conditions'][9][0]['keyword_contains']);
        $this->phpExcel->getActiveSheet()->setCellValue('AZ' . $row, $this->exportKWDContainTypeMap[$json['conditions'][9][0]['keyword_contains_type']]);
        $this->phpExcel->getActiveSheet()->setCellValue('BA' . $row, $json['conditions'][9][0]['keyword_exclusions']);
        $this->phpExcel->getActiveSheet()->setCellValue('BB' . $row, $this->exportKWDContainTypeMap[$json['conditions'][9][0]['keyword_exclusions_type']]);
        $this->phpExcel->getActiveSheet()->setCellValue('BC' . $row, $this->exportStayPageCondTypeMap[$json['conditions'][9][0]['stayPageCond']]);
      }

      // 営業時間
      if (isset($json['conditions'][10])) {
        $this->phpExcel->getActiveSheet()->setCellValue('S' . $row, 'する');
        $this->phpExcel->getActiveSheet()->setCellValue('T' . $row, $this->exportBusinessHourMap[$json['conditions'][10][0]['operatingHoursTime']]);
      }
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

  public function setColumnConditionalFormat($beginColumn, $row, $condition)
  {
    $conditionCol   = ($beginColumn == 'BE' || $beginColumn == 'BQ') ? 'BD' : $beginColumn;
    $condition      = '$' . $conditionCol . $row . ' = "' . $condition . '"';
    $objConditional = new PHPExcel_Style_Conditional();
    $objConditional->setConditionType(PHPExcel_Style_Conditional::CONDITION_EXPRESSION);
    $objConditional->addCondition($condition);
    $objConditional->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getEndColor()->setARGB('FFBFBFBF');

    $conditionalStyles = $this->phpExcel->getActiveSheet()->getStyle($beginColumn . $row)->getConditionalStyles();
    array_push($conditionalStyles, $objConditional);
    $this->setConditionStyle($beginColumn, $conditionalStyles, $row);
  }

  public function setRowConditionalFormat($row)
  {
    $this->setColumnConditionalFormat('E', $row, "しない");
    $this->setColumnConditionalFormat('I', $row, "しない");
    $this->setColumnConditionalFormat('L', $row, "しない");
    $this->setColumnConditionalFormat('S', $row, "しない");
    $this->setColumnConditionalFormat('U', $row, "しない");
    $this->setColumnConditionalFormat('Y', $row, "しない");
    $this->setColumnConditionalFormat('AE', $row, "しない");
    $this->setColumnConditionalFormat('AH', $row, "しない");
    $this->setColumnConditionalFormat('AP', $row, "しない");
    $this->setColumnConditionalFormat('AW', $row, "しない");
    $this->setColumnConditionalFormat('BE', $row, "シナリオを選択する");
    $this->setColumnConditionalFormat('BQ', $row, "チャットメッセージを送る");
  }

  public function setConditionStyle($beginColumn, $conditionalStyles, $index)
  {
    switch ($beginColumn) {
      case 'E':
        $this->phpExcel->getActiveSheet()->getStyle('E' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('F' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('G' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('H' . $index)->setConditionalStyles($conditionalStyles);
        break;
      case 'I':
        $this->phpExcel->getActiveSheet()->getStyle('I' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('J' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('K' . $index)->setConditionalStyles($conditionalStyles);
        break;
      case 'L':
        $this->phpExcel->getActiveSheet()->getStyle('L' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('M' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('N' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('O' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('P' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('Q' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('R' . $index)->setConditionalStyles($conditionalStyles);
        break;
      case 'S':
        $this->phpExcel->getActiveSheet()->getStyle('S' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('T' . $index)->setConditionalStyles($conditionalStyles);
        break;
      case 'U':
        $this->phpExcel->getActiveSheet()->getStyle('U' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('V' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('W' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('X' . $index)->setConditionalStyles($conditionalStyles);
        break;
      case 'Y':
        $this->phpExcel->getActiveSheet()->getStyle('Y' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('Z' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('AA' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('AB' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('AC' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('AD' . $index)->setConditionalStyles($conditionalStyles);
        break;
      case 'AE':
        $this->phpExcel->getActiveSheet()->getStyle('AE' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('AF' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('AG' . $index)->setConditionalStyles($conditionalStyles);
        break;
      case 'AH':
        $this->phpExcel->getActiveSheet()->getStyle('AH' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('AI' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('AJ' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('AK' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('AL' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('AM' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('AN' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('AO' . $index)->setConditionalStyles($conditionalStyles);
        break;
      case 'AP':
        $this->phpExcel->getActiveSheet()->getStyle('AP' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('AQ' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('AR' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('AS' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('AT' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('AU' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('AV' . $index)->setConditionalStyles($conditionalStyles);
        break;
      case 'AW':
        $this->phpExcel->getActiveSheet()->getStyle('AW' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('AX' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('AY' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('AZ' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('BA' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('BB' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('BC' . $index)->setConditionalStyles($conditionalStyles);
        break;
      case 'BE':
        $this->phpExcel->getActiveSheet()->getStyle('BE' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('BF' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('BG' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('BH' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('BI' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('BJ' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('BK' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('BL' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('BM' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('BN' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('BO' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('BP' . $index)->setConditionalStyles($conditionalStyles);
        break;
      case 'BQ':
        $this->phpExcel->getActiveSheet()->getStyle('BQ' . $index)->setConditionalStyles($conditionalStyles);
        $this->phpExcel->getActiveSheet()->getStyle('BR' . $index)->setConditionalStyles($conditionalStyles);
        break;
      default:
        break;
    }
  }

  public function setRowDataValidation($row)
  {
    $this->setCellDataValidation('B', $row, "無効", "無効, 有効");
    $this->setCellDataValidation('D', $row, "すべて一致", "すべて一致, いずれかが一致");
    // 滞在時間
    $this->setCellDataValidation('E', $row, "しない", "しない, する");
    $this->setCellDataValidation('F', $row, "", "サイト, ページ");
    $this->setCellDataValidation('G', $row, "", "秒, 分, 時");
    // 訪問回数
    $this->setCellDataValidation('I', $row, "しない", "しない, する");
    $this->setCellDataValidation('K', $row, "", "一致, 以上, 未満");
    // ページ
    $this->setCellDataValidation('L', $row, "しない", "しない, する");
    $this->setCellDataValidation('M', $row, "", "URL, タイトル");
    $this->setCellDataValidation('O', $row, "", "をすべて含む, のいずれかを含む");
    $this->setCellDataValidation('Q', $row, "", "をすべて含む, のいずれかを含む");
    $this->setCellDataValidation('R', $row, "", "完全一致, 部分一致");
    // 営業時間
    $this->setCellDataValidation('S', $row, "しない", "しない, する");
    $this->setCellDataValidation('T', $row, "", "営業時間内, 営業時間外");
    // 曜日・時間
    $this->setCellDataValidation('U', $row, "しない", "しない, する");
    // 参照元URL（リファラー）
    $this->setCellDataValidation('Y', $row, "しない", "しない, する");
    $this->setCellDataValidation('AA', $row, "", "をすべて含む, のいずれかを含む");
    $this->setCellDataValidation('AC', $row, "", "をすべて含む, のいずれかを含む");
    $this->setCellDataValidation('AD', $row, "", "完全一致, 部分一致");
    // 検索キーワード
    $this->setCellDataValidation('AE', $row, "しない", "しない, する");
    $this->setCellDataValidation('AG', $row, "", "完全一致, 部分一致, 不一致");
    // 発言内容
    $this->setCellDataValidation('AH', $row, "しない", "しない, する");
    $this->setCellDataValidation('AJ', $row, "", "をすべて含む, のいずれかを含む");
    $this->setCellDataValidation('AM', $row, "", "完全一致, 部分一致");
    $this->setCellDataValidation('AL', $row, "", "をすべて含む, のいずれかを含む");
    $this->setCellDataValidation('AO', $row, "", "一回のみ有効, 何度でも有効");
    // 最初に訪れたページ
    $this->setCellDataValidation('AP', $row, "しない", "しない, する");
    $this->setCellDataValidation('AQ', $row, "", "URL, タイトル");
    $this->setCellDataValidation('AS', $row, "", "をすべて含む, のいずれかを含む");
    $this->setCellDataValidation('AU', $row, "", "をすべて含む, のいずれかを含む");
    $this->setCellDataValidation('AV', $row, "", "完全一致, 部分一致");
    // 前のページ
    $this->setCellDataValidation('AW', $row, "しない", "しない, する");
    $this->setCellDataValidation('AX', $row, "", "URL, タイトル");
    $this->setCellDataValidation('AZ', $row, "", "をすべて含む, のいずれかを含む");
    $this->setCellDataValidation('BB', $row, "", "をすべて含む, のいずれかを含む");
    $this->setCellDataValidation('BC', $row, "", "完全一致, 部分一致");

    // 実行設定
    $this->setCellDataValidation('BD', $row, "チャットメッセージを送る", "チャットメッセージを送る, シナリオを選択する");
    $this->setCellDataValidation('BE', $row, "自動で最大化する", "自動で最大化する, 自動で最大化しない");
    $this->setCellDataValidation('BG', $row, "ON （自由入力可）", "ON （自由入力可）, OFF (自由入力不可）");
    $this->setCellDataValidation('BH', $row, "しない", "しない, する");
    $this->setCellDataValidation('BI', $row, "しない", "しない, する");

    $this->setCellDataValidation('BR', $row, "", "自動で最大化する, 自動で最大化しない");

//    $this->setCellDataValidation('V', $row, "", "月, 火, 水, 木, 金, 土, 日");
  }

  public function setCellDataValidation($column, $row, $default, $options)
  {
    $this->phpExcel->getActiveSheet()->setCellValue($column . $row, $default);
    $objValidation = $this->phpExcel->getActiveSheet()->getCell($column . $row)->getDataValidation();
    $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
    $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
    $objValidation->setAllowBlank(false);
    $objValidation->setShowInputMessage(true);
    $objValidation->setShowErrorMessage(true);
    $objValidation->setShowDropDown(true);
    $objValidation->setFormula1('"' . $options . '"');
  }

  public function copyRowStyle($baseRow, $targetRow)
  {
    foreach (range('A', 'R') as $column) {
      $this->phpExcel->getActiveSheet()->duplicateStyle($this->phpExcel->getActiveSheet()->getStyle('B' . $column . $baseRow), 'B' . $column . $targetRow);
    }

    foreach (range('A', 'Z') as $column) {
      $this->phpExcel->getActiveSheet()->duplicateStyle($this->phpExcel->getActiveSheet()->getStyle($column . $baseRow), $column . $targetRow);
      $this->phpExcel->getActiveSheet()->duplicateStyle($this->phpExcel->getActiveSheet()->getStyle('A' . $column . $baseRow), 'A' . $column . $targetRow);
    }
  }

  private function isBOLrow($row) {
    return strcmp($this->t($row['T']), self::CONTENT_BOL) === 0;
  }

  private function isEOLrow($row) {
    return strcmp($this->t($row['T']), self::CONTENT_EOL) === 0;
  }

  private function isSampleDataRow($index) {
    return strcmp($index, 10) === 0 || strcmp($index, 11) === 0;
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
      $this->addError($errors, self::ROW_EXCLUSIONS_TYPE,'すべて含む／いずれかを含む のいずれかの指定のみ可能です');
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