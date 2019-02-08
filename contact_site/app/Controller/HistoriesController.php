<?php
App::uses('CustomerInformationUtil', 'Vendor/Util');

/**
 * HistoriesController controller.
 * 履歴一覧画面
 */
class HistoriesController extends AppController
{
  public $helpers = ['Time'];
  public $uses = [
    'MUser',
    'MCompany',
    'MCustomer',
    'TCustomerInformationSetting',
    'TCampaign',
    'THistory',
    'THistoryChatLog',
    'THistoryStayLog',
    'THistoryShareDisplay',
    'MLandscapeData',
    'THistoryLinkCountLog'
  ];
  public $components = ['LandscapeLbcAPI'];
  public $paginate = [
    'THistory' => [
      'limit' => 100,
      'order' => [
        'THistory.access_date' => 'desc',
        'THistory.id' => 'desc'
      ],
      'fields' => [
        'THistory.*'
      ],
      'conditions' => []
    ],
    'THistoryStayLog' => []
  ];

  const LABEL_AUTO_SPEECH_OPERATOR = '＊自動返信対応';

  public function beforeFilter()
  {
    parent::beforeFilter();
    $ret = $this->MCompany->read(null, $this->userInfo['MCompany']['id']);
    $this->paginate['THistory']['conditions'] = [
      'THistory.m_companies_id' => $this->userInfo['MCompany']['id']
    ];

    $this->set('siteKey', $this->userInfo['MCompany']['company_key']);
    if (isset($this->coreSettings[C_COMPANY_REF_COMPANY_DATA]) && $this->coreSettings[C_COMPANY_REF_COMPANY_DATA]) {
      $this->set('token', $this->userInfo['accessToken']);
    } else {
      $this->set('token', '');
    }
    $this->set('title_for_layout', '履歴');
  }

  /* *
   * 一覧画面
   * @return void
   * */
  public function index()
  {
    $isChat = 'true';
    if (!empty($this->params->query['isChat'])) {
      $this->Session->write('authenticity', $this->params->query['isChat']);
    }
    $isChat = $this->Session->read('authenticity');
    $this->_searchProcessing(3);
    // 成果の名称リスト
    $this->set('achievementType', Configure::read('achievementTypeForSearch'));
    if (!$this->request->is('ajax')) {
      $this->_setList($isChat);
    }
  }

  public function remoteGetCustomerInfo()
  {
    Configure::write('debug', 0);
    $this->autoRender = false;
    $this->layout = 'ajax';
    $data = [];

    if (!empty($this->params->query['historyId'])) {
      $params = [
        'fields' => '*',
        'conditions' => [
          'id' => $this->params->query['historyId'],
          'm_companies_id' => $this->userInfo['MCompany']['id']
        ]
      ];
      $tHistoryData = $this->THistory->find('first', $params);

      $params = [
        'fields' => [
          'count(*) as cnt'
        ],
        'conditions' => [
          'visitors_id = ' . $tHistoryData['THistory']['visitors_id'],
          'm_companies_id' => $this->userInfo['MCompany']['id'],
          'id <= ' . $tHistoryData['THistory']['id']
        ]
      ];
      $tHistoryCountData = $this->THistory->find('first', $params);

      $mCusData = ['MCustomer' => []];
      if (!empty($tHistoryData['THistory']['visitors_id'])) {
        $mCusData = $this->MCustomer->getCustomerInfoForVisitorId($this->userInfo['MCompany']['id'],
          $tHistoryData['THistory']['visitors_id']);
      }

      $data = am($tHistoryData, ['THistoryCount' => $tHistoryCountData[0]], $mCusData);
    }
    $this->set('data', $data);
    // 顧客情報のテンプレート
    $this->set('infoList', $this->_getInfomationList());
    return $this->render('/Elements/Histories/remoteGetCustomerInfo');
  }

  public function remoteSaveCustomerInfo()
  {
    Configure::write('debug', 0);
    $this->autoRender = false;
    $this->layout = 'ajax';
    $ret = true;
    $data = $this->params->query;
    if (!isset($data['visitorsId'])) {
      return false;
    }

    $inputData = []; // 顧客情報リスト
    $saveData = [
      'MCustomer' => [
        'visitors_id' => $data['visitorsId'],
        'm_companies_id' => $this->userInfo['MCompany']['id']
      ]
    ]; // 保存データ

    if (isset($data['customerId'])) {
      $saveData['MCustomer']['id'] = $data['customerId'];
    } else {
      $this->MCustomer->create();
    }

    // 顧客情報のテンプレートを取得
    $infoList = $this->_getInfomationList();
    foreach ($infoList as $key => $val) {
      $inputData[$key] = (isset($data['saveData'][$key])) ? $data['saveData'][$key] : "";
    }

    $saveData['MCustomer']['informations'] = $this->jsonEncode($inputData);

    $this->MCustomer->begin();
    $this->MCustomer->set($saveData);

    if ($this->MCustomer->save()) {
      $this->MCustomer->commit();
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
    } else {
      $this->MCustomer->rollback();
      $ret = false;
      $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
    }
    return new CakeResponse(['body' => json_encode($ret)]);
  }

  public function remoteGetChatLogs()
  {
    Configure::write('debug', 0);
    $this->autoRender = false;
    $this->layout = 'ajax';

    $ret = $this->_getChatLog($this->params->query['historyId']);
    $permissionLevel = $this->userInfo['permission_level'];
    $this->set('THistoryChatLog', $ret);
    $this->set('permissionLevel', $permissionLevel);
    return $this->render('/Elements/Histories/remoteGetChatLogs');
  }

  public function remoteGetStayLogs()
  {
    Configure::write('debug', 0);
    $this->autoRender = false;
    $this->layout = 'ajax';

    $historyId = $this->params->query['historyId'];

    $params = [
      'fields' => '*',
      'conditions' => [
        'THistoryStayLog.t_histories_id' => $historyId
      ],
      'recursive' => -1
    ];
    $ret = $this->THistoryStayLog->find('all', $params);
    $this->set('THistoryStayLog', $ret);
    /* 除外情報取得 */
    $this->set('excludeList', $this->MCompany->getExcludeList($this->userInfo['MCompany']['id']));
    return $this->render('/Elements/Histories/remoteGetStayLogs');
  }

  /**
   * 滞在時間を計算する
   * @param $startDateTime　THistoryのaccess_date
   * @param $endDateTime THistoryのout_date
   * @return out_dateからaccess_dateを引いた滞在時間
   * */
  public function calcTime($startDateTime, $endDateTime)
  {
    Configure::write('debug', 0);
    if (empty($startDateTime) || empty($endDateTime) || strtotime($startDateTime) > strtotime($endDateTime)) {
      return "-";
    }
    $start = new DateTime($startDateTime);
    $end = new DateTime($endDateTime);
    $diff = $start->diff($end);
    return $diff->format('%H:%I:%S');
  }

  /**
   * 指定されたパラメータを除外する
   * @param $excludes array パラメーターリスト
   * @param $url url URL
   * @return 加工後URL
   * */
  public function trimToURL($excludes, $url)
  {
    if (empty($excludes)) {
      return $url;
    }
    $elements = parse_url($url);
    if (!isset($elements['query'])) {
      return $url;
    }
    $params = $this->request->params;
    parse_str($elements['query'], $params);
    $elements['query'] = "";
    foreach (array_diff_key($params, array_flip($excludes)) as $key => $val) {
      $elements['query'] .= ($elements['query'] !== "") ? "&" : "";
      $elements['query'] .= (isset($val) && $val !== "") ? $key . "=" . $val : $key;
    }
    return $this->build_url($elements);
  }

  /**
   * parseしたURLを元に戻す
   * @param $elements array parse_urlの結果配列
   * @return URL
   * */
  public function build_url(array $elements)
  {
    $e = $elements;
    return
      (isset($e['host']) ? (
        (isset($e['scheme']) ? "$e[scheme]://" : '//') .
        (isset($e['user']) ? $e['user'] . (isset($e['pass']) ? ":$e[pass]" : '') . '@' : '') .
        $e['host'] .
        (isset($e['port']) ? ":$e[port]" : '')
      ) : '') .
      (isset($e['path']) ? $e['path'] : '/') .
      (isset($e['query']) ? (
      is_array($e['query']) ?
        '?' . http_build_query($e['query'], '', '&') :
        (($e['query'] !== "") ? '?' . $e['query'] : '')
      ) : '') .
      (isset($e['fragment']) ? "#$e[fragment]" : '');
  }

  public function outputCSVOfHistory()
  {
    Configure::write('debug', 0);
    ini_set("max_execution_time", "1200");
    ini_set('memory_limit', '-1'); // 無制限
    $name = "sinclo-history";

    if (isset($this->coreSettings[C_COMPANY_USE_HISTORY_EXPORTING]) && $this->coreSettings[C_COMPANY_USE_HISTORY_EXPORTING]) {
      $customerSettingList = $this->TCustomerInformationSetting->find('all', array(
        'conditions' => array(
          'delete_flg' => 0,
          'm_companies_id' => $this->userInfo['MCompany']['id']
        ),
        'order' => array(
          'sort' => 'asc'
        )
      ));
      $customerInformationSettingList = [];
      foreach ($customerSettingList as $k => $v) {
        array_push($customerInformationSettingList, $v['TCustomerInformationSetting']);
      }

      $customerInfoDisplaySettingMap = array();
      foreach ($customerSettingList as $index => $customerData) {
        $customerInfoDisplaySettingMap[$customerData['TCustomerInformationSetting']['item_name']] = (boolean)$customerData['TCustomerInformationSetting']['show_realtime_monitor_flg'];
      }
      //$returnData:$historyListで使うjoinのリストとconditionsの検索条件
      $this->printProcessTimetoLog('BEGIN _searchConditions');
      $returnData = $this->_searchConditions();
      $this->printProcessTimetoLog('BEGIN $this->THistory->find');
      $historyList = $this->THistory->find('all', [
        'order' => [
          'THistory.access_date' => 'desc',
          'THistory.id' => 'desc'
        ],
        'group' => [
          'THistory.id'
        ],
        'fields' => [
          '*'
        ],
        'joins' => array_merge(array('force index (t_histories_m_companies_id_access_date_id_index)'),
          $returnData['joinList']),
        'conditions' => $returnData['conditions']
      ]);
      $this->log($this->THistory->getDataSource()->getLog(), LOG_DEBUG);
      //$historyListに担当者を追加
      $this->printProcessTimetoLog('BEGIN $this->_userList($historyList)');
      $userList = $this->_userList($historyList);
      //THistoryChatLogの「firstURL」と「count」をと取ってくる
      $this->printProcessTimetoLog('BEGIN $this->_stayList($userList)');
      $stayList = $this->_stayList($userList);
      //最終発言時間を取得
      $this->printProcessTimetoLog('BEGIN $this->_lastSpeechTimeList($historyList)');
      $lastSpeechList = $this->_lastSpeechTimeList($historyList);

      // ヘッダー
      $csv[] = [
        "日時",
        "IPアドレス",
        "訪問ユーザ",
        "プラットフォーム",
        "ブラウザ",
        "キャンペーン",
        "流入ページタイトル",
        "閲覧ページ数",
        "参照元URL",
        "滞在時間"
      ];

      if ($this->coreSettings[C_COMPANY_USE_CHAT]) {
        $csv[0][] = "最終発言後離脱時間";
        $csv[0][] = "成果";
        $csv[0][] = "チャット担当者";
      }

      //除外パラメーターリスト
      $excludeList = $this->MCompany->getExcludeList($this->userInfo['MCompany']['id']);

      $campaignList = $this->TCampaign->getList();
      foreach ($userList as $key => $history) {
        $campaignParam = "";
        if (array_key_exists($history['THistory']['id'], $stayList)) {
          $tmp = mb_strstr($stayList[$history['THistory']['id']]['THistoryStayLog']['firstURL'], '?');
          if ($tmp !== "") {
            foreach ($campaignList as $k => $v) {
              if (strpos($tmp, $k) !== false) {
                if ($campaignParam !== "") {
                  $campaignParam .= "\n";
                }
                $campaignParam .= $v;
              }
            }
          }
        }
        $row = [];
        // 日時
        $dateTime = date_format(date_create($history['THistory']['access_date']), "Y/m/d\nH:i:s");
        $row['date'] = $dateTime;
        // IPアドレス
        if ($history['THistory']['ip_address'] !== "") {
          if (empty($row['ip'])) {
            $row['ip'] = "";
          }
          if ($row['ip'] !== "") {
            $row['ip'] .= "\n";
          }
          if ((isset($this->coreSettings[C_COMPANY_REF_COMPANY_DATA])
              && $this->coreSettings[C_COMPANY_REF_COMPANY_DATA])
            && !empty($history['LandscapeData']['org_name'])
            && ($this->isViewableMLCompanyInfo() || !LandscapeLbcAPIComponent::isMLLbcCode($history['LandscapeData']['lbc_code']))
          ) {
            $row['ip'] .= $history['LandscapeData']['org_name'];
          } else {
            $row['ip'] .= $history['THistory']['ip_address'];
          }
        }
        // 訪問ユーザ
        $row['customer'] = "";
        if (!empty($history['MCustomer']['informations'])) {
          $informations = CustomerInformationUtil::convertOldIFData((array)json_decode($history['MCustomer']['informations']));
          foreach ($customerInfoDisplaySettingMap as $k => $v) {
            if ($v && array_key_exists($k, $informations)) {
              $row['customer'] .= $informations[$k] . "\n";
            }
          }
        }

        // OS
        $row['os'] = $this->_userAgentCheckOs($history);
        // ブラウザ
        $row['browser'] = $this->_userAgentCheckBrowser($history);
        //キャンペーン
        $row['campaign'] = $campaignParam;
        //ランディングページ
        $row['landing'] = array_key_exists($history['THistory']['id'],
          $stayList) ? $stayList[$history['THistory']['id']]['THistoryStayLog']['title'] : "";
        // 閲覧ページ数
        $row['pageCnt'] = array_key_exists($history['THistory']['id'],
          $stayList) ? $stayList[$history['THistory']['id']]['THistoryStayLog']['count'] : "";
        // 参照元URL
        $params = $excludeList['params'];
        $row['referrer'] = $this->trimToURL($params, $history['THistory']['referrer_url']);
        // 滞在時間
        $row['visitTime'] = $this->calcTime($history['THistory']['access_date'],
          $history['THistory']['out_date']);
        if ($this->coreSettings[C_COMPANY_USE_CHAT]) {
          // 最終発言
          $row['lastSpeechTime'] = $this->calcTime(!empty($lastSpeechList[$history['THistory']['id']]) ? $lastSpeechList[$history['THistory']['id']] : "",
            $history['THistory']['out_date']);
          // 成果
          $row['achievement'] = "";
          if (!empty($history['THistoryChatLog2']['eff']) && $history['THistoryChatLog2']['eff'] != 0) {
            $row['achievement'] = Configure::read('achievementTypeForSearch')[2];
          } else {
            if (!empty($history['THistoryChatLog2']['deny']) && $history['THistoryChatLog2']['deny'] != 0) {
              $row['achievement'] = Configure::read('achievementTypeForSearch')[1];
            }
          }
          if (isset($history['THistoryChatLog2']['terminate']) && $history['THistoryChatLog2']['terminate'] != 0 && $history['THistoryChatLog2']['cv'] == 0) {
            $row['achievement'] = Configure::read('achievementTypeForSearch')[3];
          } else {
            if ($history['THistoryChatLog2']['cv'] != 0) {
              $row['achievement'] .= Configure::read('achievementTypeForSearch')[0];
            }
          }
          //　担当者
          $row['user'] = $history['User'];
        }

        $csv[] = $row;
      }
      $this->_outputCSV($name, $csv);
      $this->printProcessTimetoLog('END   outputCSVOfHistory');
    }
  }

  public function outputCSVOfChatHistory()
  {
    Configure::write('debug', 0);
    ini_set("max_execution_time", "1200");
    ini_set('memory_limit', '-1'); // 無制限

    if (isset($this->coreSettings[C_COMPANY_USE_HISTORY_EXPORTING]) && $this->coreSettings[C_COMPANY_USE_HISTORY_EXPORTING]) {
      $customerSettingList = $this->TCustomerInformationSetting->find('all', array(
        'conditions' => array(
          'delete_flg' => 0,
          'm_companies_id' => $this->userInfo['MCompany']['id']
        ),
        'order' => array(
          'sort' => 'asc'
        )
      ));
      $customerInformationSettingList = [];
      foreach ($customerSettingList as $k => $v) {
        array_push($customerInformationSettingList, $v['TCustomerInformationSetting']);
      }

      $customerInfoDisplaySettingMap = array();
      foreach ($customerSettingList as $index => $customerData) {
        $customerInfoDisplaySettingMap[$customerData['TCustomerInformationSetting']['item_name']] = (boolean)$customerData['TCustomerInformationSetting']['show_realtime_monitor_flg'];
      }
      //$returnData:$historyListで使うjoinのリストとconditionsの検索条件
      $returnData = $this->_searchConditions();
      //$returnData:チャット履歴CSV出力に必要なTHistoryChatLog、MUser、THistoryStayLogとjoinする
      $returnData = $this->_searchConditionsChat($returnData);

      $historyList = $this->THistory->find('all', [
        'fields' => '*',
        'joins' => $returnData['joinList'],
        'conditions' => $returnData['conditions'],
        'group' => ['THistoryChatLog.id'],
        'order' => [
          'THistory.access_date' => 'desc',
          'THistory.id' => 'desc',
          'THistoryChatLog.created'
        ]
      ]);

      //$historyListに担当者を追加
      $userList = $this->_userList($historyList);

      $name = "sinclo-chat-history";

      // ヘッダー
      $csv[] = [
        "訪問日時",
        "IPアドレス",
        "訪問ユーザ",
        "プラットフォーム",
        "ブラウザ",
        "送信元ページ",
        "送信日時",
        "送信種別",
        "送信者",
        "メッセージ",
        "担当者"
      ];
      foreach ($userList as $val) {
        $outputTarget = true;
        $row = [];
        // 日時
        $dateTime = $val['THistory']['access_date'];
        $row['date'] = $dateTime;
        //IPアドレス
        if ($val['THistory']['ip_address'] !== "") {
          if (empty($row['ip'])) {
            $row['ip'] = "";
          }
          if ($row['ip'] !== "") {
            $row['ip'] .= "\n";
          }
          if ((isset($this->coreSettings[C_COMPANY_REF_COMPANY_DATA])
              && $this->coreSettings[C_COMPANY_REF_COMPANY_DATA])
            && !empty($val['LandscapeData']['org_name'])
            && ($this->isViewableMLCompanyInfo() || !LandscapeLbcAPIComponent::isMLLbcCode($val['LandscapeData']['lbc_code']))) {
            $row['ip'] .= $val['LandscapeData']['org_name'];
          } else {
            $row['ip'] .= $val['THistory']['ip_address'];
          }
        }
        //訪問ユーザ
        $row['customer'] = "";
        if (!empty($val['MCustomer']['informations'])) {
          $informations = CustomerInformationUtil::convertOldIFData((array)json_decode($val['MCustomer']['informations']));
          foreach ($customerInfoDisplaySettingMap as $k => $v) {
            if ($v && array_key_exists($k, $informations)) {
              $row['customer'] .= $informations[$k] . "\n";
            }
          }
        }
        // OS
        $row['os'] = $this->_userAgentCheckOs($val);
        //ブラウザ
        $row['browser'] = $this->_userAgentCheckBrowser($val);
        //送信元ページ
        if ($val['THistoryChatLog']['message_type'] == 1) {
          $row['sourcePage'] = $val['THistoryStayLog']['url'];
        } else {
          $row['sourcePage'] = '';
        }
        // 送信日時
        $row['pageCnt'] = substr(preg_replace("/[\n,]+/", " ", $val['THistoryChatLog']['created']), 0, 20);

        // 送信種別
        if ($val['THistoryChatLog']['message_type'] == 1) {
          $row['transmissionKind'] = '訪問者';
          $row['transmissionPerson'] = '';
        }
        if ($val['THistoryChatLog']['message_type'] == 2) {
          $row['transmissionKind'] = 'オペレーター';
          $row['transmissionPerson'] = $val['MUser']['display_name'] . "さん";
        }
        if ($val['THistoryChatLog']['message_type'] == 3) {
          $row['transmissionKind'] = 'オートメッセージ';
          $row['transmissionPerson'] = $this->userInfo['MCompany']['company_name'];
        }
        if ($val['THistoryChatLog']['message_type'] == 4) {
          $row['transmissionKind'] = 'Sorryメッセージ';
          $row['transmissionPerson'] = $this->userInfo['MCompany']['company_name'];
        }
        if ($val['THistoryChatLog']['message_type'] == 5) {
          $row['transmissionKind'] = '自動返信';
          $row['transmissionPerson'] = $this->userInfo['MCompany']['company_name'];
        }
        if ($val['THistoryChatLog']['message_type'] == 6) {
          $row['transmissionKind'] = 'ファイル送信';
          $row['transmissionPerson'] = $val['MUser']['display_name'];
          $json = json_decode($val['THistoryChatLog']['message'], true);
          $val['THistoryChatLog']['message'] = $json['fileName'] . "\n" . $this->prettyByte2Str($json['fileSize']);
        }
        if ($val['THistoryChatLog']['message_type'] == 8) {
          $row['transmissionKind'] = '訪問者';
          $row['transmissionPerson'] = '';
          $val['THistoryChatLog']['message'] = "（「" . $val['THistoryChatLog']['message'] . "」をクリック）";
        }
        if ($val['THistoryChatLog']['message_type'] == 12 || $val['THistoryChatLog']['message_type'] == 33
          || $val['THistoryChatLog']['message_type'] == 34 || $val['THistoryChatLog']['message_type'] == 35
          || $val['THistoryChatLog']['message_type'] == 43 || $val['THistoryChatLog']['message_type'] == 47) {
          $row['transmissionKind'] = '訪問者（ヒアリング回答）';
          $row['transmissionPerson'] = '';
        }
        if ($val['THistoryChatLog']['message_type'] == 13) {
          $row['transmissionKind'] = '訪問者（選択肢回答）';
          $row['transmissionPerson'] = '';
        }
        if ($val['THistoryChatLog']['message_type'] == 19) {
          $row['transmissionKind'] = 'シナリオメッセージ（ファイル受信）';
          $row['transmissionPerson'] = "";
          $json = json_decode($val['THistoryChatLog']['message'], true);
          if (isset($json['downloadUrl']) && isset($json['comment'])) {
            $val['THistoryChatLog']['message'] = "＜コメント＞" . "\n" . $json['comment'] . "\n" . $json['downloadUrl'];
          } else {
            if (isset($json['canceled']) && isset($json['message']) && $json['canceled']) {
              $val['THistoryChatLog']['message'] = "（" . $json['message'] . "）";
            }
          }
        }
        if ($val['THistoryChatLog']['message_type'] == 21) {
          $row['transmissionKind'] = 'シナリオメッセージ（テキスト発言）';
          $row['transmissionPerson'] = $this->userInfo['MCompany']['company_name'];
        }
        if ($val['THistoryChatLog']['message_type'] == 22) {
          $row['transmissionKind'] = 'シナリオメッセージ（ヒアリング）';
          $row['transmissionPerson'] = $this->userInfo['MCompany']['company_name'];
          // replace [*] by [] radio selected
          $val['THistoryChatLog']['message'] = str_replace('[*]', '[]', $val['THistoryChatLog']['message']);
        }
        if ($val['THistoryChatLog']['message_type'] == 23) {
          $row['transmissionKind'] = 'シナリオメッセージ（選択肢）';
          $row['transmissionPerson'] = $this->userInfo['MCompany']['company_name'];
        }
        if ($val['THistoryChatLog']['message_type'] == 27) {
          $row['transmissionKind'] = 'シナリオメッセージ（ファイル送信）';
          $row['transmissionPerson'] = "";
          $json = json_decode($val['THistoryChatLog']['message'], true);
          $val['THistoryChatLog']['message'] = $json['fileName'] . "\n" . $this->prettyByte2Str($json['fileSize']);
        }
        if ($val['THistoryChatLog']['message_type'] == 29) {
          $row['transmissionKind'] = 'シナリオメッセージ（ファイル受信）';
          $row['transmissionPerson'] = "";
          $json = json_decode($val['THistoryChatLog']['message'], true);
          $val['THistoryChatLog']['message'] = "＜コメント＞" . "\n" . $json['comment'] . "\n" . $json['downloadUrl'];
        }
        if ($val['THistoryChatLog']['message_type'] == 30) {
          $row['transmissionKind'] = 'シナリオメッセージ（一括ヒアリング回答）';
          $row['transmissionPerson'] = "";
        }
        if ($val['THistoryChatLog']['message_type'] == 31) {
          // 一括ヒアリング内容（未修正OK）は管理画面側で表示しない
          $row['transmissionKind'] = 'シナリオメッセージ（一括ヒアリング解析結果未修正）';
          $row['transmissionPerson'] = "";
          $outputTarget = false;
        }
        if ($val['THistoryChatLog']['message_type'] == 32) {
          $row['transmissionKind'] = 'シナリオメッセージ（一括ヒアリング内容修正）';
          $row['transmissionPerson'] = "";
          $json = json_decode($val['THistoryChatLog']['message'], true);
          $val['THistoryChatLog']['message'] = "";
          foreach ($json as $variableName => $object) {
            $val['THistoryChatLog']['message'] .= $object['label'] . '：' . ($object['value']) . "\n";
          }
        }
        if ($val['THistoryChatLog']['message_type'] == 36 || $val['THistoryChatLog']['message_type'] == 37
          || $val['THistoryChatLog']['message_type'] == 38 || $val['THistoryChatLog']['message_type'] == 39
          || $val['THistoryChatLog']['message_type'] == 44 || $val['THistoryChatLog']['message_type'] == 48) {
          $row['transmissionKind'] = 'シナリオメッセージ（ヒアリング再回答）';
          $row['transmissionPerson'] = '';
        }
        if ($val['THistoryChatLog']['message_type'] == 40) {
          $row['transmissionKind'] = 'シナリオメッセージ（一括ヒアリング解析結果）';
          $row['transmissionPerson'] = $this->userInfo['MCompany']['company_name'];
          $json = json_decode($val['THistoryChatLog']['message'], true);
          $val['THistoryChatLog']['message'] = "";
          foreach ($json['target'] as $index => $object) {
            $val['THistoryChatLog']['message'] .= $object['label'] . '：' . ((!empty($json['message'][$object['inputType']])) ? $json['message'][$object['inputType']] : "（なし）") . "\n";
          }
        }
        if ($val['THistoryChatLog']['message_type'] == 41 || $val['THistoryChatLog']['message_type'] == 42
          || $val['THistoryChatLog']['message_type'] == 45 || $val['THistoryChatLog']['message_type'] == 46) {
          $row['transmissionKind'] = 'シナリオメッセージ（ヒアリング）';
          $row['transmissionPerson'] = $this->userInfo['MCompany']['company_name'];
          $json = json_decode($val['THistoryChatLog']['message']);
          if (!$json->message) {
            switch ($val['THistoryChatLog']['message_type']) {
              case 41:
                $val['THistoryChatLog']['message'] = '（プルダウン質問内容なし）';
                break;
              case 42:
                $val['THistoryChatLog']['message'] = '（カレンダー質問内容なし）';
                break;
              case 45:
                $val['THistoryChatLog']['message'] = '（カルーセル質問内容なし）';
                break;
              default:
                $val['THistoryChatLog']['message'] = '（質問内容なし）';
                break;
            }
          } else {
            $val['THistoryChatLog']['message'] = $json->message;
          }
        }
        if ($val['THistoryChatLog']['message_type'] == 81) {
          $row['transmissionKind'] = 'チャットボットメッセージ';
          $row['transmissionPerson'] = $this->userInfo['MCompany']['company_name'];
        }
        if ($val['THistoryChatLog']['message_type'] == 82) {
          $row['transmissionKind'] = 'チャットボットメッセージ';
          $row['transmissionPerson'] = $this->userInfo['MCompany']['company_name'];
        }
        if ($val['THistoryChatLog']['message_type'] == 90) {
          // 何も表示しない
          continue;
        }
        if ($val['THistoryChatLog']['message_type'] == 998 || $val['THistoryChatLog']['message_type'] == 999) {
          $row['transmissionKind'] = '通知メッセージ';
          $row['transmissionPerson'] = "";
          $val['THistoryChatLog']['message'] = '-' . $val['MUser']['display_name'] . 'が' . $val['THistoryChatLog']['message'] . 'しました-';
        }
        // チャットメッセージ
        if ($val['THistoryChatLog']['delete_flg'] == 1) {
          $row['message'] = "(このメッセージは " . $val['THistoryChatLog']['deleted'] . " に " . $val['DeleteMUser']['display_name'] . " さんによって削除されました。)";
        } else {
          $row['message'] = $val['THistoryChatLog']['message'];
        }
        // チャット担当者
        if ($val['THistoryChatLog']['message_type'] == 2 || $val['THistoryChatLog']['message_type'] == 6) {
          $row['user'] = $val['User'];
        }
        $csv[] = $row;
      }
      $this->_outputCSV($name, $csv);
    }
  }

  /**
   * //
   *  csvチャット履歴出力、joinのテーブル追加
   * @param  検索条件
   * @return 検索条件にチャット履歴出力のために必要なテーブルを追加
   * */
  private function _searchConditionsChat($returnData)
  {
    //message,messagetypeを使うためTHistoryChatLogとjoin
    $returnData['joinList'][] = [
      'type' => 'LEFT',
      'table' => "(SELECT * FROM t_history_chat_logs where m_companies_id = " . $this->userInfo['m_companies_id'] . " ORDER BY t_histories_id, created )",
      'alias' => 'THistoryChatLog',
      'conditions' => [
        'THistoryChatLog.t_histories_id = THistory.id'
      ]
    ];
    //display_nameを使うためMUserとjoin
    $returnData['joinList'][] = [
      'type' => 'LEFT',
      'table' => 'm_users',
      'alias' => 'MUser',
      'conditions' => [
        'THistoryChatLog.m_users_id = MUser.id',
        'MUser.m_companies_id' => $this->userInfo['MCompany']['id']
      ]
    ];
    //url（送信元ページ)を使うためTHistoryStayLogとjoin
    $returnData['joinList'][] = [
      'type' => 'LEFT',
      'table' => 't_history_stay_logs',
      'alias' => 'THistoryStayLog',
      'conditions' => [
        'THistoryChatLog.t_history_stay_logs_id = THistoryStayLog.id'
      ],
    ];
    $returnData['joinList'][] = [
      'type' => 'LEFT',
      'table' => 'm_users',
      'alias' => 'DeleteMUser',
      'conditions' => [
        'THistoryChatLog.deleted_user_id = DeleteMUser.id',
        'DeleteMUser.m_companies_id' => $this->userInfo['MCompany']['id'],
        'THistoryChatLog.delete_flg' => 1,
      ]
    ];
    return $returnData;
  }

  public function outputCSVOfChat($id = null)
  {
    Configure::write('debug', 0);

    if (empty($id)) {
      return false;
    }
    $name = "sinclo-chat-history";
    $ret = $this->_getChatLog($id);

    // ヘッダー
    $csv[] = [
      "送信日時",
      "送信種別",
      "送信者",
      "メッセージ"
    ];

    foreach ($ret as $val) {
      $row = [];
      $date = date('Y/m/d H:i:s', strtotime($val['THistoryChatLog']['created'])); // 送信日時
      if ($val['THistoryChatLog']['delete_flg'] == 1) {
        $message = "(このメッセージは " . $val['THistoryChatLog']['deleted'] . " に " . $val['DeleteMUser']['display_name'] . " さんによって削除されました。)";
      } else {
        if ($val['THistoryChatLog']['message_type'] == 41 || $val['THistoryChatLog']['message_type'] == 42) {
          $message = json_decode($val['THistoryChatLog']['message'], true)['message'];
        } else {
          $message = $val['THistoryChatLog']['message'];
        }
      }
      switch ($val['THistoryChatLog']['message_type']) {
        case 1: // 企業側からの送信
          $row = $this->_setData($date, "訪問者", "", $message);
          break;
        case 2: // 訪問者側からの送信
          $row = $this->_setData($date, "オペレーター", $val['MUser']['display_name'], $message);
          break;
        case 3: // オートメッセージ
          $row = $this->_setData($date, "オートメッセージ", $this->userInfo['MCompany']['company_name'], $message);
          break;
        case 4: //sorryメッセージ
          $row = $this->_setData($date, "Sorryメッセージ", $this->userInfo['MCompany']['company_name'], $message);
          break;
        case 5: // 自動返信
          $row = $this->_setData($date, "自動返信", $this->userInfo['MCompany']['company_name'], $message);
          break;
        case 6: // ファイル送信
          $json = json_decode($val['THistoryChatLog']['message'], true);
          if ($val['THistoryChatLog']['delete_flg'] != 1) {
            $message = $json['fileName'] . "\n" . $this->prettyByte2Str($json['fileSize']);
          }
          $row = $this->_setData($date, "ファイル送信", $val['MUser']['display_name'], $message);
          break;
        case 8: // リンククリック
          $row = $this->_setData($date, "訪問者", "", "（「" . $message . "」をクリック）");
          break;
        case 12: // 訪問者（シナリオ：ヒアリング回答）
        case 33: // 訪問者（シナリオ：ヒアリング回答）radio
        case 34: // 訪問者（シナリオ：ヒアリング回答）pulldown
        case 35: // 訪問者（シナリオ：ヒアリング回答）calendar
        case 47:
          $row = $this->_setData($date, "訪問者（ヒアリング回答）", "", $message);
          break;
        case 13: // 訪問者（シナリオ：選択肢回答）
          $row = $this->_setData($date, "訪問者（選択肢回答）", "", $message);
          break;
        case 21: // シナリオメッセージ（テキスト発言）
          $row = $this->_setData($date, "シナリオメッセージ（テキスト発言）", $this->userInfo['MCompany']['company_name'],
            $message);
          break;
        case 22: // シナリオメッセージ（ヒアリング）
          $message = str_replace('[*]', '[]', $message);
          $row = $this->_setData($date, "シナリオメッセージ（ヒアリング）", $this->userInfo['MCompany']['company_name'],
            $message);
          break;
        case 23: // シナリオメッセージ（選択肢）
          $row = $this->_setData($date, "シナリオメッセージ（選択肢）", $this->userInfo['MCompany']['company_name'],
            $message);
          break;
        case 36: // 訪問者（シナリオ：ヒアリング再回答）text
        case 37: // 訪問者（シナリオ：ヒアリング再回答）radio
        case 38: // 訪問者（シナリオ：ヒアリング再回答）pulldown
        case 39: // 訪問者（シナリオ：ヒアリング再回答）calendar
        case 48:
          $row = $this->_setData($date, "シナリオメッセージ（ヒアリング再回答）", "", $message);
          break;
        case 41: // シナリオメッセージ（ヒアリング）pulldown
        case 42: // シナリオメッセージ（ヒアリング）calendar
        case 46:
          $row = $this->_setData($date, "シナリオメッセージ（ヒアリング）", $this->userInfo['MCompany']['company_name'],
            $message);
          break;
        case 998: // 入室メッセージ
        case 999: // 退室メッセージ
          $row = $this->_setData($date, "通知メッセージ", "",
            " - " . $val['MUser']['display_name'] . "が" . $message . "しました - ");
          break;
      }
      $csv[] = $row;
    }
    $this->_outputCSV($name, $csv);
  }

  private function _outputCSV($name, $csv = [])
  {
    $this->layout = null;

    //メモリ上に領域確保
    $fp = fopen('php://temp/maxmemory:' . (5 * 1024 * 1024), 'a');

    foreach ($csv as $row) {
      fputcsv($fp, $row);
    }

    //ビューを使わない
    $this->autoRender = false;

    $filename = date("YmdHis") . "_" . $name;

    //download()内ではheader("Content-Disposition: attachment; filename=hoge.csv")を行っている
    $this->response->download($filename . ".csv");

    //ファイルポインタを先頭へ
    rewind($fp);

    //リソースを読み込み文字列を取得する
    $csv = stream_get_contents($fp);

    //Content-Typeを指定
    $this->response->type('csv');

    //CSVをエクセルで開くことを想定して文字コードをSJIS-win
    $csv = mb_convert_encoding($csv, 'SJIS-win', 'utf8');

    $this->response->body($csv);

    fclose($fp);
  }

  /**
   * CSVデータのセット
   * @param $date date
   * @param $type string
   * @param $name string
   * @param $message string
   * @return array
   * */
  private function _setData($date = "", $type = "", $name = "", $message = "")
  {
    return [
      $date, // 送信日時
      $type, // 送信種別
      $name, // 送信者
      $message // メッセージ
    ];
  }

  /**
   * 顧客情報のデータリスト
   * @return array
   * */
  private function _getInfomationList()
  {
    $customerSettingList = $this->TCustomerInformationSetting->find('all', array(
      'conditions' => array(
        'delete_flg' => 0,
        'm_companies_id' => $this->userInfo['MCompany']['id']
      ),
      'order' => array(
        'sort' => 'asc'
      )
    ));
    $customerInformationSettingList = [];
    foreach ($customerSettingList as $k => $v) {
      array_push($customerInformationSettingList, $v['TCustomerInformationSetting']);
    }

    $customerInfoDisplaySettingMap = array();
    foreach ($customerSettingList as $index => $customerData) {
      $customerInfoDisplaySettingMap[$customerData['TCustomerInformationSetting']['item_name']] = $customerData['TCustomerInformationSetting'];
    }
    return $customerInfoDisplaySettingMap;
  }

  private function _setListByChat($type, $data, $visitorsIds = [])
  {
    $cond = [];

    return $cond;
  }

  /**
   *  入力された条件にマッチした顧客のIDを取得
   * @param $searchData array 入力データ
   * @return array 顧客のIDリスト
   * */
  private function _searchCustomer($searchData)
  {
    $visitorsIds = [];
    $userCond = [
      'MCustomer.m_companies_id' => $this->userInfo['MCompany']['id'],
    ];

    $customerSettingList = $this->TCustomerInformationSetting->find('all', array(
      'conditions' => array(
        'delete_flg' => 0,
        'm_companies_id' => $this->userInfo['MCompany']['id']
      ),
      'order' => array(
        'sort' => 'asc'
      )
    ));

    $customerInformationSettingList = [];
    foreach ($customerSettingList as $k => $v) {
      array_push($customerInformationSettingList, $v['TCustomerInformationSetting']);
    }

    $customerInfoDisplaySettingMap = array();
    foreach ($customerSettingList as $index => $customerData) {
      $customerInfoDisplaySettingMap[$customerData['TCustomerInformationSetting']['item_name']] = $customerData['TCustomerInformationSetting']['show_realtime_monitor_flg'];
    }

    $allusers = $this->MCustomer->find('all', [
      'fields' => '*',
      'conditions' => $userCond
    ]);

    foreach ($allusers as $alluser) {
      $setFlg = false;
      $userInfo = CustomerInformationUtil::convertOldIFData((array)json_decode($alluser['MCustomer']['informations']));
      foreach ($customerInfoDisplaySettingMap as $key => $val) {
        if (isset($searchData[$key]) && $searchData[$key] != "") {
          if (!isset($userInfo[$key]) || $userInfo[$key] === "") {
            $setFlg = false;
            continue 2;
          } else {
            if (strpos($userInfo[$key], $searchData[$key]) !== false) {
              $setFlg = true;
            } else {
              $setFlg = false;
            }
          }
        }
      }
      if ($setFlg) {
        $visitorsIds[$alluser['MCustomer']['visitors_id']] = true;
      }
    }
    return array_keys($visitorsIds);
  }

  private function _searchProcessing($type)
  {
    $data = $this->Session->read('Thistory');
    $companyStartDay = date("Y/m/d", strtotime($this->userInfo['MCompany']['created']));
    $historyConditions = [
      'History' => [
        'company_start_day' => $companyStartDay,
        'ip_address' => ''
      ],
      'CustomData' => [],
      'THistoryChatLog' => ['responsible_name' => '', 'achievement_flg' => '', 'message' => '']
    ];
    switch ($type) {
      //履歴一覧ボタンをクリックした場合
      case 1:
        $historyConditions['History']['start_day'] = date("Y/m/d", strtotime("-1 day"));
        $historyConditions['History']['finish_day'] = date("Y/m/d");
        $historyConditions['History']['period'] = '過去２日間';
        break;
      //条件クリアをクリックした場合
      case 2:
        $historyConditions['History']['start_day'] = $data['History']['start_day'];
        $historyConditions['History']['finish_day'] = $data['History']['finish_day'];
        $historyConditions['History']['period'] = $data['History']['period'];
        break;
      //デフォルト
      default:
        $historyConditions = $data;
        if ($this->request->is('post')) {
          $historyConditions = $this->request->data;
          $historyConditions['History']['company_start_day'] = $companyStartDay;
        }
        break;
    }
    $this->Session->write('Thistory', $historyConditions);
  }

  private function _setList($type = true)
  {
    $data = '';
    $userCond = [
      'm_companies_id' => $this->userInfo['MCompany']['id']
    ];
    $visitorsIds = [];
    $chatCond = [];
    $chatLogCond = [];
    //履歴検索機能
    if ($this->Session->check('Thistory')) {
      $data = $this->Session->read('Thistory');
      /* ○ 検索処理 */

      /* 顧客情報に関する検索条件 会社名、名前、電話、メール検索 */
      $check = false;
      foreach ($data['CustomData'] as $key => $value) {
        if (!empty($value)) {
          $check = true;
        }
      }
      if ($check === true) {
        //会社名が入っている場合
        if ((isset($this->coreSettings[C_COMPANY_REF_COMPANY_DATA]) && $this->coreSettings[C_COMPANY_REF_COMPANY_DATA]) && (isset($data['CustomData']['会社名']) && $data['CustomData']['会社名'] !== "")) {
          //会社名がランドスケープテーブルに登録されている場合
          $companyData = $this->MLandscapeData->find('all', [
            'fields' => 'lbc_code,ip_address,org_name',
            'conditions' => [
              'MLandscapeData.org_name LIKE' => '%' . $data['CustomData']['会社名'] . '%'
            ]
          ]);

          if (!empty($companyData)) {
            $visitorsIds = $this->_searchCustomer($data['CustomData']);
            $customDataWithoutCompany = $data['CustomData'];
            unset($customDataWithoutCompany['会社名']);
            $visitorIdsWithoutCompany = $this->_searchCustomer($customDataWithoutCompany);

            $ipAddressList = [];
            foreach ($companyData as $k => $v) {
              $ipAddressList[] = $v['MLandscapeData']['ip_address'];
            }
            if (count($visitorIdsWithoutCompany) > 0) {
              $chatCond['visitors_id'] = $visitorIdsWithoutCompany;
              $this->paginate['THistory']['conditions'] = array(
                'THistory.m_companies_id' => $this->userInfo['MCompany']['id'],
                'OR' => array(
                  'THistory.ip_address' => $ipAddressList,
                  'THistory.visitors_id' => $visitorsIds
                ),
                'AND' => array(
                  'THistory.visitors_id' => $visitorIdsWithoutCompany
                )
              );
            } else {
              $chatCond['visitors_id'] = $visitorsIds;
              $this->paginate['THistory']['conditions'] = array(
                'THistory.m_companies_id' => $this->userInfo['MCompany']['id'],
                'OR' => array(
                  'THistory.ip_address' => $ipAddressList,
                  'THistory.visitors_id' => $visitorsIds
                )
              );
            }
          } else {
            $visitorsIds = $this->_searchCustomer($data['CustomData']);
            $this->paginate['THistory']['conditions']['THistory.visitors_id'] = $visitorsIds;
            $chatCond['visitors_id'] = $visitorsIds;
          }
        } else {
          $visitorsIds = $this->_searchCustomer($data['CustomData']);
          $this->paginate['THistory']['conditions']['THistory.visitors_id'] = $visitorsIds;
          $chatCond['visitors_id'] = $visitorsIds;
        }
      }

      //ipアドレス
      if (isset($data['History']['ip_address']) && $data['History']['ip_address'] !== "") {
        //「%」が含まれていた場合
        if (strpos($data['History']['ip_address'], '%') !== false) {
          $data['History']['ip_address'] = str_replace('%', '\\%', $data['History']['ip_address']);
        }
        $this->paginate['THistory']['conditions']['THistory.ip_address LIKE'] = '%' . $data['History']['ip_address'] . '%';
      }
      //開始日
      if (!empty($data['History']['start_day'])) {
        $this->paginate['THistory']['conditions']['THistory.access_date >='] = $data['History']['start_day'] . ' 00:00:00';
      }
      //終了日
      if (!empty($data['History']['finish_day'])) {
        $this->paginate['THistory']['conditions']['THistory.access_date <='] = $data['History']['finish_day'] . ' 23:59:59';
      }

      // 担当者に関する検索条件
      $joinType = 'LEFT';
      if (isset($data['THistoryChatLog']['responsible_name']) && $data['THistoryChatLog']['responsible_name'] !== "") {
        //「%」が含まれていた場合
        if (strpos($data['THistoryChatLog']['responsible_name'], '%') !== false) {
          $data['THistoryChatLog']['responsible_name'] = str_replace('%', '\\%',
            $data['THistoryChatLog']['responsible_name']);
        }
        $userCond['display_name LIKE'] = "%" . $data['THistoryChatLog']['responsible_name'] . "%";
        $joinType = 'INNER';
      }

      /* チャットに関する検索条件 チャット担当者、チャット内容、チャット成果 */

      // 検索条件に成果がある場合
      if (isset($data['THistoryChatLog']['achievement_flg']) && $data['THistoryChatLog']['achievement_flg'] !== "") {
        switch ($data['THistoryChatLog']['achievement_flg']) {
          case '0':
            $chatLogCond['chat.cv !='] = 0;
            break;
          case '1':
            $chatLogCond['chat.eff = 0 AND chat.deny != '] = 0;
            break;
          case '2':
            $chatLogCond['chat.eff != '] = 0;
            break;
          case '3': // 途中離脱
            $chatLogCond['chat.cv = 0 AND chat.terminate != '] = 0;
            break;
        }
      } else {
        if (!empty($type) && $type == 'false') {
          $chatLogCond['chat.cv != '] = 0;
        }
      }

      // 検索条件にメッセージがある場合
      if (isset($data['THistoryChatLog']['message']) && $data['THistoryChatLog']['message'] !== "") {
        //「%」が含まれていた場合
        if (strpos($data['THistoryChatLog']['message'], '%') !== false) {
          $data['THistoryChatLog']['message'] = str_replace('%', '\\%', $data['THistoryChatLog']['message']);
        }
        // メッセージ条件に対応した履歴のリストを取得するサブクエリを作成
        $message = $this->THistoryChatLog->getDataSource();
        $hisIdsForMessageQuery = $message->buildStatement(
          [
            'table' => $message->fullTableName($this->THistoryChatLog),
            'alias' => 'thcl',
            'fields' => ['t_histories_id'],
            'conditions' => [
              'message LIKE' => "%" . $data['THistoryChatLog']['message'] . "%"
            ], // メッセージでの絞り込み
            'order' => 't_histories_id',
            'group' => 't_histories_id'
          ],
          $this->THistoryChatLog
        );
        $this->paginate['THistory']['joins'][] = [
          'type' => 'INNER',
          'alias' => 'message',
          'table' => "({$hisIdsForMessageQuery})",
          'conditions' => 'message.t_histories_id = THistory.id'
        ];
      }

      if (!empty($data['THistoryChatLog']) && !empty(array_filter($data['THistoryChatLog']))) {
        // 対象ユーザーのIDリストを取得するサブクエリを作成
        $users = $this->MUser->getDataSource();
        $userListQurey = $users->buildStatement(
          [
            'table' => $users->fullTableName($this->MUser),
            'alias' => 'MUser',
            'fields' => ['id'],
            'conditions' => $userCond, // 担当者検索結果
          ],
          $this->MUser
        );

        // 対象ユーザーが対応した履歴のリストを取得するサブクエリを作成
        $chatLogQuery = $this->THistoryChatLog->getDataSource();
        $historyIdListQuery = $chatLogQuery->buildStatement(
          [
            'table' => $users->fullTableName($this->THistoryChatLog),
            'alias' => 'chatLog',
            'fields' => ['t_histories_id'],
            'joins' => [
              [
                'type' => $joinType,
                'alias' => 'muser',
                'table' => "({$userListQurey})",
                'conditions' => 'muser.id = chatLog.m_users_id'
              ]
            ],
            'conditions' => $chatCond, // 顧客検索結果
            'order' => 'chatLog.t_histories_id',
            'group' => 'chatLog.t_histories_id'
          ],
          $this->THistoryChatLog
        );
        if ($this->coreSettings[C_COMPANY_USE_CHAT]) {
          // チャットのみ表示との切り替え（担当者検索の場合、強制的にINNER）
          $this->paginate['THistory']['joins'][] = [
            'type' => 'INNER',
            'alias' => 'his',
            'table' => "({$historyIdListQuery})",
            'conditions' => 'his.t_histories_id = THistory.id'
          ];
        }
      }
    }

    if (isset($this->coreSettings[C_COMPANY_REF_COMPANY_DATA]) && $this->coreSettings[C_COMPANY_REF_COMPANY_DATA]) {
      $joinToLandscapeData = [
        'type' => 'LEFT',
        'table' => 'm_landscape_data',
        'alias' => 'LandscapeData',
        'field' => ['lbc_code', 'ip_address', 'org_name'],
        'conditions' => [
          'LandscapeData.ip_address = THistory.ip_address',
        ],
      ];
      // MLの企業情報を閲覧できない企業であれば
      if (!$this->isViewableMLCompanyInfo()) {
        $joinToLandscapeData['conditions']['NOT']['LandscapeData.lbc_code'] = LandscapeLbcAPIComponent::ML_LBC_CODE;
      }
    }

    // 3) チャットに関する検索条件
    if ($this->coreSettings[C_COMPANY_USE_CHAT]) {

      $dbo2 = $this->THistoryChatLog->getDataSource();
      $chatStateList = $dbo2->buildStatement(
        [
          'table' => $dbo2->fullTableName($this->THistoryChatLog),
          'alias' => 'THistoryChatLog',
          'fields' => [
            't_histories_id, COUNT(*) AS count',
            'MAX(achievement_flg) AS achievementFlg',
            'SUM(CASE WHEN message_type = 998 THEN 1 ELSE 0 END) cmp',
            'SUM(CASE WHEN message_type = 4 THEN 1 ELSE 0 END) sry',
            'SUM(CASE WHEN message_type = 1 THEN 1 ELSE 0 END) cus'
          ],
          'order' => 't_histories_id',
          'group' => 't_histories_id'
        ],
        $this->THistoryChatLog
      );

      $dbo2 = $this->THistoryChatLog->getDataSource();

      $chatStateList = $dbo2->buildStatement(
        [
          'table' => "(SELECT t_histories_id,t_history_stay_logs_id,m_companies_id,message_type,notice_flg,created,message_read_flg, COUNT(*) AS count, SUM(CASE WHEN achievement_flg = -1 THEN 1 ELSE 0 END) terminate, SUM(CASE WHEN achievement_flg = 0 THEN 1 ELSE 0 END) cv, SUM(CASE WHEN achievement_flg = 1 THEN 1 ELSE 0 END) deny, SUM(CASE WHEN achievement_flg = 2 THEN 1 ELSE 0 END) eff, SUM(CASE WHEN message_type = 998 THEN 1 ELSE 0 END) cmp,SUM(CASE WHEN notice_flg = 1 THEN 1 ELSE 0 END) notice,SUM(CASE WHEN message_type = 3 THEN 1 ELSE 0 END) auto_message,SUM(CASE WHEN message_type = 4 THEN 1 ELSE 0 END) sry, SUM(CASE WHEN message_type = 1 THEN 1 ELSE 0 END) cus,SUM(CASE WHEN message_type = 1 AND message_read_flg = 0 THEN 1 ELSE 0 END) unread, SUM(CASE WHEN message_type = 5 THEN 1 ELSE 0 END) auto_speech, SUM(CASE WHEN message_type >= 12 AND message_type <= 13 THEN 1 ELSE 0 END) se_cus, SUM(CASE WHEN message_type >= 21 AND message_type <= 27 THEN 1 ELSE 0 END) se_auto FROM t_history_chat_logs AS THistoryChatLog force index(idx_t_history_chat_logs_m_companies_id_t_histories_id_created) WHERE `THistoryChatLog`.m_companies_id = " . $this->userInfo['m_companies_id'] . " AND created between '" . $data['History']['start_day'] . " 00:00:00' AND '" . $data['History']['finish_day'] . " 23:59:59' GROUP BY t_histories_id ORDER BY t_histories_id desc)",
          'alias' => 'chat',
          'fields' => [
            'chat.*',
            '( CASE  WHEN chat.cmp = 0 AND notice > 0 AND chat.cus > 0 THEN "未入室" WHEN chat.cmp = 0 AND chat.cus > 0 AND chat.sry > 0 THEN "拒否" WHEN chat.cmp = 0 AND chat.cus > 0 AND chat.sry = 0 AND auto_speech > 0 THEN "自動返信" WHEN chat.cmp = 0 AND chat.cus = 0 AND chat.sry = 0 AND auto_speech = 0 AND auto_message > 0 THEN "自動返信" WHEN chat.cmp = 0 AND chat.cus >= 0 AND chat.sry = 0 AND auto_speech = 0 AND auto_message >= 0 AND (se_cus >= 0 AND se_auto >= 0) THEN "自動返信" ELSE "" END ) AS type'
          ],
          'conditions' => $chatLogCond
        ],
        $this->THistoryChatLog
      );

      $joinToChat = [
        'type' => 'INNER',
        'table' => "({$chatStateList})",
        'alias' => 'THistoryChatLog',
        'conditions' => [
          'THistoryChatLog.t_histories_id = THistory.id'
        ]
      ];
      // チャットのみ表示との切り替え（担当者検索の場合、強制的にINNER）
      if (strcmp($type,
          'true') === 0 && !(!empty($data['THistoryChatLog']) && !empty(array_filter($data['THistoryChatLog'])))) {
        if (isset($data['THistoryChatLog']['achievement_flg']) && $data['THistoryChatLog']['achievement_flg'] !== "") {
          $joinToChat['type'] = "INNER";
        } else {
          $joinToChat['type'] = "LEFT";
        }
      } else {
        if (empty($type)) {
          $joinToChat['type'] = "LEFT";
        } else {
          $joinToChat['type'] = "INNER";
        }
      }

      $joinToLastSpeechChatTime = [
        'type' => 'LEFT',
        'table' => '(SELECT t_histories_id, message_type, MAX(created) as created FROM t_history_chat_logs WHERE message_type = 1 AND m_companies_id =' . $this->userInfo['m_companies_id'] . ' GROUP BY t_histories_id ORDER BY t_histories_id desc)',
        'alias' => 'LastSpeechTime',
        'field' => 'created as lastSpeechTime',
        'conditions' => [
          'LastSpeechTime.t_histories_id = THistoryChatLog.t_histories_id',
        ],
      ];

      $this->paginate['THistory']['fields'][] = 'THistoryChatLog.*';
      $this->paginate['THistory']['fields'][] = 'LastSpeechTime.created as lastSpeechTime';
      $this->paginate['THistory']['joins'][] = $joinToChat;
      $this->paginate['THistory']['joins'][] = $joinToLastSpeechChatTime;

      if (isset($this->coreSettings[C_COMPANY_REF_COMPANY_DATA]) && $this->coreSettings[C_COMPANY_REF_COMPANY_DATA]) {
        $this->paginate['THistory']['fields'][] = 'LandscapeData.*';
        $this->paginate['THistory']['joins'][] = $joinToLandscapeData;
      }
    }
    $historyList = $this->paginate('THistory');

    // TODO 良いやり方が無いか模索する
    $historyIdList = [];
    $customerIdList = [];
    foreach ($historyList as $key => $val) {
      $historyIdList[] = $val['THistory']['id'];
      $customerIdList[$val['THistory']['visitors_id']] = true;
    }
    $tHistoryStayLogList = $this->THistoryStayLog->find('all', [
      'fields' => [
        't_histories_id',
        'url AS firstURL',
        'title',
        'COUNT(t_histories_id) AS count '
      ],
      'conditions' => [
        't_histories_id' => $historyIdList
      ],
      'group' => 't_histories_id'
    ]);

    $stayList = [];
    foreach ($tHistoryStayLogList as $val) {
      $stayList[$val['THistoryStayLog']['t_histories_id']] = [
        'THistoryStayLog' => [
          'firstURL' => $val['THistoryStayLog']['firstURL'],
          'title' => $val['THistoryStayLog']['title'],
          'count' => $val[0]['count']
        ]
      ];
    }

    $mCustomerList = $this->MCustomer->find('list', [
      'fields' => ['visitors_id', 'informations'],
      'conditions' => [
        'm_companies_id' => $this->userInfo['MCompany']['id'],
        'visitors_id' => array_keys($customerIdList),
      ]
    ]);
    // いったんJSONデータをデコードする
    foreach ($mCustomerList as $key => $value) {
      $mCustomerList[$key] = json_decode($value, true);
    }

    $customerSettingList = $this->TCustomerInformationSetting->find('all', array(
      'conditions' => array(
        'delete_flg' => 0,
        'm_companies_id' => $this->userInfo['MCompany']['id']
      ),
      'order' => array(
        'sort' => 'asc'
      )
    ));

    $customerInformationSettingList = [];
    foreach ($customerSettingList as $k => $v) {
      array_push($customerInformationSettingList, $v['TCustomerInformationSetting']);
    }

    $customerInfoDisplaySettingMap = array();
    foreach ($customerSettingList as $index => $customerData) {
      $customerInfoDisplaySettingMap[$customerData['TCustomerInformationSetting']['item_name']] = (boolean)$customerData['TCustomerInformationSetting']['show_realtime_monitor_flg'];
    }

    $this->set('customerInformationList', $customerInformationSettingList);
    $this->set('customerInfoDisplaySettingMap', $customerInfoDisplaySettingMap);

    $data['History']['start_day'] = htmlspecialchars($data['History']['start_day']);
    $data['History']['finish_day'] = htmlspecialchars($data['History']['finish_day']);
    $this->set('data', $data);
    $this->set('historyList', $historyList);
    $this->set('stayList', $stayList);
    $this->set('mCustomerList', $mCustomerList);
    $this->set('chatUserList', $this->_getChatUser(array_keys($stayList))); // チャット担当者リスト
    $this->set('groupByChatChecked', $type);
    $this->set('campaignList', $this->TCampaign->getList());
    /* 除外情報取得 */
    $this->set('excludeList', $this->MCompany->getExcludeList($this->userInfo['MCompany']['id']));
  }

  /**
   * // TODO ブラッシュアップできそう
   *  チャット応対リストの取得
   * @param　array $historyList // 履歴リスト
   * @return array $ret 結果
   * */
  private function _getChatUser($historyList)
  {
    if (empty($historyList)) {
      return [];
    } // 履歴が取得できなかったら
    $params = [
      'fields' => [
        'THistory.id',
        'MUser.display_name',
        'THistoryChatLog.message_type'
      ],
      'joins' => [
        [
          'type' => 'INNER',
          'table' => '(SELECT * FROM t_history_chat_logs ' .
            ' WHERE (m_users_id IS NOT NULL OR message_type = 5 OR (message_type >= 21 AND message_type <= 27))' .
            '   AND t_histories_id IN (' . implode(",", $historyList) . ')' .
            ' GROUP BY t_histories_id, m_users_id' .
            ')',
          'alias' => 'THistoryChatLog',
          'conditions' => [
            'THistoryChatLog.t_histories_id = THistory.id'
          ]
        ],
        [
          'type' => 'LEFT',
          'table' => 'm_users',
          'alias' => 'MUser',
          'conditions' => [
            'THistoryChatLog.m_users_id = MUser.id'
          ]
        ]
      ],
      'conditions' => [
        'THistory.m_companies_id' => $this->userInfo['MCompany']['id']
      ],
      'recursive' => -1
    ];
    $ret = $this->THistory->find('all', $params);
    $chat = [];
    foreach ((array)$ret as $val) {
      if (isset($chat[$val['THistory']['id']])) {
        if (strcmp($chat[$val['THistory']['id']], self::LABEL_AUTO_SPEECH_OPERATOR) === 0) {
          //自動返信後にオペレータが応対していた場合は自動返信の表示を消す
          $chat[$val['THistory']['id']] = $val['MUser']['display_name'] . "さん";
        } else {
          $chat[$val['THistory']['id']] .= "\n" . $val['MUser']['display_name'] . "さん";
        }
      } else {
        if ($val['MUser']['display_name'] !== null) {
          $chat[$val['THistory']['id']] = $val['MUser']['display_name'] . "さん";
        } else {
          if (strcmp($val['THistoryChatLog']['message_type'], "5") === 0
            || strcmp($val['THistoryChatLog']['message_type'], "21") === 0
            || strcmp($val['THistoryChatLog']['message_type'], "22") === 0
            || strcmp($val['THistoryChatLog']['message_type'], "23") === 0
            || strcmp($val['THistoryChatLog']['message_type'], "24") === 0
            || strcmp($val['THistoryChatLog']['message_type'], "27") === 0) {
            $chat[$val['THistory']['id']] = self::LABEL_AUTO_SPEECH_OPERATOR;
          }
        }
      }
    }
    return $chat;
  }

  private function _getChatLog($historyId)
  {
    $params = [
      'fields' => [
        'MUser.display_name',
        'DeleteMUser.display_name',
        'THistoryChatLog.*',
        'THistoryStayLog.url'
      ],
      'conditions' => [
        'THistoryChatLog.t_histories_id' => $historyId
      ],
      'joins' => [
        [
          'type' => 'LEFT',
          'table' => 'm_users',
          'alias' => 'MUser',
          'conditions' => [
            'THistoryChatLog.m_users_id = MUser.id',
            'MUser.m_companies_id' => $this->userInfo['MCompany']['id']
          ]
        ],
        [
          'type' => 'LEFT',
          'table' => 'm_users',
          'alias' => 'DeleteMUser',
          'conditions' => [
            'THistoryChatLog.deleted_user_id = DeleteMUser.id',
            'DeleteMUser.m_companies_id' => $this->userInfo['MCompany']['id'],
            'THistoryChatLog.delete_flg' => 1,
          ]
        ],
        [
          'type' => 'LEFT',
          'table' => 't_history_stay_logs',
          'alias' => 'THistoryStayLog',
          'conditions' => [
            'THistoryChatLog.t_history_stay_logs_id = THistoryStayLog.id'
          ]
        ]
      ],
      'order' => 'THistoryChatLog.created',
      'recursive' => -1
    ];
    return $this->THistoryChatLog->find('all', $params);
  }

  /* *
 * 履歴削除ダイアログ表示
 * @return void
 * */
  public function openEntryDelete()
  {
    Configure::write('debug', 0);
    $this->autoRender = false;
    $this->layout = 'ajax';
    $data = $this->request->data;
    //メッセージが10文字以上の場合3点リーダー表示
    if (mb_strlen($data['message']) > 10) {
      $data['message'] = mb_substr($data['message'], 0, 10) . '…';
    }
    $data = json_encode($data);
    $this->set('data', $data);
    //ポップアップの呼び出し
    $this->render('/Elements/Histories/remoteDelete');
  }

  /* *
   * 履歴削除
   * @return void
   * */
  public function remoteDeleteChat()
  {
    Configure::write('debug', 0);
    $this->autoRender = false;
    $this->layout = 'ajax';
    if ($this->userInfo['permission_level'] == 1 &&
      isset($this->coreSettings[C_COMPANY_USE_HISTORY_DELETE]) && $this->coreSettings[C_COMPANY_USE_HISTORY_DELETE]) {
      $id = $this->request->data['id'];
      $now = date('Y/m/d H:i:s');
      $userName = $this->userInfo['display_name'];

      $params = [
        'fields' => [
          'id'
        ],
        'conditions' => [
          'THistoryChatLog.id' => $id,
          'THistoryChatLog.delete_flg' => 0
        ]
      ];
      //対象の履歴が既に削除されていないかチェック
      $checkDeleteHistory = $this->THistoryChatLog->find('first', $params);

      if (!empty($checkDeleteHistory)) {
        $params = [
          'fields' => [
            'm_companies_id'
          ],
          'conditions' => [
            'THistoryChatLog.id' => $id
          ]
        ];

        //m_companies_id
        $m_companies_id = $this->THistoryChatLog->find('first', $params)['THistoryChatLog']['m_companies_id'];

        if ($m_companies_id == $this->userInfo['MCompany']['id']) {
          $saveData = [];
          $saveData = $this->THistoryChatLog->read(null, $id);
          $saveData['THistoryChatLog']['message'] = "(このメッセージは $now に 削除されました。)";
          $saveData['THistoryChatLog']['delete_flg'] = 1;
          $saveData['THistoryChatLog']['deleted'] = $now;
          $saveData['THistoryChatLog']['deleted_user_id'] = $this->userInfo['id'];

          $this->THistoryChatLog->set($saveData);
          $this->THistoryChatLog->begin();
          if ($this->THistoryChatLog->save()) {
            $this->THistoryChatLog->commit();

            //リンククリック件数の場合
            if ($saveData['THistoryChatLog']['message_type'] == 8) {
              $deleteData = [
                't_histories_id' => $saveData['THistoryChatLog']['t_histories_id'],
                'm_companies_id' => $m_companies_id,
                'created' => date('Y-m-d H:i:s', strtotime($saveData['THistoryChatLog']['created']))
              ];
              $this->THistoryLinkCountLog->deleteAll($deleteData);
            }
            $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
          } else {
            $this->THistoryChatLog->rollback();
            $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
          }
        } else {
          $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
        }
      } else {
        // すでに存在しない履歴のため変更済みとしてエラーを返す
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deletedHistory'));
      }
    } else {
      $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
    }
  }

  /* *
   * 検索画面
   * @return void
   * */
  public function remoteSearchCustomerInfo()
  {
    Configure::write('debug', 0);
    $this->autoRender = false;
    $this->layout = 'ajax';
    $this->data = $this->Session->read('Thistory');
    $this->set('data', $this->Session->read('Thistory'));


    // 成果種別リスト スタンダードプラン以上
    if (isset($this->coreSettings[C_COMPANY_USE_CV]) && $this->coreSettings[C_COMPANY_USE_CV]) {
      $this->set('achievementType', Configure::read('achievementTypeForSearch'));
    } // 成果種別リスト スタンダードプラン以下
    else {
      $achievementType = Configure::read('achievementTypeForSearch');
      unset($achievementType[0]);
      unset($achievementType[3]);
      $this->set('achievementType', $achievementType);
    }

    // 訪問ユーザ情報設定状態を取得
    /* 訪問ユーザ情報設定リストを取得 */
    $customerSettingList = $this->TCustomerInformationSetting->find('all', array(
      'conditions' => array(
        'delete_flg' => 0,
        'm_companies_id' => $this->userInfo['MCompany']['id']
      ),
      'order' => array(
        'sort' => 'asc'
      )
    ));
    $customerInformationSettingList = [];
    foreach ($customerSettingList as $k => $v) {
      array_push($customerInformationSettingList, $v['TCustomerInformationSetting']);
    }

    $customerInfoDisplaySettingMap = array();
    foreach ($customerSettingList as $index => $customerData) {
      $customerInfoDisplaySettingMap[$customerData['TCustomerInformationSetting']['item_name']] = $customerData['TCustomerInformationSetting']['show_realtime_monitor_flg'];
    }
    $this->set('customerInformationList', $customerInformationSettingList);
    $this->set('customerInfoDisplaySettingMap', $customerInfoDisplaySettingMap);

    // const
    $this->render('/Elements/Histories/remoteSearchCustomerInfo');
  }

  /* *
  * Session削除(条件クリア)
  * @return void
  * */
  public function portionClearSession()
  {
    $this->_searchProcessing(2);
    $this->redirect(['controller' => 'Histories', 'action' => 'index']);
  }

  /* *
  * Session削除(一覧画面)
  * @return void
  * */
  public function clearSession()
  {
    $this->Session->delete('Thistory');
    $this->Session->delete('authenticity');
    $this->_searchProcessing(1);
    $this->redirect(['controller' => 'Histories', 'action' => 'index']);
  }

  /**
   * //
   *  csv出力,join,検索条件(一覧画面)
   * @return 　検索条件にメッセージがある場合のjoin
   * @return m_users_idを取るためjoin
   * @return 未入室、拒否、履歴を振り分けるためjoin
   * @return 検索条件(conditions)
   * */
  private function _searchConditions()
  {
    $chatCond = [];
    $chatPlan = [];
    $chatLogCond = [];
    $conditions = [];
    $joinList = [
      [
        'type' => 'left',
        'alias' => 'MCustomer',
        'table' => '(SELECT * FROM m_customers WHERE m_companies_id = ' . $this->userInfo["MCompany"]["id"] . ')',
        'conditions' => [
          'THistory.visitors_id = MCustomer.visitors_id'
        ]
      ]
    ];
    if (isset($this->coreSettings[C_COMPANY_REF_COMPANY_DATA]) && $this->coreSettings[C_COMPANY_REF_COMPANY_DATA]) {
      $landscapeJoinListCondition = [
        'type' => 'left',
        'alias' => 'LandscapeData',
        'table' => 'm_landscape_data',
        'conditions' => [
          'THistory.ip_address = LandscapeData.ip_address'
        ]
      ];
      // MLの企業情報を閲覧できない企業であれば
      if (!$this->isViewableMLCompanyInfo()) {
        $landscapeJoinListCondition['conditions']['NOT']['LandscapeData.lbc_code'] = LandscapeLbcAPIComponent::ML_LBC_CODE;
      }
      $joinList[] = $landscapeJoinListCondition;
    }
    $userCond = [
      'm_companies_id' => $this->userInfo['MCompany']['id']
    ];
    $type = 'true';
    if (!empty($this->params->query['isChat'])) {
      $this->Session->write('authenticity', $this->params->query['isChat']);
    }
    $type = $this->Session->read('authenticity');
    $data = $this->Session->read('Thistory');

    /* 顧客情報に関する検索条件 会社名、名前、電話、メール検索 */
    $check = false;
    if (!empty($data['CustomData'])) {
      foreach ($data['CustomData'] as $key => $value) {
        if (!empty($value)) {
          $check = true;
        }
      }
    }
    if ($check) {
      //会社名が入っている場合
      if ((isset($this->coreSettings[C_COMPANY_REF_COMPANY_DATA]) && $this->coreSettings[C_COMPANY_REF_COMPANY_DATA]) && (isset($data['CustomData']['会社名']) && $data['CustomData']['会社名'] !== "")) {
        //会社名がランドスケープテーブルに登録されている場合
        $companyConditions = [
          'fields' => 'lbc_code,ip_address,org_name',
          'conditions' => [
            'MLandscapeData.org_name LIKE' => '%' . $data['CustomData']['会社名'] . '%'
          ]
        ];
        // MLの企業情報を閲覧できない企業であれば
        if (!$this->isViewableMLCompanyInfo()) {
          $companyConditions['conditions']['NOT']['MLandscapeData.lbc_code'] = LandscapeLbcAPIComponent::ML_LBC_CODE;
        }
        $companyData = $this->MLandscapeData->find('all', $companyConditions);

        if (!empty($companyData)) {
          $visitorsIds = $this->_searchCustomer($data['CustomData']);
          $customDataWithoutCompany = $data['CustomData'];
          unset($customDataWithoutCompany['会社名']);
          $visitorIdsWithoutCompany = $this->_searchCustomer($customDataWithoutCompany);

          $ipAddressList = [];
          foreach ($companyData as $k => $v) {
            $ipAddressList[] = $v['MLandscapeData']['ip_address'];
          }
          if (count($visitorIdsWithoutCompany) > 0) {
            $chatCond['visitors_id'] = $visitorIdsWithoutCompany;
            $conditions[] = array(
              'THistory.m_companies_id' => $this->userInfo['MCompany']['id'],
              'OR' => array(
                'THistory.ip_address' => $ipAddressList,
                'THistory.visitors_id' => $visitorsIds
              ),
              'AND' => array(
                'THistory.visitors_id' => $visitorIdsWithoutCompany
              )
            );
          } else {
            $chatCond['visitors_id'] = $visitorsIds;
            $conditions[] = array(
              'THistory.m_companies_id' => $this->userInfo['MCompany']['id'],
              'OR' => array(
                'THistory.ip_address' => $ipAddressList,
                'THistory.visitors_id' => $visitorsIds
              )
            );
          }
        } else {
          $visitorsIds = $this->_searchCustomer($data['CustomData']);
          $conditions[] = [
            'THistory.visitors_id' => $visitorsIds
          ];
          $chatCond['visitors_id'] = $visitorsIds;
        }
      } else {
        $visitorsIds = $this->_searchCustomer($data['History']);
        $conditions[] = [
          'THistory.visitors_id' => $visitorsIds
        ];
        $chatCond['visitors_id'] = $visitorsIds;
      }
    }

    if (isset($data['History']['ip_address']) && $data['History']['ip_address'] !== "") {
      //「%」が含まれていた場合
      if (strpos($data['History']['ip_address'], '%') !== false) {
        $data['History']['ip_address'] = str_replace('%', '\\%', $data['History']['ip_address']);
      }
      $conditions[] = [
        'THistory.ip_address LIKE' => '%' . $data['History']['ip_address'] . '%',
      ];
    }
    //開始日
    if (!empty($data['History']['start_day'])) {
      $conditions[] = [
        'THistory.access_date >=' => $data['History']['start_day'] . ' 00:00:00',
        'THistory.m_companies_id' => $this->userInfo['MCompany']['id']
      ];
    }

    //終了日
    if (!empty($data['History']['finish_day'])) {
      $conditions[] = [
        'THistory.access_date <=' => $data['History']['finish_day'] . ' 23:59:59',
        'THistory.m_companies_id' => $this->userInfo['MCompany']['id']
      ];
    }

    $joinType = 'LEFT';
    // 担当者に関する検索条件
    if (isset($data['THistoryChatLog']['responsible_name']) && $data['THistoryChatLog']['responsible_name'] !== "") {
      //「%」が含まれていた場合
      if (strpos($data['THistoryChatLog']['responsible_name'], '%') !== false) {
        $data['THistoryChatLog']['responsible_name'] = str_replace('%', '\\%',
          $data['THistoryChatLog']['responsible_name']);
      }
      $userCond['display_name LIKE'] = "%" . $data['THistoryChatLog']['responsible_name'] . "%";
      $joinType = 'INNER';
    }

    // 検索条件に成果がある場合
    if (isset($data['THistoryChatLog']['achievement_flg']) && $data['THistoryChatLog']['achievement_flg'] !== "") {
      $type = "false";
      switch ($data['THistoryChatLog']['achievement_flg']) {
        case '0':
          $chatLogCond['chat.cv !='] = 0;
          break;
        case '1':
          $chatLogCond['chat.eff = 0 AND chat.deny != '] = 0;
          break;
        case '2':
          $chatLogCond['chat.eff != '] = 0;
          break;
        case '3': // 途中離脱
          $chatLogCond['chat.cv = 0 AND chat.terminate != '] = 0;
          break;
      }
    } else {
      if (!empty($type) && $type == 'false') {
        $chatLogCond['chat.cv != '] = 0;
      }
    }

    // 検索条件にメッセージがある場合
    if (isset($data['THistoryChatLog']['message']) && $data['THistoryChatLog']['message'] !== "") {
      //「%」が含まれていた場合
      if (strpos($data['THistoryChatLog']['message'], '%') !== false) {
        $data['THistoryChatLog']['message'] = str_replace('%', '\\%', $data['THistoryChatLog']['message']);
      }
      // メッセージ条件に対応した履歴のリストを取得するサブクエリを作成
      $message = $this->THistoryChatLog->getDataSource();
      $hisIdsForMessageQuery = $message->buildStatement(
        [
          'table' => $message->fullTableName($this->THistoryChatLog),
          'alias' => 'thcl',
          'fields' => ['t_histories_id'],
          'conditions' => [
            'message LIKE' => "%" . $data['THistoryChatLog']['message'] . "%"
          ], // メッセージでの絞り込み
          'order' => 't_histories_id',
          'group' => 't_histories_id'
        ],
        $this->THistoryChatLog
      );
      $joinList[] = [
        'type' => 'INNER',
        'alias' => 'message',
        'table' => "({$hisIdsForMessageQuery})",
        'conditions' => 'message.t_histories_id = THistory.id'
      ];
    }

//    $joinList[] = array(
//      'type' => 'INNER',
//      'alias' => 'THistoryStayLog',
//      'table' => '(select thsl.t_histories_id as t_histories_id, thsl.title as title, thsl.url AS firstURL, COUNT(thsl.t_histories_id) as count from t_history_stay_logs as thsl inner join t_histories as th on (thsl.t_histories_id = th.id) where th.m_companies_id = '.$this->userInfo['MCompany']['id'].' and thsl.created >= "'.$data['History']['start_day'].' 00:00:00" and thsl.created <= "'.$data['History']['finish_day'].' 23:59:59" group by t_histories_id)',
//      'conditions' => 'THistoryStayLog.t_histories_id = THistory.id'
//    );

    if (!empty($data['THistoryChatLog']) && !empty(array_filter($data['THistoryChatLog']))) {
      // 対象ユーザーのIDリストを取得するサブクエリを作成
      $users = $this->MUser->getDataSource();
      $userListQurey = $users->buildStatement(
        [
          'table' => $users->fullTableName($this->MUser),
          'alias' => 'MUser',
          'fields' => ['id'],
          'conditions' => $userCond, // 担当者検索結果
        ],
        $this->MUser
      );
      // 対象ユーザーが対応した履歴のリストを取得するサブクエリを作成
      $chatLogQuery = $this->THistoryChatLog->getDataSource();
      $historyIdListQuery = $chatLogQuery->buildStatement(
        [
          'table' => $users->fullTableName($this->THistoryChatLog),
          'alias' => 'chatLog',
          'fields' => ['t_histories_id'],
          'joins' => [
            [
              'type' => $joinType,
              'alias' => 'muser',
              'table' => "({$userListQurey})",
              'conditions' => 'muser.id = chatLog.m_users_id'
            ]
          ],
          'conditions' => $chatCond, // 顧客検索結果
          'order' => 'chatLog.t_histories_id',
          'group' => 'chatLog.t_histories_id'
        ],
        $this->THistoryChatLog
      );

      if ($this->coreSettings[C_COMPANY_USE_CHAT]) {
        // チャットのみ表示との切り替え（担当者検索の場合、強制的にINNER）
        $joinList[] = [
          'type' => 'INNER',
          'alias' => 'his',
          'table' => "({$historyIdListQuery})",
          'conditions' => 'his.t_histories_id = THistory.id'
        ];
      }
    }

    // 3) チャットに関する検索条件
    if ($this->coreSettings[C_COMPANY_USE_CHAT]) {
      $dbo2 = $this->THistoryChatLog->getDataSource();
      $chatStateList = $dbo2->buildStatement(
        [
          'table' => $dbo2->fullTableName($this->THistoryChatLog),
          'alias' => 'THistoryChatLog',
          'fields' => [
            't_histories_id, COUNT(*) AS count',
            'MAX(achievement_flg) AS achievementFlg',
            'SUM(CASE WHEN message_type = 998 THEN 1 ELSE 0 END) cmp',
            'SUM(CASE WHEN message_type = 4 THEN 1 ELSE 0 END) sry',
            'SUM(CASE WHEN message_type = 1 THEN 1 ELSE 0 END) cus'
          ],
          'order' => 't_histories_id',
          'group' => 't_histories_id'
        ],
        $this->THistoryChatLog
      );

      $dbo2 = $this->THistoryChatLog->getDataSource();
      if (empty($chatLogCond) || $chatLogCond['chat.achievementFlg'] == 1 || $chatLogCond['chat.achievementFlg'] == 2) {
        $value = 'MAX';
      } //成果でCVを検索する場合
      else {
        if (!empty($chatLogCond) && $chatLogCond['chat.achievementFlg'] == 0) {
          $value = 'MIN';
        }
      }
      $chatStateList = $dbo2->buildStatement(
        [
          'table' => "(SELECT t_histories_id, t_history_stay_logs_id, m_companies_id, message_type, notice_flg, created, message_read_flg,COUNT(*) AS count, SUM(CASE WHEN achievement_flg = -1 THEN 1 ELSE 0 END) terminate, SUM(CASE WHEN achievement_flg = 0 THEN 1 ELSE 0 END) cv, SUM(CASE WHEN achievement_flg = 1 THEN 1 ELSE 0 END) deny, SUM(CASE WHEN achievement_flg = 2 THEN 1 ELSE 0 END) eff, SUM(CASE WHEN message_type = 998 THEN 1 ELSE 0 END) cmp,SUM(CASE WHEN notice_flg = 1 THEN 1 ELSE 0 END) notice,SUM(CASE WHEN message_type = 3 THEN 1 ELSE 0 END) auto_message, SUM(CASE WHEN message_type = 4 THEN 1 ELSE 0 END) sry, SUM(CASE WHEN message_type = 1 THEN 1 ELSE 0 END) cus,SUM(CASE WHEN message_type = 1 AND message_read_flg = 0 THEN 1 ELSE 0 END) unread, SUM(CASE WHEN message_type = 5 THEN 1 ELSE 0 END) auto_speech FROM t_history_chat_logs AS THistoryChatLog force index (idx_t_history_chat_logs_m_companies_id_t_histories_id_created) WHERE `THistoryChatLog`.m_companies_id =" . $this->userInfo['m_companies_id'] . " AND `t_histories_id` = `THistoryChatLog`.t_histories_id AND created >= '" . $data['History']['start_day'] . " 00:00:00' AND created <= '" . $data['History']['finish_day'] . " 23:59:59' GROUP BY t_histories_id ORDER BY t_histories_id)",
          'alias' => 'chat',
          'fields' => [
            'chat.*',
            '( CASE  WHEN chat.cmp = 0 AND notice > 0 AND chat.cus > 0 THEN "未入室" WHEN chat.cmp = 0 AND chat.cus > 0 AND chat.sry > 0 THEN "拒否" WHEN chat.cmp = 0 AND chat.cus > 0 AND chat.sry = 0 AND auto_speech > 0 THEN "自動返信" WHEN chat.cmp = 0 AND chat.cus = 0 AND chat.sry = 0 AND auto_speech = 0 AND auto_message > 0 THEN "自動返信" ELSE "" END ) AS type'
          ],
          'conditions' => $chatLogCond
        ],
        $this->THistoryChatLog
      );
      $joinToChat = [
        'type' => 'INNER',
        'table' => "({$chatStateList})",
        'alias' => 'THistoryChatLog2',
        'conditions' => [
          'THistoryChatLog2.t_histories_id = THistory.id'
        ]
      ];

      // チャットのみ表示との切り替え（担当者検索の場合、強制的にINNER）
      if (strcmp($type,
          'true') === 0 && !(!empty($data['THistoryChatLog']) && !empty(array_filter($data['THistoryChatLog'])))) {
        $joinToChat['type'] = "LEFT";
      } else {
        if (empty($type)) {
          $joinToChat['type'] = "LEFT";
        } else {
          $joinToChat['type'] = "INNER";
        }
      }
      $joinList[] = $joinToChat;
    }
    return ['joinList' => $joinList, 'conditions' => $conditions];
  }

  /**
   * //
   *  csv出力担当者リスト(一覧画面)
   * @param  csv出力内容
   * @return 担当者リスト追加
   * */
  private function _userList($historyList)
  {

    $userNameList = $this->MUser->find('list', [
      'fields' => [
        'id',
        'display_name'
      ],
    ]);

    $chatList = $this->THistoryChatLog->find('all', [
      'fields' => [
        'THistoryChatLog.t_histories_id',
        'THistoryChatLog.m_users_id',
        'THistoryChatLog.message_type'
      ],
      'order' => [
        'THistoryChatLog.t_histories_id' => 'asc'
      ],
      'conditions' => [
        'OR' => [
          array('THistoryChatLog.message_type' => 998),
          array('THistoryChatLog.message_type' => 5)
        ],
        'AND' => [
          array('THistoryChatLog.m_companies_id' => $this->userInfo['MCompany']['id'])
        ]
      ],
      'group' => ['THistoryChatLog.t_histories_id', 'THistoryChatLog.m_users_id']
    ]);

    $users = [];

    foreach ($chatList as $val2) {
      $users[$val2['THistoryChatLog']['t_histories_id']][] = $val2['THistoryChatLog']['m_users_id'];
    }

    $userList = [];
    foreach ($historyList as $value2) {
      $tmp = $value2;
      $tmp['User'] = '';
      if (isset($users[$value2['THistory']['id']])) {
        foreach ($users[$value2['THistory']['id']] as $val3) {
          $userName = !empty($userNameList[$val3]) ? $userNameList[$val3] : null;
          if (!empty($tmp['User'])) {
            $tmp['User'] .= '、' . "\n";
          }
          if ($userName === null) {
            // 名前がnullの場合は自動応答
            $userName = self::LABEL_AUTO_SPEECH_OPERATOR;
          }
          if (strpos($tmp['User'], self::LABEL_AUTO_SPEECH_OPERATOR) !== false) {
            // 自動対応オペレータの名前を挿入したあとにユーザー名を表示する場合は自動対応のラベルを消す
            $tmp['User'] = $userName . "さん";
          } else {
            if (empty($tmp['User']) && strcmp($userName, self::LABEL_AUTO_SPEECH_OPERATOR) === 0) {
              $tmp['User'] = $userName;
            } else {
              $tmp['User'] .= $userName . "さん";
            }
          }
        }
      }
      $userList[] = $tmp;
    }
    return $userList;
  }

  /**
   * //
   *  csv出力、THistoryChatLogの「firstURL」と「count」を取ってくる(一覧画面)
   * @param  csv出力内容
   * @return THistoryChatLogの「firstURL」と「count」
   * */
  private function _stayList($userList)
  {
    // TODO 良いやり方が無いか模索する
    $historyIdList = [];
    $customerIdList = [];
    foreach ($userList as $val) {
      $historyIdList[] = $val['THistory']['id'];
      $customerIdList[$val['THistory']['visitors_id']] = true;
    }

    $stayList = [];
    if (count($historyIdList) >= 100000) {

    } else {
      $chunkedHistoryIdList = array_chunk($historyIdList, 1000);

      foreach ($chunkedHistoryIdList as $index => $list) {
        $tHistoryStayLogList = $this->THistoryStayLog->find('all', [
          'fields' => [
            't_histories_id',
            'title',
            'url AS firstURL',
            'COUNT(t_histories_id) AS count '
          ],
          'conditions' => [
            't_histories_id' => $list
          ],
          'group' => 't_histories_id'
        ]);

        foreach ($tHistoryStayLogList as $val) {
          $stayList[$val['THistoryStayLog']['t_histories_id']] = [
            'THistoryStayLog' => [
              'firstURL' => $val['THistoryStayLog']['firstURL'],
              'title' => $val['THistoryStayLog']['title'],
              'count' => $val[0]['count']
            ]
          ];
        }
      }
    }
    return $stayList;
  }

  private function _lastSpeechTimeList($historyList)
  {
    $historyIdList = [];
    foreach ($historyList as $val) {
      $historyIdList[] = $val['THistory']['id'];
    }

    $list = array();

    if (count($historyIdList) >= 100000) {

    } else {
      $chunkedHistoryIdList = array_chunk($historyIdList, 1000);
      $chatList = array();
      foreach ($chunkedHistoryIdList as $index => $chunk) {
        $chatList = $this->THistoryChatLog->find('all', [
          'fields' => [
            'THistoryChatLog.t_histories_id',
            'THistoryChatLog.message_type',
            'MAX(created) as created'
          ],
          'order' => [
            'THistoryChatLog.t_histories_id' => 'asc'
          ],
          'conditions' => [
            'AND' => array(
              't_histories_id' => $chunk,
              'message_type = 1'
            )
          ],
          'group' => ['THistoryChatLog.t_histories_id']
        ]);
      }

      foreach ($chatList as $k => $chat) {
        $list[$chat['THistoryChatLog']['t_histories_id']] = $chat[0]['created'];
      }
    }
    return $list;
  }

  /**
   * //
   *  csv,os出力(一覧画面)
   * @param  csv出力内容
   * @return  osの種類
   * */
  private function _userAgentCheckOS($val)
  {
    if (preg_match('/Windows NT 10.0/', $val['THistory']['user_agent'])) {
      $os = "Windows 10"; // Windows 10 の処理
    } else {
      if (preg_match('/Windows NT 6.3/', $val['THistory']['user_agent'])) {
        $os = "Windows 8.1"; // Windows 8.1 の処理
      } else {
        if (preg_match('/Windows NT 6.2/', $val['THistory']['user_agent'])) {
          $os = "Windows 8"; // Windows 8 の処理
        } else {
          if (preg_match('/Windows NT 6.1/', $val['THistory']['user_agent'])) {
            $os = "Windows 7"; // Windows 7 の処理
          } else {
            if (preg_match('/Windows NT 6.0/', $val['THistory']['user_agent'])) {
              $os = "Windows Vista"; // Windows Vista の処理
            } else {
              if (preg_match('/Windows NT 5.2/', $val['THistory']['user_agent'])) {
                $os = "Windows Server 2003";  // Windows Server 2003 の処理
              } else {
                if (preg_match('/Windows NT 5.1/', $val['THistory']['user_agent'])) {
                  $os = "Windows XP"; // Windows XP の処理
                } else {
                  if (preg_match('/Windows NT 4.90/', $val['THistory']['user_agent'])) {
                    $os = "Windows ME"; // Windows ME の処理
                  } else {
                    if (preg_match('/Windows NT 5.0/', $val['THistory']['user_agent'])) {
                      $os = "Windows 2000"; // Windows 2000 の処理
                    } else {
                      if (preg_match('/Windows 98/', $val['THistory']['user_agent'])) {
                        $os = "Windows 98"; // Windows 98 の処理
                      } else {
                        if (preg_match('/Windows NT 4.0/', $val['THistory']['user_agent'])) {
                          $os = "Windows NT"; // Windows NT の処理
                        } else {
                          if (preg_match('/Windows 95/', $val['THistory']['user_agent'])) {
                            $os = "Windows 95"; // Windows 95 の処理
                          } else {
                            if (preg_match('/Windows NT 5.2/',
                                $val['THistory']['user_agent']) && preg_match('/Phone/',
                                $val['THistory']['user_agent'])) {
                              $os = "Windows Phone"; // Windows Phone の処理
                            } else {
                              if (preg_match('/Xbox/', $val['THistory']['user_agent'])) {
                                $os = "Xbox"; // Xbox の処理
                              } else {
                                if (preg_match('/^.*\s([A-Za-z]*BSD)/',
                                  $val['THistory']['user_agent'])) {
                                  $os = "FreeBSD"; // BSD 系の処理
                                } else {
                                  if (preg_match('/SunOS/',
                                    $val['THistory']['user_agent'])) {
                                    $os = "Solaris"; // Solaris の処理
                                  } else {
                                    if (preg_match('/iPhone/',
                                      $val['THistory']['user_agent'])) {
                                      $os = "iPhone"; // iPhone の処理
                                      if (preg_match('/iPhone OS/',
                                        $val['THistory']['user_agent'])) {
                                        $myKey = "iPhone OS ";
                                        $myEnd = " like";
                                        $myStart = strpos($val['THistory']['user_agent'],
                                            $myKey) + strlen($myKey);
                                        $myEnd = strpos($val['THistory']['user_agent'],
                                          $myEnd);
                                        $myDifference = $myEnd - $myStart;
                                        $version = mb_substr($val['THistory']['user_agent'],
                                          $myStart, $myDifference);
                                        $os = " iPhone(ver." . str_replace('_',
                                            '.', $version) . ")";
                                      }
                                    } else {
                                      if (preg_match('/iPad/',
                                        $val['THistory']['user_agent'])) {
                                        $os = "iPad"; // iPad の処理
                                        if (preg_match('/ OS/',
                                          $val['THistory']['user_agent'])) {
                                          $myKey = " OS ";
                                          $myEnd = " like";
                                          $myStart = strpos($val['THistory']['user_agent'],
                                              $myKey) + strlen($myKey);
                                          $myEnd = strpos($val['THistory']['user_agent'],
                                            $myEnd);
                                          $myDifference = $myEnd - $myStart;
                                          $version = mb_substr($val['THistory']['user_agent'],
                                            $myStart, $myDifference);
                                          $os = " iPad(ver." . str_replace('_',
                                              '.', $version) . ")";
                                        }
                                      } else {
                                        if (preg_match('/iPod/',
                                          $val['THistory']['user_agent'])) {
                                          $os = "iPod"; // iPod の処理
                                        } else {
                                          if (preg_match('/Mac|PPC/',
                                            $val['THistory']['user_agent'])) {
                                            $os = "Mac OS"; // Macintosh の処理
                                          } else {
                                            if (preg_match('/Android/',
                                              $val['THistory']['user_agent'])) {
                                              $myKey = "Android";
                                              $End = ";";
                                              $terminal = "";
                                              $terminalEnd = "Build";
                                              preg_match('/(Android\s[0-9.]*);(\sja\-jp;)?\s([a-zA-Z0-9\-]*)?/',
                                                $val['THistory']['user_agent'],
                                                $match);

                                              $os = (isset($match[1]) ? $match[1] : "Android") . ' (' . (isset($match[3]) ? $match[3] : "unknown") . ')';
                                              $os = trim($os);
                                            } else {
                                              if (preg_match('/Firefox/' && '/Mobile/') && !preg_match('/Android/',
                                                  $val['THistory']['user_agent'])) {
                                                $os = "FireFox Mobile"; // FireFoxOS の処理
                                              } else {
                                                if (preg_match('/Firefox/' && '/Tablet/') && !preg_match('/Android/',
                                                    $val['THistory']['user_agent'])) {
                                                  $os = "FireFox Tablet"; // FireFoxOS の処理
                                                } else {
                                                  if (preg_match('/BlackBerry/',
                                                      $val['THistory']['user_agent']) || preg_match('/BB10/' && '/Android/',
                                                      $val['THistory']['user_agent'])) {
                                                    $os = "BlackBerry"; // BlackBerry の処理
                                                  } else {
                                                    if (preg_match('/Ubuntu/',
                                                      $val['THistory']['user_agent'])) {
                                                      $os = "Ubuntu"; // Ubuntu の処理
                                                    } else {
                                                      if (preg_match('/Linux Mint/',
                                                        $val['THistory']['user_agent'])) {
                                                        $os = "Linux Mint"; // Linux Mint の処理
                                                      } else {
                                                        if (preg_match('/Fedora/',
                                                          $val['THistory']['user_agent'])) {
                                                          $os = "Fedora"; // Fedora の処理
                                                        } else {
                                                          if (preg_match('/Gentoo/',
                                                            $val['THistory']['user_agent'])) {
                                                            $os = "Gentoo"; // Gentoo の処理
                                                          } else {
                                                            if (preg_match('/Linux/',
                                                              $val['THistory']['user_agent'])) {
                                                              $os = "Linux"; // Linux の処理
                                                            } else {
                                                              $os = "unknown"; // 上記以外 OS の処理
                                                            }
                                                          }
                                                        }
                                                      }
                                                    }
                                                  }
                                                }
                                              }
                                            }
                                          }
                                        }
                                      }
                                    }
                                  }
                                }
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
    return $os;
  }

  /**
   * //
   *  csv,browser出力(一覧画面)
   * @param  csv出力内容
   * @return  browserの種類
   * */
  private function _userAgentCheckBrowser($val)
  {
    Configure::write('debug', 0);
    $browser = 'unknown';
    if (strpos($val['THistory']['user_agent'], 'MSIE')) {
      preg_match('/MSIE.([1-9][0-9]*|0)(.[0-9]+)(.[0-9]+)?(.[0-9]+)?(.[0-9]+)?/', $val['THistory']['user_agent'],
        $match);
      $version = str_replace("MSIE", "", $match[0]);
      $browser = "IE(ver." . $version . ")";
    } else {
      if (preg_match('/sleipnir/i', $val['THistory']['user_agent'])) {
        preg_match('/Sleipnir.([1-9][0-9]*|0)(.[0-9]+)(.[0-9]+)?(.[0-9]+)?(.[0-9]+)?/',
          $val['THistory']['user_agent'],
          $match);
        $version = str_replace("Sleipnir/", "", $match[0]);
        $browser = "Sleipnir(ver." . $version . ")";
      } else {
        if (preg_match('/lunascape/i', $val['THistory']['user_agent'])) {
          preg_match('/Lunascape.([1-9][0-9]*|0)(.[0-9]+)(.[0-9]+)?(.[0-9]+)?(.[0-9]+)?/',
            $val['THistory']['user_agent'], $match);
          $version = str_replace("Lunascape/", "", $match[0]);
          $browser = "Lunascape(ver." . $version . ")";
        } else {
          if (strpos($val['THistory']['user_agent'], 'Trident/7')) {
            preg_match('/rv:.([1-9][0-9]*|0)(.[0-9]+)(.[0-9]+)?(.[0-9]+)?(.[0-9]+)?/',
              $val['THistory']['user_agent'],
              $match);
            $version = str_replace("rv:", "", $match[0]);
            $browser = "IE(ver." . $version . ")";
          } else {
            if (preg_match('/edge/i', $val['THistory']['user_agent'])) {
              preg_match('/Edge.([1-9][0-9]*|0)(.[0-9]+)(.[0-9]+)?(.[0-9]+)?(.[0-9]+)?/',
                $val['THistory']['user_agent'], $match);
              $version = str_replace("Edge/", "", $match[0]);
              $browser = "Edge(ver." . $version . ")";
            } else {
              if (preg_match('/opera mini/i', $val['THistory']['user_agent'])) {
                preg_match('/Opera Mini.([1-9][0-9]*|0)(.[0-9]+)(.[0-9]+)?(.[0-9]+)?(.[0-9]+)?/',
                  $val['THistory']['user_agent'], $match);
                $version = str_replace("Opera Mini/", "", $match[0]);
                $myEnd = "/";
                $myEnd = strpos($version, $myEnd);
                $version = mb_substr($version, 0, $myEnd);
                $browser = "Opera Mini(ver." . $version . ")";
              } else {
                if (preg_match('/opera/i', $val['THistory']['user_agent'])) {
                  preg_match('/Opera.([1-9][0-9]*|0)(.[0-9]+)(.[0-9]+)?(.[0-9]+)?(.[0-9]+)?/',
                    $val['THistory']['user_agent'], $match);
                  $version = str_replace("Opera/", "", $match[0]);
                  $browser = "Opera(ver." . $version . ")";
                } else {
                  if (preg_match('/opr/i', $val['THistory']['user_agent'])) {
                    preg_match('/OPR.([1-9][0-9]*|0)(.[0-9]+)(.[0-9]+)?(.[0-9]+)?(.[0-9]+)?/',
                      $val['THistory']['user_agent'], $match);
                    $version = str_replace("OPR/", "", (isset($match[0]) ? $match[0] : "unknown"));
                    $browser = "Opera(ver." . $version . ")";
                  } else {
                    if (preg_match('/vivaldi/i', $val['THistory']['user_agent'])) {
                      preg_match('/Vivaldi.([1-9][0-9]*|0)(.[0-9]+)(.[0-9]+)?(.[0-9]+)?(.[0-9]+)?/',
                        $val['THistory']['user_agent'], $match);
                      $version = str_replace("Vivaldi/", "", $match[0]);
                      $browser = "Vivaldi(ver." . $version . ")";
                    } else {
                      if (preg_match('/firefox/i', $val['THistory']['user_agent'])) {
                        preg_match('/Firefox.([1-9][0-9]*|0)(.[0-9]+)(.[0-9]+)?(.[0-9]+)?(.[0-9]+)?/',
                          $val['THistory']['user_agent'], $match);
                        $version = str_replace("Firefox/", "", $match[0]);
                        $browser = "Firefox(ver." . $version . ")";
                      } else {
                        if (preg_match('/palemoon/i', $val['THistory']['user_agent'])) {
                          preg_match('/Palemoon.([1-9][0-9]*|0)(.[0-9]+)(.[0-9]+)?(.[0-9]+)?(.[0-9]+)?/',
                            $val['THistory']['user_agent'], $match);
                          $version = str_replace("Palemoon/", "", $match[0]);
                          $browser = "Palemoon(ver." . $version . ")";
                        } else {
                          if (preg_match('/phantomjs/i', $val['THistory']['user_agent'])) {
                            preg_match('/PhantomJs.([1-9][0-9]*|0)(.[0-9]+)(.[0-9]+)?(.[0-9]+)?(.[0-9]+)?/',
                              $val['THistory']['user_agent'], $match);
                            $version = str_replace("PhantomJs/", "", $match[0]);
                            $browser = "PhantomJs(ver." . $version . ")";
                          } else {
                            if (preg_match('/jp.co.yahoo.ipn.appli/i',
                              $val['THistory']['user_agent'])) {
                              preg_match('/jp.co.yahoo.ipn.appli.([1-9][0-9]*|0)(.[0-9]+)(.[0-9]+)?(.[0-9]+)?(.[0-9]+)?/',
                                $val['THistory']['user_agent'], $match);
                              $version = str_replace("jp.co.yahoo.ipn.appli/", "",
                                $match[0]);
                              $browser = "YahooJapanブラウザ(ver." . $version . ")";
                            } else {
                              if (preg_match('/jp.co.yahoo.ymail/i',
                                $val['THistory']['user_agent'])) {
                                preg_match('/jp.co.yahoo.ymail.([1-9][0-9]*|0)(.[0-9]+)(.[0-9]+)?(.[0-9]+)?(.[0-9]+)?/',
                                  $val['THistory']['user_agent'], $match);
                                $version = str_replace("jp.co.yahoo.ymail/", "",
                                  $match[0]);
                                $browser = "YahooJapanブラウザ(ver." . $version . ")";
                              } else {
                                if (preg_match('/Chrome/i',
                                    $val['THistory']['user_agent']) && !preg_match('/samsungbrowser/i',
                                    $val['THistory']['user_agent'])) {
                                  preg_match('/Chrome.([1-9][0-9]*|0)(.[0-9]+)(.[0-9]+)?(.[0-9]+)?(.[0-9]+)?/',
                                    $val['THistory']['user_agent'], $match);
                                  $version = str_replace("Chrome/", "", $match[0]);
                                  $browser = "Chrome(ver." . $version . ")";
                                } else {
                                  if (preg_match('/crios/i',
                                      $val['THistory']['user_agent']) && !preg_match('/samsungbrowser/i',
                                      $val['THistory']['user_agent'])) {
                                    preg_match('/CriOS.([1-9][0-9]*|0)(.[0-9]+)(.[0-9]+)?(.[0-9]+)?(.[0-9]+)?/',
                                      $val['THistory']['user_agent'], $match);
                                    $version = str_replace("CriOS/", "", $match[0]);
                                    $browser = "Chrome(ver." . $version . ")";
                                  } else {
                                    if (preg_match('/blackberry/i',
                                        $val['THistory']['user_agent']) || preg_match('/bb10/i',
                                        $val['THistory']['user_agent'])) {
                                      preg_match('/Version.([1-9][0-9]*|0)(.[0-9]+)(.[0-9]+)?(.[0-9]+)?(.[0-9]+)?/',
                                        $val['THistory']['user_agent'], $match);
                                      $version = str_replace("Version/", "",
                                        $match[0]);
                                      $browser = "標準ブラウザ(ver." . $version . ")";
                                    } else {
                                      if (preg_match('/safari/i',
                                          $val['THistory']['user_agent']) && preg_match('/android/i',
                                          $val['THistory']['user_agent'])) {
                                        $browser = "標準ブラウザ";
                                      } else {
                                        if (preg_match('/samsungbrowser/i',
                                            $val['THistory']['user_agent']) && preg_match('/android/i',
                                            $val['THistory']['user_agent'])) {
                                          $browser = "標準ブラウザ";
                                        } else {
                                          if (preg_match('/safari/i',
                                              $val['THistory']['user_agent']) && !preg_match('/android/i',
                                              $val['THistory']['user_agent'])) {
                                            preg_match('/Version.([1-9][0-9]*|0)(.[0-9]+)(.[0-9]+)?(.[0-9]+)?(.[0-9]+)?/',
                                              $val['THistory']['user_agent'],
                                              $match);
                                            if (isset($match[0])) {
                                              $version = str_replace("Version/",
                                                "", $match[0]);
                                            } else {
                                              $version = 'unknown';
                                            }
                                            $browser = "Safari(ver." . $version . ")";
                                          } else {
                                            if (preg_match('/iphone/i',
                                                $val['THistory']['user_agent']) || preg_match('/ipad/i',
                                                $val['THistory']['user_agent'])) {
                                              $browser = "Safari";
                                            }
                                          }
                                        }
                                      }
                                    }
                                  }
                                }
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
    return $browser;
  }

  private function printProcessTimetoLog($prefix)
  {
    //microtimeを.で分割
    $arrTime = explode('.', microtime(true));
    //日時＋ミリ秒
    $this->log($prefix . '::PROCESS_TIME ' . date('Y-m-d H:i:s', $arrTime[0]) . '.' . $arrTime[1], LOG_DEBUG);
  }
}
