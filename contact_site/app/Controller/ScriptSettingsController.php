<?php
/**
 * ScriptSettingController controller
 * コード設置・デモ画面
 */
class ScriptSettingsController extends AppController {

    public function index(){
        $fileName = C_NODE_SERVER_ADDR . "/" . $this->userInfo['MCompany']['company_key'] . ".js";
        $scriptTag  = "";
        $scriptTag .= "<script type='text/javascript' src='" . $fileName . "'>";
        $scriptTag .= "</script>";

        $this->set("scriptName", $scriptTag);

    }

    public function demopage(){
        $this->layout = "frame";
    }
}
