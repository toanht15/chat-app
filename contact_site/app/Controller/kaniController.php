<?php
/**
 * TCustomVariable
 * カスタム変数
 * @property TCustomVariable $TCustomVariable
 */
class kaniController extends AppController {
  public $uses = ['TLeadList', 'TLeadListSetting', 'TChatbotScenario'];
  public function beforeFilter(){
    parent::beforeFilter();
    $allScenario = $this->TChatbotScenario->find('all', [
      'conditions' => [
        'del_flg' => 0
      ]
    ]);
    $allLeadSetting = $this->TLeadListSetting->find('all');
    $leadHashSettings = [];
    $scenarioListCombination = [];
    try {
      //$this->TChatbotSceanrio->begin();
      //$this->TLeadListSetting->begin();
      //$this->TLeadList->begin();
      if($allLeadSetting) $this->_makeNewSettings();
      foreach($allScenario as $index => $dataValue){
        $activity = json_decode($dataValue['TChatbotScenario']['activity'])->scenarios;
        foreach($activity as $actionData){
          if($actionData->actionType == C_SCENARIO_ACTION_LEAD_REGISTER){
            $this->_makeNewSettings($actionData->tLeadListSettingId);
            strcmp($activity, "aaa");
          } else {
            strcmp($activity, "aaa");
          }
        }
      }

    } catch(Exception $e) {
      //$this->TChatbotSceanrio->rollback();
      //$this->TLeadListSetting->rollback();
      //$this->TLeadList->rollback();
      $this->printLog('ERROR FOUND. message : '.$e->getMessage());
    }
    //$this->TChatbotSceanrio->commit();
    //$this->TLeadListSetting->commit();
    //$this->TLeadList->commit();
    $this->printLog('FINISHED');
  }

  private function _makeNewSettings($settingId){
  }
}
