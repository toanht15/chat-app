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
App::uses('WidgetSettingController', 'Controller');

class TChatbotDiagramsController extends WidgetSettingController
{
  public $uses = array('TChatbotDiagram', 'TransactionManager', 'TChatbotScenario', 'MWidgetSetting', 'TAutoMessage');
  public $paginate = array(
    'TChatbotDiagram' => array(
      'limit' => 100,
      'order' => array(
        'TChatbotDiagram.sort' => 'asc',
        'TChatbotDiagram.id' => 'asc'
      ),
      'fields' => array('TChatbotDiagram.*'),
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

  public function index()
  {
    $this->paginate['TChatbotDiagram']['conditions']['TChatbotDiagram.m_companies_id'] = $this->userInfo['MCompany']['id'];
    $data = $this->paginate('TChatbotDiagram');
    foreach($data as &$item) {
      $item['callerInfo'] = $this->_getDiagramCallerInfo($item['TChatbotDiagram']['id'], array());
    }
    $this->set('settingList', $data);
  }

  public function add($id = null)
  {
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
  }

  public function save()
  {
    if ($this->request->is('post')) {
      $transaction = $this->TransactionManager->begin();

      // FIXME ソート
      $saveData = array(
        'm_companies_id' => $this->userInfo['MCompany']['id'],
        'name' => $this->request->data['TChatDiagram']['name'],
        'activity' => $this->request->data['TChatbotDiagram']['activity']
      );

      try {
        $this->TChatbotDiagram->save($saveData);
        $this->TransactionManager->commitTransaction($transaction);
      } catch (Exception $exception) {
        throw new InternalErrorException('DB処理に失敗しました。：' . $exception->getMessage());
        $this->TransactionManager->rollbackTransaction($transaction);

      }

      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
      $this->redirect(['action' => 'index']);
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
  private function _getDiagramCallerInfo($id, $scenarioList) {
    $callerInfo = [];

    // 呼び出し元オートメッセージ情報を取得する
    $callerInfo['TAutoMessage'] = $this->TAutoMessage->coFind('list', [
      'fileds' => ['id', 'name'],
      'order' => [
        'TAutoMessage.sort' => 'asc',
        'TAutoMessage.id' => 'asc'
      ],
      'conditions' => [
        'TAutoMessage.del_flg != ' => 1,
        'TAutoMessage.t_chatbot_diagram_id' => $id,
        'TAutoMessage.m_companies_id' => $this->userInfo['MCompany']['id']
      ]
    ]);

    // 呼び出し元シナリオ情報を取得する
    $matchScenarioNames = [];
    /*
    $keyword = '"tChatbotScenarioId":"'. $id . '"';
    $branchOnCondKeyword = '"callScenarioId":"'. $id . '"';
    $branchOnCondKeywordForSelf = '"callScenarioId":"self"';
    foreach ($scenarioList as $scenario) {
      if (strpos($scenario['TChatbotScenario']['activity'], $keyword)) {
        $matchScenarioNames[] = $scenario['TChatbotScenario']['name'];
      } else if (strpos($scenario['TChatbotScenario']['activity'], $branchOnCondKeyword)) {
        $matchScenarioNames[] = $scenario['TChatbotScenario']['name'];
      } else if (strcmp($id, $scenario['TChatbotScenario']['id']) === 0 && strpos($scenario['TChatbotScenario']['activity'], $branchOnCondKeywordForSelf)) {
        $matchScenarioNames[] = self::CALL_SELF_SCENARIO_NAME;
      }
    }
    */
    $callerInfo['TChatbotScenario'] = $matchScenarioNames;

    return $callerInfo;
  }
}
