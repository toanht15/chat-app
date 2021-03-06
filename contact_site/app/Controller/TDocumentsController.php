<?php
/**
 *TDocumentsController  controller.
 * 資料設定
 */

class TDocumentsController extends AppController {
  public $uses = ['TDocument','MDocumentTag'];
  public $components = ['Amazon'];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', '資料設定');
  }

  /* *
   * 一覧画面
   * @return void
   * */
  public function index() {
    if($this->userInfo['permission_level'] == 1 || $this->userInfo['permission_level'] == 99) {
      $documentList = $this->TDocument->find('all', $this->_setParams());
      $labelList = $this->MDocumentTag->find('list', ['fields'=> ['id','name']]);
      $showDocumentList = [];
      foreach ($documentList as $key => $document){
        $tags = [];
        foreach((array)json_decode($document['TDocument']['tag'],true) as $id){
          if ( !empty($labelList[$id]) ) {
            $tags[] = $labelList[$id];
          }
        }
        $document['TDocument']['tag'] = $tags;
        $showDocumentList[$key] = $document;
      }
      $this->set('documentList', $showDocumentList);
    }
    else {
      $this->redirect($this->referer());
    }
  }

  /* *
   * 登録画面
   * @return void
   * */
  public function add() {
    $this->_radioConfiguration();
    if($this->userInfo['permission_level'] == 1 || $this->userInfo['permission_level'] == 99) {
      if($this->request->is('post')) {
        $this->request->data['TDocument']['settings'] = htmlspecialchars_decode( $this->request->data['TDocument']['settings']);
        $this->request->data['TDocument']['rotation'] = htmlspecialchars_decode( $this->request->data['TDocument']['rotation']);
        $this->request->data['TDocument']['manuscript'] = htmlspecialchars_decode( $this->request->data['TDocument']['manuscript']);
        $this->_entry($this->request->data);
        $errors = $this->TDocument->validationErrors;
        $this->set('errors', $errors);
      }
      /*　タグリスト　*/
      $status = ['fields'=> ['id','name']];
      $tagList = $this->MDocumentTag->find('list',$status);
      $this->set('tagList',$tagList);
    }
    else {
      $this->redirect($this->referer());
    }
  }

  /* *
   * 更新画面
   * @return void
   * */
  public function edit($id) {
    $this->_radioConfiguration();
    if($this->userInfo['permission_level'] == 1 || $this->userInfo['permission_level'] == 99) {
      if($this->request->is('post') || $this->request->is('put')) {
        $this->request->data['TDocument']['settings'] = htmlspecialchars_decode( $this->request->data['TDocument']['settings']);
        $this->request->data['TDocument']['rotation'] = htmlspecialchars_decode( $this->request->data['TDocument']['rotation']);
        $this->request->data['TDocument']['manuscript'] = htmlspecialchars_decode( $this->request->data['TDocument']['manuscript']);
        $this->_entry($this->request->data);
        $errors = $this->TDocument->validationErrors;
        $this->set('errors', $errors);
        /*　タグリスト　*/
        $status = ['fields'=> ['id','name']];
        $tagList = $this->MDocumentTag->find('list',$status);
        $this->set('tagList',$tagList);
      }
      else {
        /* 更新画面　タグリスト表示 */
        $this->TDocument->id = $id;
        $tags = json_decode($this->TDocument->read(null,$id)['TDocument'] ['tag'],true);
        $this->request->data = $this->TDocument->read(null,$id);
        if($this->request->data['TDocument']['m_companies_id'] == $this->userInfo['MCompany']['id']) {
          $tagList = $this->MDocumentTag->find('list', ['fields'=> ['id','name']]);
          $documentList = [];
          $tags = [];
          foreach((array)json_decode($this->request->data['TDocument']['tag'],true) as $id){
            if ( !empty($tagList[$id]) ) {
              $tags[] = $id;
            }
          }
          $this->request->data['TDocument']['tag'] = $tags;
          $status = ['fields'=> ['id','name']];
          $tagList = $this->MDocumentTag->find('list',$status);
          $selectedTagList = $tags;
          $this->set('selectedTagList', $selectedTagList);
          $this->set('tagList',$tagList);
        }
        else {
          $this->redirect($this->referer());
        }
      }
    }
    else {
      $this->redirect($this->referer());
    }
  }

  /* *
   * タグ登録
   * @return void
   * */
  public function addTag() {
    $this->autoRender = false;
    if($this->request->is('post')) {
      $this->request->data['MDocumentTag']['m_companies_id'] = $this->userInfo['MCompany']['id'];
      if($this->MDocumentTag->save($this->request->data,false)) {
        $this->redirect($this->referer());
      }
    }
  }

  /* *
   * 削除機能
   * @return void
   * */
  public function remoteDelete(){
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';

    $id = $this->request->data['id'];
    $ret = $this->TDocument->find('first', [
      'fields' => 'TDocument.*',
      'conditions' => [
        'TDocument.del_flg' => 0,
        'TDocument.id' => $id,
        'TDocument.m_companies_id' => $this->userInfo['MCompany']['id']
      ],
      'recursive' => -1
    ]);

    if ( !empty($ret) ) {
      if ( $this->TDocument->delete($id) ) {
        // ファイルを削除
        $this->_removeDocuments($ret['TDocument']['file_name'], $ret['TDocument']['settings']);
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
      }
      else {
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
      }
    }
  }

  /* *
   * 一覧からの選択して削除
   * */
  public function remoteDeleteDocuments(){
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $this->TDocument->recursive = -1;
    $selectedList = $this->request->data['selectedList'];
    $this->TDocument->begin();
    $res = true;
    foreach($selectedList as $key => $val){
      $id = $val;
      $ret = $this->TDocument->find('first', [
          'fields' => 'TDocument.*',
          'conditions' => [
              'TDocument.del_flg' => 0,
              'TDocument.id' => $id,
              'TDocument.m_companies_id' => $this->userInfo['MCompany']['id']
          ],
          'recursive' => -1
      ]);
      if ( !empty($ret) ) {
        if (! $this->TDocument->delete($val) ) {
          $res = false;
        }
      }
    }
    if($res){
      $this->TDocument->commit();
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
    }
    else{
      $this->TDocument->rollback();
      $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
    }
  }

  /**
   * 資料設定ソート順更新
   *
   * */
  public function remoteSaveSort(){
    Configure::write('debug', 2);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    if ( !$this->request->is('ajax') ) return false;
    if ( !empty($this->params->data['list']) ) {
      $this->TDocument->begin();
      $list = $this->params->data['list'];
      $this->log($list,LOG_DEBUG);
      /* 現在の並び順を取得 */
      $params = $this->_setParams();
      $params['fields'] = [
          'TDocument.id',
          'TDocument.sort'
      ];
      unset($params['limit']);
      $prevSort = $this->TDocument->find('list', $params);
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
        $i = 1;
        foreach($prevSort as $key => $val){
          $prevSort[$key] = strval($i);
          $i++;
        }
      }
      $prevSortKeys = am($prevSort);
      $this->log($prevSortKeys,LOG_DEBUG);
      /* アップデート分の並び順を設定 */
      $ret = true;
      for ($i = 0; count($list) > $i; $i++) {
        $id = $list[$i];
        if ( isset($prevSort[$id]) ) {
          $saveData = [
              'TDocument' => [
                  'id' => $id,
                  'sort' => $prevSortKeys[$i]
              ]
          ];
          if (!$this->TDocument->validates()) {
            $ret = false;
            break;
          }
          if (!$this->TDocument->save($saveData)) {
            $ret = false;
            break;
          }
        } else {
          // 送信された資料設定と現在DBに存在する資料設定に差がある場合
          $this->TDocument->rollback();
          $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.configChanged'));
          return;
        }
      }
      if ($ret) {
        $this->TDocument->commit();
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
      }
      else {
        $this->TDocument->rollback();
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
      }
    }
  }

  /**
   * 資料設定ソート順を現在のID順でセット
   *
   * */
  public function remoteSetSort(){
    $this->TDocument->begin();
    /* 現在の並び順を取得 */
    $params = $this->_setParams();
    $params['fields'] = [
        'TDocument.id',
        'TDocument.sort'
    ];
    unset($params['limit']);
    $prevSort = $this->TDocument->find('list', $params);
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
          'TDocument' => [
              'id' => $id,
              'sort' => $prevSortKeys[$i]
          ]
      ];
      if (!$this->TDocument->validates()) {
        $ret = false;
        break;
      }
      if (!$this->TDocument->save($saveData)) {
        $ret = false;
        break;
      }
      $i++;
    }
    if ($ret) {
      $this->TDocument->commit();
      return true;
    }
    else {
      $this->TDocument->rollback();
      return false;
    }
  }

  /**
   * 保存機能
   * @param array $inputData
   * @return void
   * */
  private function _entry($saveData) {
    $nowData = [];
    if(!empty($saveData['TDocument']['tag'])) {
      $inputData = $saveData['TDocument']['tag'];
      $saveData['TDocument']['tag'] = $this->jsonEncode($inputData);
    }

    $saveData['TDocument']['m_companies_id'] = $this->userInfo['MCompany']['id'];
    $this->TDocument->begin();
    if ( empty($saveData['TDocument']['id']) ) {
      //新規追加
      $this->TDocument->create();
      if ( !isset($saveData['TDocument']['files']) ) {
        $saveData['TDocument']['files'] = [];
      }
      $params = [
          'fields' => [
              'TDocument.sort'
          ],
          'conditions' => [
              'TDocument.m_companies_id' => $this->userInfo['MCompany']['id']
          ],
          'order' => [
              'TDocument.sort' => 'desc',
              'TDocument.id' => 'desc'
          ],
          'limit' => 1,
          'recursive' => -1
      ];
      $lastData = $this->TDocument->find('first', $params);
      if($lastData){
        if($lastData['TDocument']['sort'] === '0'
            || $lastData['TDocument']['sort'] === 0
            || $lastData['TDocument']['sort'] === null){
          //ソート順が登録されていなかったらソート順をセットする
          if(! $this->remoteSetSort()){
            $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
            return false;
          }
          //もう一度ソートの最大値を取り直す
          $lastData = $this->TDocument->find('first', $params);
        }
      }
      $nextSort = 1;
      if (!empty($lastData)) {
        $nextSort = intval($lastData['TDocument']['sort']) + 1;
      }
      $saveData['TDocument']['sort'] = $nextSort;
    }
    else {
      // ファイルがアップロードされなかった場合はunset
      if ( isset($saveData['TDocument']['files']) && strcmp($saveData['TDocument']['files']['error'], UPLOAD_ERR_NO_FILE) === 0 ) {
        unset($saveData['TDocument']['files']);
      }
    }

    $this->TDocument->set($saveData);
    // バリデーションチェックに失敗したら
    if ( !$this->TDocument->validates() ) return false;

    $fileSettings = (!empty($saveData['TDocument']['settings'])) ? (array)json_decode($saveData['TDocument']['settings']) : [];
    if ( isset($saveData['TDocument']['rotation']) ) {
      $fileSettings['rotation'] = $saveData['TDocument']['rotation'];
    }

    // ファイルが添付されたら
    $fileName = "";
    if ( !empty($saveData['TDocument']['files']) ) {
      $files = $saveData['TDocument']['files'];
      $fileName = $this->userInfo['MCompany']['company_key']."-".date("YmdHis").".".pathinfo($files['name'], PATHINFO_EXTENSION);
      $dirPath = WWW_ROOT.'files'.DS.pathinfo($fileName, PATHINFO_FILENAME).DS;

      // 専用ディレクトリを用意、添付されたファイルを共有ディレクトリに移動
      system('mkdir -p '.$dirPath.'; cd $_; pdf2svg '.$files['tmp_name'].' %d.svg all');
      $ret = "";
      // ディレクトリの存在を確認し、ハンドルを取得
      if( is_dir( $dirPath ) && $handle = opendir( $dirPath ) ) {
        // スライドファイル（SVG）をアップロード
        if ( file_exists($dirPath."1.svg") ) {
          $ret = $this->_uploadSlide($dirPath, $fileName); // 戻り値が「svg」だと成功
        }

        // サムネイルをアップロード
        if ( strcmp($ret, "svg") === 0 ) {
          $ret = $this->_createThumnail($dirPath); // 戻り値が「jpg」だと成功
        }

        $fileSettings['pages'] = count(glob($dirPath."*.svg"));
        // 専用ディレクトリを削除する
        system("rm -Rf ".WWW_ROOT.'files'.DS.pathinfo($fileName, PATHINFO_FILENAME));
      }
      // SVGファイルが保存できたら、PDFとサムネイルを保存する
      if ( strcmp($ret, "jpg") === 0 && file_exists($files['tmp_name']) ) {
        $retPath = $this->Amazon->putObject("medialink".DS.basename($fileName), $files['tmp_name']);
        $ret = ( $retPath ) ? pathinfo($retPath, PATHINFO_EXTENSION) : "";
      }

      // PDFファイルが保存できなかったら
      if ( strcmp($ret, "pdf") !== 0 ) {
        $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.fileSaveFailed')]);
        return false;
      }

      // 今保存されているファイルを削除する
      if ( !empty($saveData['TDocument']['id']) ) { // ファイル添付＆更新の場合
        $nowData = $this->TDocument->read(null, $saveData['TDocument']['id']);
      }
      unset($saveData['TDocument']['files']);

      // 最新のファイル情報をセットする
      $saveData['TDocument']['file_name'] = $fileName;
    }
    $saveData['TDocument']['settings'] = $this->jsonEncode($fileSettings);

    if($this->TDocument->save($saveData, false)) {
      // 昔のファイルを削除する
      if ( !empty($nowData['TDocument']) ) {
        // ファイルを削除
        $this->_removeDocuments($nowData['TDocument']['file_name'], $nowData['TDocument']['settings']);
      }
      $this->TDocument->commit();
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
      $this->redirect(['controller' => 'TDocuments', 'action' => 'index']);
    }
    else {
      $this->TDocument->rollback();
      if ( !empty($fileName) ) {
        // ファイルを削除
        $this->_removeDocuments($fileName, $fileSettings);
      }
      $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
    }
  }

  /**
   *  特定の資料を削除する
   *  @param string $fileName 資料の名前
   *  @param string $settings 資料の情報
   *  @return void
   * */
  public function _removeDocuments($fileName, $settings){
    $this->Amazon->removeObject("medialink/".$fileName);
    $this->Amazon->removeObject("medialink/".C_PREFIX_DOCUMENT.pathinfo($fileName, PATHINFO_FILENAME).".jpg");
    $settings = ( !empty($settings) ) ? json_decode($settings) : [];
    if ( !empty($settings->pages) ) {
      for ($i=1; $i <= $settings->pages; $i++) {
        $this->Amazon->removeObject("medialink/svg_".pathinfo($fileName, PATHINFO_FILENAME)."_".$i.".svg");
      }
    }
  }

  /**
   *  資料のスライドをアップロードする
   *  @param string $dirPath 資料の保存先パス
   *  @param string $fileName 資料の保存名
   *  @return void
   * */
  private function _uploadSlide($dirPath, $fileName){
    $ret = "";
    foreach( glob($dirPath."*.svg") as $path ) {
      $retPath = $this->Amazon->putObject("medialink".DS."svg_".pathinfo($fileName, PATHINFO_FILENAME)."_".basename($path), $path);
      $ret = ( $retPath ) ? pathinfo($retPath, PATHINFO_EXTENSION) : ""; // 拡張子を取得
      if( strcmp($ret, "svg") !== 0 ) break; // 拡張子が得られなければ保存失敗とみなす
    }
    return $ret;
  }

  /**
   *  資料のサムネイルを作成、アップロードする
   *  @param string $fileName 資料の保存先パス
   *  @return void
   * */
  private function _createThumnail($fileName){
    $name = C_PREFIX_DOCUMENT.pathinfo($fileName, PATHINFO_FILENAME).".jpg"; //サムネイルのファイル名
    system('convert '.$fileName.'1.svg ' . $fileName.$name);
    $retPath = $this->Amazon->putObject("medialink/".$name, $fileName.$name);
    return ( $retPath ) ? pathinfo($retPath, PATHINFO_EXTENSION) : ""; // 拡張子を取得
  }

   /**
   * radioボタン設定
   * @return void
   * */
  public function _radioConfiguration() {
    $download = array(
      '1' => '可',
      '2' => '不可',
    );

    $pagenation = array(
      '1' => 'する',
      '2' => 'しない'
    );

    $this->set('download', $download);
    $this->set('pagenation', $pagenation);
  }

  private function _setParams(){
    //TDocument
    $params = [
        'order' => [
            'TDocument.sort' => 'asc',
            'TDocument.id' => 'asc'
        ],
        'fields' => [
            'TDocument.*'
        ],
        'conditions' => [
            'TDocument.m_companies_id' => $this->userInfo['MCompany']['id']
        ],
        'recursive' => -1
    ];
    return $params;
  }

    /**
   * remoteOpenDocumentPreview
   * 共有する資料プレビューを表示
   * @return string html
   * */
  public function remoteOpenDocumentPreview(){
    $this->layout = "ajax";
    $ret = [];
    $DocumentPreview = $this->TDocument->find('first', [
      'conditions' => [
        'm_companies_id' => $this->userInfo['MCompany']['id'],
        'id' => $this->request->data['id']
      ],
      'recursive' => -1
    ]);
    $ret['documentPreview'] = json_encode([$DocumentPreview], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    return new CakeResponse(['body' => json_encode($ret)]);
  }
}
