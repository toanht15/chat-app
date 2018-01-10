<?php
/**
 * TAutoMessagesController controller.
 * ユーザーマスタ
 */
class TAutoMessagesController extends AppController {
  public $uses = ['TAutoMessage','MOperatingHour', 'MMailTransmissionSetting', 'MMailTemplate', 'MWidgetSetting'];
  public $helpers = ['AutoMessage'];
  public $paginate = [
    'TAutoMessage' => [
      'limit' => 100,
      'order' => [
          'TAutoMessage.sort' => 'asc',
          'TAutoMessage.id' => 'asc'
      ],
      'fields' => ['TAutoMessage.*'],
      'conditions' => ['TAutoMessage.del_flg != ' => 1],
      'recursive' => -1
    ]
  ];
  public $outMessageIfType;
  public $outMessageTriggerList;

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
    $this->set('title_for_layout', 'オートメッセージ機能');
    $this->outMessageIfType = Configure::read('outMessageIfType');
    $this->outMessageTriggerList = Configure::read('outMessageTriggerList');
    $operatingHourData = $this->MOperatingHour->find('first', ['conditions' => [
        'm_companies_id' => $this->userInfo['MCompany']['id']
    ]]);
    if(empty($operatingHourData)) {
      $operatingHourData['MOperatingHour']['active_flg'] = 2;
    }
    $this->set('operatingHourData',$operatingHourData['MOperatingHour']['active_flg']);
  }

  /**
   * 一覧画面
   * @return void
   * */
  public function index() {

    $this->paginate['TAutoMessage']['conditions']['TAutoMessage.m_companies_id'] = $this->userInfo['MCompany']['id'];
    $data = $this->paginate('TAutoMessage');
    foreach($data as $index => $value) {
      $activity = json_decode($value['TAutoMessage']['activity'], true);
      foreach($activity['conditions'] as $key => $val){
        $targetKey = $key;
        if($targetKey >= 4) {
          $targetKey = $targetKey+1;
        } else if($targetKey === 10) {
          $targetKey = 4;
        }
        $activity = $this->convertOldIFData($targetKey, $val, $activity, $key);
      }
      $data[$index]['TAutoMessage']['activity'] = json_encode($activity);
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

    $operatingHourData = $this->MOperatingHour->find('first', ['conditions' => [
      'm_companies_id' => $this->userInfo['MCompany']['id']
    ]]);
    if(empty($operatingHourData)) {
      $operatingHourData['MOperatingHour']['active_flg'] = 2;
    }
    $this->set('operatingHourData',$operatingHourData['MOperatingHour']['active_flg']);

    // シミュレーター表示用ウィジェット設定の取得
    $this->request->data['widgetSettings'] = $this->_getWidgetSettings();

    $this->_viewElement();
  }

   /**
   * 更新画面
   * @return void
   * */
  public function edit($id=null) {
    if ($this->request->is('put')) {
      $this->_entry($this->request->data);
    }
    else {
      // 確実なデータを取得するために企業IDを指定する形とする
      $editData = $this->TAutoMessage->coFind("all", [
        'conditions' => [
          'TAutoMessage.id' => $id
        ]
      ]);

      //オートメッセージ　営業時間を4番目に入れたので並び替え処理
      $changeEditData = json_decode($editData[0]['TAutoMessage']['activity'], true);
      $changeEditData['conditions'] = array_reverse($changeEditData['conditions'], true);
      foreach($changeEditData['conditions'] as $key => $val){
        if($key >= 4) {
          unset($changeEditData['conditions'][$key]);
          $changeEditData['conditions'][$key+1] = json_decode($editData[0]['TAutoMessage']['activity'], true)['conditions'][$key];
        }
      }

      foreach($changeEditData['conditions'] as $key => $val){
        if($key === 11) {
          unset($changeEditData['conditions'][11]);
          $changeEditData['conditions'][4] = json_decode($editData[0]['TAutoMessage']['activity'], true)['conditions'][10];
        }
        $changeEditData = $this->convertOldIFData($key, $val, $changeEditData, $key);
      }

      if (empty($editData) || (!empty($editData) && empty($editData[0]))) {
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.notFoundId'));
        $this->redirect('/TAutoMessages/index');
      }

      $changeEditData = json_encode($changeEditData);
      $editData[0]['TAutoMessage']['activity'] = $changeEditData;
      $json = json_decode($editData[0]['TAutoMessage']['activity'], true);
      $this->request->data = $editData[0];
      $this->request->data['TAutoMessage']['condition_type'] = (!empty($json['conditionType'])) ? $json['conditionType'] : "";
      $this->request->data['TAutoMessage']['action'] = (!empty($json['message'])) ? $json['message'] : "";
      $this->request->data['TAutoMessage']['widget_open'] = (!empty($json['widgetOpen'])) ? $json['widgetOpen'] : "";
      $this->request->data['TAutoMessage']['chat_textarea'] = (!empty($json['chatTextarea'])) ? $json['chatTextarea'] : "";
      $this->request->data['TAutoMessage']['cv'] = (!empty($json['cv'])) ? $json['cv'] : "";
      if (array_key_exists('send_mail_flg', $editData[0]['TAutoMessage'])) {
        $this->request->data['TAutoMessage']['send_mail_flg'] = $editData[0]['TAutoMessage']['send_mail_flg'];
        $transmissionData = $this->MMailTransmissionSetting->findById($editData[0]['TAutoMessage']['m_mail_transmission_settings_id']);
        if(!empty($transmissionData)) {
          $this->request->data['TAutoMessage']['m_mail_transmission_settings_id'] = $editData[0]['TAutoMessage']['m_mail_transmission_settings_id'];
          $splitedMailAddresses = explode(',',$transmissionData['MMailTransmissionSetting']['to_address']);
          $this->request->data['TAutoMessage']['mail_address_1'] = !empty($splitedMailAddresses[0]) ? $splitedMailAddresses[0] : "";
          $this->request->data['TAutoMessage']['mail_address_2'] = !empty($splitedMailAddresses[1]) ? $splitedMailAddresses[1] : "";
          $this->request->data['TAutoMessage']['mail_address_3'] = !empty($splitedMailAddresses[2]) ? $splitedMailAddresses[2] : "";
          $this->request->data['TAutoMessage']['mail_address_4'] = !empty($splitedMailAddresses[3]) ? $splitedMailAddresses[3] : "";
          $this->request->data['TAutoMessage']['mail_address_5'] = !empty($splitedMailAddresses[4]) ? $splitedMailAddresses[4] : "";
          $this->request->data['TAutoMessage']['subject'] = !empty($transmissionData['MMailTransmissionSetting']['subject']) ? $transmissionData['MMailTransmissionSetting']['subject'] : "";
          $this->request->data['TAutoMessage']['from_name'] = !empty($transmissionData['MMailTransmissionSetting']['from_name']) ? $transmissionData['MMailTransmissionSetting']['from_name'] : "";
        }
        $this->request->data['TAutoMessage']['m_mail_template_id'] = $editData[0]['TAutoMessage']['m_mail_template_id'];
      }
    }

    // シミュレーター表示用ウィジェット設定の取得
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
    $ret = $this->TAutoMessage->find('first', [
      'fields' => 'TAutoMessage.*',
      'conditions' => [
        'TAutoMessage.del_flg' => 0,
        'TAutoMessage.id' => $id,
        'TAutoMessage.m_companies_id' => $this->userInfo['MCompany']['id']
      ],
      'recursive' => -1
    ]);
    if ( count($ret) === 1 ) {
      if ( $this->TAutoMessage->logicalDelete($id) ) {
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
    $this->TAutoMessage->begin();
    $res = true;
    foreach($selectedList as $key => $val){
      $id = (isset($val)) ? $val: "";
      $ret = $this->TAutoMessage->find('first', [
          'fields' => 'TAutoMessage.*',
          'conditions' => [
              'TAutoMessage.del_flg' => 0,
              'TAutoMessage.id' => $id,
              'TAutoMessage.m_companies_id' => $this->userInfo['MCompany']['id']
          ],
          'recursive' => -1
      ]);
      if ( count($ret) === 1 ) {
        if (! $this->TAutoMessage->delete($val) ) {
          $res = false;
        }
      }
    }
    if($res){
      $this->TAutoMessage->commit();
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
    }
    else {
      $this->TAutoMessage->rollback();
      $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
    }
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
    //コピー元のオートメッセージリスト取得
    foreach($selectedList as $value){
      $copyData[] = $this->TAutoMessage->read(null, $value);
    }
    $errorMessage = [];
    //コピー元のオートメッセージリストの数だけ繰り返し
    $res = true;
    foreach($copyData as $value){
      $this->TAutoMessage->create();
      $saveData = [];
      $params = [
          'fields' => [
              'TAutoMessage.sort'
          ],
          'conditions' => [
              'TAutoMessage.m_companies_id' => $this->userInfo['MCompany']['id']
              //'TAutoMessage.del_flg != ' => 1
          ],
          'order' => [
              'TAutoMessage.sort' => 'desc',
              'TAutoMessage.id' => 'desc'
          ],
          'limit' => 1,
          'recursive' => -1
      ];
      $lastData = $this->TAutoMessage->find('first', $params);
      if($lastData['TAutoMessage']['sort'] === '0'
          || $lastData['TAutoMessage']['sort'] === 0
          || $lastData['TAutoMessage']['sort'] === null){
            //ソート順が登録されていなかったらソート順をセットする
            if(! $this->remoteSetSort()){
              $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
              return false;
            }
            //もう一度ソートの最大値を取り直す
            $lastData = $this->TAutoMessage->find('first', $params);
      }
      $nextSort = 1;
      if (!empty($lastData)) {
        $nextSort = intval($lastData['TAutoMessage']['sort']) + 1;
      }

      //オートメッセージ　営業時間を4番目に入れたので並び替え処理
      $changeEditData = json_decode($value['TAutoMessage']['activity'], true);

      foreach($changeEditData['conditions'] as $key => $val){
        if($key >= 4) {
          unset($changeEditData['conditions'][$key]);
          $changeEditData['conditions'][$key+1] = json_decode($value['TAutoMessage']['activity'], true)['conditions'][$key];
        }
      }

      foreach($changeEditData['conditions'] as $key => $val){
        if($key === 11) {
          unset($changeEditData['conditions'][11]);
          $changeEditData['conditions'][4] = json_decode($value['TAutoMessage']['activity'], true)['conditions'][10];
        }
      }

      $mailTransmissionData = $this->MMailTransmissionSetting->findById($value['TAutoMessage']['m_mail_transmission_settings_id']);
      if(!empty($mailTransmissionData)) {
        $this->MMailTransmissionSetting->create();
        $mailTransmissionData['MMailTransmissionSetting']['id'] = null;
        $this->MMailTransmissionSetting->set($mailTransmissionData);
        $this->MMailTransmissionSetting->begin();
        $result = $this->MMailTransmissionSetting->save();
        $value['TAutoMessage']['m_mail_transmission_settings_id'] = $this->MMailTransmissionSetting->getLastInsertId();
      }

      $changeEditData = json_encode($changeEditData);

      $value['TAutoMessage']['activity'] = $changeEditData;

      $saveData['TAutoMessage']['sort'] = $nextSort;
      $saveData['TAutoMessage']['m_companies_id'] = $value['TAutoMessage']['m_companies_id'];
      $saveData['TAutoMessage']['name'] = $value['TAutoMessage']['name'].'コピー';
      $saveData['TAutoMessage']['trigger_type'] = $value['TAutoMessage']['trigger_type'];
      $saveData['TAutoMessage']['activity'] = $value['TAutoMessage']['activity'];
      $saveData['TAutoMessage']['action_type'] = $value['TAutoMessage']['action_type'];
      $saveData['TAutoMessage']['active_flg'] = $value['TAutoMessage']['active_flg'];
      $saveData['TAutoMessage']['send_mail_flg'] = $value['TAutoMessage']['send_mail_flg'];
      $saveData['TAutoMessage']['m_mail_transmission_settings_id'] = $value['TAutoMessage']['m_mail_transmission_settings_id'];
      $saveData['TAutoMessage']['m_mail_template_id'] = $value['TAutoMessage']['m_mail_template_id'];
      $saveData['TAutoMessage']['del_flg'] = $value['TAutoMessage']['del_flg'];

      $this->TAutoMessage->set($saveData);
      $this->TAutoMessage->begin();

      // バリデーションチェックでエラーが出た場合
      if($res){
        if(!$this->TAutoMessage->validates()) {
          $res = false;
          $errorMessage = $this->TAutoMessage->validationErrors;
          $this->MMailTransmissionSetting->rollback();
          $this->TAutoMessage->rollback();
        }
        else{
          //オートメッセージ　営業時間を4番目に入れたので並び替え処理
          $changeEditData = json_decode($saveData['TAutoMessage']['activity'],true);
          foreach($changeEditData['conditions'] as $key => $val){
            if($key == 4) {
              unset($changeEditData['conditions'][4]);
              $changeEditData['conditions'][11] = json_decode($value['TAutoMessage']['activity'], true)['conditions'][4];
            }
          }

          foreach($changeEditData['conditions'] as $key => $val){
            if($key >= 4 && $key != 11) {
              unset($changeEditData['conditions'][$key]);
              $changeEditData['conditions'][$key-1] = json_decode($value['TAutoMessage']['activity'], true)['conditions'][$key];
            }
            if($key == 11) {
              $changeEditData['conditions'][10] = $changeEditData['conditions'][11];
              unset($changeEditData['conditions'][11]);
            }
          }

          $changeEditData = json_encode($changeEditData);
          $saveData['TAutoMessage']['activity'] = $changeEditData;

          if( $this->TAutoMessage->save($saveData,false) ) {
            $this->MMailTransmissionSetting->commit();
            $this->TAutoMessage->commit();
            $this->Session->delete('dstoken');
            $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
          }
        }
      }
    }
  }

  /**
   * オートメッセージ設定ソート順更新
   *
   * */
  public function remoteSaveSort(){
    Configure::write('debug', 2);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    if ( !$this->request->is('ajax') ) return false;
    if ( !empty($this->params->data['list']) ) {
      $this->TAutoMessage->begin();
      $list = $this->params->data['list'];
      $sortNoList = $this->params->data['sortNolist'];
      sort($sortNoList);
      $this->log($list,LOG_DEBUG);
      /* 現在の並び順を取得 */
      $params = $this->paginate['TAutoMessage'];
      $params['fields'] = [
          'TAutoMessage.id',
          'TAutoMessage.sort'
      ];
      $params['conditions']['TAutoMessage.m_companies_id'] = $this->userInfo['MCompany']['id'];
      unset($params['limit']);
      $prevSort = $this->TAutoMessage->find('list', $params);
      //新しくソート順を設定したため、空で来ることがある
      $reset_flg = false;
      foreach($prevSort as $key => $val){
        //設定されていない値'0'が一つでも入っていたらsortをリセット
        if($val === '0' || $val === 0 || $val === null){
          $reset_flg = true;
        }
      }
      if($reset_flg){
        //ソート順のリセットはID順とする
//         $i = 1;
//         foreach($prevSort as $key => $val){
//           $prevSort[$key] = strval($i);
//           $i++;
//         }
        //ソート順が登録されていなかったらソート順をセットする
        if(! $this->remoteSetSort()){
          $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
          return false;
        }
        $prevSort = $this->TAutoMessage->find('list', $params);
        //この時$sortNoListは空なので作成する
        if(empty($sortNoList)){
          for ($i = 0; count($list) > $i; $i++) {
            $id = $list[$i];
            $sortNoList[] = $prevSort[$id];
          }
          sort($sortNoList);
        }
      }
//       $prevSortKeys = am($prevSort);
//       $this->log($prevSortKeys,LOG_DEBUG);
      /* アップデート分の並び順を設定 */
      $ret = true;
      for ($i = 0; count($list) > $i; $i++) {
        $id = $list[$i];
        if ( isset($prevSort[$id]) ) {
          $saveData = [
              'TAutoMessage' => [
                  'id' => $id,
                  'sort' => $sortNoList[$i]
              ]
          ];
          if (!$this->TAutoMessage->validates()) {
            $ret = false;
            break;
          }
          if (!$this->TAutoMessage->save($saveData)) {
            $ret = false;
            break;
          }
        } else {
          // 送信されたオートメッセージ設定と現在DBに存在するオートメッセージ設定に差がある場合
          $this->TAutoMessage->rollback();
          $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.configChanged'));
          return;
        }
      }
      if ($ret) {
        $this->TAutoMessage->commit();
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
      }
      else {
        $this->TAutoMessage->rollback();
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
      }
    }
  }

  /**
   * オートメッセージ設定ソート順を現在のID順でセット
   *
   * */
  public function remoteSetSort(){
    $this->TAutoMessage->begin();
    /* 現在の並び順を取得 */
    $this->paginate['TAutoMessage']['conditions']['TAutoMessage.m_companies_id'] = $this->userInfo['MCompany']['id'];
    $params = [
        'fields' => [
            'TAutoMessage.sort'
        ],
        'conditions' => [
            'TAutoMessage.m_companies_id' => $this->userInfo['MCompany']['id']
//            'TAutoMessage.del_flg != ' => 1
        ],
        'order' => [
            'TAutoMessage.sort' => 'asc',
            'TAutoMessage.id' => 'asc'
        ],
        'limit' => 1,
        'recursive' => -1
    ];
    $params['fields'] = [
        'TAutoMessage.id',
        'TAutoMessage.sort'
    ];
    unset($params['limit']);
    $prevSort = $this->TAutoMessage->find('list', $params);
    //ソート順のリセットはID順とする
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
          'TAutoMessage' => [
              'id' => $id,
              'sort' => $prevSortKeys[$i]
          ]
      ];
      if (!$this->TAutoMessage->validates()) {
        $ret = false;
        break;
      }
      if (!$this->TAutoMessage->save($saveData)) {
        $ret = false;
        break;
      }
      $i++;
    }
    if ($ret) {
      $this->TAutoMessage->commit();
      return true;
    }
    else {
      $this->TAutoMessage->rollback();
      return false;
    }
  }

  /**
   * ステータス更新
   * @return void
   * */
  public function changeStatus() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $inputData = $this->request->query;
    $case = gettype($inputData['status']);
    $activeFlg = 1;
    if ($case === "boolean" && $inputData['status'] || $case === "string" && strcmp($inputData['status'], 'true') === 0) {
      $activeFlg = 0;
    }
    $this->TAutoMessage->updateAll(
      ['active_flg'=>$activeFlg],
      [
        'id' => $inputData['targetList'],
        'm_companies_id' => $this->userInfo['MCompany']['id'],
        'del_flg' => 0
      ]
    );
  }

  /**
   * 保存機能
   * @param array $inputData
   * @return void
   * */
  private function _entry($saveData) {
    $errors = [];
    $saveData['TAutoMessage']['m_companies_id'] = $this->userInfo['MCompany']['id'];
    if(array_key_exists ('lastPage',$saveData)){
      $nextPage = $saveData['lastPage'];
    }
    else{
      $nextPage = '1';
    }

    $this->TAutoMessage->begin();
    if ( empty($saveData['TAutoMessage']['id']) ) {
      //新規追加
      $this->TAutoMessage->create();
      $params = [
          'fields' => [
              'TAutoMessage.sort'
          ],
          'conditions' => [
              'TAutoMessage.m_companies_id' => $this->userInfo['MCompany']['id']
//              'TAutoMessage.del_flg != ' => 1
          ],
          'order' => [
              'TAutoMessage.sort' => 'desc',
              'TAutoMessage.id' => 'desc'
          ],
          'limit' => 1,
          'recursive' => -1
      ];
      $lastData = $this->TAutoMessage->find('first', $params);
      if($lastData){
        if($lastData['TAutoMessage']['sort'] === '0'
            || $lastData['TAutoMessage']['sort'] === 0
            || $lastData['TAutoMessage']['sort'] === null){
              //ソート順が登録されていなかったらソート順をセットする
              if(! $this->remoteSetSort()){
                $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
                return false;
              }
              //もう一度ソートの最大値を取り直す
              $lastData = $this->TAutoMessage->find('first', $params);
        }
      }
      $nextSort = 1;
      if (!empty($lastData)) {
        $nextSort = intval($lastData['TAutoMessage']['sort']) + 1;
      }
      $saveData['TAutoMessage']['sort'] = $nextSort;
    }

    // メール送信設定の値を抜く
    $toAddresses = '';
    $subject = '';
    $fromName = '';
    $templateId = 0;
    if(!empty($saveData['main']['send_mail_flg']) && intval($saveData['main']['send_mail_flg']) === C_CHECK_ON) {
      $this->request->data['TAutoMessage']['send_mail_flg'] = intval($saveData['main']['send_mail_flg']);
      $saveData['TAutoMessage']['send_mail_flg'] = intval($saveData['main']['send_mail_flg']);
      foreach($saveData['main'] as $k => $v) {
        if(preg_match('/mail_address_[1-5]/', $k)) {
          $this->request->data['TAutoMessage'][$k] = $v;
          if(!empty($v)) {
            if($toAddresses !== '') {
              $toAddresses .= ',';
            }
            $toAddresses .= $v;
          }
        }
        if(strpos($k, 'subject') === 0) {
          $this->request->data['TAutoMessage']['subject'] = $v;
          $subject = $v;
        }
        if(strpos($k, 'from_name') === 0) {
          $this->request->data['TAutoMessage']['from_name'] = $v;
          $fromName = $v;
        }
      }
      $this->MMailTransmissionSetting->begin();
      if(empty($saveData['TAutoMessage']['m_mail_transmission_settings_id'])) {
        $this->MMailTransmissionSetting->create();
      } else {
        $this->MMailTransmissionSetting->read(null, $saveData['TAutoMessage']['m_mail_transmission_settings_id']);
      }
      $this->MMailTransmissionSetting->set([
        'm_companies_id' => $this->userInfo['MCompany']['id'],
        'from_name' => $fromName,
        'to_address' => $toAddresses,
        'subject' => $subject
      ]);
      $validate = $this->MMailTransmissionSetting->validates();
      $errors = $this->MMailTransmissionSetting->validationErrors;
      if(empty($errors)){
        $this->MMailTransmissionSetting->save();
        if(empty($saveData['TAutoMessage']['m_mail_transmission_settings_id'])) {
          $saveData['TAutoMessage']['m_mail_transmission_settings_id'] = $this->MMailTransmissionSetting->getLastInsertId();
        }
        if(empty($saveData['TAutoMessage']['m_mail_template_id'])) {
          $templateData = $this->MMailTemplate->find('first',[
              'conditions' => [
                  'm_companies_id' => $this->userInfo['MCompany']['id'],
                  'mail_type_cd' => 'AM001'
              ]
          ]);
          if(!empty($templateData)) {
            $saveData['TAutoMessage']['m_mail_template_id'] = $templateData['MMailTemplate']['id'];
          }
        }
      } else {
        $this->TAutoMessage->rollback();
        $this->MMailTransmissionSetting->rollback();
        $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
        $this->set('errors', $errors);
        $this->set('lastPage', $nextPage);
        return;
      }
    } else {
      $saveData['main']['send_mail_flg'] = 0;
      $saveData['main']['m_mail_transmission_settings_id'] = 0;
      $saveData['main']['m_mail_template_id'] = 0;
      $saveData['TAutoMessage']['send_mail_flg'] = 0;
    }

    $this->TAutoMessage->set($saveData);

    $validate = $this->TAutoMessage->validates();
    $errors = $this->TAutoMessage->validationErrors;

    // その他のチェック
    if ( !empty($saveData['TAutoMessage']) ) {
      $activity = json_decode($saveData['TAutoMessage']['activity']);

      /* 項目ごとの設定数上限チェック */
      $tmpMessage = "%sの場合、『%s』は%d個まで設定可能です";

      foreach((array)$activity->conditions as $key => $val) {
        $setting = $this->outMessageTriggerList[$key];
        if ( !isset($setting['createLimit'][$activity->conditionType]) ) continue;
        if ( count($val) > intval($setting['createLimit'][$activity->conditionType]) ) {
          $validate = false;
          $errors['triggers'][$setting['key']] = sprintf($tmpMessage, $this->outMessageIfType[$activity->conditionType], $setting['label'], $setting['createLimit'][$activity->conditionType]);
        }
      }
    }

    if ($validate) {
      //オートメッセージ　営業時間を4番目に入れたので並び替え処理
      $changeEditData = json_decode($saveData['TAutoMessage']['activity'],true);
      foreach($changeEditData['conditions'] as $key => $val){
        if($key === 4) {
          unset($changeEditData['conditions'][4]);
          $changeEditData['conditions'][10] = json_decode($saveData['TAutoMessage']['activity'],true)['conditions'][4];
        }
        if($key >= 5) {
          unset($changeEditData['conditions'][$key]);
          $changeEditData['conditions'][$key-1] = json_decode($saveData['TAutoMessage']['activity'],true)['conditions'][$key];
        }
      }
      $changeEditData = json_encode($changeEditData);
      $saveData['TAutoMessage']['activity'] = $changeEditData;
      if( $this->TAutoMessage->save($saveData,false) ) {
        $this->TAutoMessage->commit();
        $this->MMailTransmissionSetting->commit();
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
        $this->redirect('/TAutoMessages/index/page:'.$nextPage);
      }
    }
    else {
      $this->TAutoMessage->rollback();
      $this->MMailTransmissionSetting->rollback();
      $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
    }
    $this->set('errors', $errors);
    $this->set('lastPage', $nextPage);
  }

  /**
   * ビュー部品セット
   * @return void
   * */
  private function _viewElement() {
    // TODO out -> auto に変更
    // トリガー種別
    $this->set('outMessageTriggerType', Configure::read('outMessageTriggerType'));
    // 条件設定種別
    $this->set('outMessageIfType', $this->outMessageIfType);
    // 条件リスト
    $this->set('outMessageTriggerList', $this->outMessageTriggerList);
    // アクション種別
    $this->set('outMessageActionType', Configure::read('outMessageActionType'));
    // ウィジェット種別
    $this->set('outMessageWidgetOpenType', Configure::read('outMessageWidgetOpenType'));
    // テキストエリア
    $this->set('outMessageTextarea', Configure::read('outMessageTextarea'));
    //cv
    $this->set('outMessageCvType', Configure::read('outMessageCvType'));
    // 有効無効
    $this->set('outMessageAvailableType', Configure::read('outMessageAvailableType'));
    // 画像パス
    $this->set('gallaryPath', C_NODE_SERVER_ADDR.C_NODE_SERVER_FILE_PORT.'/img/widget/');
    // 最後に表示していたページ番号
    if(!empty($this->request->query['lastpage'])){
      $this->set('lastPage', $this->request->query['lastpage']);
    }
  }

  /**
   * @param $type
   * @param $conditions
   * @param $activity
   * @return mixed
   */
  private function convertOldIFData($type, $conditions, $activity, $actualType)
  {
    // ページ、参照元URL、発言内容、最初に訪れたページ、前のページの旧IF対応
    if ($type === C_AUTO_TRIGGER_STAY_PAGE) {
      $arr = array();
      foreach ($conditions as $index => $settings) {
        if (array_key_exists('keyword', $settings)) {
          $newSettings = array(
              "targetName" => "1",
              "keyword_contains" => "",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "stayPageCond" => 1
          );
          $newSettings["targetName"] = $settings['targetName'];
          switch ($settings['stayPageCond']) {
            case 1: // 完全一致
              $newSettings["keyword_contains"] = $settings['keyword'];
              $newSettings["stayPageCond"] = 1;
              break;
            case 2: // 部分一致
              $newSettings["keyword_contains"] = $settings['keyword'];
              $newSettings["stayPageCond"] = 2;
              break;
            case 3: // 不一致
              $newSettings["keyword_exclusions"] = $settings['keyword'];
              $newSettings["stayPageCond"] = 1;
              break;
          }
          array_push($arr, $newSettings);
        } else {
          array_push($arr, $settings);
        }
      }
      $activity['conditions'][$actualType] = $arr;
    }
    if ($type === C_AUTO_TRIGGER_REFERRER) {
      $arr = array();
      foreach ($conditions as $index => $settings) {
        if (array_key_exists('keyword', $settings)) {
          $newSettings = array(
              "keyword_contains" => "",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "referrerCond" => 2
          );
          switch ($settings['referrerCond']) {
            case 1: // 完全一致
              $newSettings["keyword_contains"] = $settings['keyword'];
              $newSettings["referrerCond"] = 1;
              break;
            case 2: // 部分一致
              $newSettings["keyword_contains"] = $settings['keyword'];
              $newSettings["referrerCond"] = 2;
              break;
            case 3: // 不一致
              $newSettings["keyword_exclusions"] = $settings['keyword'];
              $newSettings["referrerCond"] = 1;
              break;
          }
          array_push($arr, $newSettings);
        } else {
          array_push($arr, $settings);
        }
      }
      $activity['conditions'][$actualType] = $arr;
    }
    if ($type === C_AUTO_TRIGGER_SPEECH_CONTENT) {
      $arr = array();
      foreach ($conditions as $index => $settings) {
        if (array_key_exists('speechContent', $settings)) {
          $newSettings = array(
              "keyword_contains" => "",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "speechContentCond" => "1",
              "triggerTimeSec" => 3,
              "speechTriggerCond" => "1"
          );
          $newSettings['speechContentCond'] = $settings['speechContentCond'];
          $newSettings['triggerTimeSec'] = $settings['triggerTimeSec'];
          $newSettings['speechTriggerCond'] = $settings['speechTriggerCond'];
          switch ($settings['speechContentCond']) {
            case 1: // 完全一致
              $newSettings["keyword_contains"] = $settings['speechContent'];
              $newSettings["speechContentCond"] = 1;
              break;
            case 2: // 部分一致
              $newSettings["keyword_contains"] = $settings['speechContent'];
              $newSettings["speechContentCond"] = 2;
              break;
            case 3: // 不一致
              $newSettings["keyword_exclusions"] = $settings['speechContent'];
              $newSettings["speechContentCond"] = 1;
              break;
          }
          array_push($arr, $newSettings);
        } else {
          array_push($arr, $settings);
        }
      }
      $activity['conditions'][$actualType] = $arr;
    }
    if ($type === C_AUTO_TRIGGER_STAY_PAGE_OF_FIRST) {
      $arr = array();
      foreach ($conditions as $index => $settings) {
        if (array_key_exists('keyword', $settings)) {
          $newSettings = array(
              "targetName" => "1",
              "keyword_contains" => "",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "stayPageCond" => 1
          );
          $newSettings["targetName"] = $settings['targetName'];
          switch ($settings['stayPageCond']) {
            case 1: // 完全一致
              $newSettings["keyword_contains"] = $settings['keyword'];
              $newSettings["stayPageCond"] = 1;
              break;
            case 2: // 部分一致
              $newSettings["keyword_contains"] = $settings['keyword'];
              $newSettings["stayPageCond"] = 2;
              break;
            case 3: // 不一致
              $newSettings["keyword_exclusions"] = $settings['keyword'];
              $newSettings["stayPageCond"] = 1;
              break;
          }
          array_push($arr, $newSettings);
        } else {
          array_push($arr, $settings);
        }
      }
      $activity['conditions'][$actualType] = $arr;
    }
    if ($type === C_AUTO_TRIGGER_STAY_PAGE_OF_PREVIOUS) {
      $arr = array();
      foreach ($conditions as $index => $settings) {
        if (array_key_exists('keyword', $settings)) {
          $newSettings = array(
              "targetName" => "1",
              "keyword_contains" => "",
              "keyword_contains_type" => "1",
              "keyword_exclusions" => "",
              "keyword_exclusions_type" => "1",
              "stayPageCond" => 1
          );
          $newSettings["targetName"] = $settings['targetName'];
          switch ($settings['stayPageCond']) {
            case 1: // 完全一致
              $newSettings["keyword_contains"] = $settings['keyword'];
              $newSettings["stayPageCond"] = 1;
              break;
            case 2: // 部分一致
              $newSettings["keyword_contains"] = $settings['keyword'];
              $newSettings["stayPageCond"] = 2;
              break;
            case 3: // 不一致
              $newSettings["keyword_exclusions"] = $settings['keyword'];
              $newSettings["stayPageCond"] = 1;
              break;
          }
          array_push($arr, $newSettings);
        } else {
          array_push($arr, $settings);
        }
      }
      $activity['conditions'][$actualType] = $arr;
    }
    return $activity;
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

    //営業時間設定確認
    $operatingHourData = $this->MOperatingHour->find('first', ['conditions' => [
      'm_companies_id' => $this->userInfo['MCompany']['id']
    ]]);
    if(empty($operatingHourData)) {
      $operatingHourData['MOperatingHour']['active_flg'] = 2;
    }
    $this->set('operatingHourData',$operatingHourData['MOperatingHour']['active_flg']);
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
