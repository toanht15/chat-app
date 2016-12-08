<?php
/**
 * TDictionariesController controller.
 * 簡易入力
 */
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

class TDictionariesController extends AppController {
  public $uses = ['TDictionary','MCompany'];

  public function beforeFilter() {
    parent::beforeFilter();
    $this->set('title_for_layout', '単語帳管理');
  }

  /* *
  * 一覧画面
  * @return void
  * */
  public function index() {
    //MCompany SessionData
    $mcompanyData = $this->Session->read('global.tmpdata')[0];
    $this->set('dictionaryList', $this->TDictionary->find('all', $this->_setParams()));
  }

  /* *
  * 登録画面
  * @return void
  * */
  public function remoteOpenEntryForm() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    //const
    if ( strcmp($this->request->data['type'], 2) === 0 ) {
      $this->request->data = $this->TDictionary->read(null, $this->request->data['id']);
    }
    $this->render('/TDictionaries/remoteEntryUser');
  }

  /* *
   * 登録処理
   * @return void
   * */
  public function remoteSaveEntryForm() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $tmpData = [];
    $saveData = [];
    $insertFlg = true;
    $errorMessage = null;

    if ( !$this->request->is('ajax') ) return false;
    if (!empty($this->request->data['dictionaryId'])) {
      $tmpData = $this->TDictionary->read(null, $this->request->data['dictionaryId']);
      $insertFlg = false;
    }
    else {
      $this->TDictionary->create();
    }

    $tmpData['TDictionary']['word'] = $this->request->data['word'];
    $tmpData['TDictionary']['type'] = C_AUTHORITY_ADMIN;
    $tmpData['TDictionary']['m_users_id'] = $this->userInfo['id'];
    $mcompanyData = $this->Session->read('global.tmpdata')[0];
    $tmpData['TDictionary']['m_companies_id'] = $mcompanyData['MCompany']['id'];
    //登録の場合sort番号振り分け
    if (empty($this->request->data['dictionaryId'])) {
      $params = [
        'limit' => 1,
        'order' => [
          'TDictionary.sort' => 'desc',
          'TDictionary.id' => 'desc'
        ],
        'fields' => [
          'TDictionary.sort'
        ],
        'conditions' => [
          'TDictionary.m_companies_id' => $mcompanyData['MCompany']['id']
        ],
        'recursive' => -1
      ];
      $lastData = $this->TDictionary->find('first', $params);
      $nextSort = 1;
      if (!empty($lastData)) {
        $nextSort = intval($lastData['TDictionary']['sort']) + 1;
      }
      $tmpData['TDictionary']['sort'] = $nextSort;
    }

    // const
    $this->TDictionary->set($tmpData);
    $this->TDictionary->begin();
    //　バリデーションチェックが成功した場合
    if ( empty($errorMessage) && $this->TDictionary->validates() ) {
      $saveData = $tmpData;
      if ( $this->TDictionary->save($saveData, false) ) {
        $this->TDictionary->commit();
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
      }
      else {
        $this->TDictionary->rollback();
      }
    }
    if ( empty($errorMessage) ) {
      $errorMessage = $this->TDictionary->validationErrors;
    }
    return new CakeResponse(['body' => json_encode($errorMessage)]);
  }

  /* *
  * 削除
  * @return void
  * */
  public function remoteDelete(){
    Configure::write('debug', 0);
    $mcompanyData = $this->Session->read('global.tmpdata')[0];
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $id = $this->request->data['id'];
    $ret = $this->TDictionary->find('first', [
      'fields' => 'TDictionary.*',
      'conditions' => [
        'TDictionary.id' => $id,
        'TDictionary.m_companies_id' => $mcompanyData['MCompany']['id']
      ],
      'recursive' => -1
    ]);;
    if ( count($ret) === 1 ) {
      if ( $this->TDictionary->delete($id) ) {
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
      }
      else {
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
      }
    }
  }

  /**
   * ソート順更新
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
      /* 現在の並び順を取得 */
      $params = $this->_setParams();
      $params['fields'] = [
        'TDictionary.id',
        'TDictionary.sort'
      ];
      unset($params['limit']);
      $prevSort = $this->TDictionary->find('list',$params);
      $this->log($prevSort,LOG_DEBUG);
      $prevSortKeys = am($prevSort);

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
          if (!$this->TDictionary->save($saveData)) {
            $ret = false;
            break;
          }
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

  private function _setParams(){
    $mcompanyData = $this->Session->read('global.tmpdata')[0];
    $params = [
      'order' => [
        'TDictionary.sort' => 'asc',
        'TDictionary.id' => 'asc'
      ],
      'fields' => [
        'TDictionary.*'
      ],
      'conditions' => [
        'TDictionary.m_companies_id' => $mcompanyData['MCompany']['id']
      ],
      'recursive' => -1
    ];
    return $params;
  }
}