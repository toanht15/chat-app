<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/07/11
 * Time: 22:44
 * @property MCompany $MCompany
 */

class RefreshCompanyCoreSettingsShell extends AppShell {
  public $uses = array('MCompany');

  public function refresh()
  {
    $this->log('BEGIN RefreshCompanyCoreSettingsShell::refresh', LOG_INFO, 'refresh');
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

        $companyJsonObj = json_decode($company['MCompany']['core_settings'], TRUE);
        $settingJsonObj = $this->getCoreSettingsByContactTypesId($company['MCompany']['m_contact_types_id']);
        foreach ($settingJsonObj as $key => $value) {
          if (array_key_exists($key, $companyJsonObj)) {
            $this->log('KEY ' . $key . ' is found. NOT override.', LOG_INFO, 'refresh');
          } else {
            $this->log('KEY ' . $key . ' is NOT found. set value : ' . var_export($value, TRUE), LOG_INFO, 'refresh');
            $companyJsonObj[$key] = $value;
          }
        }
        $company['MCompany']['core_settings'] = json_encode($companyJsonObj);
        $this->log('SAVE DATA : '.var_export($company, TRUE), LOG_INFO, 'refresh');
        $this->log('SAVE RESULT : '.var_export($this->MCompany->save($company, false), TRUE), LOG_INFO, 'refresh');
        $this->log('==========================================', LOG_INFO, 'refresh');

      }
      $this->MCompany->commit();
    } catch (Exception $e) {
      $this->MCompany->rollback();
      $this->log('ERROR FOUND !! message => '.$e->getMessage(), LOG_ERROR, 'refresh');
    }
    $this->log('END   RefreshCompanyCoreSettingsShell::refresh', LOG_INFO, 'refresh');
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