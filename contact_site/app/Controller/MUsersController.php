<?php
/**
 * MUsersController controller.
 * ユーザーマスタ
 */
class MUsersController extends AppController {
    public $uses = array('MUser', 'MCompany');
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

    public function beforeFilter(){
        parent::beforeFilter();
        $this->set('title_for_layout', 'ユーザー管理');
        $this->set('siteKey', $this->userInfo['MCompany']['company_key']);
        $this->set('limitUserNum', $this->userInfo['MCompany']['limit_users']);
    }

    /* *
     * 一覧画面
     * @return void
     * */
    public function index() {
        $this->paginate['MUser']['conditions']['MUser.m_companies_id'] = $this->userInfo['MCompany']['id'];
        $this->_viewElement();
        $this->set('userList', $this->paginate('MUser'));
        $this->set('userListCnt', $this->MUser->find('count', $this->paginate['MUser']['conditions']));
    }

    /* *
     * 登録画面
     * @return void
     * */
    public function remoteOpenEntryForm() {
        Configure::write('debug', 0);
        $this->autoRender = FALSE;
        $this->layout = 'ajax';
        $this->_viewElement();
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
        $errorMessage = null;

        if ( !$this->request->is('ajax') ) return false;

        if (!empty($this->request->data['userId'])) {
            $this->MUser->recursive = -1;
            $tmpData = $this->MUser->read(null, $this->request->data['userId']);
            $insertFlg = false;
        }
        else {
            $this->MUser->create();

            // アカウント数チェック
            if (!$this->_checkAcoundNum()) {
                $errorMessage = ['other' => ["契約しているアカウント数をオーバーしています"]];
            }
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
        if ( empty($errorMessage) && $this->MUser->validates() ) {
            $saveData = $tmpData;
            $saveData['MUser']['m_companies_id'] = $this->userInfo['MCompany']['id'];
            if ( $this->MUser->save($saveData, false) ) {
                $this->MUser->commit();
                $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
            }
            else {
                $this->MUser->rollback();
            }
        }
        if ( empty($errorMessage) ) {
            $errorMessage = $this->MUser->validationErrors;
        }
        return new CakeResponse(array('body' => json_encode($errorMessage)));
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
        if ( $this->MUser->logicalDelete($this->request->data['id']) ) {
            $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.deleteSuccessful'));
        }
        else {
            $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.deleteFailed'));
        }
    }

    private function _viewElement(){
        $this->set('authorityList', Configure::read("Authority"));
    }

    /**
     * アカウント数のチェック
     * @return bool
     **/
    private function _checkAcoundNum(){
        $mCompany = $this->MCompany->read(null, $this->userInfo['MCompany']['id']);
        if ( isset($mCompany['MCompany']) ) {
            $this->userInfo['MCompany'] = $mCompany['MCompany'];
        }

        $params = [
          'fields' => 'MUser.id',
          'conditions' => [
              'MUser.del_flg !=' => 1,
              'MUser.m_companies_id' => $this->userInfo['MCompany']['id']
          ],
          'recursive' => -1
        ];
        $mUserCnt = $this->MUser->find('count', $params);
        if ( $this->userInfo['MCompany']['limit_users'] <= $mUserCnt ) {
          return false;
        }
        return true;
    }

}
