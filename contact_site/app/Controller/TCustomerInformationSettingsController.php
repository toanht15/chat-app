<?php
/**
 * TCustomerInformationSettings
 * 訪問ユーザー情報設定
 * @property TCustomerInformationSettings $TCustomerInformationSettings
 */
class TCustomerInformationSettingsController extends AppController {
  public $components = ['NodeSettingsReload'];
  public $uses = ['TCustomerInformationSetting','TCustomVariable', 'MCustomer'];
  public $paginate = [
    'TCustomerInformationSetting' => [
      'limit' => 100,
      'order' => [
        'TCustomerInformationSetting.sort' => 'asc',
        'TCustomerInformationSetting.id' => 'asc'
      ],
      'fields' => ['TCustomerInformationSetting.*'],
      'recursive' => -1
    ]
  ];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', '訪問ユーザー情報設定');
  }

  /* *
   * 一覧画面
   * @return void
   * */
  public function index() {
    Configure::write('debug', 0);
    //DB作成後復元
    $this->paginate['TCustomerInformationSetting']['conditions']['TCustomerInformationSetting.m_companies_id'] = $this->userInfo['MCompany']['id'];
    $data = $this->paginate('TCustomerInformationSetting');
    $documentList = $this->TCustomVariable->find('list', $this->_setParamsVariable());
    $this->set('variableList',$documentList);
    $this->set('tCustomerInformationSettingList', $data);
  }

  /* *
   * 登録,更新画面
   * @return void
   * */
  public function remoteOpenEntryForm() {
  	ini_set('display_errors',1);
    Configure::write('debug', 2);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    if ( strcmp($this->request->data['type'], 2) === 0 ) {
      $this->request->data = $this->TCustomerInformationSetting->read(null, $this->request->data['id']);
    }
    $ShowList = $this->TCustomerInformationSetting->find('list', $this->_setParams());
    $documentList = $this->TCustomVariable->find('list', $this->_setParamsVariable());
    $this->set('variableList',$documentList);
    $this->set('FlgList',$ShowList);
    $this->render('/Elements/TCustomerInformationSettings/remoteEntry');
  }
  /* *
   * 保存処理
   * @return void
   * */
  public function remoteSaveEntryForm() {
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $saveData = [];
    $errorMessage = [];

    if (!empty($this->request->data['customerinformationsettingId'])) {
      $this->TCustomerInformationSetting->recursive = -1;
      $saveData = $this->TCustomerInformationSetting->read(null, $this->request->data['customerinformationsettingId']);
    }else{
      $this->TCustomerInformationSetting->create();
      $params = [
        'fields' => [
          'TCustomerInformationSetting.sort'
        ],
        'conditions' => [
          'TCustomerInformationSetting.m_companies_id' => $this->userInfo['MCompany']['id']
        ],
        'order' => [
          'TCustomerInformationSetting.sort' => 'desc',
          'TCustomerInformationSetting.id' => 'desc'
        ],
        'limit' => 1,
        'recursive' => -1
      ];

      $lastData = $this->TCustomerInformationSetting->find('first', $params);
      if($lastData){
        if($lastData['TCustomerInformationSetting']['sort'] === '0'
        || $lastData['TCustomerInformationSetting']['sort'] === 0
        || $lastData['TCustomerInformationSetting']['sort'] === null){
          //ソート順が登録されていなかったらソート順をセットする
          if(! $this->remoteSetSort()){
            $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
            return false;
          }
          //もう一度ソートの最大値を取り直す
          $lastData = $this->TCustomerInformationSetting->find('first', $params);
        }
      }
      $nextSort = 1;
      if (!empty($lastData)) {
      	//今現在登録されているsort値の最大に1を足した値を保存するための処理
        $nextSort = intval($lastData['TCustomerInformationSetting']['sort']) + 1;
      }
      $saveData['TCustomerInformationSetting']['sort'] = $nextSort;

    }
    $oldItemName = "";
    $newItemName = "";
    $itemNameChanged = false;
    if(isset($saveData['TCustomerInformationSetting']['item_name'])){
      if(strcmp($saveData['TCustomerInformationSetting']['item_name'], $this->request->data['item_name']) !== 0) {
        $itemNameChanged = true;
        $oldItemName = $saveData['TCustomerInformationSetting']['item_name'];
        $newItemName = $this->request->data['item_name'];
      }
    }

    $saveData['TCustomerInformationSetting']['m_companies_id'] = $this->userInfo['MCompany']['id'];
    $saveData['TCustomerInformationSetting']['item_name'] = $this->request->data['item_name'];
    $saveData['TCustomerInformationSetting']['input_type'] = $this->request->data['input_type'];
    if(isset($this->request->data['input_option'])){
    $saveData['TCustomerInformationSetting']['input_option'] = $this->request->data['input_option'];
    }else{
    $saveData['TCustomerInformationSetting']['input_option'] ="";
    }
    $saveData['TCustomerInformationSetting']['show_realtime_monitor_flg'] = $this->request->data['show_realtime_monitor_flg'];
    $saveData['TCustomerInformationSetting']['show_send_mail_flg'] = $this->request->data['show_send_mail_flg'];
    $saveData['TCustomerInformationSetting']['sync_custom_variable_flg'] = $this->request->data['sync_custom_variable_flg'];
    $saveData['TCustomerInformationSetting']['t_custom_variables_id'] = $this->request->data['t_custom_variables_id'];
    $saveData['TCustomerInformationSetting']['comment'] = $this->request->data['comment'];
    // const
    $this->TCustomerInformationSetting->set($saveData);
    $this->TCustomerInformationSetting->begin();

    // バリデーションチェックでエラーが出た場合
    if ( $this->TCustomerInformationSetting->save() ) {
      try {
        if($itemNameChanged) {
          $this->log($this->userInfo['MCompany']['company_key'].': ITEM NAME CHANGED before => '.$oldItemName.' after => '.$newItemName, LOG_INFO);
          $this->replaceAllCustomerInformationKey($oldItemName, $newItemName);
        }
        $this->TCustomerInformationSetting->commit();
      } catch(Exception $e) {
        $this->log('訪問ユーザ情報のキー書き換え時にエラーが発生しました。', LOG_ERR);
        $this->TCustomerInformationSetting->rollback();
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
        return new CakeResponse(['body' => json_encode(array('result' => false))]);
      }
      NodeSettingsReloadComponent::reloadCustomVariableSettings($this->userInfo['MCompany']['company_key']);
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
    }
    else {
      $this->TCustomerInformationSetting->rollback();
    }

    $errorMessage = $this->TCustomerInformationSetting->validationErrors;
    return new CakeResponse(['body' => json_encode($errorMessage)]);
  }

  private function replaceAllCustomerInformationKey($oldKey, $newKey) {
    $allCustomerInfo = $this->MCustomer->find('all', array(
      'conditions' => array(
        'm_companies_id' => $this->userInfo['MCompany']['id']
      )
    ));
    if(count($allCustomerInfo) > 0) {
      $this->MCustomer->begin();
      foreach($allCustomerInfo as $index => $data) {
        $informations = json_decode($data['MCustomer']['informations'], TRUE);
        $newInformaions = array();
        foreach($informations as $key => $value) {
          if(strcmp($oldKey, $key) === 0) {
            $newInformations[$newKey] = $value;
          } else {
            $newInformations[$key] = $value;
          }
        }
        $data['MCustomer']['informations'] = json_encode($newInformations);
        if(!$data['MCustomer']['informations']) {
          $this->MCustomer->rollback();
          throw new Exception('書き換え中にエラーが発生しました。');
        }
        $this->MCustomer->create(false);
        $this->MCustomer->save($data);
      }
      $this->MCustomer->commit();
    }
  }

  /* *
   * 削除
   * @return void
   * */
  public function remoteDeleteUser() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $this->TCustomerInformationSetting->recursive = -1;
    $selectedList = $this->request->data['selectedList'];
    $this->TCustomerInformationSetting->begin();
    $res = true;
    foreach($selectedList as $key => $val){
      if (! $this->TCustomerInformationSetting->delete($val) ) {
        $res = false;
      }
    }
    if($res){
      $this->TCustomerInformationSetting->commit();
      NodeSettingsReloadComponent::reloadCustomVariableSettings($this->userInfo['MCompany']['company_key']);
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
    }
    else{
      $this->TCustomerInformationSetting->rollback();
      $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
    }
  }

  /* *
   * コピー処理
   * @return void
   * */
  public function remoteCopyEntryForm() {
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $selectedList = $this->request->data['selectedList'];
    //コピー元の訪問ユーザー情報リスト取得
    foreach($selectedList as $value){
      $copyData[] = $this->TCustomerInformationSetting->read(null, $value);
    }
    $errorMessage = [];
    //コピー元の訪問ユーザー情報リストの数だけ繰り返し
    $res = true;
    foreach($copyData as $value){
      $this->TCustomerInformationSetting->create();
      $saveData = [];
      $params = [
          'fields' => [
              'TCustomerInformationSetting.sort'
          ],
          'conditions' => [
              'TCustomerInformationSetting.m_companies_id' => $this->userInfo['MCompany']['id']
          ],
          'order' => [
              'TCustomerInformationSetting.sort' => 'desc',
              'TCustomerInformationSetting.id' => 'desc'
          ],
          'limit' => 1,
          'recursive' => -1
      ];
      $lastData = $this->TCustomerInformationSetting->find('first', $params);
      if($lastData['TCustomerInformationSetting']['sort'] === '0'
          || $lastData['TCustomerInformationSetting']['sort'] === 0
          || $lastData['TCustomerInformationSetting']['sort'] === null){
            //ソート順が登録されていなかったらソート順をセットする
            if(! $this->remoteSetSort()){
              $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
              return false;
            }
            //もう一度ソートの最大値を取り直す
            $lastData = $this->TCustomerInformationSetting->find('first', $params);
      }
      $nextSort = 1;
      if (!empty($lastData)) {
        $nextSort = intval($lastData['TCustomerInformationSetting']['sort']) + 1;
      }
      $saveData['TCustomerInformationSetting']['sort'] = $nextSort;
      $saveData['TCustomerInformationSetting']['m_companies_id'] = $value['TCustomerInformationSetting']['m_companies_id'];
      $saveData['TCustomerInformationSetting']['item_name'] = $value['TCustomerInformationSetting']['item_name'].'コピー';
      $saveData['TCustomerInformationSetting']['input_type'] = $value['TCustomerInformationSetting']['input_type'];
      $saveData['TCustomerInformationSetting']['input_option'] = $value['TCustomerInformationSetting']['input_option'];
      $saveData['TCustomerInformationSetting']['show_realtime_monitor_flg'] = 0;
      $saveData['TCustomerInformationSetting']['show_send_mail_flg'] = $value['TCustomerInformationSetting']['show_send_mail_flg'];
      $saveData['TCustomerInformationSetting']['sync_custom_variable_flg'] = $value['TCustomerInformationSetting']['sync_custom_variable_flg'];
      $saveData['TCustomerInformationSetting']['t_custom_variables_id'] = $value['TCustomerInformationSetting']['t_custom_variables_id'];
      $saveData['TCustomerInformationSetting']['comment'] = $value['TCustomerInformationSetting']['comment'];
      $this->TCustomerInformationSetting->set($saveData);
      $this->TCustomerInformationSetting->begin();
      // バリデーションチェックでエラーが出た場合
      if($res){
        if (! $this->TCustomerInformationSetting->save() ) {
          $res = false;
          $errorMessage = $this->TCustomerInformationSetting->validationErrors;
          $this->TCustomerInformationSetting->rollback();
        }
        else{
          $this->TCustomerInformationSetting->commit();
          NodeSettingsReloadComponent::reloadCustomVariableSettings($this->userInfo['MCompany']['company_key']);
          $this->Session->delete('dstoken');
          $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
        }
      }
    }
  }

  /**
   * 訪問ユーザー情報ソート順更新
   *
   * */
  public function remoteSaveSort(){
    Configure::write('debug', 2);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    if ( !$this->request->is('ajax') ) return false;
    if ( !empty($this->params->data['list']) ) {
      $this->TCustomerInformationSetting->begin();
      $list = $this->params->data['list'];
      $sortNoList = $this->params->data['sortNolist'];
      sort($sortNoList);

      $this->log($list,LOG_DEBUG);
      /* 現在の並び順を取得 */
      $params = $this->paginate['TCustomerInformationSetting'];
      $params['fields'] = [
          'TCustomerInformationSetting.id',
          'TCustomerInformationSetting.sort'
      ];
      $params['conditions']['TCustomerInformationSetting.m_companies_id'] = $this ->userInfo['MCompany']['id'];
      unset($params['limit']);
      $prevSort = $this->TCustomerInformationSetting->find('list', $params);
      //新しくソート順を設定したため、空で来ることがある
      $reset_flg = false;
      foreach($prevSort as $key => $val){
        //設定されていない値'0'が一つでも入っていたらsortをリセット
        if($val === '0' || $val === 0 || $val === null){
          $reset_flg = true;
        }
      }
      if($reset_flg){
      	//ソート順が登録されていなかった場合の処理
      	if(! $this->remoteSetSort()){
      		$this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
      		return false;
        }
        //$prevSort = $this->TCustomerInformationSetting->find('list', $params);243行目付近で定義されているので不要?
        //この時点では$sortNoListが空の為作成する
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
        if(isset($prevSort[$id])){
          $saveData = [
              'TCustomerInformationSetting' => [
                  'id' => $id,
                  'sort' => $sortNoList[$i]
              ]
          ];
          if (!$this->TCustomerInformationSetting->validates()) {
            $ret = false;
            break;
          }
          if (!$this->TCustomerInformationSetting->save($saveData)) {
            $ret = false;
            break;
          }
        } else {
          // 送信されたカスタム変数と現在DBに存在するカスタム変数に差がある場合
          $this->TCustomerInformationSetting->rollback();
          $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.configChanged'));
          return;
        }
      }
      if ($ret) {
        $this->TCustomerInformationSetting->commit();
        NodeSettingsReloadComponent::reloadCustomVariableSettings($this->userInfo['MCompany']['company_key']);
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
      }
      else {
        $this->TCustomerInformationSetting->rollback();
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
      }
    }
  }


  /**
   * カスタム変数ソート順を現在のID順でセット
   *
   * */
  public function remoteSetSort(){
    $this->TCustomerInformationSetting->begin();
    /* 現在の並び順を取得 */
    $this->paginate['TCustomerInformationSetting']['conditions']['TCustomerInformationSetting.m_companies_id'] = $this->userInfo['MCompany']['id'];
    $params = [
      'fields' => [
        'TCustomerInformationSetting.sort'
      ],
      'conditions' => [
         'TCustomerInformationSetting.m_companies_id' => $this->userInfo['MCompany']['id']
      ],
      'order' => [
        'TCustomerInformationSetting.sort' => 'asc',
        'TCustomerInformationSetting.id' => 'asc'
      ],
      'limit' => 1,
      'recursive' => -1
    ];
   $params['fields'] = [
      'TCustomerInformationSetting.id',
      'TCustomerInformationSetting.sort'
    ];
    unset($params['limit']);
    $prevSort = $this->TCustomerInformationSetting->find('list', $params);
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
          'TCustomerInformationSetting' => [
              'id' => $id,
              'sort' => $prevSortKeys[$i]
          ]
      ];
      if (!$this->TCustomerInformationSetting->validates()) {
        $ret = false;
        break;
      }
      if (!$this->TCustomerInformationSetting->save($saveData)) {
        $ret = false;
        break;
      }
      $i++;
    }
    if ($ret) {
      $this->TCustomerInformationSetting->commit();
      NodeSettingsReloadComponent::reloadCustomVariableSettings($this->userInfo['MCompany']['company_key']);
      return true;
    }
    else {
      $this->TCustomerInformationSetting->rollback();
      return false;
    }
  }

  private function _setParams(){
    $params = [
      'order' => [
        'TCustomerInformationSetting.sort' => 'asc',
        'TCustomerInformationSetting.id' => 'asc'
      ],
      'fields' => [
        'TCustomerInformationSetting.show_realtime_monitor_flg'
      ],
      'conditions' => [
        'TCustomerInformationSetting.m_companies_id' => $this->userInfo['MCompany']['id']
      ],
      'recursive' => -1
    ];
    return $params;
  }

  private function _setParamsVariable(){
  $params = [
    'order' => [
      'TCustomVariable.sort' => 'asc',
      'TCustomVariable.id' => 'asc'
    ],
    'fields' => [
      'TCustomVariable.id',
      'TCustomVariable.variable_name'
     ],
     'conditions' => [
       'TCustomVariable.m_companies_id' => $this->userInfo['MCompany']['id']
     ],
     'recursive' => -1
     ];
    return $params;
  }
}
