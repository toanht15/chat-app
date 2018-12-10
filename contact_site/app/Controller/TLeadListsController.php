<?php
/**
 *TDLeadListsController  controller.
 * リードリスト出力画面
 */

class TLeadListsController extends AppController{
  public $uses = ['TLeadList','TLeadListSetting','TChatbotScenario','TCampaign','THistory'];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', 'リードリスト出力');
  }

  /* *
   * 一覧画面
   * @return void
   * */
  public function index(){
    if ($this->request->is('post')){
      if($this->request->data['selectList'] === "all"){
        $this->getAllCSV();
      } else {
        $this->getOneCSV(intval($this->request->data['selectList']));
      }
    }
    $leadList = $this->getAllSetting('list_name');
    if(!empty($leadList)) {
      $leadList = $leadList + ['all' => "（すべてのリスト）"];
    }
    $leadList = ['none'=>"リストを選択してください"] + $leadList;
    $this->set("leadList", $leadList);
    $data = $this->dateTimeSet();
    $data['History']['start_day'] = htmlspecialchars($data['History']['start_day']);
    $data['History']['finish_day'] = htmlspecialchars($data['History']['finish_day']);
    $this->set('data',$data);
  }

  private function dateTimeSet(){
    $historyConditions = [
      'History' => [
        'company_start_day' => date("Y/m/d", strtotime($this->userInfo['MCompany']['created'])),
        'start_day' => date("Y/m/d", strtotime("-6 day")),
        'finish_day' => date("Y/m/d"),
        'period' => '過去一週間'
      ]
    ];
    return $historyConditions;
  }

  private function getAllCSV(){
    $allLeadList = $this->getAllSetting('list_name');
    $zip = new ZipArchive();
    $zipDir = "/tmp/";
    $filename = date("YmdHi").'.zip';
    $zip->open($zipDir.$filename, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
    forEach($allLeadList as $targetId => $targetName) {
      $targetInfo = $this->createCSV($targetId);
      $targetName = mb_convert_encoding($targetName, 'SJIS-win', 'utf8');
      $zip->addFromString($targetName.".csv",$this->_outputCSV($targetInfo));
    }
    $zip->close();
    $this->response->type('zip');
    header('Content-Type: application/zip; name="'.$filename.'"');
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    header('Content-Length:'.filesize($zipDir.$filename));
    readfile($zipDir.$filename);
    unlink($zipDir.$filename);
  }

  private function getAllSetting($identifier){
    $result = $this->TLeadListSetting->find('list',[
      'recursive' => -1,
      'fields' => [
        $identifier
      ],
      'conditions' => [
        "m_companies_id" => $this->userInfo['MCompany']['id']
      ]
    ]);
    return $result;
  }

  private function getOneCSV($targetId){
    $targetInfo = $this->createCSV($targetId);
    $this->response->type('csv');
    $this->response->body($this->_outputCSV($targetInfo));
  }

  private function createCSV($targetId){
    $targetInfo = $this->searchListInfo($targetId);
    $targetInfo = $this->convertDataForCSV($targetInfo, $targetId);
    return $targetInfo;
  }

  private function _outputCSV($csv = []){
    $this->layout = null;
    //メモリ上に領域確保
    $fp = fopen('php://temp/maxmemory:'.(5*1024*1024),'a');
    foreach($csv as $row){
      fputcsv($fp, $row);
    }
    //ビューを使わない
    $this->autoRender = false;
    //ファイルポインタを先頭へ
    rewind($fp);
    //リソースを読み込み文字列を取得する
    $csv = stream_get_contents($fp);
    // 文字化け対策
    $csv = mb_convert_encoding($csv, 'SJIS-win', 'utf8');

    //
    fclose($fp);
    return $csv;
  }

  /* 選ばれたIDからターゲットとなる名前を取得する
   *
   */

  private function getTargetName($id){
    $result = $this->TLeadListSetting->find('list',[
      'recursive' => -1,
      'fields' => [
        "list_name"
      ],
      'conditions' => [
        "m_companies_id" => $this->userInfo['MCompany']['id'],
        "id" => $id
      ],
    ]);
    return $result;
  }

  private function searchListInfo($id){
    $resultArray = [];
    $result = $this->TLeadList->find('all', [
      'recursive' => -1,
      'conditions' => [
        "m_companies_id" => $this->userInfo['MCompany']['id'],
        "t_lead_list_settings_id" => $id,
        [
          'created BETWEEN ? AND ?' => [
            $this->request->data['startDate']." 00:00:00",
            $this->request->data['endDate']." 23:59:59"
          ]
        ]
      ],
      'order' => [
        'created' => 'desc'
      ]
    ]);
    forEach($result as $data){
      $resultArray[] = $data;
    }
    return $resultArray;
  }

  /* CSVのデータを作成する
   * @param allData   リードリストの保存されたデータ
   * @param targetId  今回取得しようとしているリード設定のID
   */

  private function convertDataForCSV($allData, $targetId){
    // CSVのヘッダーを作成する
    // 有効なリードリスト設定のヘッダーを取得する
    $leadHeader = $this->_getEffectiveLeadSettings($this->_getHeaderSetting($targetId), $allData);

    $head = [
      "登録日時"
    ];
    // ヘッダーを追加する
    $head = $this->addLeadHeader($head, $leadHeader);
    array_push($head,
      "ブラウザ",
        "キャンペーン",
           "リード登録ページ",
           "実行シナリオ",
           "リード名"
    );
    $csv[] = $head;
    forEach($allData as $currentData){
      $row = [];
      $row['data'] = $currentData['TLeadList']['created'];
      // リードリスト情報に合わせて可変なrowを追加する
      $row = $this->addLeadData($row, $currentData, $leadHeader);
      $row['browser'] = $this->getBrowser($currentData['TLeadList']['user_agent']);
      $row['campaign'] = $this->getCampaign($currentData['TLeadList']['landing_page']);
      $row['leadPage'] = $currentData['TLeadList']['lead_regist_page'];
      $row['executeScenario'] = $this->getScenarioName($currentData['TLeadList']['t_chatbot_scenarios_id']);
      $leadData = $this->getTargetName($currentData['TLeadList']['t_lead_list_settings_id']);
      $row['leadName'] = $leadData[key($leadData)];
      $csv[] = $row;
    }
    return $csv;
  }

  /*　ブラウザ情報を取得
   *  @param ua ユーザーエージェント
   *  return ブラウザの名前
   */

  private function getBrowser($ua){
    $ua = strtolower($ua);
    $browser = "標準ブラウザ";
    if(strpos($ua, "edge")){
      $browser = "Edge";
    } elseif (strpos($ua, "trident")){
      $browser = "Internet Explorer";
    } elseif (strpos($ua, "chrome")) {
      $browser = "Chrome";
    } elseif (strpos($ua, "firefox")) {
      $browser = "Firefox";
    } elseif (strpos($ua, "safari")) {
      $browser = "Safari";
    } elseif (strpos($ua, "opera")) {
      $browser = "Opera";
    }
    return $browser;
  }

  /* キャンペーン情報を取得
   * @param lp ランディングページのURL
   * return キャンペーン名
   */

  private function getCampaign($lp){
    $campaignList = $this->TCampaign->find('list',[
      'recursive' => -1,
      'fields' => [
        'name',
        'parameter'
      ],
      'conditions' => [
        "m_companies_id" => $this->userInfo['MCompany']['id'],
      ],
    ]);
    $existCampaign = "";
    $url = explode("/",$lp);
    $searchTarget = $url[count($url) - 1];
    $cnt = 1;
    $length = count($campaignList);
    foreach($campaignList as $name => $campaign){
      if(strpos($searchTarget,$campaign)){
        $existCampaign .= $name;
        if($cnt < $length) {
          $existCampaign .= "\n";
        }
      }
      $cnt++;
    }
    return $existCampaign;
  }

  /* シナリオ名を取得
 * @param scenarioId リード登録時に呼び出したシナリオのID
 * return シナリオ名
 */

  private function getScenarioName($scenarioId){
    $scenarioData = $this->TChatbotScenario->find('list',[
      'recursive' => -1,
      'fields' => 'name',
      'conditions' => [
        "m_companies_id" => $this->userInfo['MCompany']['id'],
        'id' => $scenarioId
      ],
    ]);
    return reset($scenarioData);
  }

  /*
   *  @param row 追加先
   *  @param element 追加する要素
   */
  private function addLeadData($row, $element,$leadHeader){
    $leadSettings = json_decode($element['TLeadList']['lead_informations']);
    foreach ($leadHeader as $key => $header) {
      $targetData = "";
      foreach($leadSettings as $rowData) {
        if(strcmp($header['leadUniqueHash'], $rowData->leadUniqueHash) == 0){
          $targetData = $rowData->leadVariable;
        }
      }
      $row['leadData'.$key] = $targetData;
    }
    return $row;
  }

  private function addLeadHeader($head, $header){
    foreach($header as $column){
      array_push( $head, $column['leadLabelName']);
    }
    return $head;
  }

  private function _getHeaderSetting($id){
    $targetData = $this->TLeadListSetting->find('first',[
      'recursive' => -1,
      'field' => [
        'list_parameter'
      ],
      'conditions' => [
        "m_companies_id" => $this->userInfo['MCompany']['id'],
        "id" => $id
      ]
    ]);
    return json_decode($targetData['TLeadListSetting']['list_parameter']);
  }

  private function _getEffectiveLeadSettings($settings, $dataSet){
    $headDataSet = [];
    foreach( $settings as $setting ){
      $head = [];
      if($setting->deleted == 0){
        // 削除されていない場合、そのまま対象として追加
        $head = [
          'leadUniqueHash' => $setting->leadUniqueHash,
          'leadLabelName'  => $setting->leadLabelName
        ];
        array_push($headDataSet, $head);
      } else {
        $existLabelFlg = false;
        // 削除されている場合、保存されているリードリストに存在するか調べる
        foreach( $dataSet as $data ){
          $searchTargets = json_decode($data['TLeadList']['lead_informations']);
          foreach( $searchTargets as $target){
            if( strcmp($setting->leadUniqueHash,$target->leadUniqueHash) == 0 ){
              // 削除されているがリードリスト内に保存されている場合は有効な項目として表示する
              $head = [
                'leadUniqueHash' => $setting->leadUniqueHash,
                'leadLabelName'  => $setting->leadLabelName
              ];
              array_push($headDataSet, $head);
              $existLabelFlg = true;
            }
          }
          if($existLabelFlg) break;
        }
      }
    }
    return $headDataSet;
  }

}
