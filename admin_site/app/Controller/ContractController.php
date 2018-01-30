<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/08/08
 * Time: 12:09
 */

App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
class ContractController extends AppController
{
  public $uses = ['MCompany', 'MAgreements', 'MUser', 'MWidgetSetting', 'MChatSetting', 'TAutoMessages', 'TDictionaries', 'TDictionaryCategory', 'MMailTemplate', 'TransactionManager'];

  public $paginate = [
    'MCompany' => [
      'order' => ['MCompany.id' => 'asc'],
      'fields' => ['*'],
      'limit' => 1000,
      'joins' => [
        [
          'type' => 'left',
          'table' => 'm_agreements',
          'alias' => 'MAgreement',
          'conditions' => [
            'MAgreement.m_companies_id = MCompany.id',
          ],
        ],
        [
          'type' => 'left',
          'table' => '(SELECT id,m_companies_id,mail_address,password FROM m_users WHERE del_flg != 1 AND permission_level = 99 GROUP BY m_companies_id)',
          'alias' => 'AdminUser',
          'conditions' => [
            'AdminUser.m_companies_id = MCompany.id',
          ],
        ],
        [
          'type' => 'inner',
          'table' => '(SELECT id,m_companies_id,mail_address,password,count(m_companies_id) AS user_account FROM  m_users WHERE del_flg != 1 AND permission_level != 99 GROUP BY m_companies_id)',
          'alias' => 'MUser',
          'conditions' => [
            'MUser.m_companies_id = MCompany.id',
          ],
        ],
      ],
      'conditions' => [
          'MCompany.del_flg != ' => 1,
      ],
    ]
  ];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', 'サイトキー管理');
    $this->Auth->allow(['remoteSaveForm']);
    header('Access-Control-Allow-Origin: *');
  }

  /**
   * 初期画面
   * @return void
   */
  public function index() {
    $this->set('title_for_layout', 'サイトキー管理');
    $this->set('companyList', $this->paginate('MCompany'));
  }

  public function add() {
    Configure::write('debug', 0);
    if($this->isOverAllUserCountLimit()) {
      $this->set('overLimitMessage', 'アカウントの登録上限数を超過しているため、新規に企業キーを登録できません。');
      return;
    }

    $this->set('title_for_layout', 'サイトキー登録');

    if( $this->request->is('post') ) {
      $this->autoRender = false;
      $this->layout = "ajax";
      $this->log($this->data, LOG_DEBUG);
      $data = $this->getParams();

      try {
        $this->processTransaction($data['MCompany'], $data['Contract'], $data['MAgreements']);
      } catch(Exception $e) {
        $this->log("Exception Occured : ".$e->getMessage(), LOG_WARNING);
        $this->log($e->getTraceAsString(),LOG_WARNING);
        $this->response->statusCode(400);
        return json_encode([
          'success' => false,
          'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
      }
    }
  }

  /* *
   * 更新画面
   * @param id
   * @return void
   * */
  public function edit($id)
  {
      $this->MCompany->id = $id;

    if ($this->request->is('post') || $this->request->is('put')) {
      $companyEditData = $this->MCompany->read(null, $id);
      $agreementEditData = $this->MAgreements->find('first',[
          'conditions' => array(
            'm_companies_id' => $companyEditData['MCompany']['id']
          )]
      );
      $transactions = $this->TransactionManager->begin();
      $saveData = $this->request->data;
      $companySaveData = [];
      $companySaveData['MCompany'] = $saveData['MCompany'];
      $companySaveData['MCompany']['id'] = $companyEditData['MCompany']['id'];
      // 画面キャプチャ共有とリアルタイムモニタ表示は設定を引き継ぐ
      $coreSetting = json_decode($companyEditData['MCompany']['core_settings'], TRUE);
      $saveData['MCompany']['options']['laCoBrowse'] = !empty($coreSetting['laCoBrowse']) ? $coreSetting['laCoBrowse'] : false;
      $saveData['MCompany']['options']['hideRealtimeMonitor'] = !empty($coreSetting['hideRealtimeMonitor']) ? $coreSetting['hideRealtimeMonitor'] : false;
      $companySaveData['MCompany']['core_settings'] = $this->getCoreSettingsFromContactTypesId($saveData['MCompany']['m_contact_types_id'], $saveData['MCompany']['options']);
      $this->MCompany->save($companySaveData,false);

      if(empty($agreementEditData)) {
        $this->MAgreements->create();
        $this->MAgreements->set([
          'm_companies_id' => $companyEditData['MCompany']['id'],
          'trial_start_day' => $saveData['MAgreements']['application_day'],
          'trial_end_day' => $saveData['MAgreements']['application_day'],
          'agreement_start_day' => $saveData['MAgreements']['agreement_start_day'],
          'agreement_end_day' => $saveData['MAgreements']['agreement_end_day']
        ]);
        $this->MAgreements->save();
      } else {
        $agreementSaveData = [];
        $agreementSaveData['MAgreements'] = array_merge($agreementEditData['MAgreements'], $saveData['MAgreements']);
        $this->MAgreements->save($agreementSaveData, false);
      }

      // アップグレードがある場合で、デフォルト設定が必要なものは設定を追加する
      $this->upgradeProcess($companyEditData['MCompany']['m_contact_types_id'], $saveData['MCompany']['m_contact_types_id'], $companyEditData['MCompany']['id'], $saveData['MCompany']);
      $this->TransactionManager->commit($transactions);
    } else {
      $editData = $this->MCompany->read(null, $id);
      // オプションを別領域に設定
      $editData['MCompany']['options']['refCompanyData'] = json_decode($editData['MCompany']['core_settings'],TRUE)['refCompanyData'];
      // ここまで
      $agreementData = $this->MAgreements->find('first',[
        'conditions' => array(
          'm_companies_id' => $editData['MCompany']['id']
        )]
      );

      $editData = array_merge($editData, $agreementData);

      $this->set('companyId', $editData['MCompany']['id']);//削除に必要なもの
      $this->set('companyKey', $editData['MCompany']['company_key']);//削除に必要なもの
      $this->request->data = $editData;
    }
  }

  public function deleteCompany() {
    if($this->request->is('post')) {
      $deleteTarget = $this->getParams();

      $transaction = $this->TransactionManager->begin();

      try {
        // ユーザーの削除（論理削除）
        $data = array(
          'MUser.del_flg' => '1',
          'MUser.deleted' => '"'.date('Y-m-d H:i:s').'"'
        );
        $conditions = array(
          'MUser.m_companies_id' => $deleteTarget['id'],
          'MUser.permission_level != ' => "99" // ML用アカウントは残す
        );
        $this->MUser->updateAll($data, $conditions);

        //企業用JavaScriptの退避
        $this->backupCompanyJSFile($deleteTarget['companyKey']);

        //企業キーの削除（論理削除）
        $deleteCompanyData = array('MCompany' => array('id' => $deleteTarget['id'], 'del_flg' => '1', 'deleted' => date('Y-m-d H:i:s')));
        $deleteCompanyFields = array('del_flg', 'deleted');
        $this->MCompany->save($deleteCompanyData, false, $deleteCompanyFields);

      } catch(Exception $e) {
        $this->TransactionManager->rollback($transaction);
        $this->log("Delete Exception Occured : ".$e->getMessage(), LOG_WARNING);
      }
      $this->TransactionManager->commit($transaction);
    }
    $this->autoRender = false;
  }


  private function getParams() {
    return $this->request->data;
  }

  private function validateParams($action) {

  }

  private function processTransaction($companyInfo, $userInfo, $agreementInfo) {
    try {
      $transaction = $this->TransactionManager->begin();
      $addedCompanyInfo = $this->createCompany($companyInfo);
      $this->createAgreementInfo($addedCompanyInfo, $companyInfo, $agreementInfo);
      $this->createFirstAdministratorUser($addedCompanyInfo['id'], $userInfo);
      $this->addDefaultChatPersonalSettings($addedCompanyInfo['id'], $companyInfo);
      $this->addDefaultWidgetSettings($addedCompanyInfo['id'], $companyInfo);
      $this->addDefaultAutoMessages($addedCompanyInfo['id'], $companyInfo);
      $this->addDefaultDictionaries($addedCompanyInfo['id'], $companyInfo);
      $this->addDefaultMailTemplate($addedCompanyInfo['id'], $companyInfo);
      $this->addCompanyJSFile($addedCompanyInfo['companyKey']);
    } catch (Exception $e) {
      $this->TransactionManager->rollback($transaction);
      throw $e;
    }
    $this->TransactionManager->commit($transaction);
  }

  private function upgradeProcess($beforeContactTypeId, $afterContactTypeId, $targetCompanyId, $companyInfo) {
    if(strcmp($beforeContactTypeId, $afterContactTypeId) === 0) {
      // プラン変更なし
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_CHAT_BASIC_PLAN_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_CHAT_PLAN_ID) === 0) {
      // ベーシック => スタンダード
      $this->addDefaultMailTemplate($targetCompanyId, $companyInfo);
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_CHAT_BASIC_PLAN_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_SCREEN_SHARING_ID) === 0) {
      // ベーシック => シェアリング
      $this->upgradeWidgetSettings($beforeContactTypeId, $afterContactTypeId, $targetCompanyId);
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_CHAT_BASIC_PLAN_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_FULL_PLAN_ID) === 0) {
      // ベーシック => プレミアム
      $this->upgradeWidgetSettings($beforeContactTypeId, $afterContactTypeId, $targetCompanyId);
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_CHAT_PLAN_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_CHAT_BASIC_PLAN_ID) === 0) {
      // スタンダード => ベーシック
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_CHAT_PLAN_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_SCREEN_SHARING_ID) === 0) {
      // スタンダード => シェアリング
      $this->upgradeWidgetSettings($beforeContactTypeId, $afterContactTypeId, $targetCompanyId);
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_CHAT_PLAN_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_FULL_PLAN_ID) === 0) {
      // スタンダード => プレミアム
      $this->upgradeWidgetSettings($beforeContactTypeId, $afterContactTypeId, $targetCompanyId);
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_SCREEN_SHARING_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_CHAT_BASIC_PLAN_ID) === 0) {
      // シェアリング => ベーシック
      $this->upgradeWidgetSettings($beforeContactTypeId, $afterContactTypeId, $targetCompanyId);
      $this->addDefaultChatPersonalSettings($targetCompanyId, $companyInfo);
      $this->addDefaultAutoMessages($targetCompanyId, $companyInfo);
      $this->addDefaultDictionaries($targetCompanyId, $companyInfo);
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_SCREEN_SHARING_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_CHAT_PLAN_ID) === 0) {
      // シェアリング => スタンダード
      $this->upgradeWidgetSettings($beforeContactTypeId, $afterContactTypeId, $targetCompanyId);
      $this->addDefaultChatPersonalSettings($targetCompanyId, $companyInfo);
      $this->addDefaultAutoMessages($targetCompanyId, $companyInfo);
      $this->addDefaultDictionaries($targetCompanyId, $companyInfo);
      $this->addDefaultMailTemplate($targetCompanyId, $companyInfo);
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_SCREEN_SHARING_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_FULL_PLAN_ID) === 0) {
      // シェアリング => プレミアム
      $this->upgradeWidgetSettings($beforeContactTypeId, $afterContactTypeId, $targetCompanyId);
      $this->addDefaultChatPersonalSettings($targetCompanyId, $companyInfo);
      $this->addDefaultAutoMessages($targetCompanyId, $companyInfo);
      $this->addDefaultDictionaries($targetCompanyId, $companyInfo);
      $this->addDefaultMailTemplate($targetCompanyId, $companyInfo);
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_FULL_PLAN_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_CHAT_BASIC_PLAN_ID) === 0) {
      // プレミアム => ベーシック
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_FULL_PLAN_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_CHAT_PLAN_ID) === 0) {
      // プレミアム => スタンダード
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_CHAT_BASIC_PLAN_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_SCREEN_SHARING_ID) === 0) {
      // プレミアム => シェアリング
    }
  }

  /**
   * @return 一番最後に追加した
   */
  private function createCompany($companyInfo) {
    try {
      $companyKey = $this->generateCompanyKey();
      $this->MCompany->create();
      $this->MCompany->set([
        "company_name" => $companyInfo['company_name'],
        "company_key" => $companyKey,
        "m_contact_types_id" => $companyInfo['m_contact_types_id'],
        "limit_users" => $companyInfo['limit_users'],
        "core_settings" => $this->getCoreSettingsFromContactTypesId($companyInfo['m_contact_types_id'], $companyInfo['options']),
        "trial_flg" => $companyInfo['trial_flg']
      ]);
      $this->MCompany->save();
    } catch(Exception $e) {
      throw $e;
    }
    return [
        'id' => $this->MCompany->getLastInsertID(),
        'companyKey' => $companyKey
    ];
  }

  private function createAgreementInfo($addedCompanyInfo, $companyInfo, $agreementInfo) {
    $password = $this->generateRandomPassword(8);

    $this->MAgreements->create();
    $this->MAgreements->set([
      'm_companies_id' => $addedCompanyInfo['id'],
      'application_day' => date("Y-m-d"), // FIXME（自動発行）
      'trial_start_day' => $agreementInfo['trial_start_day'],
      'trial_end_day' => $agreementInfo['trial_end_day'],
      'agreement_start_day' => $agreementInfo['agreement_start_day'],
      'agreement_end_day' => $agreementInfo['agreement_end_day'],
      'admin_password' => $password
    ]);
    $this->MAgreements->save();

    // スーパー管理者情報追加
    $tmpData = [
      "m_companies_id" => $addedCompanyInfo['id'],
      "user_name" => 'MLAdmin',
      "display_name" => 'MLAdmin',
      "mail_address" => $addedCompanyInfo['companyKey'].C_MAGREEMENT_MAIL_ADDRESS,
      "permission_level" => C_AUTHORITY_SUPER,
      "new_password" => $password
    ];
    $this->MUser->create();
    $this->MUser->set($tmpData);
    if(!$this->MUser->validates()) {
      throw new Exception("MUser validation error");
    }
    $this->MUser->save();
  }

  private function createFirstAdministratorUser($m_companies_id, $userInfo) {
    $tmpData = [
        "m_companies_id" => $m_companies_id,
        "user_name" => $userInfo["user_name"],
        "display_name" => $userInfo["user_display_name"],
        "mail_address" => $userInfo["user_mail_address"],
        "permission_level" => C_AUTHORITY_ADMIN,
        "new_password" => $userInfo["user_password"]
    ];
    $this->MUser->create();
    $this->MUser->set($tmpData);
    if(!$this->MUser->validates()) {
      throw new Exception("MUser validation error");
    }
    $this->MUser->save();
  }

  private function createSuperAdministratorUser($addedCompanyInfo, $userInfo) {
    $password = $this->generateRandomPassword(8);
    $tmpData = [
      "m_companies_id" => $addedCompanyInfo['id'],
      "user_name" => 'MLAdmin',
      "display_name" => 'MLAdmin',
      "mail_address" => $addedCompanyInfo['companyKey'].C_MAGREEMENT_MAIL_ADDRESS,
      "permission_level" => C_AUTHORITY_SUPER,
      "new_password" => $password
    ];
    $this->MUser->create();
    $this->MUser->set($tmpData);
    if(!$this->MUser->validates()) {
      throw new Exception(json_encode($this->MUser->validationErrors, JSON_UNESCAPED_UNICODE));
    }
    $this->MUser->save();
  }

  private function addDefaultChatPersonalSettings($m_companies_id, $companyInfo) {
    if(!$this->isChatEnable($companyInfo['m_contact_types_id'])) return;
    $default = $this->getDefaultChatBasicConfigurations();
    $this->MChatSetting->create();
    $this->MChatSetting->set([
      "m_companies_id" => $m_companies_id,
      "sc_flg" => $default['sc_flg'],
      "sc_default_num" => $default['sc_default_num'],
      "sorry_message" => $default['sorry_message']
    ]);
    $this->MChatSetting->save();
  }

  private function addDefaultWidgetSettings($m_companies_id, $companyInfo) {
    $default = $this->getWidgetSettingsFromContactTypesId($companyInfo['m_contact_types_id']);
    $styleSettings = $default;
    // 設定保持の構造上display_typeを持ってしまっているがstyle_settingsにはいらないため省く
    unset($styleSettings['display_type']);
    $this->MWidgetSetting->create();
    $this->MWidgetSetting->set([
        "m_companies_id" => $m_companies_id,
        "display_type" => $default['display_type'],
        "style_settings" => json_encode($styleSettings, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)
    ]);
    $this->MWidgetSetting->save();
  }

  private function addDefaultAutoMessages($m_companies_id, $companyInfo) {
    if(!$this->isChatEnable($companyInfo['m_contact_types_id'])) return;
    $autoMessages = $this->TAutoMessages->find('all',[
      'conditions' => array(
        'm_companies_id' => $m_companies_id
      )]
    );
    if(empty($autoMessages)) {
      $default = $this->getDefaultAutomessageConfigurations();
      foreach($default as $item) {
        $this->TAutoMessages->create();
        $this->TAutoMessages->set([
          "m_companies_id" => $m_companies_id,
          "name" => $item['name'],
          "trigger_type" => $item['trigger_type'],
          "activity" => $this->convertActivityToJSON($item['activity']),
          "action_type" => $item['action_type'],
          "sort" => $item['sort'],
          "active_flg" => $item['active_type']
        ]);
        $this->TAutoMessages->save();
      }
    }
  }

  private function addDefaultDictionaries($m_companies_id, $companyInfo) {
    if(!$this->isChatEnable($companyInfo['m_contact_types_id'])) return;
    $dictionaries = $this->TDictionaries->find('all',[
        'conditions' => array(
          'm_companies_id' => $m_companies_id
        )]
    );
    if(empty($dictionaries)) {
      // まずカテゴリ[定型文]を入れる
      $this->TDictionaryCategory->create();
      $this->TDictionaryCategory->set([
        "m_companies_id" => $m_companies_id,
        "category_name" => "定型文",
        "sort" => 1
      ]);
      $this->TDictionaryCategory->save();
      $categoryId = $this->TDictionaryCategory->getLastInsertID();

      //カテゴリに紐づく定型文を入れる
      $default = $this->getDefaultDictionaryConfigurations();
      foreach($default as $item) {
        $this->TDictionaries->create();
        $this->TDictionaries->set([
          "m_companies_id" => $m_companies_id,
          "m_user_id" =>  0, // 共有設定なので0固定
          "m_category_id" => $categoryId,
          "word" => $item['word'],
          "type" => $item['type'],
          "sort" => $item['sort']
        ]);
        $this->TDictionaries->save();
      }
    }
  }

  private function addDefaultMailTemplate($m_companies_id, $companyInfo) {
    if(!$this->isAdvancedChatEnable($companyInfo['m_contact_types_id'])) return;
    $default = $this->getDefaultMailTemplateConfigurations();
    foreach($default as $key => $item) {
      $this->MMailTemplate->create();
      $this->MMailTemplate->set([
         'm_companies_id' => $m_companies_id,
         'mail_type_cd' => $key,
         'template' => $item
      ]);
      $this->MMailTemplate->save();
    }
  }

  private function addCompanyJSFile($companyKey) {
    $templateData = new File(C_COMPANY_JS_TEMPLATE_FILE);
    $contents = $templateData->read();
    $companyKeyReplaced = str_replace('##COMPANY_KEY##', $companyKey, $contents);
    $replacedContents = str_replace('##NODE_SERVER_URL##',C_NODE_SERVER_ADDR.C_NODE_SERVER_WS_PORT, $companyKeyReplaced);
    // 書き換えた内容を<companyKey>.jsで保存
    $saveFile = new File(C_COMPANY_JS_FILE_DIR.'/'.$companyKey.'.js', true, 0644);
    $saveFile->open('w');
    $saveFile->append($replacedContents);
    $saveFile->close();
  }

  private function backupCompanyJSFile($companyKey) {
    if(!rename(C_COMPANY_JS_FILE_DIR.'/'.$companyKey.'.js', C_COMPANY_JS_FILE_DIR.'/'.$companyKey.'.js_'.date('Ymd').'bk')) {
      throw Exception('企業用JavaScriptファイルのバックアップに失敗しました。');
    };
  }

  private function generateCompanyKey() {
    return uniqid();
  }

  private function getCoreSettingsFromContactTypesId($m_contact_types_id, $options) {
    $plan = "";
    switch($m_contact_types_id) {
      case C_CONTRACT_FULL_PLAN_ID:
        $plan = C_CONTRACT_FULL_PLAN;
        break;
      case C_CONTRACT_CHAT_PLAN_ID:
        $plan = C_CONTRACT_CHAT_PLAN;
        break;
      case C_CONTRACT_SCREEN_SHARING_ID:
        $plan = C_CONTRACT_SCREEN_SHARING_PLAN;
        break;
      case C_CONTRACT_CHAT_BASIC_PLAN_ID:
        $plan = C_CONTRACT_CHAT_BASIC_PLAN;
        break;
      default:
        throw Exception("不明なプランID: ".$m_contact_types_id);
    }
    $planObj = json_decode($plan, TRUE);
    foreach($options as $key => $enabled) {
      $planObj[$key] = strcmp($enabled, "1") === 0;
    }
    return json_encode($planObj);
  }

  private function getWidgetSettingsFromContactTypesId($m_contact_types_id) {
    $widgetConfiguration = Configure::read('default.widget');
    $val = [];
    switch($m_contact_types_id) {
      case C_CONTRACT_FULL_PLAN_ID:
        $val = array_merge($val, $widgetConfiguration['common'], $widgetConfiguration['chat'], $widgetConfiguration['sharing']);
        break;
      case C_CONTRACT_CHAT_PLAN_ID:
        $val = array_merge($val, $widgetConfiguration['common'], $widgetConfiguration['chat']);
        break;
      case C_CONTRACT_SCREEN_SHARING_ID:
        $val = array_merge($val, $widgetConfiguration['common'], $widgetConfiguration['sharing']);
        break;
      case C_CONTRACT_CHAT_BASIC_PLAN_ID:
        $val = array_merge($val, $widgetConfiguration['common'], $widgetConfiguration['chat']);
        break;
      default:
        throw Exception("不明なプランID: ".$m_contact_types_id);
    }
    return $val;
  }

  private function upgradeWidgetSettings($beforeContactTypeId, $afterContactTypeId, $targetCompanyId) {
    $currentWidgetSettings = $this->MWidgetSetting->find('first',[
        'conditions' => array(
          'm_companies_id' => intval($targetCompanyId)
        )]
    );
    if(!empty($currentWidgetSettings)) {
      $saveData = json_decode($currentWidgetSettings['MWidgetSetting']['style_settings'], TRUE);
      $currentWidgetSettings['MWidgetSetting']['style_settings'] = json_encode($this->getUpgradedWidgetSettings($saveData, $beforeContactTypeId, $afterContactTypeId), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
      $this->MWidgetSetting->save($currentWidgetSettings['MWidgetSetting'], false, array('style_settings'));
    } else {
      throw new Exception('ウィジェット設定の取得に失敗しました。 id: '.$targetCompanyId);
    }
  }

  private function getUpgradedWidgetSettings($currentSettings, $beforeContactTypeId, $afterContactTypeId) {
    $widgetConfiguration = Configure::read('default.widget');
    $val = [];
    if(strcmp($beforeContactTypeId, $afterContactTypeId) === 0) {
      // プラン変更なし
      $val = $currentSettings;
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_CHAT_BASIC_PLAN_ID) === 0
            && strcmp($afterContactTypeId, C_CONTRACT_CHAT_PLAN_ID) === 0) {
      // ベーシック => スタンダード
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_CHAT_BASIC_PLAN_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_SCREEN_SHARING_ID) === 0) {
      // ベーシック => シェアリング
      $val = array_merge($currentSettings, $widgetConfiguration['sharing']);
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_CHAT_BASIC_PLAN_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_FULL_PLAN_ID) === 0) {
      // ベーシック => プレミアム
      $val = array_merge($currentSettings, $widgetConfiguration['sharing']);
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_CHAT_PLAN_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_CHAT_BASIC_PLAN_ID) === 0) {
      // スタンダード => ベーシック
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_CHAT_PLAN_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_SCREEN_SHARING_ID) === 0) {
      // スタンダード => シェアリング
      $val = array_merge($currentSettings, $widgetConfiguration['sharing']);
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_CHAT_PLAN_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_FULL_PLAN_ID) === 0) {
      // スタンダード => プレミアム
      $val = array_merge($currentSettings, $widgetConfiguration['sharing']);
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_SCREEN_SHARING_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_CHAT_BASIC_PLAN_ID) === 0) {
      // シェアリング => ベーシック
      $val = array_merge($currentSettings, $widgetConfiguration['chat']);
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_SCREEN_SHARING_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_CHAT_PLAN_ID) === 0) {
      // シェアリング => スタンダード
      $val = array_merge($currentSettings, $widgetConfiguration['chat']);
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_SCREEN_SHARING_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_FULL_PLAN_ID) === 0) {
      // シェアリング => プレミアム
      $val = array_merge($currentSettings, $widgetConfiguration['chat']);
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_FULL_PLAN_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_CHAT_BASIC_PLAN_ID) === 0) {
      // プレミアム => ベーシック
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_FULL_PLAN_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_CHAT_PLAN_ID) === 0) {
      // プレミアム => スタンダード
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_CHAT_BASIC_PLAN_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_SCREEN_SHARING_ID) === 0) {
      // プレミアム => シェアリング
    }
    return $val;
  }

  private function getDefaultChatBasicConfigurations() {
    return Configure::read('default.chat.basic');
  }

  private function getDefaultDictionaryConfigurations() {
    return Configure::read('default.dictionary');
  }

  private function getDefaultAutomessageConfigurations() {
    return Configure::read('default.autoMessages');
  }

  private function getDefaultMailTemplateConfigurations() {
    return Configure::read('default.mail.templates');
  }

  private function isChatEnable($m_contact_types_id) {
    return $m_contact_types_id === C_CONTRACT_FULL_PLAN_ID
        || $m_contact_types_id === C_CONTRACT_CHAT_PLAN_ID
        || $m_contact_types_id === C_CONTRACT_CHAT_BASIC_PLAN_ID;
  }

  private function isAdvancedChatEnable($m_contact_types_id) {
    return $m_contact_types_id === C_CONTRACT_FULL_PLAN_ID
        || $m_contact_types_id === C_CONTRACT_CHAT_PLAN_ID;
  }

  private function convertActivityToJSON($activity) {
    return json_encode($activity, JSON_UNESCAPED_UNICODE);
  }

  private function generateRandomPassword($length) {
    $str = array_merge(range('a', 'z'), range('0', '9'), range('A', 'Z'));
    $r_str = null;
    for ($i = 0; $i < $length; $i++) {
      $r_str .= $str[rand(0, count($str) - 1)];
    }
    return $r_str;
  }

  private function isOverAllUserCountLimit() {
    $maxCreateUserCount = Configure::read('limitation.createUserCount');
    return $maxCreateUserCount <= $this->getAllUserCount();
  }

  private function getAllUserCount() {
    $addUserCount = $this->MCompany->find('first', array(
      'fields' => array('SUM(MCompany.limit_users) as allUsers')
    ));
    return intval($addUserCount[0]['allUsers']);
  }
}