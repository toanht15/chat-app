<?php
/**
 * TAutoMessagesController controller.
 * ユーザーマスタ
 */
class TAutoMessagesController extends AppController {
    public $uses = array('TAutoMessage');
    public $paginate = array(
        'TAutoMessage' => array(
            'limit' => 10,
            'order' => array(
                'TAutoMessage.id' => 'asc'
            ),
            'fields' => array(
                'TAutoMessage.*'
            ),
            'conditions' => array(
                'TAutoMessage.del_flg != ' => 1
            ),
            'recursive' => -1
        )
    );

    public function beforeFilter(){
        parent::beforeFilter();
        $this->set('title_for_layout', 'オートメッセージ機能');
        // $this->set('siteKey', $this->userInfo['MCompany']['company_key']);
        // $this->set('limitUserNum', $this->userInfo['MCompany']['limit_users']);
    }

    /**
     * 一覧画面
     * @return void
     * */
    public function index() {
        $this->paginate['TAutoMessage']['conditions']['TAutoMessage.m_companies_id'] = $this->userInfo['MCompany']['id'];
        $this->set('settingList', $this->paginate('TAutoMessage'));
    }

    /**
     * 登録画面
     * @return void
     * */
    public function add() {
        $errors = [];
        if ( $this->request->is('post') ) {
            $this->_entry($this->request->data);
        }
        $this->_viewElement();
    }

     /**
     * 更新画面
     * @return void
     * */
    public function edit($id=null) {
        $errors = [];

        if ($this->request->is('put')) {
            $this->_entry($this->request->data);
        }
        else {
            // 確実なデータを取得するために企業IDを指定する形とする
            $editData = $this->TAutoMessage->coFind("all", [
                'conditions' => [
                    'TAutoMessage.id' => $id
                ]
            ]);
            if (empty($editData) || (!empty($editData) && empty($editData[0]))) {
                $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.notFoundId'));
            	$this->redirect('/TAutoMessages/index');
            }
            $json = json_decode($editData[0]['TAutoMessage']['activity'], true);
            $this->request->data = $editData[0];
            $this->request->data['TAutoMessage']['condition_type'] = (!empty($json['conditionType'])) ? $json['conditionType'] : "";
            $this->request->data['TAutoMessage']['action'] = (!empty($json['message'])) ? $json['message'] : "";
        }


        $this->_viewElement();
    }

    /**
     * ステータス更新
     * @return void
     * */
    public function changeStatus() {
        Configure::write('debug', 0);
        $this->autoRender = FALSE;
        $this->layout = 'ajax';
        $inputData = $this->request->query;
        $case = gettype($inputData['status']);
        $activeFlg = 1;
        if ($case === "boolean" && $inputData['status'] || $case === "string" && strcmp($inputData['status'], 'true') === 0) {
            $activeFlg = 0;
        }
        $this->TAutoMessage->updateAll(
            ['active_flg'=>$activeFlg],
            [
                'id' => $inputData['targetList'],
                'm_companies_id' => $this->userInfo['MCompany']['id'],
                'del_flg' => 0
            ]
        );
    }

    /**
     * 保存機能
     * @param array $inputData
     * @return void
     * */
    private function _entry($inputData) {

        $inputData['TAutoMessage']['m_companies_id'] = $this->userInfo['MCompany']['id'];

        $this->TAutoMessage->begin();
        if ( empty($inputData['TAutoMessage']['id']) ) {
            $this->TAutoMessage->create();
        }
        $this->TAutoMessage->set($inputData);

        if ($this->TAutoMessage->save()) {
            $this->TAutoMessage->commit();
            $this->set('alertMessage',['type' => C_MESSAGE_TYPE_SUCCESS, 'text'=>Configure::read('message.const.saveSuccessful')]);
        }
        else {
            $this->TAutoMessage->rollback();
            $errors = $this->TAutoMessage->validationErrors;
            $this->set('alertMessage',['type' => C_MESSAGE_TYPE_ERROR, 'text'=>Configure::read('message.const.saveFailed')]);
        }
        $this->set('errors', $errors);
    }

    /**
     * ビュー部品セット
     * @return void
     * */
    private function _viewElement() {
        // トリガー種別
        $this->set('outMessageTriggerType', Configure::read('outMessageTriggerType'));
        // 条件設定種別
        $this->set('outMessageIfType', Configure::read('outMessageIfType'));
        // 条件リスト
        $this->set('outMessageTriggerList', Configure::read('outMessageTriggerList'));
        // アクション種別
        $this->set('outMessageActionType', Configure::read('outMessageActionType'));
        // 有効無効
        $this->set('outMessageAvailableType', Configure::read('outMessageAvailableType'));
    }



}
