<?php
/**
 *TDocumentsController  controller.
 * 資料設定
 */
class TDocumentsController extends AppController {
  public $uses = ['TDocument','MDocumentTag'];
  public $paginate = [
    'MDocumentTag' => [
      'limit' => 10,
      'order' => ['TDocument.id' => 'asc'],
      'joins' => [
        [
          'type' => 'inner',    // もしくは left
          'table' => 'm_document_tags',
          'alias' => 'MDocumentTag',
          'conditions' => [
            'MDocumentTag.m_companies_id = TDocument.m_companies_id'
          ],
        ],
      ],
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
    $users = $this->TDocument->find('all');
    $labelList = $this->MDocumentTag->find('list', ['fields'=> ['id','name']]);
    $documentList = [];
    foreach ($users as $key => $user){
      $tags = [];
      foreach((array)json_decode($user['TDocument']['tag'],true) as $id){
        if ( !empty($labelList[$id]) ) {
          $tags[] = $labelList[$id];
        }
      }
      $user['TDocument']['tag'] = $tags;
      $documentList[$key] = $user;
    }
    $this->set('userList', $documentList);
  }

  public function add() {

    $radio = array(
      '1' => '可',
      '2' => '不可',
    );

    $radio2 = array(
      '1' => 'する',
      '2' => 'しない'
    );

    $this->set('radio', $radio);
    $this->set('radio2', $radio2);

    if($this->request->is('post')) {
      $this->_entry($this->request->data);
        $errors = $this->TDocument->validationErrors;
        $this->set('errors', $errors);
    }
    $status = ['fields'=> ['id','name']];
    $labelList = $this->MDocumentTag->find('list',$status);
    $this->set('labelHideList',$labelList);
  }

  public function edit($id) {

    $radio = array(
      '1' => '可',
      '2' => '不可',
    );

    $radio2 = array(
      '1' => 'する',
      '2' => 'しない'
    );

    $this->set('radio', $radio);
    $this->set('radio2', $radio2);

    if($this->request->is('post') || $this->request->is('put')) {
       $this->_entry($this->request->data);

        $errors = $this->TDocument->validationErrors;
        $this->set('errors', $errors);
        $status = ['fields'=> ['id','name']];
        $labelList = $this->MDocumentTag->find('list',$status);
        $this->set('labelHideList',$labelList);
    }
    else {
      $this->TDocument->id = $id;
      $tags =   json_decode($this->TDocument->read(null,$id)['TDocument'] ['tag'],true);
      $this->request->data = $this->TDocument->read(null,$id);
      $labelList = $this->MDocumentTag->find('list', ['fields'=> ['id','name']]);
      $documentList = [];
      $tags = [];
      foreach((array)json_decode($this->request->data['TDocument']['tag'],true) as $id){
        if ( !empty($labelList[$id]) ) {
          $tags[] = $id;
          }
        }
      $this->request->data['TDocument']['tag'] = $tags;
      $status = ['fields'=> ['id','name']];
      $labelList = $this->MDocumentTag->find('list',$status);
      $selectedLabelList = $tags;
      $this->set('selectedLabelList', $selectedLabelList);
      $this->set('labelHideList',$labelList);
    }
  }

  public function addTag() {
    $this->autoRender = false;
    if($this->request->is('post')) {
      $this->request->data['m_companies_id'] = $this->userInfo['MCompany']['id'];
      if($this->MDocumentTag->save($this->request->data,false)) {
        $this->redirect($this->referer());
      }
    }
  }

  public function remoteDelete(){
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $id =  $this->request->data['id'];
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
    $saveData = $this->request->data;
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

       if($this->TDocument->validates() && $this->TDocument->save($saveData,false)) {
        $this->TDocument->commit();
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
        $this->redirect(['controller' => 'TDocuments', 'action' => 'index']);
      }
      else {
        $this->TDocument->rollback();
        $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
    }
  }
}
