<?php
/**
 * ScriptSettingController controller
 * コード設置・デモ画面
 */
class ScriptSettingsController extends AppController {

    public function beforeRender(){
    	$fileName = C_NODE_SERVER_ADDR . C_NODE_SERVER_FILE_PORT . "/client/" . $this->userInfo['MCompany']['company_key'] . ".js";
    	$scriptTag  = "";
    	$scriptTag .= "<script type='text/javascript' src='" . $fileName . "'>";
    	$scriptTag .= "</script>";

    	$this->set("scriptName", $scriptTag);
    }

    public function index(){
        $this->set('title_for_layout', 'コード設置・デモ画面');
    }

    public function demopage(){
        $this->set('title_for_layout', 'デモ画面');
        $this->layout = "frame";
    }

    public function testpage(){
        $this->set('title_for_layout', 'デモ画面');
        $this->layout = "normal";
        $this->set('layoutNumber', 1);
        $this->set('title_for_layout', "サンプルページ：１ページ目");
    }

    public function testpage2(){
        $this->set('title_for_layout', 'デモ画面');
        $this->layout = "normal";
        $this->set('layoutNumber', 2);
        $this->set('title_for_layout', "サンプルページ：２ページ目");
        $this->render('testpage');
    }
}
