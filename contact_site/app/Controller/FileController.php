<?php
/**
 * ファイル送信用コントローラー
 * User: masashi_shimizu
 * Date: 2017/12/20
 * Time: 11:26
 * @property TUploadTransferFile $TUploadTransferFile
 */

class FileController extends AppController
{

  const ENCRYPT_SECRET_KEY = "FoW3wLKXUB4HiCDC0fpIb63E066L03R57f3r6wwk3VyxTp3AGp68lBb7bghmRU80";
  const ENCRYPT_PARAM_DELIMITER = "@@";
  const EXPIRE_SEC = 3600; // ここを変更する場合はS3のライフサイクル設定も見直すこと。

  const PARAM_FILE = "file";
  const PARAM_TARGET_USER_ID = "targetUserId";
  const PARAM_PARAM = "param";

  const FILE_TRANSFER_PREFIX = "fileTransfer/";
  const FILE_SCENARIO_TRANSFER_PREFIX = "fileScenarioTransfer/";

  public $uses = ['TUploadTransferFile'];
  public $components = ['Amazon'];

  public function beforeFilter() {
    parent::beforeFilter();
    $this->Auth->allow('download');
  }

  public function upload() {
    $this->autoRender = false;

    $this->validatePostMethod();
    // パラメータ取得
    $file = $this->params['form'][self::PARAM_FILE];
    $targetUserId = $this->request->data(self::PARAM_TARGET_USER_ID);
    $saveFileName = $this->getFilenameForSave($file);

    $filePath = $this->putFile($file, $saveFileName, self::FILE_TRANSFER_PREFIX);
    return $this->saveUploadFileData($file, $saveFileName, $filePath);
  }

  /**
   * ファイルアップロード(シナリオ設定のファイル送信アクション)
   * @return String 保存先URL
   */
  public function uploadForScenario() {
    $this->autoRender = false;
    $this->validatePostMethod();

    $file = $this->params['form'][self::PARAM_FILE];
    $saveFileName = $this->getFilenameForSave($file);

    // $filePath = $this->putFile($file, $saveFileName, self::FILE_SCENARIO_TRANSFER_PREFIX);
    $key = $this->getSaveKey($saveFileName, self::FILE_SCENARIO_TRANSFER_PREFIX);

    return json_encode([
      'success' => true,
      // 'file_path' => $filePath,
      'file_path' => $key,
      'file_name' => $file['name'],
      'file_size' => $file['size']
    ]);
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

  private function getFilenameForSave($file) {
    return $this->userInfo['MCompany']['company_key']."-".date("YmdHis").".".$this->getExtension($file['name']);
  }

  /**
   * S3へのファイルアップロード
   * @param  Object $file         実ファイル
   * @param  String $saveFileName ファイル名
   * @param  String $dirName      保存先パス
   * @return String               保存先URL
   */
  private function putFile($file, $saveFileName, $dirName = self::FILE_TRANSFER_PREFIX) {
    return $this->Amazon->putObject($this->getSaveKey($saveFileName, $dirName), $file['tmp_name']);
  }

  /**
   * S3の保存先パス+ファイル名の生成
   * @param  String $saveFileName 保存ファイル名
   * @param  String $dirName      S3の保存先パス
   * @return String               S3の保存先パス+ファイル名
   */
  private function getSaveKey($saveFileName, $dirName = self::FILE_TRANSFER_PREFIX) {
    return $dirName.$saveFileName;
  }

  private function getFileByFileId($fileId) {
    try {
      $data = $this->TUploadTransferFile->findById($fileId);
      $file = $this->Amazon->getObject($this->getSaveKey($data['TUploadTransferFile']['saved_file_key']));
      if(empty($file)) {
        $this->log('Aws::getObject時のデータが空です。 fileId : '.$fileId, LOG_WARNING);
        throw new NotFoundException('ファイルが存在しません。');
      }
    } catch (\Aws\S3\Exception\S3Exception $e) {
      if(strcmp($e->getAwsErrorCode(), 'NoSuchKey') !== 0) {
        $this->log('Aws::getObjectで不明なエラー : '.$e->getMessage(), LOG_WARNING);
      }
      throw new NotFoundException('ファイルが存在しません。');
    } catch (\Aws\Exception\AwsException $e) {
      $this->log('AWSで不明なエラー : '.$e->getMessage(), LOG_WARNING);
      throw new NotFoundException('ファイルが存在しません。');
    } catch (Exception $e) {
      $this->log('FileControll::getFileByFileIdで不明なエラー : '.$e->getMessage(), LOG_WARNING);
      throw new NotFoundException('ファイルが存在しません。');
    }

    return array(
      'fileObj' => $file,
      'record' => $data['TUploadTransferFile']
    );
  }

  private function updateDownloadDataById($fileId) {
    $this->TUploadTransferFile->read(null, $fileId);
    $this->TUploadTransferFile->set([
      'download_flg' => 1,
      'downloaded' => date('Y-m-d H:i:s')
    ]);
    $this->TUploadTransferFile->save();
  }

  private function saveUploadFileData($file, $saveFileName, $filePath) {
    try {
      $this->TUploadTransferFile->create();
      $this->TUploadTransferFile->begin();
      $this->TUploadTransferFile->set([
        'm_companies_id' => $this->userInfo['MCompany']['id'],
        'saved_file_key' => $saveFileName,
        'file_path' => $filePath,
        'file_name' => $file['name'],
        'file_size' => $file['size'],
        'download_flg' => 0
      ]);
      $this->TUploadTransferFile->save();
      $lastInsertedId = $this->TUploadTransferFile->getLastInsertID();
      $created = $this->TUploadTransferFile->field('created');
      $downloadUrl = $this->createDownloadUrl($created, $lastInsertedId);
      $this->TUploadTransferFile->set([
        'download_url' => $downloadUrl
      ]);
      $this->TUploadTransferFile->save();
      $this->TUploadTransferFile->commit();
      return json_encode([
        'success' => true,
        'downloadUrl' => $this->TUploadTransferFile->field('download_url'),
        'fileName' => $file['name'],
        'fileSize' => $file['size'],
        'extension' => $this->getExtension($file['name']),
        'expired' => $this->getExpiredDatetime($created)
      ]);
    } catch(Exception $e) {
      $this->TUploadTransferFile->rollback();
      //FIXME レスポンスデータ
      throw $e;
    }
  }

  private function getExpiredDatetime($created) {
    return date('Y-m-d H:i:s', strtotime($created) + self::EXPIRE_SEC);
  }

  private function isExpire($created) {
    return (time() - strtotime($created)) >= self::EXPIRE_SEC;
  }

  private function createDownloadUrl($created, $fileId) {
    return C_NODE_SERVER_ADDR.'/File/download?param='.$this->encryptParameterForDownload($created, $fileId);
  }

  private function encryptParameterForDownload($created, $fileId) {
    // ※結合順序注意！！
    $param = $created.self::ENCRYPT_PARAM_DELIMITER.$fileId;
    $encrypted = Security::encrypt($param, self::ENCRYPT_SECRET_KEY);
    return rawurlencode(base64_encode($encrypted));
  }

  private function decryptParameterForDownload($val) {
    $decrypted = Security::decrypt(base64_decode(rawurldecode($val)), self::ENCRYPT_SECRET_KEY);
    if(empty($decrypted)) {
      throw new Exception('復号失敗 : '.$val);
    }
    $explodeVal = explode(self::ENCRYPT_PARAM_DELIMITER, $decrypted);
    return array(
      'created' => $explodeVal[0],
      'fileId' => $explodeVal[1]
    );
  }

  private function getExtension($filename) {
    return mb_strtolower(pathinfo($filename, PATHINFO_EXTENSION));
  }
}
