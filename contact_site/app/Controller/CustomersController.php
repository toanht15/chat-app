<?php
/**
 * CustomersController controller.
 * モニタリング機能
 */
class CustomersController extends AppController {
  public $uses = ['THistory', 'THistoryChatLog', 'MUser', 'MWidgetSetting', 'TDictionary'];

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
        "recursive" => -1
      ]
    );

    $list = [];
    foreach ( (array)$dictionaryList as $key => $val ) {
      $list[] = [
        'id' => $key,
        'label' => $val
      ];
    }

    $widgetSettings = $this->MWidgetSetting->coFind('first', null);

    // ユーザーの最新情報を取得
    $mUser = $this->MUser->coFind('first', ['fields', '*', 'recursive' => -1]);

    $this->request->data['settings'] = [];

    if ( !empty($mUser['MUser']['settings']) ) {
      $mySettings = json_decode($mUser['MUser']['settings']);
      if ( isset($mySettings->sendPattarn) && strcmp($mySettings->sendPattarn, "true") === 0 ) {
      $this->request->data['settings']['sendPattarn'] = true;
      }
      else if ( isset($mySettings->sendPattarn) && strcmp($mySettings->sendPattarn, "false" === 0) ) {
      $this->request->data['settings']['sendPattarn'] = false;
      }
    }

    $styleSettings = [];
    if ( isset($widgetSettings['MWidgetSetting']['style_settings']) ) {
      $styleSettings = json_decode($widgetSettings['MWidgetSetting']['style_settings']);
    }
    $this->set('widgetSettings', $widgetSettings['MWidgetSetting']['style_settings']);


    $this->set('dictionaryList', $list);
    $this->set('responderList', $this->MUser->coFind('list',["fields" => ["MUser.id", "MUser.display_name"], "recursive" => -1]));
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
      'accessId' => 'アクセスID',
      'ipAddress' => '訪問ユーザ',
      'ua' => 'ユーザー環境',
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
    // データーベースへ保存
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
}
