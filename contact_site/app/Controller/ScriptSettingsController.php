<?php
/**
 * ScriptSettingController controller
 * コード設置・デモ画面
 */
class ScriptSettingsController extends AppController {

  public function beforeRender(){
    $protocol = (APP_MODE_DEV) ? "http" : "https";
    $fileName = $protocol . ":" . C_NODE_SERVER_ADDR . C_NODE_SERVER_FILE_PORT . "/client/" . $this->userInfo['MCompany']['company_key'] . ".js";
    $this->set("fileName", $fileName);

    $this->set("optList", [
      'sexes' => ["男性", "女性"],
      'products' => ["製品A", "製品B", "製品C", "製品D", "製品E"],
      'works' => ["会社員", "パート", "役　員", "学　生", "その他"]
    ]);
  }

  public function index(){
    $this->set('title_for_layout', 'コード設置・デモ画面');
  }

  public function demopage(){
    $this->set('title_for_layout', 'デモ画面');
    $this->layout = "frame";
  }

  public function testpage(){
    $this->set('title_for_layout', '目次');
    $this->layout = "normal";
    $this->set('layoutNumber', 1);
  }

  public function testpage2(){
    $this->layout = "normal";
    $this->set('layoutNumber', 2);
    $this->set('title_for_layout', "ウィジェット非表示タグ");
    $this->render('testpage');
  }

  public function testpage3(){
    $this->layout = "normal";
    $this->set('layoutNumber', 3);
    $this->set('title_for_layout', "フォーム用タグ");
    $this->render('testpage');
  }
  public function confirm(){
    $this->layout = "normal";
    $this->set('layoutNumber', 4);
    if ( !isset($this->data['ScriptSettings']) ) {
      $this->request->data['ScriptSettings'] = [];
    }
    $this->set('data', $this->request->data['ScriptSettings']);
    $this->set('title_for_layout', "フォーム確認");
    $this->render('testpage');
  }
}
