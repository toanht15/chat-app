<?php
/**
 * HistoriesController controller.
 * 履歴一覧画面
 */
class HistoriesController extends AppController {
  public $helpers = ['Time'];
  public $uses = ['MUser', 'MCompany', 'MCustomer', 'TCampaign', 'THistory', 'THistoryChatLog', 'THistoryStayLog', 'THistoryShareDisplay'];
  public $paginate = [
    'THistory' => [
      'limit' => 100,
      'order' => [
        'THistory.access_date' => 'desc',
        'THistory.id' => 'desc'
      ],
      'fields' => [
        'THistory.*',
        'THistoryChatLog.*',
      ],
      'joins' => [
        [
          'type' => 'INNER',
          'table' => '('.
            '  SELECT'.
            '    chat.*,'.
            '    ( CASE WHEN chat.cus > 0 AND chat.sry = 0 AND chat.cmp = 0 THEN "拒否" WHEN chat.cus > 0 AND chat.sry > 0 THEN "未入室" ELSE "" END ) AS type'.
            '  FROM ('.
            '    SELECT'.
            '      t_histories_id, COUNT(*) AS count,'.
            '      MIN(achievement_flg) AS achievementFlg,'.
            '      SUM(CASE WHEN message_type = 98 THEN 1 ELSE 0 END) cmp,'.
            '      SUM(CASE WHEN message_type = 4 THEN 1 ELSE 0 END) sry,'.
            '      SUM(CASE WHEN message_type = 1 THEN 1 ELSE 0 END) cus'.
            '    FROM t_history_chat_logs '.
            '    GROUP BY t_histories_id ORDER BY t_histories_id'.
            '  ) AS chat'.
          ')',
          'alias' => 'THistoryChatLog',
          'conditions' => [
            'THistoryChatLog.t_histories_id = THistory.id'
          ]
        ]
      ],
      'conditions' => []
    ],
    'THistoryStayLog' => []
  ];

  public function beforeFilter(){
    parent::beforeFilter();
    $ret = $this->MCompany->read(null, $this->userInfo['MCompany']['id']);
    $orList = [];
    if ( !empty($ret['MCompany']['exclude_ips']) ) {
      foreach( explode("\n", trim($ret['MCompany']['exclude_ips'])) as $v ){
        if ( preg_match("/^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}$/", trim($v)) ) {
          $orList[] = "INET_ATON('".trim($v)."') = INET_ATON(THistory.ip_address)";
          continue;
        }
        $ips = $this->MCompany->cidrToRange(trim($v));
        $list = [];
        if ( count($ips) === 2 ) {
          $list[] = "INET_ATON('".trim($ips[0])."') <= INET_ATON(THistory.ip_address)";
          $list[] = "INET_ATON('".trim($ips[1])."') >= INET_ATON(THistory.ip_address)";
        }
        $orList[] = $list;
      }
    }

    $this->paginate['THistory']['conditions'] = [
      'THistory.m_companies_id' => $this->userInfo['MCompany']['id']
    ];

    if ( !empty($orList) ) {
      $this->paginate['THistory']['conditions']['NOT'] = ['OR' => $orList];
    }

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
    // 成果の名称リスト
    $this->set('achievementType', Configure::read('achievementType'));
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
          'visitors_id = '.$tHistoryData['THistory']['visitors_id'],
          'm_companies_id' => $this->userInfo['MCompany']['id'],
          'id <= '.$tHistoryData['THistory']['id']
        ]
      ];
      $tHistoryCountData = $this->THistory->find('first', $params);

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
      "キャンペーン",
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
      // キャンペーン
      $row['campaign'] = $val->campaign;
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

  public function outputCSVOfContents(){
    Configure::write('debug', 0);
    if ( !isset($this->request->data['History']['outputData'] ) ) return false;

    $name = "sinclo-chat-history";
    $ret = (array) json_decode($this->request->data['History']['outputData'] );

    // ヘッダー
    $csv[] = [
      "日時",
      "IPアドレス",
      "会社名",
      "名前",
      "プラットフォーム",
      "ブラウザ",
      "送信元ページ",
      "送信日時",
      "送信種別",
      "送信者",
      "メッセージ",
      "担当者"
     ];

     if ( $this->coreSettings[C_COMPANY_USE_CHAT] ) {
      $csv[0][] = "";
     }
     else {
      $csv[0][] = "担当者";
    }

    foreach($ret as $val){
      $row = [];
      // 日時
      $dateTime = preg_replace("/[\n,]+/", " ", $val->date);
      $row['date'] = $dateTime;
      $info = preg_split("/[\n,]+/", $val->ip);
      // IPアドレス
      $row['ip'] = $info[2];
      // 会社名
      $row['company'] = $info[0];
      // 名前
      $row['name'] = $info[1];
      // OS
      $ua = preg_split("/[\n,]+/", $val->useragent);
      $row['os'] = $ua[0];
      //ブラウザ
      $row['browser'] = $ua[1];
      // 参照元URL
      $row['referrer'] = $val->referrer;

      //id取得
      $id = $this->THistory->find('first',array(
        'conditions' => array(
          'created' => $row['date']))
      );

      $chatLog = $this->_getChatLog($id['THistory']['id']);
      foreach($chatLog as $key => $value) {
        // 送信日時
        $row['pageCnt'] = preg_replace("/[\n,]+/", " ", $value['THistoryChatLog']['created']);
        // 送信種別
        if($value['THistoryChatLog']['message_type'] == 1) {
          $row['transmissionKind'] = '訪問者';
          $row['transmissionPerson'] = '';
        }
        if($value['THistoryChatLog']['message_type'] == 2) {
          $row['transmissionKind'] = 'オペレーター';
          $users = $value['THistoryChatLog']['display_name'];
          $row['transmissionPerson'] = $users;
        }
        if($value['THistoryChatLog']['message_type'] == 3) {
          $row['transmissionKind'] = 'オートメッセージ';
          $companyName = $this->MCompany->find('all',[
          'conditions' => [
            'id' => $this->userInfo['MCompany']['id']]]);
          $row['transmissionPerson'] = $companyName[0]['MCompany']['company_name'];
        }
        if($value['THistoryChatLog']['message_type'] == 98) {
          continue;
        }
        // チャットメッセージ
        $row['message'] = $value['THistoryChatLog']['message'];
        // チャット担当者
        $users = preg_replace("/[\n,]+/", ", ", $val->user);
        $row['user'] = $users;
        $csv[] = $row;
      }
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

    // チャットのみ表示との切り替え
    if ( !$this->coreSettings[C_COMPANY_USE_CHAT] || strcmp($type, 'false') === 0 ) {
      $this->paginate['THistory']['joins'][0]['type'] = "LEFT";
    }
    else {
      $this->paginate['THistory']['joins'][0]['type'] = "INNER";
    }

    $data = '';

    //履歴検索機能
    if($this->request->is('post')) {
      $this->Session->write('Thistory', $this->data);
    }

    if ($this->Session->check('Thistory')) {
      $data = $this->Session->read('Thistory');
      //検索期間ワード(ex,今日、今月など)
      if(isset($data['History']['period'])){
        //カスタム検索の場合
        if(mb_strlen($data['History']['period'])==0){
          $data['History']['period'] = "カスタム";
        }
        //今月、先月、過去一か月間の検索の場合
        else if(mb_strlen($data['History']['period'])==4 || mb_strlen($data['History']['period'])==8){
          $data['History']['period'] = substr($data['History']['period'], 2);
        }
        //今月、先月の検索の場合
        else if(mb_strlen($data['History']['period'])==7){
          $data['History']['period'] = substr($data['History']['period'], 5);
        }
        //それ以外の検索の場合
        else{
        $data['History']['period'] = substr($data['History']['period'], 4);
        }
      }
      //ipアドレス
      if(isset($data['History']['ip_address'])) {
        $this->paginate['THistory']['conditions'][] = ['THistory.ip_address like' =>'%'.$data['History']['ip_address'].'%'];
      }
      //開始日
      if(!empty($data['History']['start_day'])) {
        $this->paginate['THistory']['conditions'][] = ['THistory.access_date >=' => $data['History']['start_day'].' 00:00:00'];
      }
      //終了日
      if(!empty($data['History']['finish_day'] )) {
        $this->paginate['THistory']['conditions'][] = ['THistory.access_date <=' => $data['History']['finish_day'].' 23:59:59'];
      }
      //担当者
      if(isset($data['History']['responsible_name'])) {
        //ユーザーid取得
        $muserData = $this->MUser->find('first',[
          'conditions' => [
            'MUser.user_name like' => '%'.$data['History']['responsible_name'].'%',
            'MUser.m_companies_id' => $this->userInfo['MCompany']['id']]]);
        //ユーザーidからチャット内容検索
        if(!empty($muserData)){
          $messageDatas = $this->THistoryChatLog->find('all',[
            'conditions' => [
              'THistoryChatLog.m_users_id' => $muserData['MUser']['id']]]);
          $messageDatasBox = [];
          foreach($messageDatas as $messageData) {
            $messageDatasBox[]=$messageData['THistoryChatLog']['t_histories_id'];
          }
          $this->paginate['THistory']['conditions'][] = ['THistory.id' => $messageDatasBox];
        }
      }
      //チャット内容
      if(isset($data['History']['message'])) {
        //チャット内容検索
        $chatDatas = $this->THistoryChatLog->find('all',[
          'conditions' => [
            'THistoryChatLog.message like' => '%'.$data['History']['message'].'%',]]);
        $chatDatasBox = [];
        foreach($chatDatas as $chatData) {
          $chatDatasBox[]=$chatData['THistoryChatLog']['t_histories_id'];
        }
        $this->paginate['THistory']['conditions'][] = ['THistory.id' => $chatDatasBox];
      }
      //会社名、名前、電話、メール検索
      if(isset($data['History']['company_name']) || isset($data['History']['customer_name']) || isset($data['History']['telephone_number']) || isset($data['History']['mail_address']) ) {
        $userCond = [];
        if (isset($data['History']['company_name'])) { $userCond[] = ['MCustomer.informations LIKE' => '%'.$data['History']['company_name'].'%']; }
        if (isset($data['History']['customer_name'])) { $userCond[] = [' MCustomer.informations LIKE' => '%'.$data['History']['customer_name'].'%']; }
        if (isset($data['History']['telephone_number'])) { $userCond[] = ['MCustomer.informations LIKE' => '%'.$data['History']['telephone_number'].'%']; }
        if (isset($data['History']['mail_address'])) { $userCond[] = [' MCustomer.informations LIKE' => '%'.$data['History']['mail_address'].'%']; }
        $allusers = $this->MCustomer->find('all', [
          'fields' => '*',
          'conditions' => [
            'MCustomer.m_companies_id' => $this->userInfo['MCompany']['id'],
            'OR' => $userCond
          ]
        ]);
        $ret=[];
        foreach($allusers as $alluser) {
          $settings = json_decode($alluser['MCustomer']['informations']);
          if($data['History']['company_name'] != '' && !(isset($settings->company) && strstr($settings->company,$data['History']['company_name']))) {
            continue;
          }
          if($data['History']['customer_name'] != '' && !(isset($settings->name) && strstr($settings->name,$data['History']['customer_name']))) {
            continue;
          }
          if($data['History']['telephone_number'] != '' && !(isset($settings->tel) && strstr($settings->tel,$data['History']['telephone_number']))) {
            continue;
          }
          if($data['History']['mail_address'] != '' && !(isset($settings->mail) && strstr($settings->mail,$data['History']['mail_address']))) {
            continue;
          }
          $ret[]=$alluser['MCustomer']['visitors_id'];
        }

        $this->paginate['THistory']['conditions'][] = ['THistory.visitors_id' => $ret];
      }
    }
    $historyList = $this->paginate('THistory');
    // TODO 良いやり方が無いか模索する
    $historyIdList = [];
    $customerIdList = [];
    foreach($historyList as $val){
      $historyIdList[] = $val['THistory']['id'];
      $customerIdList[$val['THistory']['visitors_id']] = true;
    }
    $tHistoryStayLogList = $this->THistoryStayLog->find('all', [
      'fields' => [
        't_histories_id',
        'url AS firstURL',
        'COUNT(t_histories_id) AS count '
      ],
      'conditions' => [
        't_histories_id' => $historyIdList
      ],
      'group' => 't_histories_id'
    ]);

    $stayList = [];
    foreach($tHistoryStayLogList as $val){
      $stayList[$val['THistoryStayLog']['t_histories_id']] = [
        'THistoryStayLog' => [
          'firstURL' => $val['THistoryStayLog']['firstURL'],
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
               '   AND t_histories_id IN (' . implode(",", $historyList) .')'.
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
        'THistory.m_companies_id' => $this->userInfo['MCompany']['id']
      ],
      'recursive' => -1
    ];
    $ret = $this->THistory->find('all', $params);
    $chat = [];
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
    /*chat内容のCSV出力のため追加*/
    $this->THistoryChatLog->virtualFields['display_name'] = 'concat(MUser.display_name,"さん")';
    return $this->THistoryChatLog->find('all', $params);
  }

  /* *
   * 検索画面
   * @return void
   * */
  public function remoteOpenEntryForm() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $this->data = $this->Session->read('Thistory');
    //範囲が全期間の場合
    if(empty($this->data['History']['start_day']) && empty($this->data['History']['finish_day'])) {
      $today = date("Y/m/d");
      $this->request->data['History']['start_day'] = $today;
      $this->request->data['History']['finish_day'] = $today;
    }
    // const
    $this->render('/Elements/Histories/remoteSearchCustomerInfo');
  }

 /* *
   * Session削除
   * @return void
   * */
  public function clearSession() {
    $this->Session->delete('Thistory');
    $this->redirect(['controller' => 'Histories', 'action' => 'index']);
  }
}