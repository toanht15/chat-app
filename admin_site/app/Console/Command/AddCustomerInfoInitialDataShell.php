<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/07/11
 * Time: 22:44
 * @property TCustomerInformationSetting $TCustomerInformationSetting
 */

class AddCustomerInfoInitialDataShell extends AppShell {
  public $uses = array('MCompany','MCustomer','TCustomerInformationSetting');

  public function addAll()
  {
    $this->log('BEGIN AddCustomerInfoInitialDataShell::addAll', LOG_INFO, 'refresh');
    $record = $this->MCompany->find('all',[
      'conditions' => [
        'NOT' => [
          'del_flg' => 1
        ]
      ]
    ]);
    try {
      $this->MCompany->begin();
      foreach ($record as $index => $company) {
        $this->log('==========================================', LOG_INFO, 'refresh');
        $this->log('TARGET : ' . $company['MCompany']['company_name'], LOG_INFO, 'refresh');
        $this->log('KEY    : ' . $company['MCompany']['company_key'], LOG_INFO, 'refresh');
        $this->log('PLAN   : ' . $this->convertContactTypesIdToString($company['MCompany']['m_contact_types_id']), LOG_INFO, 'refresh');
        $this->log('~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~', LOG_INFO, 'refresh');

        $exists = $this->TCustomerInformationSetting->find('first', array(
          'conditions' => array(
            'm_companies_id' => $company['MCompany']['id']
          )
        ));

        if(count($exists) === 0) {
          $this->TCustomerInformationSetting->create();
          $this->TCustomerInformationSetting->set(array(
            'm_companies_id' => $company['MCompany']['id'],
            'item_name' => '会社名',
            'input_type' => 1,
            'show_realtime_monitor_flg' => 1,
            'show_send_mail_flg' => 0,
            'sync_custom_variable_flg' => 0,
            't_custom_variable_flg' => 0,
            'sort' => 1
          ));
          $this->TCustomerInformationSetting->save();

          $this->TCustomerInformationSetting->create();
          $this->TCustomerInformationSetting->set(array(
            'm_companies_id' => $company['MCompany']['id'],
            'item_name' => '名前',
            'input_type' => 1,
            'show_realtime_monitor_flg' => 1,
            'show_send_mail_flg' => 0,
            'sync_custom_variable_flg' => 0,
            't_custom_variable_flg' => 0,
            'sort' => 2
          ));
          $this->TCustomerInformationSetting->save();

          $this->TCustomerInformationSetting->create();
          $this->TCustomerInformationSetting->set(array(
            'm_companies_id' => $company['MCompany']['id'],
            'item_name' => '電話番号',
            'input_type' => 1,
            'show_realtime_monitor_flg' => 0,
            'show_send_mail_flg' => 0,
            'sync_custom_variable_flg' => 0,
            't_custom_variable_flg' => 0,
            'sort' => 3
          ));
          $this->TCustomerInformationSetting->save();

          $this->TCustomerInformationSetting->create();
          $this->TCustomerInformationSetting->set(array(
            'm_companies_id' => $company['MCompany']['id'],
            'item_name' => 'メールアドレス',
            'input_type' => 1,
            'show_realtime_monitor_flg' => 0,
            'show_send_mail_flg' => 0,
            'sync_custom_variable_flg' => 0,
            't_custom_variable_flg' => 0,
            'sort' => 4
          ));
          $this->TCustomerInformationSetting->save();

          $this->TCustomerInformationSetting->create();
          $this->TCustomerInformationSetting->set(array(
            'm_companies_id' => $company['MCompany']['id'],
            'item_name' => 'メモ',
            'input_type' => 2,
            'show_realtime_monitor_flg' => 0,
            'show_send_mail_flg' => 0,
            'sync_custom_variable_flg' => 0,
            't_custom_variable_flg' => 0,
            'sort' => 5
          ));
          $this->TCustomerInformationSetting->save();
          $this->TCustomerInformationSetting->commit();
          $this->log('==========================================', LOG_INFO, 'refresh');
          $this->log('SAVE RESULT : OK', LOG_INFO, 'refresh');
          $this->log('==========================================', LOG_INFO, 'refresh');
        } else {
          $this->log('==========================================', LOG_INFO, 'refresh');
          $this->log('SAVE DATA is found. skipping... ', LOG_INFO, 'refresh');
          $this->log('==========================================', LOG_INFO, 'refresh');
        }
      }
    } catch (Exception $e) {
      $this->TCustomerInformationSetting->rollback();
      $this->log('ERROR FOUND !! message => '.$e->getMessage(), LOG_ERR, 'refresh');
    }
    $this->log('END   AddCustomerInfoInitialDataShell::addAll', LOG_INFO, 'refresh');
  }

  public function replaceAllOldIf() {
    $this->log('BEGIN AddCustomerInfoInitialDataShell::replaceAllOldIf', LOG_INFO, 'refresh');
    $record = $this->MCustomer->find('all',[]);
    try {
      $ifKeyMap = array(
        'company' => '会社名',
        'name' => '名前',
        'tel' => '電話番号',
        'mail' => 'メールアドレス',
        'memo' => 'メモ'
      );
      $this->MCustomer->begin();
      foreach ($record as $index => $customer) {
        $this->log('==========================================', LOG_INFO, 'refresh');
        $this->log('TARGET : ' . $customer['MCustomer']['m_companies_id'], LOG_INFO, 'refresh');
        $this->log('DATA    : ' . $customer['MCustomer']['informations'], LOG_INFO, 'refresh');
        $this->log('~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~', LOG_INFO, 'refresh');

        $oldData = json_decode($customer['MCustomer']['informations'], TRUE);
        $newData = array();
        $oldIfFound = false;
        foreach($oldData as $k => $v) {
          if(array_key_exists($k, $ifKeyMap)) {
            $oldIfFound = true;
            $newData[$ifKeyMap[$k]] = $v;
          } else {
            $newData[$k] = $v;
          }
        }

        if($oldIfFound) {
          $customer['MCustomer']['informations'] = json_encode($newData);
          $this->MCustomer->set($customer);
          $this->MCustomer->save();
          $this->log('==========================================', LOG_INFO, 'refresh');
          $this->log('SAVE RESULT : OK', LOG_INFO, 'refresh');
          $this->log('==========================================', LOG_INFO, 'refresh');
        } else {
          $this->log('==========================================', LOG_INFO, 'refresh');
          $this->log('SAVE DATA is found. skipping... ', LOG_INFO, 'refresh');
          $this->log('==========================================', LOG_INFO, 'refresh');
        }
      }
      $this->MCustomer->commit();
    } catch (Exception $e) {
      $this->TCustomerInformationSetting->rollback();
      $this->log('ERROR FOUND !! message => '.$e->getMessage(), LOG_ERR, 'refresh');
    }
    $this->log('END   AddCustomerInfoInitialDataShell::replaceAllOldIf', LOG_INFO, 'refresh');
  }

  private function convertContactTypesIdToString($m_contact_types_id) {
    $val = "";
    switch($m_contact_types_id) {
      case C_CONTRACT_FULL_PLAN_ID:
        $val = 'フルプラン';
        break;
      case C_CONTRACT_CHAT_PLAN_ID:
        $val = 'スタンダードプラン';
        break;
      case C_CONTRACT_SCREEN_SHARING_ID:
        $val = 'シェアリングプラン';
        break;
      case C_CONTRACT_CHAT_BASIC_PLAN_ID:
        $val = 'ベーシックプラン';
        break;
    }
    return $val;
  }

  private function getCoreSettingsByContactTypesId($m_contact_types_id) {
    $val = "";
    switch($m_contact_types_id) {
      case C_CONTRACT_FULL_PLAN_ID:
        $val = C_CONTRACT_FULL_PLAN;
        break;
      case C_CONTRACT_CHAT_PLAN_ID:
        $val = C_CONTRACT_CHAT_PLAN;
        break;
      case C_CONTRACT_SCREEN_SHARING_ID:
        $val = C_CONTRACT_SCREEN_SHARING_PLAN;
        break;
      case C_CONTRACT_CHAT_BASIC_PLAN_ID:
        $val = C_CONTRACT_CHAT_BASIC_PLAN;
        break;
    }
    return json_decode($val, TRUE);
  }
}