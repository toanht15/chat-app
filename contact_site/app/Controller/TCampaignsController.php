<?php
/**
 * TCampaignsController
 * キャンペーン設定
 */
class TCampaignsController extends AppController {
  public $uses = ['TCampaign'];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', 'キャンペーン設定');
  }

  /* *
   * 一覧画面
   * @return void
   * */
  public function index() {
    //$this->set('tCampaignList', $this->TCampaign->find('all', $this->_setParams()));
    $documentList = $this->TCampaign->find('all', $this->_setParams());
    $this->set('tCampaignList', $documentList);
  }

  /* *
   * 登録,更新画面
   * @return void
   * */
  public function remoteOpenEntryForm() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    // const
    if ( strcmp($this->request->data['type'], 2) === 0 ) {
      $this->request->data = $this->TCampaign->read(null, $this->request->data['id']);
    }
    $this->render('/Elements/TCampaigns/remoteEntry');
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

    if (!empty($this->request->data['campaignId'])) {
      $this->TCampaign->recursive = -1;
      $saveData = $this->TCampaign->read(null, $this->request->data['campaignId']);
    }
    else {
      $this->TCampaign->create();
      $params = [
          'fields' => [
              'TCampaign.sort'
          ],
          'conditions' => [
              'TCampaign.m_companies_id' => $this->userInfo['MCompany']['id']
          ],
          'order' => [
              'TCampaign.sort' => 'desc',
              'TCampaign.id' => 'desc'
          ],
          'limit' => 1,
          'recursive' => -1
      ];
      $lastData = $this->TCampaign->find('first', $params);
      if($lastData['TCampaign']['sort'] === '0'
          || $lastData['TCampaign']['sort'] === 0
          || $lastData['TCampaign']['sort'] === null){
            //ソート順が登録されていなかったらソート順をセットする
            if(! $this->remoteSetSort()){
              $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
              return false;
            }
            //もう一度ソートの最大値を取り直す
            $lastData = $this->TCampaign->find('first', $params);
      }
      $nextSort = 1;
      if (!empty($lastData)) {
        $nextSort = intval($lastData['TCampaign']['sort']) + 1;
      }
      $saveData['TCampaign']['sort'] = $nextSort;
    }
    $saveData['TCampaign']['m_companies_id'] = $this->userInfo['MCompany']['id'];
    $saveData['TCampaign']['name'] = $this->request->data['name'];
    $saveData['TCampaign']['parameter'] = $this->request->data['parameter'];
    $saveData['TCampaign']['comment'] = $this->request->data['comment'];
    // const
    $this->TCampaign->set($saveData);
    $this->TCampaign->begin();

    // バリデーションチェックでエラーが出た場合
    if ( $this->TCampaign->save() ) {
      $this->TCampaign->commit();
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
    }
    else {
      $this->TCampaign->rollback();
    }
    $errorMessage = $this->TCampaign->validationErrors;
    return new CakeResponse(['body' => json_encode($errorMessage)]);
  }

  /* *
   * 削除
   * @return void
   * */
  public function remoteDeleteUser() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $this->TCampaign->recursive = -1;
    $selectedList = $this->request->data['selectedList'];
    $this->TCampaign->begin();
    $res = true;
    foreach($selectedList as $key => $val){
      if (! $this->TCampaign->delete($val) ) {
        $res = false;
      }
    }
    if($res){
      $this->TCampaign->commit();
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
    }
    else{
      $this->TCampaign->rollback();
      $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
    }
//     if ( $this->TCampaign->delete($this->request->data['id']) ) {
//       $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
//     }
//     else {
//       $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
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
//    $this->TCampaign->recursive = -1;
    $selectedList = $this->request->data['selectedList'];
    //コピー元のキャンペーンリスト取得
    foreach($selectedList as $value){
      $copyData[] = $this->TCampaign->read(null, $value);
    }
    $errorMessage = [];
    //コピー元のキャンペーンリストの数だけ繰り返し
    $res = true;
    foreach($copyData as $value){
      $this->TCampaign->create();
      $saveData = [];
      $params = [
          'fields' => [
              'TCampaign.sort'
          ],
          'conditions' => [
              'TCampaign.m_companies_id' => $this->userInfo['MCompany']['id']
          ],
          'order' => [
              'TCampaign.sort' => 'desc',
              'TCampaign.id' => 'desc'
          ],
          'limit' => 1,
          'recursive' => -1
      ];
      $lastData = $this->TCampaign->find('first', $params);
      if($lastData['TCampaign']['sort'] === '0'
          || $lastData['TCampaign']['sort'] === 0
          || $lastData['TCampaign']['sort'] === null){
            //ソート順が登録されていなかったらソート順をセットする
            if(! $this->remoteSetSort()){
              $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
              return false;
            }
            //もう一度ソートの最大値を取り直す
            $lastData = $this->TCampaign->find('first', $params);
      }
      $nextSort = 1;
      if (!empty($lastData)) {
        $nextSort = intval($lastData['TCampaign']['sort']) + 1;
      }
      $saveData['TCampaign']['sort'] = $nextSort;
      $saveData['TCampaign']['m_companies_id'] = $value['TCampaign']['m_companies_id'];
      $saveData['TCampaign']['name'] = $value['TCampaign']['name'];
      $saveData['TCampaign']['parameter'] = $value['TCampaign']['parameter'];
      $saveData['TCampaign']['comment'] = $value['TCampaign']['comment'];
      $this->TCampaign->set($saveData);
      $this->TCampaign->begin();
      // バリデーションチェックでエラーが出た場合
      if($res){
        if (! $this->TCampaign->save() ) {
          $res = false;
          $errorMessage = $this->TCampaign->validationErrors;
          $this->TCampaign->rollback();
        }
        else{
          $this->TCampaign->commit();
          $this->Session->delete('dstoken');
          $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
        }
      }
    }
  }

  /**
   * 資料設定ソート順更新
   *
   * */
  public function remoteSaveSort(){
    Configure::write('debug', 2);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    if ( !$this->request->is('ajax') ) return false;
    if ( !empty($this->params->data['list']) ) {
      $this->TCampaign->begin();
      $list = $this->params->data['list'];
      $this->log($list,LOG_DEBUG);
      /* 現在の並び順を取得 */
      $params = $this->_setParams();
      $params['fields'] = [
          'TCampaign.id',
          'TCampaign.sort'
      ];
      unset($params['limit']);
      $prevSort = $this->TCampaign->find('list', $params);
      //新しくソート順を設定したため、空で来ることがある
      $reset_flg = false;
      foreach($prevSort as $key => $val){
        //設定されていない値'0'が一つでも入っていたらsortをリセット
        if($val === '0' || $val === 0 || $val === null){
          $reset_flg = true;
        }
      }
      if($reset_flg){
        //ソート順のリセットはID順とする
        $i = 1;
        foreach($prevSort as $key => $val){
          $prevSort[$key] = strval($i);
          $i++;
        }
      }
      $prevSortKeys = am($prevSort);
      $this->log($prevSortKeys,LOG_DEBUG);
      /* アップデート分の並び順を設定 */
      $ret = true;
      for ($i = 0; count($list) > $i; $i++) {
        $id = $list[$i];
        if ( isset($prevSort[$id]) ) {
          $saveData = [
              'TCampaign' => [
                  'id' => $id,
                  'sort' => $prevSortKeys[$i]
              ]
          ];
          if (!$this->TCampaign->validates()) {
            $ret = false;
            break;
          }
          if (!$this->TCampaign->save($saveData)) {
            $ret = false;
            break;
          }
        } else {
          // 送信された資料設定と現在DBに存在する資料設定に差がある場合
          $this->TCampaign->rollback();
          $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.configChanged'));
          return;
        }
      }
      if ($ret) {
        $this->TCampaign->commit();
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
      }
      else {
        $this->TCampaign->rollback();
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
      }
    }
  }

  /**
   * 資料設定ソート順を現在のID順でセット
   *
   * */
  public function remoteSetSort(){
    $this->TCampaign->begin();
    /* 現在の並び順を取得 */
    $params = $this->_setParams();
    $params['fields'] = [
        'TCampaign.id',
        'TCampaign.sort'
    ];
    unset($params['limit']);
    $prevSort = $this->TCampaign->find('list', $params);
    //ソート順のリセットはID順とする
    $i = 1;
    foreach($prevSort as $key => $val){
      $prevSort[$key] = strval($i);
      $i++;
    }
    $prevSortKeys = am($prevSort);
    $this->log($prevSortKeys,LOG_DEBUG);
    $i = 0;
    $ret = true;
    foreach($prevSort as $key => $val){
      $id = $key;
      $saveData = [
          'TCampaign' => [
              'id' => $id,
              'sort' => $prevSortKeys[$i]
          ]
      ];
      if (!$this->TCampaign->validates()) {
        $ret = false;
        break;
      }
      if (!$this->TCampaign->save($saveData)) {
        $ret = false;
        break;
      }
      $i++;
    }
    if ($ret) {
      $this->TCampaign->commit();
      return true;
    }
    else {
      $this->TCampaign->rollback();
      return false;
    }
  }

  private function _setParams(){
    $params = [
      'order' => [
        'TCampaign.sort' => 'asc',
        'TCampaign.id' => 'asc'
      ],
      'fields' => [
        'TCampaign.*'
      ],
      'conditions' => [
        'TCampaign.m_companies_id' => $this->userInfo['MCompany']['id']
      ],
      'recursive' => -1
    ];
    return $params;
  }
}
