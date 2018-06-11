<?php
/**
 * TCustomerInformationSettings
 * 訪問ユーザー情報設定
 * @property TCustomerInformationSettings $TCustomerInformationSettings
 */
class TCustomerInformationSettingsController extends AppController {
  public $uses = ['TCustomerInformationSettings'];
  //DB作成後復元
  /*public $paginate = [
    'TCustomerInformationSettings' => [
      'limit' => 100,
      'order' => [
        'TCustomerInformationSettings.sort' => 'asc',
        'TCustomerInformationSettings.id' => 'asc'
      ],
      'fields' => ['TCustomerInformationSettings.*'],
      'recursive' => -1
    ]
  ];*/

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', '訪問ユーザー情報設定');
  }

  /* *
   * 一覧画面
   * @return void
   * */
  public function index() {
  	Configure::write('debug', 2);
  	//DB作成後復元
  	//$this->paginate['TCustomerInformationSetting']['conditions']['TCustomerInformationSetting.m_companies_id'] = $this->userInfo['MCompany']['id'];
    //$data = $this->paginate('TCustomerInformationSetting');
  	//$documentList = $this->TCustomerInformationSetting->find('all', $this->_setParams());
    $this->set('TCustomerInformationSettingList', $data);
  }

  /* *
   * 登録,更新画面
   * @return void
   * */
  public function remoteOpenEntryForm() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    // const
    if ( strcmp($this->request->data['type'], 2) === 0 ) {
      $this->request->data = $this->TCustomerInformationSetting->read(null, $this->request->data['id']);
    }
    $this->render('/Elements/TCustomerInformationSettings/remoteEntry');
  }
  /* *
   * 保存処理
   * @return void
   * */
  public function remoteSaveEntryForm() {
  	ini_set('display_errors',1);
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $saveData['TCustomerInformationSetting']['m_compaines_id'] = $this->userInfo['MCompany']['id'];
    $errorMessage = [];

    if (empty($saveData['TCustomerInformationSetting']['id'])){
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
    $saveData['TCustomerInformationSetting']['m_companies_id'] = $this->userInfo['MCompany']['id'];
    $saveData['TCustomerInformationSetting']['variable_name'] = $this->request->data['variable_name'];
    $saveData['TCustomerInformationSetting']['attribute_value'] = $this->request->data['attribute_value'];
    $saveData['TCustomerInformationSetting']['comment'] = $this->request->data['comment'];
    // const
    $this->TCustomerInformationSetting->set($saveData);
    $this->TCustomerInformationSetting->begin();

    // バリデーションチェックでエラーが出た場合
    if ( $this->TCustomerInformationSetting->save() ) {
      $this->TCustomerInformationSetting->commit();
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
    }
    else {
      $this->TCustomerInformationSetting->rollback();
    }
    $errorMessage = $this->TCustomerInformationSetting->validationErrors;
    return new CakeResponse(['body' => json_encode($errorMessage)]);
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
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $selectedList = $this->request->data['selectedList'];
    //コピー元のカスタム変数リスト取得
    foreach($selectedList as $value){
      $copyData[] = $this->TCustomerInformationSetting->read(null, $value);
    }
    $errorMessage = [];
    //コピー元のカスタム変数リストの数だけ繰り返し
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
      $saveData['TCustomerInformationSetting']['variable_name'] = $value['TCustomerInformationSetting']['variable_name'].'コピー';
      $saveData['TCustomerInformationSetting']['attribute_value'] = $value['TCustomerInformationSetting']['attribute_value'];
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
          $this->Session->delete('dstoken');
          $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
        }
      }
    }
  }

  /**
   * カスタム変数ソート順更新
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
        'TCustomerInformationSetting.*'
      ],
      'conditions' => [
        'TCustomerInformationSetting.m_companies_id' => $this->userInfo['MCompany']['id']
      ],
      'recursive' => -1
    ];
    return $params;
  }
}
