<?php
/**
 * ScriptSettingController controller
 * コード設置・デモサイト
 */
class ScriptSettingsController extends AppController {

  public $uses = ['MCompany'];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->Auth->allow(['index','demopage','testpage','testpage2','testpage3','testpage4','testpage5','testpage6',
      'testpage7','testpage8','testpage9','testpage10','confirm']);
    if(isset($this->coreSettings[C_COMPANY_USE_SYNCLO]) && $this->coreSettings[C_COMPANY_USE_SYNCLO]) {
      $this->set('plan', 'synclo');
    }
    else {
      $this->set('plan', 'chat');
    }
  }

  public function beforeRender(){
    $url = Router::url();
    if($this->action == 'index') {
      $company_key = $this->userInfo['MCompany']['company_key'];
    }
    else {
      if(strpos($url,'/testpage2') !== false){
        $start = mb_strpos(Router::url(), '/testpage2')+11;
      }
      else if(strpos($url,'/testpage3') !== false){
        $start = mb_strpos(Router::url(), '/testpage3')+11;
      }
      else if(strpos($url,'/testpage4') !== false){
        $start = mb_strpos(Router::url(), '/testpage4')+11;
      }
      else if(strpos($url,'/testpage5') !== false){
        $start = mb_strpos(Router::url(), '/testpage5')+11;
      }
      else if(strpos($url,'/testpage6') !== false){
        $start = mb_strpos(Router::url(), '/testpage6')+11;
      }
      else if(strpos($url,'/testpage7') !== false){
        $start = mb_strpos(Router::url(), '/testpage7')+11;
      }
      else if(strpos($url,'/testpage8') !== false){
        $start = mb_strpos(Router::url(), '/testpage8')+11;
      }
      else if(strpos($url,'/testpage9') !== false){
        $start = mb_strpos(Router::url(), '/testpage9')+11;
      }
      else if(strpos($url,'/testpage10') !== false){
        $start = mb_strpos(Router::url(), '/testpage10')+12;
      }
      else if(strpos($url,'/testpage') !== false){
        $start = mb_strpos(Router::url(), '/testpage')+10;
      }
      else if(strpos($url,'/confirm') !== false){
        $start = mb_strpos(Router::url(), '/confirm')+9;
      }
      $company_key = substr($url, $start);
      $this->set("company_key", substr($url, $start));
    }
    $fileName = C_NODE_SERVER_ADDR . C_NODE_SERVER_FILE_PORT . "/client/" . $company_key . ".js";
    $plan = $this->MCompany->find('first', [
      'fields' => [
        'core_settings'
      ],
      'conditions' => [
        'company_key' => $company_key
      ]
    ]);
    $plan = json_decode($plan['MCompany']['core_settings']);
    //ベーシック、スタンダード
    if($plan->chat == 1 && empty($plan->synclo)) {
      $plan = "chat";
    }
    //シェアリング、プレミアム
    else if(($plan->chat == 1 && $plan->synclo == 1) || (empty($plan->chat) && $plan->synclo == 1)) {
      $plan = "sharing";
    }
    $this->set("plan", $plan);
    $this->set("fileName", $fileName);
    $this->set("optList", [
      'sexes' => ["男性", "女性"],
      'inquiry' => ["お問い合わせ項目1", "お問い合わせ項目2", "お問い合わせ項目3", "お問い合わせ項目4", "お問い合わせ項目5"]
    ]);
  }

  public function index(){
    $this->set('title_for_layout', 'コード設置・デモサイト');
    $this->set('companyKey',$this->userInfo['MCompany']['company_key']);
  }

  public function demopage(){
    $this->set('title_for_layout', 'デモサイト');
    $this->layout = "frame";
  }

  public function testpage(){
    Configure::write('debug', 0);
    $this->set('title_for_layout', '目次');
    $this->set('layoutNumber', 1);
  }

  public function testpage2(){
    Configure::write('debug', 0);
    $this->set('layoutNumber', 2);
    $this->set('title_for_layout', "プラン・費用");
    $this->render('testpage');
  }

  public function testpage3(){
    Configure::write('debug', 0);
    $this->set('layoutNumber', 3);
    $this->set('title_for_layout', "制作の流れ");
    $this->render('testpage');
  }

  public function testpage4(){
    Configure::write('debug', 0);
    $option = array(
      '都道府県選択' => '都道府県選択',
      '北海道' => '北海道',
      '青森県' => '青森県',
      '岩手県' => '岩手県',
      '宮城県' => '宮城県',
      '秋田県' => '秋田県',
      '山形県' => '山形県',
      '福島県' => '福島県',
      '茨城県' => '茨城県',
      '栃木県' => '栃木県',
      '群馬県' => '群馬県',
      '埼玉県' => '埼玉県',
      '千葉県' => '千葉県',
      '東京都' => '東京都',
      '神奈川県' => '神奈川県',
      '新潟県' => '新潟県',
      '富山県' => '富山県',
      '石川県' => '石川県',
      '福井県' => '福井県',
      '山梨県' => '山梨県',
      '長野県' => '長野県',
      '岐阜県' => '岐阜県',
      '静岡県' => '静岡県',
      '愛知県' => '愛知県',
      '三重県' => '三重県',
      '滋賀県' => '滋賀県',
      '京都府' => '京都府',
      '大阪府' => '大阪府',
      '兵庫県' => '兵庫県',
      '奈良県' => '奈良県',
      '和歌山県' => '和歌山県',
      '鳥取県' => '鳥取県',
      '島根県' => '島根県',
      '岡山県' => '岡山県',
      '広島県' => '広島県',
      '山口県' => '山口県',
      '徳島県' => '徳島県',
      '香川県' => '香川県',
      '愛媛県' => '愛媛県',
      '高知県' => '高知県',
      '福岡県' => '福岡県',
      '佐賀県' => '佐賀県',
      '長崎県' => '長崎県',
      '熊本県' => '熊本県',
      '大分県' => '大分県',
      '宮崎県' => '宮崎県',
      '鹿児島県' => '鹿児島県',
      '沖縄県' => '沖縄県'
    );
    $this->set('layoutNumber', 4);
    $this->set('title_for_layout', "フォーム用タグ");
    $this->set('option', $option);
    $this->render('testpage');
  }

  public function testpage5(){
    Configure::write('debug', 0);
    $this->set('layoutNumber', 5);
    $this->set('title_for_layout', "会社概要");
    $this->render('testpage');
  }

  public function testpage6(){
    Configure::write('debug', 0);
    $this->set('layoutNumber', 6);
    $this->set('title_for_layout', "制作実績");
    $this->render('testpage');
  }

  public function testpage7(){
    Configure::write('debug', 0);
    $this->set('layoutNumber', 7);
    $this->set('title_for_layout', "スタッフ紹介");
    $this->render('testpage');
  }

  public function testpage8(){
    Configure::write('debug', 0);
    $this->set('layoutNumber', 8);
    $this->set('title_for_layout', "リンク集");
    $this->render('testpage');
  }

  public function testpage9(){
    Configure::write('debug', 0);
    $this->set('layoutNumber', 9);
    $this->set('title_for_layout', "よく頂く質問");
    $this->render('testpage');
  }

  public function testpage10(){
    Configure::write('debug', 0);
    $this->set('layoutNumber', 10);
    $this->set('title_for_layout', "キャンペーン情報");
    $this->render('testpage');
  }

  public function confirm(){
    Configure::write('debug', 0);
    $this->set('layoutNumber', 11);
    if ( !isset($this->data['ScriptSettings']) ) {
      $this->request->data['ScriptSettings'] = [];
    }
    $this->set('data', $this->request->data['ScriptSettings']);
    $this->set('title_for_layout', "フォーム確認");
    $this->render('testpage');
  }
}
