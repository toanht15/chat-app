<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/05/29
 * Time: 13:22
 * @property MCompany $MCompany
 * @property TReceiveVisitorFile $TReceiveVisitorFile
 */
App::uses('CakeText', 'Utility');
App::uses('FileAppController', 'Controller');

class FCController extends FileAppController
{
  const PARAM_COMMENT = 'c';
  const PARAM_SITE_KEY = 'k';

  public $uses = array('MCompany', 'TReceiveVisitorFile');
  public $components = array('Amazon', 'ImageThumbnailCreator');

  public function beforeFilter() {
    ini_set('memory_limit', '512M');
    parent::beforeFilter();
    $this->Auth->allow(array('pu','gd'));
    $this->response->header('Access-Control-Allow-Origin','*');
    $this->response->header('Access-Control-Allow-Methods','GET, POST');
    $this->response->header('Access-Control-Allow-Headers','X-Requested-With');
    $this->response->header('Access-Control-Allow-Headers','Content-Type');
    // FileAppController
    $this->fileTransferPrefix = "receivedFile/";
  }

  public function pu() {
    $this->autoRender = false;

    $this->validatePostMethod();
    // パラメータ取得
    $file = $this->params['form']['f'];
    $sitekey = $this->request->data(self::PARAM_SITE_KEY);
    $comment = $this->request->data(self::PARAM_COMMENT);
    $saveFileName = $this->getFilenameForSave($file);

    $filePath = $this->putFile($file, $saveFileName);

    // サムネイル生成
    $component = new ImageThumbnailCreatorComponent();
    $component->setFileData($file);
    $component->setFilename($saveFileName);
    $component->setScale(0.2);
    $pathAndFilename = $component->create();
    $thumbFilepath = $this->putFileByFullpath($pathAndFilename['path'], $pathAndFilename['filename']);

    return $this->saveUploadFile($sitekey, $file, $saveFileName, $filePath, $comment);
  }

  public function pus() {
    $this->autoRender = false;

    $this->validatePostMethod();
    // パラメータ取得
    $file = $this->params['form']['f'];
    $sitekey = $this->request->data(self::PARAM_SITE_KEY);
    $comment = $this->request->data(self::PARAM_COMMENT);
    $saveFileName = $this->getFilenameForSave($file);

    return json_encode(array(
      'success' => true,
      'downloadUrl' => 'dummy',
      'fileName' => $file['name'],
      'fileSize' => $file['size'],
      'extension' => $this->getExtension($file['name']),
      'comment' => $comment
    ));
  }

  public function gd($uuid) {
    try {
      $this->autoRender = false;
      $this->validateGetMethod();
      if (!empty($uuid)) {
        $data = $this->getFileDataFromUUID($uuid);
        $this->response->type($this->getExtension($data['TReceiveVisitorFile']['file_name']));
        $this->response->length($data['fileObj']['ContentLength']);
        $this->response->header('Content-Disposition', 'attachment; filename*=UTF-8\'\'' . rawurlencode($data['TReceiveVisitorFile']['file_name']));
        $this->response->body($data['fileObj']['Body']);
      } else {
        $this->response->statusCode(400);
        throw new BadRequestException('指定のパラメータでのアクセスではありません。');
      }
    } catch(Exception $e) {
      echo $e->getMessage();
      // IEのデフォルトエラーページが表示される対応
      // @see http://kiririmode.hatenablog.jp/entry/20160205/1454598000
      $str = '';
      for($i = 0; $i < 512; $i++) {
        $str .= ' ';
      }
      echo $str;
    }
  }

  private function validParameters() {
    $this->validatePostMethod();
    $this->validSiteKey();
  }

  private function saveUploadFile($company_key, $file, $saveFileName, $filePath, $comment) {
    try {
      $m_companies_id = $this->getIdFromCompanyKey($company_key);
      $uuid = str_replace('-', '', CakeText::uuid());
      $downloadUrl = $this->createReceiveFileDownloadUrl($uuid);
      $this->TReceiveVisitorFile->create();
      $this->TReceiveVisitorFile->begin();
      $this->TReceiveVisitorFile->set(array(
        'm_companies_id' => $m_companies_id,
        'uuid' => $uuid,
        'saved_file_key' => $saveFileName,
        'download_url' => $downloadUrl,
        'file_path' => $filePath,
        'file_name' => $file['name'],
        'file_size' => $file['size'],
        'comment' => $comment
      ));
      $this->TReceiveVisitorFile->save();
      $this->TReceiveVisitorFile->commit();
      return json_encode(array(
        'success' => true,
        'downloadUrl' => $this->TReceiveVisitorFile->field('download_url'),
        'fileName' => $file['name'],
        'fileSize' => $file['size'],
        'extension' => $this->getExtension($file['name']),
        'comment' => $comment
      ));
    } catch(Exception $e) {
      $this->TReceiveVisitorFile->rollback();
      $this->log('ファイル保存時にエラーが発生しました。 message : '.$e->getMessage());
      throw $e;
    }
  }

  /**
   * @param $uuid
   * @return string
   */
  protected function createReceiveFileDownloadUrl($uuid) {
    return C_NODE_SERVER_ADDR.'/FC/gd/'.$uuid;
  }

  private function getIdFromCompanyKey($company_key) {
    $data = $this->MCompany->find('first', array(
      'conditions' => array(
        'company_key' => $company_key,
        'del_flg' => 0
      )
    ));
    if(empty($data)) {
      throw new Exception('不明なcompany_key : '.$company_key);
    }
    return $data['MCompany']['id'];
  }

  private function getFileDataFromUUID($uuid) {
    $data = $this->TReceiveVisitorFile->find('first', array(
      'conditions' => array(
        'AND' => array(
          'uuid' => $uuid,
          'deleted' => null
        )
      )
    ));
    if(!isset($data['TReceiveVisitorFile'])) {
      return false;
    }

    $fileObj = $this->getFile($this->getSaveKey('thumb_'.$data['TReceiveVisitorFile']['saved_file_key']));
    $data['fileObj'] = $fileObj;

    return $data;
  }
}