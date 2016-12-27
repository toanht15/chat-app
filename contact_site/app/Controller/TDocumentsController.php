<?php
/**
 *TDocumentsController  controller.
 * 資料設定
 */

class TDocumentsController extends AppController {
  public $uses = ['TDocument','MDocumentTag'];
  public $components = ['Amazon'];
  public $paginate = [
    'TDocument' => [
      'order' => ['TDocument.id' => 'asc'],
      'fields' => ['*'],
    ]
  ];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', '資料設定');
  }

  /* *
   * 一覧画面
   * @return void
   * */
  public function index() {
    $documents =  $this->paginate('TDocument');
    $labelList = $this->MDocumentTag->find('list', ['fields'=> ['id','name']]);
    $documentList = [];
    foreach ($documents as $key => $document){
      $tags = [];
      foreach((array)json_decode($document['TDocument']['tag'],true) as $id){
        if ( !empty($labelList[$id]) ) {
          $tags[] = $labelList[$id];
        }
      }
      $document['TDocument']['tag'] = $tags;
      $documentList[$key] = $document;
    }
    $this->set('documentList', $documentList);
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
    ]);;
    if ( count($ret) === 1 ) {
      if ( $this->TDocument->delete($id) ) {
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
    if(!empty($this->request->data['TDocument']['tag'])) {
      $inputData = $this->request->data['TDocument']['tag'];
      $saveData['TDocument']['tag'] = $this->jsonEncode($inputData);
    }
    $saveData['TDocument']['m_companies_id'] = $this->userInfo['MCompany']['id'];

    $this->TDocument->begin();
    if ( empty($saveData['TDocument']['id']) ) {
      $this->TDocument->create();
    }

    $this->TDocument->set($saveData);

    // バリデーションチェックに失敗したら
    if ( !$this->TDocument->validates() ) return false;

    // ファイルが添付されたら
    if ( !empty($saveData['TDocument']['files']) ) {
      $file = $saveData['TDocument']['files'];
      $fileName = $this->userInfo['MCompany']['company_key']."_".date("YmdHis").".".pathinfo($file['name'], PATHINFO_EXTENSION);
      $ret = $this->Amazon->putObject("medialink/".$fileName, $file['tmp_name']);

      // ファイルが保存できなかったら
      if ( empty($ret) ) {
        $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.fileSaveFailed')]);
        return false;
      }

      unset($saveData['TDocument']['files']);
      $saveData['TDocument']['file_name'] = $fileName;
    }

    if($this->TDocument->save($saveData, false)) {
      $this->TDocument->commit();
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
      $this->redirect(['controller' => 'TDocuments', 'action' => 'index']);
    }
    else {
      $this->TDocument->rollback();
      $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
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
