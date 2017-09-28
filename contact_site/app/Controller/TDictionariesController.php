<?php
/**
 * TDictionariesController controller.
 * ユーザーマスタ
 */
class TDictionariesController extends AppController {
  public $uses = ['TDictionary','TDictionaryCategory'];
  public $paginate = [
    'TDictionary' => [
      'limit' => 10,
      'order' => [
        'TDictionary.sort' => 'asc',
        'TDictionary.id' => 'asc'
      ],
      'fields' => [
        'TDictionary.*'
      ],
      'conditions' => [
        'OR' => [
          'TDictionary.type' => C_DICTIONARY_TYPE_COMP,
          [
          'TDictionary.type' => C_DICTIONARY_TYPE_PERSON
          ]
        ]
      ],
      'recursive' => -1
    ]
  ];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', '定型文管理');
  }

  /* *
   * 一覧画面
   * @return void
   * */
  public function index() {
    //プラン別対応
    if($this->coreSettings['dictionaryCategory']){
      $stint_flg = '1';
    }
    else{
      $stint_flg = '0';
    }
    //#451 定型文カテゴリ対応 start
    $dictionaryList = $this->TDictionary->find('all', $this->_setParams());
    $categoryList = $this->TDictionaryCategory->find('all', $this->_setCategoryParams());
    $ret = array_search('0', $categoryList);
    $this->set('categoryList', $categoryList);
    $tab_array = array();
    $tab_name= array();
    //タブ名配列の生成
    if(!empty($categoryList)){
      foreach($categoryList as $key => $val){
        $tab_name[] = array(
            'id' => $val['TDictionaryCategory']['id'],
            'name' => $val['TDictionaryCategory']['category_name']
        );
      }
    }
    else{
      //カテゴリが一つもなかった場合エラー
    }
    //定型文配列のタブごとの連想配列の生成
    if(!empty($dictionaryList)){
      foreach($tab_name as $key => $val){
        //定型文配列のタブごとの連想配列の生成
        foreach($dictionaryList as $d_key => $d_val){
          if($val['id'] == $d_val['TDictionary']['m_category_id']){
            $tab_array[$key][] = $d_val;
          }
        }
        if(empty($tab_array[$key])){
          $tab_array[$key] = array();
        }
      }
    }
    $this->set('nameList', $tab_name);
    $this->set('dictionaryList', $tab_array);
    $this->set('stint_flg', $stint_flg);
    if(isset($this->request->params['named']['tabindex'])){
      $tabindex = $this->request->params['named']['tabindex'];
      $this->set('tabindex', $tabindex);
    }
    else{
      if(isset($this->request->data['index'])){
        $tabindex = $this->request->data['index'];
        $this->set('tabindex', $tabindex);
      }
      else{
        $this->set('tabindex', 0);
      }
    }
    //#451 定型文カテゴリ対応 end
    $this->_viewElement();
  }

  /* *
   * カテゴリ登録
   * @return void
   * */
  public function remoteSaveCategoryEntryForm(){
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $saveData = [];
    $errorMessage = [];
    //追加（新規）
    $this->TDictionaryCategory->create();
    $saveData['TDictionaryCategory']['m_companies_id'] = $this->userInfo['MCompany']['id'];
    $saveData['TDictionaryCategory']['category_name'] = $this->request->data['category_name'];
    $params = [
        'fields' => [
            'TDictionaryCategory.sort'
        ],
        'conditions' => [
            'TDictionaryCategory.m_companies_id' => $this->userInfo['MCompany']['id']
        ],
        'order' => [
            'TDictionaryCategory.sort' => 'desc',
            'TDictionaryCategory.id' => 'desc'
        ],
        'limit' => 1,
        'recursive' => -1
    ];
    $lastData = $this->TDictionaryCategory->find('first', $params);
    $nextSort = 1;
    if (!empty($lastData)) {
      $nextSort = intval($lastData['TDictionaryCategory']['sort']) + 1;
    }
    $saveData['TDictionaryCategory']['sort'] = $nextSort;
    // const
    $this->TDictionaryCategory->set($saveData);
    $this->TDictionaryCategory->begin();
    // バリデーションチェックでエラーが出た場合
    if ( $this->TDictionaryCategory->save() ) {
      $this->TDictionaryCategory->commit();
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
    }
    else {
      $this->TDictionaryCategory->rollback();
    }
    $errorMessage = $this->TDictionaryCategory->validationErrors;
    return new CakeResponse(['body' => json_encode($errorMessage)]);
  }

  /* *
   * 登録画面
   * @return void
   * */
  public function remoteOpenEntryForm() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $this->_viewElement();
    $this->set('tabid', $this->request->data['tabid']);
    $this->set('tabindex', $this->request->data['tabindex']);
    // const
    if ( strcmp($this->request->data['type'], 2) === 0 ) {
      $this->TDictionary->recursive = -1;
      $this->request->data = $this->TDictionary->read(null, $this->request->data['id']);
    }
    //二重操作防止
    $dstoken = $this->rand(); //ランダムな文字列を生成する関数用意
    $this->Session->write('dstoken', $dstoken);
    $this->set('dstoken', $dstoken);
    $this->render('/Elements/TDictionaries/remoteEntry');
  }

  function rand($length = 8){
    $char = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_";
    mt_srand();
    $ret = "";
    for($i = 0; $i < $length; $i++){
      $ret .= $char{mt_rand(0, strlen($char) - 1)};
    }
    return $ret;
  }

  /* *
   * 定型文コピー/移動・カテゴリ更新/削除ダイアログ表示
   * @return void
   * */
  public function openEntryEdit(){
    //二重操作防止
    $dstoken = $this->rand(); //ランダムな文字列を生成する関数用意
    $this->Session->write('dstoken', $dstoken);
    $this->set('dstoken', $dstoken);
    //プラン別対応
    if($this->coreSettings['dictionaryCategory']){
      $stint_flg = '1';
    }
    else{
      $stint_flg = '0';
    }
    $this->set('stint_flg', $stint_flg);
    $data = $this->request->data;
    //コピーか移動かの切り分け
    if(($data['type'] == '3')||($data['type'] == '4')){
      $this->remoteCopyForm();
    }
    else{
      if($data['type'] == '1'){
        //カテゴリの更新
        $this->remoteCategoryEditForm();
      }
      else{
        //カテゴリの削除
        $this->remoteCategoryDeleteForm();
      }
    }
  }

  /* *
   * コピー/移動画面
   * @return void
   * */
  public function remoteCopyForm() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    //カテゴリリスト取得
    $categoryList = $this->TDictionaryCategory->find('all', $this->_setCategoryParams());
    $data = $this->request->data;
    if(!empty($categoryList)){
      $tab_name= array();
      foreach($categoryList as $key => $val){
        $tab_name[] = array(
            'id' => $val['TDictionaryCategory']['id'],
            'name' => $val['TDictionaryCategory']['category_name']
        );
      }
    }
    //コピーか移動かの切り分け
    if($data['type'] == '3'){
      //コピー
      //現在選択されているカテゴリを「このカテゴリという名称にする」
      foreach($tab_name as $key => $val){
        if($data['select_tab_index'] == $key){
          $val_key = $key;
          $tab_name[$key]['name'] = 'このカテゴリ';
        }
      }
      if($val_key != 0){
        $zero_array = $tab_name[0];
        $key_array = $tab_name[$val_key];
        $tab_name[0] = $key_array;
        $tab_name[$val_key] = $zero_array;
      }
      $this->set('val_key', $val_key);
    }
    else{
      //移動
      //現在選択されているカテゴリをリストから削除
      foreach($tab_name as $key => $val){
        if($data['select_tab_index'] == $key){
          $del_key = $key;
        }
      }
      unset($tab_name[$del_key]);
      //キーの振り直し
      $tab_name = array_values($tab_name);
    }
    $names = array();
    foreach($tab_name as $value){
      $names[$value['id']] = $value['name'];
    }
    $this->set('selectTabIndex', $data['select_tab_index']);
    $this->set('type', $data['type']);
    $this->set('id', $tab_name[0]['id']);
    $this->set('names', $names);
    $data=json_encode($data);
    $this->set('data', $data);
    //ポップアップの呼び出し
    $this->render('/Elements/TDictionaries/remoteCopy');
  }

  private function getCategoryEntityFromId($id) {
      //カテゴリーIDチェック
      $param = [
        'fields' => [
          'TDictionaryCategory.id'
        ],
        'conditions' => [
          'TDictionaryCategory.id' => $id
        ]
      ];

      return $this->TDictionaryCategory->find('first',$param);
  }

  private function getCategoryId($id) {
      //カテゴリーIDチェック
      $param = [
        'fields' => [
          'TDictionary.m_category_id'
        ],
        'conditions' => [
          'TDictionary.id' => $id
        ]
      ];

      return $this->TDictionary->find('first',$param);
  }

  private function getDictionaryEntityId($id) {
      //カテゴリーIDチェック
      $param = [
        'fields' => [
          'TDictionary.id'
        ],
        'conditions' => [
          'TDictionary.id' => $id
        ]
      ];

      return $this->TDictionary->find('first',$param);
  }

  /* *
   * コピー処理
   * @return void
   * */
  public function remoteCopyEntryForm() {
    if($this->Session->read('dstoken') == $this->request->data['dstoken']){
      Configure::write('debug', 0);
      $this->autoRender = FALSE;
      $this->layout = 'ajax';
      $data = $this->request->data;
      //カテゴリーIDチェック
      $selectedCategory = $this->getCategoryEntityFromId($data['selectedCategory']);

      if(!empty($selectedCategory)) {
        //コピー元の定型文リスト取得
        foreach($data['selectedList'] as $value){
          $copyData[] = $this->TDictionary->read(null, $value);
        }
        $res = true;
        foreach($copyData as $value){
          $this->TDictionary->create();
          $saveData['TDictionary']['m_companies_id'] = $this->userInfo['MCompany']['id'];
          $saveData['TDictionary']['m_category_id'] = $selectedCategory['TDictionaryCategory']['id'];
          $saveData['TDictionary']['word'] = $value['TDictionary']['word'];
          $saveData['TDictionary']['type'] = $value['TDictionary']['type'];
          $params = [
              'fields' => [
                  'TDictionary.sort'
              ],
              'conditions' => [
                  'TDictionary.m_companies_id' => $this->userInfo['MCompany']['id']
              ],
              'order' => [
                  'TDictionary.sort' => 'desc',
                  'TDictionary.id' => 'desc'
              ],
              'limit' => 1,
              'recursive' => -1
          ];
          $lastData = $this->TDictionary->find('first', $params);
          $nextSort = 1;
          if (!empty($lastData)) {
            $nextSort = intval($lastData['TDictionary']['sort']) + 1;
          }
          $saveData['TDictionary']['sort'] = $nextSort;
          $saveData['TDictionary']['m_users_id'] = $this->userInfo['id'];
          $this->TDictionary->set($saveData);
          $this->TDictionary->begin();
          // バリデーションチェックでエラーが出た場合
          if($res){
            if (! $this->TDictionary->save() ) {
              $res = false;
              $errorMessage = $this->TDictionary->validationErrors;
              $this->TDictionary->rollback();
            }
            else{
              $this->TDictionary->commit();
              $this->Session->delete('dstoken');
              $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
              $this->set('tabindex', $data['selectedCategory']);
              return $this->getCategoryIndexFromId($saveData['TDictionary']['m_category_id']);
              //$data['selectedCategory'];
            }
          }
        }
      } else {
        // すでに存在しないカテゴリへのコピーのため変更済みとしてエラーを返す
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.configChanged'));
        $this->response->statusCode(409); //CONFLICT
        return;
      }
    }
  }


  /* *
   * 移動処理
   * @return void
   * */
  public function remoteMoveEntryForm() {
    if($this->Session->read('dstoken') == $this->request->data['dstoken']){
      Configure::write('debug', 0);
      $this->autoRender = FALSE;
      $this->layout = 'ajax';
      $data = $this->request->data;
      //カテゴリーIDチェック
      $selectedCategory = $this->getCategoryEntityFromId($data['selectedCategory']);

      if(!empty($selectedCategory)) {
        //移動元の定型文リスト取得
        $res = true;
        foreach($data['selectedList'] as $value){
          $moveData = $this->TDictionary->read(null, $value);
          $this->TDictionary->recursive = -1;
          $moveData['TDictionary']['m_category_id'] = $selectedCategory['TDictionaryCategory']['id'];
          $moveData['TDictionary']['m_users_id'] = $this->userInfo['id'];
          $this->TDictionary->set($moveData);
          $this->TDictionary->begin();
          // バリデーションチェックでエラーが出た場合
          if($res){
            if (! $this->TDictionary->save() ) {
              $res = false;
              $errorMessage = $this->TDictionary->validationErrors;
              $this->TDictionary->rollback();
            }
            else{
              $this->TDictionary->commit();
              $this->Session->delete('dstoken');
              $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
              return $this->getCategoryIndexFromId($moveData['TDictionary']['m_category_id']);
            }
          }
        }
      }
      else {
        // すでに存在しないカテゴリへのコピーのため変更済みとしてエラーを返す
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.configChanged'));
        $this->response->statusCode(409); //CONFLICT
        return;
      }
    }
  }


  /* *
   * 保存処理
   * @return void
   * */
  public function remoteSaveEntryForm() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $saveData = [];
    $errorMessage = [];
    if($this->Session->read('dstoken') == $this->request->data['dstoken']){
      //カテゴリーIDチェック
      $selectedCategory = $this->getCategoryEntityFromId($this->request->data['tab']);
      if(!empty($selectedCategory)) {
        // if ( !$this->request->is('ajax') ) return false;
        if (!empty($this->request->data['dictionaryId'])) {
          $this->TDictionary->recursive = -1;
          $saveData = $this->TDictionary->read(null, $this->request->data['dictionaryId']);
        }
        else {
          $this->TDictionary->create();
        }
        $saveData['TDictionary']['m_companies_id'] = $this->userInfo['MCompany']['id'];
        $saveData['TDictionary']['m_category_id'] = $selectedCategory['TDictionaryCategory']['id'];
        $saveData['TDictionary']['word'] = $this->request->data['word'];
        if (empty($this->request->data['dictionaryId'])) {
          $params = [
              'fields' => [
                  'TDictionary.sort'
              ],
              'conditions' => [
                  'TDictionary.m_companies_id' => $this->userInfo['MCompany']['id']
              ],
              'order' => [
                  'TDictionary.sort' => 'desc',
                  'TDictionary.id' => 'desc'
              ],
              'limit' => 1,
              'recursive' => -1
          ];
          $lastData = $this->TDictionary->find('first', $params);
          $nextSort = 1;
          if (!empty($lastData)) {
            $nextSort = intval($lastData['TDictionary']['sort']) + 1;
          }
          $saveData['TDictionary']['sort'] = $nextSort;
        }
        $saveData['TDictionary']['type'] = $this->request->data['type'];
        if ( strcmp($saveData['TDictionary']['type'], C_AUTHORITY_NORMAL) === 0 ) {
          $saveData['TDictionary']['m_users_id'] = $this->userInfo['id'];
        }
        // const
        $this->TDictionary->set($saveData);
        $this->TDictionary->begin();
        // バリデーションチェックでエラーが出た場合
        if ( $this->TDictionary->save() ) {
          $this->TDictionary->commit();
          $this->Session->delete('dstoken');
          $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
          return json_encode(array(
            "success" => true,
            "showIndex" => $this->getCategoryIndexFromId($saveData['TDictionary']['m_category_id'])
          ));
        }
        else {
          $this->TDictionary->rollback();
        }
        $errorMessage = $this->TDictionary->validationErrors;
        return new CakeResponse(['body' => json_encode($errorMessage)]);
      }
      else {
        // すでに存在しないカテゴリへの登録・更新のため変更済みとしてエラーを返す
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.configChanged'));
        $this->response->statusCode(409); //CONFLICT
        return;
      }
    }
  }

  /**
   * カテゴリ更新フォーム
   *
   * */
  public function remoteCategoryEditForm(){
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $data = $this->request->data;
    //カテゴリ名の取得
    $category_data = $this->TDictionaryCategory->read(null, $data['id']);
    $this->set('name', $category_data['TDictionaryCategory']['category_name']);
    $this->set('type', $data['type']);
    $data=json_encode($data);
    $this->set('data', $data);
    //ポップアップの呼び出し
    $this->render('/Elements/TDictionaries/remoteCopy');
  }

  /**
   * カテゴリ更新処理
   *
   * */
  public function remoteCategoryEdit(){
    if($this->Session->read('dstoken') == $this->request->data['dstoken']){
      Configure::write('debug', 0);
      $this->autoRender = FALSE;
      $this->layout = 'ajax';
      $data = $this->request->data;

      //カテゴリーIDチェック
      $selectedCategory = $this->getCategoryEntityFromId($data['id']);
      if(!empty($selectedCategory)) {
        $saveData = [];
        $errorMessage = [];
        //更新
        $this->TDictionaryCategory->recursive = -1;
        $saveData = $this->TDictionaryCategory->read(null, $data['id']);
        $saveData['TDictionaryCategory']['m_companies_id'] = $this->userInfo['MCompany']['id'];
        $saveData['TDictionaryCategory']['category_name'] = $data['name'];
        // const
        $this->TDictionaryCategory->set($saveData);
        $this->TDictionaryCategory->begin();
        // バリデーションチェックでエラーが出た場合
        if ( $this->TDictionaryCategory->save() ) {
          $this->TDictionaryCategory->commit();
          $this->Session->delete('dstoken');
          $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
          return $this->getCategoryIndexFromId($data['id']);
        }
        else {
          $this->TDictionaryCategory->rollback();
        }
        $errorMessage = $this->TDictionaryCategory->validationErrors;
        return new CakeResponse(['body' => json_encode($errorMessage)]);
      }
      else {
        // すでに存在しないカテゴリへのコピーのため変更済みとしてエラーを返す
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.configChanged'));
        $this->response->statusCode(409); //CONFLICT
        return;
      }
    }
  }

  /**
   * カテゴリ削除フォーム
   *
   * */
  public function remoteCategoryDeleteForm(){
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $data = $this->request->data;
    $this->set('type', $data['type']);
    $data=json_encode($data);
    $this->set('data', $data);
    //ポップアップの呼び出し
    $this->render('/Elements/TDictionaries/remoteCopy');
  }

  /**
   * カテゴリ削除処理
   *
   * */
  public function remoteCategoryDelete(){
    if($this->Session->read('dstoken') == $this->request->data['dstoken']){
      Configure::write('debug', 0);
      $this->autoRender = FALSE;
      $this->layout = 'ajax';
      $this->TDictionaryCategory->recursive = -1;
      $data = $this->request->data;
      $this->TDictionaryCategory->begin();
      if($this->TDictionaryCategory->delete($data['id'])){
        $this->TDictionaryCategory->commit();
        $this->Session->delete('dstoken');
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
        //カテゴリが削除し終わったら定型文も削除する
        $params = [
            'fields' => [
                'TDictionary.id'
            ],
            'conditions' => [
                'TDictionary.m_category_id' => $data['id']
            ]
        ];
        $dictionaryList = $this->TDictionary->find('all', $params);
        $delete_list = array();
        foreach($dictionaryList as $value){
          $delete_list[] = $value['TDictionary']['id'];
        }
        //変数に削除するIDリストを代入する
        $this->request->data['selectedList'] = $delete_list;
        $this->request->data['isDeleteCategory'] = true;
        $this->remoteDeleteUser();
      }
      else{
        $this->TDictionaryCategory->rollback();
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
      }
      $errorMessage = $this->TDictionaryCategory->validationErrors;
      return new CakeResponse(['body' => json_encode($errorMessage)]);
    }
  }

  private function getCategoryEntities() {
   return $this->TDictionaryCategory->find('all', $this->_setCategoryParams());
 }

  private function getCategoryIndexFromId($id) {
   $entities = $this->getCategoryEntities();
   $index = 0;
   foreach($entities as $key => $entity) {
     if($entity['TDictionaryCategory']['id'] === $id ) {
       break;
     }
     $index++;
   }
   return $index;
 }

  /**
   * カテゴリソート順更新
   *
   * */
  public function remoteSaveTabSort(){
    Configure::write('debug', 2);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    //TDictionaryCategory
    if ( !$this->request->is('ajax') ) return false;

    if ( !empty($this->params->data['list']) ) {
      $this->TDictionaryCategory->begin();
      $list = $this->params->data['list'];
      $list = array_unique($list);
      $list = array_values($list);//キーを振りなおす
      $this->log($list,LOG_DEBUG);

      //カテゴリーIDチェック
      $selectedCategory = $this->getCategoryEntities();
      if(count($selectedCategory) == count($list)) {
        /* 現在の並び順を取得 */
        $params = $this->_setCategoryParams();
        $params['fields'] = [
            'TDictionaryCategory.id',
            'TDictionaryCategory.sort'
        ];
        unset($params['limit']);
        $prevSort = $this->TDictionaryCategory->find('list', $params);
        $array = array();
        $i = 1;
        foreach($prevSort as $key => $val){
          $array[$key] = $i;
          $i++;
        }
        $prevSort = $array;
        $prevSortKeys = am($prevSort);
        $this->log($prevSortKeys,LOG_DEBUG);
        /* アップデート分の並び順を設定 */
        $ret = true;
        $rescount = 0;
        for ($i = 0; count($list) > $i; $i++) {
          $id = $list[$i];
          if ( isset($prevSort[$id]) ) {
            $saveData = [
                'TDictionaryCategory' => [
                    'id' => $id,
                    'sort' => $prevSortKeys[$i]
                ]
            ];
            if (!$this->TDictionaryCategory->validates()) {
              $ret = false;
              break;
            }
            if (!$this->TDictionaryCategory->save($saveData)) {
              $ret = false;
              break;
            }
            $this->log($ret,LOG_DEBUG);
          } else {
            // 送信されたカテゴリ設定と現在DBに存在する定型文設定に差がある場合
            $this->TDictionaryCategory->rollback();
            $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.configChanged'));
            $this->response->statusCode(409); //CONFLICT
            return;
          }
        }
        if ($ret) {
          $this->TDictionaryCategory->commit();
          $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
          //return $this->getCategoryIndexFromId(96);
        }
        else {
          $this->TDictionaryCategory->rollback();
          $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
        }
      }
      else {
        // すでに存在しないカテゴリへのコピーのため変更済みとしてエラーを返す
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.configChanged'));
        $this->response->statusCode(409); //CONFLICT
        return;
      }
    }
  }

  /**
   * 定型文ソート順更新
   *
   * */
  public function remoteSaveSort(){
    Configure::write('debug', 2);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    if ( !$this->request->is('ajax') ) return false;
    if ( !empty($this->params->data['list']) ) {
      $this->TDictionary->begin();
      $list = $this->params->data['list'];
      $this->log($list,LOG_DEBUG);
      /* 現在の並び順を取得 */
      $params = $this->_setParams();
      $params['fields'] = [
        'TDictionary.id',
        'TDictionary.sort'
      ];
      unset($params['limit']);
      $prevSort = $this->TDictionary->find('list', $params);
      $prevSortKeys = am($prevSort);
      $this->log($prevSortKeys,LOG_DEBUG);
      /* アップデート分の並び順を設定 */
      $ret = true;
      for ($i = 0; count($list) > $i; $i++) {
        $id = $list[$i];
        if ( isset($prevSort[$id]) ) {
          $saveData = [
            'TDictionary' => [
              'id' => $id,
              'sort' => $prevSortKeys[$i]
            ]
          ];
          if (!$this->TDictionary->validates()) {
            $ret = false;
            break;
          }
          if (!$this->TDictionary->save($saveData)) {
            $ret = false;
            break;
          }
        } else {
          // 送信された定型文設定と現在DBに存在する定型文設定に差がある場合
          $this->TDictionary->rollback();
          $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.configChanged'));
          return;
        }
      }
      if ($ret) {
        $this->TDictionary->commit();
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
      }
      else {
        $this->TDictionary->rollback();
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
      }
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
    $this->TDictionary->recursive = -1;
    $selectedList = $this->request->data['selectedList'];
    $isDeleteCategory = $this->request->data['isDeleteCategory'];
    if(empty($isDeleteCategory)) {
      $selectedCategoryId = $this->getCategoryId($this->request->data['selectedList'][0]);
      //カテゴリーIDチェック
      $selectedCategory = $this->getCategoryEntityFromId($selectedCategoryId['TDictionary']['m_category_id']);
      if(!empty($selectedCategory)) {
        $this->TDictionary->begin();
        $res = true;
        foreach($selectedList as $key => $val){
          if (! $this->TDictionary->delete($val) ) {
            $res = false;
          }
        }
        if($res){
          $this->TDictionary->commit();
          $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
          return $this->getCategoryIndexFromId($selectedCategoryId['TDictionary']['m_category_id']);
        }
        else{
          $this->TDictionary->rollback();
          $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
        }
      }
      else {
        //すでに存在しないカテゴリへのコピーのため変更済みとしてエラーを返す
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.configChanged'));
        $this->response->statusCode(409); //CONFLICT
        return;
      }
    }
    else {
      $this->TDictionary->begin();
      $res = true;
      foreach($selectedList as $key => $val){
        if (! $this->TDictionary->delete($val) ) {
          $res = false;
        }
      }
      if($res){
        $this->TDictionary->commit();
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
      }
      else{
        $this->TDictionary->rollback();
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
      }
    }
  }

  private function _setParams(){
    $params = [
      'order' => [
        'TDictionary.sort' => 'asc',
        'TDictionary.id' => 'asc'
      ],
      'fields' => [
        'TDictionary.*'
      ],
      'conditions' => [
        'TDictionary.m_companies_id' => $this->userInfo['MCompany']['id'],
        'NOT' => [
          ['TDictionary.m_category_id' => 0],
          ['TDictionary.m_category_id' => NULL]
        ],
        'OR' => [
          'TDictionary.type' => C_DICTIONARY_TYPE_COMP,
          [
          'TDictionary.m_users_id' => $this->userInfo['id'],
          'TDictionary.type' => C_DICTIONARY_TYPE_PERSON
          ]
        ]
      ],
      'recursive' => -1
    ];
    return $params;
  }

  //#451 定型文カテゴリ対応 start
  private function _setCategoryParams(){
    $params = [
      'order' => [
        'TDictionaryCategory.sort' => 'asc',
        'TDictionaryCategory.id' => 'asc'
      ],
      'fields' => [
        'TDictionaryCategory.*'
      ],
      'conditions' => [
        'TDictionaryCategory.m_companies_id' => $this->userInfo['MCompany']['id']
      ],
      'recursive' => -1
    ];
    return $params;
  }
  //#451 定型文カテゴリ対応 end

  private function _viewElement(){
    $this->set('dictionaryTypeList', Configure::read("dictionaryType"));
  }

}
