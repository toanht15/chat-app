<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/02/20
 * Time: 19:07
 * @property TAutoMessage $TAutoMessage
 */

class AddCustomSpWidgetSettingsShell extends AppShell
{
  const LOG_INFO = 'batch-info';
  const LOG_ERROR = 'batch-error';

  public $uses = array('MWidgetSetting','MCompany');

  /**
   * MailSenderComponent.phpの呼び出し
   * @see https://qiita.com/colorrabbit/items/d302cc0eeec3adc18456
   */
  public function startup() {
    parent::startup();
  }

  /**
   * オートメッセージの
   */
  public function addCustomSetting() {
    $targetCompaniesId = array(154, 175, 256, 259, 267, 273, 276, 278, 279, 280, 283, 285, 286, 287, 288, 289, 290, 291, 292, 295, 296, 297, 298, 299, 300, 301, 302, 303, 304, 305, 306, 307, 309, 311, 312, 313, 314, 316, 321, 324, 332, 348, 508, 509, 510, 511, 512, 513, 514, 515, 516, 517, 518, 424, 426, 427, 428, 429, 430, 431, 432, 433, 434, 435, 436, 437, 438, 439);
    $allData = $this->MWidgetSetting->find('all', array(
      'conditions' => array(
        'm_companies_id' => $targetCompaniesId)
    ));
    try {
      $this->MWidgetSetting->begin();
      foreach ($allData as $index => $data) {
        $company = $this->MCompany->find('first', array(
          'conditions' => array(
            'id' => $data['MWidgetSetting']['m_companies_id']
          ))
        );
        $this->printLog("====================================================");
        $this->printLog("BEGIN UPDATE : ".$company['MCompany']['company_name']);
        $this->printLog("BEFORE : ".$data['MWidgetSetting']['style_settings']);
        $this->printLog("~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~");
        $jsonObj = json_decode($data['MWidgetSetting']['style_settings'], TRUE);
        $jsonObj['spBannerPosition'] = '3'; // 右中央
        $jsonObj['spWidgetViewPattern'] = '3'; // ２段階表示（最大化＜＝＞小さなバナー表示）
        $jsonObj['spBannerVerticalPositionFromTop'] = "50%"; // 上から50%
        $data['MWidgetSetting']['style_settings'] = json_encode($jsonObj);
        $this->MWidgetSetting->create();
        $this->MWidgetSetting->set($data['MWidgetSetting']);
        $this->MWidgetSetting->save();
        $this->printLog("AFTER : ".$data['MWidgetSetting']['style_settings']);
        $this->printLog("====================================================");
      }
    } catch(Exception $e) {
      $this->MWidgetSetting->rollback();
      $this->printLog('ERROR FOUND. message : '.$e->getMessage());
    }
    $this->MWidgetSetting->commit();
    $this->printLog('FINISHED');
  }

  private function printLog($msg) {
    $this->log($msg, self::LOG_INFO);
    $this->out($msg);
  }
}