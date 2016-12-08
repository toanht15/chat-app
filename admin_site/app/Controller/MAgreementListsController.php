<?php
/**
 * PersonalSettingsController controller.
 * ユーザーマスタ
 */
class MAgreementListsController extends AppController {
  public $uses = ['MCompany','MUser','MAgreementList'];

  public $paginate = [
    'MCompany' => [
      'limit' => 10,
      'order' => ['MCompany.id' => 'asc'],
      'fields' => ['*'],
      'joins' => [
        [
          'type' => 'inner',    // もしくは left
          'table' => 'm_agreement_lists',
          'alias' => 'MAgreementList',
          'conditions' => [
          'MAgreementList .m_companies_id = MCompany.id',
          ],
        ],
        [
          'type' => 'inner',    // もしくは left
          'table' => '(SELECT id,m_companies_id,count(m_companies_id) AS companyId FROM  m_users WHERE del_flg != 1 GROUP BY m_companies_id)',
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
      $saveData = $this->request->data;
      $saveData['MCompany']['company_name'] = $saveData['MAgreementList']['company_name'];
      $saveData['MCompany']['company_key'] = $saveData['MAgreementList']['company_key'];
      $saveData['MCompany']['limit_users'] = $saveData['MAgreementList']['limit_users'];
      $saveData['MCompany']['m_contact_types_id'] = $saveData['MAgreementList']['m_contact_types_id'];
      $saveData['MCompany']['trial_flg'] = $saveData['MAgreementList']['trial_flg'];
      $this->_mcompany($saveData);

      $saveData2 = $this->request->data;
      $saveData2['MUser']['user_name'] = 'ML用アカウント';
      $saveData2['MUser']['display_name'] = 'ML用アカウント';
      $saveData2['MUser']['mail_address'] = $saveData2['MAgreementList']['mail_address'];
      $saveData2['MUser']['password'] = $saveData2['MAgreementList']['admin_password'];
      $saveData2['MUser']['permission_level'] = 99;
      $saveData2['MUser']['del_flg'] = 0;
      $this->_muser($saveData2);

      $saveData3 = $this->request->data;
      $saveData3['MAgreementList']['del_flg'] = 0;
      $this->_magreement($saveData3);

      if($this->_mcompany($saveData)==true && $this->_muser($saveData2) == true && $this->_magreement($saveData3)==true) {
        if($this->MCompany->save($saveData)){
          $last_id = $this->MCompany->getLastInsertID();
          $saveData2['MUser']['m_companies_id'] = $last_id;
          $saveData3['MAgreementList']['m_companies_id'] = $last_id;
          $this->MUser->set($saveData2);
          $this->MAgreementList->set($saveData3);
          if($this->MUser->save($saveData2) && $this->MAgreementList->save($saveData3)) {
            $this->MCompany->commit();
            $this->MUser->commit();
            $this->MAgreementList->commit();
            $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
            $this->redirect(['controller' => 'MAgreementLists', 'action' => 'index']);
          }
        }
      }
    }
  }

  /* *
   * 更新画面
   * @return void
   * */
  public function edit($id) {
    $this->MAgreementList->id = $id;

    if($this->request->is('post') || $this->request->is('put')) {
      $editData = $this->MAgreementList->read(null,$id);
      $data = $this->MUser->find('all',[
        'conditions' => array('m_companies_id' => $editData['MAgreementList']['m_companies_id'])
      ]);

      $m_companies_id = $editData['MCompany']['id'];
      $m_user_id = $data[0]['MUser']['id'];
      $this->set('m_companies_id', $m_companies_id);
      $this->set('m_user_id', $m_user_id);
      $saveData = $this->request->data;
      $saveData['MCompany']['id'] = $m_companies_id;
      $saveData['MCompany']['company_name'] = $saveData['MAgreementList']['company_name'];
      $saveData['MCompany']['company_key'] = $saveData['MAgreementList']['company_key'];
      $saveData['MCompany']['limit_users'] = $saveData['MAgreementList']['limit_users'];
      $saveData['MCompany']['m_contact_types_id'] = $saveData['MAgreementList']['m_contact_types_id'];
      $saveData['MCompany']['trial_flg'] = $saveData['MAgreementList']['trial_flg'];
      $this->_mcompany($saveData);

      $saveData2 = $this->request->data;
      $saveData2['MUser']['id'] = $m_user_id;
      $saveData2['MUser']['user_name'] = 'ML用アカウント';
      $saveData2['MUser']['display_name'] = 'ML用アカウント';
      $saveData2['MUser']['mail_address'] = $saveData2['MAgreementList']['mail_address'];
      $saveData2['MUser']['password'] = $saveData2['MAgreementList']['admin_password'];
      $saveData2['MUser']['permission_level'] = 99;
      $saveData2['MUser']['del_flg'] = 0;
      $this->_muser($saveData2);

      $saveData3 = $this->request->data;
      $saveData3['MAgreementList']['del_flg'] = 0;
      $this->_magreement($saveData3);

      if($this->_mcompany($saveData)==true && $this->_muser($saveData2) == true && $this->_magreement($saveData3)==true) {
        if($this->MCompany->save($saveData)){
          if ( empty($saveData['MAgreementList']['id']) ) {
            $last_id = $this->MCompany->getLastInsertID();
            $saveData2['MUser']['m_companies_id'] = $last_id;
            $saveData3['MAgreementList']['m_companies_id'] = $last_id;
          }
          $this->MUser->set($saveData2);
          $this->MAgreementList->set($saveData3);
          if($this->MUser->save($saveData2) && $this->MAgreementList->save($saveData3)) {
            $this->MCompany->commit();
            $this->MUser->commit();
            $this->MAgreementList->commit();
            $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
            $this->redirect(['controller' => 'MAgreementLists', 'action' => 'index']);
          }
        }
      }
    }
    else{
      $this->request->data = $this->MAgreementList->read(null,$id);
      $data = $this->MUser->find('all',[
        'conditions' => array('m_companies_id' => $this->request->data['MAgreementList']['m_companies_id'])
      ]);

      $m_companies_id = $this->request->data['MCompany']['id'];
      $this->request->data['MUser']['id'] = $data[0]['MUser']['id'];
      $m_user_id = $data[0]['MUser']['id'];
      $this->set('m_companies_id', $m_companies_id);
      $this->set('m_user_id', $m_user_id);
      $this->request->data['MAgreementList']['company_name'] = $this->request->data['MCompany']['company_name'];
      $this->request->data['MAgreementList']['company_key'] = $this->request->data['MCompany']['company_key'];
      $this->request->data['MAgreementList']['limit_users'] = $this->request->data['MCompany']['limit_users'];
      $this->request->data['MAgreementList']['m_contact_types_id'] = $this->request->data['MCompany']['m_contact_types_id'];
      $this->request->data['MAgreementList']['trial_flg'] = $this->request->data['MCompany']['trial_flg'];
      $this->request->data['MAgreementList']['mail_address'] = $data[0]['MUser']['mail_address'];
    }
  }

   /**
   * 保存機能
   * @param array $inputData
   * @return void
   * */
  private function _mcompany($saveData) {
    $this->MCompany->begin();
    if ( empty($saveData['MAgreementList']['id']) ) {
      $this->MCompany->create();
    }
    $this->MCompany->set($saveData);
    if($this->MCompany->validates()) {
      return true;
    }
    else{
      $this->MCompany->rollback();
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
    $this->MUser->begin();
    if ( empty($saveData2['MAgreementList']['id']) ) {
      $this->MUser->create();
    }
    $this->MUser->set($saveData2);
    if($this->MUser->validates()) {
      return true;
    }
    else {
      $this->MUser->rollback();
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
    $this->MAgreementList->begin();
    if ( empty($saveData3['MAgreementList']['id']) ) {
      $this->MAgreementList->create();
    }

    $this->MAgreementList->set($saveData3);
    if($this->MAgreementList->validates()) {
      return true;
    }
    else {
      $this->MAgreementList->rollback();
      $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
      $errors = $this->MUser->validationErrors;
      $this->set('errors', $errors);
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
    if ( $this->MUser->logicalDelete($this->request->data['m_user_id']) ) {
      if ( $this->MCompany->logicalDelete($this->request->data['m_companies_id']) ) {
        if ( $this->MAgreementList->logicalDelete($this->request->data['id']) ) {
          $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
        }
      }
    }
    else {
      $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
    }
  }


  /* *
   * jsfile登録画面
   * @return void
   * */
  public function _addFile() {
    $this->autoRender = FALSE;
    $name = $this->name;
    // 作成するファイル名の指定
    $file_name ="C:/Project/sinclo/socket/webroot/client/{$name}.js";
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
    chmod( $file_name, 0666 );
    echo('Info - ファイル作成完了。 file name:['.$file_name.']');

    $fp = fopen("C:/Project/sinclo/socket/webroot/client/{$name}.js", 'w');
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

    $this->render('/MAgreementLists/index');
  }
}
