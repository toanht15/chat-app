<?php
/**
 * TChatbotScenarioController controller.
 * シナリオ設定
 * @property TransactionManager $TransactionManager
 * @property TChatbotScenario $TChatbotScenario
 * @property TAutoMessage $TAutoMessage
 * @property MWidgetSetting $MWidgetSetting
 * @property MMailTransmissionSetting $MMailTransmissionSetting
 * @property MMailTemplate $MMailTemplate
 * @property TExternalApiConnection $TExternalApiConnection
 * @property TChatbotScenarioSendFile $TChatbotScenarioSendFile
 */

App::uses('WidgetSettingController', 'Controller');
App::uses('ChatbotScenarioException', 'Lib/Error');

class TChatbotScenarioController extends WidgetSettingController {

  const CALL_SELF_SCENARIO_NAME = "（このシナリオ）";

  public $uses = ['TransactionManager', 'TChatbotScenario', 'TAutoMessage', 'MWidgetSetting', 'MMailTransmissionSetting', 'MMailTemplate', 'TExternalApiConnection', 'TChatbotScenarioSendFile', 'TCustomerInformationSetting', 'TLeadListSetting', 'TLeadList', 'TChatbotDiagram'];
  public $paginate = [
    'TChatbotScenario' => [
      'limit' => 100,
      'order' => [
          'TChatbotScenario.sort' => 'asc',
          'TChatbotScenario.id' => 'asc'
      ],
      'fields' => ['TChatbotScenario.*'],
      'conditions' => ['TChatbotScenario.del_flg != ' => 1],
      'recursive' => -1
    ]
  ];

  public $coreSettings = null;

  const SCENARIO_VARIABLES_TEMPLATE = "※このメールはお客様の設定によりsincloから自動送信されました。

ご担当者様

sincloのシナリオ設定によりメールを送信致しました。
以下のメッセージ内容をご確認下さい。

##SCENARIO_VARIABLES_BLOCK##

------------------------------------------------------------------
このメールにお心当たりのない方は、誠に恐れ入りますが
下記連絡先までご連絡ください。
sinclo@medialink-ml.co.jp
------------------------------------------------------------------";

  const SCENARIO_ALL_MESSAGE_TEMPLATE = "※このメールはお客様の設定によりsincloから自動送信されました。

ご担当者様

sincloのシナリオ設定によりメールを送信致しました。
以下のメッセージ内容をご確認下さい。

##SCENARIO_ALL_MESSAGE_BLOCK##

------------------------------------------------------------------
このメールにお心当たりのない方は、誠に恐れ入りますが
下記連絡先までご連絡ください。
sinclo@medialink-ml.co.jp
------------------------------------------------------------------";

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', 'シナリオ設定');
    $this->chatbotScenarioActionList = Configure::read('chatbotScenarioActionList');
    $this->chatbotScenarioInputType = Configure::read('chatbotScenarioInputType');
    $this->chatbotScenarioAttributeType = Configure::read('chatbotScenarioAttributeType');
    $this->chatbotScenarioSendMailType = Configure::read('chatbotScenarioSendMailType');
    $this->chatbotScenarioApiMethodType = Configure::read('chatbotScenarioApiMethodType');
    $this->chatbotScenarioExternalType = Configure::read('chatbotScenarioExternalType');
    $this->chatbotScenarioApiResponseType = Configure::read('chatbotScenarioApiResponseType');
    $this->chatbotScenarioReceiveFileTypeList = Configure::read('chatbotScenarioReceiveFileTypeList');
    $this->chatbotScenarioBranchOnConditionMatchValueType = Configure::read('chatbotScenarioBranchOnConditionMatchValueType');
    $this->chatbotScenarioBranchOnConditionActionType = Configure::read('chatbotScenarioBranchOnConditionActionType');
    $this->chatbotScenarioBranchOnConditionElseActionType = Configure::read('chatbotScenarioBranchOnConditionElseActionType');
    $this->chatbotScenarioLeadTypeList = Configure::read('chatbotScenarioLeadTypeList');

    // FileAppController
    $this->fileTransferPrefix = "fileScenarioTransfer/";
  }

  /**
   * 一覧画面
   * @return void
   * */
  public function index()
  {
    $this->paginate['TChatbotScenario']['conditions']['TChatbotScenario.m_companies_id'] = $this->userInfo['MCompany']['id'];
    $data = $this->paginate('TChatbotScenario');
    // 呼び出し元情報を取得する
    $callActionList        = $this->_findScenarioByActionType(C_SCENARIO_ACTION_CALL_SCENARIO);
    $branchOnConditionList = $this->_findScenarioByActionType(C_SCENARIO_ACTION_BRANCH_ON_CONDITION);
    $scenarioList          = array_merge($callActionList, $branchOnConditionList);
    foreach ($data as &$item) {
      $item['callerInfo'] = $this->_getScenarioCallerInfo($item['TChatbotScenario']['id'], $scenarioList);
    }

    $this->set('settingList', $data);
    $this->_viewElement();
  }

  /**
   * 登録画面
   * @return void
   * */
  public function add() {
    if ( $this->request->is('post') ) {
      $this->_entry($this->request->data);
    }

    // シナリオ設定の一覧を取得する
    $this->request->data['scenarioList'] = $this->_getScenarioList();
    $this->request->data['scenarioListForBranchOnCond'] = array_merge(array(
      0 => array(
        'TChatbotScenario' => array (
          'id' => 'self',
          'name' => 'このシナリオ'
        )
      )
    ),$this->request->data['scenarioList']);
    // プレビュー・シミュレーター表示用ウィジェット設定の取得
    $this->request->data['widgetSettings'] = $this->_getWidgetSettings();
    $this->request->data['leadList'] = $this->leadInfoSet();
    $this->request->data['chatbotDiagramList'] = $this->getChatbotDiagramSettingList();
    $this->_deleteInvalidLeadList();
    $this->set('storedVariableList', $this->getStoredAllVariableList());
    $this->_viewElement();
  }

  /**
   * 更新画面
   * @return void
   * */
  public function edit($id=null) {
    // プレビュー・シミュレーター表示用ウィジェット設定の取得
    $this->request->data['widgetSettings'] = $this->_getWidgetSettings();

    if ( $this->request->is('put') ) {
      $this->_entry($this->request->data);
    }
    else {
      // 確実なデータを取得するために企業IDを指定する形とする
      $editData = $this->TChatbotScenario->coFind("all", [
        'conditions' => [
          'TChatbotScenario.id' => $id
        ]
      ]);

      if (empty($editData) || (!empty($editData) && empty($editData[0]))) {
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.notFoundId'));
        $this->redirect('/TChatbotScenario/index');
      }
      // convert old data -> new json structure
      $this->_convertOldDataToNewStructure($editData[0]['TChatbotScenario']['activity']);
      // アクションごとに必要な設定を追加する
      $this->_setActivityDetailSettings($editData[0]['TChatbotScenario']['activity']);
      $this->request->data['TChatbotScenario'] = $editData[0]['TChatbotScenario'];
    }

    // 呼び出し元情報を取得する
    $scenarioList                      = $this->_findScenarioByActionType(C_SCENARIO_ACTION_CALL_SCENARIO);
    $scenarioList                      = array_merge($scenarioList, $this->_findScenarioByActionType(C_SCENARIO_ACTION_BRANCH_ON_CONDITION));
    $this->request->data['callerInfo'] = $this->_getScenarioCallerInfo($id, $scenarioList);
    // シナリオ設定の一覧を取得する
    $this->request->data['scenarioList'] = $this->_getScenarioList($id);
    $this->request->data['scenarioListForBranchOnCond'] = array_merge(array(
      0 => array(
        'TChatbotScenario' => array (
          'id' => 'self',
          'name' => 'このシナリオ'
        )
      )
    ),$this->request->data['scenarioList']);
    $this->set('storedVariableList', $this->getStoredAllVariableList($id));
    $this->request->data['leadList'] = $this->leadInfoSet();
    $this->request->data['chatbotDiagramList'] = $this->getChatbotDiagramSettingList();
    $this->_viewElement();
  }

  /* *
   * 削除(一覧ページから実行)
   * */
  public function remoteDelete(){
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $scenarioId = (isset($this->request->data['id'])) ? $this->request->data['id'] : "";
    $targetDeleteFileIds = (isset($this->request->data['targetDeleteFileIds'])) ? json_decode($this->request->data['targetDeleteFileIds']) : [];

    // 呼び出し設定されている場合は削除しない
    $scenarioList = $this->_findScenarioByActionType(C_SCENARIO_ACTION_CALL_SCENARIO);
    $scenarioList = array_merge($scenarioList, $this->_findScenarioByActionType(C_SCENARIO_ACTION_BRANCH_ON_CONDITION));
    $callerInfo = $this->_getScenarioCallerInfo($scenarioId, $scenarioList);
    if (!empty($callerInfo['TAutoMessage']) || !empty($callerInfo['TChatbotDiagram']) || !$this->isDeletableScenario($callerInfo['TChatbotScenario'])) {
      $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
      return;
    }

    $transactions = null;
    try {
      $transactions = $this->TransactionManager->begin();

      if (!$this->TChatbotScenario->logicalDelete($scenarioId)) {
        throw new ChatbotScenarioException('シナリオ削除エラー');
      }

      // 関連するテーブルからレコードを削除する
      // (メール送信設定は物理削除・論理削除共に行わない)
      $activity = json_decode($scenarioData['TChatbotScenario']['activity']);
      foreach ($activity->scenarios as $action) {

        if ($action->actionType == C_SCENARIO_ACTION_EXTERNAL_API) {
          // 外部システム連携
          if (!empty($action->tExternalApiConnectionId)) {
            $this->TExternalApiConnection->logicalDelete($action->tExternalApiConnectionId);
          }
        } else
        if ($action->actionType == C_SCENARIO_ACTION_SEND_FILE) {
          // ファイル送信
          if (!empty($action->tChatbotScenarioSendFileId)) {
            $targetDeleteFileIds[] = $action->tChatbotScenarioSendFileId;
          }
        }
      }

      // ファイル送信設定を削除する
      if (!empty($targetDeleteFileIds)) {
        $this->_deleteInvalidSendFileData($targetDeleteFileIds);
      }
      $this->_deleteInvalidLeadList();
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

  /* *
   * 削除(編集ページから実行)
   * */
  public function chkRemoteDelete(){
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';

    $selectedList = $this->request->data['selectedList'];
    $targetDeleteFileData = (isset($this->request->data['targetDeleteFileData'])) ? json_decode($this->request->data['targetDeleteFileData']) : [];

    // 呼び出し設定されているシナリオは削除対象から外す
    $targetList = [];
    $scenarioList = $this->_findScenarioByActionType(C_SCENARIO_ACTION_CALL_SCENARIO);
    $scenarioList = array_merge($scenarioList, $this->_findScenarioByActionType(C_SCENARIO_ACTION_BRANCH_ON_CONDITION));
    foreach ($selectedList as $scenarioId) {
      $callerInfo = $this->_getScenarioCallerInfo($scenarioId, $scenarioList);
      if (empty($callerInfo['TAutoMessage']) && $this->isDeletableScenario($callerInfo['TChatbotScenario'])) {
        $targetList[] = $scenarioId;
      }
    }

    $res = false;
    $deletedList = [];
    $transaction = null;
    try {
      $transactions = $this->TransactionManager->begin();

      foreach ($targetList as $scenarioId) {
        if ($this->TChatbotScenario->logicalDelete($scenarioId)) {
          $deletedList[] = $scenarioId;
          $targetDeleteFileIds = [];
          $res = true;

          // 関連するテーブルからレコードを削除する
          // (メール送信設定は物理削除・論理削除共に行わない)
          $scenarioData = $this->TChatbotScenario->findById($scenarioId);
          $activity = json_decode($scenarioData['TChatbotScenario']['activity']);
          foreach ($activity->scenarios as $action) {

            if ($action->actionType == C_SCENARIO_ACTION_EXTERNAL_API) {
              // 外部システム連携
              if (!empty($action->tExternalApiConnectionId)) {
                $this->TExternalApiConnection->logicalDelete($action->tExternalApiConnectionId);
              }
            } else
            if ($action->actionType == C_SCENARIO_ACTION_SEND_FILE) {
              // ファイル送信
              if (!empty($action->tChatbotScenarioSendFileId)) {
                $targetDeleteFileIds[] = $action->tChatbotScenarioSendFileId;
              }
            }
          }

          // ファイル送信設定を削除
          foreach ($targetDeleteFileData as $targetData) {
            if ($scenarioId == $targetData->id) {
              $targetDeleteFileIds = array_merge($targetDeleteFileIds, $targetData->targetDeleteFileIds);
            }
          }
          if (!empty($targetDeleteFileIds)) {
            $this->_deleteInvalidSendFileData($targetDeleteFileIds);
          }
        }
      }
      $this->_deleteInvalidLeadList();

      if($res){
        $this->TransactionManager->commitTransaction($transactions);
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
      }
      else {
        $this->TransactionManager->rollbackTransaction($transactions);
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
      }

      // 一時保存データを削除するため、実際に削除したシナリオIDを返す
      echo json_encode($deletedList);
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

  /* *
   * コピー処理
   * @return void
   * */
  public function remoteCopyEntryForm() {
    // ini_set('memory_limit', '-1');
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $selectedList = $this->request->data['selectedList'];
    // コピー元のシナリオリスト取得
    foreach($selectedList as $value){
      $copyData[] = $this->TChatbotScenario->read(null, $value);
    }
    $errorMessage = [];
    // コピー元のシナリオリストの数だけ繰り返し
    $res = true;
    foreach($copyData as $value){
      $this->TChatbotScenario->create();
      $saveData = [];

      // シナリオ設定の詳細
      try {
        $transaction = $this->TransactionManager->begin();
        $activity = json_decode($value['TChatbotScenario']['activity']);
        foreach ($activity->scenarios as $key => &$action) {
          if ($action->actionType == C_SCENARIO_ACTION_SEND_MAIL) {
            // メール送信設定のコピー
            $mailTransmissionData = $this->MMailTransmissionSetting->findById($action->mMailTransmissionId);
            if (!empty($mailTransmissionData)) {
              $this->MMailTransmissionSetting->create();
              $mailTransmissionData['MMailTransmissionSetting']['id'] = null;
              $this->MMailTransmissionSetting->set($mailTransmissionData);
              $result = $this->MMailTransmissionSetting->save();
              $action->mMailTransmissionId = $this->MMailTransmissionSetting->getLastInsertId();
            }
            // メールテンプレートのコピー
            $mailTemplateData = $this->MMailTemplate->findById($action->mMailTemplateId);
            if (!empty($mailTemplateData)) {
              $this->MMailTemplate->create();
              $mailTemplateData['MMailTemplate']['id'] = null;
              $this->MMailTemplate->set($mailTemplateData);
              $result = $this->MMailTemplate->save();
              $action->mMailTemplateId = $this->MMailTemplate->getLastInsertId();
            }
          } else
          if ($action->actionType == C_SCENARIO_ACTION_EXTERNAL_API) {
            // 外部システム連携設定のコピー
            $externalApiData = $this->TExternalApiConnection->findById($action->tExternalApiConnectionId);
            if (!empty($externalApiData)) {
              $this->TExternalApiConnection->create();
              $externalApiData['TExternalApiConnection']['id'] = null;
              $this->TExternalApiConnection->set($externalApiData);
              $result = $this->TExternalApiConnection->save();
              $action->tExternalApiConnectionId = $this->TExternalApiConnection->getLastInsertId();
            }
          } else
          if ($action->actionType == C_SCENARIO_ACTION_SEND_FILE) {
            // ファイル送信設定のコピー
            $sendFileData = $this->TChatbotScenarioSendFile->findById($action->tChatbotScenarioSendFileId);
            if (!empty($sendFileData)) {
              // S3上のファイルコピー
              $copyFile = $this->_copyFile($sendFileData['TChatbotScenarioSendFile']);

              $this->TChatbotScenarioSendFile->create();
              $sendFileData['TChatbotScenarioSendFile']['id'] = null;
              $sendFileData['TChatbotScenarioSendFile']['file_path'] = $copyFile['file_path'];
              $this->TChatbotScenarioSendFile->set($sendFileData);
              $this->TChatbotScenarioSendFile->save();

              // ダウンロードURLの生成
              $lastInsertedId = $this->TChatbotScenarioSendFile->getLastInsertId();
              $created = $this->TChatbotScenarioSendFile->field('created');
              $downloadUrl = $this->createDownloadUrl($created, $lastInsertedId, true);
              $this->TChatbotScenarioSendFile->set([
                'download_url' => $downloadUrl
              ]);
              $this->TChatbotScenarioSendFile->save();
              $action->tChatbotScenarioSendFileId = $lastInsertedId;
            }
          }
        }

        $saveData['TChatbotScenario']['sort'] = $this->_getNextSort();
        $saveData['TChatbotScenario']['m_companies_id'] = $value['TChatbotScenario']['m_companies_id'];
        $saveData['TChatbotScenario']['name'] = $value['TChatbotScenario']['name'].'コピー';
        $saveData['TChatbotScenario']['activity'] = json_encode($activity);
        $saveData['TChatbotScenario']['del_flg'] = $value['TChatbotScenario']['del_flg'];
        $this->TChatbotScenario->set($saveData);

        // バリデーションチェックでエラーが出た場合
        if (!$this->TChatbotScenario->validates()) {
          $res = false;
          $errorMessage = $this->TChatbotScenario->validationErrors;
          $this->TransactionManager->rollbackTransaction($transaction);
        } else
        if ($this->TChatbotScenario->save($saveData,false)) {
          $this->TransactionManager->commitTransaction($transaction);
          $this->Session->delete('dstoken');
          $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
        }
      } catch (Exception $e) {
        if ($transaction) {
          $this->TransactionManager->rollbackTransaction($transaction);
        }
        $this->log("Failed to copy scenario. => " . $e->getMessage, LOG_WARNING);
      }
    }
  }

  /**
   * シナリオ設定ソート順更新
   *
   * */
  public function remoteSaveSort(){
    Configure::write('debug', 2);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    if ( !$this->request->is('ajax') ) return false;
    if ( !empty($this->params->data['list']) ) {
      $this->TChatbotScenario->begin();
      $list = $this->params->data['list'];
      $sortNoList = $this->params->data['sortNolist'];
      sort($sortNoList);
      /* 現在の並び順を取得 */
      $params = $this->paginate['TChatbotScenario'];
      $params['fields'] = [
          'TChatbotScenario.id',
          'TChatbotScenario.sort'
      ];
      $params['conditions']['TChatbotScenario.m_companies_id'] = $this->userInfo['MCompany']['id'];
      unset($params['limit']);
      $prevSort = $this->TChatbotScenario->find('list', $params);
      // 新しくソート順を設定したため、空で来ることがある
      $reset_flg = false;
      foreach($prevSort as $key => $val){
        // 設定されていない値'0'が一つでも入っていたらsortをリセット
        if($val === '0' || $val === 0 || $val === null){
          $reset_flg = true;
        }
      }
      if($reset_flg){
        // ソート順が登録されていなかったらソート順をセットする
        if(! $this->_remoteSetSort()){
          $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
          return false;
        }
        $prevSort = $this->TChatbotScenario->find('list', $params);
        // この時$sortNoListは空なので作成する
        if(empty($sortNoList)){
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
        if ( isset($prevSort[$id]) ) {
          $saveData = [
              'TChatbotScenario' => [
                  'id' => $id,
                  'sort' => $sortNoList[$i]
              ]
          ];
          if (!$this->TChatbotScenario->validates()) {
            $ret = false;
            break;
          }
          if (!$this->TChatbotScenario->save($saveData)) {
            $ret = false;
            break;
          }
        } else {
          // 送信されたシナリオ設定と現在DBに存在するシナリオ設定に差がある場合
          $this->TChatbotScenario->rollback();
          $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.configChanged'));
          return;
        }
      }
      if ($ret) {
        $this->TChatbotScenario->commit();
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
      }
      else {
        $this->TChatbotScenario->rollback();
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
      }
    }
   }

  /**
   * 呼び出し先のシナリオ詳細の取得(アクション：シナリオ呼び出し)
   */
  public function remoteGetActionDetail() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $this->validatePostMethod();

    try {
      $id = (isset($this->request->data['id'])) ? $this->request->data['id']: '';
      $ret = $this->TChatbotScenario->find('first', [
        'fields' => ['id', 'activity'],
        'conditions' => [
          'TChatbotScenario.id' => $id,
          'TChatbotScenario.del_flg' => 0,
          'TChatbotScenario.m_companies_id' => $this->userInfo['MCompany']['id']
        ]
      ]);

      if (count($ret) === 1) {
        $this->_convertOldDataToNewStructure($ret['TChatbotScenario']['activity']);
        $this->_setActivityDetailSettings($ret['TChatbotScenario']['activity']);
        return json_encode($ret);
      } else {
        return false;
      }
    } catch (Exception $e) {
      return false;
    }
  }

  /**
   * ファイルアップロード(アクション：ファイル送信)
   */
  public function remoteUploadFile() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;

    $this->validatePostMethod();
    $saveData = $this->params['form'];

    try {
      // S3へファイルアップロード
      $saveData['file'] = $this->_uploadFile($saveData['file']);

      $this->TChatbotScenarioSendFile->begin();

      // ファイル情報は常にINSERTする(シナリオ保存・削除時に不要なファイル情報を削除する)
      $this->TChatbotScenarioSendFile->create();
      $this->TChatbotScenarioSendFile->set($saveData['file']);

      $this->TChatbotScenarioSendFile->validates();
      $errors = $this->TChatbotScenarioSendFile->validationErrors;
      if (!empty($errors)) {
        throw new ChatbotScenarioException('バリデーションエラー');
      }

      // ダウンロードURLの生成と送信ファイル設定の保存
      $this->TChatbotScenarioSendFile->save();
      $lastInsertedId = $this->TChatbotScenarioSendFile->getLastInsertId();
      $created = $this->TChatbotScenarioSendFile->field('created');
      $expire = date('Y-m-d H:i:s', strtotime('+10 year', strtotime($created)));
      $downloadUrl = $this->createDownloadUrl($expire, $lastInsertedId, true);
      $this->TChatbotScenarioSendFile->set([
        'download_url' => $downloadUrl
      ]);
      $this->TChatbotScenarioSendFile->save();
      $this->TChatbotScenarioSendFile->commit();

      $saveData['tChatbotScenarioSendFileId'] = $lastInsertedId;
      $saveData['file']['download_url'] = $downloadUrl;
      $saveData['file']['file_size'] = $this->prettyByte2Str($saveData['file']['file_size']);
      $saveData['file']['extension'] = $this->getExtension($saveData['file']['file_name']);
      unset($saveData['file']['file_path']);

      return json_encode([
        'success' => true,
        'save_data' => $saveData,
      ]);
    } catch (Exception $e) {
      $this->TChatbotScenarioSendFile->rollback();
      return false;
    }
  }

  public function remoteUploadCarouselImage()
  {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->validatePostMethod();
    $saveData = $this->params['form'];
    $url = $this->_uploadCarouseImage($saveData['file']);

    return json_encode([
      'success' => true,
      'url' => $url,
    ]);
  }

  /**
   * これまでのシナリオ設定を元に、登録可能なsort順を算出する
   * @return Integer sort順
   */
  private function _getNextSort() {
    try {
      $nextSort = 1;
      $params = [
        'fields' => [
          'TChatbotScenario.sort'
        ],
        'conditions' => [
          'TChatbotScenario.m_companies_id' => $this->userInfo['MCompany']['id']
        ],
        'order' => [
          'TChatbotScenario.sort' => 'desc',
          'TChatbotScenario.id' => 'desc'
        ],
        'limit' => 1,
        'recursive' => -1
      ];

      // ソートの最大値を取り出す
      $lastData = $this->TChatbotScenario->find('first', $params);

      // ソート順が取得できなかった場合、該当するシナリオ全体をソートし直す
      if (empty($lastData['TChatbotScenario']['sort'])) {
        if (!$this->_remoteSetSort()) {
          throw new Exception();
        }
        $lastData = $this->TChatbotScenario->find('first', $params);
      }

      if (!empty($lastData)) {
        $nextSort = intval($lastData['TChatbotScenario']['sort']) + 1;
      }
      return $nextSort;
    } catch(Exception $e) {
      $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
      throw $e;
    }
  }

   /**
   * シナリオ設定ソート順を現在のID順でセット
   * */
  private function _remoteSetSort(){
    try {
      // 現在の並び順を取得
      $scenarioList = $this->TChatbotScenario->find('all', [
        'fields' => [
          'TChatbotScenario.id',
          'TChatbotScenario.sort'
        ],
        'conditions' => [
          'TChatbotScenario.m_companies_id' => $this->userInfo['MCompany']['id']
        ],
        'order' => [
          'TChatbotScenario.sort' => 'asc',
          'TChatbotScenario.id' => 'asc'
        ],
        'recursive' => -1
      ]);

      // sort順を振り直す
      $count = 1;
      foreach($scenarioList as &$value){
        $value['TChatbotScenario']['sort'] = $count++;
      }

      return $this->TChatbotScenario->saveAll($scenarioList);
    } catch(Exeption $e) {
      throw $e;
    }
  }

  /**
   * 保存機能
   * @param array $saveData
   * @return void
   * */
  private function _entry($saveData) {
    $nextPage = '1';
      $transactions = null;
    try {
      $transactions = $this->TransactionManager->begin();
      $nextPage = $this->_entryProcess($saveData);
      $this->TransactionManager->commitTransaction($transactions);
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
      $this->redirect('/TChatbotScenario/index/page:'.$nextPage);
    } catch(ChatbotScenarioException $e) {
      $this->TransactionManager->rollbackTransaction($transactions);
      $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
      $this->set('errors', $e->getErrors());
      $this->set('lastPage', $e->getLastPage());
    } catch(Exception $e) {
      $this->TransactionManager->rollbackTransaction($transactions);
      $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
    }
  }

  /**
   * 保存機能
   * トランザクションはこのメソッドの呼び出し元で管理している。（TransactionManager）
   * @param Array $saveData
   * @return String
   * */
  private function _entryProcess($saveData) {
    $errors = [];
    $saveData['TChatbotScenario']['m_companies_id'] = $this->userInfo['MCompany']['id'];
    if(array_key_exists ('lastPage',$saveData)){
      $nextPage = $saveData['lastPage'];
    }
    else{
      $nextPage = '1';
    }

    if ( empty($saveData['TChatbotScenario']['id']) ) {
      //新規追加
      $this->TChatbotScenario->create();
      // 設定するsort順を算出する
      $saveData['TChatbotScenario']['sort'] = $this->_getNextSort();
    }

    // その他のチェック
    if ( !empty($saveData['TChatbotScenario']) ) {
      $activity = json_decode($saveData['TChatbotScenario']['activity']);
      foreach($activity->scenarios as &$action) {
        if ($action->actionType == C_SCENARIO_ACTION_SEND_MAIL) {
          // メール送信設定の保存と、IDの取得
          $action = $this->_entryProcessForSendMail($action);
        } else
        if ($action->actionType == C_SCENARIO_ACTION_CALL_SCENARIO) {
          // シナリオ呼び出し設定
          $action->tChatbotScenarioId = $action->scenarioId;
          unset($action->scenarioId);
        } else
        if ($action->actionType == C_SCENARIO_ACTION_EXTERNAL_API) {
          // 外部連携
          $action = $this->_entryProcessForExternalApi($action);
        } else
        if ($action->actionType == C_SCENARIO_ACTION_SEND_FILE) {
          // ファイル送信
          unset($action->file);
        } else
        if($action->actionType == C_SCENARIO_ACTION_LEAD_REGISTER) {
          // リード登録
          $action = $this->_entryProcessForLeadRegister($action);
        }
      }

      // 無効なファイル送信設定を削除する
      if (!empty($activity->targetDeleteFileIds)) {
        $this->_deleteInvalidSendFileData($activity->targetDeleteFileIds, $activity->scenarios);
      }
      unset($activity->targetDeleteFileIds);
    }

    $saveData['TChatbotScenario']['activity'] = json_encode($activity);
    //DB保存時、テーブルサイズを越えてしまうようであればエラーを吐かせる
    if(strlen($saveData['TChatbotScenario']['activity']) > 65535){

      $this->log('文字数超過エラー',LOG_DEBUG);
      $this->log($saveData['TChatbotScenario']['activity'],LOG_DEBUG);
      $exception = new ChatbotScenarioException('バリデーションエラー');
      $exception->setErrors($errors);
      $exception->setLastPage($nextPage);
      throw $exception;
    }
    $this->TChatbotScenario->set($saveData);

    $validate = $this->TChatbotScenario->validates();
    $errors = $this->TChatbotScenario->validationErrors;
    if ($validate) {
      if( $this->TChatbotScenario->save($saveData,false) ) {
      }
    }
    else {
      $exception = new ChatbotScenarioException('バリデーションエラー');
      $exception->setErrors($errors);
      $exception->setLastPage($nextPage);
      throw $exception;
    }

    $count = $this->TChatbotScenario->find('first',[
      'fields' => ['count(*) as count'],
      'conditions' => ['TChatbotScenario.del_flg != ' => 1, 'm_companies_id' => $this->userInfo['MCompany']['id']]
    ]);

    $this->_deleteInvalidLeadList();

    $page = floor((intval($count[0]['count']) + 99) / 100);
    return $page >= 1 ? $page : 1;
  }

  /* シナリオの全activity・保存済みリードリストのt_lead_list_settingsIdを取得
   * activityに無い && データが保存されていないリストはテーブルから存在を消す
   */

  private function _deleteInvalidLeadList(){
    $remainLeadListId = [];
    $remainLeadListId = array_merge($remainLeadListId,$this->_getCalledLeadLists());
    $remainLeadListId = array_merge($remainLeadListId,$this->_getSavedLeadLists());
    $remainLeadListId = array_unique($remainLeadListId);
    $this->_deleteNotExistLeadList($remainLeadListId);
  }

  private function _deleteNotExistLeadList($searchList){
    $idList = $this->TLeadListSetting->find('list', [
      'conditions' => [
        'm_companies_id' => $this->userInfo['MCompany']['id']
      ]
    ]);
    foreach($idList as $targetId){
      if(!in_array($targetId, $searchList)){
        $this->TLeadListSetting->delete((int)$targetId);
      }
    }
  }


  private function _getCalledLeadLists(){
    $calledLeadListId = [];
    $allScenarioActivity = $this->TChatbotScenario->find('list', [
      'conditions' => [
        'm_companies_id' => $this->userInfo['MCompany']['id'],
        'del_flg' => 0
      ],
      'fields' => 'activity'
    ]);

    foreach ($allScenarioActivity as $scenarioActivity) {
      $scenarioData = json_decode($scenarioActivity)->scenarios;
      foreach ($scenarioData as $scenarioAction) {
        if ($scenarioAction->actionType == C_SCENARIO_ACTION_LEAD_REGISTER) {
          $calledLeadListId[] = $scenarioAction->tLeadListSettingId;
        }
      }
    }
    return $calledLeadListId;
  }

  private function _getSavedLeadLists(){
    $allLeadList= $this->TLeadList->find('list', [
      'conditions' => [
        'm_companies_id' => $this->userInfo['MCompany']['id']
      ],
      'fields' => 't_lead_list_settings_id',
      'group' => 't_lead_list_settings_id'
    ]);
    return $allLeadList;
  }

  /**
   * ハッシュ値を作るプロセス
   * @param hasSalt 項目名
   * return ハッシュ値(8桁)
   */
  private function _makeHashProcess($hashSalt){
    return hash("fnv132", (string)microtime().$hashSalt);
  }

  /*
   *
   */
  private function _grantDeletedFlg($currentData, $labelArray){
    $resultArray = $labelArray;
    foreach($currentData as $oldLabelData){
      $deleted = false;
      foreach($labelArray as $newLabelData){
        if(strcmp($newLabelData['leadUniqueHash'], $oldLabelData->leadUniqueHash) == 0){
          $deleted = false;
          break;
        } else {
          $deleted = true;
        }
      }
      if($deleted){
        $oldLabelData->deleted = 1;
        $resultArray[] = json_decode(json_encode($oldLabelData),true);
      }
    }
    return $resultArray;
  }

  private function _getSameNameHash($currentLabelArray, $result){
    $uniqueKey = "";
    $currentLabelArray = json_decode($currentLabelArray);
    foreach($currentLabelArray as $searchTarget){
      if(strcmp($searchTarget->leadLabelName, $result->leadLabelName) == 0){
        $uniqueKey = $searchTarget->leadUniqueHash;
      }
    }
    return $uniqueKey;
  }


  /**
   * リードリスト保存時、該当IDのリスト設定内に同名の項目が存在するか
   *
   *
   * */
  private function _makeLeadDataProcess($saveData){
    $labelArray = [];
    $valueArray = [];
    $targetId = $saveData->tLeadListSettingId;
    if(!empty($targetId)) {
      $currentLabelArray = $this->TLeadListSetting->find('list', [
        'recursive' => -1,
        'fields' => [
          'list_parameter'
        ],
        'conditions' => [
          'id' => $targetId
        ]
      ]);
    }
    foreach($saveData->leadInformations as $key => $result) {
      if(empty($result->leadUniqueHash)) {
        $uniqueKey = empty($targetId) ? "" : $this->_getSameNameHash($currentLabelArray[$targetId], $result);
        $result->leadUniqueHash = $uniqueKey == "" ? $this->_makeHashProcess($result->leadLabelName) : $uniqueKey;
      }
      $labelArray[] = ['leadUniqueHash' => $result->leadUniqueHash , 'leadLabelName' => $result->leadLabelName , 'deleted' => 0];
      $valueArray[] = ['leadUniqueHash' => $result->leadUniqueHash , 'leadVariableName' => $result->leadVariableName];
    }
    // 現在保存しようとしている対象のデータを取得
    if (!empty($currentLabelArray)) {
      $labelArray = $this->_grantDeletedFlg(json_decode($currentLabelArray[$targetId]), $labelArray);
    }
    // t_lead_list_settingsにはここで入れる
    $this->TLeadListSetting->set([
      'm_companies_id' => $this->userInfo['MCompany']['id'],
      'list_name' => $saveData->leadTitleLabel,
      'list_parameter' => json_encode($labelArray)
    ]);
    // t_chatbot_scenariosに入れる情報だけreturnする
    return $valueArray;
  }

  /**
   * リードリスト保存機能（ここでt_lead_list_settingsテーブルに情報が保存され、設定idとハッシュ値をアクション詳細に返却する
   * @param Object $saveData アクション詳細
   * @return Object          t_chatbot_scenarioに保存するアクション詳細
   * */
  private function _entryProcessForLeadRegister($saveData)
  {
    if (empty($saveData->tLeadListSettingId) || $saveData->makeLeadTypeList == 1) {
      $this->TLeadListSetting->create();
    } else {
      $this->TLeadListSetting->read(null, $saveData->tLeadListSettingId);
    }
    $saveData->leadInformations = $this->_makeLeadDataProcess($saveData);

    $errors = $this->TLeadListSetting->validationErrors;
    if (empty($errors)) {
      $this->TLeadListSetting->save();
      //IDが無い場合
      if (empty($saveData->tLeadListSettingId) || $saveData->makeLeadTypeList == 1) {
        $saveData->tLeadListSettingId = $this->TLeadListSetting->getLastInsertId();
      }
    } else {
      $exception = new ChatbotScenarioException('バリデーションエラー');
      $exception->setErrors($errors);
      throw $exception;
    }
    // リード登録設定DBに保存した情報をオブジェクトから削除する
    unset($saveData->leadTitleLabel);
    unset($saveData->makeLeadTypeList);
    return $saveData;
  }

  /**
   * メール送信設定の保存機能（トランザクションはこのメソッドの先祖で管理している）
   * @param Object $saveData アクション詳細
   * @return Object          t_chatbot_scenarioに保存するアクション詳細
   * */
  private function _entryProcessForSendMail($saveData) {
    // 送信先メールアドレス情報の値を、保存可能な形式に変換する
    $toAddresses = '';
    if(count($saveData->toAddress)) {
      foreach($saveData->toAddress as $address) {
        if (!empty($address)) {
          if ($toAddresses !== '') {
            $toAddresses .= ',';
          }
          $toAddresses .= $address;
        }
      }
    }

    // メール送信設定の保存
    if(empty($saveData->mMailTransmissionId)) {
      $this->MMailTransmissionSetting->create();
    } else {
      $this->MMailTransmissionSetting->read(null, $saveData->mMailTransmissionId);
    }
    $this->MMailTransmissionSetting->set([
      'm_companies_id' => $this->userInfo['MCompany']['id'],
      'from_address' => 'test@example.com',
      'from_name' => $saveData->fromName,
      'to_address' => $toAddresses,
      'subject' => $saveData->subject
    ]);
    $validate = $this->MMailTransmissionSetting->validates();
    $errors = $this->MMailTransmissionSetting->validationErrors;
    if(empty($errors)){
      $this->MMailTransmissionSetting->save();
      if(empty($saveData->mMailTransmissionId)) {
        $saveData->mMailTransmissionId = $this->MMailTransmissionSetting->getLastInsertId();
      }
    } else {
      $exception = new ChatbotScenarioException('バリデーションエラー');
      $exception->setErrors($errors);
      $exception->setLastPage($nextPage);
      throw $exception;
    }

    // メールテンプレートの設定
    $template = '';
    if ($saveData->mailType == C_SCENARIO_MAIL_TYPE_CUSTOMIZE) {
      // メール本文をカスタマイズする
      $template = $saveData->template;
    } else
    if ($saveData->mailType == C_SCENARIO_MAIL_TYPE_VARIABLES) {
      // 変数の値のみメールする
      $template = self::SCENARIO_VARIABLES_TEMPLATE;
    } else {
      // メール内容をすべてメールする
      $template = self::SCENARIO_ALL_MESSAGE_TEMPLATE;
    }

    // メールテンプレート設定の保存
    if(empty($saveData->mMailTemplateId)) {
      $this->MMailTemplate->create();
    } else {
      $this->MMailTemplate->read(null, $saveData->mMailTemplateId);
    }
    $this->MMailTemplate->set([
      'm_companies_id' => $this->userInfo['MCompany']['id'],
      'mail_type_cd' => 'CS001',
      'template' => $template
    ]);
    $validate = $this->MMailTemplate->validates();
    $errors = $this->MMailTemplate->validationErrors;
    if(empty($errors)) {
      $this->MMailTemplate->save();
      if(empty($saveData->mMailTemplateId)) {
        $saveData->mMailTemplateId = $this->MMailTemplate->getLastInsertId();
      }
    } else {
      $exception = new ChatbotScenarioException('バリデーションエラー');
      $exception->setErrors($errors);
      $exception->setLastPage($nextPage);
      throw $exception;
    }

    // 保存済みの設定をオブジェクトから削除する
    unset($saveData->fromName);
    unset($saveData->toAddress);
    unset($saveData->subject);
    unset($saveData->template);

    return $saveData;
  }

  /**
   * 外部システム連携の保存機能（トランザクションはこのメソッドの先祖で管理している）
   * @param  Object $saveData アクション詳細
   * @return Object           t_chatbot_scenarioに保存するアクション詳細
   */
  private function _entryProcessForExternalApi($saveData) {
    if($saveData->externalType == C_SCENARIO_EXTERNAL_TYPE_API){
      //連携タイプがAPI連携の場合
      if (empty($saveData->tExternalApiConnectionId)) {
        $this->TExternalApiConnection->create();
      } else {
        $this->TExternalApiConnection->read(null, $saveData->tExternalApiConnectionId);
      }
      $this->TExternalApiConnection->set([
        'm_companies_id' => $this->userInfo['MCompany']['id'],
        'url' => $saveData->url,
        'method_type' => $saveData->methodType,
        'request_headers' => json_encode($saveData->requestHeaders),
        'request_body' => $saveData->requestBody,
        'responseType' => $saveData->responseType,
        'response_body_maps' => json_encode($saveData->responseBodyMaps)
      ]);

      $validate = $this->TExternalApiConnection->validates();
      $errors = $this->TExternalApiConnection->validationErrors;
      if(empty($errors)){
        $this->TExternalApiConnection->save();
        if(empty($saveData->tExternalApiConnectionId)) {
          $saveData->tExternalApiConnectionId = $this->TExternalApiConnection->getLastInsertId();
        }
      } else {
        $exception = new ChatbotScenarioException('バリデーションエラー');
        $exception->setErrors($errors);
        $exception->setLastPage($nextPage);
        throw $exception;
      }
    //スクリプト連携に関する設定をオブジェクトから削除する
    unset($saveData->externalScript);
    } else
    if($saveData->externalType == C_SCENARIO_EXTERNAL_TYPE_SCRIPT){
    //連携タイプがスクリプトの場合
      $scriptPattern = '/<(.*script.*)>/';
      if(preg_match($scriptPattern,$saveData->externalScript)){
        $exception = new ChatbotScenarioException('バリデーションエラー');
        $exception->setErrors($errors);
        $exception->setLastPage($nextPage);
        throw $exception;
      }
    }
    // API連携に関する設定をオブジェクトから削除する(共通)
    // スクリプト連携の場合は不要、かつAPI連携の場合も別テーブルに保存される領域の為
    unset($saveData->url);
    unset($saveData->methodType);
    unset($saveData->requestHeaders);
    unset($saveData->requestBody);
    unset($saveData->responseType);
    unset($saveData->responseBodyMaps);
    return $saveData;
  }

  /**
   * ファイル送信設定の、画面上で削除された無効なデータを論理削除する
   * @param  Array $targetDeleteFileIds 削除対象のファイルID一覧
   * @param  Array $scenarios           有効なシナリオ設定
   * @return Void
   */
  private function _deleteInvalidSendFileData($targetDeleteFileIds, $scenarios = []) {
    // 有効なファイルIDを抽出する
    $validIds = [];
    foreach ($scenarios as $action) {
      if ($action->actionType == C_SCENARIO_ACTION_SEND_FILE ) {
        $validIds[] = $action->tChatbotScenarioSendFileId;
      }
    }

    // リストから、有効なファイルIDを除外する
    $targetIds = [];
    foreach (array_unique($targetDeleteFileIds) as $fileId) {
      if (!empty($fileId) && array_search($fileId, $validIds) === FALSE) {
        $targetIds[] = $fileId;
      };
    }

    // 無効なファイル情報を削除する
    $targetList = $this->TChatbotScenarioSendFile->find('all', [
      'fields' => ['id', 'file_path'],
      'conditions' => [
        'TChatbotScenarioSendFile.id' => $targetIds,
        'TChatbotScenarioSendFile.del_flg' => 0
      ],
      'recursive' => -1
    ]);
    foreach ($targetList as $file) {
      $this->TChatbotScenarioSendFile->logicalDelete($file['TChatbotScenarioSendFile']['id']);
      $this->_removeFile($file['TChatbotScenarioSendFile']['file_path']);
    }
  }

  /**
   * ビュー部品セット
   * @return void
   * */
  private function _viewElement() {
    // アクション種別
    $this->set('chatbotScenarioActionList', $this->chatbotScenarioActionList);
    // 入力タイプ種別
    $this->set('chatbotScenarioInputType', $this->chatbotScenarioInputType);
    // 属性タイプ種別
    $this->set('chatbotScenarioAttributeType', $this->chatbotScenarioAttributeType);
    // メール送信タイプ種別
    $this->set('chatbotScenarioSendMailType', $this->chatbotScenarioSendMailType);
    // 外部連携種別
    $this->set('chatbotScenarioExternalType', $this->chatbotScenarioExternalType);
    // API通信メソッド種別
    $this->set('chatbotScenarioApiMethodType', $this->chatbotScenarioApiMethodType);
    // API通信レスポンス種別
    $this->set('chatbotScenarioApiResponseType', $this->chatbotScenarioApiResponseType);
    // ファイル受信ファイル形式種別
    $this->set('chatbotScenarioReceiveFileTypeList', $this->chatbotScenarioReceiveFileTypeList);
    // 条件分岐変数値マッチ条件
    $this->set('chatbotScenarioBranchOnConditionMatchValueType', $this->chatbotScenarioBranchOnConditionMatchValueType);
    // 条件分岐アクション種別
    $this->set('chatbotScenarioBranchOnConditionActionType', $this->chatbotScenarioBranchOnConditionActionType);
    // 条件分岐アクション種別（上記を満たさない場合）
    $this->set('chatbotScenarioBranchOnConditionElseActionType', $this->chatbotScenarioBranchOnConditionElseActionType);
    // リード登録リードリスト名種別
    $this->set('chatbotScenarioLeadTypeList', $this->chatbotScenarioLeadTypeList);
    // ファイル受信用にcompany_keyをsetしておく
    $this->set('companyKey', $this->userInfo['MCompany']['company_key']);
    // 最後に表示していたページ番号
    if(!empty($this->request->query['lastpage'])){
      $this->set('lastPage', $this->request->query['lastpage']);
    }
    $chatbotScenarioAddCustomerInformationList = $this->TCustomerInformationSetting->find('all', array(
      'conditions' => array(
        'm_companies_id' => $this->userInfo['MCompany']['id'],
        'delete_flg' => 0
      ),
      'order' => array(
        'sort' => 'asc'
      )
    ));
    $this->set('chatbotScenarioAddCustomerInformationList', $chatbotScenarioAddCustomerInformationList);
  }

  /**
   * 呼び出し元情報を取得する
   * @param  Int    $id           シナリオID
   * @param  Array  $scenarioList アクション「シナリオ呼び出し」を含むシナリオ一覧
   * @return Array                呼び出し元情報
   */
  private function _getScenarioCallerInfo($id, $scenarioList)
  {
    $callerInfo = [];

    // 呼び出し元オートメッセージ情報を取得する
    $callerInfo['TAutoMessage'] = $this->TAutoMessage->coFind('list', [
      'fileds'     => ['DISTINCT id', 'name'],
      'order'      => [
        'TAutoMessage.sort' => 'asc',
        'TAutoMessage.id'   => 'asc'
      ],
      'conditions' => [
        'TAutoMessage.del_flg != '           => 1,
        'TAutoMessage.t_chatbot_scenario_id' => $id,
        'TAutoMessage.m_companies_id'        => $this->userInfo['MCompany']['id']
      ]
    ]);

    $callerInfo['TChatbotDiagram'] = $this->TChatbotDiagram->coFind('list', array(
      'fields'     => array('id', 'name'),
      'order'      => array(
        'TChatbotDiagram.sort' => 'asc',
        'TChatbotDiagram.id'   => 'asc'
      ),
      'conditions' => array(
        'TChatbotDiagram.del_flg'       => 0,
        'TChatbotDiagram.activity LIKE' => "%\"scenarioId\":\"" . $id . "\"%"
      )
    ));

    // 呼び出し元シナリオ情報を取得する
    $matchScenarioNames         = [];
    $keyword                    = '"tChatbotScenarioId":"' . $id . '"';
    $branchOnCondKeyword        = '"callScenarioId":"' . $id . '"';
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
    $callerInfo['TChatbotScenario'] = array_unique($matchScenarioNames);

    return $callerInfo;
  }

  /**
   * 指定されたアクションタイプのシナリオ一覧を返す
   * @param Integer $actionType アクションタイプ
   * @return Array              シナリオ一覧
   */
  private function _findScenarioByActionType($actionType) {
    $scenarioList = $this->TChatbotScenario->coFind('all', [
      'fileds' => ['id', 'name'],
      'order' => [
        'TChatbotScenario.sort' => 'asc',
        'TChatbotScenario.id' => 'asc'
      ],
      'conditions' => [
        'TChatbotScenario.del_flg != ' => 1,
        'TChatbotScenario.m_companies_id' => $this->userInfo['MCompany']['id']
      ],
      'recursive' => -1
    ]);

    // 指定されたアクションタイプのシナリオのみを抽出する
    $filteredScenarioList = [];
    $keyword = '"actionType":"'. $actionType . '"';
    foreach ($scenarioList as $scenario) {
      if (strpos($scenario['TChatbotScenario']['activity'], $keyword)) {
        $filteredScenarioList[] = $scenario;
      }
    }

    return $filteredScenarioList;
  }

  /**
   * アクション「シナリオ呼び出し」に表示する、idとnameの一覧を返す
   * @param  Integer $currentId 現在表示中のシナリオID（結果のリストから除外する）
   * @return Array              シナリオ一覧
   */
  private function _getScenarioList($currentId=null) {
    return $this->TChatbotScenario->coFind('all', [
      'fields' => ['TChatbotScenario.id', 'TChatbotScenario.name'],
      'order' => [
        'TChatbotScenario.sort' => 'asc',
        'TChatbotScenario.id' => 'asc'
      ],
      'conditions' => [
        'TChatbotScenario.id !=' => $currentId,
        'TChatbotScenario.del_flg != ' => 1
      ]
    ]);
  }

  /**
   * convert old data (inputType, selection action type) to new hearing structure
   * @param $json
   */
  private function _convertOldDataToNewStructure(&$json)
  {
    $activity = json_decode($json);
    foreach ($activity->scenarios as $key => &$action) {
      // add restore
      if (!isset($action->restore)) {
        $action->restore = false;
      }

      if ($action->actionType == C_SCENARIO_ACTION_HEARING) {
        foreach ($action->hearings as $key => &$param) {
          // convert break line to uiType
          if (!isset($param->uiType) && isset($param->inputLFType) && isset($param->inputType)) {
            $param = $this->convertHearingTextType($param, $action->errorMessage);
          }
        }
      } else if ($action->actionType == C_SCENARIO_ACTION_SELECT_OPTION) {
        $action = (object)$this->convertSelectionToHearing($action);
      }
    }

    $json = json_encode($activity);
  }

  /**
   * convert selection to hearing
   * @param $data
   * @return mixed
   */
  private function convertSelectionToHearing($data)
  {
    $action = $this->chatbotScenarioActionList[C_SCENARIO_ACTION_HEARING]['default'];
    $action['actionType'] = '2';
    $action['restore'] = false;
    $action['hearings'][0] = $this->convertHearingRadioButton($data);

    return $action;
  }

  /**
   * convert selection data to hearing radio button type data
   * @param $data
   * @return mixed
   */
  private function convertHearingRadioButton($data)
  {
    $widget = $this->_getWidgetSettings();
    $radio = $this->chatbotScenarioActionList[C_SCENARIO_ACTION_HEARING]['default']['hearings'][0];
    $radio['variableName'] = $data->selection->variableName;
    $radio['message'] = $data->message;
    $radio['uiType'] = '3'; // radio button type
    $radio['settings']['radioStyle'] = '2';
    $radio['settings']['radioCustomDesign'] = true;
    $radio['settings']['customDesign']['radioBorderColor'] = '#999';
    $radio['settings']['customDesign']['radioActiveColor'] = $widget['main_color'];
    $radio['settings']['customDesign']['radioEntireActiveColor'] = $widget['main_color'];
    $radio['settings']['customDesign']['radioEntireBackgroundColor'] = $widget['main_color'];
    $radio['settings']['customDesign']['radioTextColor'] = $widget['re_text_color'];
    $radio['settings']['customDesign']['radioActiveTextColor'] = $widget['re_text_color'];
    $radio['settings']['customDesign']['radioBackgroundColor'] = '#FFFFFF';

    $radio["settings"]["options"] = [];
    foreach ($data->selection->options as $option) {
      array_push($radio["settings"]["options"], $option);
    }

    return $radio;
  }

  /**
   * convert inputType to uiType
   * @param $data
   * @param $errorMessage
   * @return mixed
   */
  private function convertHearingTextType($data, $errorMessage)
  {
    $hearing = $this->chatbotScenarioActionList[C_SCENARIO_ACTION_HEARING]['default']['hearings'][0];
    $hearing['variableName'] = $data->variableName;
    $hearing['message'] = $data->message;
    // 改行可 -> テキスト複数行
    if ($data->inputLFType === '2') {
      $hearing['uiType'] = '2';
      // old input type is email or tel-> convert to text
      $hearing['inputType'] = $data->inputType == 2 ? '2' : '1';
    }
    // 改行不可 -> テキスト一行
    if ($data->inputLFType === '1') {
      $hearing['uiType'] = '1';
      $hearing['inputType'] = $data->inputType;
    }

    // convert error message if inputType is not text
    if ($hearing['inputType'] !== '1') {
      $hearing['errorMessage'] = $errorMessage;
    }

    return $hearing;
  }

      /**
   * アクションごとに必要な設定を追加する
   * @param Object $json activity
   */
  private function _setActivityDetailSettings(&$json) {
    $activity = json_decode($json);
    foreach ($activity->scenarios as $key => &$action) {
      if ($action->actionType == C_SCENARIO_ACTION_HEARING) {
        foreach ($action->hearings as $key => &$param) {
          // 自由入力エリアの改行設定の初期化(機能追加前に保存された設定は、改行可とする)
          $param->inputLFType = empty($param->inputLFType) ? C_SCENARIO_INPUT_LF_TYPE_ALLOW : $param->inputLFType;
        }
      } else
      if ($action->actionType == C_SCENARIO_ACTION_SEND_MAIL) {
        // メール送信設定の取得
        if (!empty($action->mMailTransmissionId)) {
          $mailTransmissionData = $this->MMailTransmissionSetting->findById($action->mMailTransmissionId);
          $action->toAddress = explode(',', $mailTransmissionData['MMailTransmissionSetting']['to_address']);
          $action->subject = $mailTransmissionData['MMailTransmissionSetting']['subject'];
          $action->fromName = $mailTransmissionData['MMailTransmissionSetting']['from_name'];
        }
        // メールテンプレートの取得
        if (!empty($action->mMailTemplateId)) {
          $mailTemplateData = $this->MMailTemplate->findById($action->mMailTemplateId);
          if ($action->mailType == C_SCENARIO_MAIL_TYPE_CUSTOMIZE) {
            $action->template = $mailTemplateData['MMailTemplate']['template'];
          }
        }
        if(!empty($action->mailTemplate)){
          unset($action->mailTemplate);
        }

      } else
      if ($action->actionType == C_SCENARIO_ACTION_CALL_SCENARIO) {
        // シナリオ呼び出し設定
        if (!empty($action->tChatbotScenarioId)) {
          $action->scenarioId = $action->tChatbotScenarioId;
        }
      } else
      if ($action->actionType == C_SCENARIO_ACTION_EXTERNAL_API) {
        // 外部システム連携設定
        if (!empty($action->tExternalApiConnectionId)) {
          $externalApiData = $this->TExternalApiConnection->findById($action->tExternalApiConnectionId);
          if(!isset($action->externalType)){
            $action->externalType = C_SCENARIO_EXTERNAL_TYPE_API;  //デフォルト値
          }
          $action->url = $externalApiData['TExternalApiConnection']['url'];
          $action->methodType = $externalApiData['TExternalApiConnection']['method_type'];
          $action->requestHeaders = json_decode($externalApiData['TExternalApiConnection']['request_headers']);
          $action->requestBody = $externalApiData['TExternalApiConnection']['request_body'];
          $action->responseType = $externalApiData['TExternalApiConnection']['response_type'];
          $action->responseBodyMaps = json_decode($externalApiData['TExternalApiConnection']['response_body_maps']);
        }
      } else
      if ($action->actionType == C_SCENARIO_ACTION_SEND_FILE) {
        // ファイル送信
        if (!empty($action->tChatbotScenarioSendFileId)) {
          $fileData = $this->TChatbotScenarioSendFile->findById($action->tChatbotScenarioSendFileId);
          $action->file = [
            'download_url' => $fileData['TChatbotScenarioSendFile']['download_url'],
            'file_name' => $fileData['TChatbotScenarioSendFile']['file_name'],
            'file_size' => $this->prettyByte2Str($fileData['TChatbotScenarioSendFile']['file_size']),
            'extension' => $this->getExtension($fileData['TChatbotScenarioSendFile']['file_name'])
          ];
        }
      } else
      if ($action->actionType == C_SCENARIO_ACTION_LEAD_REGISTER) {
        if (!empty($action->tLeadListSettingId)) {
          $action->makeLeadTypeList = C_SCENARIO_LEAD_USE;
          if(!isset($action->makeLeadTypeList)){
            $action->makeLeadTypeList = C_SCENARIO_LEAD_REGIST;
          }
          $leadData = $this->TLeadListSetting->findById($action->tLeadListSettingId);
          $action->leadTitleLabel = $leadData['TLeadListSetting']['list_name'];
          $action->leadInformations = $this->convertLeadDataForView($action->leadInformations, json_decode($leadData['TLeadListSetting']['list_parameter']));
        }
      }
    }
    $json = json_encode($activity);
  }

  private function convertLeadDataForView($variables, $labels){
    $resultArray = [];

    foreach($labels as $key => $value){
      if($value->deleted == 0) {
        $varialbleName = "";
        foreach ($variables as $variable) {
          // ユニークキーが合致する情報を変数とラベル名から探す
          if (strcmp($value->leadUniqueHash, $variable->leadUniqueHash) == 0) {
            $varialbleName = $variable->leadVariableName;
          }
        }
        $resultArray[] = ['leadUniqueHash' => $value->leadUniqueHash,
          'leadLabelName' => $value->leadLabelName,
          'leadVariableName' => $varialbleName];
      }
    }

    return $resultArray;
  }

  private function leadInfoSet(){
    $targetList = $this->TLeadListSetting->find('all',[
      'recursive' => -1,
      'fields' => [
        "id",
        "list_name",
        "list_parameter"
      ],
      'conditions' => [
        "m_companies_id" => $this->userInfo['MCompany']['id'],
      ]
    ]);

    foreach($targetList as $currentId => $target){
      $labelList = json_decode($target['TLeadListSetting']['list_parameter']);
      foreach($labelList as $key => $labelData){
        if(property_exists($labelData, "deleted")) {
          if ($labelData->deleted == 1) {
            array_splice($labelList, $key, 1);
          }
        } else {
          $labelData->deleted = 0;
        }
      }
      $targetList[$currentId]['TLeadListSetting']['list_parameter'] = json_encode($labelList);
    }
    return $targetList;
  }

  private function getChatbotDiagramSettingList() {
    $data = $this->TChatbotDiagram->find('list', array(
      'field' => array('id', 'name'),
      'conditions' => array(
        'm_companies_id' => $this->userInfo['MCompany']['id'],
        'del_flg' => '0'
      )
    ));
    return $data;
  }

  /**
   * ファイルアップロード
   * @param  Object ファイル情報
   * @return Object アップロードしたファイル情報
   */
  private function _uploadFile($file) {
    $saveFileName = $this->getFilenameForSave($file);
    $filePath = $this->putFile($file, $saveFileName);

    return [
      'm_companies_id' => $this->userInfo['MCompany']['id'],
      'file_path' => $filePath,
      'file_name' => $file['name'],
      'file_size' => $file['size']
    ];
  }

  private function _uploadCarouseImage($file) {
    $saveFileName = $this->generateImageName($file);
    $filePath = $this->putImage($file, $saveFileName);

    return $filePath;
  }

  /**
   * ファイル削除
   * @param  String $file ファイルパス
   * @return Void
   */
  private function _removeFile($filePath) {
    $pos = strpos($filePath, $this->fileTransferPrefix);
    if ($pos !== FALSE) {
      $this->removeFile(substr($filePath, $pos));
    }
  }

  /**
   * ファイルのコピー(get/put)
   * @param  Object $file コピー元のファイル情報
   * @return Object       ファイル情報
   */
  private function _copyFile($file) {
    $pos = strpos($file['file_path'], $this->fileTransferPrefix);
    if ($pos === FALSE) {
      return [];
    }

    $saveFileName = $this->getFilenameForSave(['name' => $file['file_name']]);
    $fileData = $this->getFile(substr($file['file_path'], $pos));
    $fileObj = array();

    $tmpFile = new File('/tmp/'.$file['file_name']);
    $tmpFile->write($fileData['Body'], 'w');
    $tmpFile->close();

    $fileObj['tmp_name'] = '/tmp/'.$file['file_name'];
    $filePath = $this->putFile($fileObj, $saveFileName);

    return [
      'file_path' => $filePath,
      'file_name' => $file['file_name'],
      'file_size' => $this->prettyByte2Str($file['file_size']),
      'extension' => $this->getExtension($file['file_name'])
    ];
  }

  private function isDeletableScenario($scenarioCallerInfo) {
    return empty($scenarioCallerInfo) || (count($scenarioCallerInfo) === 1 && strcmp($scenarioCallerInfo[0], self::CALL_SELF_SCENARIO_NAME) === 0);
  }

  private function getStoredAllVariableList($ignoreId = 0) {
    $variableList = array();
    $scenarioList = $this->TChatbotScenario->find('all',array(
      'conditions' => array(
        'AND' => array(
          'm_companies_id' => $this->userInfo['MCompany']['id'],
          'del_flg' => 0
        ),
        'NOT' => array(
          'id' => $ignoreId
        )
      ),
      'order' => array(
        'sort' => 'asc'
      )
    ));
    foreach($scenarioList as $key => $scenario) {
      $activity = json_decode($scenario['TChatbotScenario']['activity'], TRUE);
      foreach($activity['scenarios'] as $sequenceNum => $action) {
        if($this->hasVariableInAction($action)) {
          $variableList = array_merge($variableList, $this->getVariableListInAction($action));
          $variableList = array_unique($variableList);
          $variableList = array_diff($variableList, array(''));
          $variableList = array_values($variableList);
        }
      }
    }
    return $variableList;
  }

  private function hasVariableInAction($action) {
    return strcmp($action['actionType'], C_SCENARIO_ACTION_HEARING) === 0
      || strcmp($action['actionType'], C_SCENARIO_ACTION_SELECT_OPTION) === 0
      || strcmp($action['actionType'], C_SCENARIO_ACTION_EXTERNAL_API) === 0
      || strcmp($action['actionType'], C_SCENARIO_ACTION_GET_ATTRIBUTE) === 0
      || strcmp($action['actionType'], C_SCENARIO_ACTION_BULK_HEARING) === 0
      || strcmp($action['actionType'], C_SCENARIO_ACTION_CONTROL_VARIABLE) === 0;
  }

  private function getVariableListInAction($action) {
    $arr = array();
    switch($action['actionType']) {
      case C_SCENARIO_ACTION_HEARING:
        foreach($action['hearings'] as $index => $hearing) {
          array_push($arr, $hearing['variableName']);
        }
        break;
      case C_SCENARIO_ACTION_SELECT_OPTION:
        array_push($arr, $action['selection']['variableName']);
        break;
      case C_SCENARIO_ACTION_GET_ATTRIBUTE:
        foreach($action['getAttributes'] as $index => $getAttribute) {
          array_push($arr, $getAttribute['variableName']);
        }
        break;
      case C_SCENARIO_ACTION_BULK_HEARING:
        foreach($action['multipleHearings'] as $index => $multipleHearing) {
          array_push($arr, $multipleHearing['variableName']);
        }
        break;
      case C_SCENARIO_ACTION_CONTROL_VARIABLE:
        foreach($action['calcRules'] as $index => $calcRule) {
          array_push($arr, $calcRule['variableName']);
        }
        break;
    }
    return $arr;
  }
}
