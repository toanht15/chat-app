<?php
/**
 * ファイル送信用コントローラー
 * User: masashi_shimizu
 * Date: 2017/12/20
 * Time: 11:26
 */
App::uses('FileAppController', 'Controller');

class FileController extends FileAppController
{
  public function beforeFilter() {
    parent::beforeFilter();
    $this->Auth->allow('download');

    // FileAppController
    $this->fileTransferPrefix = "fileTransfer/";
  }

  public function upload() {
    $this->autoRender = false;

    $this->validatePostMethod();
    // パラメータ取得
    $file = $this->params['form'][self::PARAM_FILE];
    $targetUserId = $this->request->data(self::PARAM_TARGET_USER_ID);
    $saveFileName = $this->getFilenameForSave($file);

    $filePath = $this->putFile($file, $saveFileName);
    return $this->saveUploadFileData($file, $saveFileName, $filePath);
  }

  public function download() {
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
}
