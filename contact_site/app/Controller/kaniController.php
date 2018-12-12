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


    $leadCompanies = $this->TLeadListSetting->find('list', [
      'fields' => 'm_companies_id',
      'group' => 'm_companies_id'
    ]);

    try {
      //$this->TChatbotScenario->begin();
      //$this->TLeadListSetting->begin();
      //$this->TLeadList->begin();

      foreach($leadCompanies as $companyId){
        // リード設定を保持している会社毎に洗い替えを行う(別会社の同一リードリスト名の場合を考慮して)

        // [[マージ先id] => ハッシュ値, 項目名], ...]　のセット配列を作る
        $hashMaster = $this->_makeHashMaster($this->_getGroupedLeadListSettings($companyId));
        // マージ先id、マージされるidのリストを作る
        $mergeIdList = $this->_setIdList($hashMaster, $companyId);
        // マージ先idに、ハッシュ値を全て寄せる
        $hashMaster = $this->_checkParentSettings($hashMaster, $mergeIdList, $companyId);
        // リードリスト情報の値を置き換える（ハッシュ値：値）
        $this->_replaceLeadListData($hashMaster, $mergeIdList, $companyId);
        // シナリオ設定の値を置き換える（ハッシュ値：変数名）
        $this->_replaceScenarioData($hashMaster, $mergeIdList, $companyId);
        // リードリスト設定の値を置き換える（ハッシュ値：項目名）
        $this->_replaceLeadListSettingData($hashMaster, $mergeIdList, $companyId);

      }

    } catch(Exception $e) {
      //$this->TChatbotScenario->rollback();
      //$this->TLeadListSetting->rollback();
      //$this->TLeadList->rollback();
      //$this->printLog('ERROR FOUND. message : '.$e->getMessage());
    }
    //$this->TChatbotScenario->rollback();
    //$this->TLeadListSetting->rollback();
    //$this->TLeadList->rollback();
    //$this->TChatbotScenario->commit();
    //$this->TLeadListSetting->commit();
    ///$this->TLeadList->commit();
    //$this->printLog('FINISHED');
  }

  private function _replaceLeadListSettingData($hashMaster, $mergeIdList, $companyId){
    // 親情報以外のデータは削除する
    foreach($hashMaster as $key => $hashSet){
      if($hashSet['id'] !== $this->_replaceChildrenId($hashSet['id'],$mergeIdList)){
        $this->TLeadListSetting->delete((int)$hashSet['id']);
        unset($hashMaster[$key]);
      }
    }

    foreach($hashMaster as $parentData){
      $saveDataList = [];
      $this->TLeadListSetting->id = (int)$parentData['id'];
      foreach($parentData['list_parameter'] as $leadInfo){
        $saveData = [
          'leadUniqueHash' => $leadInfo['leadUniqueHash'],
          'leadLabelName' => $leadInfo['leadLabelName'],
          'deleted' => 0
        ];
        $saveDataList[] = $saveData;
      }
      $saveDataList = json_encode($saveDataList);
      $this->TLeadListSetting->save(['list_parameter' => $saveDataList]);
    }



  }

  private function _replaceScenarioData($hashMaster, $mergeIdList, $companyId){
    $allScenarios = $this->TChatbotScenario->find('all', [
      'recursive' => -1,
      'fields'=> ['id', 'activity'],
      'conditions' => [
        'm_companies_id' => $companyId,
        'del_flg' => 0
      ]
    ]);
    foreach($allScenarios as $scenario){

      $action = json_decode($scenario['TChatbotScenario']['activity'],true);
      $saveData = $action;
      if($key = array_search(C_SCENARIO_ACTION_LEAD_REGISTER, array_column($action['scenarios'], "actionType"))){
        $leadListSettingsId = $this->_replaceChildrenId($action['scenarios'][$key]['tLeadListSettingId'], $mergeIdList);
        $leadInformation = $this->_makeLeadInformationForScenario($action['scenarios'][$key]['tLeadListSettingId'], $hashMaster);
        $saveData['scenarios'][$key]['tLeadListSettingId'] = $leadListSettingsId;
        $saveData['scenarios'][$key]['leadInformations'] = $leadInformation;
      }
      $saveData = json_encode($saveData);
      $this->TChatbotScenario->id = (int)$scenario['TChatbotScenario']['id'];
      $this->TChatbotScenario->save(['activity' => $saveData]);
    }
  }

  private function _makeLeadInformationForScenario($id, $hashList){
    $allInfo = [];
    $key = array_search($id,array_column($hashList, "id"));
    foreach($hashList[$key]['list_parameter'] as $parameter) {
      $information = [
        'leadUniqueHash' => $parameter['leadUniqueHash'],
        'leadVariableName' => $parameter['leadVariableName']
      ];
      $allInfo[] = $information;
    }
    return $allInfo;
  }

  private function _replaceLeadListData($hashMaster, $mergeIdList, $companyId){
    $leadLists = $this->TLeadList->find('all', [
      'recursive' => -1,
      'fields' => ['id','t_lead_list_settings_id','lead_informations'],
      'conditions' => [
        'm_companies_id' => $companyId
      ]
    ]);
    foreach($leadLists as $leadList){
      $saveList = [];
      $key = array_search($leadList['TLeadList']['t_lead_list_settings_id'], array_column($hashMaster, "id"));
      $replaceHash = $hashMaster[$key]['list_parameter'];
      $leadInformations = json_decode($leadList['TLeadList']['lead_informations'], true);
      foreach($leadInformations as $leadInfo) {
        $idx = array_search($leadInfo['leadLabelName'], array_column($replaceHash, 'leadLabelName'));
          $saveData = [
            'leadUniqueHash' => $replaceHash[$idx]['leadUniqueHash'],
            'leadVariable' => $leadInfo['leadVariable']
          ];
        $saveList[] = $saveData;
      }
      $leadListSettingsId = $this->_replaceChildrenId($leadList['TLeadList']['t_lead_list_settings_id'], $mergeIdList);
      $saveList = json_encode($saveList, JSON_UNESCAPED_UNICODE);
      $this->TLeadList->id = (int)$leadList['TLeadList']['id'];
      $this->TLeadList->save(['lead_informations' => $saveList, 't_lead_list_settings_id' => $leadListSettingsId]);
    }
  }
  
  

  private function _checkParentSettings($hashMaster, $mergeIdList, $companyId){
    $allLeadListSettings = $this->_getAllLeadListSettings($companyId);
    foreach($allLeadListSettings as $targetSettings){
      if(!in_array($targetSettings['TLeadListSetting']['id'], array_column($hashMaster, "id"))){
        // マージする側だった場合、ハッシュ値を寄せる
        $hashMaster = $this->_gatherSettings($hashMaster, $mergeIdList, $targetSettings);
      }
    }
    return $hashMaster;
  }

  private function _gatherSettings($hashMaster, $mergeIdList, $targetSettings){

    foreach($mergeIdList as $idList){
      if(in_array($targetSettings['TLeadListSetting']['id'], $idList['children'])){
        //
        $key = array_search($idList['parent'], array_column($hashMaster, "id"));
        $mergeData = [
          'id' => $targetSettings['TLeadListSetting']['id'],
          'list_name' => $targetSettings['TLeadListSetting']['list_name'],
          'list_parameter' => $hashMaster[$key]['list_parameter']
        ];
        array_push($hashMaster, $mergeData);
        break;
      }
    }
    return $hashMaster;
  }

  private function _setIdList($parents, $companyId){
    $childrenList = [];
    foreach($parents as $parent){
      $idList = $this->TLeadListSetting->find('list', [
        'conditions' => [
          'list_name' => $parent['list_name'],
          'id !=' => $parent['id'],
          'm_companies_id' => $companyId
        ]
      ]);
      $childrenList[] = [
        'parent' => $parent['id'],
        'children' => $idList
      ];
    }
    return $childrenList;
  }

  private function _getAllLeadListSettings($id){
    return $this->TLeadListSetting->find('all', [
      'recursive' => -1,
      'conditions' => [
        'm_companies_id' => $id
      ]
    ]);
  }

  private function _getGroupedLeadListSettings($id){
    return $this->TLeadListSetting->find('all', [
      'recursive' => -1,
      'conditions' => [
        'm_companies_id' => $id
      ],
      'group' => 'list_name',
      'order' => [
        'id'  => 'asc'
      ]
    ]);
  }

  private function _replaceChildrenId($id, $idList){
    if(in_array($id, array_column($idList, "parent"))){
      // 親ならそのまま自分のIDを使用する
      return $id;
    } else {
      // 子なら親のidを検索する
      foreach($idList as $idRelation){
        if(in_array($id, $idRelation['children'])){
          return $idRelation['parent'];
          break;
        }
      }
    }
  }

  private function _makeHashMaster($groupedLeadListSettings){
    $allSettings = [];
    foreach($groupedLeadListSettings as $leadListSettings){
      $setting = [];
      $paramSettings = json_decode($leadListSettings['TLeadListSetting']['list_parameter']);
      foreach($paramSettings as $param){
        $setting[] = [
          'leadUniqueHash' => $this->_makeHashProcess($param->leadLabelName),
          'leadLabelName' => $param->leadLabelName,
          'leadVariableName' => $param->leadVariableName
        ];
      }
      $allSettings[] = [
        'id' => $leadListSettings['TLeadListSetting']['id'],
        'list_name' => $leadListSettings['TLeadListSetting']['list_name'],
        'list_parameter' => $setting
      ];
    }
    return $allSettings;
  }

  private function _makeHashProcess($hashSalt){
    return hash("fnv132", (string)microtime().$hashSalt);
  }

}
