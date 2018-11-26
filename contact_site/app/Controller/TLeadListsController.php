<?php
/**
 *TDLeadListsController  controller.
 * リードリスト出力画面
 */

class TLeadListsController extends AppController{
  public $uses = ['TLeadList','TLeadListSetting','TChatbotScenario','TCampaign'];

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
        $this->allCSVoutput();
      } else {
        $this->CSVoutput();
      }
    }
    $leadList = $this->getAllSetting('list_name');
    if(!empty($leadList)) {
      $leadList = $leadList + ['all' => "（すべてのリスト）"];
    }
    $leadList = ['none'=>"リストを選択してください"] + $leadList;
    $this->set("leadList", $leadList);
    $data = $this->Session->read('Thistory');
    $data['History']['start_day'] = htmlspecialchars($data['History']['start_day']);
    $data['History']['finish_day'] = htmlspecialchars($data['History']['finish_day']);
    $this->set('data',$data);
  }

  private function allCSVoutput(){
    $targetNames = $this->getAllName();
    $zip = new ZipArchive();
    $zipDir = "/tmp/";
    $filename = date("YmdHi").'.zip';
    $zip->open($zipDir.$filename, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
    forEach($targetNames as $targetName) {
      $targetInfo = $this->searchListInfo($this->getTargetIdList($targetName));
      $targetInfo = $this->convertDataForCSV($targetInfo);
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

  private function getCompanyInfo(){
    $result = $this->MCompany->find('list',[
      'recursive' => -1,
      'fields' => 'created',
      'conditions' => [
        "id" => $this->userInfo['MCompany']['id']
      ]
    ]);
    return $result;
  }

  private function getAllSetting($identifier){
    $result = $this->TLeadListSetting->find('list',[
      'recursive' => -1,
      'fields' => [
        $identifier
      ],
      'conditions' => [
        "m_companies_id" => $this->userInfo['MCompany']['id']
      ],
      'group' => 'list_name'
    ]);
    return $result;
  }

  private function CSVoutput(){
    $targetName = $this->getTargetName(intval($this->request->data['selectList']));
    $targetInfo = $this->searchListInfo($this->getTargetIdList($targetName));
    $targetInfo = $this->convertDataForCSV($targetInfo);
    $this->response->type('csv');
    $this->response->body($this->_outputCSV($targetInfo));
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

  private function getAllName(){
    $result = $this->TLeadListSetting->find('list',[
      'recursive' => -1,
      'fields' => [
        "list_name"
      ],
      'conditions' => [
        "m_companies_id" => $this->userInfo['MCompany']['id'],
      ],
      'group' => 'list_name'
    ]);
    return $result;
  }

  private function getTargetIdList($name){
    $result = $this->TLeadListSetting->find('list',[
      'recursive' => -1,
      'conditions' => [
        "m_companies_id" => $this->userInfo['MCompany']['id'],
        "list_name" => $name
      ],
    ]);
    return $result;
  }

  private function searchListInfo($idList){
    $resultArray = [];
    foreach($idList as $id) {
      $result = $this->TLeadList->find('all', [
        'recursive' => -1,
        'conditions' => [
          "m_companies_id" => $this->userInfo['MCompany']['id'],
          "t_lead_list_settings_id" => $id
        ],
        'order' => [
          'created' => 'desc'
        ]
      ]);
      forEach($result as $data){
        $resultArray[] = $data;
      }
    }
    return $resultArray;
  }

  private function convertDataForCSV($allData){
    // CSVのヘッダーを作成する
    $head = [
      "登録日時"
    ];
    // リードリスト情報に合わせて可変なヘッダーを追加する
    $head = $this->addLeadHeader($head, $allData);
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
      $row = $this->addLeadData($row, $currentData);
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
    forEach($campaignList as $name => $campaign){
      if(strpos($searchTarget,$campaign)){
        $existCampaign = $name;
        break;
      }
    }
    return $existCampaign;
  }

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
  private function addLeadData($row, $element){
    $leadSettings = json_decode($element['TLeadList']['lead_informations']);
    for($i = 0; $i<count($leadSettings); $i++){
      $row['leadData'.$i] = $leadSettings[$i]->leadVariable;
    }
    return $row;
  }

  private function addLeadHeader($head, $element){
    // ヘッダー情報は同一リードリスト名では同じなため、最初の1つだけ見る
    $leadHeaders = json_decode($element[0]['TLeadList']['lead_informations']);
    foreach($leadHeaders as $leadHeader){
      array_push($head, $leadHeader->leadLabelName);
    }
    return $head;
  }

}