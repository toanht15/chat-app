<?php
/**
 * MChatSettingsController controller.
 * チャット基本設定
 */
class MChatSettingsController extends AppController {
  public $uses = ['MChatSetting', 'MUser'];

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
    }
    // 表示処理
    else {
      // 対象企業のチャット基本設定データを取得し、セットする
      $this->request->data = $this->MChatSetting->find('first', ['conditions' => [
        'm_companies_id' => $this->userInfo['MCompany']['id']
      ]]);
    }

    $this->set('mUserList', $this->MUser->getUser()); // ユーザーのリスト
    $this->set('scFlgOpt', [C_SC_DISABLED => '利用しない', C_SC_ENABLED => '利用する']); // 同時対応数上限設定のラベルリスト
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
    $this->log($saveData,LOG_DEBUG);

    // チャット基本設定のバリデーション結果を変数に渡す
    $ret = $this->MChatSetting->validates();
    $ret = '';
    $this->log('ret',LOG_DEBUG);
    $this->log($ret,LOG_DEBUG);

    // ユーザーへの同時対応数設定を複数行一括保存が出来るように加工する
    $saveData = $this->_makeSaveUserData($inputData);
    $userRet = true;
    if ( !empty($saveData) ) {
      $userRet = $this->MUser->saveAll($saveData, ['validate' => 'only']);
      $this->log('userRet',LOG_DEBUG);
      $this->log($userRet,LOG_DEBUG);
      $this->log($saveData,LOG_DEBUG);
    }

    // ユーザーデータの一括バリデーションチェック
    // 何れかのバリデーションチェックでfalseだった場合は処理を中止する
    if ( !$userRet || !$ret ) {
    $this->log('valivli',LOG_DEBUG);
      return false;
    }

    // 保存処理
    if ( $this->MChatSetting->save() && $this->MUser->saveAll($saveData) ) {
      // 双方コミットし、true を返す
      $this->MChatSetting->commit();
      $this->MUser->commit();
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
    // 同時対応数上限機能を有効にしている場合のみ保存データを作成する
    if ( intval($inputData['MChatSetting']['sc_flg']) === intval(C_SC_ENABLED) ) {
      // ユーザー単位でのループ
      foreach($inputData['MUser'] as $key => $val){
        // 最新のユーザー情報の取得し、保存用配列に格納
        $saveData[$key] = $this->MUser->getUser($key);
        // 最新のユーザー情報が保持しているJSONデータをデコードし配列化
        $settings = (array)json_decode($saveData[$key]['MUser']['settings']);
        // POSTデータが空の場合、0に置換
        $settings['sc_num'] = ( !empty($val['sc_num']) ) ? $val['sc_num'] : 0;
        // POSTデータを含めた配列をJSON文字列にし、保存用配列に格納
        $saveData[$key]['MUser']['settings'] = $this->jsonEncode($settings);
        // バリデーションチェック用にセット
        $saveData[$key]['MUser']['sc_num'] = $settings['sc_num'];
      }
    }
    return $saveData;
  }

}