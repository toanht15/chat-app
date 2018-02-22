<?php
/**
 * TChatbotScenarioController controller.
 * シナリオ設定
 * @property TransactionManager $TransactionManager
 * @property TChatbotScenario $TChatbotScenario
 * @property MWidgetSetting $MWidgetSetting
 * @property MMailTransmissionSetting $MMailTransmissionSetting
 * @property MMailTemplate $MMailTemplate
 */

App::uses('ChatbotScenarioException', 'Lib/Error');

class TChatbotScenarioController extends AppController {
  public $uses = ['TransactionManager', 'TChatbotScenario', 'TAutoMessage', 'MWidgetSetting', 'MMailTransmissionSetting', 'MMailTemplate'];
  public $paginate = [
    'TChatbotScenario' => [
      'limit' => 100,
      'order' => [
          'TChatbotScenario.sort' => 'asc',
          'TChatbotScenario.id' => 'asc'
      ],
      'fields' => ['TChatbotScenario.*'],
      'conditions' => ['TChatbotScenario.del_flg != ' => 1],
      'recursive' => -1
    ]
  ];

  public $coreSettings = null;
  public $styleSetting = [
    'common' => [
      'show_timing', 'max_show_timing_site', 'max_show_timing_page',
      'show_time', 'max_show_time', 'max_show_time_page', 'show_position', 'widget_size_type', 'title', 'show_subtitle', 'sub_title', 'show_description', 'description',
      'show_main_image', 'main_image', 'radius_ratio', 'box_shadow', 'minimize_design_type','close_button_setting','close_button_mode_type','bannertext',
      /* カラー設定styat */
      'color_setting_type','main_color','string_color','message_text_color','other_text_color','widget_border_color','chat_talk_border_color','header_background_color','sub_title_text_color','description_text_color',
      'chat_talk_background_color','c_name_text_color','re_text_color','re_background_color','re_border_color','re_border_none','se_text_color','se_background_color','se_border_color','se_border_none','chat_message_background_color',
      'message_box_text_color','message_box_background_color','message_box_border_color','message_box_border_none','chat_send_btn_text_color','chat_send_btn_background_color','widget_inside_border_color','widget_inside_border_none'
      /* カラー設定end */
    ],
    'synclo' => ['tel', 'content', 'display_time_flg', 'time_text'],
    'chat' => ['chat_radio_behavior', 'chat_trigger', 'show_name',  'chat_message_design_type', 'chat_message_with_animation', 'chat_message_copy', 'sp_show_flg', 'sp_header_light_flg', 'sp_auto_open_flg',],
  ];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', 'シナリオ設定');
    $this->chatbotScenarioActionList = Configure::read('chatbotScenarioActionList');
    $this->chatbotScenarioInputType = Configure::read('chatbotScenarioInputType');
    $this->chatbotScenarioSendMailType = Configure::read('chatbotScenarioSendMailType');
  }

  /**
   * 一覧画面
   * @return void
   * */
  public function index() {
    $this->paginate['TChatbotScenario']['conditions']['TChatbotScenario.m_companies_id'] = $this->userInfo['MCompany']['id'];
    $data = $this->paginate('TChatbotScenario');

    // 呼び出し元であるオートメッセージ情報を取得する
    foreach($data as &$item) {
      $autoMessage = $this->TAutoMessage->coFind('list', [
        'fileds' => ['id', 'name'],
        'order' => [
          'TAutoMessage.sort' => 'asc',
          'TAutoMessage.id' => 'asc'
        ],
        'conditions' => [
          'TAutoMessage.del_flg != ' => 1,
          'TAutoMessage.t_chatbot_scenario_id' => $item['TChatbotScenario']['id']
        ]
      ]);
      $item['TAutoMessage'] = $autoMessage;
    }

    $this->set('settingList', $data);
    $this->_viewElement();
  }

  /**
   * 登録画面
   * @return void
   * */
  public function add() {
    if ( $this->request->is('post') ) {
      $this->_entry($this->request->data);
    }

    // プレビュー・シミュレーター表示用ウィジェット設定の取得
    $this->request->data['widgetSettings'] = $this->_getWidgetSettings();
    $this->_viewElement();
  }

  /**
   * 更新画面
   * @return void
   * */
  public function edit($id=null) {
    if ( $this->request->is('put') ) {
      $this->_entry($this->request->data);
    }
    else {
      // 確実なデータを取得するために企業IDを指定する形とする
      $editData = $this->TChatbotScenario->coFind("all", [
        'conditions' => [
          'TChatbotScenario.id' => $id
        ]
      ]);

      if (empty($editData) || (!empty($editData) && empty($editData[0]))) {
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.notFoundId'));
        $this->redirect('/TChatbotScenario/index');
      }

      $activity = json_decode($editData[0]['TChatbotScenario']['activity']);
      foreach ($activity->scenarios as $key => &$action) {
        if ($action->actionType == C_SCENARIO_ACTION_HEARING) {
          foreach ($action->hearings as $key => &$param) {
            // 自由入力エリアの許可状態のパラメーターがない場合、デフォルトで "1" を設定する
            $param->allowInputLF = empty($param->allowInputLF) ? '1' : $param->allowInputLF;
          }
        }
        if ($action->actionType == C_SCENARIO_ACTION_SEND_MAIL) {
          // メール送信設定の取得
          if (!empty($action->mMailTransmissionId)) {
            $mailTransmissionData = $this->MMailTransmissionSetting->findById($action->mMailTransmissionId);
            $action->toAddress = explode(',', $mailTransmissionData['MMailTransmissionSetting']['to_address']);
            $action->subject = $mailTransmissionData['MMailTransmissionSetting']['subject'];
            $action->fromName = $mailTransmissionData['MMailTransmissionSetting']['from_name'];
          }
          // メールテンプレートの取得
          if (!empty($action->mMailTemplateId)) {
            $mailTemplateData = $this->MMailTemplate->findById($action->mMailTemplateId);
            if ($action->mailType == C_SCENARIO_MAIL_TYPE_CUSTOMIZE) {
              $action->template = $mailTemplateData['MMailTemplate']['template'];
            }
          }
        }
      }
      $editData[0]['TChatbotScenario']['activity'] = json_encode($activity);
      $this->request->data['TChatbotScenario'] = $editData[0]['TChatbotScenario'];
    }

    // 呼び出し元であるオートメッセージ情報を取得する
    $autoMessage = $this->TAutoMessage->coFind('list', [
      'conditions' => [
        'TAutoMessage.del_flg != ' => 1,
        'TAutoMessage.t_chatbot_scenario_id' => $id
      ]
    ]);
    $this->request->data['TAutoMessage'] = $autoMessage;

    // プレビュー・シミュレーター表示用ウィジェット設定の取得
    $this->request->data['widgetSettings'] = $this->_getWidgetSettings();
    $this->_viewElement();
  }

  /* *
   * 削除
   * @return void
   * */
  public function remoteDelete(){
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $id = (isset($this->request->data['id'])) ? $this->request->data['id'] : "";
    $ret = $this->TChatbotScenario->find('first', [
      'fields' => 'TChatbotScenario.*',
      'conditions' => [
        'TChatbotScenario.del_flg' => 0,
        'TChatbotScenario.id' => $id,
        'TChatbotScenario.m_companies_id' => $this->userInfo['MCompany']['id'],
        'TAutoMessage.t_chatbot_scenario_id' => NULL
      ],
      'joins' => [[
        'type' => 'LEFT OUTER',
        'table' => 't_auto_messages',
        'alias' => 'TAutoMessage',
        'conditions' => [
          'TChatbotScenario.id = TAutoMessage.t_chatbot_scenario_id'
        ]
      ]],
      'recursive' => -1
    ]);
    if ( count($ret) === 1 ) {
      if ( $this->TChatbotScenario->logicalDelete($id) ) {
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
      }
      else {
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
      }
    }
  }

  public function chkRemoteDelete(){
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';

    $selectedList = $this->request->data['selectedList'];
    $this->TChatbotScenario->begin();
    $res = false;

    // オートメッセージで連携済みのシナリオは削除対象から外す
    $linkedList = $this->TChatbotScenario->find('list', [
        'fields' => 'TChatbotScenario.id',
        'conditions' => [
            'TChatbotScenario.del_flg' => 0,
            'TChatbotScenario.id' => $selectedList,
            'TChatbotScenario.m_companies_id' => $this->userInfo['MCompany']['id']
        ],
        'joins' => [
          [
            'type' => 'INNER',
            'table' => 't_auto_messages',
            'alias' => 'TAutoMessage',
            'conditions' => [
              'TChatbotScenario.id = TAutoMessage.t_chatbot_scenario_id'
            ]
          ]
        ],
        'recursive' => -1
    ]);
    $targetList = array_diff($selectedList, $linkedList);

    $deletedList = [];
    foreach ($targetList as $id) {
      if ($this->TChatbotScenario->logicalDelete($id)) {
        $deletedList[] = $id;
        $res = true;
      }
    }

    if($res){
      $this->TChatbotScenario->commit();
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
    }
    else {
      $this->TChatbotScenario->rollback();
      $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
    }

    // 実際に削除したシナリオIDを返す
    echo json_encode($deletedList);
  }

  /* *
   * コピー処理
   * @return void
   * */
  public function remoteCopyEntryForm() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $selectedList = $this->request->data['selectedList'];
    // コピー元のシナリオリスト取得
    foreach($selectedList as $value){
      $copyData[] = $this->TChatbotScenario->read(null, $value);
    }
    $errorMessage = [];
    // コピー元のシナリオリストの数だけ繰り返し
    $res = true;
    foreach($copyData as $value){
      $this->TChatbotScenario->create();
      $saveData = [];
      $params = [
        'fields' => [
          'TChatbotScenario.sort'
        ],
        'conditions' => [
          'TChatbotScenario.m_companies_id' => $this->userInfo['MCompany']['id']
        ],
        'order' => [
          'TChatbotScenario.sort' => 'desc',
          'TChatbotScenario.id' => 'desc'
        ],
        'recursive' => -1
      ];
      $lastData = $this->TChatbotScenario->find('first', $params);
      if($lastData['TChatbotScenario']['sort'] === '0'
        || $lastData['TChatbotScenario']['sort'] === 0
        || $lastData['TChatbotScenario']['sort'] === null){
        // ソート順が登録されていなかったらソート順をセットする
        if(! $this->remoteSetSort()){
          $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
          return false;
        }
        // もう一度ソートの最大値を取り直す
        $lastData = $this->TChatbotScenario->find('first', $params);
      }
      $nextSort = 1;
      if (!empty($lastData)) {
        $nextSort = intval($lastData['TChatbotScenario']['sort']) + 1;
      }

      // シナリオ設定の詳細
      $activity = json_decode($value['TChatbotScenario']['activity']);
      $transaction = $this->TransactionManager->begin();
      foreach ($activity->scenarios as $key => &$action) {
        if ($action->actionType == C_SCENARIO_ACTION_SEND_MAIL) {
          // メール送信設定のコピー
          $mailTransmissionData = $this->MMailTransmissionSetting->findById($action->mMailTransmissionId);
          if (!empty($mailTransmissionData)) {
            $this->MMailTransmissionSetting->create();
            $mailTransmissionData['MMailTransmissionSetting']['id'] = null;
            $this->MMailTransmissionSetting->set($mailTransmissionData);
            $result = $this->MMailTransmissionSetting->save();
            $action->mMailTransmissionId = $this->MMailTransmissionSetting->getLastInsertId();
          }
          // メールテンプレートのコピー
          $mailTemplateData = $this->MMailTemplate->findById($action->mMailTemplateId);
          if (!empty($mailTemplateData)) {
            $this->MMailTemplate->create();
            $mailTemplateData['MMailTemplate']['id'] = null;
            $this->MMailTemplate->set($mailTemplateData);
            $result = $this->MMailTemplate->save();
            $action->mMailTemplateId = $this->MMailTemplate->getLastInsertId();
          }
        }
      }

      $saveData['TChatbotScenario']['sort'] = $nextSort;
      $saveData['TChatbotScenario']['m_companies_id'] = $value['TChatbotScenario']['m_companies_id'];
      $saveData['TChatbotScenario']['name'] = $value['TChatbotScenario']['name'].'コピー';
      $saveData['TChatbotScenario']['activity'] = json_encode($activity);
      $saveData['TChatbotScenario']['del_flg'] = $value['TChatbotScenario']['del_flg'];
      $this->TChatbotScenario->set($saveData);

      try {
        // バリデーションチェックでエラーが出た場合
        if (!$this->TChatbotScenario->validates()) {
          $res = false;
          $errorMessage = $this->TChatbotScenario->validationErrors;
          $this->TransactionManager->rollbackTransaction($transaction);
        } else
        if ($this->TChatbotScenario->save($saveData,false)) {
          $this->TransactionManager->commitTransaction($transaction);
          $this->Session->delete('dstoken');
          $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
        }
      } catch (Exception $e) {
        if ($transaction) {
          $this->TransactionManager->rollbackTransaction($transaction);
        }
        $this->log("Failed to copy scenario. => " . $e->getMessage, LOG_WARNING);
      }
    }
  }

  /**
   * シナリオ設定ソート順更新
   *
   * */
  public function remoteSaveSort(){
    Configure::write('debug', 2);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    if ( !$this->request->is('ajax') ) return false;
    if ( !empty($this->params->data['list']) ) {
      $this->TChatbotScenario->begin();
      $list = $this->params->data['list'];
      $sortNoList = $this->params->data['sortNolist'];
      sort($sortNoList);
      $this->log($list,LOG_DEBUG);
      /* 現在の並び順を取得 */
      $params = $this->paginate['TChatbotScenario'];
      $params['fields'] = [
          'TChatbotScenario.id',
          'TChatbotScenario.sort'
      ];
      $params['conditions']['TChatbotScenario.m_companies_id'] = $this->userInfo['MCompany']['id'];
      unset($params['limit']);
      $prevSort = $this->TChatbotScenario->find('list', $params);
      // 新しくソート順を設定したため、空で来ることがある
      $reset_flg = false;
      foreach($prevSort as $key => $val){
        // 設定されていない値'0'が一つでも入っていたらsortをリセット
        if($val === '0' || $val === 0 || $val === null){
          $reset_flg = true;
        }
      }
      if($reset_flg){
        // ソート順が登録されていなかったらソート順をセットする
        if(! $this->remoteSetSort()){
          $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
          return false;
        }
        $prevSort = $this->TChatbotScenario->find('list', $params);
        // この時$sortNoListは空なので作成する
        if(empty($sortNoList)){
          for ($i = 0; count($list) > $i; $i++) {
            $id = $list[$i];
            $sortNoList[] = $prevSort[$id];
          }
          sort($sortNoList);
        }
      }
      /* アップデート分の並び順を設定 */
      $ret = true;
      for ($i = 0; count($list) > $i; $i++) {
        $id = $list[$i];
        if ( isset($prevSort[$id]) ) {
          $saveData = [
              'TChatbotScenario' => [
                  'id' => $id,
                  'sort' => $sortNoList[$i]
              ]
          ];
          if (!$this->TChatbotScenario->validates()) {
            $ret = false;
            break;
          }
          if (!$this->TChatbotScenario->save($saveData)) {
            $ret = false;
            break;
          }
        } else {
          // 送信されたシナリオ設定と現在DBに存在するシナリオ設定に差がある場合
          $this->TChatbotScenario->rollback();
          $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.configChanged'));
          return;
        }
      }
      if ($ret) {
        $this->TChatbotScenario->commit();
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
      }
      else {
        $this->TChatbotScenario->rollback();
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
      }
    }
   }

   /**
   * シナリオ設定ソート順を現在のID順でセット
   *
   * */
   public function remoteSetSort(){
     $this->TChatbotScenario->begin();
     /* 現在の並び順を取得 */
     $this->paginate['TChatbotScenario']['conditions']['TChatbotScenario.m_companies_id'] = $this->userInfo['MCompany']['id'];
     $params = [
         'fields' => [
             'TChatbotScenario.sort'
         ],
         'conditions' => [
             'TChatbotScenario.m_companies_id' => $this->userInfo['MCompany']['id']
         ],
         'order' => [
             'TChatbotScenario.sort' => 'asc',
             'TChatbotScenario.id' => 'asc'
         ],
         'limit' => 1,
         'recursive' => -1
     ];
     $params['fields'] = [
         'TChatbotScenario.id',
         'TChatbotScenario.sort'
     ];
     unset($params['limit']);
     $prevSort = $this->TChatbotScenario->find('list', $params);
     // ソート順のリセットはID順とする
     $i = 1;
     foreach($prevSort as $key => $val){
       $prevSort[$key] = strval($i);
       $i++;
     }
     $prevSortKeys = am($prevSort);
     $this->log($prevSortKeys,LOG_DEBUG);
     $i = 0;
     $ret = true;
     foreach($prevSort as $key => $val){
       $id = $key;
       $saveData = [
           'TChatbotScenario' => [
               'id' => $id,
               'sort' => $prevSortKeys[$i]
           ]
       ];
       if (!$this->TChatbotScenario->validates()) {
         $ret = false;
         break;
       }
       if (!$this->TChatbotScenario->save($saveData)) {
         $ret = false;
         break;
       }
       $i++;
     }
     if ($ret) {
       $this->TChatbotScenario->commit();
       return true;
     }
     else {
       $this->TChatbotScenario->rollback();
       return false;
     }
   }

  /**
   * 保存機能
   * @param array $saveData
   * @return void
   * */
  private function _entry($saveData) {
    $nextPage = '1';
    $transactions = null;
    try {
      $transactions = $this->TransactionManager->begin();
      $nextPage = $this->_entryProcess($saveData);
      $this->TransactionManager->commitTransaction($transactions);
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
      $this->redirect('/TChatbotScenario/index/page:'.$nextPage);
    } catch(ChatbotScenarioException $e) {
      $this->TransactionManager->rollbackTransaction($transactions);
      $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
      $this->set('errors', $e->getErrors());
      $this->set('lastPage', $e->getLastPage());
    } catch(Exception $e) {
      $this->TransactionManager->rollbackTransaction($transactions);
      $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
    }
  }

  /**
   * 保存機能
   * トランザクションはこのメソッドの呼び出し元で管理している。（TransactionManager）
   * @param Array $saveData
   * @return String
   * */
  private function _entryProcess($saveData) {
    $errors = [];
    $saveData['TChatbotScenario']['m_companies_id'] = $this->userInfo['MCompany']['id'];
    if(array_key_exists ('lastPage',$saveData)){
      $nextPage = $saveData['lastPage'];
    }
    else{
      $nextPage = '1';
    }

    if ( empty($saveData['TChatbotScenario']['id']) ) {
      //新規追加
      $this->TChatbotScenario->create();

      // 設定するsort順を算出する
      $params = [
          'fields' => [
              'TChatbotScenario.sort'
          ],
          'conditions' => [
              'TChatbotScenario.m_companies_id' => $this->userInfo['MCompany']['id']
             // 'TChatbotScenario.del_flg != ' => 1
          ],
          'order' => [
              'TChatbotScenario.sort' => 'desc',
              'TChatbotScenario.id' => 'desc'
          ],
          'limit' => 1,
          'recursive' => -1
      ];
      $lastData = $this->TChatbotScenario->find('first', $params);
      if($lastData){
        if($lastData['TChatbotScenario']['sort'] === '0'
            || $lastData['TChatbotScenario']['sort'] === 0
            || $lastData['TChatbotScenario']['sort'] === null){
          //ソート順が登録されていなかったらソート順をセットする
          if(! $this->remoteSetSort()){
            $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
            throw new ChatbotScenarioException('ソート順が設定できませんでした。');
          }
          //もう一度ソートの最大値を取り直す
          $lastData = $this->TChatbotScenario->find('first', $params);
        }
      }
      $nextSort = 1;
      if (!empty($lastData)) {
        $nextSort = intval($lastData['TChatbotScenario']['sort']) + 1;
      }
      $saveData['TChatbotScenario']['sort'] = $nextSort;
    }

    $this->TChatbotScenario->set($saveData);

    $validate = $this->TChatbotScenario->validates();
    $errors = $this->TChatbotScenario->validationErrors;

    // その他のチェック
    if ( !empty($saveData['TChatbotScenario']) ) {
      $activity = json_decode($saveData['TChatbotScenario']['activity']);

      foreach($activity->scenarios as $key => &$action) {
        if ($action->actionType == C_SCENARIO_ACTION_SEND_MAIL) {
          // メール送信設定の保存と、IDの取得
          $action = $this->_entryProcessForMessage($action);
        }
      }
    }

    $saveData['TChatbotScenario']['activity'] = json_encode($activity);
    $this->TChatbotScenario->set($saveData);

    $validate = $this->TChatbotScenario->validates();
    $errors = $this->TChatbotScenario->validationErrors;

    if ($validate) {
      if( $this->TChatbotScenario->save($saveData,false) ) {
      }
    }
    else {
      $exception = new ChatbotScenarioException('バリデーションエラー');
      $exception->setErrors($errors);
      $exception->setLastPage($nextPage);
      throw $exception;
    }

    $count = $this->TChatbotScenario->find('first',[
      'fields' => ['count(*) as count'],
      'conditions' => ['TChatbotScenario.del_flg != ' => 1, 'm_companies_id' => $this->userInfo['MCompany']['id']]
    ]);

    $page = floor((intval($count[0]['count']) + 99) / 100);
    return $page >= 1 ? $page : 1;
  }

  /**
   * メール送信設定の保存機能
   * トランザクションはこのメソッドの先祖で管理している。（TransactionManager）
   * @param array $saveData
   * @return {Void}
   * */
  private function _entryProcessForMessage($saveData) {
    // 送信先メールアドレス情報の値を、保存可能な形式に変換する
    $toAddresses = '';
    if(count($saveData->toAddress)) {
      foreach($saveData->toAddress as $address) {
        if (!empty($address)) {
          if ($toAddresses !== '') {
            $toAddresses .= ',';
          }
          $toAddresses .= $address;
        }
      }
    }

    // メール送信設定の保存
    if(empty($saveData->mMailTransmissionId)) {
      $this->MMailTransmissionSetting->create();
    } else {
      $this->MMailTransmissionSetting->read(null, $saveData->mMailTransmissionId);
    }
    $this->MMailTransmissionSetting->set([
      'm_companies_id' => $this->userInfo['MCompany']['id'],
      'from_name' => $saveData->fromName,
      'to_address' => $toAddresses,
      'subject' => $saveData->subject
    ]);
    $validate = $this->MMailTransmissionSetting->validates();
    $errors = $this->MMailTransmissionSetting->validationErrors;
    if(empty($errors)){
      $this->MMailTransmissionSetting->save();
      if(empty($saveData->mMailTransmissionId)) {
        $saveData->mMailTransmissionId = $this->MMailTransmissionSetting->getLastInsertId();
      }
    } else {
      $exception = new ChatbotScenarioException('バリデーションエラー');
      $exception->setErrors($errors);
      $exception->setLastPage($nextPage);
      throw $exception;
    }

    // メールテンプレートの設定
    $template = '';
    if ($saveData->mailType == C_SCENARIO_MAIL_TYPE_CUSTOMIZE) {
      // メール本文をカスタマイズする
      $template = $saveData->template;
    } else
    if ($saveData->mailType == C_SCENARIO_MAIL_TYPE_VARIABLES) {
      // 変数の値のみメールする
      $template = "※このメールはお客様の設定によりsincloから自動送信されました。

ご担当者様

sincloのシナリオ設定によりメールを送信致しました。
以下のメッセージ内容をご確認下さい。

##SCENARIO_VARIABLES_BLOCK##

------------------------------------------------------------------
このメールにお心当たりのない方は、誠に恐れ入りますが
下記連絡先までご連絡ください。
sinclo@medialink-ml.co.jp
------------------------------------------------------------------";
    } else {
      // メール内容をすべてメールする
      $template = "※このメールはお客様の設定によりsincloから自動送信されました。

ご担当者様

sincloのシナリオ設定によりメールを送信致しました。
以下のメッセージ内容をご確認下さい。

##SCENARIO_ALL_MESSAGE_BLOCK##

------------------------------------------------------------------
このメールにお心当たりのない方は、誠に恐れ入りますが
下記連絡先までご連絡ください。
sinclo@medialink-ml.co.jp
------------------------------------------------------------------";
    }

    // メールテンプレート設定の保存
    if(empty($saveData->mMailTemplateId)) {
      $this->MMailTemplate->create();
    } else {
      $this->MMailTemplate->read(null, $saveData->mMailTemplateId);
    }
    $this->MMailTemplate->set([
      'm_companies_id' => $this->userInfo['MCompany']['id'],
      'mail_type_cd' => 'CS001',
      'template' => $template
    ]);
    $validate = $this->MMailTemplate->validates();
    $errors = $this->MMailTemplate->validationErrors;
    if(empty($errors)) {
      $this->MMailTemplate->save();
      if(empty($saveData->mMailTemplateId)) {
        $saveData->mMailTemplateId = $this->MMailTemplate->getLastInsertId();
      }
    } else {
      $exception = new ChatbotScenarioException('バリデーションエラー');
      $exception->setErrors($errors);
      $exception->setLastPage($nextPage);
      throw $exception;
    }

    // 保存済みの設定をオブジェクトから削除する
    unset($saveData->fromName);
    unset($saveData->toAddress);
    unset($saveData->subject);
    unset($saveData->template);

    return $saveData;
  }

  /**
   * ビュー部品セット
   * @return void
   * */
  private function _viewElement() {
    // アクション種別
    $this->set('chatbotScenarioActionList', $this->chatbotScenarioActionList);
    // 入力タイプ種別
    $this->set('chatbotScenarioInputType', $this->chatbotScenarioInputType);
    // メール送信タイプ種別
    $this->set('chatbotScenarioSendMailType', $this->chatbotScenarioSendMailType);
    // 最後に表示していたページ番号
    if(!empty($this->request->query['lastpage'])){
      $this->set('lastPage', $this->request->query['lastpage']);
    }
  }

  /**
   * _getWidgetSettings
   * ウィジェット設定を取得し、シミュレーター表示用にパラメーターを設定する
   *
   * @return $inputData['MWidgetSetting'] シミュレーター表示用にパラメーターを設定したもの
   */
  private function _getWidgetSettings() {
    $inputData = [];
    $ret = $this->MWidgetSetting->coFind('first');
    $inputData = $ret['MWidgetSetting'];

    // 表示ウィジェットのセット
    $inputData = $this->_setShowTab($inputData);

    // 詳細設定
    if ( isset($ret['MWidgetSetting']['style_settings']) ) {
      $json = $this->_settingToObj($ret['MWidgetSetting']['style_settings']);
      $inputData = $this->_setStyleSetting($inputData, $json);
    }
    if(array_key_exists ('re_border_color',$json)){
      if($json['re_border_color'] === 'none'){
        $this->set('re_border_color_flg', false);
        $inputData['re_border_color'] = 'なし';
        $inputData['re_border_none'] = true;
      }
      else{
        $this->set('re_border_color_flg', true);
      }
    }
    else{
      //初回読み込み時
//         $this->set('re_border_color_flg', false);
//         $inputData['re_border_color'] = 'なし';
//         $inputData]['re_border_none'] = true;
      $this->set('re_border_color_flg', true);
    }
    if(array_key_exists ('se_border_color',$json)){
      if($json['se_border_color'] === 'none'){
        $this->set('se_border_color_flg', false);
        $inputData['se_border_color'] = 'なし';
        $inputData['se_border_none'] = true;
      }
      else{
        $this->set('se_border_color_flg', true);
      }
    }
    else{
      //初回読み込み時
//         $this->set('se_border_color_flg', false);
//         $inputData['se_border_color'] = 'なし';
//         $inputData['se_border_none'] = true;
      $this->set('se_border_color_flg', true);
    }
    if(array_key_exists ('message_box_border_color',$json)){
      if($json['message_box_border_color'] === 'none'){
        $this->set('message_box_border_color_flg', false);
        $inputData['message_box_border_color'] = 'なし';
        $inputData['message_box_border_none'] = true;
      }
      else{
        $this->set('message_box_border_color_flg', true);
      }
    }
    else{
      $this->set('message_box_border_color_flg', true);
    }
    //ウィジェット外枠線
    if(array_key_exists ('widget_border_color',$json)){
      if($json['widget_border_color'] === 'none'){
        $this->set('widget_border_color_flg', false);
        $inputData['widget_border_color'] = 'なし';
        $inputData['widget_outside_border_none'] = true;
      }
      else{
        $this->set('widget_border_color_flg', true);
      }
    }
    else{
      $this->set('widget_border_color_flg', true);
    }
    //ウィジェット内枠線
    if(array_key_exists ('widget_inside_border_color',$json)){
      if($json['widget_inside_border_color'] === 'none'){
        $this->set('widget_inside_border_color_flg', false);
        $inputData['widget_inside_border_color'] = 'なし';
        $inputData['widget_inside_border_none'] = true;
      }
      else{
        $this->set('widget_inside_border_color_flg', true);
      }
    }
    else{
      $this->set('widget_inside_border_color_flg', true);
    }
    //仕様変更常に高度な設定の設定値が反映されるようにする
    if(array_key_exists ('color_setting_type',$json)){
      if($json['color_setting_type'] === '1'){
        $inputData['color_setting_type'] = '0';
      }
    }

    $titleLength = 12;
    $subTitleLength = 15;
    $descriptionLength = 15;
    switch ($inputData['widget_size_type']) {
      //大きさによってトップタイトル、企業名、説明文のmaxlengthを可変とする
      case '1': //小
        $titleLength = 12;
        $subTitleLength = 15;
        $descriptionLength = 15;
        break;
      case '2': //中
        $titleLength = 16;
        $subTitleLength = 20;
        $descriptionLength = 20;
        break;
      case '3': //大
        $titleLength = 19;
        $subTitleLength = 24;
        $descriptionLength = 24;
        break;
    }

    return $inputData;
  }

  /**
   * _settingToObj
   * JSON形式で取得した値をオブジェクト形式に変換
   *
   * @param $jsonData JSON JSON形式のデータ
   * @return $settings オブジェクト JSON形式のデータをオブジェクトに変換したもの
   *
   * */
  private function _settingToObj($jsonData){
    $settings = [];

    // キーの管理用変数のキーと値を入れ替える
    $styleColumns = array_flip($this->MWidgetSetting->styleColumns);

    // JSONからオブジェクトに変更
    $json = json_decode($jsonData);

    // 保持していた設定ごとループ処理
    foreach($json as $key => $val){
      // 設定名が管理しているキーである場合、値を $settings にセット
      if ( isset($styleColumns[$key]) ) {
      $settings[$styleColumns[$key]] = $val;
      }
    }

    return $settings;
  }

  /**
   * デフォルトで表示するタブを選定
   * @param $d ($inputData)
   * @return $d ($inputData)
   * */
  private function _setShowTab($d){
    // チャットのみ
    if ( $this->coreSettings[C_COMPANY_USE_CHAT] && !$this->coreSettings[C_COMPANY_USE_SYNCLO] ) {
      $d['widget']['showTab'] = "chat";
    }
    // 画面・資料同期のみ
    else if ( ($this->coreSettings[C_COMPANY_USE_SYNCLO] || (isset($this->coreSettings[C_COMPANY_USE_DOCUMENT]) && $this->coreSettings[C_COMPANY_USE_DOCUMENT]) ) && !$this->coreSettings[C_COMPANY_USE_CHAT] ) {
      $d['widget']['showTab'] = "call";
    }
    // どちらも
    else {
      // チャットがデフォルト
      $d['widget']['showTab'] = "chat";
      if ( isset($this->request->params['named']['showTab']) && strcmp($this->request->params['named']['showTab'], "call") === 0 ) {
      $d['widget']['showTab'] = "call";
      }
    }
    return $d;
  }

  /**
   * jsonデータとして纏めていた設定値を配列に直す
   * @param $d ($inputData)
   * @param $json ($inputData['MWidgetSetting']['style_settings']をjson_decodeしたもの)
   * @return $d ($inputData)
   * */
  private function _setStyleSetting($d, $json) {
    foreach($this->styleSetting as $key => $list) {
      foreach($list as $v) {
        switch ($key) {
          case 'chat':
            if ( !$this->coreSettings[C_COMPANY_USE_CHAT] ) { continue; }
            if ( strcmp($v, 'chat_radio_behavior') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['chat_radio_behavior'] = C_WIDGET_RADIO_CLICK_SEND; // デフォルト値
            }
            if ( strcmp($v, 'chat_trigger') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['chat_trigger'] = C_WIDGET_SEND_ACT_PUSH_KEY; // デフォルト値
            }
            if ( strcmp($v, 'show_name') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['show_name'] = C_WIDGET_SHOW_COMP; // デフォルト値
            }
            if ( strcmp($v, 'chat_message_design_type') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['chat_message_design_type'] = C_WIDGET_CHAT_MESSAGE_DESIGN_TYPE_BOX; // デフォルト値
            }
            if ( strcmp($v, 'chat_message_with_animation') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['chat_message_with_animation'] = C_CHECK_OFF; // デフォルト値（非選択状態：アニメーション無効）
            }
            if ( strcmp($v, 'chat_message_copy') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['chat_message_copy'] = C_WIDGET_CHAT_MESSAGE_CAN_COPY; // デフォルト値
            }
            if ( strcmp($v, 'sp_show_flg') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['sp_show_flg'] = C_SELECT_CAN; // デフォルト値
            }

            if ( strcmp($v, 'sp_header_light_flg') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['sp_header_light_flg'] = C_SELECT_CAN_NOT; // デフォルト値
            }

            if ( strcmp($v, 'sp_auto_open_flg') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['sp_auto_open_flg'] = C_CHECK_OFF; // デフォルト値
            }

            if ( isset($json[$v]) ) {
              $d[$v] = $json[$v];
            }
            break;
          case 'synclo':
            if ( !($this->coreSettings[C_COMPANY_USE_SYNCLO] || (isset($this->coreSettings[C_COMPANY_USE_DOCUMENT]) && $this->coreSettings[C_COMPANY_USE_DOCUMENT]) ) ) { continue; }

            if ( isset($json[$v]) ) {
              $d[$v] = $json[$v];
            }
            break;
          case 'common':
            if ( strcmp($v, "max_show_timing_site") === 0 || strcmp($v, "max_show_timing_page") === 0 ) { continue; }
            if ( strcmp($v, "show_timing") === 0 && isset($json[$v]) ) {
              if ( strcmp($json[$v], C_WIDGET_SHOW_TIMING_SITE) === 0 ) {
                if ( isset($json["max_show_timing_site"]) ) {
                  $d["max_show_timing_site"] = $json["max_show_timing_site"];
                }
              }
              else if ( strcmp($json[$v], C_WIDGET_SHOW_TIMING_PAGE) === 0 ) {
                if ( isset($json["max_show_timing_page"]) ) {
                  $d["max_show_timing_page"] = $json["max_show_timing_page"];
                }
              }
            }
            if ( strcmp($v, "max_show_time") === 0 || strcmp($v, "max_show_time_page") === 0 ) { continue; }
            if ( strcmp($v, "show_time") === 0 && isset($json[$v]) ) {
              if ( strcmp($json[$v], C_WIDGET_AUTO_OPEN_TYPE_SITE) === 0 ) {
                if ( isset($json["max_show_time"]) ) {
                  $d["max_show_time"] = $json["max_show_time"];
                }
              }
              else if ( strcmp($json[$v], C_WIDGET_AUTO_OPEN_TYPE_PAGE) === 0 ) {
                if ( isset($json["max_show_time_page"]) ) {
                  $d["max_show_time_page"] = $json["max_show_time_page"];
                }
              }
            }
            //ウィジットサイズタイプ
            if ( strcmp($v, 'widget_size_type') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['widget_size_type'] = C_WIDGET_SIZE_TYPE_SMALL; // デフォルト値
            }
            //最小化時のデザインタイプ
            if ( strcmp($v, 'minimize_design_type') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['minimize_design_type'] = C_MINIMIZED_DESIGN_NO_SIMPLE; // デフォルト値
            }
            //背景の影
            if ( strcmp($v, 'box_shadow') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['box_shadow'] = C_BOX_SHADOW; // デフォルト値
            }
            //閉じるボタン
            //閉じるボタン有効無効
            if ( strcmp($v, 'close_button_setting') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['close_button_setting'] = C_CLOSE_BUTTON_SETTING_OFF; // デフォルト値
            }
            //小さなバナー表示有効無効
            if ( strcmp($v, 'close_button_mode_type') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['close_button_mode_type'] = C_CLOSE_BUTTON_SETTING_MODE_TYPE_HIDDEN; // デフォルト値
            }
            //バナーテキスト
            if ( strcmp($v, 'bannertext') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['bannertext'] = C_BANNER_TEXT; // デフォルト値
            }
            //閉じるボタン
            /* カラー設定styat */
            //0.通常設定・高度設定
            if ( strcmp($v, 'color_setting_type') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['color_setting_type'] = COLOR_SETTING_TYPE_OFF; // デフォルト値
            }
            //1.メインカラー
            if ( strcmp($v, 'main_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['main_color'] = MAIN_COLOR; // デフォルト値
            }
            //2.タイトル文字色
            if ( strcmp($v, 'string_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['string_color'] = STRING_COLOR; // デフォルト値
            }
            //3.吹き出し文字色
            if ( strcmp($v, 'message_text_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['message_text_color'] = MESSAGE_TEXT_COLOR; // デフォルト値
            }
            //4.その他文字色
            if ( strcmp($v, 'other_text_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['other_text_color'] = OTHER_TEXT_COLOR; // デフォルト値
            }
            //5.ウィジェット枠線色
            if ( strcmp($v, 'widget_border_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['widget_border_color'] = WIDGET_BORDER_COLOR; // デフォルト値
            }
            //6.ヘッダー背景色
            if ( strcmp($v, 'header_background_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['header_background_color'] = HEADER_BACKGROUND_COLOR; // デフォルト値
            }
            //6.吹き出し枠線色
            if ( strcmp($v, 'chat_talk_border_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['chat_talk_border_color'] = CHAT_TALK_BORDER_COLOR; // デフォルト値
            }
            //7.企業名文字色
            if ( strcmp($v, 'sub_title_text_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              if($json['main_color'] && $json['main_color'] !== MAIN_COLOR){
                $d['sub_title_text_color'] = $json['main_color'];
              }
              else{
                $d['sub_title_text_color'] = SUB_TITLE_TEXT_COLOR; // デフォルト値
              }
            }
            //8.説明文文字色
            if ( strcmp($v, 'description_text_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['description_text_color'] = DESCRIPTION_TEXT_COLOR; // デフォルト値
            }
            //9.チャットエリア背景色
            if ( strcmp($v, 'chat_talk_background_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['chat_talk_background_color'] = CHAT_TALK_BACKGROUND_COLOR; // デフォルト値
            }
            //10.企業名担当者名文字色
            if ( strcmp($v, 'c_name_text_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              if($json['main_color'] && $json['main_color'] !== MAIN_COLOR){
                $d['c_name_text_color'] = $json['main_color'];
              }
              else{
                $d['c_name_text_color'] = C_NAME_TEXT_COLOR; // デフォルト値
              }
            }
            //11.企業側吹き出し文字色
            if ( strcmp($v, 're_text_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['re_text_color'] = RE_TEXT_COLOR; // デフォルト値
            }
            //12.企業側吹き出し背景色
            if ( strcmp($v, 're_background_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              if($json['main_color'] || $json['main_color'] !== MAIN_COLOR){
                //企業側吹き出し用の色をメインカラーから算出
                $main_color = $json['main_color'];
                $code = substr($main_color,1);
                if(strlen($code) === 3){
                  $r = substr($code,0,1).substr($code,0,1);
                  $g = substr($code,1,1).substr($code,1,1);
                  $b = substr($code,2).substr($code,2);
                }
                else{
                  $r = substr($code,0,2);
                  $g = substr($code,2,2);
                  $b = substr($code,4);
                }

                $balloonR = dechex(255 - (255 - intval($r,16)) * 0.1);
                $balloonG = dechex(255 - (255 - intval($g,16)) * 0.1);
                $balloonB = dechex(255 - (255 - intval($b,16)) * 0.1);
                $defColor = '#'.$balloonR.$balloonG.$balloonB;
                $d['re_background_color'] = $defColor;
              }
              else{
                $d['re_background_color'] = RE_BACKGROUND_COLOR; // デフォルト値
              }
            }
            //13.企業側吹き出し枠線色
            if ( strcmp($v, 're_border_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
//               if($json['re_border_color'] === 'false'){
//                 $d['re_border_color'] = 'false';
//               }
//               else{
                $d['re_border_color'] = RE_BORDER_COLOR; // デフォルト値
//               }
            }
//             //14.企業側吹き出し枠線なし
//             if ( strcmp($v, 're_border_none') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
//               $d['re_border_none'] = COLOR_SETTING_TYPE_OFF; // デフォルト値
//             }
            //15.訪問者側吹き出し文字色
            if ( strcmp($v, 'se_text_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['se_text_color'] = SE_TEXT_COLOR; // デフォルト値
            }
            //16.訪問者側吹き出し背景色
            if ( strcmp($v, 'se_background_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['se_background_color'] = SE_BACKGROUND_COLOR; // デフォルト値
            }
            //17.訪問者側吹き出し枠線色
            if ( strcmp($v, 'se_border_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['se_border_color'] = SE_BORDER_COLOR; // デフォルト値
            }
//             //18.訪問者側吹き出し枠線なし
//             if ( strcmp($v, 'se_border_none') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
//               $d['se_border_none'] = COLOR_SETTING_TYPE_OFF; // デフォルト値
//             }
            //19.メッセージエリア背景色
            if ( strcmp($v, 'chat_message_background_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['chat_message_background_color'] = CHAT_MESSAGE_BACKGROUND_COLOR; // デフォルト値
            }
            //20.メッセージBOX文字色
            if ( strcmp($v, 'message_box_text_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['message_box_text_color'] = MESSAGE_BOX_TEXT_COLOR; // デフォルト値
            }
            //21.メッセージBOX背景色
            if ( strcmp($v, 'message_box_background_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['message_box_background_color'] = MESSAGE_BOX_BACKGROUND_COLOR; // デフォルト値
            }
            //22.メッセージBOX枠線色
            if ( strcmp($v, 'message_box_border_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['message_box_border_color'] = MESSAGE_BOX_BORDER_COLOR; // デフォルト値
            }
//             //23.メッセージBOX枠線なし
//             if ( strcmp($v, 'message_box_border_none') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
//               $d['message_box_border_none'] = COLOR_SETTING_TYPE_OFF; // デフォルト値
//             }
            //24.送信ボタン文字色
            if ( strcmp($v, 'chat_send_btn_text_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              if($json['string_color'] && $json['string_color'] !== STRING_COLOR){
                $d['chat_send_btn_text_color'] = $json['string_color'];
              }
              else{
                $d['chat_send_btn_text_color'] = CHAT_SEND_BTN_TEXT_COLOR; // デフォルト値
              }
            }
            //25.送信ボタン背景色
            if ( strcmp($v, 'chat_send_btn_background_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              if($json['main_color'] && $json['main_color'] !== MAIN_COLOR){
                $d['chat_send_btn_background_color'] = $json['main_color'];
              }
              else{
                $d['chat_send_btn_background_color'] = CHAT_SEND_BTN_BACKGROUND_COLOR; // デフォルト値
              }
            }
            //26.ウィジット内枠線色
            if ( strcmp($v, 'widget_inside_border_color') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
              $d['widget_inside_border_color'] = WIDGET_INSIDE_BORDER_COLOR; // デフォルト値
            }
//             //26.ウィジット内枠線色
//             if ( strcmp($v, 'widget_inside_border_none') === 0 & (!isset($json[$v]) || (isset($json[$v]) && !is_numeric($json[$v]))) ) {
//               $d['widget_inside_border_none'] = COLOR_SETTING_TYPE_OFF; // デフォルト値
//             }
            /* カラー設定end */

            if ( isset($json[$v]) ) {
              $d[$v] = $json[$v];
            }
            break;
        }
      }
    }
    return $d;
  }
}
