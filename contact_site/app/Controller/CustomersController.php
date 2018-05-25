<?php
/**
 * CustomersController controller.
 * モニタリング機能
 */

App::uses('MFileTransferSettingController', 'Controller');

class CustomersController extends AppController {
  public $uses = [
    'MCompany', 'MUser', 'MCustomer', 'MWidgetSetting', 'MChatNotification', 'MChatSetting',
    'THistory', 'THistoryChatLog', 'TCampaign', 'TDocument', 'TDictionary', 'TDictionaryCategory'
  ];

  public $tmpLabelHideList = ["accessId", "ipAddress", "customer", "ua", "stayCount", "time", "campaign", "stayTime", "page", "title", "referrer"];

  public function beforeRender(){
    $this->set('siteKey', $this->userInfo['MCompany']['company_key']);
    $this->set('muserId', $this->userInfo['id']);
    $this->set('title_for_layout', 'リアルタイムモニタ');
  }

  /* *
   * 一覧画面
   * @return void
   * */
  public function index() {

    $this->request->data['settings'] = [];

    /* チャット基本情報を読み込む */
    $chatSetting = $this->MChatSetting->coFind('first', [], false);
    $scFlg = ( !empty($chatSetting['MChatSetting']['sc_flg']) ) ? intval($chatSetting['MChatSetting']['sc_flg']) : C_SC_DISABLED;
    $this->set('scFlg', $scFlg);

    if(isset($this->coreSettings[C_COMPANY_USE_SEND_FILE]) && $this->coreSettings[C_COMPANY_USE_SEND_FILE]) {
      $controller = new MFileTransferSettingController();
      $this->set('allowExtensions', $controller->getAllowExtensions());
    }

    /* 個人設定を読み込む */
    // ユーザーの最新情報を取得
    $mUser = $this->MUser->coFind('first', ['fields', 'conditions' => ['id' => $this->userInfo['id']], 'recursive' => -1]);
    if ( !empty($mUser['MUser']['settings']) ) {
      $mySettings = json_decode($mUser['MUser']['settings']);
      // チャット送信方法
      $this->request->data['settings']['sendPattarn'] = ( isset($mySettings->sendPattarn) && strcmp($mySettings->sendPattarn, "true") === 0 ) ? true : false;
    }
    // チャット
    if ( strcmp($scFlg, C_SC_ENABLED) === 0 ) {
      // チャット同時対応数制限
      $this->set("scNum", ( !empty($mySettings->sc_num) ) ? $mySettings->sc_num : 0);
    }

    /* ウィジェットの設定を読み込む */
    // ウィジェット設定を取得
    $widgetSettings = $this->MWidgetSetting->coFind('first', null);
    $this->set('widgetSettings', $widgetSettings['MWidgetSetting']['style_settings']);

    /* 企業ユーザーリストを取得 */
    $this->set('responderList', $this->MUser->coFind('list',["fields" => ["MUser.id", "MUser.display_name"], "recursive" => -1]));

    if(isset($this->coreSettings[C_COMPANY_REF_COMPANY_DATA]) && $this->coreSettings[C_COMPANY_REF_COMPANY_DATA]) {
      $this->set('token', $this->userInfo['accessToken']);
    } else {
      $this->set('token', '');
    }

    $this->_viewElement();

  }

  /* *
   * モニタリング画面
   * @return void
   * */
  public function frame() {
    $this->layout = 'frame';
    $this->set('query', $this->request->query);
  }

  /* *
   * モニタリング画面(資料)
   * @return void
   * */
  public function docFrame() {
    $this->layout = 'frame';
    $this->set('docData', $this->TDocument->read(null, $this->params->query['docId']));
    $this->set('tabInfo', $this->params->query['tabInfo']);
    return $this->render('/Customers/docFrame');
  }

  /* *
   * モニタリング画面(画面キャプチャ共有用)
   * @return void
   * */
  public function laFrame() {
    $this->layout = 'frame';
    $this->set('query', $this->params->query);
    return $this->render('/Customers/laFrame');
  }

  /* *
   * モニタリング画面
   * @return void
   * */
  public function monitor() {
    $this->layout = 'frame';
    Configure::write('debug', 0);
  }

  /* *
   * 表示項目名設定
   * @return void
   * */
  public function remoteCreateSetting() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = null;
    $labelList = [
      'accessId' => 'ID',
      'ipAddress' => 'IPアドレス',
      'customer' => '訪問ユーザ',
      'ua' => 'プラットフォーム／ブラウザ',
      'stayCount' => '訪問回数',
      'time' => 'アクセス日時',
      'campaign' => 'キャンペーン',
      'stayTime' => '滞在時間',
      'page' => '閲覧ページ数',
      'title' => '閲覧中ページ',
      'referrer' => '参照元URL'
    ];
    $labelHideList = [];
    $selectedLabelList = [];
    $dataList = [];
    if ( isset($this->request->query['labelHideList']) ) {
      $dataList = (array)json_decode($this->request->query['labelHideList']);
    }
    foreach ( $dataList as $key => $val ) {
      $labelHideList[$key] = $labelList[$key];
      if ( $val ) {
        $selectedLabelList[] = $key;
      }
    }

    $this->set('labelHideList', $labelHideList);
    $this->set('selectedLabelList', $selectedLabelList);
    return $this->render('/Customers/remoteCreateSetting');
  }

  /* ユーザーの個別設定を保存する */
  public function remoteChageSetting() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = null;
    $ret = false;

    if ( isset($this->params->data['type']) && isset($this->params->data['value']) ) {
      $newSetting = $this->params->data;

      // データーベースより取得
      $mUser = $this->MUser->coFind('first', ['fields', 'conditions' => ['id' => $this->userInfo['id']], 'recursive' => -1]);
      $mySettings = [];
      if ( !empty($mUser['MUser']['settings']) ) {
        $mySettings = (array)json_decode($mUser['MUser']['settings']);
      }
      $mySettings[$newSetting['type']] = $newSetting['value'];
      $mUser['MUser']["settings"] = $this->jsonEncode($mySettings);
      // データーベースへ保存
      $this->MUser->begin();

      if ($this->MUser->save($mUser)) {
      $this->MUser->commit();
      $ret = true;
      }
      else {
      $this->MUser->rollback();
      }
    }
    return new CakeResponse(['body' => json_encode($ret)]);
  }

  public function remoteSaveSetting() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = null;
    $tmpLabelHideList = [];
    $labelHideList = [];
    if (!empty($this->params->query['labelHideList'])) {
      foreach ( (array)json_decode($this->params->query['labelHideList'] ) as $key => $value ) {
        if ( $value ) {
          $tmpLabelHideList[$key] = $value;
        }
      }
    }

    $labelHideList = $this->jsonEncode($tmpLabelHideList);

    $saveData = [
      'MUser' => [
        'id' => $this->userInfo['id'],
        'operation_list_columns' => $labelHideList
      ]
    ];

    $this->MUser->begin();
    if ( $this->MUser->save($saveData) ) {
      $this->MUser->commit();
      $this->userInfo['operation_list_columns'] = $labelHideList;
      $this->Session->write('global.userInfo', $this->userInfo);
    }
    else {
      $labelHideList = [];
      $this->MUser->rollback();
    }

    return new CakeResponse(['body' => $labelHideList]);

  }

  public function remoteGetCusInfo() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = null;
    $data = "";
    // 空チェック
    if ( !empty($this->request->data['v']) ) {
      // データーベースへ保存
      $visitorId = $this->request->data['v'];

      $mCustomer = $this->MCustomer->find('first',
                [
                  'conditions' => [
                    'visitors_id' => $visitorId,
                    'm_companies_id' => $this->userInfo['MCompany']['id']
                  ],
                  'order' => ['id' => 'desc']
                ]
              );

      if ( !empty($mCustomer) ) {
        $data = json_decode($mCustomer['MCustomer']['informations']);
      }
    }
    return new CakeResponse(['body' => json_encode($data)]);
  }

  /**
   * タブIDから履歴IDを返す
   * */
  public function remoteGetHistoriesId() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = null;
    $historyId = "";
    if ( !empty($this->params->query['tabId']) ) {
      $historyData = $this->THistory->find('first', [
        'fields' => 'id',
        'conditions' => [
          'tab_id' => $this->params->query['tabId']
        ],
        'recursive' => -1
      ]);
      if ( !empty($historyData['THistory']['id']) ) {
        $historyId = $historyData['THistory']['id'];
      }
    }
    return new CakeResponse(['body' => $historyId]);
  }

  public function remoteSaveCusInfo() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = null;
    $ret = false;
    // 空チェック
    if ( !empty($this->request->data['v']) && !empty($this->request->data['i'])) {
      // データーベースへ保存
      $visitorId = $this->request->data['v'];
      $value = $this->jsonEncode($this->request->data['i']);

      $saveData = $this->MCustomer->find('first',
                [
                  'conditions' => ['visitors_id' => $visitorId],
                  'order' => ['id' => 'desc']
                ]
              );

      $this->MCustomer->begin();
      if ( empty($saveData) ) {
        $this->MCustomer->create();
        $saveData['MCustomer'] = [
          'visitors_id' => $visitorId
        ];
      }
      $saveData['MCustomer']['m_companies_id'] = $this->userInfo['MCompany']['id'];
      $saveData['MCustomer']['informations'] = $value;

      if ( $this->MCustomer->save($saveData) ) {
        $this->MCustomer->commit();
        $ret = true;
      }
      else {
        $this->MCustomer->rollback();
      }

    }
    return new CakeResponse(['body' => json_encode($ret)]);
  }

  public function remoteGetStayLogs() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';

    $ret = [];
    if ( isset($this->params->query['tabId']) ) {
      $params = [
        'fields' => '*',
        'conditions' => [
          'THistory.m_companies_id' => $this->userInfo['MCompany']['id'],
          'THistory.tab_id' => $this->params->query['tabId']
        ],
        'joins' => [
          [
            'type' => 'LEFT',
            'table' => 't_history_stay_logs',
            'alias' => 'THistoryStayLog',
            'conditions' => [
              'THistoryStayLog.t_histories_id = THistory.id'
            ]
          ]
        ],
        'sort' => ['THistory.id' => 'DESC'],
        'recursive' => -1
      ];
      $ret = $this->THistory->find('all', $params);
    }

    $this->set('THistoryStayLog', $ret);
    /* 除外情報取得 */
    $this->set('excludeList', $this->MCompany->getExcludeList($this->userInfo['MCompany']['id']));
    return $this->render('/Elements/Histories/remoteGetStayLogs');
  }

  public function remoteGetChatInfo(){
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = null;
    $ret = [];
    if ( !empty($this->params->query['historyId']) ) {
      $params = [
        'fields' => [
          'THistoryChatLog.message',
          'THistoryChatLog.message_type',
          'THistoryChatLog.created'
        ],
        'conditions' => [
          'THistoryChatLog.t_histories_id' => $this->params->query['historyId']
        ],
        'order' => 'created',
        'recursive' => -1
      ];
      $ret = $this->THistoryChatLog->find('all', $params);
    }
    return new CakeResponse(['body' => json_encode($ret)]);
  }

  public function remoteGetChatList(){
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = null;
    $ret = [];
    if ( !empty($this->params->query['userId'] ) ) {

      $params = [
        'fields' => [
          'THistoryChatLog.t_histories_id',
          'THistoryChatLog.created',
        ],
        'joins' => [
          [
            'type' => 'INNER',
            'table' => 't_histories',
            'alias' => 'THistory',
            'conditions' => [
              'THistory.id = THistoryChatLog.t_histories_id',
              'THistory.m_companies_id' => $this->userInfo['MCompany']['id']

            ]
          ]
        ],
        'conditions' => [
          'THistoryChatLog.visitors_id' => $this->params->query['userId']
        ],
        'order' => ['created' => 'desc'],
        'group' => 't_histories_id',
          'recursive' => -1
      ];
      $ret = $this->THistoryChatLog->find('list', $params);
    }
    return new CakeResponse(['body' => json_encode($ret)]);
  }

  public function remoteGetOldChat(){
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = null;
    $ret = [];
    if ( !empty($this->params->query['historyId'] ) ) {

      $params = [
        'fields' => [
          'THistoryChatLog.id',
          'THistoryChatLog.message',
          'THistoryChatLog.message_read_flg AS messageReadFlg',
          'THistoryChatLog.achievement_flg AS achievementFlg',
          'THistoryChatLog.message_type AS messageType',
          'THistoryChatLog.m_users_id AS userId',
          'THistoryChatLog.created'
        ],
        'conditions' => [
          'THistoryChatLog.t_histories_id' => $this->params->query['historyId']
        ],
        'order' => 'created',
        'recursive' => -1
      ];
      $chatLog = $this->THistoryChatLog->find('all', $params);
      foreach($chatLog as $val){
        $date = new DateTime($val['THistoryChatLog']['created']);
        $val['THistoryChatLog']['sort'] = substr($date->format('YmdHisu'), 0, 16);
        $ret[] = $val['THistoryChatLog'];
      }
    }
    return new CakeResponse(['body' => json_encode($ret)]);
  }

  /**
   * remoteOpenDocumentList
   * 共有する資料リストを表示
   * @return string html
   * */
  public function remoteOpenDocumentLists(){
    $this->layout = "ajax";
    $ret = [];
    $ret['tagList'] = $this->jsonEncode([1 => 'メイン', 2 => '紹介用', 3 => '営業用', 4 => '製品A', 5 => '製品B']);
    $tDocumentList = $this->TDocument->find('all', [
      'fields' => [
        'id', 'name', 'file_name', 'overview', 'tag', 'manuscript', 'settings', 'pagenation_flg', 'download_flg', 'password'
      ],
      'conditions' => [
        'm_companies_id' => $this->userInfo['MCompany']['id']
      ],
      'recursive' => -1
    ]);
    $docList = [];
    foreach ($tDocumentList as $key => $val ) {
      $tmp = $val['TDocument'];
      $tmp['thumnail'] = C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/".C_PREFIX_DOCUMENT.pathinfo(h($val['TDocument']['file_name']), PATHINFO_FILENAME).".jpg";
      $docList[] = $tmp;
    }
    $ret['documentList'] = json_encode($docList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

    return new CakeResponse(['body' => json_encode($ret)]);
  }

  /**
   * 成果更新（チャット）
   * @return void
   * */
  public function remoteChangeAchievement(){
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = null;
    $ret = ["result" => false];
    $query = $this->params->query;
    if ( !empty($query['chatId']) && isset($query['value']) && !empty($query['userId'] ) ) {
      $params = [
        'conditions' => [
          'id' => $query['chatId'],
          'm_users_id' => $query['userId'],
          'message_type' => 98,
        ],
        'recursive' => -1
      ];
      $chatLog = $this->THistoryChatLog->find('first', $params);

      if ( !empty($chatLog) ) {
        $saveData = [
          'THistoryChatLog' => [
            'id' => $query['chatId'],
            'achievement_flg' => $query['value']
          ]
        ];
        $this->THistoryChatLog->begin();
        if ( $this->THistoryChatLog->save($saveData) ) {
          $this->THistoryChatLog->commit();
          $ret["result"] = true;
        }
        else {
          $this->THistoryChatLog->rollback();
        }
      }
    }
    return new CakeResponse(['body' => json_encode($ret)]);
  }

  /**
   * 定型文選択ウィンドウ表示
   */
  public function openCategoryDictionaryEdit(){
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $this->_viewElement();
    //ポップアップの呼び出し
    $this->render('/Elements/Customers/categoryDictionary');
  }

  /**
   * 定型文選択ウィンドウから値の返却
   */
  public function resCategoryDictionaryEdit(){
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $this->_viewElement();
    return ;
  }

  /**
   * ビュー表示用
   * @return void
   * */
  private function _viewElement(){
    /* キャンペーン情報取得 */
    $this->set('campaignList', $this->jsonEncode($this->TCampaign->getList()));
    /* 通知設定取得 */
    $this->_getNotificationSettingList();
    /* 定型文カテゴリリスト取得 */
    $this->_getDictionaryCategoriesList();
    /* 定型文情報リスト取得 */
    $this->_getDictionaryList();
    /* オペレータ一覧情報取得 */
    $this->getOperatorList();
    /* 除外情報取得 */
    $this->set('excludeList', $this->MCompany->getExcludeList($this->userInfo['MCompany']['id']));
    /* 契約状態取得 */
    $cType = "full";
    if ( !$this->coreSettings[C_COMPANY_USE_SYNCLO] && $this->coreSettings[C_COMPANY_USE_CHAT] ) {
      $cType = "chatOnly";
    }
    else if ( $this->coreSettings[C_COMPANY_USE_SYNCLO] && !$this->coreSettings[C_COMPANY_USE_CHAT] ) {
      $cType = "syncOnly";
    }
    /* 表示項目設定 */
    $labelHideList = [];
    $userSetting = json_decode($this->userInfo['operation_list_columns'], true);
    foreach( $this->tmpLabelHideList as $val ){
      $labelHideList[$val] = ( isset($userSetting[$val]) ) ? true : false;
    }

    $this->set('labelHideList', $labelHideList);
    $this->set('cType', $cType);
    $this->set('tabStatusList', Configure::read('tabStatusList'));
    $this->set('tabStatusStrList', Configure::read('tabStatusStrList'));
    $this->set('tabStatusNotificationMessageList', Configure::read('tabStatusNotificationMessageList'));
    $this->set('viewableMLCompanyInfo', $this->isViewableMLCompanyInfo());
    $achievementType = Configure::read('achievementType');
    unset($achievementType[0]);
    $this->set('achievementType', $achievementType);
  }

  /**
   * 定型文カテゴリ取得
   *
   */
  private function _getDictionaryCategoriesList(){
    $params = [
        'order' => [
            'TDictionaryCategory.sort' => 'asc',
            'TDictionaryCategory.id' => 'asc'
        ],
        'fields' => [
            "TDictionaryCategory.id", "TDictionaryCategory.category_name"
        ],
        'conditions' => [
            'TDictionaryCategory.m_companies_id' => $this->userInfo['MCompany']['id']
        ],
        'recursive' => -1
    ];
    $dictionaryCategoriesList = $this->TDictionaryCategory->find('list',$params);
    $list = [];
    foreach ( (array)$dictionaryCategoriesList as $key => $val ) {
      $list[] = [
          'id' => $key,
          'label' => $val
      ];
    }
    $this->set('dictionaryCategoriesList', $list);
  }

  /**
   * 定型文情報取得
   * @return void
   * */
  private function _getDictionaryList(){
    //上記のカテゴリインデントを定型文配列に付与、定型文配列をカテゴリごとに振り分け
    $list = array();
    foreach ( (array)$this->viewVars['dictionaryCategoriesList'] as $ckey => $cval ) {
      $dictionaryList = $this->TDictionary->find('list',
      [
        "fields" => [
          "TDictionary.id", "TDictionary.word"
        ],
        "conditions" => [
          'OR' => [
            'TDictionary.type' => C_DICTIONARY_TYPE_COMP,
            [
              'TDictionary.type' => C_DICTIONARY_TYPE_PERSON,
              'TDictionary.m_users_id' => $this->userInfo['id']
            ]
          ],
            'TDictionary.m_companies_id' => $this->userInfo['MCompany']['id'],
            'TDictionary.m_category_id' => $cval['id']
        ],
        'order' => [
          'sort' => 'asc',
          'id' => 'asc'
        ],
          "recursive" => -1
        ]
      );
      $list[$ckey] = [];
      foreach ( (array)$dictionaryList as $key => $val ) {
        $list[$ckey][] = [
            'id' => $key,
            'label' => $this->setChatValiable($val)
        ];
      }
    }
    $this->set('dictionaryList', $list);
  }

  /**
   * 通知設定を取得
   * @return void
   * */
  private function _getNotificationSettingList(){
    $ret = $this->MChatNotification->coFind('all',[
      "fields" => ["type", "name", "keyword", "image"], "recursive" => -1
    ]);
    $settings = [];
    foreach($ret as $key => $val){
      $settings[$key] = $val['MChatNotification'];
    }
    $this->set('notificationList', $this->jsonEncode($settings));
  }

  public function popupFileUploadElement() {
    $this->autoRender = false;
    $this->layout = "ajax";
    $this->render('/Elements/Customers/fileUploadView');
  }

  private function getOperatorList() {
    // 一般ユーザーはリストを返さない
    $result = [];
    if(strcmp($this->userInfo['permission_level'], C_AUTHORITY_NORMAL) === 0) {
      return json_encode($result);
    }
    $list = $this->MUser->find('all', [
      'conditions' => [
        'MUser.m_companies_id' => intval($this->userInfo['MCompany']['id']),
        'NOT' => [
          'MUser.permission_level' => C_AUTHORITY_SUPER,
          'MUser.del_flg' => 1
        ]
      ]
    ]);
    foreach($list as $index => $value) {
      $result[intval($value['MUser']['id'])] = [
        'id' => intval($value['MUser']['id']),
        'user_name' => $value['MUser']['display_name'],
        'display_name' => $value['MUser']['display_name'],
        'mail_address' => $value['MUser']['mail_address'],
        'permission_level' => intval($value['MUser']['permission_level'])
      ];
    }
    $this->set('userList', $result);
  }

}
