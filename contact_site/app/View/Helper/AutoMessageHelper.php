<?php
/**
 * Angular用
 * FormHelper拡張ヘルパー
 * htmlExHelper
 */
class autoMessageHelper extends AppHelper {

	/** *
	 * 要素を作成するためのリスト
	 *  */
	public $dataList = [
		'stayTimeCheckType' => [
			'label' => '種類',
			'dataList' => [
				2 => "サイト",
				1 => "ページ"
			]
		],
		'stayTimeType' => [
			'label' => '単位',
			'dataList' => [
				1 => "秒",
				2 => "分",
				3 => "時"
			]
		],
		'visitCntCond' => [
			'label' => '条件',
			'dataList' => [
				1 => "に一致する場合",
				2 => "以上の場合",
				3 => "未満の場合",
        4 => "以上"
			]
		],
		'targetName' => [
			'label' => '対象',
			'dataList' => [
        2 => "URL",
				1 => "タイトル"
			]
		],
		'stayPageCond' => [
			'label' => '条件',
			'dataList' => [
				1 => "完全一致",
				2 => "部分一致"
			]
		],
		'timeSetting' => [
			'label' => '時間指定',
			'dataList' => [
				1 => "する",
				2 => "しない"
			]
		],
		'referrerCond' => [
			'label' => '条件',
			'dataList' => [
				1 => "完全一致",
				2 => "部分一致"
			]
		],
		'searchCond' => [
			'label' => '条件',
			'dataList' => [
				1 => "完全一致",
				2 => "部分一致",
				3 => "不一致（若しくは取得できなかった場合）"
			]
		],
		'operatingHoursTime' => [
			'label' => '条件',
			'dataList' => [
				1 => "営業時間内",
				2 => "営業時間外"
			]
		],
    'speechContentCond' => [
      'label' => '条件',
        'dataList' => [
          1 => "完全一致",
          2 => "部分一致"
        ]
    ],
    'speechTriggerCond' => [
      'label' => '発動回数',
      'dataList' => [
        1 => "１回のみ有効",
        2 => "何度でも有効"
      ]
    ],
		'day' => [
			'label' => '曜日',
			'dataList' => [
				1 => "月",
				2 => "火",
				3 => "水",
				4 => "木",
				5 => "金",
				6 => "土",
				7 => "日"
			],
			'nameList' => [ // チェックボックスのみ必須(表示名として使用)
				1 => "mon",
				2 => "tue",
				3 => "wed",
				4 => "thu",
				5 => "fri",
				6 => "sat",
				7 => "sun"
			]
		],
	];

	public $containsList = [
    1 => "をすべて含む",
    2 => "のいずれかを含む"
  ];

  public $exclusionsList = [
      1 => "をすべて含む場合は対象外",
      2 => "のいずれかを含む場合は対象外"
  ];

	/** *
	 * 説明用のフォーマットリスト
	 *  */
	public $labelList = [
		C_AUTO_TRIGGER_STAY_TIME => "%s滞在時間が %d%s経過",
		C_AUTO_TRIGGER_VISIT_CNT => [
		  C_SINGLE => "訪問回数が %d回%s",
      C_MULTIPLE => "訪問回数が %d回以上%d回未満の場合",
    ],
		C_AUTO_TRIGGER_STAY_PAGE => "ページの%sにて「%s」という文字列を%s%s（内容は%s）",
		C_AUTO_TRIGGER_OPERATING_HOURS => "%s",
		C_AUTO_TRIGGER_DAY_TIME => [
			C_SELECT_CAN => "曜日が「%s」曜日で「%s～%s」の間",
			C_SELECT_CAN_NOT => "曜日が「%s」曜日",
		],
		C_AUTO_TRIGGER_REFERRER => "参照元URLにて「%s」という文字列%s%s（内容は%s）",
		C_AUTO_TRIGGER_SEARCH_KEY => "検索キーワードにて「%s」という文字列が%s",
    C_AUTO_TRIGGER_SPEECH_CONTENT => "発言内容が「%s」という文字列%s%s（内容は%s）",
    C_AUTO_TRIGGER_STAY_PAGE_OF_FIRST => "最初に訪れたページの%sにて「%s」という文字列%s%s（内容は%s）",
    C_AUTO_TRIGGER_STAY_PAGE_OF_PREVIOUS => "前のページの%sにて「%s」という文字列を%s%s（内容は%s）",
    C_AUTO_TRIGGER_VISITOR_DEVICE => "サイト訪問者の端末が「%s」"
  ];

	public function select($itemKey=null) {
		$returnTag = "";
		$labelTmp = "<span><label>%s</label></span>";
		$selectTagTmp = "<select name='%s' ng-model='setItem.%s'>%s</select>";
		$optionTagTmp = "<option value='%d'>%s</option>";
		if (!empty($this->dataList[$itemKey])) {
			$data = $this->dataList[$itemKey];
			$options = "";
			foreach($data['dataList'] as $key => $val){
				$options .= sprintf($optionTagTmp, $key, $val);
			}
			$selectTag = sprintf($selectTagTmp, $itemKey, $itemKey, $options);
			$returnTag = sprintf($labelTmp, $data['label']) . $selectTag;
		}
		return $returnTag;
	}

	public function radio($itemKey=null) {
		$returnTag = "";
		$labelTmp = "<span><label>%s</label></span>";
		$radioTmp = "<label class='pointer'><input type='radio' ng-model='setItem.%s' name='%s{{itemId}}_{{\$id}}' value='%d'>%s</label>&nbsp;";
		if (!empty($this->dataList[$itemKey])) {
			$data = $this->dataList[$itemKey];
			$radioTags = "";
			foreach($data['dataList'] as $key => $val){
				$radioTags .= sprintf($radioTmp, $itemKey, $itemKey, $key, $val);
			}
			$returnTag = sprintf($labelTmp, $data['label']) . "<radios>" . $radioTags . "</radios>";
		}
		return $returnTag;
	}

	public function checkbox($itemKey=null) {
		$returnTag = "";
		$labelTmp = "<span><label>%s</label></span>";
		$checkboxTmp = "<label class='pointer'><input type='checkbox' ng-model='setItem.%s.%s' name='%s' value='%d' %s>%s</label>";
		$requiled = "ng-required='main.requireCheckBox(setItem.%s)'";
		if (!empty($this->dataList[$itemKey])) {
			$data = $this->dataList[$itemKey];
			$checkboxTags = "";
			foreach($data['dataList'] as $key => $val){
				$required = (count($data['dataList']) === ($key)) ? sprintf($requiled, $itemKey) : "" ;
				$checkboxTags .= sprintf($checkboxTmp, $itemKey,  $data['nameList'][$key], $itemKey, $key, $required, $val);
			}
			$returnTag = sprintf($labelTmp, $data['label']) . "<boxes>" . $checkboxTags . "</boxes>";
		}
		return $returnTag;
	}


	public function setAutoMessage($list = []){
		//営業時間が4番目なので順番変更
		$changeEditData = $list;
		foreach($changeEditData as $key => $val){
			if($key >= 4 && $key < 10) {
				unset($changeEditData[$key]);
				$changeEditData[$key+1] = $list[$key];
			}
			if($key === 10) {
				unset($changeEditData[10]);
				$changeEditData[4] = $list[10];
			}
		}
		$list = $changeEditData;
		$retList = [];
		$dayList = $this->dataList['day']['nameList'];
		foreach( (array)$list as $itemId => $items ){
			switch ($itemId) {
				case C_AUTO_TRIGGER_STAY_TIME: // 滞在時間
					foreach((array)$items as $v) {
						if (isset($v['stayTimeRange']) && isset($v['stayTimeType']) && isset($v['stayTimeCheckType']) && !empty($this->dataList['stayTimeType']['dataList'][$v['stayTimeType']])) {
							$retList[] = sprintf(
								$this->labelList[$itemId],
								$this->dataList['stayTimeCheckType']['dataList'][$v['stayTimeCheckType']],
								$v['stayTimeRange'],
								$this->dataList['stayTimeType']['dataList'][$v['stayTimeType']]
							);
						}
					}
					break;

				case C_AUTO_TRIGGER_VISIT_CNT: // 訪問回数
					foreach((array)$items as $v) {
						if (isset($v['visitCnt']) && isset($v['visitCntCond']) && !empty($this->dataList['visitCntCond']['dataList'][$v['visitCntCond']])) {
              if ($v['visitCntCond'] == 4) {
                $retList[] = sprintf(
                  $this->labelList[$itemId][C_MULTIPLE], $v['visitCnt'], $v['visitCntMax']
                );
              } else {
                $retList[] = sprintf(
                  $this->labelList[$itemId][C_SINGLE], $v['visitCnt'],
                  $this->dataList['visitCntCond']['dataList'][$v['visitCntCond']]
                );
              }
						}
					}
					break;

				case C_AUTO_TRIGGER_STAY_PAGE: // ページ
					foreach((array)$items as $v) {
						if ((isset($v['keyword_contains']) || isset($v['keyword_exclusions']))
							&& isset($v['targetName']) && !empty($this->dataList['targetName']['dataList'][$v['targetName']])
							&& isset($v['stayPageCond']) && !empty($this->dataList['stayPageCond']['dataList'][$v['stayPageCond']])
						) { //ページの%sにて「%s」という文字列を%s%s（内容は%s）
              if(!empty($v['keyword_contains']) && !empty($v['keyword_exclusions'])) {
                $retList[] = sprintf(
                    $this->labelList[$itemId],
                    $this->dataList['targetName']['dataList'][$v['targetName']],
                    $v['keyword_contains'],
                    $this->containsList[intval($v['keyword_contains_type'])],
                    sprintf('、ただし「%s」%s', $v['keyword_exclusions'], $this->exclusionsList[intval($v['keyword_exclusions_type'])]),
                    $this->dataList['stayPageCond']['dataList'][$v['stayPageCond']]
                );
              } else if(!empty($v['keyword_contains'])) {
                $retList[] = sprintf(
                    $this->labelList[$itemId],
                    $this->dataList['targetName']['dataList'][$v['targetName']],
                    $v['keyword_contains'],
                    $this->containsList[intval($v['keyword_contains_type'])],
                    "",
                    $this->dataList['stayPageCond']['dataList'][$v['stayPageCond']]
                );
              } else if(!empty($v['keyword_exclusions'])) {
                $retList[] = sprintf(
                    $this->labelList[$itemId],
                    $this->dataList['targetName']['dataList'][$v['targetName']],
                    $v['keyword_exclusions'],
                    $this->exclusionsList[intval($v['keyword_exclusions_type'])],
                    "",
                    $this->dataList['stayPageCond']['dataList'][$v['stayPageCond']]
                );
              }
						}
					}
					break;

				case C_AUTO_TRIGGER_OPERATING_HOURS: // 営業時間
					foreach((array)$items as $v) {
						if (isset($v['operatingHoursTime']) && !empty($this->dataList['operatingHoursTime']['dataList'][$v['operatingHoursTime']])) {
							$retList[] = sprintf(
								$this->labelList[$itemId],
								$this->dataList['operatingHoursTime']['dataList'][$v['operatingHoursTime']]
							);
						}
					}
					break;

				case C_AUTO_TRIGGER_DAY_TIME: // 曜日・時間
					foreach((array)$items as $v) {
						if ( isset($v['day'])
							&& isset($v['timeSetting']) && !empty($this->dataList['timeSetting']['dataList'][$v['timeSetting']])
						) {
							$tmpDayList = [];
							// TODO より良いロジックに変更する
							$i = 1;
							foreach($v['day'] as $dayKey => $dayVal ){
								if ( intval($dayVal) === 1 ) {
									$tmpDayList[] = $this->dataList['day']['dataList'][$i];
								}
								$i++;
							}
							$dayList = implode($tmpDayList, ",");
							if ( intval($v['timeSetting']) === intval(C_SELECT_CAN)
								&& isset($v['startTime']) && isset($v['endTime'])
							) {
								$retList[] = sprintf(
									$this->labelList[$itemId][C_SELECT_CAN], $dayList, $v['startTime'], $v['endTime']
								);
							}
							else {
								$retList[] = sprintf(
									$this->labelList[$itemId][C_SELECT_CAN_NOT], $dayList
								);
							}
						}
					}
					break;

				case C_AUTO_TRIGGER_REFERRER: // 参照元URL（リファラー）
					foreach((array)$items as $v) {
						if ( (isset($v['keyword_contains']) || isset($v['keyword_exclusions']))
                && isset($v['referrerCond']) && !empty($this->dataList['referrerCond']['dataList'][$v['referrerCond']])
						) {
              if(!empty($v['keyword_contains']) && !empty($v['keyword_exclusions'])) {
                $retList[] = sprintf(
                    $this->labelList[$itemId],
                    $v['keyword_contains'],
                    $this->containsList[intval($v['keyword_contains_type'])],
                    sprintf('、ただし「%s」%s', $v['keyword_exclusions'], $this->exclusionsList[intval($v['keyword_exclusions_type'])]),
                    $this->dataList['referrerCond']['dataList'][$v['referrerCond']]
                );
              } else if(!empty($v['keyword_contains'])) {
                $retList[] = sprintf(
                    $this->labelList[$itemId],
                    $v['keyword_contains'],
                    $this->containsList[intval($v['keyword_contains_type'])],
                    "",
                    $this->dataList['referrerCond']['dataList'][$v['referrerCond']]
                );
              } else if(!empty($v['keyword_exclusions'])) {
                $retList[] = sprintf(
                    $this->labelList[$itemId],
                    $v['keyword_exclusions'],
                    $this->exclusionsList[intval($v['keyword_exclusions_type'])],
                    "",
                    $this->dataList['referrerCond']['dataList'][$v['referrerCond']]
                );
              }

						}
					}
					break;

				case C_AUTO_TRIGGER_SEARCH_KEY: // 検索キーワード
					foreach((array)$items as $v) {
						if ( isset($v['keyword'])
							&& isset($v['searchCond']) && !empty($this->dataList['searchCond']['dataList'][$v['searchCond']])
						) {
							$retList[] = sprintf(
								$this->labelList[$itemId],
								$v['keyword'],
								$this->dataList['searchCond']['dataList'][$v['searchCond']]
							);

						}
					}

					break;

        case C_AUTO_TRIGGER_SPEECH_CONTENT: // 発言内容
          foreach((array)$items as $v) {
            if ( (isset($v['keyword_contains']) || isset($v['keyword_exclusions']))
                && isset($v['speechContentCond']) && !empty($this->dataList['speechContentCond']['dataList'][$v['speechContentCond']])
            ) {
              if(!empty($v['keyword_contains']) && !empty($v['keyword_exclusions'])) {
                $retList[] = sprintf(
                    $this->labelList[$itemId],
                    $v['keyword_contains'],
                    $this->containsList[intval($v['keyword_contains_type'])],
                    sprintf('、ただし「%s」%s', $v['keyword_exclusions'], $this->exclusionsList[intval($v['keyword_exclusions_type'])]),
                    $this->dataList['speechContentCond']['dataList'][$v['speechContentCond']]
                );
              } else if(!empty($v['keyword_contains'])) {
                $retList[] = sprintf(
                    $this->labelList[$itemId],
                    $v['keyword_contains'],
                    $this->containsList[intval($v['keyword_contains_type'])],
                    "",
                    $this->dataList['speechContentCond']['dataList'][$v['speechContentCond']]
                );
              } else if(!empty($v['keyword_exclusions'])) {
                $retList[] = sprintf(
                    $this->labelList[$itemId],
                    $v['keyword_exclusions'],
                    $this->exclusionsList[intval($v['keyword_exclusions_type'])],
                    "",
                    $this->dataList['speechContentCond']['dataList'][$v['speechContentCond']]
                );
              }

            }
          }

          break;

        case C_AUTO_TRIGGER_STAY_PAGE_OF_FIRST: // 最初に訪れたページ
          foreach((array)$items as $v) {
            if ((isset($v['keyword_contains']) || isset($v['keyword_exclusions']))
                && isset($v['targetName']) && !empty($this->dataList['targetName']['dataList'][$v['targetName']])
                && isset($v['stayPageCond']) && !empty($this->dataList['stayPageCond']['dataList'][$v['stayPageCond']])
            ) {
              if(!empty($v['keyword_contains']) && !empty($v['keyword_exclusions'])) {
                $retList[] = sprintf(
                    $this->labelList[$itemId],
                    $this->dataList['targetName']['dataList'][$v['targetName']],
                    $v['keyword_contains'],
                    $this->containsList[intval($v['keyword_contains_type'])],
                    sprintf('、ただし「%s」%s', $v['keyword_exclusions'], $this->exclusionsList[intval($v['keyword_exclusions_type'])]),
                    $this->dataList['stayPageCond']['dataList'][$v['stayPageCond']]
                );
              } else if(!empty($v['keyword_contains'])) {
                $retList[] = sprintf(
                    $this->labelList[$itemId],
                    $this->dataList['targetName']['dataList'][$v['targetName']],
                    $v['keyword_contains'],
                    $this->containsList[intval($v['keyword_contains_type'])],
                    "",
                    $this->dataList['stayPageCond']['dataList'][$v['stayPageCond']]
                );
              } else if(!empty($v['keyword_exclusions'])) {
                $retList[] = sprintf(
                    $this->labelList[$itemId],
                    $this->dataList['targetName']['dataList'][$v['targetName']],
                    $v['keyword_exclusions'],
                    $this->exclusionsList[intval($v['keyword_exclusions_type'])],
                    "",
                    $this->dataList['stayPageCond']['dataList'][$v['stayPageCond']]
                );
              }
            }
          }
          break;

        case C_AUTO_TRIGGER_STAY_PAGE_OF_PREVIOUS: // 前のページ
          foreach((array)$items as $v) {
            if ((isset($v['keyword_contains']) || isset($v['keyword_exclusions']))
                && isset($v['targetName']) && !empty($this->dataList['targetName']['dataList'][$v['targetName']])
                && isset($v['stayPageCond']) && !empty($this->dataList['stayPageCond']['dataList'][$v['stayPageCond']])
            ) {
              if(!empty($v['keyword_contains']) && !empty($v['keyword_exclusions'])) {
                $retList[] = sprintf(
                    $this->labelList[$itemId],
                    $this->dataList['targetName']['dataList'][$v['targetName']],
                    $v['keyword_contains'],
                    $this->containsList[intval($v['keyword_contains_type'])],
                    sprintf('、ただし「%s」%s', $v['keyword_exclusions'], $this->exclusionsList[intval($v['keyword_exclusions_type'])]),
                    $this->dataList['stayPageCond']['dataList'][$v['stayPageCond']]
                );
              } else if(!empty($v['keyword_contains'])) {
                $retList[] = sprintf(
                    $this->labelList[$itemId],
                    $this->dataList['targetName']['dataList'][$v['targetName']],
                    $v['keyword_contains'],
                    $this->containsList[intval($v['keyword_contains_type'])],
                    "",
                    $this->dataList['stayPageCond']['dataList'][$v['stayPageCond']]
                );
              } else if(!empty($v['keyword_exclusions'])) {
                $retList[] = sprintf(
                    $this->labelList[$itemId],
                    $this->dataList['targetName']['dataList'][$v['targetName']],
                    $v['keyword_exclusions'],
                    $this->exclusionsList[intval($v['keyword_exclusions_type'])],
                    "",
                    $this->dataList['stayPageCond']['dataList'][$v['stayPageCond']]
                );
              }
            }
          }
          break;

        case C_AUTO_TRIGGER_VISITOR_DEVICE:
          foreach((array)$items as $v) {
            $deviceList = [];
            if ($v['pc']) $deviceList[] = 'PC';
            if ($v['smartphone']) $deviceList[] = 'スマートフォン';
            if ($v['tablet']) $deviceList[] = 'タブレット';
              $retList[] = sprintf($this->labelList[$itemId], implode($deviceList, ','));
          }
          break;

				default:
					# code...
					break;
			}
		}
		return $retList;
	}

	private function _setStayTimeTemp($items = []){
		$ret = [];

		return $ret;
	}

/*
		C_AUTO_TRIGGER_REFERRER => "参照元URLの%sにて「%s」という文字列が%s",
		C_AUTO_TRIGGER_SEARCH_KEY => "検索キーワードにて「%s」という文字列が%s",

*/

}
