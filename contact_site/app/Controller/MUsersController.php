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
		// Configure::write('debug', 0);
		$this->autoRender = FALSE;
		$this->layout = 'ajax';
		$this->__viewElement();
		// const
		if ( $this->request->data['type'] === 2 ) {

		}
		$this->render('/MUsers/remoteEntryUser');
	}

	/* *
	 * 登録画面
	 * @return void
	 * */
	public function remoteSaveEntryForm() {
		// Configure::write('debug', 0);
		$this->autoRender = FALSE;
		$this->layout = 'ajax';
		$tmpData = [];
		$saveData = [];
		if ( !$this->request->is('ajax') ) return false;
		$tmpData['MUser']['user_name'] = $this->request->data['userName'];
		$tmpData['MUser']['display_name'] = $this->request->data['displayName'];
		$tmpData['MUser']['mail_address'] = $this->request->data['mailAddress'];
		$tmpData['MUser']['password'] = $this->request->data['password'];
		$tmpData['MUser']['permission_level'] = $this->request->data['permissionLevel'];
// pr($this->request);
		// const
		$this->MUser->set($tmpData);
		$this->MUser->begin();
		// バリデーションチェックでエラーが出た場合
		if ( $this->MUser->validates() ) {
			$saveData = $tmpData;
			$saveData['MUser']['m_companies_id'] = $this->userInfo['MCompany']['id'];
			$saveData['MUser']['password'] = $this->MUser->makePassword($tmpData['MUser']['password']);
			$this->MUser->create();
			if ( $this->MUser->save($saveData, false) ) {
				$this->MUser->rollback();
				// $this->MUser->commit();
			}
			else {
				$this->MUser->rollback();
			}
		}
		$json = $this->MUser->validationErrors;
		return new CakeResponse(array('body' => json_encode($json)));
	}


	/* *
	 * 更新画面
	 * @return void
	 * */
	public function delete() {

	}

	private function __viewElement(){
		$this->set('authorityList', Configure::read("Authority"));
	}

}
