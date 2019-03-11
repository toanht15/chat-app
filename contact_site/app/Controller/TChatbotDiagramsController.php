<?php

/**
 * Created by PhpStorm.
 * User: ryo.hosokawa
 * Date: 2019/02/18
 * Time: 21:03
 * @property TChatbotDiagram $TChatbotDiagram
 * @property TChatbotScenario $TChatbotScenario
 * @property TransactionManager $TransactionManager
 */
class TChatbotDiagramsController extends AppController
{
  public $uses = array('TChatbotDiagram', 'TransactionManager', 'TChatbotScenario');
  public $paginate = [
    'TChatbotDiagram' => [
      'limit' => 100,
      'order' => [
        'TChatbotDiagram.sort' => 'asc',
        'TChatbotDiagram.id' => 'asc'
      ],
      'fields' => ['TChatbotDiagram.*'],
      'conditions' => ['TChatbotDiagram.del_flg != ' => 1],
      'recursive' => -1
    ]
  ];

  public function beforeFilter()
  {
    parent::beforeFilter();
    $this->set('title_for_layout', 'チャットツリー設定');
  }

  public function index()
  {
    $this->paginate['TChatbotDiagram']['conditions']['TChatbotDiagram.m_companies_id'] = $this->userInfo['MCompany']['id'];
    $data = $this->paginate('TChatbotDiagram');
    $this->set('settingList', $data);
  }

  public function add($id)
  {
    if (empty($id)) {
      $scenarioData = $this->_getScenarioList();
      $this->set('scenarioList', $scenarioData);
    } else {
      $data = $this->TChatbotDiagram->find('all', array(
        'conditions' => array(
          'id' => $id,
          'm_companies_id' => $this->userInfo['MCompany']['id']
        )
      ));
      $this->request->data = $data;
    }
  }

  public function save()
  {
    if ($this->request->is('post')) {
      $transaction = $this->TransactionManager->begin();

      $saveData = array(
        'm_companies_id' => $this->userInfo['MCompany']['id'],
        'name' => $this->request->data['TChatbotDiagrams']['name'],
        'activity' => $this->request->data['TChatbotDiagrams']['activity']
      );

      try {
        $this->TChatbotDiagram->save($saveData);
        $this->TransactionManager->commitTransaction($transaction);
      } catch (Exception $exception) {
        throw new InternalErrorException('DB処理に失敗しました。：' . $exception->getMessage());
        $this->TransactionManager->rollbackTransaction($transaction);

      }

      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
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
        $this->redirect(array('Controller' => $this->name, 'action' => 'index'));
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));

      } else {
        $this->TChatbotDiagram->rollback();
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
      }
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
    return $this->TChatbotScenario->coFind('all', [
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
}
