<?php
/**
 * OperatingHoursController
 * 営業時間設定
 */
class MOperatingHoursController extends AppController {
  public $uses = ['MOperatingHour','MWidgetSetting','TAutoMessage'];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', '営業時間設定');
  }

  /* *
   * 一覧画面
   * @return void
   * */
  public function index() {
    $operatingHourData = $this->MOperatingHour->find('first', ['conditions' => [
      'm_companies_id' => $this->userInfo['MCompany']['id']
    ]]);
    //ウィジェット情報
    $widgetData = $this->MWidgetSetting->find('first', ['conditions' => [
      'm_companies_id' => $this->userInfo['MCompany']['id']
    ]]);
    //オートメッセージ情報
    $autoMessageData = $this->TAutoMessage->find('all', ['conditions' => [
      'm_companies_id' => $this->userInfo['MCompany']['id'],
      'active_flg' => 0,
      'del_flg' => 0
    ]]);
    $check = '';
    foreach($autoMessageData as $v){
      //オートメッセージの条件に営業時間設定が入っているかチェック
      if(!empty(json_decode($v['TAutoMessage']['activity'],true)['conditions'][10])) {
        $check = 'included';
      }
    }

    if($this->request->is('post')) {
      if(!$this->coreSettings[C_COMPANY_USE_OPERATING_HOUR]) {
        $this->redirect("/");
      }
      $saveData = $this->MOperatingHour->read(null, $operatingHourData['MOperatingHour']['id']);

      if(isset($this->request->data['MOperatingHour']['outputData'])) {
        //営業時間 「毎日」validateチェック
        $time_settings = json_decode($this->request->data['MOperatingHour']['outputData'],true);
        foreach($time_settings['everyday'] as $key => $v) {
          foreach($v as $key => $v2) {
            if( ($v2['start'] != "" && !preg_match(C_MATCH_RULE_TIME, $v2['start']))
            || ($v2['end'] != "" && !preg_match(C_MATCH_RULE_TIME, $v2['end']))) {
              $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
              $this->redirect(['controller' => $this->name, 'action' => 'index']);
              return;
            }
          }
        }
        //営業時間 「平日・週末」validateチェック
        foreach($time_settings['weekly'] as $key => $v) {
          foreach($v as $key => $v2) {
            if( ($v2['start'] != "" && !preg_match(C_MATCH_RULE_TIME, $v2['start']))
            || ($v2['end'] != "" && !preg_match(C_MATCH_RULE_TIME, $v2['end']))) {
              $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
              $this->redirect(['controller' => $this->name, 'action' => 'index']);
              return;
            }
          }
        }
      }
      $saveData['MOperatingHour']['active_flg'] = $this->request->data['MOperatingHour']['active_flg'];

      //営業時間設定を利用する場合
      if($this->request->data['MOperatingHour']['active_flg'] == 1) {
        $saveData['MOperatingHour']['time_settings'] = $this->request->data['MOperatingHour']['outputData'];
        $saveData['MOperatingHour']['type'] = $this->request->data['MOperatingHour']['type'];
      }
      $this->MOperatingHour->set($saveData);
      $this->MOperatingHour->begin();
        if ( $this->MOperatingHour->save() ) {
          $this->MOperatingHour->commit();
          $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
          $this->redirect(['controller' => $this->name, 'action' => 'index']);
        }
        else {
          $this->MOperatingHour->rollback();
          $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
          $this->redirect(['controller' => $this->name, 'action' => 'index']);
        }
      $this->set('operatingHourData',$saveData);
    }
    else {
      //デフォルト設定
      if(empty($operatingHourData)) {
        $saveData['MOperatingHour']['m_companies_id'] = $this->userInfo['MCompany']['id'];
        $saveData['MOperatingHour']['time_settings'] = "{\"everyday\":{\"mon\":[{\"start\":\"09:00\",\"end\":\"18:00\"}],\"tue\":[{\"start\":\"09:00\",\"end\":\"18:00\"}],\"wed\":[{\"start\":\"09:00\",\"end\":\"18:00\"}],\"thu\":[{\"start\":\"09:00\",\"end\":\"18:00\"}],\"fri\":[{\"start\":\"09:00\",\"end\":\"18:00\"}],\"sat\":[{\"start\":\"\",\"end\":\"\"}],\"sun\":[{\"start\":\"\",\"end\":\"\"}],\"pub\":[{\"start\":\"\",\"end\":\"\"}]},\"weekly\":{\"week\":[{\"start\":\"09:00\",\"end\":\"18:00\"}],\"weekend\":[{\"start\":\"\",\"end\":\"\"}],\"weekpub\":[{\"start\":\"\",\"end\":\"\"}]}}";
        $saveData['MOperatingHour']['active_flg'] = C_ACTIVE_DISABLED;
        $saveData['MOperatingHour']['type'] = 1;
        $this->MOperatingHour->create();
        $this->MOperatingHour->set($saveData);
        $this->MOperatingHour->begin();
        if ( $this->MOperatingHour->save() ) {
          $this->MOperatingHour->commit();
          $this->redirect(['controller' => $this->name, 'action' => 'index']);
        }
        else {
          $this->MOperatingHour->rollback();
          $this->redirect(['controller' => $this->name, 'action' => 'index']);
        }
        $this->set('operatingHourData',$saveData);
      }
      //ページ読み込み時
      else {
        $this->request->data['MOperatingHour']['active_flg'] = $operatingHourData['MOperatingHour']['active_flg'];
        $this->request->data['MOperatingHour']['type'] = $operatingHourData['MOperatingHour']['type'];
        $days = array(
          0 => 'mon',
          1 => 'tue',
          2 => 'wed',
          3 => 'thu',
          4 => 'fri',
          5 => 'sat',
          6 => 'sun',
          7 => 'pub'
        );
        $weekly = array(
          0 => 'week',
          1 => 'weekend',
          2 => 'weekpub'
        );
        $this->set('days',$days);
        $this->set('weekly',$weekly);
      }
      $this->set('check',$check);
      $this->set('operatingHourData',$operatingHourData);
      $this->set('widgetData',$widgetData['MWidgetSetting']['display_type']);
    }
    $this->set('scFlgOpt', [C_SC_DISABLED => '利用しない', C_SC_ENABLED => '利用する']); // 営業時間設定のラベルリスト
  }

  /* *
   * 更新(モーダル)画面
   * @return void
   * */
  public function remoteOpenEntryForm() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $date = $this->request->data['day'];
    if($this->request->data['day'] == 'mon') {
      $this->request->data['day'] = '月曜日';
    }
    else if($this->request->data['day'] == 'tue') {
      $this->request->data['day'] = '火曜日';
    }
    else if($this->request->data['day'] == 'wed') {
      $this->request->data['day'] = '水曜日';
    }
    else if($this->request->data['day'] == 'thu') {
      $this->request->data['day'] = '木曜日';
    }
    else if($this->request->data['day'] == 'fri') {
      $this->request->data['day'] = '金曜日';
    }
    else if($this->request->data['day'] == 'sat') {
      $this->request->data['day'] = '土曜日';
    }
    else if($this->request->data['day'] == 'sun') {
      $this->request->data['day'] = '日曜日';
    }
    else if($this->request->data['day'] == 'pub') {
      $this->request->data['day'] = '祝日';
    }
    else if($this->request->data['day'] == 'week') {
      $this->request->data['day'] = '平日';
    }
    else if($this->request->data['day'] == 'weekend') {
      $this->request->data['day'] = '週末';
    }

    if($this->request->data['day'] != '平日' && $this->request->data['day'] != '週末' && $this->request->data['day'] != 'weekpub') {
      $days = array(
        0 => 'sun',
        1 => 'mon',
        2 => 'tue',
        3 => 'wed',
        4 => 'thu',
        5 => 'fri',
        6 => 'sat',
        7 => 'pub');
    }
    else {
      $days = array(
        0 => 'week',
        1 => 'weekend',
        2 => 'weekpub'
        );
    }
    if($this->request->data['day'] == 'weekpub') {
      $this->request->data['day'] = '祝日';
    }

    $this->set('dayOfWeek', $this->request->data['day']);
    $this->set('date', $date);
    $this->set('days',$days);
    $this->set('data', $this->request->data['timeData']);
    $this->set('jsonData', $this->request->data['jsonData']);
    $this->set('type', $this->request->data['dayType']);
    //二重操作防止
    $this->render('/Elements/MOperatingHours/remoteEntry');
  }
}
