<?php
/**
 * FileController controller.
 * S3のファイル管理用コントローラー
 * @property TUploadTransferFile $TUploadTransferFile
 */

class FileAppController extends AppController
{

  const ENCRYPT_SECRET_KEY = "FoW3wLKXUB4HiCDC0fpIb63E066L03R57f3r6wwk3VyxTp3AGp68lBb7bghmRU80";
  const ENCRYPT_PARAM_DELIMITER = "@@";
  const EXPIRE_SEC = 3600; // ここを変更する場合はS3のライフサイクル設定も見直すこと。

  const PARAM_FILE = "file";
  const PARAM_TARGET_USER_ID = "targetUserId";
  const PARAM_PARAM = "param";

  public $uses = ['TUploadTransferFile'];
  public $components = ['Amazon'];

  // デフォルト設定
  protected $fileTransferPrefix = 'fileTransfer/';

  public function beforeFilter() {
    parent::beforeFilter();
  }

  /**
   * S3保存時のファイル名の生成
   * @param  Object $file ファイルオブジェクト
   * @return String       ファイル名
   */
  protected function getFilenameForSave($file) {
    return $this->userInfo['MCompany']['company_key']."-".date("YmdHis").".".$this->getExtension($file['name']);
  }

  /**
   * S3へのファイルアップロード
   * @param  Object $file         ファイルオブジェクト
   * @param  String $saveFileName ファイル名
   * @return String               保存先URL
   */
  protected function putFile($file, $saveFileName) {
    return $this->Amazon->putObject($this->getSaveKey($saveFileName), $file['tmp_name']);
  }

  /**
   * S3のファイル削除
   * @param  String $file 保存先パス + ファイル名
   * @return Void
   */
  protected function removeFile($file) {
    return $this->Amazon->removeObject($file);
  }

  /**
   * S3保存時の相対パス取得
   * @param  String $saveFileName 保存ファイル名
   * @return String               ファイル名を含む相対パス
   */
  protected function getSaveKey($saveFileName) {
    return $this->fileTransferPrefix.$saveFileName;
  }

  protected function getFileByFileId($fileId) {
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

  protected function updateDownloadDataById($fileId) {
    $this->TUploadTransferFile->read(null, $fileId);
    $this->TUploadTransferFile->set([
      'download_flg' => 1,
      'downloaded' => date('Y-m-d H:i:s')
    ]);
    $this->TUploadTransferFile->save();
  }

  protected function saveUploadFileData($file, $saveFileName, $filePath) {
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

  protected function isExpire($created) {
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

  protected function decryptParameterForDownload($val) {
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

  protected function getExtension($filename) {
    return mb_strtolower(pathinfo($filename, PATHINFO_EXTENSION));
  }
}
