<?php
App::uses('HttpSocket', 'Network/Http', 'Component', 'Controller', 'Utility/Validation');
class SalesForceController extends AppController {

  const API_CALL_TIMEOUT = 5;
  const SALES_FORCE_API = "https://webto.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8";

  public function beforeFilter(){
    parent::beforeFilter();
    $this->Auth->allow(['add']);
  }

  /* *
   * 基本設定ページ
   * @return void
   * */
  public function add() {
    $this->autoRender = FALSE;
    $data = $this->request->data;
    $error = $this->validateData($data);

    if (count($error) > 0) {
      $this->response->statusCode(400);
      $returnData = json_encode([
        'success' => false,
        'param' => $error['param'],
        'message' => $error['message']
      ]);

      return $returnData;
    }

    $salesForceData = $this->matchData($data);
    $socket = new HttpSocket(array(
      'timeout' => self::API_CALL_TIMEOUT
    ));

    $result = $socket->post(self::SALES_FORCE_API, $salesForceData);

    if (json_decode($result->code) === 409) {
      $this->response->statusCode(409);
      return json_encode([
        'success' => false,
        'message' => '登録に失敗しました。システム管理者にお問い合わせください。'
      ]);
    }
  }

  /**
   * @param $data
   * @return array
   */
  private function validateData($data)
  {
    $selectionData = $this->loadSelectionData();
    $error = [];
    if (!isset($data['last_name']) || !$data['last_name']) {
      $error['param'] = 'last_name';
      $error['message'] = '指定が必要です';
      return $error;
    }

    if (!isset($data['company_name']) || !$data['company_name']) {
      $error['param'] = 'company_name';
      $error['message'] = '指定が必要です';
      return $error;
    }

    if (!isset($data['mail']) || !$data['mail']) {
      $error['param'] = 'mail';
      $error['message'] = '指定が必要です';
      return $error;
    }

    if (isset($data['business_model'])) {
      foreach ($data['business_model'] as $datum) {
        if (!in_array($datum, ['社外（toC）', '社外（toB）', '社内'])) {
          $error['param'] = 'business_model';
          $error['message'] = 'この値が指定できません';
          return $error;
        }
      }

    }

    if (isset($data['target'])) {
      foreach ($data['target'] as $datum) {
        if (!in_array($datum, ['既存顧客', '見込み顧客', 'パートナー企業', '社内'])) {
          $error['param'] = 'target';
          $error['message'] = 'この値が指定できません';
          return $error;
        }
      }
    }

    foreach (array_keys($selectionData) as $key) {
      if ($this->isInvalidValue($key, $data, $selectionData)) {
        $error['param'] = $key;
        $error['message'] = 'この値が指定できません';
        return $error;
      }
    }

    return $error;
  }

  /**
   * @param $key
   * @param $data
   * @param $selectionData
   * @return bool
   */
  private function isInvalidValue($key, $data, $selectionData)
  {
    return isset($data[$key]) && $data[$key] != "" && !in_array($data[$key], $selectionData[$key]);
  }

  /**
   * @return array
   */
  private function loadSelectionData()
  {
    $data                                = [];
    $data['name_title']                  = ['Mr.', 'Ms.', 'Mrs.', 'Dr.', 'Prof.'];
    $data['lead_acquisition_channel']    = ['電話', 'トライアル申込フォーム', '問合せフォーム', 'チャット', 'ボクシル', 'ITトレンド', 'フォームDM', 'リトルクラウド', 'ボクシル（無効）', 'sales'];
    $data['customer_collection_channel'] = ['SEO', 'Google広告', '一括サイト', '比較サイト', 'リファラー', 'メールなど', '不明'];
    $data['introducer']                  = ['直契約', '代理店'];
    $data['distribution_channel']        = ['直契約', '代理店'];
    $data['product_type']                = ['MC', 'MV', 'MO', 'その他'];
    $data['usage']                       = ['Bot', '有人', '未定', '両方'];
    $data['sale_style']                  = ['オンプレ', 'ハーフクラウド', 'フルクラウド', 'その他'];
    $data['member_site']                 = ['有', '無', '両方'];
    $data['transaction_type']            = ['新規', '既存'];

    return $data;
  }

  /**
   * @param $data
   * @return array
   */
  private function matchData($data){
    $salesForceData                    = [];
    $salesForceData['first_name']      = $this->getData('first_name', $data); // 名
    $salesForceData['last_name']       = $this->getData('last_name', $data);; // 姓
    $salesForceData['email']           = $this->getData('mail', $data);; // メール
    $salesForceData['company']         = $this->getData('company_name', $data);; // 会社名
    $salesForceData['city']            = $this->getData('address2', $data);; // 市区郡
    $salesForceData['state']           = $this->getData('address1', $data);; // 都道府県
    $salesForceData['salutation']      = $this->getData('name_title', $data);; // 敬称
    $salesForceData['title']           = $this->getData('position', $data); // 役職
    $salesForceData['ULR']             = $this->getData('website', $data); // Web サイト
    $salesForceData['phone']           = $this->getData('phone', $data); // 電話
    $salesForceData['mobile']          = $this->getData('mobile', $data); // 携帯
    $salesForceData['street']          = $this->getData('address3', $data); // 町名・番地
    $salesForceData['zip']             = $this->getData('post_code', $data); // 郵便番号
    $salesForceData['country']         = $this->getData('country', $data); // 国
    $salesForceData['rating']          = $this->getData('rate', $data); // 評価
    $salesForceData['00N0o00000FSL7m'] = $this->getData('question', $data); // 問い合わせ内容
    $salesForceData['00N0o00000FSLXz'] = $this->getData('trial_begin_date', $data); // トライアル開始日
    $salesForceData['00N0o00000FSLYE'] = $this->getData('trial_end_date', $data); // トライアル終了日
    $salesForceData['00N0o00000GlN2z'] = isset($data['payer']) ? $data['payer'] : 0; // 決済者
    $salesForceData['00N0o00000GlN39'] = $this->getData('lead_acquisition_channel', $data); // リード獲得経路
    $salesForceData['00N0o00000GlN3J'] = $this->getMultipleData('business_model', $data); // ビジネスモデル
    $salesForceData['00N0o00000GlN3O'] = $this->getData('usage', $data); // 使い方
    $salesForceData['00N0o00000GlN3d'] = $this->getData('member_site', $data); // 会員向けサイト
    $salesForceData['00N0o00000GlN47'] = $this->getData('distribution_channel', $data); // 商流
    $salesForceData['00N0o00000GlN4C'] = $this->getData('introducer', $data); // 紹介元
    $salesForceData['00N0o00000GlN4H'] = $this->getData('transaction_type', $data); // 取引区分
    $salesForceData['00N0o00000GlN4R'] = $this->getData('investigation_motive', $data); // 検討動機
    $salesForceData['00N0o00000HHnko'] = $this->getMultipleData('target', $data); // 対象者
    $salesForceData['00N0o00000HKXWd'] = $this->getData('department', $data); // 部署
    $salesForceData['00N0o00000JZ6Hm'] = $this->getData('product_type', $data); // 製品種別
    $salesForceData['00N0o00000JZ6Hw'] = $this->getData('sale_style', $data); // 販売形態
    $salesForceData['00N0o00000JZEL3'] = $this->getData('customer_collection_channel', $data); // 集客チャネル

    return $salesForceData;
  }

  /**
   * @param $key
   * @param $data
   * @return string
   */
  private function getData($key, $data)
  {
   return isset($data[$key]) ? $data[$key] : "";
  }

  private function getMultipleData($key, $data)
  {
    $result = [];
    if (isset($data[$key])) {
      foreach ($data[$key] as $datum) {
        $result[] = $datum;
      }
    }

    return count($result) > 0 ? $result : "";
  }
}
