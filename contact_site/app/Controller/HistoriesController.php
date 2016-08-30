<?php
/**
 * HistoriesController controller.
 * 履歴一覧画面
 */
class HistoriesController extends AppController {
  public $helpers = ['Time'];
  public $uses = ['MUser', 'THistory', 'THistoryChatLog', 'THistoryStayLog', 'THistoryShareDisplay'];
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
    $this->layout = 'ajax';

    $historyId = $this->params->query['historyId'];

    $params = [
      'fields' => [
        'MUser.display_name',
        'THistoryChatLog.*'
      ],
      'conditions' => [
        'THistoryChatLog.t_histories_id' => $historyId
      ],
      'joins' => [
        [
          'type' => 'LEFT',
          'table' => 'm_users',
          'alias' => 'MUser',
          'conditions' => [
          'THistoryChatLog.m_users_id = MUser.id',
          'MUser.m_companies_id' => $this->userInfo['MCompany']['id']
          ]
        ]
      ],
      'order' => 'THistoryChatLog.created',
      'recursive' => -1
    ];
    $ret = $this->THistoryChatLog->find('all', $params);
    $this->set('THistoryChatLog', $ret);
    return $this->render('/Elements/Histories/remoteGetChatLogs');
  }

  public function remoteGetStayLogs() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';

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

  public function outputCSVOfChat(){
    $this->layout = null;
    //メモリ上に領域確保
    $fp = fopen('php://temp/maxmemory:'.(5*1024*1024),'a');

    $user_list = [
      ["あ", "hogehoge"],
      ["huga", "hugahuga"],
      ["hoge", "hogehoge"],
      ["huga", "hugahuga"]
    ];

    foreach($user_list as $user){
      fputcsv($fp, $user);
    }

    //ビューを使わない
    $this->autoRender = false;

    //download()内ではheader("Content-Disposition: attachment; filename=hoge.csv")を行っている
    $this->response->download("hoge.csv");

    //ファイルポインタを先頭へ
    rewind($fp);

    //リソースを読み込み文字列を取得する
    $csv = stream_get_contents($fp);

    //Content-Typeを指定
    $this->response->type('csv');

    //CSVをエクセルで開くことを想定して文字コードをSJIS-win
    $csv = mb_convert_encoding($csv,'SJIS-win','utf8');

    $this->response->body($csv);

    fclose($fp);
  }

  private function _setList($type=true){
    if ( !$this->coreSettings[C_COMPANY_USE_CHAT] || strcmp($type, 'false') === 0 ) {
      $this->paginate['THistory']['joins'][0]['type'] = "LEFT";
    }
    else {
      $this->paginate['THistory']['joins'][0]['type'] = "INNER";
    }

    $this->Session->write("histories.joins", $this->paginate['THistory']['joins'][0]['type']);
    $historyList = $this->paginate('THistory');

    // チャット担当者リスト
    // select * from t_history_chat_logs WHERE  GROUP BY t_histories_id, m_users_id
    $chat = [];
    if ( !empty($historyList) ) {
      $params = [
        'fields' => [
          'THistory.id',
          'MUser.display_name'
        ],
        'joins' => [
          [
            'type' => 'INNER',
            'table' => '(SELECT * FROM t_history_chat_logs '.
                 ' WHERE m_users_id IS NOT NULL '.
                 '   AND t_histories_id <= ' . $historyList[0]['THistory']['id'].
                 '   AND t_histories_id >= ' . $historyList[count($historyList) - 1]['THistory']['id'].
                 ' GROUP BY t_histories_id, m_users_id'.
                 ')',
            'alias' => 'THistoryChatLog',
            'conditions' => [
              'THistoryChatLog.t_histories_id = THistory.id'
            ]
          ],
          [
            'type' => 'INNER',
            'table' => 'm_users',
            'alias' => 'MUser',
            'conditions' => [
              'THistoryChatLog.m_users_id = MUser.id'
            ]
          ]
        ],
        'conditions' => [
          'THistory.m_companies_id' => $this->userInfo['MCompany']['id'],
          'THistory.del_flg !=' => 1
        ],
        'recursive' => -1
      ];
      $ret = $this->THistory->find('all', $params);
      foreach((array)$ret as $val){
        if ( isset($chat[$val['THistory']['id']]) ) {
          $chat[$val['THistory']['id']] .= "\n".$val['MUser']['display_name']."さん";
        }
        else {
          $chat[$val['THistory']['id']] = $val['MUser']['display_name']."さん";
        }
      }
    }
    $this->set('userList', $historyList);
    $this->set('historyList', $historyList);
    $this->set('chatUserList', $chat);
    $this->set('groupByChatChecked', $type);
  }

}
