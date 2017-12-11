<?php
/**
 * TAutoMessagesController controller.
 * ユーザーマスタ
 */
class TAutoMessagesController extends AppController {
  public $uses = ['TAutoMessage','MOperatingHour', 'MMailTransmissionSetting', 'MMailTemplate'];
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
    $this->set('settingList', $this->paginate('TAutoMessage'));
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
      if (!empty($editData[0]['TAutoMessage']['send_mail_flg'])) {
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

      $changeEditData = json_encode($changeEditData);

      $value['TAutoMessage']['activity'] = $changeEditData;

      $saveData['TAutoMessage']['sort'] = $nextSort;
      $saveData['TAutoMessage']['m_companies_id'] = $value['TAutoMessage']['m_companies_id'];
      $saveData['TAutoMessage']['name'] = $value['TAutoMessage']['name'].'コピー';
      $saveData['TAutoMessage']['trigger_type'] = $value['TAutoMessage']['trigger_type'];
      $saveData['TAutoMessage']['activity'] = $value['TAutoMessage']['activity'];
      $saveData['TAutoMessage']['action_type'] = $value['TAutoMessage']['action_type'];
      $saveData['TAutoMessage']['active_flg'] = $value['TAutoMessage']['active_flg'];
      $saveData['TAutoMessage']['del_flg'] = $value['TAutoMessage']['del_flg'];

      $this->TAutoMessage->set($saveData);
      $this->TAutoMessage->begin();
      // バリデーションチェックでエラーが出た場合
      if($res){
        if(!$this->TAutoMessage->validates()) {
          $res = false;
          $errorMessage = $this->TAutoMessage->validationErrors;
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
    // 最後に表示していたページ番号
    if(!empty($this->request->query['lastpage'])){
      $this->set('lastPage', $this->request->query['lastpage']);
    }
  }



}
