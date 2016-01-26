<?php
/**
 * ScriptSettingController controller
 * コード設置・デモ画面
 */
class ScriptSettingsController extends AppController {

    public function beforeFilter(){
    }

    public function index(){
        $this->set('title_for_layout', 'コード設置・デモ画面');
        $fileName = C_NODE_SERVER_ADDR . "/client/" . $this->userInfo['MCompany']['company_key'] . ".js";
        $scriptTag  = "";
        $scriptTag .= "<script type='text/javascript' src='" . $fileName . "'>";
        $scriptTag .= "</script>";

        $this->set("scriptName", $scriptTag);

    }

    public function demopage(){
        $this->set('title_for_layout', 'デモ画面');
        $this->layout = "frame";
    }

    public function testpage(){
        $this->set('title_for_layout', 'デモ画面');
        $this->layout = "normal";
        $layoutNumber = 1;
        $title = "サンプルページ：１ページ目";
        if ( !empty($this->params->query['page']) ) {
            if ( intval($this->params->query['page']) === 2 ) {
                $layoutNumber = $this->params->query['page'];
                $title = 'サンプルページ：２ページ目';
            }
        }
        $this->set('layoutNumber', $layoutNumber);
        $this->set('title_for_layout', $title);
    }
}
