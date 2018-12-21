<?php
/**
 * Created by PhpStorm.
 * User: toan.hoang.the
 * Date: 2018/12/14
 * Time: 10:28
 */
App::uses('AutoMessageException','Lib/Error');
App::uses('ExcelParserComponent', 'Controller/Component');

class AutoMessageExcelImportComponent extends ExcelParserComponent
{
  private $containsTypeMap;
  private $exclusionsTypeMap;
  private $keywordConditionMap;
  private $triggerConditionMap;
  private $triggerFreeInputMap;

  private $isSettingMap;
  private $activeFlgMap;
  private $conditionTypeMap;
  private $actionTypeMap;
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
  private $weekdayMap;
  private $speechTriggerCondMap;
  private $businessHourMap;

  public function __construct($filePath)
  {
    parent::__construct($filePath);
    $this->readSettingMapFromConfig();
  }

  public function getImportData()
  {
    $this->readData(true);
    $this->setActiveSheet(0);
  }

  private function readSettingMapFromConfig() {
    $this->containsTypeMap = [
      'をすぺて含む' => "1",
      'のいずれかを含む' => "2"
    ];

    $this->exclusionsTypeMap = [
      'をすぺて含む' => "1",
      'のいずれかを含む' => "2"
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

    $this->isSettingMap = [
      'する' => 1,
      'しない' => 0
    ];

    $this->activeFlgMap = [
      '有効' => 0,
      '無効' => 1
    ];

    $this->widgetOpenMap = [
      '自動で最大化する' => 1,
      '自動で最大化しない' => 2
    ];

    $this->conditionTypeMap = [
      'すべて一致' => 1,
      'いずれかが一致' => 2
    ];

    $this->actionTypeMap = [
      'シナリオを選択する' => 2,
      'チャットメッセージを送る' => 1
    ];

    $this->chatTextAreaMap = [
      Configure::read('outMessageTextarea')[1] => 1,
      Configure::read('outMessageTextarea')[2] => 2
    ];

    $this->triggerCVMap = [
      Configure::read('outMessageCvType')[1] => 1,
      Configure::read('outMessageCvType')[2] => 2,
    ];

    $this->sendMailFlgMap = [
      'する' => 1,
      'しない' => 0
    ];

    $this->stayTimeCheckTypeMap = [
      'サイト' => 1,
      'ページ' => 2
    ];

    $this->stayTimeTypeMap = [
      '秒' => "1",
      '分' => "2",
      '時' => "3"
    ];

    $this->visitCntCondMap = [
      '一致' => 1,
      '以上' => 2,
      '未満' => 3
    ];

    $this->targetNameMap = [
      'ページ' => 1,
      'URL' => 2,
    ];

    $this->kWDContainTypeMap = [
      'すぺて含む' => 1,
      'のいずれかを含む' => 2,
    ];

    $this->kWDExclusionTypeMap = [
      'すぺて含む' => 1,
      'のいずれかを含む' => 2,
    ];

    $this->stayPageCondTypeMap = [
      '完全一致' => 1,
      '部分一致' => 2,
      '不一致' => 3
    ];

    $this->weekdayMap = [
      '月' => 'mon',
      '火' => 'tue',
      '水' => 'wed',
      '木' => 'thu',
      '金' => 'fri',
      '土' => 'sat',
      '日' => 'sun',
    ];

    $this->speechTriggerCondMap = [
      '１回のみ有効' => 1,
      '何度でも有効' => 2,
    ];

    $this->businessHourMap = [
      '営業時間内' => 1,
      '営業時間外' => 2,
    ];
  }

  public function parseData()
  {
    $blankRow = [
      'A' => NULL,
      'B' => '無効',
      'C' => NULL,
      'D' => 'すべて一致',
      'E' => 'しない',
      'F' => NULL,
      'G' => NULL,
      'H' => NULL,
      'I' => 'しない',
      'J' => NULL,
      'K' => NULL,
      'L' => 'しない',
      'M' => NULL,
      'N' => NULL,
      'O' => NULL,
      'P' => NULL,
      'Q' => NULL,
      'R' => NULL,
      'S' => 'しない',
      'T' => NULL,
      'U' => 'しない',
      'V' => NULL,
      'W' => NULL,
      'X' => NULL,
      'Y' => 'しない',
      'Z' => NULL,
      'AA' => NULL,
      'AB' => NULL,
      'AC' => NULL,
      'AD' => NULL,
      'AE' => 'しない',
      'AF' => NULL,
      'AG' => NULL,
      'AH' => 'しない',
      'AI' => NULL,
      'AJ' => NULL,
      'AK' => NULL,
      'AL' => NULL,
      'AM' => NULL,
      'AN' => NULL,
      'AO' => NULL,
      'AP' => 'しない',
      'AQ' => NULL,
      'AR' => NULL,
      'AS' => NULL,
      'AT' => NULL,
      'AU' => NULL,
      'AV' => NULL,
      'AW' => 'しない',
      'AX' => NULL,
      'AY' => NULL,
      'AZ' => NULL,
      'BA' => NULL,
      'BB' => NULL,
      'BC' => NULL,
      'BD' => 'チャットメッセージを送る',
      'BE' => '自動で最大化する',
      'BF' => NULL,
      'BG' => 'ON（自由入力可）',
      'BH' => 'しない',
      'BI' => 'しない',
      'BJ' => NULL,
      'BK' => NULL,
      'BL' => NULL,
      'BM' => NULL,
      'BN' => NULL,
      'BO' => NULL,
      'BP' => NULL,
      'BQ' => NULL,
      'BR' => NULL,
    ];
    $importData = [];
    $errorFound = false;
    foreach ($this->dataArray as $key => $row) {
      // if row is header -> continue
      if ($key <= 4) {
        continue;
      }

      if ($row != $blankRow) {
        $errors = $this->rowValidate($row);

        if (!empty($errors)) {
          $errorFound = true;
          $errorArray[$key] = $errors;
        } else {
          $importData[$key]['name']       = $row['C'];
          $importData[$key]['active_flg'] = $this->activeFlgMap[$row['B']];

          $importData[$key]['activity']['conditionType'] = $this->conditionTypeMap[$row['D']];
          $importData[$key]['action_type']               = $actionType = $this->actionTypeMap[$row['BD']];

          // 滞在時間
          if ($this->isSettingMap[$row['E']] == 1) {
            $importData[$key]['activity']['conditions'][1][0]['stayTimeCheckType'] = $this->stayTimeCheckTypeMap[$row['F']];
            $importData[$key]['activity']['conditions'][1][0]['stayTimeType']      = $this->stayTimeTypeMap[$row['G']];
            $importData[$key]['activity']['conditions'][1][0]['stayTimeRange']     = $row['H'];
          }

          // 訪問回数
          if ($this->isSettingMap[$row['I']] == 1) {
            $importData[$key]['activity']['conditions'][2][0]['visitCnt']     = $row['J'];
            $importData[$key]['activity']['conditions'][2][0]['visitCntCond'] = $this->visitCntCondMap[$row['K']];
          }

          // ページ
          if ($this->isSettingMap[$row['L']] == 1) {
            $importData[$key]['activity']['conditions'][3][0]['targetName']              = $this->targetNameMap[$row['M']];
            $importData[$key]['activity']['conditions'][3][0]['keyword_contains']        = $this->getCellValue($row['N']);
            $importData[$key]['activity']['conditions'][3][0]['keyword_contains_type']   = $this->containsTypeMap[$row['O']];
            $importData[$key]['activity']['conditions'][3][0]['keyword_exclusions']      = $this->getCellValue($row['P']);
            $importData[$key]['activity']['conditions'][3][0]['keyword_exclusions_type'] = $this->exclusionsTypeMap[$row['Q']];
            $importData[$key]['activity']['conditions'][3][0]['stayPageCond']            = $this->stayPageCondTypeMap[$row['R']];
          }

          // 曜日・時間
          if ($this->isSettingMap[$row['U']] == 1) {
            $day        = [
              'mon' => false,
              'tue' => false,
              'wed' => false,
              'thu' => false,
              'fri' => false,
              'sat' => false,
              'sun' => false,
            ];
            $string = str_replace(' ', '', $row['V']);
            $importDays = explode(',', rtrim($string, ','));
            foreach ($importDays as $importDay) {
              $day[$this->weekdayMap[$importDay]] = true;
            }
            $importData[$key]['activity']['conditions'][5][0]['day'] = $day;
            if (!empty($row['W'] && !empty($row['X']))) {
              $importData[$key]['activity']['conditions'][5][0]['timeSetting'] = 1;
              $importData[$key]['activity']['conditions'][5][0]['startTime']   = $this->getCellValue($row['W']);
              $importData[$key]['activity']['conditions'][5][0]['endTime']     = $this->getCellValue($row['X']);
            } else {
              $importData[$key]['activity']['conditions'][5][0]['timeSetting'] = 2;
            }
          }

          // 参照元URL（リファラー）
          if ($this->isSettingMap[$row['Y']] == 1) {
            $importData[$key]['activity']['conditions'][6][0]['keyword_contains']        = $this->getCellValue($row['Z']);
            $importData[$key]['activity']['conditions'][6][0]['keyword_contains_type']   = $this->containsTypeMap[$row['AA']];
            $importData[$key]['activity']['conditions'][6][0]['keyword_exclusions']      = $this->getCellValue($row['AB']);
            $importData[$key]['activity']['conditions'][6][0]['keyword_exclusions_type'] = $this->exclusionsTypeMap[$row['AC']];
            $importData[$key]['activity']['conditions'][6][0]['referrerCond']            = $this->stayPageCondTypeMap[$row['AD']];
          }

          // 検索キーワード
          if ($this->isSettingMap[$row['AE']] == 1) {
            $importData[$key]['activity']['conditions'][7][0]['keyword']    = $this->getCellValue($row['AF']);
            $importData[$key]['activity']['conditions'][7][0]['searchCond'] = $this->stayPageCondTypeMap[$row['AG']];
          }

          // 発言内容
          if ($this->isSettingMap[$row['AH']] == 1) {
            $importData[$key]['activity']['conditions'][8][0]['keyword_contains']        = $this->getCellValue($row['AI']);
            $importData[$key]['activity']['conditions'][8][0]['keyword_contains_type']   = $this->containsTypeMap[$row['AJ']];
            $importData[$key]['activity']['conditions'][8][0]['keyword_exclusions']      = $this->getCellValue($row['AK']);
            $importData[$key]['activity']['conditions'][8][0]['keyword_exclusions_type'] = $this->exclusionsTypeMap[$row['AL']];
            $importData[$key]['activity']['conditions'][8][0]['speechContentCond']       = $this->stayPageCondTypeMap[$row['AM']];
            $importData[$key]['activity']['conditions'][8][0]['triggerTimeSec']          = (int)$this->getCellValue($row['AN']);
            $importData[$key]['activity']['conditions'][8][0]['speechTriggerCond']       = $this->speechTriggerCondMap[$row['AO']];
          }

          // 最初に訪れたページ
          if ($this->isSettingMap[$row['AP']] == 1) {
            $importData[$key]['activity']['conditions'][9][0]['targetName']              = $this->targetNameMap[$row['AQ']];
            $importData[$key]['activity']['conditions'][9][0]['keyword_contains']        = $this->getCellValue($row['AR']);
            $importData[$key]['activity']['conditions'][9][0]['keyword_contains_type']   = $this->containsTypeMap[$row['AS']];
            $importData[$key]['activity']['conditions'][9][0]['keyword_exclusions']      = $this->getCellValue($row['AT']);
            $importData[$key]['activity']['conditions'][9][0]['keyword_exclusions_type'] = $this->exclusionsTypeMap[$row['AU']];
            $importData[$key]['activity']['conditions'][9][0]['stayPageCond']            = $this->stayPageCondTypeMap[$row['AV']];
          }

          // 前のページ
          if ($this->isSettingMap[$row['AW']] == 1) {
            $importData[$key]['activity']['conditions'][10][0]['targetName']              = $this->targetNameMap[$row['AX']];
            $importData[$key]['activity']['conditions'][10][0]['keyword_contains']        = $this->getCellValue($row['AY']);
            $importData[$key]['activity']['conditions'][10][0]['keyword_contains_type']   = $this->containsTypeMap[$row['AZ']];
            $importData[$key]['activity']['conditions'][10][0]['keyword_exclusions']      = $this->getCellValue($row['BA']);
            $importData[$key]['activity']['conditions'][10][0]['keyword_exclusions_type'] = $this->exclusionsTypeMap[$row['BB']];
            $importData[$key]['activity']['conditions'][10][0]['stayPageCond']            = $this->stayPageCondTypeMap[$row['BC']];
          }

          // 営業時間
          if ($this->isSettingMap[$row['S']] == 1) {
            $importData[$key]['activity']['conditions'][4][0]['operatingHoursTime'] = $this->businessHourMap[$row['T']];
          }

          if ($actionType == 2) {
            // scenario
            $importData[$key]['activity']['widgetOpen'] = $this->widgetOpenMap[$row['BR']];
            $importData[$key]['activity']['message']      = "";
            $importData[$key]['activity']['chatTextarea'] = 1;
            $importData[$key]['activity']['cv']           = 2;
            $importData[$key]['scenario']               = $this->getCellValue($row['BQ']);
          } else {
            // send mail
            $importData[$key]['activity']['widgetOpen']   = $this->widgetOpenMap[$row['BE']];
            $importData[$key]['activity']['message']      = $row['BF'];
            $importData[$key]['activity']['chatTextarea'] = $this->chatTextAreaMap[$row['BG']];
            $importData[$key]['activity']['cv']           = $this->triggerCVMap[$row['BH']];
            $importData[$key]['send_mail_flg']            = $sendMailFlg = $this->sendMailFlgMap[$row['BI']];
            if ($sendMailFlg == 1) {
              $importData[$key]['mail_address_1'] = $this->getCellValue($row['BJ']);
              $importData[$key]['mail_address_2'] = $this->getCellValue($row['BK']);
              $importData[$key]['mail_address_3'] = $this->getCellValue($row['BL']);
              $importData[$key]['mail_address_4'] = $this->getCellValue($row['BM']);
              $importData[$key]['mail_address_5'] = $this->getCellValue($row['BN']);
              $importData[$key]['mail_subject']   = $this->getCellValue($row['BO']);
              $importData[$key]['mail_from_name'] = $this->getCellValue($row['BP']);
            }
        }

        }
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
   * @param $row
   * @return array
   */
  private function rowValidate($row) {
    $errors = [];
    if(!Validation::maxLength($row['C'], 50)) {
      $this->addError($errors, 'C','５０文字以内で入力してください');
    }

    if(empty($row['B'])) {
      $this->addError($errors, 'B','有効／無効 のいずれかの指定のみ可能です');
    }
    // 滞在時間
    if ($this->isSettingMap[$row['E']] == 1) {
      if(empty($row['F'])) {
        $this->addError($errors, 'F','サイト／ページ のいずれかの指定のみ可能です');
      }

      if(empty($row['G'])) {
        $this->addError($errors, 'G','秒/分/時 のいずれかの指定のみ可能です');
      }

      if(!Validation::numeric($row['H'])) {
        $this->addError($errors, 'H','数字のみ指定可能です');
      }
    }

    // 訪問回数
    if ($this->isSettingMap[$row['I']] == 1) {
      if(!Validation::numeric($row['J'])) {
        $this->addError($errors, 'J','数字のみ指定可能です');
      }

      if(empty($row['K'])) {
        $this->addError($errors, 'K','一致/以上/未満 のいずれかの指定のみ可能です');
      }
    }

    // ページ
    if ($this->isSettingMap[$row['L']] == 1) {
      if(empty($row['M'])) {
        $this->addError($errors, 'M','URL/タイトル のいずれかの指定のみ可能です');
      }

      if(empty($row['N']) && empty($row['P'])) {
        $this->addError($errors, 'N','キーワードはいずれかの指定が必須です');
      }

      if(!empty($row['N']) && empty($row['O'])) {
        $this->addError($errors, 'O','をすべて含む／のいずれかを含む のいずれかの指定のみ可能です');
      }

      if(!empty($row['P']) && empty($row['Q'])) {
        $this->addError($errors, 'Q','をすべて含む／のいずれかを含む のいずれかの指定のみ可能です');
      }

      if(empty($row['R'])) {
        $this->addError($errors, 'R','完全一致/部分一致 のいずれかの指定のみ可能です');
      }
    }

    // 曜日・時間
    if ($this->isSettingMap[$row['U']] == 1) {
      if (empty($row['V'])) {
        $this->addError($errors, 'V', '曜日が未入力です');
      } else {
        $string     = str_replace(' ', '', $row['V']);
        $importDays = explode(',', rtrim($string, ','));
        foreach ($importDays as $importDay) {
          if (!in_array($importDay, array_keys($this->weekdayMap))) {
            $this->addError($errors, 'V', '月/火/水/木/金/土/日 のみ指定可能です');
            break;
          }
        }
      }

      if(empty($row['W']) && !empty($row['X'])) {
        $this->addError($errors, 'X','開始時間が未入力です');
      }

      if(!empty($row['W']) && empty($row['X'])) {
          $this->addError($errors, 'X','終了時間が未入力です');
      }

      if(!empty($row['W'])) {
        $time = PHPExcel_Style_NumberFormat::toFormattedString($row['W'], 'hh:mm');
        if (!preg_match("/^(?:2[0-4]|[01][1-9]|10):([0-5][0-9])$/", $time)) {
          $this->addError($errors, 'W','MM:SS形式のみ指定可能です');
        }
      }

      if(!empty($row['X'])) {
        $time = PHPExcel_Style_NumberFormat::toFormattedString($row['X'], 'hh:mm');
        if (!preg_match("/^(?:2[0-4]|[01][1-9]|10):([0-5][0-9])$/", $time)) {
          $this->addError($errors, 'X','MM:SS形式のみ指定可能です');
        }
      }
    }

    // 参照元URL（リファラー）
    if ($this->isSettingMap[$row['Y']] == 1) {
      if(empty($row['AA']) && empty($row['AC'])) {
        $this->addError($errors, 'AA','キーワードはいずれかの指定が必須です');
      }

      if(!empty($row['Z']) && empty($row['AA'])) {
        $this->addError($errors, 'AA','をすべて含む／のいずれかを含む のいずれかの指定のみ可能です');
      }

      if(!empty($row['AB']) && empty($row['AC'])) {
        $this->addError($errors, 'AC','をすべて含む／のいずれかを含む のいずれかの指定のみ可能です');
      }

      if(empty($row['AD'])) {
        $this->addError($errors, 'AD','完全一致/部分一致 のいずれかの指定のみ可能です');
      }
    }

    // 検索キーワード
    if ($this->isSettingMap[$row['AE']] == 1) {
      if(empty($row['AF'])) {
        $this->addError($errors, 'AF','キーワードが未入力です。');
      }

      if(empty($row['AG'])) {
        $this->addError($errors, 'AG','完全一致/部分一致/不一致 のいずれかの指定のみ可能です');
      }
    }

    // 発言内容
    if ($this->isSettingMap[$row['AH']] == 1) {
      if(empty($row['AI']) && empty($row['AK'])) {
        $this->addError($errors, 'AI','キーワードはいずれかの指定が必須です');
      }

      if(!empty($row['AI']) && empty($row['AJ'])) {
        $this->addError($errors, 'AI','をすべて含む／のいずれかを含む のいずれかの指定のみ可能です');
      }

      if(!empty($row['AK']) && empty($row['AL'])) {
        $this->addError($errors, 'AK','をすべて含む／のいずれかを含む のいずれかの指定のみ可能です');
      }

      if(empty($row['AM'])) {
        $this->addError($errors, 'AM','完全一致/部分一致 のいずれかの指定のみ可能です');
      }

      if(!Validation::range($row['AN'], 0, 61)) {
        $this->addError($errors, 'AN','1から60までの数値指定のみ可能です');
      }

      if(empty($row['AO'])) {
        $this->addError($errors, 'AO','1回のみ有効／何度でも有効 のいずれかの指定のみ可能です');
      }
    }

    // 最初に訪れたページ
    if ($this->isSettingMap[$row['AP']] == 1) {
      if(empty($row['AQ'])) {
        $this->addError($errors, 'AQ','URL/タイトル のいずれかの指定のみ可能です');
      }

      if(empty($row['AR']) && empty($row['AT'])) {
        $this->addError($errors, 'AR','キーワードはいずれかの指定が必須です');
      }

      if(!empty($row['AR']) && empty($row['AS'])) {
        $this->addError($errors, 'AR','をすべて含む／のいずれかを含む のいずれかの指定のみ可能です');
      }

      if(!empty($row['AT']) && empty($row['AU'])) {
        $this->addError($errors, 'AT','をすべて含む／のいずれかを含む のいずれかの指定のみ可能です');
      }

      if(empty($row['AV'])) {
        $this->addError($errors, 'AV','完全一致/部分一致 のいずれかの指定のみ可能です');
      }
    }

    // 前のページ
    if ($this->isSettingMap[$row['AW']] == 1) {
      if(empty($row['AX'])) {
        $this->addError($errors, 'AX','URL/タイトル のいずれかの指定のみ可能です');
      }

      if(empty($row['AY']) && empty($row['BA'])) {
        $this->addError($errors, 'AY','キーワードはいずれかの指定が必須です');
      }

      if(!empty($row['AY']) && empty($row['AZ'])) {
        $this->addError($errors, 'AY','をすべて含む／のいずれかを含む のいずれかの指定のみ可能です');
      }

      if(!empty($row['BA']) && empty($row['BB'])) {
        $this->addError($errors, 'BA','をすべて含む／のいずれかを含む のいずれかの指定のみ可能です');
      }

      if(empty($row['BC'])) {
        $this->addError($errors, 'BC','完全一致/部分一致 のいずれかの指定のみ可能です');
      }
    }

    // 営業時間
    if ($this->isSettingMap[$row['S']] == 1) {
      if(empty($row['T'])) {
        $this->addError($errors, 'T','営業時間内/営業時間外 のいずれかの指定のみ可能です');
      }
    }

    // send message
    if ($this->actionTypeMap[$row['BD']] == 1) {
      if(empty($row['BE'])) {
        $this->addError($errors, 'BE','自動で最大化する／自動で最大化しない のいずれかの指定のみ可能です');
      }

      if(empty($row['BF'])) {
        $this->addError($errors, 'BF','メッセージの指定は必須です');
      }

      if(empty($row['BG'])) {
        $this->addError($errors, 'BG','ON（自由入力可）/OFF（自由入力不可） のいずれかの指定のみ可能です');
      }

      if(empty($row['BH'])) {
        $this->addError($errors, 'BH','する/しない のいずれかの指定のみ可能です');
      }

      if(empty($row['BI'])) {
        $this->addError($errors, 'BI','する/しない のいずれかの指定のみ可能です');
      }
      // if send mail
      if($this->isSettingMap[$row['BI']] == 1) {
        if(empty($row['BO'])) {
          $this->addError($errors, 'BI','メールタイトルの指定は必須です');
        }

        if(!Validation::maxLength($row['BO'], 100)) {
          $this->addError($errors, 'BI','メールタイトルは１００文字以内で設定してください');
        }

        if(empty($row['BP'])) {
          $this->addError($errors, 'BI','差出人名の指定は必須です');
        }

        if(!Validation::maxLength($row['BP'], 100)) {
          $this->addError($errors, 'BP','差出人名は１００文字以内で設定してください');
        }

        if(empty($row['BJ']) && empty($row['BK']) && empty($row['BL']) && empty($row['BM']) && empty($row['BN'])) {
          $this->addError($errors, 'BJ','メールアドレスはいずれかの指定が必須です');
        }

        if(!empty($row['BJ']) && !Validation::email($row['BJ'])) {
          $this->addError($errors, 'BJ','メールアドレスのみ指定可能です');
        }

        if(!empty($row['BK']) && !Validation::email($row['BK'])) {
          $this->addError($errors, 'BK','メールアドレスのみ指定可能です');
        }

        if(!empty($row['BL']) && !Validation::email($row['BL'])) {
          $this->addError($errors, 'BL','メールアドレスのみ指定可能です');
        }

        if(!empty($row['BM']) && !Validation::email($row['BM'])) {
          $this->addError($errors, 'BM','メールアドレスのみ指定可能です');
        }

        if(!empty($row['BN']) && !Validation::email($row['BN'])) {
          $this->addError($errors, 'BN','メールアドレスのみ指定可能です');
        }
      }
    }

    // scenario
    if ($this->actionTypeMap[$row['BD']] == 2) {
      if(empty($row['BR'])) {
        $this->addError($errors, 'BR','自動で最大化する／自動で最大化しない のいずれかの指定のみ可能です');
      }

      if(empty($row['BR'])) {
        $this->addError($errors, 'BR','シナリオが未入力です');
      }
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

  /**
   * @param $cell
   * @return string
   */
  private function getCellValue($cell)
  {
    return !(empty(trim($cell))) ? trim($cell) : "";
  }
}