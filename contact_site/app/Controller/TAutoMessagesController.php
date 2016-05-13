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
        if ( $this->request->is('post') ) {

        }
        // $inputData = [
        //     'TAutoMessage' => [
        //         'trigger' => C_AUTO_TRIGGER_TYPE_BODYLOAD,
        //         'action' => C_AUTO_ACTION_TYPE_SENDMESSAGE
        //     ]
        // ];
        // $this->data = $inputData;
        $this->_viewElement();
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
