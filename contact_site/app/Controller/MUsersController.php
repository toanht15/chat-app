<?php
/**
 * MUsersController controller.
 * ユーザーマスタ
 */
class MUsersController extends AppController {
	public $uses = array('MUser');
	public $paginate = array(
		'MUser' => array(
			'limit' => 10,
			'order' => array(
				'MUser.id' => 'asc'
			),
			'fields' => array(
				'MUser.*'
			),
			'conditions' => array(
				'MUser.del_flg != ' => 1
			),
			'recursive' => -1
		)
	);

	public function beforeRender(){
		$this->set('siteKey', $this->userInfo['MCompany']['company_key']);
	}

	/* *
	 * 一覧画面
	 * @return void
	 * */
	public function index() {
		$this->paginate['MUser']['conditions']['MUser.m_companies_id'] = $this->userInfo['MCompany']['id'];
		$this->__viewElement();
		$this->set('userList', $this->paginate('MUser'));
	}

	/* *
	 * 登録画面
	 * @return void
	 * */
	public function remoteOpenEntryForm() {
		Configure::write('debug', 0);
		$this->autoRender = FALSE;
		$this->layout = 'ajax';
		$this->__viewElement();
		// const
		if ( strcmp($this->request->data['type'], 2) === 0 ) {
			$this->MUser->recursive = -1;
			$this->request->data = $this->MUser->read(null, $this->request->data['id']);
		}
		$this->render('/MUsers/remoteEntryUser');
	}

	/* *
	 * 登録画面
	 * @return void
	 * */
	public function remoteSaveEntryForm() {
		Configure::write('debug', 0);
		$this->autoRender = FALSE;
		$this->layout = 'ajax';
		$tmpData = [];
		$saveData = [];
		$insertFlg = true;

		if ( !$this->request->is('ajax') ) return false;

		if (!empty($this->request->data['userId'])) {
			$this->MUser->recursive = -1;
			$tmpData = $this->MUser->read(null, $this->request->data['userId']);
			$insertFlg = false;
		}
		else {
			$this->MUser->create();
		}

		$tmpData['MUser']['user_name'] = $this->request->data['userName'];
		$tmpData['MUser']['display_name'] = $this->request->data['displayName'];
		$tmpData['MUser']['mail_address'] = $this->request->data['mailAddress'];
		$tmpData['MUser']['permission_level'] = $this->request->data['permissionLevel'];

		if ( !$insertFlg && empty($this->request->data['password']) ) {
			unset($this->MUser->validate['password']);
		}
		else {
			$tmpData['MUser']['new_password'] = $this->request->data['password'];
		}

		// const
		$this->MUser->set($tmpData);

		$this->MUser->begin();
		// バリデーションチェックでエラーが出た場合
		if ( $this->MUser->validates() ) {
			$saveData = $tmpData;
			$saveData['MUser']['m_companies_id'] = $this->userInfo['MCompany']['id'];
			if ( !empty($saveData['MUser']['new_password']) ) {
				unset($saveData['MUser']['new_password']);
				$saveData['MUser']['password'] = $this->MUser->makePassword($tmpData['MUser']['password']);
			}
			if ( $this->MUser->save($saveData, false) ) {
				$this->MUser->commit();
			}
			else {
				$this->MUser->rollback();
			}
		}
		$json = $this->MUser->validationErrors;
		return new CakeResponse(array('body' => json_encode($json)));
	}


	/* *
	 * 削除
	 * @return void
	 * */
	public function remoteDeleteUser() {
		Configure::write('debug', 0);
		$this->autoRender = FALSE;
		$this->layout = 'ajax';
		$this->MUser->recursive = -1;
		$ret = $this->MUser->logicalDelete($this->request->data['id']);
		// $saveData = $ret;
		// $saveData['MUser']['del_flg'] = 1;
		// $this->log($saveData, 'debug');
	}

	private function __viewElement(){
		$this->set('authorityList', Configure::read("Authority"));
	}

}
