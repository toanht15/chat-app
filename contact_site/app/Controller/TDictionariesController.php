<?php
/**
 * TDictionariesController controller.
 * ユーザーマスタ
 */
class TDictionariesController extends AppController {
  public $uses = ['TDictionary'];
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
    $this->set('title_for_layout', '単語帳管理');
  }

  /* *
   * 一覧画面
   * @return void
   * */
  public function index() {
    $this->set('dictionaryList', $this->TDictionary->find('all', $this->_setParams()));
    $this->_viewElement();
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
    // const
    if ( strcmp($this->request->data['type'], 2) === 0 ) {
      $this->TDictionary->recursive = -1;
      $this->request->data = $this->TDictionary->read(null, $this->request->data['id']);
    }
    $this->render('/Elements/TDictionaries/remoteEntry');
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

    // if ( !$this->request->is('ajax') ) return false;
    if (!empty($this->request->data['dictionaryId'])) {
      $this->TDictionary->recursive = -1;
      $saveData = $this->TDictionary->read(null, $this->request->data['dictionaryId']);
    }
    else {
      $this->TDictionary->create();
    }

    $saveData['TDictionary']['m_companies_id'] = $this->userInfo['MCompany']['id'];
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
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
    }
    else {
      $this->TDictionary->rollback();
    }
    $errorMessage = $this->TDictionary->validationErrors;
    return new CakeResponse(['body' => json_encode($errorMessage)]);
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
    if ( $this->TDictionary->delete($this->request->data['id']) ) {
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
    }
    else {
      $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
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
  private function _viewElement(){
    $this->set('dictionaryTypeList', Configure::read("dictionaryType"));
  }

}
