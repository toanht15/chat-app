<?php
/**
 * TCustomVariable
 * カスタム変数
 * @property TCustomVariable $TCustomVariable
 */
class TCustomVariablesController extends AppController {
  public $uses = ['TCustomVariable'];
  public $paginate = [
    'TCustomVariable' => [
      'limit' => 100,
      'order' => [
        'TCustomVariable.sort' => 'asc',
        'TCustomVariable.id' => 'asc'
      ],
      'fields' => ['TCustomVariable.*'],
      'recursive' => -1
    ]
  ];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', 'カスタム変数');
  }

  /* *
   * 一覧画面
   * @return void
   * */
  public function index() {
  	Configure::write('debug', 2);
  	$this->paginate['TCustomVariable']['conditions']['TCustomVariable.m_companies_id'] = $this->userInfo['MCompany']['id'];
    $data = $this->paginate('TCustomVariable');
  	//$documentList = $this->TCustomVariable->find('all', $this->_setParams());
    $this->set('tCustomVariableList', $data);
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
      $this->request->data = $this->TCustomVariable->read(null, $this->request->data['id']);
    }
    $this->render('/Elements/TCustomVariables/remoteEntry');
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
    $saveData = [];
    $errorMessage = [];

    if (!empty($this->request->data['customvariableId'])) {
      $this->TCustomVariable->recursive = -1;
      $saveData = $this->TCustomVariable->read(null, $this->request->data['customvariableId']);
    }
    else {
      $this->TCustomVariable->create();
      $params = [
          'fields' => [
              'TCustomVariable.sort'
          ],
          'conditions' => [
              'TCustomVariable.m_companies_id' => $this->userInfo['MCompany']['id']
          ],
          'order' => [
              'TCustomVariable.sort' => 'desc',
              'TCustomVariable.id' => 'desc'
          ],
          'limit' => 1,
          'recursive' => -1
      ];
      $lastData = $this->TCustomVariable->find('first', $params);
      if($lastData){
        if($lastData['TCustomVariable']['sort'] === '0'
            || $lastData['TCustomVariable']['sort'] === 0
            || $lastData['TCustomVariable']['sort'] === null){
              //ソート順が登録されていなかったらソート順をセットする
              if(! $this->remoteSetSort()){
                $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
                return false;
              }
              //もう一度ソートの最大値を取り直す
              $lastData = $this->TCustomVariable->find('first', $params);
        }
      }
      $nextSort = 1;
      if (!empty($lastData)) {
        $nextSort = intval($lastData['TCustomVariable']['sort']) + 1;
      }
      $saveData['TCustomVariable']['sort'] = $nextSort;
    }
    $saveData['TCustomVariable']['m_companies_id'] = $this->userInfo['MCompany']['id'];
    $saveData['TCustomVariable']['variable_name'] = $this->request->data['variable_name'];
    $saveData['TCustomVariable']['attribute_value'] = $this->request->data['attribute_value'];
    $saveData['TCustomVariable']['comment'] = $this->request->data['comment'];
    // const
    $this->TCustomVariable->set($saveData);
    $this->TCustomVariable->begin();

    // バリデーションチェックでエラーが出た場合
    if ( $this->TCustomVariable->save() ) {
      $this->TCustomVariable->commit();
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
    }
    else {
      $this->TCustomVariable->rollback();
    }
    $errorMessage = $this->TCustomVariable->validationErrors;
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
    $this->TCustomVariable->recursive = -1;
    $selectedList = $this->request->data['selectedList'];
    $this->TCustomVariable->begin();
    $res = true;
    foreach($selectedList as $key => $val){
      if (! $this->TCustomVariable->delete($val) ) {
        $res = false;
      }
    }
    if($res){
      $this->TCustomVariable->commit();
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
    }
    else{
      $this->TCustomVariable->rollback();
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
      $copyData[] = $this->TCustomVariable->read(null, $value);
    }
    $errorMessage = [];
    //コピー元のカスタム変数リストの数だけ繰り返し
    $res = true;
    foreach($copyData as $value){
      $this->TCustomVariable->create();
      $saveData = [];
      $params = [
          'fields' => [
              'TCustomVariable.sort'
          ],
          'conditions' => [
              'TCustomVariable.m_companies_id' => $this->userInfo['MCompany']['id']
          ],
          'order' => [
              'TCustomVariable.sort' => 'desc',
              'TCustomVariable.id' => 'desc'
          ],
          'limit' => 1,
          'recursive' => -1
      ];
      $lastData = $this->TCustomVariable->find('first', $params);
      if($lastData['TCustomVariable']['sort'] === '0'
          || $lastData['TCustomVariable']['sort'] === 0
          || $lastData['TCustomVariable']['sort'] === null){
            //ソート順が登録されていなかったらソート順をセットする
            if(! $this->remoteSetSort()){
              $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
              return false;
            }
            //もう一度ソートの最大値を取り直す
            $lastData = $this->TCustomVariable->find('first', $params);
      }
      $nextSort = 1;
      if (!empty($lastData)) {
        $nextSort = intval($lastData['TCustomVariable']['sort']) + 1;
      }
      $saveData['TCustomVariable']['sort'] = $nextSort;
      $saveData['TCustomVariable']['m_companies_id'] = $value['TCustomVariable']['m_companies_id'];
      $saveData['TCustomVariable']['variable_name'] = $value['TCustomVariable']['variable_name'].'コピー';
      $saveData['TCustomVariable']['attribute_value'] = $value['TCustomVariable']['attribute_value'];
      $saveData['TCustomVariable']['comment'] = $value['TCustomVariable']['comment'];
      $this->TCustomVariable->set($saveData);
      $this->TCustomVariable->begin();
      // バリデーションチェックでエラーが出た場合
      if($res){
        if (! $this->TCustomVariable->save() ) {
          $res = false;
          $errorMessage = $this->TCustomVariable->validationErrors;
          $this->TCustomVariable->rollback();
        }
        else{
          $this->TCustomVariable->commit();
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
      $this->TCustomVariable->begin();
      $list = $this->params->data['list'];
      $sortNoList = $this->params->data['sortNolist'];
      $this->log($list,LOG_DEBUG);
      /* 現在の並び順を取得 */
      $params = $this->paginate['TCustomVariable'];
      $params['fields'] = [
          'TCustomVariable.id',
          'TCustomVariable.sort'
      ];
      $params['conditions']['TCustomVariable.m_companies_id'] = $this ->userInfo['MCompany']['id'];
      unset($params['limit']);
      $prevSort = $this->TCustomVariable->find('list', $params);
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
        }
        $prevSort = $this->TCustomVariable->find('list', $params);
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
        if ( isset($prevSort[$id]) ) {
          $saveData = [
              'TCustomVariable' => [
                  'id' => $id,
                  'sort' => $prevSortKeys[$i]
              ]
          ];
          if (!$this->TCustomVariable->validates()) {
            $ret = false;
            break;
          }
          if (!$this->TCustomVariable->save($saveData)) {
            $ret = false;
            break;
          }
        } else {
          // 送信されたカスタム変数と現在DBに存在するカスタム変数に差がある場合
          $this->TCustomVariable->rollback();
          $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.configChanged'));
          return;
        }
      }
      if ($ret) {
        $this->TCustomVariable->commit();
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
      }
      else {
        $this->TCustomVariable->rollback();
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
      }
    }


  /**
   * カスタム変数ソート順を現在のID順でセット
   *
   * */
  public function remoteSetSort(){
    $this->TCustomVariable->begin();
    /* 現在の並び順を取得 */
    $this->paginate['TCustomVariable']['conditions']['TCustomVariable.m_companies_id'] = $this->userInfo['MCompany']['Id'];
    $params = [
      'fields' => [
        'TCustomVariable.sort'
      ],
      'conditions' => [
         'TCustomVariable.m_companies_id' => $this->userInfo['MCompany']['id']
      ],
      'order' => [
        'TCustomVariable.sort' => 'asc',
        'TCustomVariable.id' => 'asc'
      ],
      'limit' => 1,
      'recursive' => -1
    ];
    $params['fields'] = [
      'TCustomVariable.id',
      'TCustomVariable.sort'
    ];
    unset($params['limit']);
    $prevSort = $this->TCustomVariable->find('list', $params);
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
          'TCustomVariable' => [
              'id' => $id,
              'sort' => $prevSortKeys[$i]
          ]
      ];
      if (!$this->TCustomVariable->validates()) {
        $ret = false;
        break;
      }
      if (!$this->TCustomVariable->save($saveData)) {
        $ret = false;
        break;
      }
      $i++;
    }
    if ($ret) {
      $this->TCustomVariable->commit();
      return true;
    }
    else {
      $this->TCustomVariable->rollback();
      return false;
    }
  }

  private function _setParams(){
    $params = [
      'order' => [
        'TCustomVariable.sort' => 'asc',
        'TCustomVariable.id' => 'asc'
      ],
      'fields' => [
        'TCustomVariable.*'
      ],
      'conditions' => [
        'TCustomVariable.m_companies_id' => $this->userInfo['MCompany']['id']
      ],
      'recursive' => -1
    ];
    return $params;
  }
}
