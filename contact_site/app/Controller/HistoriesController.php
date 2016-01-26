<?php
/**
 * HistoriesController controller.
 * 履歴一覧画面
 */
class HistoriesController extends AppController {
    public $uses = array('THistory', 'THistoryStayLog', 'THistoryShareDisplay');
    public $paginate = array(
        'THistory' => array(
            'limit' => 10,
            'order' => array(
                'THistory.created' => 'desc'
            ),
            'fields' => array(
                'THistory.*',
                'THistoryStayLog.*'
            ),
            'joins' => array(
                array(
                    'type' => 'LEFT',
                    'table' => '(SELECT t_histories_id, COUNT(t_histories_id) AS count FROM t_history_stay_logs WHERE del_flg != 1 GROUP BY t_histories_id)',
                    'alias' => 'THistoryStayLog',
                    'conditions' => array(
                        'THistoryStayLog.t_histories_id = THistory.id'
                    )
                )
            ),
            'conditions' => array(
                'THistory.del_flg !=' => 1
            )
        ),
        'THistoryStayLog' => array()
    );

    public function beforeFilter(){
        $this->set('siteKey', $this->userInfo['MCompany']['company_key']);
        $this->set('title_for_layout', '履歴');
    }

    /* *
     * 一覧画面
     * @return void
     * */
    public function index() {
        $historyList = $this->paginate('THistory');
        $this->set('historyList', $historyList);
    }

    public function remoteGetStayLogs() {
        Configure::write('debug', 0);
        $this->autoRender = FALSE;
        $this->layout = null;

        $historyId = $this->params->query['historyId'];

        $params = array(
            'fields' => '*',
            'conditions' => array(
                'THistoryStayLog.t_histories_id' => $historyId,
                'THistoryStayLog.del_flg !=' => 1
            ),
            'recursive' => -1
        );
        $ret = $this->THistoryStayLog->find('all', $params);
        $this->set('THistoryStayLog', $ret);
        return $this->render('/Histories/remoteGetStayLogs');
    }

}
