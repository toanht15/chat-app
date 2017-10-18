<?php
/**
 * TAutoMessagesController controller.
 * ユーザーマスタ
 */
class TAutoMessagesController extends AppController {
  public $uses = ['TAutoMessage'];
  public $helpers = ['AutoMessage'];
  public $paginate = [
    'TAutoMessage' => [
      'limit' => 10,
      'order' => ['TAutoMessage.id' => 'asc'],
      'fields' => ['TAutoMessage.*'],
      'conditions' => ['TAutoMessage.del_flg != ' => 1],
      'recursive' => -1
    ]
  ];
  public $outMessageIfType;
  public $outMessageTriggerList;

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', 'オートメッセージ機能');
    $this->outMessageIfType = Configure::read('outMessageIfType');
    $this->outMessageTriggerList = Configure::read('outMessageTriggerList');
  }

  /**
   * 一覧画面
   * @return void
   * */
  public function index() {
    $this->paginate['TAutoMessage']['conditions']['TAutoMessage.m_companies_id'] = $this->userInfo['MCompany']['id'];
    $this->set('settingList', $this->paginate('TAutoMessage'));
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
    $this->_viewElement();
  }

   /**
   * 更新画面
   * @return void
   * */
  public function edit($id=null) {
    if ($this->request->is('put')) {
      $this->_entry($this->request->data);
    }
    else {
      // 確実なデータを取得するために企業IDを指定する形とする
      $editData = $this->TAutoMessage->coFind("all", [
        'conditions' => [
          'TAutoMessage.id' => $id
        ]
      ]);
      if (empty($editData) || (!empty($editData) && empty($editData[0]))) {
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.notFoundId'));
        $this->redirect('/TAutoMessages/index');
      }
      $json = json_decode($editData[0]['TAutoMessage']['activity'], true);
      $this->request->data = $editData[0];
      $this->request->data['TAutoMessage']['condition_type'] = (!empty($json['conditionType'])) ? $json['conditionType'] : "";
      $this->request->data['TAutoMessage']['action'] = (!empty($json['message'])) ? $json['message'] : "";
      $this->request->data['TAutoMessage']['widget_open'] = (!empty($json['widgetOpen'])) ? $json['widgetOpen'] : "";
    }

    $this->_viewElement();
  }

  /* *
   * 削除
   * @return void
   * */
  public function remoteDelete(){
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';

    $selectedList = $this->request->data['selectedList'];
    $this->TAutoMessage->begin();
    $res = true;
    foreach($selectedList as $key => $val){
      $id = (isset($val)) ? $val: "";
      $ret = $this->TAutoMessage->find('first', [
        'fields' => 'TAutoMessage.*',
        'conditions' => [
          'TAutoMessage.del_flg' => 0,
          'TAutoMessage.id' => $id,
          'TAutoMessage.m_companies_id' => $this->userInfo['MCompany']['id']
        ],
        'recursive' => -1
      ]);
      if ( count($ret) === 1 ) {
        if (! $this->TAutoMessage->delete($val) ) {
          $res = false;
        }
      }
    }
    if($res){
      $this->TAutoMessage->commit();
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
    }
    else {
      $this->TAutoMessage->rollback();
      $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
    }
//     $id = (isset($this->request->data['id'])) ? $this->request->data['id'] : "";
//     $ret = $this->TAutoMessage->find('first', [
//       'fields' => 'TAutoMessage.*',
//       'conditions' => [
//         'TAutoMessage.del_flg' => 0,
//         'TAutoMessage.id' => $id,
//         'TAutoMessage.m_companies_id' => $this->userInfo['MCompany']['id']
//       ],
//       'recursive' => -1
//     ]);
//     if ( count($ret) === 1 ) {
//       if ( $this->TAutoMessage->logicalDelete($id) ) {
//         $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
//       }
//       else {
//         $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
//       }
//     }
  }

  /* *
   * コピー処理
   * @return void
   * */
  public function remoteCopyEntryForm() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $selectedList = $this->request->data['selectedList'];
    //コピー元のオートメッセージリスト取得
    foreach($selectedList as $value){
      $copyData[] = $this->TAutoMessage->read(null, $value);
    }
    $errorMessage = [];
    //コピー元のオートメッセージリストの数だけ繰り返し
    $res = true;
    foreach($copyData as $value){
      $this->TAutoMessage->create();
      $saveData = [];
      $saveData['TAutoMessage']['m_companies_id'] = $value['TAutoMessage']['m_companies_id'];
      $saveData['TAutoMessage']['name'] = $value['TAutoMessage']['name'];
      $saveData['TAutoMessage']['trigger_type'] = $value['TAutoMessage']['trigger_type'];
      $saveData['TAutoMessage']['activity'] = $value['TAutoMessage']['activity'];
      $saveData['TAutoMessage']['action_type'] = $value['TAutoMessage']['action_type'];
      $saveData['TAutoMessage']['active_flg'] = $value['TAutoMessage']['active_flg'];
      $saveData['TAutoMessage']['del_flg'] = $value['TAutoMessage']['del_flg'];
      $this->TAutoMessage->set($saveData);
      $this->TAutoMessage->begin();
      // バリデーションチェックでエラーが出た場合
      if($res){
        if (! $this->TAutoMessage->save() ) {
          $res = false;
          $errorMessage = $this->TAutoMessage->validationErrors;
          $this->TAutoMessage->rollback();
        }
        else{
          $this->TAutoMessage->commit();
          $this->Session->delete('dstoken');
          $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
        }
      }
    }
  }

  /**
   * ステータス更新
   * @return void
   * */
  public function changeStatus() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $inputData = $this->request->query;
    $case = gettype($inputData['status']);
    $activeFlg = 1;
    if ($case === "boolean" && $inputData['status'] || $case === "string" && strcmp($inputData['status'], 'true') === 0) {
      $activeFlg = 0;
    }
    $this->TAutoMessage->updateAll(
      ['active_flg'=>$activeFlg],
      [
        'id' => $inputData['targetList'],
        'm_companies_id' => $this->userInfo['MCompany']['id'],
        'del_flg' => 0
      ]
    );
  }

  /**
   * 保存機能
   * @param array $inputData
   * @return void
   * */
  private function _entry($saveData) {
    $errors = [];
    $saveData['TAutoMessage']['m_companies_id'] = $this->userInfo['MCompany']['id'];

    $this->TAutoMessage->begin();
    if ( empty($saveData['TAutoMessage']['id']) ) {
      $this->TAutoMessage->create();
    }

    $this->TAutoMessage->set($saveData);

    $validate = $this->TAutoMessage->validates();
    $errors = $this->TAutoMessage->validationErrors;

    // その他のチェック
    if ( !empty($saveData['TAutoMessage']) ) {
      $activity = json_decode($saveData['TAutoMessage']['activity']);

      /* 項目ごとの設定数上限チェック */
      $tmpMessage = "%sの場合、『%s』は%d個まで設定可能です";

      foreach((array)$activity->conditions as $key => $val) {
        $setting = $this->outMessageTriggerList[$key];
        if ( !isset($setting['createLimit'][$activity->conditionType]) ) continue;
        if ( count($val) > intval($setting['createLimit'][$activity->conditionType]) ) {
          $validate = false;
          $errors['triggers'][$setting['key']] = sprintf($tmpMessage, $this->outMessageIfType[$activity->conditionType], $setting['label'], $setting['createLimit'][$activity->conditionType]);
        }
      }
    }
    if ( $validate && $this->TAutoMessage->save(false) ) {
      $this->TAutoMessage->commit();
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
      $this->redirect('/TAutoMessages/index');
    }
    else {
      $this->TAutoMessage->rollback();
      $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
    }
    $this->set('errors', $errors);
  }

  /**
   * ビュー部品セット
   * @return void
   * */
  private function _viewElement() {
    // TODO out -> auto に変更
    // トリガー種別
    $this->set('outMessageTriggerType', Configure::read('outMessageTriggerType'));
    // 条件設定種別
    $this->set('outMessageIfType', $this->outMessageIfType);
    // 条件リスト
    $this->set('outMessageTriggerList', $this->outMessageTriggerList);
    // アクション種別
    $this->set('outMessageActionType', Configure::read('outMessageActionType'));
    // ウィジェット種別
    $this->set('outMessageWidgetOpenType', Configure::read('outMessageWidgetOpenType'));
    // 有効無効
    $this->set('outMessageAvailableType', Configure::read('outMessageAvailableType'));
  }



}
