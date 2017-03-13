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
    $documentList =  $this->TDocument->find('all', [
      'conditions' => [
        'TDocument.m_companies_id' => $this->userInfo['MCompany']['id']
      ]
    ]);
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

  /* *
   * 登録画面
   * @return void
   * */
  public function add() {
    $this->_radioConfiguration();

    if($this->request->is('post')) {
      $this->_entry($this->request->data);
      $errors = $this->TDocument->validationErrors;
      $this->set('errors', $errors);
    }
    /*　タグリスト　*/
    $status = ['fields'=> ['id','name']];
    $tagList = $this->MDocumentTag->find('list',$status);
    $this->set('tagList',$tagList);
  }

  /* *
   * 更新画面
   * @return void
   * */
  public function edit($id) {
    $this->_radioConfiguration();

    if($this->request->is('post') || $this->request->is('put')) {
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
      $this->TDocument->create();
      if ( !isset($saveData['TDocument']['files']) ) {
        $saveData['TDocument']['files'] = [];
      }
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

    // ファイルが添付されたら
    $fileName = "";
    $fileSettings = [];
    if ( !empty($saveData['TDocument']['files']) ) {
      $files = $saveData['TDocument']['files'];
      $fileName = $this->userInfo['MCompany']['company_key']."-".date("YmdHis").".".pathinfo($files['name'], PATHINFO_EXTENSION);
      $dirPath = WWW_ROOT.'files'.DS.pathinfo($fileName, PATHINFO_FILENAME).DS;

      // 専用ディレクトリを用意、添付されたファイルを共有ディレクトリに移動
      system('mkdir -p '.$dirPath.'; cd $_; pdf2svg '.$files['tmp_name'].' %d.svg all');
      $ret = "";
      // ディレクトリの存在を確認し、ハンドルを取得
      if( is_dir( $dirPath ) && $handle = opendir( $dirPath ) ) {
        $page = 0;
        // ループ処理
        while( ($file = readdir($handle)) !== false ) {
          // ファイルのみ取得
          if( filetype( $path = $dirPath . $file ) !== "file" ) continue;
          $retPath = $this->Amazon->putObject("medialink".DS."svg_".pathinfo($fileName, PATHINFO_FILENAME)."_".basename($path), $path);
          $ret = ( $retPath ) ? pathinfo($retPath, PATHINFO_EXTENSION) : ""; // 拡張子を取得
          if( strcmp($ret, "svg") !== 0 ) break; // 拡張子が得られなければ保存失敗とみなす
          $page ++;
        }

        $fileSettings['pages'] = $page;
        // 専用ディレクトリを削除する
        system("rm -Rf ".WWW_ROOT.'files'.DS.pathinfo($fileName, PATHINFO_FILENAME));
      }
      // SVGファイルが保存できたら、PDFを保存する
      if ( strcmp($ret, "svg") === 0 && file_exists($files['tmp_name']) ) {
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
      $saveData['TDocument']['settings'] = $this->jsonEncode($fileSettings);
    }

    if($this->TDocument->save($saveData, false)) {
      // 昔のファイルを削除する
      if ( !empty($nowData['TDocument']) ) {
        // ファイルを削除
        $this->_removeDocuments($nowData['TDocument']['file_name'], $nowData['TDocument']['settings']);
      }
      if ( !empty($saveData['TDocument']['file_name']) ) {
        $this->_createThumnail($saveData['TDocument']['file_name']);
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
   *  資料のサムネイルを作成する
   *  @param string $fileName 資料の保存先パス
   *  @return void
   * */
  private function _createThumnail($fileName){
    $name = C_PREFIX_DOCUMENT.pathinfo($fileName, PATHINFO_FILENAME).".jpg"; //サムネイルのファイル名
    $thumbname = C_PATH_TMP_IMG_DIR.DS.$name; //サムネイルのパス名
    /* 画像の読み込み */
    $thumbImg = new Imagick();
    /* サムネイルの作成 */
    $thumbImg->readImage(C_AWS_S3_HOSTNAME.C_AWS_S3_BUCKET."/medialink/".$fileName);
    $thumbImg->setImageIndex(0);
    $thumbImg->setImageFormat ("jpeg");
    /* リサイズした画像を保存する */
    try {
      $thumbImg->writeImage($thumbname);
      $ret = $this->Amazon->putObject("medialink/".$name, $thumbname);

      if ( $ret !== "" ) {
        unlink($thumbname);
      }

      $thumbImg->destroy();
    } catch (Exception $e) {
      echo $e->getMessage();
    }
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
  /* *
   * プレビュー画面
   * @return void
   * */
  public function openPreview() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';

    // const
    $this->render('/Elements/TDocuments/remoteOpenPreviewScreen');
  }
}
