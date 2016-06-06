
<?php
/**
 * HistoriesController controller.
 * 履歴一覧画面
 */
class HistoriesController extends AppController {
    public $uses = ['THistory', 'THistoryChatLog', 'THistoryStayLog', 'THistoryShareDisplay'];
    public $paginate = [
            'THistory' => [
                'limit' => 100,
                'order' => [
                    'THistory.created' => 'desc'
                ],
                'fields' => [
                    'THistory.*',
                    'THistoryChatLog.*',
                    'THistoryStayLog.*'
                ],
                'joins' => [
                    [
                        'type' => 'INNER',
                        'table' => '(SELECT t_histories_id, COUNT(t_histories_id) AS count FROM t_history_chat_logs GROUP BY t_histories_id)',
                        'alias' => 'THistoryChatLog',
                        'conditions' => [
                            'THistoryChatLog.t_histories_id = THistory.id'
                        ]
                    ],
                    [
                        'type' => 'LEFT',
                        'table' => '(SELECT t_histories_id, COUNT(t_histories_id) AS count FROM t_history_stay_logs WHERE del_flg != 1 GROUP BY t_histories_id)',
                        'alias' => 'THistoryStayLog',
                        'conditions' => [
                            'THistoryStayLog.t_histories_id = THistory.id'
                        ]
                    ]
                ],
                'conditions' => [
                    'THistory.del_flg !=' => 1
                ]
        ],
        'THistoryStayLog' => []
    ];

    public function beforeFilter(){
        parent::beforeFilter();
        $this->paginate['THistory']['conditions'] = [
            'THistory.del_flg !=' => 1,
            'THistory.m_companies_id' => $this->userInfo['MCompany']['id']
        ];
        $this->set('siteKey', $this->userInfo['MCompany']['company_key']);
        $this->set('title_for_layout', '履歴');
    }

    /* *
	     * 一覧画面
     * @return void
     * */
    public function index() {
        $isChat = 'true';
        if ( !empty($this->params->query['isChat']) ) {
            $isChat = $this->params->query['isChat'];
        }
        $this->_setList($isChat);
    }

    public function remoteGetChatLogs() {
        Configure::write('debug', 0);
        $this->autoRender = FALSE;
        $this->layout = null;

        $historyId = $this->params->query['historyId'];

        $params = [
            'fields' => '*',
            'conditions' => [
                'THistoryChatLog.t_histories_id' => $historyId
            ],
            'order' => 'created',
            'recursive' => -1
        ];
        $ret = $this->THistoryChatLog->find('all', $params);
        $this->set('THistoryChatLog', $ret);
        return $this->render('/Elements/Histories/remoteGetChatLogs');
    }

    public function remoteGetStayLogs() {
        Configure::write('debug', 0);
        $this->autoRender = FALSE;
        $this->layout = null;

        $historyId = $this->params->query['historyId'];

        $params = [
            'fields' => '*',
            'conditions' => [
                'THistoryStayLog.t_histories_id' => $historyId,
                'THistoryStayLog.del_flg !=' => 1
            ],
            'recursive' => -1
        ];
        $ret = $this->THistoryStayLog->find('all', $params);
        $this->set('THistoryStayLog', $ret);
        return $this->render('/Elements/Histories/remoteGetStayLogs');
    }

    private function _setList($type=true){
        if ( strcmp($type, 'false') === 0 ) {
            $this->paginate['THistory']['joins'][0]['type'] = "LEFT";
        }
        else {
            $this->paginate['THistory']['joins'][0]['type'] = "INNER";
        }

        $this->Session->write("histories.joins", $this->paginate['THistory']['joins'][0]['type']);
        $historyList = $this->paginate('THistory');
        $this->set('historyList', $historyList);
        $this->set('groupByChatChecked', $type);
    }

}
