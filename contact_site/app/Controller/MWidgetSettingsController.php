<?php
/**
 * MWidgetSettingsController controller.
 * ウィジェット設定マスタ
 */
class MWidgetSettingsController extends AppController {
	public $uses = array('MWidgetSetting');

	// public function beforeRender(){
	// 	$this->set('siteKey', $this->userInfo['MCompany']['company_key']);
	// }

	/* *
	 * 一覧画面
	 * @return void
	 * */
	public function index() {
	}

}
