<?php
/**
 * MChatSettingsController controller.
 * チャット基本設定
 */
class MChatSettingsController extends AppController {
  public $uses = ['MChatSetting', 'MUser','MOperatingHour'];
  public $components = ['NodeSettingsReload'];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', 'チャット基本設定');
  }

  /* *
   * 基本設定ページ
   * @return void
   * */
  public function index() {

    // 更新処理
    if ( $this->request->is('post') ) {
      // 保存処理関数へ
      if ($this->_update($this->request->data)){
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
        $this->redirect(['controller' => $this->name, 'action' => 'index']);
      }
      else {
        $operatingHourData = $this->MOperatingHour->find('first', ['conditions' => [
          'm_companies_id' => $this->userInfo['MCompany']['id']
        ]]);
        if(empty($operatingHourData)) {
          $operatingHourData['MOperatingHour']['active_flg'] = 2;
        }
        $this->set('operatingHourData',$operatingHourData['MOperatingHour']['active_flg']);
        $data = json_decode($this->request->data['MChatSetting']['initial_notification_message'],true);
        foreach($data as $key => $value){
          $this->request->data['MChatSetting']['initial_notification_message'.($key+1)] = $value['message'];
          $this->request->data['MChatSetting']['seconds'.($key+1)] = $value['seconds'];
        }
        $this->set('in_flg',$this->request->data['MChatSetting']['in_flg']);
        $this->set('data',$data);
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
      }
    }
    // 表示処理
    else {
      // 対象企業のチャット基本設定データを取得し、セットする
      $this->request->data = $this->MChatSetting->find('first', ['conditions' => [
        'm_companies_id' => $this->userInfo['MCompany']['id']
      ]]);
      if(!empty($this->request->data['MChatSetting']['initial_notification_message']) &&
        $this->coreSettings[C_COMPANY_USE_CHATCALLMESSAGES]) {
        $data = json_decode($this->request->data['MChatSetting']['initial_notification_message'],true);
        foreach($data as $key => $value){
          $this->request->data['MChatSetting']['initial_notification_message'.($key+1)] = $value['message'];
          $this->request->data['MChatSetting']['seconds'.($key+1)] = $value['seconds'];
        }
        $this->set('in_flg',$this->request->data['MChatSetting']['in_flg']);
        $this->set('data',$data);
      }
      else {
        //デフォルト値設定
        $data[0]['seconds'] = 0;
        $data[0]['message'] = '';
        $this->request->data['MChatSetting']['initial_notification_message1'] = 'ただいま担当の者を呼び出しておりますので、そのままでお待ちください。';
        $this->request->data['MChatSetting']['seconds1'] = 1;
        $this->request->data['MChatSetting']['in_flg'] = 2;
        $this->set('in_flg',2);
        $this->set('data',$data);
      }
      $operatingHourData = $this->MOperatingHour->find('first', ['conditions' => [
        'm_companies_id' => $this->userInfo['MCompany']['id']
      ]]);
      if(empty($operatingHourData)) {
        $operatingHourData['MOperatingHour']['active_flg'] = 2;
      }
      $this->set('operatingHourData',$operatingHourData['MOperatingHour']['active_flg']);
      $this->set('notificationArrayNumber',count($data));

      //デフォルト設定
      if(!empty($this->request->data['MChatSetting']['sorry_message'])) {
        $saveData = $this->request->data;
        if($operatingHourData['MOperatingHour']['active_flg'] == 1){
          $this->request->data['MChatSetting']['outside_hours_sorry_message'] = $this->request->data['MChatSetting']['sorry_message'];
          $saveData['MChatSetting']['outside_hours_sorry_message'] = $this->request->data['MChatSetting']['sorry_message'];
        }
        else {
          $saveData['MChatSetting']['outside_hours_sorry_message'] = $this->request->data['MChatSetting']['sorry_message'];
        }
        if($this->request->data['MChatSetting']['sc_flg'] == 1) {
          $this->request->data['MChatSetting']['wating_call_sorry_message'] = $this->request->data['MChatSetting']['sorry_message'];
          $saveData['MChatSetting']['wating_call_sorry_message'] = $this->request->data['MChatSetting']['sorry_message'];
        }
        else {
          $saveData['MChatSetting']['wating_call_sorry_message'] = $this->request->data['MChatSetting']['sorry_message'];
        }
        $this->request->data['MChatSetting']['no_standby_sorry_message'] = $this->request->data['MChatSetting']['sorry_message'];
        $saveData['MChatSetting']['no_standby_sorry_message'] = $this->request->data['MChatSetting']['sorry_message'];
        $saveData['MChatSetting']['sorry_message'] = "";

        $this->MChatSetting->set($saveData);
        if ( $this->MChatSetting->save($saveData,true) ) {
          $this->MChatSetting->commit();
        }
        else {
          $this->MChatSetting->rollback();
        }
      }
    }

    $this->set('mUserList', $this->MUser->getUser()); // ユーザーのリスト
    $this->set('scFlgOpt', [C_SC_DISABLED => '利用しない', C_SC_ENABLED => '利用する']); // 同時対応数上限設定のラベルリスト
    $this->set('scLoginStatusOpt', [C_SC_AWAY => '離席中', C_SC_WAITING => '待機中']); // 初期ステータス限設定のラベルリスト
  } // index

  /**
   * _update　保存処理を行う関数
   * @param $inputData array POSTされてきたデータ
   * @return boolean true/false 保存処理結果
   * */
  private function _update($inputData){
    // トランザクション処理の開始
    $this->MChatSetting->begin();
    $this->MUser->begin();

    // チャット設定が初回の場合
    if ( empty($inputData['MChatSetting']['id']) ) {
      // 企業IDをセットする
      $inputData['MChatSetting']['m_companies_id'] = $this->userInfo['MCompany']['id'];
      // 登録処理とする
      $this->MChatSetting->create();
    }
    // 保存用変数にチャット基本設定のみセット
    $saveData['MChatSetting'] = $inputData['MChatSetting'];

    //sorry_messageデフォルト値削除
    $saveData['MChatSetting']['sorry_message'] = "";

    // 同時対応上限数を利用しない場合
    if ( strcmp($saveData['MChatSetting']['sc_flg'], C_SC_DISABLED) === 0 ) {
      unset($saveData['MChatSetting']['sc_default_num']);
    }
    // 同時対応上限数を利用する場合
    else {
      // 基本同時対応上限数が空だった場合は0を代入する
      if ( empty($saveData['MChatSetting']['sc_default_num']) ) {
        $saveData['MChatSetting']['sc_default_num'] = 0;
      }
    }

    // バリデーションの為に、保存データの入った変数をモデルにセットする
    $this->MChatSetting->set($saveData);

    // チャット基本設定のバリデーション結果を変数に渡す
    $ret = $this->MChatSetting->validates();

    //チャット呼出中メッセージバリデーション
    $inRet = true;
    if($saveData['MChatSetting']['in_flg'] == 1) {
      $data = json_decode($this->request->data['MChatSetting']['initial_notification_message'],true);
      foreach($data as $key => $value){
        if(empty($this->request->data['MChatSetting']['initial_notification_message'.($key+1)]) ||
          mb_strlen($this->request->data['MChatSetting']['initial_notification_message'.($key+1)]) > 300) {
          $inRet = false;
        }
      }
    }

    // ユーザーへの同時対応数設定を複数行一括保存が出来るように加工する
    $saveData = $this->_makeSaveUserData($inputData);
    $userRet = true;
    if ( !empty($saveData) ) {
      $userRet = $this->MUser->saveAll($saveData, ['validate' => 'only']);
    }

    // ユーザーデータの一括バリデーションチェック
    // 何れかのバリデーションチェックでfalseだった場合は処理を中止する
    if ( !$userRet || !$ret || !$inRet) {
      return false;
    }
    $this->log($inputData,LOG_DEBUG);
    // 保存処理
    if ( $this->MChatSetting->save() && $this->MUser->saveAll($saveData)) {
      // 双方コミットし、true を返す
      $this->MChatSetting->commit();
      $this->MUser->commit();
      NodeSettingsReloadComponent::reloadChatSettings($this->userInfo['MCompany']['company_key']);
      return true;
    }
    else {
      // 双方ロールバックし、false を返す
      $this->MChatSetting->rollback();
      $this->MUser->rollback();
      return false;
    }
  } // _update

  /**
   * _makeSaveUserData 保存用ユーザーデータ作成
   * 　ユーザーごとの同時対応数を settings というカラムに保存する。
   * 　対象のカラムはJSON形式の為、"sc_num" というキーとして対象の値を保存する。
   * @param $inputData array POSTデータ
   * @return $saveData array 保存用データ
   * */
  private function _makeSaveUserData($inputData){
    $saveData = [];
    foreach($inputData['MUser'] as $key => $val){
      $saveData[$key] = $this->MUser->getUser($key);
      $settings = (array)json_decode($saveData[$key]['MUser']['settings']);
      if ( intval($inputData['MChatSetting']['sc_flg']) === intval(C_SC_ENABLED) ) {
        $settings['sc_num'] = ( !empty($val['sc_num']) ) ? $val['sc_num'] : 0;
        $saveData[$key]['MUser']['sc_num'] = $settings['sc_num'];
      }

      $settings['login_default_status'] = ( !empty($val['sc_login_status']) ) ? $val['sc_login_status'] : C_SC_AWAY;
      $saveData[$key]['MUser']['settings'] = $this->jsonEncode($settings);
    }

    return $saveData;
  }

}
