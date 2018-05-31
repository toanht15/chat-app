<?php
/**
 * 企業用情報登録コントローラー
 * User: masashi_shimizu
 * Date: 2017/08/08
 * Time: 12:09
 * @property TChatbotScenario $TChatbotScenario
 * @property MMailTransmissionSetting $MMailTransmissionSetting
 * @property MMailTemplate $MMailTemplate
 */

App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
class ContractController extends AppController
{
  const ML_MAIL_ADDRESS= "cloud-service@medialink-ml.co.jp";
  const ML_MAIL_ADDRESS_AND_ALEX = "cloud-service@medialink-ml.co.jp,alexandre.mercier@medialink-ml.co.jp";
  const API_CALL_TIMEOUT = 5;
  const COMPANY_NAME = "##COMPANY_NAME##";
  const PASSWORD = "##PASSWORD##";
  const BUSINESS_MODEL = "##BUSINESS_MODEL##";
  /** 申込者 */
  const DEPARTMENT = "##DEPARTMENT##";
  const POSITION = "##POSITION##";
  const USER_NAME = "##USER_NAME##";
  const MAIL_ADDRESS = "##MAIL_ADDRESS##";
  const PHONE_NUMBER = "##PHONE_NUMBER##";
  /** 管理者 */
  const ADMIN_DEPARTMENT = "##ADMIN_DEPARTMENT##";
  const ADMIN_POSITION = "##ADMIN_POSITION##";
  const ADMIN_USER_NAME = "##ADMIN_USER_NAME##";
  const ADMIN_MAIL_ADDRESS = "##ADMIN_MAIL_ADDRESS##";
  const URL = "##URL##";
  const OTHER = "##OTHER##";
  const PLAN_NAME = "##PLAN_NAME##";
  const BEGIN_DATE = "##BEGIN_DATE##";
  const END_DATE = "##END_DATE##";
  const USABLE_USER_COUNT = "##USABLE_USER_COUNT##";
  const OPTION_COMPANY_INFO = "##OPTION_COMPANY_INFO##";
  const OPTION_SCENARIO = "##OPTION_SCENALIO##";
  const OPTION_CAPTURE = "##OPTION_CAPTURE##";

  public $components = array('MailSender');
  public $uses = array('MCompany',
    'MAgreements',
    'MUser',
    'MWidgetSetting',
    'MChatSetting',
    'TAutoMessages',
    'TDictionaries',
    'TDictionaryCategory',
    'MMailTemplate',
    'MMailTransmissionSetting',
    'TransactionManager',
    'TMailTransmissionLog',
    'MSystemMailTemplate',
    'TSendSystemMailSchedule',
    'MJobMailTemplate',
    'TChatbotScenario'
  );

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
          'type' => 'left',
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
      'group' => [
        'MCompany.id'
      ]
    ]
  ];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', 'サイトキー管理');
    $this->Auth->allow(['add','remoteSaveForm']);
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
      $data = $this->getParams();
      $password = $this->generateRandomPassword(8);
      $data['Contract']['user_password'] = $password;

      try {
        $addedCompanyInfo = $this->processTransaction($data['MCompany'], $data['Contract'], $data['MAgreements']);
        $jobMailTemplateData = $this->MJobMailTemplate->find('all');

        $mailTemplateData = $this->MSystemMailTemplate->find('all');

        $mailType = "false";
        //無料トライアルの場合
        if($data['MCompany']['trial_flg'] == 1) {
          foreach($mailTemplateData as $key => $mailTemplate) {
            if($mailTemplate['MSystemMailTemplate']['id'] == C_AFTER_FREE_APPLICATION_TO_CUSTOMER) {
              $mailType = $key;
            }
          }
        }
        //いきなり本契約の場合
        else {
          foreach($mailTemplateData as $key => $mailTemplate) {
            if($mailTemplate['MSystemMailTemplate']['id'] == C_AFTER_APPLICATION_TO_CUSTOMER) {
              $mailType = $key;
            }
          }
        }

        if($mailType !== "false") {
          $mailBodyData = $this->replaceAllMailConstString($data, $mailTemplateData[$mailType]['MSystemMailTemplate']['mail_body']);
          // 送信前にログを生成
          $this->TMailTransmissionLog->create();
          $this->TMailTransmissionLog->set(array(
            'm_companies_id' => 0, // システムメールなので0で登録
            'mail_type_cd' => 'TL001',
            'from_address' => MailSenderComponent::MAIL_SYSTEM_FROM_ADDRESS,
            'from_name' => $mailTemplateData[$mailType]['MSystemMailTemplate']['sender'],
            'to_address' => $data['MAgreements']['application_mail_address'],
            'subject' => $mailTemplateData[$mailType]['MSystemMailTemplate']['subject'],
            'body' => $mailBodyData,
            'send_flg' => 0
          ));
          $this->TMailTransmissionLog->save();
          $lastInsertId = $this->TMailTransmissionLog->getLastInsertId();

          //お客さん向け
          $sender = new MailSenderComponent();
          $sender->setFrom(self::ML_MAIL_ADDRESS);
          $sender->setFromName($mailTemplateData[$mailType]['MSystemMailTemplate']['sender']);
          $sender->setTo($data['MAgreements']['application_mail_address']);
          $sender->setSubject($mailTemplateData[$mailType]['MSystemMailTemplate']['subject']);
          $sender->setBody($mailBodyData);
          $sender->send();


          $now = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
          $this->TMailTransmissionLog->read(null, $lastInsertId);
          $this->TMailTransmissionLog->set([
            'send_flg' => 1,
            'sent_datetime' => $now->format("Y/m/d H:i:s")
          ]);
          $this->TMailTransmissionLog->save();
        }

        $mailType = "false";
        //無料トライアルの場合
        if($data['MCompany']['trial_flg'] == 1) {
          foreach($mailTemplateData as $key => $mailTemplate) {
            if($mailTemplate['MSystemMailTemplate']['id'] == C_AFTER_FREE_APPLICATION_TO_COMPANY) {
              $mailType = $key;
            }
          }
        }
        else {
          //いきなり本契約の場合
          foreach($mailTemplateData as $key => $mailTemplate) {
            if($mailTemplate['MSystemMailTemplate']['id'] == C_AFTER_APPLICATION_TO_COMPANY) {
              $mailType = $key;
            }
          }
        }

        if($mailType !== 'false') {
          //会社向け
          $sender = new MailSenderComponent();
          $sender->setFrom($data['MAgreements']['application_mail_address']);
          if(empty($data['MAgreements']['application_name'])) {
            $data['MAgreements']['application_name'] = '';
          }
          $sender->setFromName($data['MCompany']['company_name'].'　'.$data['MAgreements']['application_name']);
          $sender->setTo(self::ML_MAIL_ADDRESS_AND_ALEX);
          $sender->setSubject($mailTemplateData[$mailType]['MSystemMailTemplate']['subject']);

          $mailBodyData = $this->replaceAllMailConstString($data, $mailTemplateData[$mailType]['MSystemMailTemplate']['mail_body']);

          $sender->setBody($mailBodyData);
          $sender->send();
          return json_encode(array(
            'success' => true,
            'message' => "OK",
            'newCompanyId' => $addedCompanyInfo['id']
          ));
        }
      } catch(Exception $e) {
        $this->log("Exception Occured : ".$e->getMessage(), LOG_WARNING);
        $this->log($e->getTraceAsString(),LOG_WARNING);
        $this->response->statusCode(409);
        return json_encode([
          'success' => false,
          'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
      }
    }
    else {
      $businessModel = Configure::read('businessModelType');
      $this->set('businessModel',$businessModel);
    }
  }

  private function replaceAllMailConstString($data, $mailTemplateData) {
    if(!empty($data['MAgreements']['business_model'])) {
      if($data['MAgreements']['business_model'] == 1) {
        $data['MAgreements']['business_model'] = 'BtoB';
      }
      if($data['MAgreements']['business_model'] == 2) {
        $data['MAgreements']['business_model'] = 'BtoC';
      }
      if($data['MAgreements']['business_model'] == 3) {
        $data['MAgreements']['business_model'] = 'どちらも';
      }
    }
    $mailBodyData = $this->replaceConstToString($data['MCompany']['company_name'],self::COMPANY_NAME, $mailTemplateData);
    $mailBodyData = $this->replaceConstToString($data['MAgreements']['business_model'], self::BUSINESS_MODEL, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($data['MAgreements']['application_department'], self::DEPARTMENT, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($data['MAgreements']['application_position'], self::POSITION, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($data['MAgreements']['application_name'], self::USER_NAME, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($data['MAgreements']['application_mail_address'], self::MAIL_ADDRESS, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($data['MAgreements']['administrator_department'], self::ADMIN_DEPARTMENT, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($data['MAgreements']['administrator_position'], self::ADMIN_POSITION, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($data['MAgreements']['administrator_name'], self::ADMIN_USER_NAME, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($data['MAgreements']['administrator_mail_address'], self::ADMIN_MAIL_ADDRESS, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($data['MAgreements']['telephone_number'], self::PHONE_NUMBER, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($data['MAgreements']['installation_url'], self::URL, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($data['MCompany']['limit_users'], self::USABLE_USER_COUNT, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($data['Contract']['user_password'], self::PASSWORD, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($this->getPlanNameStr($data), self::PLAN_NAME, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($this->getBeginDate($data), self::BEGIN_DATE, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($this->getEndDate($data), self::END_DATE, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($this->getOptionCompanyInfoEnabled($data), self::OPTION_COMPANY_INFO, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($this->getOptionChatbotScenario($data), self::OPTION_SCENARIO, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($this->getOptionLaCoBrowse($data), self::OPTION_CAPTURE, $mailBodyData);
    $mailBodyData = $this->replaceConstToString($data['MAgreements']['note'], self::OTHER, $mailBodyData);

    return $mailBodyData;
  }

  private function replaceConstToString($string, $const, $body) {
    if(!empty($string)) {
      return str_replace($const, $string, $body);
    }
    else {
      return str_replace($const, "", $body);
    }
  }

  private function getPlanNameStr($data) {
    $planId = $data['MCompany']['m_contact_types_id'];
    switch(intval($planId)) {
      case 1:
        return 'プレミアムプラン';
      case 2:
        return 'スタンダードプラン';
      case 3:
        return 'シェアリングプラン';
      case 4:
        return 'ベーシックプラン';
      default:
        return '不明なプラン';
    }
  }

  private function getBeginDate($data) {
    if(intval($data['MCompany']['trial_flg']) === 1) {
      return $data['MAgreements']['trial_start_day'];
    } else {
      return $data['MAgreements']['agreement_start_day'];
    }
  }

  private function getEndDate($data) {
    if(intval($data['MCompany']['trial_flg']) === 1) {
      return $data['MAgreements']['trial_end_day'];
    } else {
      return $data['MAgreements']['agreement_end_day'];
    }
  }

  private function getOptionCompanyInfoEnabled($data) {
    if(!empty($data['MCompany']['options']['refCompanyData'])) {
      return '企業情報付与オプション：あり';
    } else {
      return '企業情報付与オプション：なし';
    }
  }

  private function getOptionChatbotScenario($data) {
    if(!empty($data['MCompany']['options']['chatbotScenario'])) {
      return 'チャットボットシナリオオプション：あり';
    } else {
      return 'チャットボットシナリオオプション：なし';
    }
  }

  private function getOptionLaCoBrowse($data) {
    if(!empty($data['MCompany']['options']['laCoBrowse'])) {
      return '画面キャプチャオプション：あり（最大同時セッション数：'.$data['MCompany']['la_limit_users'].'）';
    } else {
      return '画面キャプチャオプション：なし';
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
      $saveData['MCompany']['options']['hideRealtimeMonitor'] = !empty($coreSetting['hideRealtimeMonitor']) ? $coreSetting['hideRealtimeMonitor'] : false;
      $saveData['MCompany']['options']['monitorPollingMode'] = !empty($coreSetting['monitorPollingMode']) ? $coreSetting['monitorPollingMode'] : false;
      $companySaveData['MCompany']['core_settings'] = $this->getCoreSettingsFromContactTypesId($saveData['MCompany']['m_contact_types_id'], $saveData['MCompany']['options']);
      $this->MCompany->save($companySaveData,false);
      // 有効・無効でJSファイルの中身が変わるので書き換える
      if(!$saveData['MCompany']['options']['laCoBrowse']) {
        $companySaveData['la_limit_users'] = 0;
      }
      if($coreSetting['laCoBrowse'] !== boolval($saveData['MCompany']['options']['laCoBrowse'])) {
        $this->addCompanyJSFile($companyEditData['MCompany']['company_key'], $saveData['MCompany']['options']['laCoBrowse']);
      }

      if(empty($agreementEditData)) {
        $this->MAgreements->create();
        $this->MAgreements->set([
          'm_companies_id' => $companyEditData['MCompany']['id'],
          'trial_start_day' => $saveData['MAgreements']['application_day'],
          'trial_end_day' => $saveData['MAgreements']['application_day'],
          'agreement_start_day' => $saveData['MAgreements']['agreement_start_day'],
          'agreement_end_day' => $saveData['MAgreements']['agreement_end_day'],
          'application_department' => $saveData['MAgreements']['application_department'],
          'application_position' => $saveData['MAgreements']['application_position'],
          'application_name' => $saveData['MAgreements']['application_name'],
          'application_mail_address' => $saveData['MAgreements']['application_mail_address'],
          'administrator_department' => $saveData['MAgreements']['administrator_department'],
          'administrator_position' => $saveData['MAgreements']['administrator_position'],
          'administrator_name' => $saveData['MAgreements']['administrator_name'],
          'administrator_mail_address' => $saveData['MAgreements']['administrator_mail_address'],
          'installation_url' => $saveData['MAgreements']['installation_url'],
          'business_model' => $saveData['MAgreements']['business_model'],
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
      $editData['MCompany']['options']['chatbotScenario'] = json_decode($editData['MCompany']['core_settings'],TRUE)['chatbotScenario'];
      $editData['MCompany']['options']['laCoBrowse'] = json_decode($editData['MCompany']['core_settings'],TRUE)['laCoBrowse'];

      // ここまで
      $agreementData = $this->MAgreements->find('first',[
        'conditions' => array(
          'm_companies_id' => $editData['MCompany']['id']
        )]
      );

      $editData = array_merge($editData, $agreementData);

      $businessModel = Configure::read('businessModelType');
      $this->set('businessModel',$businessModel);
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

  /**
   * Console/Command/ExcelImportShellからも呼び出すためpublicとなっている。
   * @param $companyInfo
   * @param $userInfo
   * @param $agreementInfo
   * @return 一番最後に追加した
   * @throws Exception
   */
  public function processTransaction($companyInfo, $userInfo, $agreementInfo) {
    try {
      $transaction = $this->TransactionManager->begin();
      $addedCompanyInfo = $this->createCompany($companyInfo);
      $this->createAgreementInfo($addedCompanyInfo, $companyInfo,$userInfo,$agreementInfo);
      $this->createFirstAdministratorUser($addedCompanyInfo['id'], $userInfo, $agreementInfo);
      $this->addDefaultChatPersonalSettings($addedCompanyInfo['id'], $companyInfo);
      $this->addDefaultWidgetSettings($addedCompanyInfo['id'], $companyInfo);
      $relationIdAssoc = $this->addDefaultScenarioMessage($addedCompanyInfo['id'], $companyInfo);
      $this->addDefaultAutoMessages($addedCompanyInfo['id'], $companyInfo, $relationIdAssoc);
      $this->addDefaultDictionaries($addedCompanyInfo['id'], $companyInfo);
      $this->addDefaultMailTemplate($addedCompanyInfo['id'], $companyInfo);
      $this->addCompanyJSFile($addedCompanyInfo['companyKey'], $addedCompanyInfo['core_settings']['laCoBrowse']);
    } catch (Exception $e) {
      $this->TransactionManager->rollback($transaction);
      throw $e;
    }
    $this->TransactionManager->commit($transaction);
    return $addedCompanyInfo;
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
      $this->addDefaultScenarioMessage($targetCompanyId, $companyInfo);
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_SCREEN_SHARING_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_CHAT_PLAN_ID) === 0) {
      // シェアリング => スタンダード
      $this->upgradeWidgetSettings($beforeContactTypeId, $afterContactTypeId, $targetCompanyId);
      $this->addDefaultChatPersonalSettings($targetCompanyId, $companyInfo);
      $this->addDefaultAutoMessages($targetCompanyId, $companyInfo);
      $this->addDefaultDictionaries($targetCompanyId, $companyInfo);
      $this->addDefaultMailTemplate($targetCompanyId, $companyInfo);
      $this->addDefaultScenarioMessage($targetCompanyId, $companyInfo);
    } else if (strcmp($beforeContactTypeId, C_CONTRACT_SCREEN_SHARING_ID) === 0
      && strcmp($afterContactTypeId, C_CONTRACT_FULL_PLAN_ID) === 0) {
      // シェアリング => プレミアム
      $this->upgradeWidgetSettings($beforeContactTypeId, $afterContactTypeId, $targetCompanyId);
      $this->addDefaultChatPersonalSettings($targetCompanyId, $companyInfo);
      $this->addDefaultAutoMessages($targetCompanyId, $companyInfo);
      $this->addDefaultDictionaries($targetCompanyId, $companyInfo);
      $this->addDefaultMailTemplate($targetCompanyId, $companyInfo);
      $this->addDefaultScenarioMessage($targetCompanyId, $companyInfo);
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
      $insertData = [
        "company_name" => $companyInfo['company_name'],
        "company_key" => $companyKey,
        "m_contact_types_id" => $companyInfo['m_contact_types_id'],
        "limit_users" => $companyInfo['limit_users'],
        "core_settings" => $this->getCoreSettingsFromContactTypesId($companyInfo['m_contact_types_id'], $companyInfo['options']),
        "trial_flg" => $companyInfo['trial_flg']
      ];
      if ($companyInfo['options']['laCoBrowse']) {
        $insertData['la_limit_users'] = $companyInfo['la_limit_users'];
      } else {
        $insertData['la_limit_users'] = 0;
      }
      $this->MCompany->create();
      $this->MCompany->set($insertData);
      $this->MCompany->save();
    } catch(Exception $e) {
      throw $e;
    }
    return [
        'id' => $this->MCompany->getLastInsertID(),
        'companyKey' => $companyKey,
        'core_settings' => json_decode($this->getCoreSettingsFromContactTypesId($companyInfo['m_contact_types_id'], $companyInfo['options']), TRUE)
    ];
  }

  private function createAgreementInfo($addedCompanyInfo, $companyInfo, $userInfo, $agreementInfo) {
    $password = $this->generateRandomPassword(8);

    $this->MAgreements->create();
    if(empty($agreementInfo['application_name'])) {
      $agreementInfo['application_name'] = "";
    }
    if(empty($agreementInfo['application_department'])) {
      $agreementInfo['application_department'] = "";
    }
    if(empty($agreementInfo['application_position'])) {
      $agreementInfo['application_position'] = "";
    }
    if(empty($agreementInfo['installation_url'])) {
      $agreementInfo['installation_url'] = "";
    }
    if(empty($agreementInfo['telephone_number'])) {
      $agreementInfo['telephone_number'] = "";
    }
    if(empty($agreementInfo['business_model'])) {
      $agreementInfo['business_model'] = "";
    }
    if(empty($agreementInfo['note'])) {
      $agreementInfo['note'] = "";
    }
    if(empty($agreementInfo['agreement_start_day'])) {
      $agreementInfo['agreement_start_day'] = "";
    }
    if(empty($agreementInfo['agreement_end_day'])) {
      $agreementInfo['agreement_end_day'] = "";
    }
    if(empty($agreementInfo['trial_start_day'])) {
      $agreementInfo['trial_start_day'] = "";
    }
    if(empty($agreementInfo['trial_end_day'])) {
      $agreementInfo['trial_end_day'] = "";
    }
    if(empty($agreementInfo['memo'])) {
      $agreementInfo['memo'] = "";
    }

    $applicationMailAddress = '';
    if(!empty($agreementInfo['application_mail_address'])) {
      $applicationMailAddress = $agreementInfo['application_mail_address'];
    } else if(!empty($userInfo["user_mail_address"])) {
      $applicationMailAddress = $userInfo["user_mail_address"];
    }

    $administratorMailAddress = '';
    if(!empty($agreementInfo['administrator_mail_address'])) {
      $administratorMailAddress = $agreementInfo['administrator_mail_address'];
    } else if(!empty($userInfo["user_mail_address"])) {
      $administratorMailAddress = $userInfo["user_mail_address"];
    }

      $this->MAgreements->set([
      'm_companies_id' => $addedCompanyInfo['id'],
      'company_name' => $companyInfo['company_name'],
      'business_model' => $agreementInfo['business_model'],
      'application_day' => date("Y-m-d"), // FIXME（自動発行）
      'trial_start_day' => $agreementInfo['trial_start_day'],
      'trial_end_day' => $agreementInfo['trial_end_day'],
      'agreement_start_day' => $agreementInfo['agreement_start_day'],
      'agreement_end_day' => $agreementInfo['agreement_end_day'],
      'application_department' => $agreementInfo['application_department'],
      'application_position' => $agreementInfo['application_position'],
      'application_name' => $agreementInfo['application_name'],
      'application_mail_address' => $applicationMailAddress,
      'administrator_department' => $agreementInfo['administrator_department'],
      'administrator_position' => $agreementInfo['administrator_position'],
      'administrator_name' => $agreementInfo['administrator_name'],
      'administrator_mail_address' => $administratorMailAddress,
      'installation_url' => $agreementInfo['installation_url'],
      'admin_password' => $password,
      'telephone_number' => $agreementInfo['telephone_number'],
      'note' => $agreementInfo['note'],
      'memo' => $agreementInfo['memo']
    ]);
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
    $this->MAgreements->save();
    $this->MUser->save();
  }

  private function createFirstAdministratorUser($m_companies_id, $userInfo, $agreementInfo) {
    $userInfo["user_name"] = 'テストユーザー';
    $userInfo["user_display_name"] = 'テストユーザー';
    $mailAddress = '';
    if(!empty($agreementInfo['administrator_mail_address'])) {
      $mailAddress = $agreementInfo['administrator_mail_address'];
    }

    $errors = [];
    $tmpData = [
        "m_companies_id" => $m_companies_id,
        "user_name" => $userInfo["user_name"],
        "display_name" => $userInfo["user_display_name"],
        "mail_address" => $mailAddress,
        "change_password_flg" => !empty($userInfo['no_change_password_flg']) ? $userInfo['no_change_password_flg'] : C_NO_CHANGE_PASSWORD_FLG,
        "permission_level" => C_AUTHORITY_ADMIN,
        "new_password" => $userInfo["user_password"]
    ];
    $this->MUser->create();
    $this->MUser->set($tmpData);
    if(!$this->MUser->validates()) {
      $this->MAgreements->rollback();
      $this->MUser->rollback();
      throw new Exception(json_encode($this->MUser->validationErrors, JSON_UNESCAPED_UNICODE));
    }
    else {
      $this->MUser->save();
    }
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
    $default = $this->getDefaultChatBasicConfigurations($companyInfo['options']['chatbotScenario']);
    $this->MChatSetting->create();
    $this->MChatSetting->set(array(
      "m_companies_id" => $m_companies_id,
      "sc_flg" => $default['sc_flg'],
      "sc_default_num" => $default['sc_default_num'],
      "outside_hours_sorry_message" => $default['outside_hours_sorry_message'],
      "wating_call_sorry_message" => $default['wating_call_sorry_message'],
      "no_standby_sorry_message" => $default['no_standby_sorry_message'],
      "sorry_message" => ""
    ));
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

  private function addDefaultAutoMessages($m_companies_id, $companyInfo, $addedRelationScenarioIds) {
    if(!$this->isChatEnable($companyInfo['m_contact_types_id'])) return;
    $autoMessages = $this->TAutoMessages->find('all',[
      'conditions' => array(
        'm_companies_id' => $m_companies_id
      )]
    );
    if(empty($autoMessages)) {
      $default = $this->getDefaultAutomessageConfigurations($companyInfo['options']['chatbotScenario']);
      foreach($default as $index => $item) {
        $this->TAutoMessages->create();
        $data = [
          "m_companies_id" => $m_companies_id,
          "name" => $item['name'],
          "trigger_type" => $item['trigger_type'],
          "activity" => $this->convertActivityToJSON($item['activity']),
          "action_type" => $item['action_type'],
          "sort" => $item['sort'],
          "active_flg" => $item['active_type']
        ];
        if(array_key_exists($index, $addedRelationScenarioIds) && array_key_exists('t_chatbot_scenario_id', $item)) {
          $data['t_chatbot_scenario_id'] = $addedRelationScenarioIds[$index];
        }
        $this->TAutoMessages->set($data);
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

  /**
   * シナリオのデフォルト設定を追加する
   * ※ この処理はConsole/AddScenarioSampleShellでもコールするためpublicとなっている
   * @param $m_companies_id
   * @param $companyInfo
   * @param bool $forceInsert
   * @throws Exception
   */
  public function addDefaultScenarioMessage($m_companies_id, $companyInfo, $forceInsert = false) {
    $autoMessageRelationAssoc = array();
    if(!$this->isChatEnable($companyInfo['m_contact_types_id'])) return;
    $scenarios = $this->TChatbotScenario->find('all',array(
        'conditions' => array(
          'm_companies_id' => $m_companies_id
        )
    ));
    if(empty($scenarios)) {
      $default = $this->getDefaultScenarioConfigurations();
      foreach($default as &$scenario) {
        $actions = &$scenario['activity']['scenarios'];
        foreach($actions as &$action) {
          if(strcmp($action['actionType'], 4) === 0) {
            // メール転送設定とテンプレート設定を追加
            $mailTransmissionSetting = $action['mailTransmission'];
            $mailTemplateSetting = $action['mailTemplate'];
            $this->MMailTransmissionSetting->create();
            $this->MMailTransmissionSetting->set(array(
              'm_companies_id' => $m_companies_id,
              'from_address' => $mailTransmissionSetting['from_address'],
              'from_name' => $mailTransmissionSetting['from_name'],
              'to_address' => $mailTransmissionSetting['to_address'],
              'subject' => $mailTransmissionSetting['subject']
            ));
            if(!$this->MMailTransmissionSetting->save()) {
              throw new Exception('シナリオのメール送信設定登録に失敗しました');
            }
            unset($action['mailTransmission']);
            $action['mMailTransmissionId'] = $this->MMailTransmissionSetting->getLastInsertId();

            $this->MMailTemplate->create();
            $this->MMailTemplate->set(array(
              'm_companies_id' => $m_companies_id,
              'mail_type_cd' => $mailTemplateSetting['mail_type_cd'],
              'template' => $mailTemplateSetting['template']
            ));
            if(!$this->MMailTemplate->save()) {
              throw new Exception('シナリオのメールテンプレート設定登録に失敗しました');
            }
            unset($action['mailTransmission']);
            $action['mMailTemplateId'] = $this->MMailTemplate->getLastInsertId();
          }
        }
        $this->TChatbotScenario->create();
        $this->TChatbotScenario->set(array(
          "m_companies_id" => $m_companies_id,
          "name" => $scenario['name'],
          "activity" => $this->convertActivityToJSON($scenario['activity']),
          "del_flg" => $scenario['del_flg'],
          "sort" => $scenario['sort']
        ));
        $this->TChatbotScenario->save();
        if(array_key_exists('relation_auto_message_index', $scenario)) {
          $autoMessageRelationAssoc[$scenario['relation_auto_message_index']] = $this->TChatbotScenario->getLastInsertId();
        }
      }
    } else if($forceInsert) {
      // 既存設定があることを考慮して今のソート番号を取得
      $lastScenario = $this->TChatbotScenario->find('first', array(
        'conditions' => array(
          'm_companies_id' => $m_companies_id
        ),
        'order' => array(
          'sort' => 'DESC'
        )
      ));
      $sortNum = 0;
      if(!empty($lastScenario)) {
        $sortNum = (int)$lastScenario['TChatbotScenario']['sort'];
      }
      $default = $this->getDefaultScenarioConfigurations();
      foreach($default as &$scenario) {
        $actions = &$scenario['activity']['scenarios'];
        foreach($actions as &$action) {
          if(strcmp($action['actionType'], 4) === 0) {
            // メール転送設定とテンプレート設定を追加
            $mailTransmissionSetting = $action['mailTransmission'];
            $mailTemplateSetting = $action['mailTemplate'];
            $this->MMailTransmissionSetting->create();
            $this->MMailTransmissionSetting->set(array(
              'm_companies_id' => $m_companies_id,
              'from_address' => $mailTransmissionSetting['from_address'],
              'from_name' => $mailTransmissionSetting['from_name'],
              'to_address' => $mailTransmissionSetting['to_address'],
              'subject' => $mailTransmissionSetting['subject']
            ));
            if(!$this->MMailTransmissionSetting->save()) {
              throw new Exception('シナリオのメール送信設定登録に失敗しました');
            }
            unset($action['mailTransmission']);
            $action['mMailTransmissionId'] = $this->MMailTransmissionSetting->getLastInsertId();

            $this->MMailTemplate->create();
            $this->MMailTemplate->set(array(
              'm_companies_id' => $m_companies_id,
              'mail_type_cd' => $mailTemplateSetting['mail_type_cd'],
              'template' => $mailTemplateSetting['template']
            ));
            if(!$this->MMailTemplate->save()) {
              throw new Exception('シナリオのメールテンプレート設定登録に失敗しました');
            }
            unset($action['mailTransmission']);
            $action['mMailTemplateId'] = $this->MMailTemplate->getLastInsertId();
          }
        }
        $sortNum = $sortNum + 1;
        $this->TChatbotScenario->create();
        $this->TChatbotScenario->set(array(
          "m_companies_id" => $m_companies_id,
          "name" => $scenario['name'],
          "activity" => $this->convertActivityToJSON($scenario['activity']),
          "del_flg" => $scenario['del_flg'],
          "sort" => $sortNum
        ));
        $this->TChatbotScenario->save();
        if(array_key_exists('relation_auto_message_index', $scenario)) {
          $autoMessageRelationAssoc[$scenario['relation_auto_message_index']] = $this->TChatbotScenario->getLastInsertId();
        }
      }
    }
    return $autoMessageRelationAssoc;
  }

  private function addCompanyJSFile($companyKey, $isLaCoBrowseEnabled) {
    $path = C_COMPANY_JS_TEMPLATE_FILE;
    if($isLaCoBrowseEnabled) {
      $path = C_COMPANY_LA_JS_TEMPLATE_FILE;
    }
    $templateData = new File($path);
    $contents = $templateData->read();
    $companyKeyReplaced = str_replace('##COMPANY_KEY##', $companyKey, $contents);
    $nodeServerReplaced = str_replace('##NODE_SERVER_URL##',C_NODE_SERVER_ADDR.C_NODE_SERVER_WS_PORT, $companyKeyReplaced);
    $replacedContents = str_replace('##LA_SERVER_URL##',C_LA_SERVER_ADDR, $nodeServerReplaced);
    // 書き換えた内容を<companyKey>.jsで保存
    $saveFile = new File(C_COMPANY_JS_FILE_DIR.'/'.$companyKey.'.js', true, 0644);
    $saveFile->open('w');
    $saveFile->write($replacedContents);
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
        // チャットスタンダードプランはウェブ接客コード表示を「表示する」にする
        $val['showAccessId'] = 1; // デフォルト：keyなし
        break;
      case C_CONTRACT_SCREEN_SHARING_ID:
        $val = array_merge($val, $widgetConfiguration['common'], $widgetConfiguration['sharing']);
        // シェアリングプランはウィジェットサイズは小にする
        $val['widgetSizeType'] = "1";
        $val['headerTextSize'] = "14";
        $val['seTextSize'] = "12";
        $val['reTextSize'] = "12";
        break;
      case C_CONTRACT_CHAT_BASIC_PLAN_ID:
        $val = array_merge($val, $widgetConfiguration['common'], $widgetConfiguration['chat']);
        // チャットベーシックプランはウェブ接客コード表示を「表示する」にする
        $val['showAccessId'] = 1; // デフォルト：keyなし
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

  private function getDefaultChatBasicConfigurations($withScenarioOptions) {
    if($withScenarioOptions) {
      return Configure::read('default.chat.basic_with_scenario');
    } else {
      return Configure::read('default.chat.basic_without_scenario');
    }
  }

  private function getDefaultDictionaryConfigurations() {
    return Configure::read('default.dictionary');
  }

  private function getDefaultAutomessageConfigurations($withScenarioOptions) {
    if($withScenarioOptions) {
      return Configure::read('default.autoMessages_with_scenario');
    } else {
      return Configure::read('default.autoMessages_without_scenario');
    }
  }

  private function getDefaultMailTemplateConfigurations() {
    return Configure::read('default.mail.templates');
  }

  private function getDefaultScenarioConfigurations() {
    return Configure::read('default.scenario');
  }

  private function isChatEnable($m_contact_types_id) {
    return strcmp($m_contact_types_id, C_CONTRACT_FULL_PLAN_ID) === 0
        || strcmp($m_contact_types_id, C_CONTRACT_CHAT_PLAN_ID) === 0
        || strcmp($m_contact_types_id, C_CONTRACT_CHAT_BASIC_PLAN_ID) === 0;
  }

  private function isAdvancedChatEnable($m_contact_types_id) {
    return strcmp($m_contact_types_id, C_CONTRACT_FULL_PLAN_ID) === 0
        || strcmp($m_contact_types_id, C_CONTRACT_CHAT_PLAN_ID) === 0;
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