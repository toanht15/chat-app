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
        $this->Amazon->removeObject("medialink/".$ret['TDocument']['file_name']);
        $this->Amazon->removeObject("medialink/".C_PREFIX_DOCUMENT.pathinfo($ret['TDocument']['file_name'], PATHINFO_FILENAME).".jpg");

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
    }

    if ( isset($saveData['TDocument']['files']['name']) && empty($saveData['TDocument']['files']['name']) ) {
      unset($saveData['TDocument']['files']);
    }
    $this->TDocument->set($saveData);

    // バリデーションチェックに失敗したら
    if ( !$this->TDocument->validates() ) return false;

    // ファイルが添付されたら
    $fileName = "";
    if ( !empty($saveData['TDocument']['files']) ) {
      $file = $saveData['TDocument']['files'];
      $fileName = $this->userInfo['MCompany']['company_key']."_".date("YmdHis").".".pathinfo($file['name'], PATHINFO_EXTENSION);
      $ret = $this->Amazon->putObject("medialink/".$fileName, $file['tmp_name']);

      // ファイルが保存できなかったら
      if ( empty($ret) ) {
        $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.fileSaveFailed')]);
        return false;
      }

      if ( !empty($saveData['TDocument']['id']) ) { // ファイル添付＆更新の場合
        $nowData = $this->TDocument->read(null, $saveData['TDocument']['id']);
      }
      unset($saveData['TDocument']['files']);
      $saveData['TDocument']['file_name'] = $fileName;
    }

    if($this->TDocument->save($saveData, false)) {
      // 昔のファイルを削除する
      if ( !empty($nowData['TDocument']) ) {
        $this->Amazon->removeObject("medialink/".$nowData['TDocument']['file_name']);
        $this->Amazon->removeObject("medialink/".C_PREFIX_DOCUMENT.pathinfo($nowData['TDocument']['file_name'], PATHINFO_FILENAME).".jpg");
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
        $this->Amazon->removeObject("medialink/".$fileName);
      }
      $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
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
}
