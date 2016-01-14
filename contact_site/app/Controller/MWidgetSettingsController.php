<?php
/**
 * MWidgetSettingsController controller.
 * ウィジェット設定マスタ
 */
class MWidgetSettingsController extends AppController {
    public $uses = array('MWidgetSetting');
    public $helpers = array('ngForm');

    /* *
     * 一覧画面
     * @return void
     * */
    public function index() {
        if ( $this->request->is('post') ) {
            $errors = $this->_update($this->request->data);
            if ( empty($errors) ) {
                $this->set('successMessage', ['type' => C_MESSAGE_TYPE_SUCCESS, 'text' => Configure::read('message.const.saveSuccessful')]);
            }
            else {
                $this->set('successMessage', ['type' => C_MESSAGE_TYPE_ERROR, 'text' => Configure::read('message.const.saveFailed')]);
            }
        }
        else {
            $this->data = $this->MWidgetSetting->read(null, $this->userInfo['MCompany']['id']);
        }
        $this->_viewElement();
    }

    private function _viewElement() {
        $this->set('widgetDisplayType', Configure::read('WidgetDisplayType'));
    }

    /* *
     * 更新
     *
     * @params $inputData(array)
     * @return $errors(array) エラー文言
     * */
    private function _update($inputData) {
        $errors = [];

        // パスワードチェックが問題なければ単独でバリデーションチェックのみ
        $this->MWidgetSetting->set($inputData);
        $this->MWidgetSetting->begin();

        if ( $this->MWidgetSetting->validates() ) {
            // バリデーションチェックが成功した場合
            // 保存処理
            if ( $this->MWidgetSetting->save($inputData, false) ) {
                $this->MWidgetSetting->commit();
            }
            else {
                $this->MWidgetSetting->rollback();
                $errors['rollback'] = "保存処理に失敗しました。";
            }
        }
        else {
            // 画面に返す
            $errors = $this->MWidgetSetting->validationErrors;
        }
        return $errors;
    }

}
