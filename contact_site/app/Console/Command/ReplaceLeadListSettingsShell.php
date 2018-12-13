<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/02/20
 * Time: 19:07
 * @property TAutoMessage $TAutoMessage
 */

class ReplaceLeadListSettingsShell extends AppShell
{
  const LOG_INFO = 'batch-info';
  const LOG_ERROR = 'batch-error';

  const ACTION_TYPE_SEND_MESSAGE = "1";

  public $uses = ['TLeadList', 'TLeadListSetting', 'TChatbotScenario', 'TransactionManager'];

  /**
   * @see https://qiita.com/colorrabbit/items/d302cc0eeec3adc18456
   */
  public function startup() {
    parent::startup();
  }

  /**
   * リードリストのデータを洗い替える
   */
  public function refreshLeadListSetting() {
    $leadCompanies = $this->TLeadListSetting->find('list', [
      'fields' => 'm_companies_id',
      'group' => 'm_companies_id'
    ]);

    try {
      $transaction = $this->TransactionManager->begin();
      foreach($leadCompanies as $companyId){
        $this->printLog("対象企業ID:".$companyId);
        // リード設定を保持している会社毎に洗い替えを行う(別会社の同一リードリスト名の場合を考慮して)

        // [[マージ先id] => ハッシュ値, 項目名, 変数名], ...]　のセット配列を作る
        $hashMaster = $this->_makeHashMaster($this->_getGroupedLeadListSettings($companyId));
        $this->printLog("ハッシュ値セット\n".var_export($hashMaster,TRUE));
        // マージ先id、マージされるidのリストを作る
        $mergeIdList = $this->_setIdList($hashMaster, $companyId);
        $this->printLog("親IDと子IDのリスト\n".var_export($mergeIdList,TRUE));
        // マージ先idに、ハッシュ値を全て寄せる
        $hashMaster = $this->_checkParentSettings($hashMaster, $mergeIdList, $companyId);
        $this->printLog("ハッシュ値寄せた後のセット\n".var_export($hashMaster,TRUE));
        // リードリスト情報の値を置き換える（ハッシュ値：値）
        $this->_replaceLeadListData($hashMaster, $mergeIdList, $companyId);
        // シナリオ設定の値を置き換える（ハッシュ値：変数名）
        $this->_replaceScenarioData($hashMaster, $mergeIdList, $companyId);
        // リードリスト設定の値を置き換える（ハッシュ値：項目名）
        $this->_replaceLeadListSettingData($hashMaster, $mergeIdList, $companyId);

      }
      $transaction->commit();
    } catch(Exception $e) {
      $transaction->rollback();
      $this->printLog('ERROR FOUND. message : '.$e->getMessage());
    }
    $this->printLog('FINISHED');
  }

  private function _replaceLeadListSettingData($hashMaster, $mergeIdList, $companyId){
    // 親情報以外のデータは削除する
    foreach($hashMaster as $key => $hashSet){
      if($hashSet['id'] !== $this->_replaceChildrenId($hashSet['id'],$mergeIdList)){
        $this->printLog("削除対象となるIDです".$hashSet['id']);
        if(!$this->TLeadListSetting->delete((int)$hashSet['id'])){
          throw new Exception('リードリスト設定の削除に失敗しました');
        }
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
      $this->printLog("書き換え後のリード設定情報".$saveDataList);
      if(!$this->TLeadListSetting->save(['list_parameter' => $saveDataList])){
        throw new Exception('リードリスト設定の更新に失敗しました');
      }
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

      $this->printLog("書き換え前のシナリオactivity".$scenario['TChatbotScenario']['activity']);
      $action = json_decode($scenario['TChatbotScenario']['activity'],true);
      $saveData = $action;
      $key = array_keys(array_column($action['scenarios'], "actionType"), C_SCENARIO_ACTION_LEAD_REGISTER);
      if(!empty($key)){
        foreach($key as $index) {
          $leadListSettingsId = $this->_replaceChildrenId($action['scenarios'][$index]['tLeadListSettingId'], $mergeIdList);
          $leadInformation = $this->_makeLeadInformationForScenario($action['scenarios'][$index]['tLeadListSettingId'], $hashMaster);
          $saveData['scenarios'][$index]['tLeadListSettingId'] = $leadListSettingsId;
          $saveData['scenarios'][$index]['leadInformations'] = $leadInformation;
        }
      }
      $saveData = json_encode($saveData);
      $this->printLog("書き換え後のシナリオactivity".$saveData);
      $this->TChatbotScenario->id = (int)$scenario['TChatbotScenario']['id'];
      if(!$this->TChatbotScenario->save(['activity' => $saveData])){
        throw new Exception('シナリオ設定の更新に失敗しました');
      }
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
      $this->printLog("書き換え前のリード情報".$leadList['TLeadList']['lead_informations']);
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
      $this->printLog("書き換え後のリード情報".$saveList);
      $this->TLeadList->id = (int)$leadList['TLeadList']['id'];
      if(!$this->TLeadList->save(['lead_informations' => $saveList, 't_lead_list_settings_id' => $leadListSettingsId])) {
        throw new Exception('リードリスト情報の更新に失敗しました');
      };
    }
  }



  private function _checkParentSettings($hashMaster, $mergeIdList, $companyId){
    $allLeadListSettings = $this->_getAllLeadListSettings($companyId);
    $this->printLog("書き換え前のリード設定\n".var_export($allLeadListSettings,TRUE));
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
        $listParam = $this->_setOriginVariableName(json_decode($targetSettings['TLeadListSetting']['list_parameter'],true), $hashMaster[$key]['list_parameter']);
        $mergeData = [
          'id' => $targetSettings['TLeadListSetting']['id'],
          'list_name' => $targetSettings['TLeadListSetting']['list_name'],
          'list_parameter' => $listParam
        ];
        array_push($hashMaster, $mergeData);
        break;
      }
    }
    return $hashMaster;
  }

  private function _setOriginVariableName($originParam, $mergeParam){
    foreach($mergeParam as $key => $param){
      $originParam[$key] =['leadUniqueHash' => $param['leadUniqueHash']] + $originParam[$key];
    }
    return $originParam;
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

  private function printLog($msg) {
    $this->log($msg, self::LOG_INFO);
    $this->out($msg);
  }

}
