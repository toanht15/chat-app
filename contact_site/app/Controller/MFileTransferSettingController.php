<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/12/21
 * Time: 11:44
 * @property MFileTransferSetting $MFileTransferSetting
 */


class MFileTransferSettingController extends AppController
{
  public $uses = ['MFileTransferSetting'];

  private $defaultAllowExtensions = array(
    'pdf',
    'ppt',
    'pptx',
    'jpg',
    'png',
    'gif'
  );

  public function beforeFilter() {
    parent::beforeFilter();
    $this->set('title_for_layout', 'ファイル送信設定');
  }

  public function index() {
    if($this->request->is('post')) {
      $this->upsert();
    } else {
      $this->renderView();
    }
    $this->set('typeSelect', Configure::read('fileTransferSettingType'));
  }

  public function getAllowExtensions() {
    $default = $this->defaultAllowExtensions;
    $myData = $this->MFileTransferSetting->find('first', array("condition" => array('MFileTransferSetting.m_companies_id' => $this->userInfo['MCompany']['id'])));
    if(!empty($myData) && strcmp($myData['MFileTransferSetting']['type'], C_FILE_TRANSFER_SETTING_TYPE_EXTEND) === 0) {
      // 拡張設定利用中であれば拡張する
      $myExtensions = explode(',',$myData['MFileTransferSetting']['allow_extensions']);
      $default = array_merge($default, $myExtensions);
    }
    return array_values(array_unique($default));
  }

  private function upsert() {
    try {
      if (empty($this->request->data['MFileTransferSetting']['id'])) {
        $this->insert();
      } else {
        $this->update();
      }
    } catch (Exception $e) {
      $this->renderMessage(C_MESSAGE_TYPE_ERROR, Configure::read('message.const.saveFailed'));
      return;
    }
    $this->renderMessage(C_MESSAGE_TYPE_SUCCESS, Configure::read('message.const.saveSuccessful'));
  }

  private function renderView() {
    $this->request->data = $this->getMyFileTransferSetting();
  }

  private function insert() {
    try {
      $this->request->data['MFileTransferSetting']['m_companies_id'] = $this->userInfo['MCompany']['id'];
      $this->MFileTransferSetting->create();
      $this->MFileTransferSetting->begin();
      $this->MFileTransferSetting->set($this->request->data);
      $this->doValidate();
      if (!empty($errors)) {
        $this->MFileTransferSetting->rollback();
        throw new Exception($errors);
      }
      if (!$this->MFileTransferSetting->save()) {
        $this->MFileTransferSetting->rollback();
        throw new Exception('DB登録時にエラーが発生しました。');
      }
      $this->MFileTransferSetting->commit();
      $this->request->data['MFileTransferSetting']['id'] = $this->MFileTransferSetting->getLastInsertId();
    } catch(Exception $e) {
      $this->MFileTransferSetting->rollback();
      if($this->MFileTransferSetting->validationErrors) {
        $this->set('errors', $this->MFileTransferSetting->validationErrors);
      }
      throw $e;
    }
  }

  private function update() {
    try {
      $this->MFileTransferSetting->read(null, $this->request->data['MFileTransferSetting']['id']);
      if ($this->MFileTransferSetting->field('m_companies_id') !== $this->userInfo['MCompany']['id']) {
        throw new Exception('不正な更新処理です。');
      }
      $this->MFileTransferSetting->set([
          'type' => $this->request->data['MFileTransferSetting']['type'],
          'allow_extensions' => $this->request->data['MFileTransferSetting']['allow_extensions']
      ]);
      $this->doValidate();
      $this->MFileTransferSetting->save();
    } catch(Exception $e) {
      $this->MFileTransferSetting->rollback();
      if($this->MFileTransferSetting->validationErrors) {
        $this->set('errors', $this->MFileTransferSetting->validationErrors);
      }
      throw $e;
    }
  }

  private function getMyFileTransferSetting() {
    $val = $this->MFileTransferSetting->findByMCompaniesId($this->userInfo['MCompany']['id']);
    if(empty($val)) {
      $val = $this->getDefaultTransferSetting();
    }
    return $val;
  }

  private function getDefaultTransferSetting() {
    return [
      'MFileTransferSetting' => [
        'm_companies_id' => $this->userInfo['MCompany']['id'],
        'type' => C_FILE_TRANSFER_SETTING_TYPE_BASIC,
        'allow_extensions' => '',
      ]
    ];
  }

  private function doValidate() {
    if(!$this->MFileTransferSetting->validates()) {
      //NG
      throw new InvalidArgumentException('バリデーションエラー');
    }
    // OK
  }
}