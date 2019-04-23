<?php

App::uses('AppController', 'Controller');

class BillingsController extends AppController
{
  const MIN_DATE = '2018/12';

  public $paginate = array(
      'MCompany' => array(
          'order' => array('MCompany.id' => 'asc'),
          'fields' => array('*'),
          'limit' => 100,
          'joins' => array(
              array(
                  'type' => 'inner',
                  'table' => 'm_agreements',
                  'alias' => 'MAgreement',
                  'conditions' => array(
                      'MAgreement.m_companies_id = MCompany.id',
                  ),
              ),
              array(
                  'type' => 'left',
                  'table' => '(SELECT id,m_companies_id,mail_address,password FROM m_users WHERE del_flg != 1 AND permission_level = ' . (ADD_ACCOUNT_TO_M_USER ? C_AUTHORITY_ADMIN : C_AUTHORITY_SUPER) . ' GROUP BY m_companies_id)',
                  'alias' => 'AdminUser',
                  'conditions' => array(
                      'AdminUser.m_companies_id = MCompany.id',
                  ),
              ),
              array(
                  'type' => 'left',
                  'table' => '(SELECT id,m_companies_id,mail_address,password,count(m_companies_id) AS user_account FROM  m_users WHERE del_flg != 1 AND permission_level != 99 GROUP BY m_companies_id)',
                  'alias' => 'MUser',
                  'conditions' => array(
                      'MUser.m_companies_id = MCompany.id',
                  ),
              ),
              array(
                  'type' => 'left',
                  'table' => '(SELECT thcl.m_companies_id as mc, date_format(th.access_date, "%Y/%m") as date, SUM(case when thcl.achievement_flg = 0 THEN 1 ELSE 0 END) cv
    FROM (select t_histories_id, m_companies_id, achievement_flg from t_history_chat_logs
     force index(idx_t_history_chat_logs_achievement_flg_companies_id) where achievement_flg = 0 group by m_companies_id, t_histories_id) as thcl,
     t_histories as th
    WHERE
      thcl.t_histories_id = th.id
    group by date)',
                  'alias' => 'CVCount',
                  'conditions' => array(
                      'CVCount.mc = MCompany.id',
                  ),
              ),
          ),
          'conditions' => array(
              'MCompany.del_flg != ' => 1,
          ),
          'group' => array(
              'MCompany.id'
          )
      )
  );

  public function cv()
  {
    $this->set('title_for_layout', 'CV請求額一覧');
    if ($this->request->is('post')) {
      $targetDate = $this->request->data('Billings.targetDate');
    } else {
      // デフォルト：当月表示
      $targetDate = date('Y/m');
    }
    $this->setPaginateCondition($targetDate);
    $this->set('targetDate', $targetDate);
    $this->set('companyList', $this->paginate('MCompany'));
    $this->set('targetDateList', $this->getTargetDateList());
  }

  public function exportCV()
  {
    if ($this->request->is('post')) {
      $targetDate = $this->request->data('targetDate');
      // ヘッダー
      $csv[] = array(
          "対象期間",
          "会社名",
          "CV単価",
          "CV件数",
          "請求額"
      );

      $list = $this->MCompany->find('all', array(
          'order' => array('MCompany.id' => 'asc'),
          'fields' => array('*'),
          'joins' => array(
              array(
                  'type' => 'inner',
                  'table' => 'm_agreements',
                  'alias' => 'MAgreement',
                  'conditions' => array(
                      'MAgreement.m_companies_id = MCompany.id',
                      'MAgreement.agreement_start_day <= ' => $targetDate . '/01',
                      'MAgreement.agreement_end_day >= ' => $targetDate . '/' . $this->getLastDayOfMonth($targetDate)
                  ),
              ),
              array(
                  'type' => 'left',
                  'table' => '(SELECT thcl.m_companies_id as mc, date_format(th.access_date, "%Y/%m") as date, SUM(case when thcl.achievement_flg = 0 THEN 1 ELSE 0 END) cv
      FROM (select t_histories_id, m_companies_id, achievement_flg from t_history_chat_logs
       force index(idx_t_history_chat_logs_achievement_flg_companies_id) where achievement_flg = 0 group by m_companies_id, t_histories_id) as thcl,
       t_histories as th
      WHERE
        thcl.t_histories_id = th.id
      group by date)',
                  'alias' => 'CVCount',
                  'conditions' => array(
                      'CVCount.mc = MCompany.id',
                      'CVCount.date' => $targetDate
                  ),
              ),
          ),
          'conditions' => array(
              'MCompany.del_flg != ' => 1,
          ),
          'group' => array(
              'MCompany.id'
          )
      ));

      foreach ($list as $k => $v) {
        $row = array();
        $row['target_date'] = $targetDate;
        $row['company_name'] = $v['MCompany']['company_name'];
        $row['cv_value'] = !empty($v['MAgreement']['cv_value']) ? $v['MAgreement']['cv_value'] : 0;
        $row['cv_count'] = !empty($v['CVCount']['cv']) ? $v['CVCount']['cv'] : 0;
        $row['billing_value'] = intval($row['cv_value']) * intval($row['cv_count']);
        $csv[] = $row;
      }

      $this->outputCSV('cv-billing' . date('Y-m', strtotime(str_replace('/', '-', $targetDate))), $csv);
    }
  }

  private function setPaginateCondition($date)
  {
    $this->paginate['MCompany']['joins'][0]['conditions']['MAgreement.agreement_start_day <= '] = $date . '/01';
    $this->paginate['MCompany']['joins'][0]['conditions']['MAgreement.agreement_end_day >= '] = $date . '/' . $this->getLastDayOfMonth($date);
    $this->paginate['MCompany']['joins'][count($this->paginate['MCompany']['joins']) - 1]['conditions']['CVCount.date'] = $date;
  }

  private function getLastDayOfMonth($date)
  {
    return date('d', strtotime('last day of ' . $date));
  }

  private function getTargetDateList()
  {
    $list = array();
    $date = date('Y/m');
    while (strcmp($date, self::MIN_DATE)) {
      $list[$date] = $date;
      //指定日時を月はじめに変換する
      $target_day = date("Y-m-1", strtotime(str_replace('/', '-', $date)));
      //1ヶ月前の日時を取得
      $date = date('Y/m', strtotime($target_day . "-1 month"));
    }
    return $list;
  }

  private function outputCSV($name, $csv = [])
  {
    $this->layout = null;

    //メモリ上に領域確保
    $fp = fopen('php://temp/maxmemory:' . (5 * 1024 * 1024), 'a');
    foreach ($csv as $row) {
      fputcsv($fp, $row);
    }

    //ビューを使わない
    $this->autoRender = false;

    $filename = date("YmdHis") . "_" . $name;

    //download()内ではheader("Content-Disposition: attachment; filename=hoge.csv")を行っている
    $this->response->download($filename . ".csv");

    //ファイルポインタを先頭へ
    rewind($fp);

    //リソースを読み込み文字列を取得する
    $csv = stream_get_contents($fp);

    //Content-Typeを指定
    $this->response->type('csv');

    //CSVをエクセルで開くことを想定して文字コードをSJIS-win
    $csv = mb_convert_encoding($csv, 'SJIS-win', 'utf8');
    $this->response->body($csv);
    fclose($fp);

  }
}