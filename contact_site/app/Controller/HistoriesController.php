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
        'THistory.*'
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
      $this->Session->write('authenticity',$this->params->query['isChat']);
    }
    $isChat = $this->Session->read('authenticity');
    $this->_searchProcessing(3);
    // 成果の名称リスト
    $this->set('achievementType', Configure::read('achievementType'));
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
      $csv[0][] = "成果";
      $csv[0][] = "チャット担当者";
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

      // 成果
     if ( $this->coreSettings[C_COMPANY_USE_CHAT] ) {
        $row['achievement'] = $val->achievement;
        // 担当者
        $users = preg_replace("/[\n,]+/", ", ", $val->user);
        $row['user'] = $users;
      }
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
      "訪問日時",
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

    foreach($ret as $val){
      $row = [];
      // 日時
      $dateTime = preg_replace("/[\n,]+/", " ", $val->date);
      $row['date'] = $dateTime;
      //訪問ユーザ
      $row['ip'] = $val->ip;
      // OS
      $ua = preg_split("/[\n,]+/", $val->useragent);
      $row['os'] = $ua[0];
      //ブラウザ
      $row['browser'] = $ua[1];
      $id = $val->id;
      $chatLog = $this->_getChatLog($id);

      foreach($chatLog as $key => $value) {
        //送信元ページ
        if($value['THistoryChatLog']['message_type'] == 1) {
          $row['sourcePage'] = $value['THistoryStayLog']['url'];
        }
        else{
          $row['sourcePage'] = '';
        }
        $users = preg_replace("/[\n,]+/", ", ", $val->user);
        // 送信日時
        $row['pageCnt'] =  substr(preg_replace("/[\n,]+/", " ", $value['THistoryChatLog']['created']),0,20);

        // 送信種別
        if($value['THistoryChatLog']['message_type'] == 1) {
          $row['transmissionKind'] = '訪問者';
          $row['transmissionPerson'] = '';
        }
        if($value['THistoryChatLog']['message_type'] == 2) {
          $row['transmissionKind'] = 'オペレーター';
          $row['transmissionPerson'] = $value['MUser']['display_name']."さん";
        }
        if($value['THistoryChatLog']['message_type'] == 3) {
          $row['transmissionKind'] = 'オートメッセージ';
          $row['transmissionPerson'] = $this->userInfo['MCompany']['company_name'];
        }
        if($value['THistoryChatLog']['message_type'] == 4) {
          $row['transmissionKind'] = 'Sorryメッセージ';
          $row['transmissionPerson'] = $this->userInfo['MCompany']['company_name'];
        }
        if($value['THistoryChatLog']['message_type'] == 98 || $value['THistoryChatLog']['message_type'] == 99) {
          $row['transmissionKind'] = '通知メッセージ';
          $row['transmissionPerson'] = "";
          $value['THistoryChatLog']['message'] = '-'.$value['MUser']['display_name'].'が'.$value['THistoryChatLog']['message'].'しました-';
        }
        // チャットメッセージ
        $row['message'] = $value['THistoryChatLog']['message'];
        // チャット担当者
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
      "送信日時",
      "送信種別",
      "送信者",
      "メッセージ"
     ];

    foreach($ret as $val){
      $row = [];
      $date = date('Y/m/d H:i:s', strtotime($val['THistoryChatLog']['created'])); // 送信日時
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
  private function _getInfomationList(){
    return [
      'company' => '会社名',
      'name' => '名前',
      'tel' => '電話番号',
      'mail' => 'メールアドレス',
      'memo' => 'メモ'
    ];
  }

  private function _setListByChat($type, $data, $visitorsIds = []){
   $cond = [];

    return $cond;
  }

  /**
   *  入力された条件にマッチした顧客のIDを取得
   * @param $data array 入力データ
   * @return array 顧客のIDリスト
   * */
  private function _searchCustomer($data){
    $visitorsIds = [];
    $userCond = [
      'MCustomer.m_companies_id' => $this->userInfo['MCompany']['id'],
    ];
    $keys = ['company_name' => 'company', 'customer_name' => 'name', 'telephone_number' => 'tel', 'mail_address' => 'mail'];

    $allusers = $this->MCustomer->find('all', [
      'fields' => '*',
      'conditions' => $userCond
    ]);

    foreach($allusers as $alluser) {
      $setFlg = false;
      $settings = (array)json_decode($alluser['MCustomer']['informations']);
      foreach ($keys as $key => $val) {
        if ( isset($data[$key]) && $data[$key] != "" ) {
          if ( !(isset($settings[$val]) && $settings[$val] != "" && strstr($settings[$val], $data[$key])) ) {
            $setFlg = false;
            continue 2;
          }
          else {
            $setFlg = true;
          }
        }
      }
      if ( $setFlg ) {
        $visitorsIds[$alluser['MCustomer']['visitors_id']] = true;
      }
    }
    return array_keys($visitorsIds);
  }

  private function _searchProcessing($type){
    $data = $this->Session->read('Thistory');
    $companyStartDay = date("Y/m/d",strtotime($this->userInfo['MCompany']['created']));
    $historyConditions = [
      'History'=>['company_start_day' => $companyStartDay,
      'ip_address' => '','company_name' => '','customer_name' => '',
      'telephone_number' => '','mail_address' => ''],
      'THistoryChatLog'=>['responsible_name' => '','achievement_flg' => '','message' => '']
    ];
    switch ($type) {
      //履歴一覧ボタンをクリックした場合
      case 1:
        $historyConditions['History']['start_day'] = date("Y/m/d",strtotime("-30 day"));
        $historyConditions['History']['finish_day'] = date("Y/m/d");
        $historyConditions['History']['period'] = '過去一ヵ月間';
      break;
      //条件クリアをクリックした場合
      case 2:
        $historyConditions['History']['start_day'] = $data['History']['start_day'];
        $historyConditions['History']['finish_day'] =$data['History']['finish_day'];
        $historyConditions['History']['period'] = $data['History']['period'];
      break;
      //デフォルト
      default:
        $historyConditions = $data;
        if($this->request->is('post')) {
          $historyConditions = $this->request->data;
          $historyConditions['History']['company_start_day'] = $companyStartDay;
        }
      break;
    }
    $this->Session->write('Thistory',$historyConditions);
  }

  private function _setList($type=true){
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

      //ipアドレス
      if(isset($data['History']['ip_address']) && $data['History']['ip_address'] !== "") {
        $this->paginate['THistory']['conditions']['THistory.ip_address LIKE'] = '%'.$data['History']['ip_address'].'%';
      }
      //開始日
      if(!empty($data['History']['start_day'])) {
        $this->paginate['THistory']['conditions']['THistory.access_date >='] = $data['History']['start_day'].' 00:00:00';
      }
      //終了日
      if(!empty($data['History']['finish_day'] )) {
        $this->paginate['THistory']['conditions']['THistory.access_date <='] = $data['History']['finish_day'].' 23:59:59';
      }

      /* 顧客情報に関する検索条件 会社名、名前、電話、メール検索 */
      if((isset($data['History']['company_name']) && $data['History']['company_name'] !== "") || (isset($data['History']['customer_name']) && $data['History']['customer_name'] !== "") || (isset($data['History']['telephone_number']) && $data['History']['telephone_number'] !== "") || (isset($data['History']['mail_address']) && $data['History']['mail_address'] !== "") ) {
        $visitorsIds = $this->_searchCustomer($data['History']);
        $this->paginate['THistory']['conditions']['THistory.visitors_id'] = $visitorsIds;
        $chatCond['visitors_id'] = $visitorsIds;
      }

      // 担当者に関する検索条件
      $joinType = 'LEFT';
      if ( isset($data['THistoryChatLog']['responsible_name']) && $data['THistoryChatLog']['responsible_name'] !== "" ) {
        $userCond['display_name LIKE'] = "%".$data['THistoryChatLog']['responsible_name']."%";
        $joinType = 'INNER';
      }

      /* チャットに関する検索条件 チャット担当者、チャット内容、チャット成果 */

      // 検索条件に成果がある場合
      if ( isset($data['THistoryChatLog']['achievement_flg']) && $data['THistoryChatLog']['achievement_flg'] !== "" ) {
        $chatLogCond['chat.achievementFlg'] = $data['THistoryChatLog']['achievement_flg'];
      }

      // 検索条件にメッセージがある場合
      if ( isset($data['THistoryChatLog']['message']) && $data['THistoryChatLog']['message'] !== "" ) {
        // メッセージ条件に対応した履歴のリストを取得するサブクエリを作成
        $message = $this->THistoryChatLog->getDataSource();
        $hisIdsForMessageQuery = $message->buildStatement(
          [
            'table' => $message->fullTableName($this->THistoryChatLog),
            'alias' => 'thcl',
            'fields' => ['t_histories_id'],
            'conditions' => [
              'message LIKE' => "%".$data['THistoryChatLog']['message']."%"
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

      if ( !empty($data['THistoryChatLog']) && !empty(array_filter($data['THistoryChatLog'])) ) {
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
        if ( $this->coreSettings[C_COMPANY_USE_CHAT] ) {
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

    // 3) チャットに関する検索条件
    if ( $this->coreSettings[C_COMPANY_USE_CHAT] ) {

      $dbo2 = $this->THistoryChatLog->getDataSource();
      $chatStateList = $dbo2->buildStatement(
        [
          'table' => $dbo2->fullTableName($this->THistoryChatLog),
          'alias' => 'THistoryChatLog',
          'fields' => [
            't_histories_id, COUNT(*) AS count',
            'MAX(achievement_flg) AS achievementFlg',
            'SUM(CASE WHEN message_type = 98 THEN 1 ELSE 0 END) cmp',
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
          'table' => '(SELECT t_histories_id, COUNT(*) AS count, MAX(achievement_flg) AS achievementFlg, SUM(CASE WHEN message_type = 98 THEN 1 ELSE 0 END) cmp, SUM(CASE WHEN message_type = 4 THEN 1 ELSE 0 END) sry, SUM(CASE WHEN message_type = 1 THEN 1 ELSE 0 END) cus FROM t_history_chat_logs AS THistoryChatLog GROUP BY t_histories_id ORDER BY t_histories_id)',
          'alias' => 'chat',
          'fields' => [
            'chat.*',
            '( CASE  WHEN chat.cus > 0 AND chat.sry = 0 AND chat.cmp = 0 THEN "未入室" WHEN chat.cus > 0 AND chat.sry > 0 THEN "拒否" ELSE "" END ) AS type',
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
      if ( strcmp($type, 'false') === 0 && !(!empty($data['THistoryChatLog']) && !empty(array_filter($data['THistoryChatLog']))) ) {
        $joinToChat['type'] = "LEFT";
      }
      else {
        $joinToChat['type'] = "INNER";
      }

      $this->paginate['THistory']['fields'][] = 'THistoryChatLog.*';

      $this->paginate['THistory']['joins'][] = $joinToChat;
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
   * 検索画面
   * @return void
   * */
  public function remoteSearchCustomerInfo() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $this->data = $this->Session->read('Thistory');

    // 成果種別リスト
    $this->set('achievementType', Configure::read('achievementType'));
    // const
    $this->render('/Elements/Histories/remoteSearchCustomerInfo');
  }

   /* *
   * Session削除(条件クリア)
   * @return void
   * */
  public function portionClearSession() {
    $this->_searchProcessing(2);
    $this->redirect(['controller' => 'Histories', 'action' => 'index']);
  }

   /* *
   * Session削除(一覧画面)
   * @return void
   * */
  public function clearSession() {
    $this->Session->delete('Thistory');
    $this->Session->delete('authenticity');
    $this->_searchProcessing(1);
    $this->redirect(['controller' => 'Histories', 'action' => 'index']);
  }

public function sample() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    //$this->data = $this->Session->read('Thistory');

    // 成果種別リスト
    $this->set('achievementType', Configure::read('achievementType'));
    // const
    $this->render('/Elements/Histories/sample');
  }
}