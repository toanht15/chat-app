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

class FCController extends FileAppController
{
  const PARAM_COMMENT = 'c';
  const PARAM_SITE_KEY = 'k';

  public $uses = array('MCompany', 'TReceiveVisitorFile');

  public function beforeFilter() {
    parent::beforeFilter();
    $this->Auth->allow(array('pu','gd'));

    // FileAppController
    $this->fileTransferPrefix = "receivedFile/";
  }

  public function pu() {
    $this->autoRender = false;

    $this->validatePostMethod();
    // パラメータ取得
    $file = $this->params['form'][self::PARAM_FILE];
    $sitekey = $this->request->data(self::PARAM_SITE_KEY);
    $comment = $this->request->data(self::PARAM_COMMENT);
    $saveFileName = $this->getFilenameForSave($file);

    $filePath = $this->putFile($file, $saveFileName);
    return $this->saveUploadFile($sitekey, $file, $saveFileName, $filePath, $comment);
  }

  public function gd() {
    try {
      $this->autoRender = false;
      $this->validateGetMethod();
      $param = $this->request->query(self::PARAM_PARAM);
      if (!empty($param)) {
        $decryptParameters = $this->decryptParameterForDownload($param);
        if ($this->isExpire($decryptParameters['created'])) {
          $this->response->statusCode(404);
          throw new NotFoundException('有効期限切れのURLです。');
        }
        $file = $this->getFileByFileId($decryptParameters['fileId']);
        $this->response->type($this->getExtension($file['record']['file_name']));
        $this->response->length($file['fileObj']['ContentLength']);
        $this->response->header('Content-Disposition', 'attachment; filename*=UTF-8\'\'' . rawurlencode($file['record']['file_name']));
        $this->response->body($file['fileObj']['Body']);
        $this->updateDownloadDataById($decryptParameters['fileId']);
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

  private function getFileByFileId($fileId) {
    $data = null;
    $file = null;
    $result = array();
    if($this->scenarioMode) {
      $data = $this->TChatbotScenarioSendFile->findById($fileId);
      $pos = strpos($data['TChatbotScenarioSendFile']['file_path'], $this->fileTransferPrefix);
      if ($pos !== FALSE) {
        $file = $this->getFile($this->getSaveKey(substr($data['TChatbotScenarioSendFile']['file_path'], $pos)));
      }
      $result = array(
        'fileObj' => $file,
        'record' => $data['TChatbotScenarioSendFile']
      );
    } else {
      $data = $this->TUploadTransferFile->findById($fileId);
      $file = $this->getFile($this->getSaveKey($data['TUploadTransferFile']['saved_file_key']));
      $result = array(
        'fileObj' => $file,
        'record' => $data['TUploadTransferFile']
      );
    }

    return $result;
  }

  private function validParameters() {
    $this->validatePostMethod();
    $this->validSiteKey();
  }

  private function saveUploadFile($company_key, $file, $saveFileName, $filePath, $comment) {
    try {
      $m_companies_id = $this->getIdFromCompanyKey($company_key);
      $uuid = str_replace('-', '', CakeText::uuid());
      $downloadUrl = $this->createDownloadUrl($uuid);
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
      $this->TUploadTransferFile->rollback();
      $this->log('ファイル保存時にエラーが発生しました。 message : '.$e->getMessage());
      throw $e;
    }
  }

  /**
   * @override
   * @param $created
   * @param $fileId
   * @param bool $isScenarioDownload
   * @return string
   */
  protected function createDownloadUrl($uuid) {
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
}