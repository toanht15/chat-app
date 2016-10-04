<?php
/**
 * HistoriesController controller.
 * 履歴一覧画面
 */
class HistoriesController extends AppController {
  public $helpers = ['Time'];
  public $uses = ['MUser', 'MCustomer', 'TCampaign', 'THistory', 'THistoryChatLog', 'THistoryStayLog', 'THistoryShareDisplay'];
  public $paginate = [
    'THistory' => [
      'limit' => 100,
      'order' => [
        'THistory.created' => 'desc',
        'THistory.id' => 'desc'
      ],
      'fields' => [
        'THistory.*',
        'THistoryChatLog.*',
        'THistoryStayLog.*',
        'MCustomer.*'
      ],
      'joins' => [
        [
          'type' => 'INNER',
          'table' => '(SELECT t_histories_id, COUNT(t_histories_id) AS count FROM t_history_chat_logs GROUP BY t_histories_id)',
          'alias' => 'THistoryChatLog',
          'conditions' => [
            'THistoryChatLog.t_histories_id = THistory.id'
          ]
        ],
        [
          'type' => 'LEFT',
          'table' => '(SELECT t_histories_id, COUNT(t_histories_id) AS count FROM t_history_stay_logs WHERE del_flg != 1 GROUP BY t_histories_id)',
          'alias' => 'THistoryStayLog',
          'conditions' => [
            'THistoryStayLog.t_histories_id = THistory.id'
          ]
        ]
      ],
      'conditions' => [
        'THistory.del_flg !=' => 1
      ]
    ],
    'THistoryStayLog' => []
  ];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->paginate['THistory']['conditions'] = [
      'THistory.del_flg !=' => 1,
      'THistory.m_companies_id' => $this->userInfo['MCompany']['id']
    ];
    $this->set('siteKey', $this->userInfo['MCompany']['company_key']);
    $this->set('title_for_layout', '履歴');
  }

  /* *
   * 一覧画面
   * @return void
   * */
  public function index() {
    $isChat = 'true';
    if ( !empty($this->params->query['isChat']) ) {
      $isChat = $this->params->query['isChat'];
    }
    $this->_setList($isChat);
  }

  public function remoteGetCustomerInfo() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $data = [];

    if ( !empty($this->params->query['historyId']) ) {
      $params = [
        'fields' => '*',
        'conditions' => [
          'id' => $this->params->query['historyId']
        ]
      ];
      $tHistoryData = $this->THistory->coFind('first', $params);

      $params = [
        'fields' => [
          'count(*) as cnt'
        ],
        'conditions' => [
          'visitors_id = '.$tHistoryData['THistory']['visitors_id'],
          'id <= '.$tHistoryData['THistory']['id']
        ]
      ];
      $tHistoryCountData = $this->THistory->coFind('first', $params);

      $mCusData = ['MCustomer' => []];
      if ( !empty($tHistoryData['THistory']['visitors_id']) ) {
        $mCusData = $this->MCustomer->getCustomerInfoForVisitorId($this->userInfo['MCompany']['id'], $tHistoryData['THistory']['visitors_id']);
      }

      $data = am($tHistoryData, ['THistoryCount' => $tHistoryCountData[0]], $mCusData);
    }
    $this->set('data', $data);
    // 顧客情報のテンプレート
    $this->set('infoList', $this->_getInfomationList());
    return $this->render('/Elements/Histories/remoteGetCustomerInfo');
  }

  public function remoteSaveCustomerInfo() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $ret = true;
    $data = $this->params->query;
    if ( !isset($data['visitorsId']) ) return false;

    $inputData = []; // 顧客情報リスト
    $saveData = [
      'MCustomer' => [
        'visitors_id' => $data['visitorsId'],
        'm_companies_id' => $this->userInfo['MCompany']['id']
      ]
    ]; // 保存データ

    if ( isset($data['customerId']) ) {
      $saveData['MCustomer']['id'] = $data['customerId'];
    }
    else {
      $this->MCustomer->create();
    }

    // 顧客情報のテンプレートを取得
    $infoList = $this->_getInfomationList();
    foreach($infoList as $key => $val) {
      $inputData[$key] = ( isset($data['saveData'][$key]) ) ? $data['saveData'][$key] : "";
    }

    $saveData['MCustomer']['informations'] = $this->jsonEncode($inputData);

    $this->MCustomer->begin();
    $this->MCustomer->set($saveData);

    if ( $this->MCustomer->save() ) {
      $this->MCustomer->commit();
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
    }
    else {
      $this->MCustomer->rollback();
      $ret = false;
      $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
    }
    return new CakeResponse(['body' => json_encode($ret)]);
  }

  public function remoteGetChatLogs() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';

    $ret = $this->_getChatLog($this->params->query['historyId']);
    $this->set('THistoryChatLog', $ret);
    return $this->render('/Elements/Histories/remoteGetChatLogs');
  }

  public function remoteGetStayLogs() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';

    $historyId = $this->params->query['historyId'];

    $params = [
      'fields' => '*',
      'conditions' => [
      'THistoryStayLog.t_histories_id' => $historyId,
      'THistoryStayLog.del_flg !=' => 1
      ],
      'recursive' => -1
    ];
    $ret = $this->THistoryStayLog->find('all', $params);
    $this->set('THistoryStayLog', $ret);
    return $this->render('/Elements/Histories/remoteGetStayLogs');
  }

  public function outputCSVOfHistory(){
    Configure::write('debug', 0);
    if ( !isset($this->request->data['History']['outputData'] ) ) return false;

    $name = "sinclo-history";
    $ret = (array) json_decode($this->request->data['History']['outputData'] );

    // ラベル

    // ヘッダー
    $csv[] = [
      "日時",
      "訪問ユーザ",
      "プラットフォーム",
      "ブラウザ",
      "参照元URL",
      "閲覧ページ数",
      "滞在時間"
     ];

     if ( $this->coreSettings[C_COMPANY_USE_CHAT] ) {
      $csv[0][] = "チャット担当者";
     }
     else {
      $csv[0][] = "担当者";
    }

    foreach($ret as $val){
      $row = [];

      // 日時
      $dateTime = preg_replace("/[\n,]+/", " ", $val->date);
      $row['date'] = $dateTime;
      // IPアドレス
      $row['ip'] = $val->ip;
      // OS
      $ua = preg_split("/[\n,]+/", $val->useragent);
      $row['os'] = $ua[0];
      // ブラウザ
      $row['browser'] = $ua[1];
      // 参照元URL
      $row['referrer'] = $val->referrer;
      // 閲覧ページ数
      $row['pageCnt'] = $val->pageCnt;
      // 滞在時間
      $row['visitTime'] = $val->visitTime;
      // チャット担当者
      $users = preg_replace("/[\n,]+/", ", ", $val->user);
      $row['user'] = $users;
      $csv[] = $row;
    }

    $this->_outputCSV($name, $csv);
  }

  public function outputCSVOfChat($id = null){
    Configure::write('debug', 0);

    if (empty($id)) return false;
    $name = "sinclo-chat-history";
    $ret = $this->_getChatLog($id);

    // ヘッダー
    $csv[] = [
      "発行日時",
      "送信種別",
      "送信者",
      "メッセージ"
     ];

    foreach($ret as $val){
      $row = [];
      $date = date('Y/m/d H:i:s', strtotime($val['THistoryChatLog']['created'])); // 発行日時
      // $date = date('Y年m月d日 H時i分s秒', strtotime($val['THistoryChatLog']['created'])); // 発行日時
      $message = $val['THistoryChatLog']['message'];
      switch($val['THistoryChatLog']['message_type']){
        case 1: // 企業側からの送信
          $row = $this->_setData($date, "訪問者", "", $message);
          break;
        case 2: // 訪問者側からの送信
          $row = $this->_setData($date, "オペレーター", $val['MUser']['display_name'], $message);
          break;
        case 3: // オートメッセージ
          $row = $this->_setData($date, "オートメッセージ", $this->userInfo['MCompany']['company_name'], $message);
          break;
        case 98: // 入室メッセージ
        case 99: // 退室メッセージ
          $row = $this->_setData($date, "通知メッセージ", "", " - ".$val['MUser']['display_name']."が".$message."しました - ");
          break;
      }
      $csv[] = $row;
    }
    $this->_outputCSV($name, $csv);
  }

  private function _outputCSV($name, $csv = []){
    $this->layout = null;

    //メモリ上に領域確保
    $fp = fopen('php://temp/maxmemory:'.(5*1024*1024),'a');

    foreach($csv as $row){
      fputcsv($fp, $row);
    }

    //ビューを使わない
    $this->autoRender = false;

    $filename = date("YmdHis")."_".$name;

    //download()内ではheader("Content-Disposition: attachment; filename=hoge.csv")を行っている
    $this->response->download($filename.".csv");

    //ファイルポインタを先頭へ
    rewind($fp);

    //リソースを読み込み文字列を取得する
    $csv = stream_get_contents($fp);

    //Content-Typeを指定
    $this->response->type('csv');

    //CSVをエクセルで開くことを想定して文字コードをSJIS-win
    $csv = mb_convert_encoding($csv,'SJIS-win','utf8');

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
  private function _setData($date = "", $type = "", $name = "", $message = "") {
    return [
      $date, // 発行日時
      $type, // 送信種別
      $name, // 送信者
      $message // メッセージ
    ];
  }

  /**
   * 顧客情報のデータリスト
   * @return array
   * */
  private function _getInfomationList(){
    return [
      'company' => '会社名',
      'name' => '名前',
      'tel' => '電話番号',
      'mail' => 'メールアドレス',
      'memo' => 'メモ'
    ];
  }

  private function _setList($type=true){
    // ユーザー情報の取得
    $this->paginate['THistory']['joins'][] = [
      'type' => 'LEFT',
      'table' => '(SELECT visitors_id, informations FROM m_customers WHERE m_customers.m_companies_id = ' . $this->userInfo['MCompany']['id'] . ')',
      'alias' => 'MCustomer',
      'conditions' => [
        'MCustomer.visitors_id = THistory.visitors_id'
      ]
    ];
    // チャットのみ表示との切り替え
    if ( !$this->coreSettings[C_COMPANY_USE_CHAT] || strcmp($type, 'false') === 0 ) {
      $this->paginate['THistory']['joins'][0]['type'] = "LEFT";
    }
    else {
      $this->paginate['THistory']['joins'][0]['type'] = "INNER";
    }

    $this->Session->write("histories.joins", $this->paginate['THistory']['joins'][0]['type']);
    $historyList = $this->paginate('THistory');

    $this->set('userList', $historyList);
    $this->set('historyList', $historyList);
    $this->set('chatUserList', $this->_getChatUser($historyList)); // チャット担当者リスト
    $this->set('groupByChatChecked', $type);
    $this->set('campaignList', $this->TCampaign->getList());
  }

  /**
   * // TODO ブラッシュアップできそう
   *  チャット応対リストの取得
   * @param　array $historyList // 履歴リスト
   * @return array $ret 結果
   * */
  private function _getChatUser($historyList){
    if ( empty($historyList) ) return []; // 履歴が取得できなかったら
    $params = [
      'fields' => [
        'THistory.id',
        'MUser.display_name'
      ],
      'joins' => [
        [
          'type' => 'INNER',
          'table' => '(SELECT * FROM t_history_chat_logs '.
               ' WHERE m_users_id IS NOT NULL '.
               '   AND t_histories_id <= ' . $historyList[0]['THistory']['id'].
               '   AND t_histories_id >= ' . $historyList[count($historyList) - 1]['THistory']['id'].
               ' GROUP BY t_histories_id, m_users_id'.
               ')',
          'alias' => 'THistoryChatLog',
          'conditions' => [
            'THistoryChatLog.t_histories_id = THistory.id'
          ]
        ],
        [
          'type' => 'INNER',
          'table' => 'm_users',
          'alias' => 'MUser',
          'conditions' => [
            'THistoryChatLog.m_users_id = MUser.id'
          ]
        ]
      ],
      'conditions' => [
        'THistory.m_companies_id' => $this->userInfo['MCompany']['id'],
        'THistory.del_flg !=' => 1
      ],
      'recursive' => -1
    ];
    $ret = $this->THistory->find('all', $params);
    foreach((array)$ret as $val){
      if ( isset($chat[$val['THistory']['id']]) ) {
        $chat[$val['THistory']['id']] .= "\n".$val['MUser']['display_name']."さん";
      }
      else {
        $chat[$val['THistory']['id']] = $val['MUser']['display_name']."さん";
      }
    }
    return $chat;
  }

  private function _getChatLog($historyId){
    $params = [
      'fields' => [
        'MUser.display_name',
        'THistoryChatLog.*'
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
        ]
      ],
      'order' => 'THistoryChatLog.created',
      'recursive' => -1
    ];
    return $this->THistoryChatLog->find('all', $params);
  }

}
