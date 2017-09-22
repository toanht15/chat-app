<?php
/**
 * MAgreementsController controller.
 * 契約
 */
class MAgreementsController extends AppController {
  public $uses = ['MCompany','MUser','MAgreement','TransactionManager','TDictionary','MWidgetSetting','TAutoMessage'];

  public $paginate = [
    'MCompany' => [
      'limit' => 10,
      'order' => ['MCompany.id' => 'asc'],
      'fields' => ['*'],
      'joins' => [
        [
          'type' => 'inner',
          'table' => 'm_agreements',
          'alias' => 'MAgreement',
          'conditions' => [
          'MAgreement.m_companies_id = MCompany.id',
          ],
        ],
        [
          'type' => 'inner',
          'table' => '(SELECT id,m_companies_id,mail_address,password,count(m_companies_id) AS companyId FROM  m_users WHERE del_flg != 1 GROUP BY m_companies_id)',
          'alias' => 'MUser',
          'conditions' => [
          'MUser.m_companies_id = MCompany.id',
          ],
        ],
      ],
      'conditions' => [
        'MCompany.del_flg != ' => 1,
      ],
    ]
  ];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', '契約管理');
  }

  /* *
   * 一覧画面
   * @return void
   * */
  public function index() {
    $this->set('companyList', $this->paginate('MCompany'));
  }

    /* *
   * 登録画面
   * @return void
   * */
  public function add() {
    if($this->request->is('post')) {
      $transactions = $this->TransactionManager->begin();
      $saveData = $this->request->data;

      if($this->_saveMcompany($saveData)){
        $saveData['MCompany']['id'] = $this->MCompany->getLastInsertID();
        if($this->_saveMuser($saveData) && $this->_saveMagreement($saveData) && $this->_saveTdictionary($saveData) && $this->_saveTautomessage($saveData) && $this->_saveMwidgetsetting($saveData)) {
          $this->TransactionManager->commit($transactions);
          //jsファイル作成
          $this->_addFile($saveData);
          $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
          $this->redirect(['controller' => 'MAgreements', 'action' => 'index']);
        }
        else{
          $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
          $this->TransactionManager->rollback($transactions);
        }
      }
      else{
        $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
        $this->TransactionManager->rollback($transactions);
      }
    }
  }

  /* *
   * 更新画面
   * @param id
   * @return void
   * */
  public function edit($id) {
    $this->MAgreement->id = $id;

    if($this->request->is('post') || $this->request->is('put')) {
      $editData = $this->MAgreement->read(null,$id);
      $transactions = $this->TransactionManager->begin();
      $saveData = $this->request->data;
      $this->set('companyId', $saveData['MAgreement']['m_companies_id']);//削除に必要なもの
      $this->set('companyKey', $saveData['MCompany']['company_key']);//削除に必要なもの
      $this->set('userId', $saveData['MAgreement']['m_users_id']);//削除に必要なもの
      if($this->_saveMcompany($saveData) && $this->_saveMuser($saveData) && $this->_saveMagreement($saveData)) {
        $this->TransactionManager->commit($transactions);
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
        $saveData['beforeData'] = $editData['MCompany']['company_key'];
        //jsファイル作成
        $this->_editFile($saveData);
        $this->redirect(['controller' => 'MAgreements', 'action' => 'index']);
      }
      else {
        $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
        $this->TransactionManager->rollback($transactions);
      }
    }
    else{
    $editData = $this->MAgreement->read(null,$id);
    $data = $this->MUser->find('first',[
      'conditions' => array(
       'MCompany.id' => $editData['MAgreement']['m_companies_id'],
        'permission_level' => 99)]);

      $this->set('companyId', $editData['MCompany']['id']);//削除に必要なもの
      $this->set('userId', $data['MUser']['id']);//削除に必要なもの
      $this->set('companyKey',$editData['MCompany']['company_key']);//削除に必要なもの
      $this->request->data = $editData;
      $this->request->data['MAgreement']['m_users_id'] = $data['MUser']['id'];
      $this->request->data['MAgreement']['mail_address'] = $data['MUser']['mail_address'];
    }
  }

  /* *
   * m_companies保存
   * @param $saveData
   * @return boolean 保存処理の結果を返す
   * */
  private function _saveMcompany($saveData) {
    //m_companiesに登録
    if ( empty($saveData['MAgreement']['id']) ) {
      $this->MCompany->create();
    }
    else{
      $saveData['MCompany']['id'] = $saveData['MAgreement']['m_companies_id'];
    }
    /* 契約プラン */
    //フルプラン
    if($saveData['MCompany']['m_contact_types_id']==1){
      $plan = array(C_MAGREEMENT_FULL_PLUN);
      $saveData['MCompany']['core_settings'] = json_encode($plan);
    }
    //チャットプラン
    else if($saveData['MCompany']['m_contact_types_id']==2){
      $plan = array(C_MAGREEMENT_CHAT_PLUN);
      $saveData['MCompany']['core_settings'] = json_encode($plan);
    }
    //画面共有プラン
    else if($saveData['MCompany']['m_contact_types_id']==3){
      $plan = array(C_MAGREEMENT_SCREEN_SHARING_PLUN);
      $saveData['MCompany']['core_settings'] = json_encode($plan);
    }
    $this->MCompany->set($saveData);

    if($this->MCompany->validates() && $this->MCompany->save($saveData,false)) {
      return true;
    }
    else{
      $errors = $this->MCompany->validationErrors;
      $this->set('errors', $errors);
      return false;
    }
  }

  /**
   * m_user保存
   * @param $saveData
   * @return boolean 保存処理の結果を返す
   * */
  private function _saveMuser($saveData) {
    //m_usersに登録
    if ( empty($saveData['MAgreement']['id']) ) {
      $this->MUser->create();
      $saveData['MUser']['m_companies_id'] = $saveData['MCompany']['id'];
    }
    else{
      $saveData['MUser']['id'] = $saveData['MAgreement']['m_users_id'];
    }
    $saveData['MUser']['user_name'] = C_MEDIALINK_ACCOUNT;
    $saveData['MUser']['display_name'] = C_MEDIALINK_ACCOUNT;
    $saveData['MUser']['mail_address'] = $saveData['MAgreement']['mail_address'];
    $saveData['MUser']['password'] = $saveData['MAgreement']['hash_password'];
    $saveData['MUser']['permission_level'] = C_MEDIALINK_PERMISSION_LEVEL;
    $saveData['MUser']['del_flg'] = 0;

    $this->MUser->set($saveData);
    if($this->MUser->validates() && $this->MUser->save($saveData,false)) {
      return true;
    }
    else {
      $userErrors = $this->MUser->validationErrors;
      $this->set('userErrors', $userErrors);
      return false;
    }
  }

/**
   * m_agreements保存
   * @param $saveData
   * @return boolean 保存処理の結果を返す
   * */
  private function _saveMagreement($saveData) {
    //m_agreementsに登録
    if ( empty($saveData['MAgreement']['id']) ) {
      $this->MAgreement->create();
      $saveData['MAgreement']['m_companies_id'] = $saveData['MCompany']['id'];
    }
    $saveData['MAgreement']['del_flg'] = 0;
    $this->MAgreement->set($saveData);
    if ($this->MAgreement->validates() && $this->MAgreement->save($saveData,false)) {
      return true;
    }
    else {
      $agreementerrors = $this->MAgreement->validationErrors;
      $this->set('agreementerrors', $agreementerrors);
      return false;
    }
  }

/**
   * tdictionaries保存
   * @param $saveData
   * @return boolean 保存処理の結果を返す
   * */
  private function _saveTdictionary($saveData) {
    //定型文メッセージデフォルト値保存
    $tdictionaryData = $this->TDictionary->find('all',[
      'conditions' => ['MCompany.company_key' => 'template']
    ]);
    $userLastId = $this->MUser->getLastInsertID();
    foreach((array)$tdictionaryData as $key => $val){
      $saveData['TDictionary']['m_companies_id'] = $saveData['MCompany']['id'];
      $saveData['TDictionary']['m_users_id'] = $userLastId;
      $saveData['TDictionary']['word'] = $val['TDictionary']['word'];
      $saveData['TDictionary']['type'] = $val['TDictionary']['type'];
      $saveData['TDictionary']['sort'] = $val['TDictionary']['sort'];
      $this->TDictionary->create();
      $this->TDictionary->set($saveData);
      if($this->TDictionary->save()) {
        continue;
      }
      else {
        return false;
      }
    }
    return true;
  }

/**
   * tautomessages保存
   * @param $saveData
   * @return boolean 保存処理の結果を返す
   * */
  private function _saveTautomessage($saveData) {
    //オートメッセージデフォルト値保存
    $tautoData = $this->TAutoMessage->find('all',[
      'conditions' => ['MCompany.company_key' => 'template']
    ]);
    foreach((array)$tautoData as $key => $val){
      $saveData['TAutoMessage']['m_companies_id'] = $saveData['MCompany']['id'];
      $saveData['TAutoMessage']['name'] = $val['TAutoMessage']['name'];
      $saveData['TAutoMessage']['trigger_type'] = $val['TAutoMessage']['trigger_type'];
      $saveData['TAutoMessage']['activity'] = $val['TAutoMessage']['activity'];
      $saveData['TAutoMessage']['action_type'] = $val['TAutoMessage']['action_type'];
      $saveData['TAutoMessage']['active_flg'] = $val['TAutoMessage']['active_flg'];
      $this->TAutoMessage->create();
      $this->TAutoMessage->set($saveData);
      if($this->TAutoMessage->save()) {
        continue;
      }
      else {
        return false;
      }
    }
    return true;
  }

  /**
   * mwidetsetting保存
   * @param $saveData
   * @return boolean 保存処理の結果を返す
   * */
  private function _saveMwidgetsetting($saveData) {
    //ウィジェットデフォルト値設定
    $widgetData = $this->MWidgetSetting->find('all',[
      'conditions' => ['MCompany.company_key' => 'template']
    ]);
    foreach((array)$widgetData as $key => $val){
      $saveData['MWidgetSetting']['m_companies_id'] = $saveData['MCompany']['id'];
      $saveData['MWidgetSetting']['display_type'] = $val['MWidgetSetting']['display_type'];
      $saveData['MWidgetSetting']['style_settings'] = $val['MWidgetSetting']['style_settings'];
      $this->MWidgetSetting->create();
      $this->MWidgetSetting->set($saveData);
      if($this->MWidgetSetting->save()) {
        continue;
      }
      else {
        return false;
      }
    }
    return true;
  }

  /* *
  * 削除
  * @return void
  * */
  public function remoteDeleteCompany() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $name = $this->request->data['companyKey'];
    if(!empty($this->request->data['userId']) && ($this->request->data['companyId']) && ($this->request->data['id'])) {
      $transactions = $this->TransactionManager->begin();
      if ( $this->MCompany->logicalDelete($this->request->data['companyId']) && $this->MUser->logicalDelete($this->request->data['userId']) && $this->MAgreement->logicalDelete($this->request->data['id'])){
        $this->TransactionManager->commit($transactions);
        // 作成するファイル名の指定
        $file_name =C_NODE_SERVER_DIR."/webroot/client/{$name}.js";
        // ファイルの存在確認
        if( file_exists($file_name) ){
          // ファイル削除
          unlink($file_name);
        }
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
      }
      else {
        $this->TransactionManager->rollback($transactions);
      }
    }
  }

  /* *
   * jsfile更新機能
   * @param $saveData
   * @return void
   * */
  public function _editFile($saveData) {
    $this->autoRender = FALSE;
    $name = $saveData['MCompany']['company_key'];
    // 作成するファイル名の指定
    $file_name =C_NODE_SERVER_DIR."/webroot/client/{$name}.js";
    // ファイルの存在確認
    if( !file_exists($file_name) ){
      $beforename = $saveData['beforeData'];
      $before_file = C_NODE_SERVER_DIR."/webroot/client/{$beforename}.js";
      // ファイル削除、作成
      unlink($before_file);
      touch($file_name);
    }
  }

  /* *
   * jsfile登録機能
   * @param $saveData
   * @return void
   * */
  public function _addFile($saveData) {
    $this->autoRender = FALSE;
    $name = $saveData['MCompany']['company_key'];
    // 作成するファイル名の指定
    $file_name = C_NODE_SERVER_DIR."/webroot/client/{$name}.js";
    // ファイルの存在確認
    if( !file_exists($file_name) ){
      // ファイル作成
      touch( $file_name );
    }else{
      // すでにファイルが存在する為エラーとする
      echo('Warning - ファイルが存在しています。 file name:['.$file_name.']');
    }
    // ファイルのパーティションの変更

    //chmod( $file_name,0606);
    echo('Info - ファイル作成完了。 file name:['.$file_name.']');

    $fp = fopen(C_NODE_SERVER_DIR."/webroot/client/{$name}.js", 'w');
    fwrite($fp,
      "<!--
        // 'use strict';
        var info;
        (function(){
          info = {
            dataset: {},
            site: {
              key: $name,
              socket: C_MEDIALINK_SOCKET_PASS,
              files: C_MEDIALINK_FILES_PASS,
              webcam_view: 'http://socket.localhost:8080/webcam.html'
            }
          };
          var b = document.getElementsByTagName('body')[0],
          l = [
            info.site.files + '/websocket/jquery-2.2.0.min.js',
            info.site.socket + '/socket.io/socket.io.js',
            info.site.files + '/websocket/common.min.js',
            info.site.files + '/websocket/sinclo.min.js'
          ],
          i = 0,
          createElm = function (u){
            var s = document.createElement('script');
            s.type = 'text/javascript';
            s.src = u;
            s.charset='UTF-8';
            b.appendChild(s);
            i ++;
            s.onload = function(){
            if ( l[i] !== undefined ) createElm(l[i]);
            }
          };
          createElm(l[i]);
        }());
      //-->
    ");
    fclose($fp);
    $this->render('index');
  }
}
