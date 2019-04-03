<?php
/**
 * TAutoMessagesController controller.
 * オートメッセージコントローラー
 * @property TransactionManager $TransactionManager
 * @property TAutoMessage $TAutoMessage
 * @property MOperatingHour $MOperatingHour
 * @property MMailTransmissionSetting $MMailTransmissionSetting
 * @property MMailTemplate $MMailTemplate
 * @property MWidgetSetting $MWidgetSetting
 * @property TChatbotScenario $TChatbotScenario
 */

App::uses('WidgetSettingController', 'Controller');
App::uses('AutoMessageException', 'Lib/Error');

class TAutoMessagesController extends WidgetSettingController
{
  const TEMPLATE_FILE_NAME = "auto_message_template.xlsx";
  const FULL_TEMPLATE_FILE_NAME = "auto_message_setting_template.xlsx";

  public $uses = array(
    'TransactionManager',
    'TAutoMessage',
    'MOperatingHour',
    'MMailTransmissionSetting',
    'MMailTemplate',
    'MWidgetSetting',
    'TChatbotScenario',
    'TChatbotDiagram'
  );
  public $components = array('AutoMessageExcelExport', 'NodeSettingsReload', 'AutoMessageExcelImport');
  public $helpers = array('AutoMessage');
  public $paginate = array(
    'TAutoMessage' => array(
      'limit' => 100,
      'order' => array(
        'TAutoMessage.sort' => 'asc',
        'TAutoMessage.id' => 'asc'
      ),
      'fields' => array('TAutoMessage.*', 'TChatbotScenario.id', 'TChatbotScenario.name', 'TChatbotDiagram.name'),
      'conditions' => array('TAutoMessage.del_flg != ' => 1),
      'joins' => array(
        array(
          'type' => 'LEFT',
          'table' => 't_chatbot_scenarios',
          'alias' => 'TChatbotScenario',
          'conditions' => array(
            'TAutoMessage.t_chatbot_scenario_id = TChatbotScenario.id'
          )
        ),
        array(
          'type' => 'LEFT',
          'table' => 't_chatbot_diagrams',
          'alias' => 'TChatbotDiagram',
          'conditions' => array(
            'TAutoMessage.t_chatbot_diagram_id = TChatbotDiagram.id'
          )
        )
      ),
      'recursive' => -1
    )
  );
  public $outMessageIfType;
  public $outMessageTriggerList;

  public $coreSettings = null;
  public $styleSetting = array(
    'common' => array(
      'show_timing',
      'max_show_timing_site',
      'max_show_timing_page',
      'show_time',
      'max_show_time',
      'max_show_time_page',
      'show_position',
      'show_access_id',
      'widget_size_type',
      'title',
      'show_subtitle',
      'sub_title',
      'show_description',
      'description',
      'show_main_image',
      'main_image',
      'show_chatbot_icon',
      'chatbot_icon_type',
      'chatbot_icon',
      'show_operator_icon',
      'operator_icon_type',
      'operator_icon',
      'radius_ratio',
      'box_shadow',
      'minimize_design_type',
      'close_button_setting',
      'close_button_mode_type',
      'bannertext',
      'widget_custom_height',
      'widget_custom_width',
      /* カラー設定styat */
      'color_setting_type',
      'main_color',
      'string_color',
      'message_text_color',
      'other_text_color',
      'header_text_size',
      'widget_border_color',
      'chat_talk_border_color',
      'header_background_color',
      'sub_title_text_color',
      'description_text_color',
      'chat_talk_background_color',
      'c_name_text_color',
      're_text_color',
      're_text_size',
      're_background_color',
      're_border_color',
      're_border_none',
      'se_text_color',
      'se_text_size',
      'se_background_color',
      'se_border_color',
      'se_border_none',
      'chat_message_background_color',
      'message_box_text_color',
      'message_box_text_size',
      'message_box_background_color',
      'message_box_border_color',
      'message_box_border_none',
      'chat_send_btn_text_color',
      'chat_send_btn_text_size',
      'chat_send_btn_background_color',
      'widget_inside_border_color',
      'widget_inside_border_none',
      'widget_title_top_type',
      'widget_title_name_type',
      'widget_title_explain_type', /* カラー設定end */
      'btw_button_margin',
      'line_button_margin',
      'sp_banner_position',
      'sp_scroll_view_setting',
      'sp_banner_vertical_position_from_top',
      'sp_banner_vertical_position_from_bottom',
      'sp_banner_horizontal_position',
      'sp_banner_text',
      'sp_widget_view_pattern'
    ),
    'synclo' => array('tel', 'content', 'display_time_flg', 'time_text'),
    'chat' => array(
      'chat_init_show_textarea',
      'chat_radio_behavior',
      'chat_trigger',
      'show_name',
      'show_automessage_name',
      'show_op_name',
      'chat_message_design_type',
      'chat_message_arrow_position',
      'chat_message_with_animation',
      'chat_message_copy',
      'sp_show_flg',
      'sp_header_light_flg',
      'sp_auto_open_flg',
      'sp_maximize_size_type'
    ),
  );

  public function beforeFilter()
  {
    parent::beforeFilter();
    $this->set('title_for_layout', 'トリガー機能');
    $this->outMessageIfType = Configure::read('outMessageIfType');
    $this->outMessageTriggerList = Configure::read('outMessageTriggerList');
    $operatingHourData = $this->MOperatingHour->find('first', [
      'conditions' => [
        'm_companies_id' => $this->userInfo['MCompany']['id']
      ]
    ]);
    if (empty($operatingHourData)) {
      $operatingHourData['MOperatingHour']['active_flg'] = 2;
    }
    $this->set('operatingHourData', $operatingHourData['MOperatingHour']['active_flg']);
  }

  public function afterFilter()
  {
    if ($this->request->is('put') || $this->request->is('post')) {
      NodeSettingsReloadComponent::reloadAutoMessages($this->userInfo['MCompany']['company_key']);
    }
  }

  /**
   * 一覧画面
   * @return void
   * */
  public function index()
  {

    $this->paginate['TAutoMessage']['conditions']['TAutoMessage.m_companies_id'] = $this->userInfo['MCompany']['id'];
    $data = $this->paginate('TAutoMessage');
    foreach ($data as $index => $value) {
      $activity = json_decode($value['TAutoMessage']['activity'], true);
      foreach ($activity['conditions'] as $key => $val) {
        $targetKey = $key;
        if ($targetKey >= 4) {
          $targetKey = $targetKey + 1;
        } else {
          if ($targetKey === 10) {
            $targetKey = 4;
          }
        }
        $activity = $this->convertOldIFData($targetKey, $val, $activity, $key);
      }
      $data[$index]['TAutoMessage']['activity'] = json_encode($activity);
    }

    // オートメッセージ一覧を取得する
    $otherAllAutoMessages = $this->TAutoMessage->find('all', array(
      'order' => array(
        'TAutoMessage.sort' => 'asc'
      ),
      'conditions' => array(
        'TAutoMessage.m_companies_id' => $this->userInfo['MCompany']['id'],
        'TAutoMessage.del_flg' => 0
      )
    ));

    $otherAutoMessages = $this->convertCallAutomessageList($otherAllAutoMessages, false);

    $this->set('autoMessageList', $otherAutoMessages['data']);
    $this->set('settingList', $data);
    $this->_viewElement();
  }

  /**
   * 登録画面
   * @return void
   * */
  public function add()
  {
    if ($this->request->is('post')) {
      if (!empty($this->request->data['TAutoMessage']['t_chatbot_scenario_id']) &&
        !(isset($this->coreSettings[C_COMPANY_USE_CHATBOT_SCENARIO]) && $this->coreSettings[C_COMPANY_USE_CHATBOT_SCENARIO])) {
        $this->redirect("/");
      }
      $this->_entry($this->request->data);
    }

    $operatingHourData = $this->MOperatingHour->find('first', [
      'conditions' => [
        'm_companies_id' => $this->userInfo['MCompany']['id']
      ]
    ]);
    if (empty($operatingHourData)) {
      $operatingHourData['MOperatingHour']['active_flg'] = 2;
    }
    $this->set('operatingHourData', $operatingHourData['MOperatingHour']['active_flg']);

    // シミュレーター表示用ウィジェット設定の取得
    $this->request->data['widgetSettings'] = $this->_getWidgetSettings();

    // シナリオ設定の一覧を取得する
    $chatbotScenario = $this->TChatbotScenario->coFind('list', [
      'fields' => ['id', 'name'],
      'order' => [
        'TChatbotScenario.sort' => 'asc',
        'TChatbotScenario.id' => 'asc'
      ],
      'conditions' => [
        'TChatbotScenario.del_flg != ' => 1
      ]
    ]);
    $this->request->data['chatbotScenario'] = $chatbotScenario;

    // チャットツリー設定の一覧を取得する
    $chatbotDiagram = $this->TChatbotDiagram->find('list', array(
      'fields' => array('id', 'name'),
      'order' => array(
        'TChatbotDiagram.sort' => 'asc',
        'TChatbotDiagram.id' => 'asc'
      ),
      'conditions' => array(
        'TChatbotDiagram.m_companies_id' => $this->userInfo['MCompany']['id'],
        'TChatbotDiagram.del_flg != ' => 1
      )
    ));

    $this->request->data['chatbotDiagram'] = $chatbotDiagram;

    // オートメッセージ一覧を取得する
    $otherAllAutoMessages = $this->TAutoMessage->find('all', array(
      'order' => array(
        'TAutoMessage.sort' => 'asc'
      ),
      'conditions' => array(
        'TAutoMessage.m_companies_id' => $this->userInfo['MCompany']['id'],
        'TAutoMessage.del_flg' => 0
      )
    ));

    $otherAutoMessages = $this->convertCallAutomessageList($otherAllAutoMessages, false);

    $this->request->data['otherAutoMessages'] = $otherAutoMessages;

    $this->_viewElement();
  }

  /**
   * 更新画面
   * @return void
   * */
  public function edit($id = null)
  {
    if ($this->request->is('put')) {
      if (!empty($this->request->data['TAutoMessage']['t_chatbot_scenario_id']) &&
        (!(isset($this->coreSettings[C_COMPANY_USE_CHATBOT_SCENARIO]) && $this->coreSettings[C_COMPANY_USE_CHATBOT_SCENARIO]))) {
        $this->redirect("/");
      }
      $this->_entry($this->request->data);
    } else {
      // 確実なデータを取得するために企業IDを指定する形とする
      $editData = $this->TAutoMessage->coFind("all", [
        'conditions' => [
          'TAutoMessage.id' => $id
        ]
      ]);

      //オートメッセージ　営業時間を4番目に入れたので並び替え処理
      $changeEditData = json_decode($editData[0]['TAutoMessage']['activity'], true);
      $changeEditData['conditions'] = array_reverse($changeEditData['conditions'], true);
      foreach ($changeEditData['conditions'] as $key => $val) {
        if ($key >= 4) {
          unset($changeEditData['conditions'][$key]);
          $changeEditData['conditions'][$key + 1] = json_decode($editData[0]['TAutoMessage']['activity'],
            true)['conditions'][$key];
        }
      }

      foreach ($changeEditData['conditions'] as $key => $val) {
        if ($key === 11) {
          unset($changeEditData['conditions'][11]);
          $changeEditData['conditions'][4] = json_decode($editData[0]['TAutoMessage']['activity'],
            true)['conditions'][10];
        }

        if ($key > 11) {
          unset($changeEditData['conditions'][$key]);
          $changeEditData['conditions'][$key - 1 ] = json_decode($editData[0]['TAutoMessage']['activity'],
            true)['conditions'][$key - 1];
        }
        $changeEditData = $this->convertOldIFData($key, $val, $changeEditData, $key);
      }

      if (empty($editData) || (!empty($editData) && empty($editData[0]))) {
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.notFoundId'));
        $this->redirect('/TAutoMessages/index');
      }

      $changeEditData = json_encode($changeEditData);
      $editData[0]['TAutoMessage']['activity'] = $changeEditData;
      $json = json_decode($editData[0]['TAutoMessage']['activity'], true);
      $this->request->data = $editData[0];
      $this->request->data['TAutoMessage']['condition_type'] = (!empty($json['conditionType'])) ? $json['conditionType'] : "";
      $this->request->data['TAutoMessage']['action'] = (!empty($json['message'])) ? $json['message'] : "";
      $this->request->data['TAutoMessage']['widget_open'] = (!empty($json['widgetOpen'])) ? $json['widgetOpen'] : "";
      $this->request->data['TAutoMessage']['chat_textarea'] = (!empty($json['chatTextarea'])) ? $json['chatTextarea'] : "";
      $this->request->data['TAutoMessage']['cv'] = (!empty($json['cv'])) ? $json['cv'] : "";
      if (strcmp($this->request->data['TAutoMessage']['action_type'], 1) === 0) {
        // チャットを送信するアクションの場合は明示的にシナリオ紐づけを解除する
        $this->request->data['TAutoMessage']['t_chatbot_scenario_id'] = 0;
      }
      if (array_key_exists('send_mail_flg', $editData[0]['TAutoMessage'])) {
        $this->request->data['TAutoMessage']['send_mail_flg'] = $editData[0]['TAutoMessage']['send_mail_flg'];
        $transmissionData = $this->MMailTransmissionSetting->findById($editData[0]['TAutoMessage']['m_mail_transmission_settings_id']);
        if (!empty($transmissionData)) {
          $this->request->data['TAutoMessage']['m_mail_transmission_settings_id'] = $editData[0]['TAutoMessage']['m_mail_transmission_settings_id'];
          $splitedMailAddresses = explode(',', $transmissionData['MMailTransmissionSetting']['to_address']);
          $this->request->data['TAutoMessage']['mail_address_1'] = !empty($splitedMailAddresses[0]) ? $splitedMailAddresses[0] : $transmissionData;
          $this->request->data['TAutoMessage']['mail_address_2'] = !empty($splitedMailAddresses[1]) ? $splitedMailAddresses[1] : $editData[0]['TAutoMessage']['m_mail_transmission_settings_id'];
          $this->request->data['TAutoMessage']['mail_address_3'] = !empty($splitedMailAddresses[2]) ? $splitedMailAddresses[2] : "";
          $this->request->data['TAutoMessage']['mail_address_4'] = !empty($splitedMailAddresses[3]) ? $splitedMailAddresses[3] : "";
          $this->request->data['TAutoMessage']['mail_address_5'] = !empty($splitedMailAddresses[4]) ? $splitedMailAddresses[4] : "";
          $this->request->data['TAutoMessage']['subject'] = !empty($transmissionData['MMailTransmissionSetting']['subject']) ? $transmissionData['MMailTransmissionSetting']['subject'] : "subject";
          $this->request->data['TAutoMessage']['from_name'] = !empty($transmissionData['MMailTransmissionSetting']['from_name']) ? $transmissionData['MMailTransmissionSetting']['from_name'] : "from_name";
        }
        $this->request->data['TAutoMessage']['m_mail_template_id'] = $editData[0]['TAutoMessage']['m_mail_template_id'];
      }
    }

    // シミュレーター表示用ウィジェット設定の取得
    $this->request->data['widgetSettings'] = $this->_getWidgetSettings();

    // シナリオ設定の一覧を取得する
    $chatbotScenario = $this->TChatbotScenario->coFind('list', [
      'fields' => ['id', 'name'],
      'order' => [
        'TChatbotScenario.sort' => 'asc',
        'TChatbotScenario.id' => 'asc'
      ],
      'conditions' => [
        'TChatbotScenario.del_flg != ' => 1
      ]
    ]);
    $this->request->data['chatbotScenario'] = $chatbotScenario;

    // チャットツリー設定の一覧を取得する
    $chatbotDiagram = $this->TChatbotDiagram->find('list', array(
      'fields' => array('id', 'name'),
      'order' => array(
        'TChatbotDiagram.sort' => 'asc',
        'TChatbotDiagram.id' => 'asc'
      ),
      'conditions' => array(
        'TChatbotDiagram.m_companies_id' => $this->userInfo['MCompany']['id'],
        'TChatbotDiagram.del_flg != ' => 1
      )
    ));

    $this->request->data['chatbotDiagram'] = $chatbotDiagram;

    // オートメッセージ一覧を取得する
    $otherAllAutoMessages = $this->TAutoMessage->find('all', array(
      'order' => array(
        'TAutoMessage.sort' => 'asc'
      ),
      'conditions' => array(
        'TAutoMessage.m_companies_id' => $this->userInfo['MCompany']['id'],
        'TAutoMessage.del_flg' => 0
      )
    ));

    $otherAutoMessages = $this->convertCallAutomessageList($otherAllAutoMessages, $id);
    $this->request->data['otherAutoMessages'] = $otherAutoMessages['data'];
    $this->request->data['disallowActiveChanging'] = $otherAutoMessages['disallowActiveChanging'];

    $this->_viewElement();
  }

  /* *
   * 削除
   * @return void
   * */
  public function remoteDelete()
  {
    Configure::write('debug', 0);
    $this->autoRender = false;
    $this->layout = 'ajax';
    $id = (isset($this->request->data['id'])) ? $this->request->data['id'] : "";
    $ret = $this->TAutoMessage->find('first', [
      'fields' => 'TAutoMessage.*',
      'conditions' => [
        'TAutoMessage.del_flg' => 0,
        'TAutoMessage.id' => $id,
        'TAutoMessage.m_companies_id' => $this->userInfo['MCompany']['id']
      ],
      'recursive' => -1
    ]);
    if (count($ret) === 1) {
      if ($this->TAutoMessage->logicalDelete($id)) {
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
      } else {
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
      }
    }
  }

  public function chkRemoteDelete()
  {
    Configure::write('debug', 0);
    $this->autoRender = false;
    $this->layout = 'ajax';

    $selectedList = $this->request->data['selectedList'];
    $this->TAutoMessage->begin();
    $res = true;
    foreach ($selectedList as $key => $val) {
      $id = (isset($val)) ? $val : "";
      $ret = $this->TAutoMessage->find('first', array(
        'fields' => 'TAutoMessage.*',
        'conditions' => array(
          'TAutoMessage.del_flg' => 0,
          'TAutoMessage.id' => $id,
          'TAutoMessage.m_companies_id' => $this->userInfo['MCompany']['id'],
        ),
        'recursive' => -1
      ));
      if (count($ret) === 1) {
        if (!$this->TAutoMessage->delete($val)) {
          $res = false;
        }
      }
    }
    if ($res) {
      $this->TAutoMessage->commit();
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
    } else {
      $this->TAutoMessage->rollback();
      $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
    }
  }

  /* *
   * コピー処理
   * @return void
   * */
  public function remoteCopyEntryForm()
  {
    Configure::write('debug', 0);
    $this->autoRender = false;
    $this->layout = 'ajax';
    $selectedList = $this->request->data['selectedList'];
    //コピー元のオートメッセージリスト取得
    foreach ($selectedList as $value) {
      $copyData[] = $this->TAutoMessage->read(null, $value);
    }
    $errorMessage = [];
    //コピー元のオートメッセージリストの数だけ繰り返し
    $res = true;
    foreach ($copyData as $value) {
      $this->TAutoMessage->create();
      $saveData = [];
      $params = [
        'fields' => [
          'TAutoMessage.sort'
        ],
        'conditions' => [
          'TAutoMessage.m_companies_id' => $this->userInfo['MCompany']['id']
          //'TAutoMessage.del_flg != ' => 1
        ],
        'order' => [
          'TAutoMessage.sort' => 'desc',
          'TAutoMessage.id' => 'desc'
        ],
        'limit' => 1,
        'recursive' => -1
      ];
      $lastData = $this->TAutoMessage->find('first', $params);
      if ($lastData['TAutoMessage']['sort'] === '0'
        || $lastData['TAutoMessage']['sort'] === 0
        || $lastData['TAutoMessage']['sort'] === null) {
        //ソート順が登録されていなかったらソート順をセットする
        if (!$this->remoteSetSort()) {
          $this->set('alertMessage',
            ['type' => C_MESSAGE_TYPE_ERROR, 'text' => Configure::read('message.const.saveFailed')]);
          return false;
        }
        //もう一度ソートの最大値を取り直す
        $lastData = $this->TAutoMessage->find('first', $params);
      }
      $nextSort = 1;
      if (!empty($lastData)) {
        $nextSort = intval($lastData['TAutoMessage']['sort']) + 1;
      }

      //オートメッセージ　営業時間を4番目に入れたので並び替え処理
      $changeEditData = json_decode($value['TAutoMessage']['activity'], true);
      $changeEditData['conditions'] = array_reverse($changeEditData['conditions'], true);

      foreach ($changeEditData['conditions'] as $key => $val) {
        if ($key >= 4) {
          unset($changeEditData['conditions'][$key]);
          $changeEditData['conditions'][$key + 1] = json_decode($value['TAutoMessage']['activity'],
            true)['conditions'][$key];
        }
      }

      foreach ($changeEditData['conditions'] as $key => $val) {
        if ($key === 11) {
          unset($changeEditData['conditions'][11]);
          $changeEditData['conditions'][4] = json_decode($value['TAutoMessage']['activity'], true)['conditions'][10];
        }
        if ($key === C_AUTO_TRIGGER_STAY_PAGE
          || $key === C_AUTO_TRIGGER_REFERRER
          || $key === C_AUTO_TRIGGER_SPEECH_CONTENT
          || $key === C_AUTO_TRIGGER_STAY_PAGE_OF_FIRST
          || $key === C_AUTO_TRIGGER_STAY_PAGE_OF_PREVIOUS) {
          $changeEditData = $this->convertOldIFData($key, $val, $changeEditData, $key);;
        }
      }

      $mailTransmissionData = $this->MMailTransmissionSetting->findById($value['TAutoMessage']['m_mail_transmission_settings_id']);
      if (!empty($mailTransmissionData)) {
        $this->MMailTransmissionSetting->create();
        $mailTransmissionData['MMailTransmissionSetting']['id'] = null;
        $this->MMailTransmissionSetting->set($mailTransmissionData);
        $this->MMailTransmissionSetting->begin();
        $result = $this->MMailTransmissionSetting->save();
        $value['TAutoMessage']['m_mail_transmission_settings_id'] = $this->MMailTransmissionSetting->getLastInsertId();
      }

      $changeEditData = json_encode($changeEditData);

      $value['TAutoMessage']['activity'] = $changeEditData;

      $saveData['TAutoMessage']['sort'] = $nextSort;
      $saveData['TAutoMessage']['m_companies_id'] = $value['TAutoMessage']['m_companies_id'];
      $saveData['TAutoMessage']['name'] = $value['TAutoMessage']['name'] . 'コピー';
      $saveData['TAutoMessage']['trigger_type'] = $value['TAutoMessage']['trigger_type'];
      $saveData['TAutoMessage']['activity'] = $value['TAutoMessage']['activity'];
      $saveData['TAutoMessage']['action_type'] = $value['TAutoMessage']['action_type'];
      $saveData['TAutoMessage']['active_flg'] = $value['TAutoMessage']['active_flg'];
      $saveData['TAutoMessage']['send_mail_flg'] = $value['TAutoMessage']['send_mail_flg'];
      $saveData['TAutoMessage']['m_mail_transmission_settings_id'] = $value['TAutoMessage']['m_mail_transmission_settings_id'];
      $saveData['TAutoMessage']['m_mail_template_id'] = $value['TAutoMessage']['m_mail_template_id'];
      $saveData['TAutoMessage']['t_chatbot_scenario_id'] = $value['TAutoMessage']['t_chatbot_scenario_id'];
      $saveData['TAutoMessage']['call_automessage_id'] = $value['TAutoMessage']['call_automessage_id'];
      $saveData['TAutoMessage']['t_chatbot_diagram_id'] = $value['TAutoMessage']['t_chatbot_diagram_id'];
      $saveData['TAutoMessage']['del_flg'] = $value['TAutoMessage']['del_flg'];

      $this->TAutoMessage->set($saveData);
      $this->TAutoMessage->begin();

      // action_typeごとに不要なバリデーションルールを削除する
      $this->TAutoMessage->checkBeforeValidates($saveData['TAutoMessage']['action_type']);

      // バリデーションチェックでエラーが出た場合
      if ($res) {
        if (!$this->TAutoMessage->validates()) {
          $res = false;
          $errorMessage = $this->TAutoMessage->validationErrors;
          $this->MMailTransmissionSetting->rollback();
          $this->TAutoMessage->rollback();
        } else {
          //オートメッセージ　営業時間を4番目に入れたので並び替え処理
          $changeEditData = json_decode($saveData['TAutoMessage']['activity'], true);
          foreach ($changeEditData['conditions'] as $key => $val) {
            if ($key == 4) {
              unset($changeEditData['conditions'][4]);
              $changeEditData['conditions'][11] = json_decode($value['TAutoMessage']['activity'],
                true)['conditions'][4];
            }
          }

          foreach ($changeEditData['conditions'] as $key => $val) {
            if ($key >= 4 && $key != 11) {
              unset($changeEditData['conditions'][$key]);
              $changeEditData['conditions'][$key - 1] = json_decode($value['TAutoMessage']['activity'],
                true)['conditions'][$key];
            }
            if ($key == 11) {
              $changeEditData['conditions'][10] = $changeEditData['conditions'][11];
              unset($changeEditData['conditions'][11]);
            }
          }

          $changeEditData = json_encode($changeEditData);
          $saveData['TAutoMessage']['activity'] = $changeEditData;

          if ($this->TAutoMessage->save($saveData, false)) {
            $this->MMailTransmissionSetting->commit();
            $this->TAutoMessage->commit();
            $this->Session->delete('dstoken');
            $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
          }
        }
      }
    }
  }

  /**
   * トリガー設定ソート順更新
   *
   * */
  public function remoteSaveSort()
  {
    Configure::write('debug', 2);
    $this->autoRender = false;
    $this->layout = 'ajax';
    if (!$this->request->is('ajax')) {
      return false;
    }
    if (!empty($this->params->data['list'])) {
      $this->TAutoMessage->begin();
      $list = $this->params->data['list'];
      $sortNoList = $this->params->data['sortNolist'];
      sort($sortNoList);
      $this->log($list, LOG_DEBUG);
      /* 現在の並び順を取得 */
      $params = $this->paginate['TAutoMessage'];
      $params['fields'] = [
        'TAutoMessage.id',
        'TAutoMessage.sort'
      ];
      $params['conditions']['TAutoMessage.m_companies_id'] = $this->userInfo['MCompany']['id'];
      unset($params['limit']);
      $prevSort = $this->TAutoMessage->find('list', $params);
      //新しくソート順を設定したため、空で来ることがある
      $reset_flg = false;
      foreach ($prevSort as $key => $val) {
        //設定されていない値'0'が一つでも入っていたらsortをリセット
        if ($val === '0' || $val === 0 || $val === null) {
          $reset_flg = true;
        }
      }
      if ($reset_flg) {
        //ソート順のリセットはID順とする
//         $i = 1;
//         foreach($prevSort as $key => $val){
//           $prevSort[$key] = strval($i);
//           $i++;
//         }
        //ソート順が登録されていなかったらソート順をセットする
        if (!$this->remoteSetSort()) {
          $this->set('alertMessage',
            ['type' => C_MESSAGE_TYPE_ERROR, 'text' => Configure::read('message.const.saveFailed')]);
          return false;
        }
        $prevSort = $this->TAutoMessage->find('list', $params);
        //この時$sortNoListは空なので作成する
        if (empty($sortNoList)) {
          for ($i = 0; count($list) > $i; $i++) {
            $id = $list[$i];
            $sortNoList[] = $prevSort[$id];
          }
          sort($sortNoList);
        }
      }
//       $prevSortKeys = am($prevSort);
//       $this->log($prevSortKeys,LOG_DEBUG);
      /* アップデート分の並び順を設定 */
      $ret = true;
      for ($i = 0; count($list) > $i; $i++) {
        $id = $list[$i];
        if (isset($prevSort[$id])) {
          $saveData = [
            'TAutoMessage' => [
              'id' => $id,
              'sort' => $sortNoList[$i]
            ]
          ];
          if (!$this->TAutoMessage->validates()) {
            $ret = false;
            break;
          }
          if (!$this->TAutoMessage->save($saveData)) {
            $ret = false;
            break;
          }
        } else {
          // 送信されたトリガー設定と現在DBに存在するトリガー設定に差がある場合
          $this->TAutoMessage->rollback();
          $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.configChanged'));
          return;
        }
      }
      if ($ret) {
        $this->TAutoMessage->commit();
        $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
      } else {
        $this->TAutoMessage->rollback();
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
      }
    }
  }

  /**
   * トリガー設定ソート順を現在のID順でセット
   *
   * */
  public function remoteSetSort()
  {
    $this->TAutoMessage->begin();
    /* 現在の並び順を取得 */
    $this->paginate['TAutoMessage']['conditions']['TAutoMessage.m_companies_id'] = $this->userInfo['MCompany']['id'];
    $params = [
      'fields' => [
        'TAutoMessage.sort'
      ],
      'conditions' => [
        'TAutoMessage.m_companies_id' => $this->userInfo['MCompany']['id']
//            'TAutoMessage.del_flg != ' => 1
      ],
      'order' => [
        'TAutoMessage.sort' => 'asc',
        'TAutoMessage.id' => 'asc'
      ],
      'limit' => 1,
      'recursive' => -1
    ];
    $params['fields'] = [
      'TAutoMessage.id',
      'TAutoMessage.sort'
    ];
    unset($params['limit']);
    $prevSort = $this->TAutoMessage->find('list', $params);
    //ソート順のリセットはID順とする
    $i = 1;
    foreach ($prevSort as $key => $val) {
      $prevSort[$key] = strval($i);
      $i++;
    }
    $prevSortKeys = am($prevSort);
    $this->log($prevSortKeys, LOG_DEBUG);
    $i = 0;
    $ret = true;
    foreach ($prevSort as $key => $val) {
      $id = $key;
      $saveData = [
        'TAutoMessage' => [
          'id' => $id,
          'sort' => $prevSortKeys[$i]
        ]
      ];
      if (!$this->TAutoMessage->validates()) {
        $ret = false;
        break;
      }
      if (!$this->TAutoMessage->save($saveData)) {
        $ret = false;
        break;
      }
      $i++;
    }
    if ($ret) {
      $this->TAutoMessage->commit();
      return true;
    } else {
      $this->TAutoMessage->rollback();
      return false;
    }
  }

  /**
   * ステータス更新
   * @return void
   * */
  public function changeStatus()
  {
    Configure::write('debug', 0);
    $this->autoRender = false;
    $this->layout = 'ajax';
    $inputData = $this->request->data;
    $case = gettype($inputData['status']);
    $activeFlg = 1;
    if ($case === "boolean" && $inputData['status'] || $case === "string" && strcmp($inputData['status'],
        'true') === 0) {
      $activeFlg = 0;
    }
    $this->TAutoMessage->begin();
    foreach($inputData['targetList'] as $index => $targetId) {
      $data = $this->TAutoMessage->find('first', array(
        'fields' => array('id','name','active_flg'),
        'conditions' => array(
          'id' => $targetId
        )
      ));

      $data['TAutoMessage']['active_flg'] = $activeFlg;
      $this->TAutoMessage->set($data);
      if ($this->TAutoMessage->validates()) {
        $this->TAutoMessage->save();
      } else {
        $this->TAutoMessage->rollback();
        $this->renderMessage(C_MESSAGE_TYPE_ERROR, $this->TAutoMessage->validationErrors['name'][0]);
        return;
      }
    }
    $this->TAutoMessage->commit();
  }

  /**
   * 一括インポート
   * @return false|string
   */
  public function bulkImport()
  {
    Configure::write('debug', 0);
    $this->autoRender = false;
    $this->layout = false;
    $result = ['success' => false];
    $file = $this->params['form']['file'];
    $lastPage = $this->request->data['lastPage'];
    $component = new AutoMessageExcelImportComponent($file['tmp_name']);
    try {
      $component->getImportData();
      $transactions = null;
      $data = $component->parseData();
      $transactions = $this->TransactionManager->begin();
      $dataArray = [];
      $errorArray = [];
      $errorFound = false;

      // delete old data
      $this->TAutoMessage->updateAll(['del_flg' => 1],
        [
          'del_flg != ' => 1,
          'm_companies_id' => $this->userInfo['MCompany']['id']
        ]
      );

      foreach ($data as $index => $row) {
        $scenarioId = null;
        $diagramId = null;
        if ($row['scenario']) {
          $scenarioId = $this->getScenarioIdByName($row['scenario']);
          // scenario not exist
          if (!$scenarioId) {
            $errorArray = [];
            $errorArray[$index]['BQ'][0] = "シナリオが存在しません";
            $exception = new AutoMessageException("Excelデータバリデーションエラー", 200);
            $exception->setErrors($errorArray);
            throw $exception;
          }
        }
        if ($row['call_diagram_name']) {
          $diagramId = $this->getDiagramIdByName($row['call_diagram_name']);
          // scenario not exist
          if (!$diagramId) {
            $errorArray = [];
            $errorArray[$index]['BT'][0] = "チャットツリーが存在しません";
            $exception = new AutoMessageException("Excelデータバリデーションエラー", 200);
            $exception->setErrors($errorArray);
            throw $exception;
          }
        }
        $saveData = [
          'TAutoMessage' => [
            'lastPage' => $lastPage,
            'm_companies_id' => $this->userInfo['MCompany']['id'],
            'name' => $row['name'],
            'trigger_type' => 0, // 「画面読み込み時」固定
            'activity' => json_encode($row['activity'], JSON_UNESCAPED_UNICODE), // 日本語はエスケープしないで入れる仕様
            'action_type' => $row['action_type'], // 「チャットメッセージを送る」固定
            'active_flg' => $row['active_flg'],
            't_chatbot_scenario_id' => $scenarioId,
            't_chatbot_diagram_id' => $diagramId,
            'del_flg' => 0
          ]
        ];

        if ($row['send_mail_flg'] === 1) {
          $saveData['main']['send_mail_flg'] = $row['send_mail_flg'];
          $saveData['main']['mail_address_1'] = $row['mail_address_1'];
          $saveData['main']['mail_address_2'] = $row['mail_address_2'];
          $saveData['main']['mail_address_3'] = $row['mail_address_3'];
          $saveData['main']['mail_address_4'] = $row['mail_address_4'];
          $saveData['main']['mail_address_5'] = $row['mail_address_5'];
          $saveData['main']['subject'] = $row['mail_subject'];
          $saveData['main']['from_name'] = $row['mail_from_name'];
        }

        $this->TAutoMessage->set($saveData);
        $this->TAutoMessage->checkBeforeValidates($saveData['TAutoMessage']['action_type'], true);

        $validate = $this->TAutoMessage->validates();
        $errors = $this->TAutoMessage->validationErrors;
        if (!empty($errors)) {
          $errorArray[$index]['E'][0] = isset($errors['activity']) ? $errors['activity'][0] : '';
          $exception = new AutoMessageException("データバリデーションエラー", 200);
          $exception->setErrors($errorArray);
          throw $exception;
        } else {
          array_push($dataArray, $saveData);
        }
      }

      $nextPage = '1';
      $idNameMap = array();
      foreach ($dataArray as $index => $saveData) {
        $saveResult = $this->_entryProcess($saveData, true);
        $idNameMap[$saveResult['name']] = $saveResult['id'];
      }
      foreach ($data as $index => $row) {
        if(!empty($row['call_automessage_name'])) {
          $targetId = $idNameMap[$row['name']];
          $callAutomessageId = $idNameMap[$row['call_automessage_name']];
          $this->TAutoMessage->read(null, $targetId);
          $this->TAutoMessage->set(array(
            'call_automessage_id' => $callAutomessageId
          ));
          $this->TAutoMessage->save();
        }
      }
      $this->TransactionManager->commitTransaction($transactions);
      $result['success'] = true;
      $result['showPageNum'] = $nextPage;
    } catch (AutoMessageException $e) {
      if ($transactions) {
        $this->TransactionManager->rollbackTransaction($transactions);
      }
      $result['success'] = false;
      $result['errorCode'] = 400;
      $result['errorMessages'] = $e->getErrors();
    } catch (Exception $e) {
      if ($transactions) {
        $this->TransactionManager->rollbackTransaction($transactions);
      }
      $result['success'] = false;
      $result['errorCode'] = 400;
      $this->log("Excel import error found. message => " . $e->getMessage, LOG_WARNING);
      $result['errorMessages'] = [
        'type' => 'system',
        'message' => 'ファイルの読み込みに失敗しました。'
      ];
    }

    return json_encode($result);
  }

  /**
   *一括エクスポート
   */
  public function bulkExport()
  {
    Configure::write('debug', 0);
    $this->autoRender = false;
    $this->layout = false;
    $filePath = ROOT . DS . self::TEMPLATE_FILE_NAME;
    $component = new AutoMessageExcelExportComponent($filePath);
    $component->getImportData();
    $params = [
      'order' => [
        'TAutoMessage.sort' => 'asc',
        'TAutoMessage.id' => 'asc'
      ],
      'fields' => ['TAutoMessage.*', 'TChatbotScenario.id', 'TChatbotScenario.name', 'CalledAutoMessage.name', 'TChatbotDiagram.name'],
      'conditions' => [
        'TAutoMessage.m_companies_id' => $this->userInfo['MCompany']['id'],
        'TAutoMessage.del_flg != ' => 1
      ],
      'joins' => [
        [
          'type' => 'LEFT',
          'table' => 't_chatbot_scenarios',
          'alias' => 'TChatbotScenario',
          'conditions' => [
            'TAutoMessage.t_chatbot_scenario_id = TChatbotScenario.id'
          ]
        ],
        [
          'type' => 'LEFT',
          'table' => 't_auto_messages',
          'alias' => 'CalledAutoMessage',
          'conditions' => [
            'TAutoMessage.call_automessage_id = CalledAutoMessage.id'
          ]
        ],
        [
          'type' => 'LEFT',
          'table' => 't_chatbot_diagrams',
          'alias' => 'TChatbotDiagram',
          'conditions' => [
            'TAutoMessage.t_chatbot_diagram_id = TChatbotDiagram.id'
          ]
        ]
      ],
      'recursive' => -1
    ];
    $data = $this->TAutoMessage->find('all', $params);
    foreach ($data as $index => $value) {
      $activity = json_decode($value['TAutoMessage']['activity'], true);
      foreach ($activity['conditions'] as $key => $val) {
        $targetKey = $key;
        if ($targetKey >= 4) {
          $targetKey = $targetKey + 1;
        } else {
          if ($targetKey === 10) {
            $targetKey = 4;
          }
        }
        $activity = $this->convertOldIFData($targetKey, $val, $activity, $key);
      }
      $data[$index]['TAutoMessage']['activity'] = json_encode($activity);
    }

    return $component->export($data);
  }

  public function downloadTemplate()
  {
    $this->autoRender = false;
    $filePath = ROOT . DS . self::FULL_TEMPLATE_FILE_NAME;
    $this->response->download(self::FULL_TEMPLATE_FILE_NAME);
    $this->response->file($filePath);
  }

  /**
   * 保存機能
   * @param array $inputData
   * @return void
   * */
  private function _entry($saveData)
  {
    $nextPage = '1';
    $transactions = null;
    try {
      $transactions = $this->TransactionManager->begin();
      $nextPage = $this->_entryProcess($saveData)['page'];
      $this->TransactionManager->commitTransaction($transactions);
      $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
      $this->redirect('/TAutoMessages/index/page:' . $nextPage, null, false);
    } catch (AutoMessageException $e) {
      $this->TransactionManager->rollbackTransaction($transactions);
      $this->set('alertMessage',
        ['type' => C_MESSAGE_TYPE_ERROR, 'text' => Configure::read('message.const.saveFailed')]);
      $this->set('errors', $e->getErrors());
      $this->set('lastPage', $e->getLastPage());
    } catch (Exception $e) {
      $this->TransactionManager->rollbackTransaction($transactions);
      $this->set('alertMessage',
        ['type' => C_MESSAGE_TYPE_ERROR, 'text' => Configure::read('message.const.saveFailed')]);
    }
  }

  /**
   * 保存機能
   * トランザクションはこのメソッドの呼び出し元で管理している。（TransactionManager）
   * @param array $inputData
   * @return {String}
   * */
  private function _entryProcess($saveData, $bulkInsertMode = false)
  {
    $errors = [];
    $saveData['TAutoMessage']['m_companies_id'] = $this->userInfo['MCompany']['id'];
    if (array_key_exists('lastPage', $saveData)) {
      $nextPage = $saveData['lastPage'];
    } else {
      $nextPage = '1';
    }

    if (empty($saveData['TAutoMessage']['id'])) {
      //新規追加
      $this->TAutoMessage->create();
      $params = [
        'fields' => [
          'TAutoMessage.sort'
        ],
        'conditions' => [
          'TAutoMessage.m_companies_id' => $this->userInfo['MCompany']['id']
//              'TAutoMessage.del_flg != ' => 1
        ],
        'order' => [
          'TAutoMessage.sort' => 'desc',
          'TAutoMessage.id' => 'desc'
        ],
        'limit' => 1,
        'recursive' => -1
      ];
      $lastData = $this->TAutoMessage->find('first', $params);
      if ($lastData) {
        if ($lastData['TAutoMessage']['sort'] === '0'
          || $lastData['TAutoMessage']['sort'] === 0
          || $lastData['TAutoMessage']['sort'] === null) {
          //ソート順が登録されていなかったらソート順をセットする
          if (!$this->remoteSetSort()) {
            $this->set('alertMessage',
              ['type' => C_MESSAGE_TYPE_ERROR, 'text' => Configure::read('message.const.saveFailed')]);
            throw new AutoMessageException('ソート順が設定できませんでした。');
          }
          //もう一度ソートの最大値を取り直す
          $lastData = $this->TAutoMessage->find('first', $params);
        }
      }
      $nextSort = 1;
      if (!empty($lastData)) {
        $nextSort = intval($lastData['TAutoMessage']['sort']) + 1;
      }
      $saveData['TAutoMessage']['sort'] = $nextSort;

      $count = $this->TAutoMessage->find('first', [
        'fields' => ['count(*) as count'],
        'conditions' => ['TAutoMessage.del_flg != ' => 1, 'm_companies_id' => $this->userInfo['MCompany']['id']]
      ]);
      $nextPage = floor((intval($count[0]['count']) + 99) / 100);
    }

    // メール送信設定の値を抜く
    $toAddresses = '';
    $subject = '';
    $fromName = '';
    $templateId = 0;
    if ($saveData['TAutoMessage']['action_type'] == C_AUTO_ACTION_TYPE_SENDMESSAGE && !empty($saveData['main']['send_mail_flg']) && intval($saveData['main']['send_mail_flg']) === C_CHECK_ON) {
      $this->request->data['TAutoMessage']['send_mail_flg'] = intval($saveData['main']['send_mail_flg']);
      $saveData['TAutoMessage']['send_mail_flg'] = intval($saveData['main']['send_mail_flg']);
      foreach ($saveData['main'] as $k => $v) {
        if (preg_match('/mail_address_[1-5]/', $k)) {
          $this->request->data['TAutoMessage'][$k] = $v;
          if (!empty($v)) {
            if ($toAddresses !== '') {
              $toAddresses .= ',';
            }
            $toAddresses .= $v;
          }
        }
        if (strpos($k, 'subject') === 0) {
          $this->request->data['TAutoMessage']['subject'] = $v;
          $subject = $v;
        }
        if (strpos($k, 'from_name') === 0) {
          $this->request->data['TAutoMessage']['from_name'] = $v;
          $fromName = $v;
        }
      }
      if (empty($saveData['TAutoMessage']['m_mail_transmission_settings_id'])) {
        $this->MMailTransmissionSetting->create();
      } else {
        $this->MMailTransmissionSetting->read(null, $saveData['TAutoMessage']['m_mail_transmission_settings_id']);
      }
      $this->MMailTransmissionSetting->set([
        'm_companies_id' => $this->userInfo['MCompany']['id'],
        'from_name' => $fromName,
        'to_address' => $toAddresses,
        'subject' => $subject
      ]);
      $validate = $this->MMailTransmissionSetting->validates();
      $errors = $this->MMailTransmissionSetting->validationErrors;
      if (empty($errors)) {
        $this->MMailTransmissionSetting->save();
        if (empty($saveData['TAutoMessage']['m_mail_transmission_settings_id'])) {
          $saveData['TAutoMessage']['m_mail_transmission_settings_id'] = $this->MMailTransmissionSetting->getLastInsertId();
        }
        if (empty($saveData['TAutoMessage']['m_mail_template_id'])) {
          $templateData = $this->MMailTemplate->find('first', [
            'conditions' => [
              'm_companies_id' => $this->userInfo['MCompany']['id'],
              'mail_type_cd' => 'AM001'
            ]
          ]);
          if (!empty($templateData)) {
            $saveData['TAutoMessage']['m_mail_template_id'] = $templateData['MMailTemplate']['id'];
          }
        }
      } else {
        $exception = new AutoMessageException('バリデーションエラー');
        $exception->setErrors($errors);
        $exception->setLastPage($nextPage);
        throw $exception;
      }
    } else {
      $saveData['main']['send_mail_flg'] = 0;
      $saveData['main']['m_mail_transmission_settings_id'] = 0;
      $saveData['main']['m_mail_template_id'] = 0;
      $saveData['TAutoMessage']['send_mail_flg'] = 0;
    }

    if (strcmp($this->request->data['TAutoMessage']['action_type'], "1") === 0) {
      $saveData['TAutoMessage']['t_chatbot_scenario_id'] = null;
    }

    $this->TAutoMessage->set($saveData);

    // action_typeごとに不要なバリデーションルールを削除する
    $this->TAutoMessage->checkBeforeValidates($saveData['TAutoMessage']['action_type'], $bulkInsertMode);

    $validate = $this->TAutoMessage->validates();
    $errors = $this->TAutoMessage->validationErrors;

    // その他のチェック
    if (!empty($saveData['TAutoMessage'])) {
      $activity = json_decode($saveData['TAutoMessage']['activity']);

      /* 項目ごとの設定数上限チェック */
      $tmpMessage = "%sの場合、『%s』は%d個まで設定可能です";

      foreach ((array)$activity->conditions as $key => $val) {
        $setting = $this->outMessageTriggerList[$key];
        if (!isset($setting['createLimit'][$activity->conditionType])) {
          continue;
        }
        if (count($val) > intval($setting['createLimit'][$activity->conditionType])) {
          $validate = false;
          $errors['triggers'][$setting['key']] = sprintf($tmpMessage, $this->outMessageIfType[$activity->conditionType],
            $setting['label'], $setting['createLimit'][$activity->conditionType]);
        }
      }
    }

    if ($validate) {
      //オートメッセージ　営業時間を4番目に入れたので並び替え処理
      $changeEditData = json_decode($saveData['TAutoMessage']['activity'], true);
      foreach ($changeEditData['conditions'] as $key => $val) {
        if ($key === 4) {
          unset($changeEditData['conditions'][4]);
          $changeEditData['conditions'][10] = json_decode($saveData['TAutoMessage']['activity'], true)['conditions'][4];
        }

        if ($key >= 5 && $key < 11) {
          unset($changeEditData['conditions'][$key]);
          $changeEditData['conditions'][$key - 1] = json_decode($saveData['TAutoMessage']['activity'],
            true)['conditions'][$key];
        }
      }
      $changeEditData = json_encode($changeEditData);
      $saveData['TAutoMessage']['activity'] = $changeEditData;
      if ($this->TAutoMessage->save($saveData, false)) {
      }
    } else {
      $exception = new AutoMessageException('バリデーションエラー');
      $exception->setErrors($errors);
      $exception->setLastPage($nextPage);
      throw $exception;
    }

    $page = $nextPage;

    return array(
      'page' => $page >= 1 ? $page : 1,
      'id' => $this->TAutoMessage->getLastInsertID(),
      'name' => $saveData['TAutoMessage']['name']
    );
  }

  /**
   * ビュー部品セット
   * @return void
   * */
  private function _viewElement()
  {
    // TODO out -> auto に変更
    // トリガー種別
    $this->set('outMessageTriggerType', Configure::read('outMessageTriggerType'));
    // 条件設定種別
    $this->set('outMessageIfType', $this->outMessageIfType);
    // 条件リスト
    $this->set('outMessageTriggerList', $this->outMessageTriggerList);
    // アクション種別
    if($this->coreSettings[C_COMPANY_USE_CHATBOT_TREE_EDITOR]) {
      $this->set('outMessageActionType', Configure::read('outMessageActionTypePrioritizeDiagram'));
    } else if($this->coreSettings[C_COMPANY_USE_CHATBOT_SCENARIO]) {
      $this->set('outMessageActionType', Configure::read('outMessageActionTypePrioritizeScenario'));
    } else {
      $this->set('outMessageActionType', Configure::read('outMessageActionType'));
    }
    // ウィジェット種別
    $this->set('outMessageWidgetOpenType', Configure::read('outMessageWidgetOpenType'));
    // テキストエリア
    $this->set('outMessageTextarea', Configure::read('outMessageTextarea'));
    //cv
    $this->set('outMessageCvType', Configure::read('outMessageCvType'));
    // 有効無効
    $this->set('outMessageAvailableType', Configure::read('outMessageAvailableType'));
    // 画像パス
    $this->set('gallaryPath', C_NODE_SERVER_ADDR . C_NODE_SERVER_FILE_PORT . '/img/widget/');

    $this->set('companyKey', $this->userInfo['MCompany']['company_key']);
    // 最後に表示していたページ番号
    if (!empty($this->request->query['lastpage'])) {
      $this->set('lastPage', $this->request->query['lastpage']);
    }
  }

  /**
   * @param $type
   * @param $conditions
   * @param $activity
   * @return mixed
   */
  private function convertOldIFData($type, $conditions, $activity, $actualType)
  {
    // ページ、参照元URL、発言内容、最初に訪れたページ、前のページの旧IF対応
    if ($type === C_AUTO_TRIGGER_STAY_PAGE) {
      $arr = array();
      foreach ($conditions as $index => $settings) {
        if (array_key_exists('keyword', $settings)) {
          $newSettings = array(
            "targetName" => "1",
            "keyword_contains" => "",
            "keyword_contains_type" => "1",
            "keyword_exclusions" => "",
            "keyword_exclusions_type" => "1",
            "stayPageCond" => 1
          );
          $newSettings["targetName"] = $settings['targetName'];
          switch ($settings['stayPageCond']) {
            case 1: // 完全一致
              $newSettings["keyword_contains"] = $settings['keyword'];
              $newSettings["stayPageCond"] = 1;
              break;
            case 2: // 部分一致
              $newSettings["keyword_contains"] = $settings['keyword'];
              $newSettings["stayPageCond"] = 2;
              break;
            case 3: // 不一致
              $newSettings["keyword_exclusions"] = $settings['keyword'];
              $newSettings["stayPageCond"] = 1;
              break;
          }
          array_push($arr, $newSettings);
        } else {
          array_push($arr, $settings);
        }
      }
      $activity['conditions'][$actualType] = $arr;
    }
    if ($type === C_AUTO_TRIGGER_VISIT_CNT) {
      $arr = array();
      foreach ($conditions as $index => $settings) {
        $settings['visitCnt'] = (int)$settings['visitCnt'];
        array_push($arr, $settings);
      }
      $activity['conditions'][$actualType] = $arr;
    }
    if ($type === C_AUTO_TRIGGER_REFERRER) {
      $arr = array();
      foreach ($conditions as $index => $settings) {
        if (array_key_exists('keyword', $settings)) {
          $newSettings = array(
            "keyword_contains" => "",
            "keyword_contains_type" => "1",
            "keyword_exclusions" => "",
            "keyword_exclusions_type" => "1",
            "referrerCond" => 2
          );
          switch ($settings['referrerCond']) {
            case 1: // 完全一致
              $newSettings["keyword_contains"] = $settings['keyword'];
              $newSettings["referrerCond"] = 1;
              break;
            case 2: // 部分一致
              $newSettings["keyword_contains"] = $settings['keyword'];
              $newSettings["referrerCond"] = 2;
              break;
            case 3: // 不一致
              $newSettings["keyword_exclusions"] = $settings['keyword'];
              $newSettings["referrerCond"] = 1;
              break;
          }
          array_push($arr, $newSettings);
        } else {
          array_push($arr, $settings);
        }
      }
      $activity['conditions'][$actualType] = $arr;
    }
    if ($type === C_AUTO_TRIGGER_SPEECH_CONTENT) {
      $arr = array();
      foreach ($conditions as $index => $settings) {
        if (array_key_exists('speechContent', $settings)) {
          $newSettings = array(
            "keyword_contains" => "",
            "keyword_contains_type" => "1",
            "keyword_exclusions" => "",
            "keyword_exclusions_type" => "1",
            "speechContentCond" => "1",
            "triggerTimeSec" => 3,
            "speechTriggerCond" => "1"
          );
          $newSettings['speechContentCond'] = $settings['speechContentCond'];
          $newSettings['triggerTimeSec'] = $settings['triggerTimeSec'];
          $newSettings['speechTriggerCond'] = $settings['speechTriggerCond'];
          switch ($settings['speechContentCond']) {
            case 1: // 完全一致
              $newSettings["keyword_contains"] = $settings['speechContent'];
              $newSettings["speechContentCond"] = 1;
              break;
            case 2: // 部分一致
              $newSettings["keyword_contains"] = $settings['speechContent'];
              $newSettings["speechContentCond"] = 2;
              break;
            case 3: // 不一致
              $newSettings["keyword_exclusions"] = $settings['speechContent'];
              $newSettings["speechContentCond"] = 1;
              break;
          }
          array_push($arr, $newSettings);
        } else {
          array_push($arr, $settings);
        }
      }
      $activity['conditions'][$actualType] = $arr;
    }
    if ($type === C_AUTO_TRIGGER_STAY_PAGE_OF_FIRST) {
      $arr = array();
      foreach ($conditions as $index => $settings) {
        if (array_key_exists('keyword', $settings)) {
          $newSettings = array(
            "targetName" => "1",
            "keyword_contains" => "",
            "keyword_contains_type" => "1",
            "keyword_exclusions" => "",
            "keyword_exclusions_type" => "1",
            "stayPageCond" => 1
          );
          $newSettings["targetName"] = $settings['targetName'];
          switch ($settings['stayPageCond']) {
            case 1: // 完全一致
              $newSettings["keyword_contains"] = $settings['keyword'];
              $newSettings["stayPageCond"] = 1;
              break;
            case 2: // 部分一致
              $newSettings["keyword_contains"] = $settings['keyword'];
              $newSettings["stayPageCond"] = 2;
              break;
            case 3: // 不一致
              $newSettings["keyword_exclusions"] = $settings['keyword'];
              $newSettings["stayPageCond"] = 1;
              break;
          }
          array_push($arr, $newSettings);
        } else {
          array_push($arr, $settings);
        }
      }
      $activity['conditions'][$actualType] = $arr;
    }
    if ($type === C_AUTO_TRIGGER_STAY_PAGE_OF_PREVIOUS) {
      $arr = array();
      foreach ($conditions as $index => $settings) {
        if (array_key_exists('keyword', $settings)) {
          $newSettings = array(
            "targetName" => "1",
            "keyword_contains" => "",
            "keyword_contains_type" => "1",
            "keyword_exclusions" => "",
            "keyword_exclusions_type" => "1",
            "stayPageCond" => 1
          );
          $newSettings["targetName"] = $settings['targetName'];
          switch ($settings['stayPageCond']) {
            case 1: // 完全一致
              $newSettings["keyword_contains"] = $settings['keyword'];
              $newSettings["stayPageCond"] = 1;
              break;
            case 2: // 部分一致
              $newSettings["keyword_contains"] = $settings['keyword'];
              $newSettings["stayPageCond"] = 2;
              break;
            case 3: // 不一致
              $newSettings["keyword_exclusions"] = $settings['keyword'];
              $newSettings["stayPageCond"] = 1;
              break;
          }
          array_push($arr, $newSettings);
        } else {
          array_push($arr, $settings);
        }
      }
      $activity['conditions'][$actualType] = $arr;
    }
    return $activity;
  }

  /**
   * @param $name
   * @return mixed
   */
  private function getScenarioIdByName($name)
  {
    $data = $this->TChatbotScenario->find('first', [
      'fields' => ['id'],
      'conditions' => [
        'name' => $name,
        'del_flg != ' => 1,
        'm_companies_id' => $this->userInfo['MCompany']['id']
      ]
    ]);

    return $data['TChatbotScenario']['id'];
  }

  /**
   * @param $name
   * @return mixed
   */
  private function getDiagramIdByName($name)
  {
    $data = $this->TChatbotDiagram->find('first', [
      'fields' => ['id'],
      'conditions' => [
        'name' => $name,
        'del_flg != ' => 1,
        'm_companies_id' => $this->userInfo['MCompany']['id']
      ]
    ]);

    return $data['TChatbotDiagram']['id'];
  }

  private function convertCallAutomessageList($array, $editTargetId)
  {
    $resultArray = array();
    $disallowActiveChanging = false;
    foreach ($array as $index => $data) {
      if (strcmp($data['TAutoMessage']['active_flg'], 1) === 0) {
        continue;
      }

      if ($editTargetId && strcmp($data['TAutoMessage']['call_automessage_id'], $editTargetId) === 0) {
        $disallowActiveChanging = true;
      }

      if ($editTargetId && strcmp($data['TAutoMessage']['id'],
          $editTargetId) !== 0 && strcmp($data['TAutoMessage']['call_automessage_id'], $editTargetId) !== 0) {
        // 編集時に必要なデータ
        $resultArray[$data['TAutoMessage']['id']] = 'No.' . ($index + 1) . '：' . $data['TAutoMessage']['name'];
      } else {
        if (!$editTargetId) {
          $resultArray[$data['TAutoMessage']['id']] = 'No.' . ($index + 1) . '：' . $data['TAutoMessage']['name'];
        }
      }
    }

    return array(
      'data' => $resultArray,
      'disallowActiveChanging' => $disallowActiveChanging
    );
  }
}
