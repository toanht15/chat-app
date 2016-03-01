<?php
/**
 * CustomersController controller.
 * モニタリング機能
 */
class CustomersController extends AppController {
    public $uses = array('THistory');

    public function beforeRender(){
        $this->set('siteKey', $this->userInfo['MCompany']['company_key']);
        $this->set('muserId', $this->userInfo['id']);
        $this->set('title_for_layout', 'リアルタイムモニタ');
    }

    /* *
     * 一覧画面
     * @return void
     * */
    public function index() {
    }

    /* *
     * モニタリング画面
     * @return void
     * */
    public function frame() {
        $this->layout = 'frame';
        Configure::write('debug', 0);
    }

    /* *
     * モニタリング画面
     * @return void
     * */
    public function remoteCreateSetting() {
        Configure::write('debug', 0);
        $this->autoRender = FALSE;
        $this->layout = null;
        $labelList = array(
                'accessId' => 'アクセスID',
                'ipAddress' => '訪問ユーザ',
                'ua' => 'ユーザー環境',
                'time' => 'アクセス日時',
                'stayTime' => '滞在時間',
                'page' => '閲覧ページ数',
                'title' => '閲覧中ページ',
                'referrer' => '参照元URL'
            );
        $labelHideList = [];
        $selectedLabelList = [];
        $dataList = [];
        if ( isset($this->request->query['labelHideList']) ) {
            $dataList = (array)json_decode($this->request->query['labelHideList']);
        }
        foreach ( $dataList as $key => $val ) {
            $labelHideList[$key] = $labelList[$key];
            if ( $val ) {
                $selectedLabelList[] = $key;
            }
        }

        $this->set('labelHideList', $labelHideList);
        $this->set('selectedLabelList', $selectedLabelList);
        return $this->render('/Customers/remoteCreateSetting');
    }

    public function remoteSaveSetting() {
        Configure::write('debug', 0);
        $this->autoRender = FALSE;
        $this->layout = null;
        // データーベースへ保存
    }

    public function remoteGetStayLogs() {
        Configure::write('debug', 0);
        $this->autoRender = FALSE;
        $this->layout = null;

        $ret = array();
        if ( isset($this->params->query['TVisitorsId']) && isset($this->params->query['tabId']) ) {
            $params = array(
                'fields' => '*',
                'conditions' => array(
                    'THistory.del_flg !=' => 1,
                    'THistory.m_companies_id' => $this->userInfo['MCompany']['id'],
                    'THistory.t_visitors_id' => $this->params->query['TVisitorsId'],
                    'THistory.tab_id' => $this->params->query['tabId']
                ),
                'joins' => array(
                    array(
                        'type' => 'LEFT',
                        'table' => 't_history_stay_logs',
                        'alias' => 'THistoryStayLog',
                        'conditions' => array(
                            'THistoryStayLog.t_histories_id = THistory.id',
                            'THistoryStayLog.del_flg !=' => 1
                        )
                    )
                ),
                'sort' => array(
                    'THistory.id' => 'DESC'
                ),
                'recursive' => -1
            );
            $ret = $this->THistory->find('all', $params);
        }

        $this->set('THistoryStayLog', $ret);
        return $this->render('/Histories/remoteGetStayLogs');
    }
}
