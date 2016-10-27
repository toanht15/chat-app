<?php
/**
 * HistoriesController controller.
 * 履歴一覧画面
 */
class HistoriesController extends AppController {
  public $helpers = ['Time'];
  public $uses = ['MUser', 'MCustomer', 'THistory', 'THistoryChatLog', 'THistoryStayLog', 'THistoryShareDisplay'];
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
    $this->data = $this->Session->read('thistory');
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

    //履歴検索機能
    if($this->request->is('post')) {
      $start = $this->data['start_day'];
      $finish = $this->data['finish_day'];
      $ip = $this->data['History']['ip_address'];
      $company = $this->data['History']['company_name'];
      $name = $this->data['History']['customer_name'];
      $tel = $this->data['History']['telephone_number'];
      $mail = $this->data['History']['mail_address'];

      $conditions = ['THistory.ip_address like' =>'%'.$ip.'%'];
      if($start != '' ) {
        $conditions += ['THistory.access_date >=' => $start];
      }
      if($finish != '' ) {
      $conditions += ['THistory.access_date <=' => $finish];
      }

      $allusers = $this->MCustomer->find('all');
      $ret=[];
      foreach($allusers as $alluser) {
        $settings = json_decode($alluser['MCustomer']['informations']);
        if($company != '' && !strstr($settings->company,$company)) {
          continue;
        }
        if($name != '' && !strstr($settings->name,$name)) {
          continue;
        }
        if($tel != '' && !strstr($settings->tel,$tel)) {
          continue;
        }
        if($mail != '' && !strstr($settings->mail,$mail)) {
          continue;
        }
        $ret[]=$alluser['MCustomer']['visitors_id'];
      }
      $conditions['THistory.visitors_id'] = $ret;
      $historyList = $this->paginate('THistory',$conditions);
    }
    else {
    $historyList = $this->paginate('THistory');
    }

    // チャット担当者リスト
    $chat = [];
    if ( !empty($historyList) ) {
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
    }
    $this->set('userList', $historyList);
    $this->set('historyList', $historyList);
    $this->set('chatUserList', $chat);
    $this->set('groupByChatChecked', $type);
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

  /* *
   * 登録,更新画面
   * @return void
   * */
  public function remoteOpenEntryForm() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $this->data = $this->Session->read('thistory');
    if(empty($this->data['start_day']) || empty($this->data['finish_day'])) {
      $today = date("Y/m/d");
      $this->request->data['start_day'] = $today;
      $this->request->data['finish_day'] = $today;
    }
    // const
    $this->render('/Elements/Histories/remoteEntry');
  }

    /* *
   * 保存処理
   * @return void
   * */
  public function remoteSearchEntryForm() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $saveData = [];
    $errorMessage = [];

     $this->paginate['THistory']['joins'][] = [
      'type' => 'LEFT',
      'table' => '(SELECT visitors_id, informations FROM m_customers WHERE m_customers.m_companies_id = ' . $this->userInfo['MCompany']['id'] . ')',
      'alias' => 'MCustomer',
      'conditions' => [
        'MCustomer.visitors_id = THistory.visitors_id'
      ]
    ];

        if($this->request->is('ajax')) {
      $start = $this->data['start_day'];
      $finish = $this->data['finish_day'];
      $ip = $this->data['ip_address'];
      $company = $this->data['company_name'];
      $name = $this->data['customer_name'];
      $tel = $this->data['telephone_number'];
      $mail = $this->data['mail_address'];

      $this->Session->write('thistory', $this->data);


      $conditions = ['THistory.ip_address like' =>'%'.$ip.'%'];
      if($start != '' ) {
        $conditions += ['THistory.access_date >=' => $start];
      }
      if($finish != '' ) {
      $conditions += ['THistory.access_date <=' => $finish];
      }

      $allusers = $this->MCustomer->find('all');
      $ret=[];
      foreach($allusers as $alluser) {
        $settings = json_decode($alluser['MCustomer']['informations']);
        if($company != '' && !strstr($settings->company,$company)) {
          continue;
        }
        if($name != '' && !strstr($settings->name,$name)) {
          continue;
        }
        if($tel != '' && !strstr($settings->tel,$tel)) {
          continue;
        }
        if($mail != '' && !strstr($settings->mail,$mail)) {
          continue;
        }
        $ret[]=$alluser['MCustomer']['visitors_id'];
      }
      $this->log($conditions,LOG_DEBUG);
      $conditions['THistory.visitors_id'] = $ret;
      $historyList = $this->paginate('THistory',$conditions);
    }
    else {
    $historyList = $this->paginate('THistory');
    }

    // バリデーションチェックでエラーが出た場合
    /*if ( $this->TCampaign->save() ) {
      $this->TCampaign->commit();
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
    }
    else {
      $this->TCampaign->rollback();
    }
    $errorMessage = $this->TCampaign->validationErrors;
    return new CakeResponse(['body' => json_encode($errorMessage)]);
  }*/
}
 /* *
   * 登録,更新画面
   * @return void
   * */
  public function remoteClearEntryForm() {
    $this->Session->delete('thistory');
    $this->redirect(['controller' => 'Histories', 'action' => 'index']);
  }
}