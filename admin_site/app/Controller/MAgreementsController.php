<?php
/**
 * PersonalSettingsController controller.
 * ユーザーマスタ
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
          'type' => 'inner',    // もしくは left
          'table' => 'm_agreements',
          'alias' => 'MAgreement',
          'conditions' => [
          'MAgreement.m_companies_id = MCompany.id',
          ],
        ],
        [
          'type' => 'inner',    // もしくは left
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
      $this->_mcompany($saveData);

      $saveData2 = $this->request->data;
      $this->_muser($saveData2);

      $saveData3 = $this->request->data;
      $this->_magreement($saveData3);

      $saveData4 = $this->request->data;
      $this->_tdictionary($saveData4);

      $saveData5 = $this->request->data;
      $this->_tautomessage($saveData5);

      $saveData6 = $this->request->data;
      $this->_mwidgetsetting($saveData6);

      if($this->_mcompany($saveData) && $this->_muser($saveData2) && $this->_magreement($saveData3) && $this->_tdictionary($saveData4) && $this->_tautomessage($saveData5) && $this->_mwidgetsetting($saveData6)) {
        $this->TransactionManager->commit($transactions);
        //$this->_addFile($saveData);
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
        $this->redirect(['controller' => 'MAgreements', 'action' => 'index']);
      }
      $this->TransactionManager->rollback($transactions);
    }
  }

  /* *
   * 更新画面
   * @return void
   * */
  public function edit($id) {
    $this->MAgreement->id = $id;

    if($this->request->is('post') || $this->request->is('put')) {
      $editData = $this->MAgreement->read(null,$id);
      $data = $this->MUser->find('all',[
        'conditions' => array('m_companies_id' => $editData['MAgreement']['m_companies_id'])
      ]);
      $companyId = $editData['MCompany']['id'];
      $companyKey = $editData['MCompany']['company_key'];
      $userId = $data[0]['MUser']['id'];
      $transactions = $this->TransactionManager->begin();

      $saveData = $this->request->data;
      $this->_mcompany($saveData);
      $saveData2 = $this->request->data;
      $this->_muser($saveData2);
      $saveData3 = $this->request->data;
      $this->_magreement($saveData3);

      $this->set('companyId', $companyId);
      $this->set('companyKey', $companyKey);
      $this->set('userId', $userId);
      if($this->_mcompany($saveData)==true && $this->_muser($saveData2) == true && $this->_magreement($saveData3)==true) {
        $this->TransactionManager->commit($transactions);
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
        //$this->_editFile($saveData,$editData);
        $this->redirect(['controller' => 'MAgreements', 'action' => 'index']);
      }
      else {
        $this->TransactionManager->rollback($transactions);
      }
    }
    else{
      $this->request->data = $this->MAgreement->read(null,$id);
      $data = $this->MUser->find('all',[
        'conditions' => array('m_companies_id' => $this->request->data['MAgreement']['m_companies_id'])
      ]);
      $companyId = $this->request->data['MCompany']['id'];
      $companyKey = $this->request->data['MCompany']['company_key'];
      $this->request->data['MUser']['id'] = $data[0]['MUser']['id'];
      $userId = $data[0]['MUser']['id'];
      $this->set('companyId', $companyId);
      $this->set('userId', $userId);
      $this->set('companyKey',$companyKey);
      $this->request->data['MAgreement']['company_name'] = $this->request->data['MCompany']['company_name'];
      $this->request->data['MAgreement']['company_key'] = $this->request->data['MCompany']['company_key'];
      $this->request->data['MAgreement']['limit_users'] = $this->request->data['MCompany']['limit_users'];
      $this->request->data['MAgreement']['m_contact_types_id'] = $this->request->data['MCompany']['m_contact_types_id'];
      $this->request->data['MAgreement']['trial_flg'] = $this->request->data['MCompany']['trial_flg'];
      $this->request->data['MAgreement']['mail_address'] = $data[0]['MUser']['mail_address'];
    }
  }

  /* *
   * 保存画面
   * @return void
   * */
  private function _mcompany($saveData) {
    //m_companiesに登録
    $saveData['MCompany']['company_name'] = $saveData['MAgreement']['company_name'];
    $saveData['MCompany']['company_key'] = $saveData['MAgreement']['company_key'];
    $saveData['MCompany']['limit_users'] = $saveData['MAgreement']['limit_users'];
    $saveData['MCompany']['m_contact_types_id'] = $saveData['MAgreement']['m_contact_types_id'];
    //coresettings
    if($saveData['MCompany']['m_contact_types_id']==1){
      $plan = array('chat' => true,'synclo' => true);
      $saveData['MCompany']['core_settings'] = json_encode($plan);
    }
    else if($saveData['MCompany']['m_contact_types_id']==2){
      $plan = array('chat' => true);
      $saveData['MCompany']['core_settings'] = json_encode($plan);
    }
    else if($saveData['MCompany']['m_contact_types_id']==3){
      $plan = array('synclo' => true);
      $saveData['MCompany']['core_settings'] = json_encode($plan);
    }
    $saveData['MCompany']['trial_flg'] = $saveData['MAgreement']['trial_flg'];

    if ( empty($saveData['MAgreement']['id']) ) {
      $this->MCompany->create();
    }
    $this->MCompany->set($saveData);

    if($this->MCompany->validates() && $this->MCompany->save($saveData,false)) {
      return true;
    }
    else{
      $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
      $errors = $this->MCompany->validationErrors;
      $this->set('errors', $errors);
    }
  }

  /**
   * 保存機能
   * @param array $inputData
   * @return void
   * */
  private function _muser($saveData2) {
    //m_usersに登録
    $companyLastId = $this->MCompany->getLastInsertID();
    $saveData2['MUser']['m_companies_id'] = $companyLastId;
    $saveData2['MUser']['user_name'] = 'ML用アカウント';
    $saveData2['MUser']['display_name'] = 'ML用アカウント';
    $saveData2['MUser']['mail_address'] = $saveData2['MAgreement']['mail_address'];
    $saveData2['MUser']['password'] = $saveData2['MAgreement']['hash_password'];
    $saveData2['MUser']['permission_level'] = 99;
    $saveData2['MUser']['del_flg'] = 0;
    if ( empty($saveData2['MAgreement']['id']) ) {
      $this->MUser->create();
    }
    $this->MUser->set($saveData2);
    if($this->MUser->validates() && $this->MUser->save($saveData2,false)) {
      return true;
    }
    else {
      $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
      $userErrors = $this->MUser->validationErrors;
      $this->set('userErrors', $userErrors);
    }
  }

/**
   * 保存機能
   * @param array $inputData
   * @return void
   * */
  private function _magreement($saveData3) {
    if ( empty($saveData3['MAgreement']['id']) ) {
      $this->MAgreement->create();
    }
    $companyLastId = $this->MCompany->getLastInsertID();
    $saveData3['MAgreement']['del_flg'] = 0;
    $saveData3['MAgreement']['m_companies_id'] = $companyLastId;
    $this->MAgreement->set($saveData3);
    if ($this->MAgreement->validates() && $this->MAgreement->save($saveData3,false)) {
      return true;
    }
    else {
      $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
      $errors = $this->MUser->validationErrors;
      $this->set('errors', $errors);
    }
  }

/**
   * 保存機能
   * @param array $inputData
   * @return void
   * */
  private function _tdictionary($saveData4) {
    //簡易入力メッセージデフォルト値
    if ( empty($saveData4['MAgreement']['id']) ) {
      $tdictionaryData = $this->TDictionary->find('all',[
        'conditions' => ['MCompany.company_key' => 'template']
      ]);
      $companyLastId = $this->MCompany->getLastInsertID();
      $userLastId = $this->MUser->getLastInsertID();
      foreach((array)$tdictionaryData as $key => $val):
      $saveData4['TDictionary']['m_companies_id'] = $companyLastId;
      $saveData4['TDictionary']['m_users_id'] = $userLastId;
      $saveData4['TDictionary']['word'] = $val['TDictionary']['word'];
      $saveData4['TDictionary']['type'] = $val['TDictionary']['type'];
      $saveData4['TDictionary']['sort'] = $val['TDictionary']['sort'];
      $this->TDictionary->create();
      $this->TDictionary->set($saveData4);
      if($this->TDictionary->save()) {
        continue;
      }
      else {
        $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
        return false;
      }
      endforeach;
      return true;
    }
  }

/**
   * 保存機能
   * @param array $inputData
   * @return void
   * */
  private function _tautomessage($saveData5) {
    if ( empty($saveData5['MAgreement']['id']) ) {
      $tautoData = $this->TAutoMessage->find('all',[
        'conditions' => ['MCompany.company_key' => 'template']
      ]);
      $companyLastId = $this->MCompany->getLastInsertID();
      foreach((array)$tautoData as $key => $val):
      $saveData5['TAutoMessage']['m_companies_id'] = $companyLastId;;
      $saveData5['TAutoMessage']['name'] = $val['TAutoMessage']['name'];
      $saveData5['TAutoMessage']['trigger_type'] = $val['TAutoMessage']['trigger_type'];
      $saveData5['TAutoMessage']['activity'] = $val['TAutoMessage']['activity'];
      $saveData5['TAutoMessage']['action_type'] = $val['TAutoMessage']['action_type'];
      $saveData5['TAutoMessage']['active_flg'] = $val['TAutoMessage']['active_flg'];
      $this->TAutoMessage->create();
      $this->TAutoMessage->set($saveData5);
      if($this->TAutoMessage->save()) {
        continue;
      }
      else {
        $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
        return false;
      }
      endforeach;
      return true;
    }
  }

  /**
   * 保存機能
   * @param array $inputData
   * @return void
   * */
  private function _mwidgetsetting($saveData6) {
    if ( empty($saveData6['MAgreement']['id']) ) {
      $widgetData = $this->MWidgetSetting->find('all',[
        'conditions' => ['MCompany.company_key' => 'template']
      ]);
      $companyLastId = $this->MCompany->getLastInsertID();
      foreach((array)$widgetData as $key => $val):
      $saveData6['MWidgetSetting']['m_companies_id'] = $companyLastId;;
      $saveData6['MWidgetSetting']['display_type'] = $val['MWidgetSetting']['display_type'];
      $saveData6['MWidgetSetting']['style_settings'] = $val['MWidgetSetting']['style_settings'];
      $this->MWidgetSetting->create();
      $this->MWidgetSetting->set($saveData6);
      if($this->MWidgetSetting->save()) {
        continue;
      }
      else {
        $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
        return false;
      }
      endforeach;
      return true;
    }
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
      if ( $this->MUser->logicalDelete($this->request->data['userId']) && $this->MCompany->logicalDelete($this->request->data['companyId']) && $this->MAgreement->logicalDelete($this->request->data['id'])) {
        $this->TransactionManager->commit($transactions);
        // 作成するファイル名の指定
        $file_name ="C:/Project/sinclo/socket/webroot/client/{$name}.js";
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

  public function _editFile($saveData,$editData) {
    $this->autoRender = FALSE;
    $name = $saveData['MCompany']['company_key'];
    //pr($saveData); exit();
    // 作成するファイル名の指定
    $file_name =C_NODE_SERVER_DIR."webroot/client/{$name}.js";
    // ファイルの存在確認
    if( !file_exists($file_name) ){
      $beforename = $editData['MCompany']['company_key'];
      $before_file = C_NODE_SERVER_DIR."webroot/client/{$beforename}.js";
      // ファイル削除、作成
      unlink($before_file);
      touch($file_name);
    }
  }

  /* *
   * jsfile登録画面
   * @return void
   * */
  public function _addFile($saveData) {
    $this->autoRender = FALSE;
    $name = $saveData['MCompany']['company_key'];
    //pr($saveData); exit();
    // 作成するファイル名の指定
    $file_name = C_NODE_SERVER_DIR."/webroot/client/{$name}.js";
    // ファイルの存在確認
    if( !file_exists($file_name) ){
      // ファイル作成
      touch( $file_name );
    }else{
      // すでにファイルが存在する為エラーとする
      echo('Warning - ファイルが存在しています。 file name:['.$file_name.']');
      exit();
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
              socket: 'http://socket.localhost:9090',
              files: 'http://socket.localhost:8080',
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

    $this->render('/MAgreements/index');
  }
}
