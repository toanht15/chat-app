<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/12/21
 * Time: 11:44
 * @property MIpFilterSetting $MIpFilterSetting
 */


class MSecuritySettingsController extends AppController
{
  public $uses = ['MIpFilterSetting'];

  public function beforeFilter() {
    parent::beforeFilter();
    $this->set('title_for_layout', 'セキュリティ設定');
  }

  /**
   * POSTされる"MSecuritySetting"はダミーのmodelで、このControllerで各テーブルに分割してデータを管理する設計としている。
   */
  public function edit() {
    if($this->request->is('post')) {
      $this->upsert();
    } else {
      $this->renderView();
    }
    $this->set('typeSelect', Configure::read('securityEnableLoginIpFilterSetting'));
  }

  private function upsert() {
    try {
      if (empty($this->request->data['MSecuritySettings']['id'])) {
        $this->insert();
      } else {
        $this->update();
      }
    } catch (Exception $e) {
      $this->set('alertMessage', ['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
      return;
    }
    $this->set('alertMessage', ['type' => C_MESSAGE_TYPE_SUCCESS, 'text'=>Configure::read('message.const.saveSuccessful')]);
  }

  private function renderView() {
    $this->request->data = $this->getMySecuritySettings();
  }

  private function insert() {
    try {
      $this->request->data['MIpFilterSetting']['m_companies_id'] = $this->userInfo['MCompany']['id'];
      $this->MIpFilterSetting->create();
      $this->MIpFilterSetting->begin();
      $insertData = [
        'm_companies_id' => $this->userInfo['MCompany']['id'],
        'active_flg' => $this->getIpFilterType(),
        'filter_type' => $this->getIpFilterType(),
        'ips' => $this->getIpSetting()
      ];

      $this->MIpFilterSetting->set($insertData);
      $this->doValidate();
      if (!empty($errors)) {
        $this->MIpFilterSetting->rollback();
        throw new Exception($errors);
      }
      if (!$this->MIpFilterSetting->save()) {
        $this->MIpFilterSetting->rollback();
        throw new Exception('DB登録時にエラーが発生しました。');
      }
      $this->MIpFilterSetting->commit();
      $this->request->data['MSecuritySettings']['id'] = $this->MIpFilterSetting->getLastInsertId();
    } catch(Exception $e) {
      $this->MIpFilterSetting->rollback();
      if($this->MIpFilterSetting->validationErrors) {
        $this->set('errors', $this->MIpFilterSetting->validationErrors);
      }
      throw $e;
    }
  }

  private function update() {
    try {
      $updateData = $this->MIpFilterSetting->find('first', array(
          "conditions" => array( 'm_companies_id' => $this->userInfo['MCompany']['id'])
      ));
      if ($updateData['MIpFilterSetting']['m_companies_id'] !== $this->userInfo['MCompany']['id']) {
        throw new Exception('不正な更新処理です。');
      }
      $updateData['MIpFilterSetting']['active_flg'] = $this->getActiveFlg($this->request->data['MSecuritySettings']['ip_filter_enabled']);
      $updateData['MIpFilterSetting']['filter_type'] = $this->getIpFilterType();
      $updateData['MIpFilterSetting']['ips'] = $this->getIpSetting();

      $this->MIpFilterSetting->set($updateData);
      $this->doValidate();
      $this->MIpFilterSetting->save();
    } catch(Exception $e) {
      $this->MIpFilterSetting->rollback();
      if($this->MIpFilterSetting->validationErrors) {
        $this->set('errors', $this->MIpFilterSetting->validationErrors);
      }
      throw $e;
    }
  }

  private function getMySecuritySettings() {
    $val = $this->MIpFilterSetting->findByMCompaniesId($this->userInfo['MCompany']['id']);
    if(empty($val)) {
      $val = $this->getDefaultTransferSetting();
    }
    return [
        'MSecuritySettings' => [
            'id' => (!empty($val['MIpFilterSetting']['id'])) ? $val['MIpFilterSetting']['id'] : "",
            'ip_filter_enabled' => $val['MIpFilterSetting']['filter_type'],
            'ip_filter_whitelist' => (strcmp($val['MIpFilterSetting']['filter_type'], 1) === 0) ? $val['MIpFilterSetting']['ips'] : "",
            'ip_filter_blacklist' => (strcmp($val['MIpFilterSetting']['filter_type'], 2) === 0) ? $val['MIpFilterSetting']['ips'] : ""
        ]
    ];
  }

  private function getDefaultTransferSetting() {
    return [
      'MIpFilterSetting' => [
        'active_flg' => 0,
        'filter_type' => 0,
        'ips' => ''
      ]
    ];
  }

  private function doValidate() {
    if(!$this->MIpFilterSetting->validates()) {
      //NG
      throw new InvalidArgumentException('バリデーションエラー');
    }
    // OK
  }

  private function getIpFilterType() {
    $filterType = 0;
//    if(!empty($this->request->data['MSecuritySettings'])) {
//      $whitelistData = $this->request->data['MSecuritySettings']['ip_filter_whitelist'];
//      $blacklistData = $this->request->data['MSecuritySettings']['ip_filter_blacklist'];
//      if(!empty($whitelistData) && !empty($blacklistData)) {
//        throw new BadRequestException('ホワイトリストとブラックリストはいずれかの設定のみ可能です。');
//      } else if(!empty($whitelistData) && empty($blacklistData)) {
//        $filterType = 1; // FIXME 定数化
//      } else if(empty($whitelistData) && !empty($blacklistData)) {
//        $filterType = 2; // FIXME 定数化
//      } else if(strcmp($this->request->data['MSecuritySettings']['ip_filter_enabled'], 0) === 0) {
//        throw new BadRequestException('設定を有効にする場合はホワイトリストまたはブラックリストのいずれかの指定が必須です。');
//      } else {
//        // 設定無効状態なので未設定として扱う
//      }
//    }
    $filterType = $this->request->data['MSecuritySettings']['ip_filter_enabled'];
    return $filterType;
  }

  private function getIpSetting() {
    $ips = "";
    $filterType = $this->getIpFilterType();
    if(strcmp($filterType, 1) === 0) {
      $ips = $this->request->data['MSecuritySettings']['ip_filter_whitelist'];
    } else if(strcmp($filterType, 2) === 0) {
      $ips = $this->request->data['MSecuritySettings']['ip_filter_blacklist'];
    }
    return $ips;
  }

  /**
   * @return mixed
   */
  private function getActiveFlg($ip_filter_enabled)
  {
    $val = 0;
    if(strcmp($ip_filter_enabled, 1) === 0 || strcmp($ip_filter_enabled, 2) === 0) {
      $val = 1;
    }
    return $val;
  }
}