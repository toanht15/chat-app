<?php
/**
 * MtrialController controller.
 * 無料トライアル登録画面
 */
class MtrialController extends AppController {
  public $uses = ['MChatSetting', 'MUser','MOperatingHour'];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', '無料トライアル登録画面');
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
      }
    }
    // 表示処理
    else {
      // 対象企業のチャット基本設定データを取得し、セットする
      $this->request->data = $this->MChatSetting->find('first', ['conditions' => [
        'm_companies_id' => $this->userInfo['MCompany']['id']
      ]]);

      $operatingHourData = $this->MOperatingHour->find('first', ['conditions' => [
        'm_companies_id' => $this->userInfo['MCompany']['id']
      ]]);
      if(empty($operatingHourData)) {
        $operatingHourData['MOperatingHour']['active_flg'] = 2;
      }
      $this->set('operatingHourData',$operatingHourData['MOperatingHour']['active_flg']);

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
  }

  public function thanks() {

  }
}
