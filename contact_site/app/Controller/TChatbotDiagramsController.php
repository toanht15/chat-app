<?php

/**
 * Created by PhpStorm.
 * User: ryo.hosokawa
 * Date: 2019/02/18
 * Time: 21:03
 * @property TChatbotDiagram $TChatbotDiagram
 * @property TChatbotDiagramNodeName $TChatbotDiagramNodeName
 * @property TChatbotScenario $TChatbotScenario
 * @property TransactionManager $TransactionManager
 */
App::uses('WidgetSettingController', 'Controller');

class TChatbotDiagramsController extends WidgetSettingController
{
  public $uses = array('TChatbotDiagram', 'TChatbotDiagramNodeName', 'TransactionManager', 'TChatbotScenario', 'MWidgetSetting', 'TAutoMessage');
  public $paginate = array(
    'TChatbotDiagram' => array(
      'limit' => 100,
      'order' => array(
        'TChatbotDiagram.sort' => 'asc',
        'TChatbotDiagram.id' => 'asc'
      ),
      'fields' => array('DISTINCT TChatbotDiagram.id', 'TChatbotDiagram.name', 'TChatbotDiagram.sort'),
      'conditions' => array('TChatbotDiagram.del_flg != ' => 1),
      'joins' => array(
        array(
          'type' => 'LEFT',
          'table' => 't_auto_messages',
          'alias' => 'TAutoMessage',
          'conditions' => array(
            'TChatbotDiagram.id = TAutoMessage.t_chatbot_diagram_id'
          )
        )
      ),
      'recursive' => -1
    )
  );

  public function beforeFilter()
  {
    parent::beforeFilter(); // TODO: Change the autogenerated stub
    $this->chatbotScenarioInputType = Configure::read('chatbotScenarioInputType');
  }

  public function index()
  {
    $this->set('title_for_layout', 'チャットツリー設定');
    $this->paginate['TChatbotDiagram']['conditions']['TChatbotDiagram.m_companies_id'] = $this->userInfo['MCompany']['id'];
    $data = $this->paginate('TChatbotDiagram');
    foreach($data as &$item) {
      $item['callerInfo'] = $this->_getDiagramCallerInfo($item['TChatbotDiagram']['id'], array());
    }
    $this->set('settingList', $data);
  }

  public function add($id = null)
  {
    if(empty($id)) {
      $this->set('title_for_layout', 'チャットツリーエディタ');
    } else {
      $this->set('title_for_layout', 'チャットツリーエディタ');
    }
    $scenarioData = $this->_getScenarioList();
    $this->set('scenarioList', $scenarioData);
    // プレビュー・シミュレーター表示用ウィジェット設定の取得
    $this->set('widgetSettings', $this->_getWidgetSettings());
    if (empty($id)) {

    } else {
      $data = $this->TChatbotDiagram->find('first', array(
        'conditions' => array(
          'id' => $id,
          'm_companies_id' => $this->userInfo['MCompany']['id']
        )
      ));
      $this->request->data = $data;
    }
    $this->_viewElement();
  }

  public function save()
  {
    if ($this->request->is('post')) {
      $this->TChatbotDiagram->create();
      $params = [
        'fields' => [
          'TChatbotDiagram.sort'
        ],
        'conditions' => [
          'TChatbotDiagram.m_companies_id' => $this->userInfo['MCompany']['id']
//              'TAutoMessage.del_flg != ' => 1
        ],
        'order' => [
          'TChatbotDiagram.sort' => 'desc',
          'TChatbotDiagram.id' => 'desc'
        ],
        'limit' => 1,
        'recursive' => -1
      ];
      $lastData = $this->TChatbotDiagram->find('first', $params);
      if ($lastData) {
        if ($lastData['TChatbotDiagram']['sort'] === '0'
          || $lastData['TChatbotDiagram']['sort'] === 0
          || $lastData['TChatbotDiagram']['sort'] === null) {
          //ソート順が登録されていなかったらソート順をセットする
          if (!$this->remoteSetSort()) {
            $this->set('alertMessage',
              ['type' => C_MESSAGE_TYPE_ERROR, 'text' => Configure::read('message.const.saveFailed')]);
            throw new AutoMessageException('ソート順が設定できませんでした。');
          }
          //もう一度ソートの最大値を取り直す
          $lastData = $this->TChatbotDiagram->find('first', $params);
        }
      }
      $nextSort = 1;
      if (!empty($lastData)) {
        $nextSort = intval($lastData['TChatbotDiagram']['sort']) + 1;
      }

      $transaction = $this->TransactionManager->begin();
      // FIXME ソート
      $saveData = array(
        'm_companies_id' => $this->userInfo['MCompany']['id'],
        'name' => $this->request->data['TChatbotDiagram']['name'],
        'activity' => $this->request->data['TChatbotDiagram']['activity'],
        'sort' => $nextSort
      );

      if(!empty($this->request->data['TChatbotDiagram']['id'])) {
        $saveData['id'] = $this->request->data['TChatbotDiagram']['id'];
      }

      try {
        if(!$this->TChatbotDiagram->save($saveData)) {
          throw new Exception('保存に失敗しました');
        }
        $insertId = $this->TChatbotDiagram->getLastInsertId() ? $this->TChatbotDiagram->getLastInsertId() : $saveData['id'];
        $this->insertNodeNameTable($insertId, json_decode($this->request->data['TChatbotDiagram']['activity'], TRUE));

        $this->TransactionManager->commitTransaction($transaction);
      } catch (Exception $exception) {
        throw new InternalErrorException('DB処理に失敗しました。：' . $exception->getMessage());
        $this->TransactionManager->rollbackTransaction($transaction);

      }

      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
      $this->redirect(['action' => 'index']);
    }
  }

  private function insertNodeNameTable($id, $activity) {
    $cells = $activity['cells'];
    foreach($cells as $index => $cell) {
      if(strcmp($cell['type'], 'devs.Model') !== 0) continue;
      if(!empty($cell['attrs']['actionParam']['nodeName'])) {
        $nodeName = $this->TChatbotDiagramNodeName->find('first', array(
          'conditions' => array(
            'm_companies_id' => $this->userInfo['MCompany']['id'],
            'node_id' => $cell['id']
          )
        ));
        if(empty($nodeName)) {
          $this->TChatbotDiagramNodeName->create();
          $this->TChatbotDiagramNodeName->set(array(
            'm_companies_id' => $this->userInfo['MCompany']['id'],
            't_chatbot_diagram_id' => $id,
            'type' => $cell['attrs']['nodeBasicInfo']['nodeType'],
            'node_id' => $cell['id'],
            'node_name' => $cell['attrs']['actionParam']['nodeName'],
            'del_flg' => 0
          ));
        } else {
          $this->TChatbotDiagramNodeName->create();
          $nodeName['TChatbotDiagramNodeName']['t_chatbot_diagram_id'] = $id;
          $nodeName['TChatbotDiagramNodeName']['type'] = $cell['attrs']['nodeBasicInfo']['nodeType'];
          $nodeName['TChatbotDiagramNodeName']['node_name'] = $cell['attrs']['actionParam']['nodeName'];
          $this->TChatbotDiagramNodeName->set($nodeName);
        }
        if (!$this->TChatbotDiagramNodeName->save()) {
          throw new Exception('t_chatbot_diagram_node_nameテーブルにデータ保存時にエラー発生しました。');
        }
      }
    }
  }

  /* *
   * コピー処理
   * @return void
   * */
  public function remoteCopyEntryForm()
  {
    Configure::write('debug', 0);
    $this->autoRender = false;
    $this->layout = 'ajax';
    $selectedList = $this->request->data['selectedList'];
    //コピー元のオートメッセージリスト取得
    foreach ($selectedList as $value) {
      $copyData[] = $this->TChatbotDiagram->read(null, $value);
    }
    $errorMessage = [];
    //コピー元のオートメッセージリストの数だけ繰り返し
    $res = true;
    foreach ($copyData as $value) {
      $this->TChatbotDiagram->create();
      $saveData = [];
      $params = [
        'fields' => [
          'TChatbotDiagram.sort'
        ],
        'conditions' => [
          'TChatbotDiagram.m_companies_id' => $this->userInfo['MCompany']['id']
          //'TChatbotDiagram.del_flg != ' => 1
        ],
        'order' => [
          'TChatbotDiagram.sort' => 'desc',
          'TChatbotDiagram.id' => 'desc'
        ],
        'limit' => 1,
        'recursive' => -1
      ];
      $lastData = $this->TChatbotDiagram->find('first', $params);
      if ($lastData['TChatbotDiagram']['sort'] === '0'
        || $lastData['TChatbotDiagram']['sort'] === 0
        || $lastData['TChatbotDiagram']['sort'] === null) {
        //ソート順が登録されていなかったらソート順をセットする
        if (!$this->remoteSetSort()) {
          $this->set('alertMessage',
            ['type' => C_MESSAGE_TYPE_ERROR, 'text' => Configure::read('message.const.saveFailed')]);
          return false;
        }
        //もう一度ソートの最大値を取り直す
        $lastData = $this->TChatbotDiagram->find('first', $params);
      }
      $nextSort = 1;
      if (!empty($lastData)) {
        $nextSort = intval($lastData['TChatbotDiagram']['sort']) + 1;
      }

      $saveData['TChatbotDiagram']['sort'] = $nextSort;
      $saveData['TChatbotDiagram']['m_companies_id'] = $value['TChatbotDiagram']['m_companies_id'];
      $saveData['TChatbotDiagram']['name'] = $value['TChatbotDiagram']['name'] . 'コピー';
      $saveData['TChatbotDiagram']['activity'] = $value['TChatbotDiagram']['activity'];
      $saveData['TChatbotDiagram']['del_flg'] = $value['TChatbotDiagram']['del_flg'];

      $this->TChatbotDiagram->set($saveData);
      $this->TChatbotDiagram->begin();

      if (!$this->TChatbotDiagram->validates()) {
        $res = false;
        $errorMessage = $this->TChatbotDiagram->validationErrors;
        $this->TChatbotDiagram->rollback();
      } else {
        if ($this->TChatbotDiagram->save($saveData, false)) {
          $this->TChatbotDiagram->commit();
          $this->Session->delete('dstoken');
          $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
        }
      }
    }
  }

  /* *
   * 削除(一覧ページから実行)
   * */
  public function remoteDelete(){
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $diagramIdList = (isset($this->request->data['selectedList'])) ? $this->request->data['selectedList'] : "";

    // 呼び出し設定されている場合は削除しない
    $callerInfo = $this->_getDiagramCallerInfo($diagramIdList);
    if (!empty($callerInfo['TAutoMessage'])) {
      $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
      return;
    }

    $transactions = null;
    try {
      $transactions = $this->TransactionManager->begin();

      for($i = 0; $i < count($diagramIdList); $i++) {
        if (!$this->TChatbotDiagram->logicalDelete($diagramIdList[$i])) {
          throw new ChatbotScenarioException('シナリオ削除エラー');
        }
      }

      $this->TransactionManager->commitTransaction($transactions);
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));

    } catch (ChatbotScenarioException $e) {
      if ($transactions) {
        $this->TransactionManager->rollbackTransaction($transactions);
      }
      $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
    } catch (Exception $e) {
      if ($transactions) {
        $this->TransactionManager->rollbackTransaction($transactions);
      }
      $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
    }
  }

  /**
   * @return bool|void
   * @throws Exception
   */
  public function changeSort()
  {
    Configure::write('debug', 2);
    $this->autoRender = false;
    $this->layout = 'ajax';
    if (!$this->request->is('ajax')) {
      return false;
    }
    if (!empty($this->params->data['list'])) {
      $this->TChatbotDiagram->begin();
      $list = $this->params->data['list'];
      $sortNoList = $this->params->data['sortNolist'];
      sort($sortNoList);
      /* 現在の並び順を取得 */
      $params = $this->paginate['TChatbotDiagram'];
      $params['fields'] = array(
        'TChatbotDiagram.id',
        'TChatbotDiagram.sort'
      );
      $params['conditions']['TChatbotDiagram.m_companies_id'] = $this->userInfo['MCompany']['id'];
      unset($params['limit']);
      $prevSort = $this->TChatbotDiagram->find('list', $params);
      // 新しくソート順を設定したため、空で来ることがある
      $reset_flg = false;
      foreach ($prevSort as $key => $val) {
        // 設定されていない値'0'が一つでも入っていたらsortをリセット
        if ($val === '0' || $val === 0 || $val === null) {
          $reset_flg = true;
        }
      }
      if ($reset_flg) {
        // ソート順が登録されていなかったらソート順をセットする
        if (!$this->_remoteSetSort()) {
          $this->set('alertMessage',
            array('type' => C_MESSAGE_TYPE_ERROR, 'text' => Configure::read('message.const.saveFailed')));
          return false;
        }
        $prevSort = $this->TChatbotDiagram->find('list', $params);
        // この時$sortNoListは空なので作成する
        if (empty($sortNoList)) {
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
        if (isset($prevSort[$id])) {
          $saveData = array(
            'TChatbotDiagram' => array(
              'id' => $id,
              'sort' => $sortNoList[$i]
            )
          );
          if (!$this->TChatbotDiagram->validates()) {
            $ret = false;
            break;
          }
          if (!$this->TChatbotDiagram->save($saveData)) {
            $ret = false;
            break;
          }
        } else {
          // 送信されたシナリオ設定と現在DBに存在するシナリオ設定に差がある場合
          $this->TChatbotDiagram->rollback();
          $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.configChanged'));
          return;
        }
      }
      if ($ret) {
        $this->TChatbotDiagram->commit();
        $this->redirect(['action' => 'index']);
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));

      } else {
        $this->TChatbotDiagram->rollback();
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
      }
    }
  }

  /**
   * オートメッセージ設定ソート順を現在のID順でセット
   *
   * */
  public function remoteSetSort()
  {
    $this->TChatbotDiagram->begin();
    /* 現在の並び順を取得 */
    $this->paginate['TChatbotDiagram']['conditions']['TChatbotDiagram.m_companies_id'] = $this->userInfo['MCompany']['id'];
    $params = [
      'fields' => [
        'TChatbotDiagram.sort'
      ],
      'conditions' => [
        'TChatbotDiagram.m_companies_id' => $this->userInfo['MCompany']['id']
//            'TChatbotDiagram.del_flg != ' => 1
      ],
      'order' => [
        'TChatbotDiagram.sort' => 'asc',
        'TChatbotDiagram.id' => 'asc'
      ],
      'limit' => 1,
      'recursive' => -1
    ];
    $params['fields'] = [
      'TChatbotDiagram.id',
      'TChatbotDiagram.sort'
    ];
    unset($params['limit']);
    $prevSort = $this->TChatbotDiagram->find('list', $params);
    //ソート順のリセットはID順とする
    $i = 1;
    foreach ($prevSort as $key => $val) {
      $prevSort[$key] = strval($i);
      $i++;
    }
    $prevSortKeys = am($prevSort);
    $this->log($prevSortKeys, LOG_DEBUG);
    $i = 0;
    $ret = true;
    foreach ($prevSort as $key => $val) {
      $id = $key;
      $saveData = [
        'TChatbotDiagram' => [
          'id' => $id,
          'sort' => $prevSortKeys[$i]
        ]
      ];
      if (!$this->TChatbotDiagram->validates()) {
        $ret = false;
        break;
      }
      if (!$this->TChatbotDiagram->save($saveData)) {
        $ret = false;
        break;
      }
      $i++;
    }
    if ($ret) {
      $this->TChatbotDiagram->commit();
      return true;
    } else {
      $this->TChatbotDiagram->rollback();
      return false;
    }
  }

  /**
   * シナリオ設定ソート順を現在のID順でセット
   * */
  private function _remoteSetSort()
  {
    try {
      // 現在の並び順を取得
      $scenarioList = $this->TChatbotDiagram->find('all', array(
        'fields' => array(
          'TChatbotDiagram.id',
          'TChatbotDiagram.sort'
        ),
        'conditions' => array(
          'TChatbotDiagram.m_companies_id' => $this->userInfo['MCompany']['id']
        ),
        'order' => array(
          'TChatbotDiagram.sort' => 'asc',
          'TChatbotDiagram.id' => 'asc'
        ),
        'recursive' => -1
      ));

      // sort順を振り直す
      $count = 1;
      foreach ($scenarioList as &$value) {
        $value['TChatbotDiagram']['sort'] = $count++;
      }

      return $this->TChatbotDiagram->saveAll($scenarioList);
    } catch (Exeption $e) {
      throw $e;
    }
  }

  /**
   * アクション「シナリオ呼び出し」に表示する、idとnameの一覧を返す
   * @param  Integer $currentId 現在表示中のシナリオID（結果のリストから除外する）
   * @return Array              シナリオ一覧
   */
  private function _getScenarioList()
  {
    return $this->TChatbotScenario->coFind('list', [
      'fields' => ['TChatbotScenario.id', 'TChatbotScenario.name'],
      'order' => [
        'TChatbotScenario.sort' => 'asc',
        'TChatbotScenario.id' => 'asc'
      ],
      'conditions' => [
        'TChatbotScenario.del_flg != ' => 1
      ]
    ]);
  }

  /**
   * 呼び出し元情報を取得する
   * @param  Int    $id           ダイアグラムID
   * @param  Array  $scenarioList アクション「チャットツリー呼び出し」を含むシナリオ一覧
   * @return Array                呼び出し元情報
   */
  private function _getDiagramCallerInfo($id) {
    $callerInfo = array();

    // 呼び出し元オートメッセージ情報を取得する
    $callerInfo['TAutoMessage'] = $this->TAutoMessage->coFind('list', array(
      'fileds' => array('id', 'name'),
      'order' => array(
        'TAutoMessage.sort' => 'asc',
        'TAutoMessage.id' => 'asc'
      ),
      'conditions' => array(
        'TAutoMessage.del_flg != ' => 1,
        'TAutoMessage.action_type' => C_AUTO_ACTION_TYPE_SELECTCHATDIAGRAM,
        'TAutoMessage.t_chatbot_diagram_id' => $id,
        'TAutoMessage.m_companies_id' => $this->userInfo['MCompany']['id']
      )
    ));

    /*
    // 呼び出し元シナリオ情報を取得する
    $matchScenarioNames = array();

    $keyword = '"tChatbotDiagramId":"'. $id . '"';
    foreach ($scenarioList as $scenario) {
      if (strpos($scenario['TChatbotScenario']['activity'], $keyword)) {
        $matchScenarioNames[] = $scenario['TChatbotScenario']['name'];
      } else if (strpos($scenario['TChatbotScenario']['activity'], $branchOnCondKeyword)) {
        $matchScenarioNames[] = $scenario['TChatbotScenario']['name'];
      } else if (strcmp($id, $scenario['TChatbotScenario']['id']) === 0 && strpos($scenario['TChatbotScenario']['activity'], $branchOnCondKeywordForSelf)) {
        $matchScenarioNames[] = self::CALL_SELF_SCENARIO_NAME;
      }
    }
    $callerInfo['TChatbotScenario'] = $matchScenarioNames;
    */

    return $callerInfo;
  }

  private function _viewElement() {
    // シミュレーター表示用ウィジェット設定の取得
    $this->request->data['widgetSettings'] = $this->_getWidgetSettings();
    $this->set('companyKey', $this->userInfo['MCompany']['company_key']);
    // 入力タイプ種別
    $this->set('chatbotScenarioInputType', $this->chatbotScenarioInputType);
  }
}
