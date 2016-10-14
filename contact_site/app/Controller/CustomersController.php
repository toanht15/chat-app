<?php
/**
 * CustomersController controller.
 * モニタリング機能
 */
class CustomersController extends AppController {
  public $uses = ['THistory', 'THistoryChatLog', 'TCampaign', 'MCompany', 'MUser', 'MCustomer', 'MWidgetSetting', 'MChatNotification', 'TDictionary'];

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

    /* 個人設定を読み込む */
    // ユーザーの最新情報を取得
    $mUser = $this->MUser->coFind('first', ['fields', '*', 'recursive' => -1]);
    if ( !empty($mUser['MUser']['settings']) ) {
      $mySettings = json_decode($mUser['MUser']['settings']);
      if ( isset($mySettings->sendPattarn) && strcmp($mySettings->sendPattarn, "true") === 0 ) {
        $this->request->data['settings']['sendPattarn'] = true;
      }
      else if ( isset($mySettings->sendPattarn) && strcmp($mySettings->sendPattarn, "false" === 0) ) {
        $this->request->data['settings']['sendPattarn'] = false;
      }
    }

    /* ウィジェットの設定を読み込む */
    // ウィジェット設定を取得
    $widgetSettings = $this->MWidgetSetting->coFind('first', null);
    $this->set('widgetSettings', $widgetSettings['MWidgetSetting']['style_settings']);

    /* 企業ユーザーリストを取得 */
    $this->set('responderList', $this->MUser->coFind('list',["fields" => ["MUser.id", "MUser.display_name"], "recursive" => -1]));

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
      'ipAddress' => '訪問ユーザ',
      'ua' => 'プラットフォーム／ブラウザ',
      'stayCount' => '訪問回数',
      'time' => 'アクセス日時',
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
      $mUser = $this->MUser->coFind('first', ['fields', '*', 'recursive' => -1]);
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
          'THistory.del_flg !=' => 1,
          'THistory.m_companies_id' => $this->userInfo['MCompany']['id'],
          'THistory.tab_id' => $this->params->query['tabId']
        ],
        'joins' => [
          [
            'type' => 'LEFT',
            'table' => 't_history_stay_logs',
            'alias' => 'THistoryStayLog',
            'conditions' => [
              'THistoryStayLog.t_histories_id = THistory.id',
              'THistoryStayLog.del_flg !=' => 1
            ]
          ]
        ],
        'sort' => ['THistory.id' => 'DESC'],
        'recursive' => -1
      ];
      $ret = $this->THistory->find('all', $params);
    }

    $this->set('THistoryStayLog', $ret);
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

  /**
   * ビュー表示用
   * @return void
   * */
  private function _viewElement(){
    /* キャンペーン情報取得 */
    $this->set('campaignList', $this->jsonEncode($this->TCampaign->getList()));
    /* 通知設定取得 */
    $this->_getNotificationSettingList();
    /* 簡易入力情報取得 */
    $this->_getDictionaryList();
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
    $this->set('cType', $cType);
    $this->set('tabStatusList', Configure::read('tabStatusList'));
    $this->set('tabStatusStrList', Configure::read('tabStatusStrList'));
  }

  /**
   * 簡易入力情報取得
   * @return void
   * */
  private function _getDictionaryList(){
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
          'TDictionary.m_companies_id' => $this->userInfo['MCompany']['id']
        ],
        'order' => [
          'sort' => 'asc',
          'id' => 'asc'
        ],
        "recursive" => -1
      ]
    );

    $list = [];
    foreach ( (array)$dictionaryList as $key => $val ) {
      $list[] = [
        'id' => $key,
        'label' => $this->setChatValiable($val)
      ];
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

}
