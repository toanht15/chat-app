<?php
/**
 * PersonalSettingsController controller.
 * ユーザーマスタ
 */
class PersonalSettingsController extends AppController {
	public $uses = array('MUser');

	public function beforeRender(){
		$this->set('siteKey', $this->userInfo['MCompany']['company_key']);
	}

	/* *
	 * 一覧画面
	 * @return void
	 * */
	public function index() {
		$this->MUser->recursive = -1;
		if ( $this->request->is('post') ) {
			$this->_update($this->request->data);
		}
		else {
			$this->data = $this->MUser->read(null, $this->userInfo['id']);
		}
	}

	/* *
	 * 更新
	 * @return void
	 * */
	private function _update($inputData) {
		$errors = [];
		// パスワードを変更する場合
		if ( !empty($inputData['MUser']['edit_password']) ) {
			$this->MUser->validate = $this->MUser->updateValidate;
		}

		// パスワードチェックが問題なければ単独でバリデーションチェックのみ
		$this->MUser->set($inputData);
		$this->MUser->begin();

		if ( $this->MUser->validates() ) {
			// バリデーションチェックが成功した場合
			// 保存処理
			if ( $this->MUser->save($inputData, false) ) {
				$this->MUser->commit();
			}
			else {
				$this->MUser->rollback();
			}
		}
		else {
			// 画面に返す
			$errors = $this->MUser->validationErrors;
		}
	}

}
