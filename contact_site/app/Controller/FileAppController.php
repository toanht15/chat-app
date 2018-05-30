<?php
/**
 * FileAppController controller.
 * S3のファイル管理用コントローラー
 * @property TUploadTransferFile $TUploadTransferFile
 * @property TChatbotScenarioSendFile $TChatbotScenarioSendFile
 */

class FileAppController extends AppController
{

  const ENCRYPT_SECRET_KEY = "FoW3wLKXUB4HiCDC0fpIb63E066L03R57f3r6wwk3VyxTp3AGp68lBb7bghmRU80";
  const ENCRYPT_PARAM_DELIMITER = "@@";
  const EXPIRE_SEC = 3600; // ここを変更する場合はS3のライフサイクル設定も見直すこと。

  const PARAM_FILE = "file";
  const PARAM_TARGET_USER_ID = "targetUserId";
  const PARAM_PARAM = "param";

  public $uses = ['TUploadTransferFile', 'TChatbotScenarioSendFile'];
  public $components = ['Amazon'];

  // デフォルト設定
  protected $fileTransferPrefix = 'fileTransfer/';

  protected $scenarioMode = false;

  public function beforeFilter() {
    parent::beforeFilter();
  }

  /**
   * 保存時のファイル名の生成
   * @param  Object $file ファイルオブジェクト
   * @return String       ファイル名
   */
  protected function getFilenameForSave($file) {
    return $this->userInfo['MCompany']['company_key']."-".date("YmdHis").".".$this->getExtension($file['name']);
  }

  /**
   * ファイルアップロード
   * @param  Object $file         ファイルオブジェクト
   * @param  String $saveFileName ファイル名
   * @return String               保存先URL
   */
  protected function putFile($file, $saveFileName) {
    return $this->Amazon->putObject($this->getSaveKey($saveFileName), $file['tmp_name']);
  }

  /**
   * ファイル削除
   * @param  String $file 保存先パス + ファイル名
   * @return Void
   */
  protected function removeFile($file) {
    return $this->Amazon->removeObject($file);
  }

  /**
   * ファイル取得
   * @param  String $key 保存先パス + ファイル名
   * @return Object      ファイル情報
   */
  protected function getFile($key) {
    try {
      $file = $this->Amazon->getObject($key);
      if(empty($file)) {
        $this->log('Aws::getObject時のデータが空です。 file : '.$key, LOG_WARNING);
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

    return $file;
  }

  /**
   * S3保存時の相対パス取得
   * @param  String $saveFileName 保存ファイル名
   * @return String               ファイル名を含む相対パス
   */
  protected function getSaveKey($saveFileName) {
    if($this->scenarioMode) {
      return $saveFileName;
    } else {
      return $this->fileTransferPrefix.$saveFileName;
    }
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

  protected function createDownloadUrl($created, $fileId, $isScenarioDownload = false) {
    return C_NODE_SERVER_ADDR.'/File/download?param='.$this->encryptParameterForDownload($created, $fileId, $isScenarioDownload);
  }

  protected function setScenarioMode($isScenarioMode) {
    $this->scenarioMode = $isScenarioMode;
  }

  private function encryptParameterForDownload($created, $fileId, $isScenarioDownload) {
    // ※結合順序注意！！
    $param = $created.self::ENCRYPT_PARAM_DELIMITER.$fileId.self::ENCRYPT_PARAM_DELIMITER.$isScenarioDownload;
    $encrypted = Security::encrypt($param, self::ENCRYPT_SECRET_KEY);
    return rawurlencode($this->urlSafeBase64Encode($encrypted));
  }

  protected function decryptParameterForDownload($val) {
    $decrypted = Security::decrypt($this->urlSafeBase64Decode(rawurldecode($val)), self::ENCRYPT_SECRET_KEY);
    if(empty($decrypted)) {
      throw new Exception('復号失敗 : '.$val);
    }
    $explodeVal = explode(self::ENCRYPT_PARAM_DELIMITER, $decrypted);
    $this->scenarioMode = !empty($explodeVal[2]) ? (bool)$explodeVal[2] : false;
    if($this->scenarioMode) {
      $this->fileTransferPrefix = 'fileScenarioTransfer/';
    }
    return array(
      'created' => $explodeVal[0],
      'fileId' => $explodeVal[1]
    );
  }

  protected function getExtension($filename) {
    return mb_strtolower(pathinfo($filename, PATHINFO_EXTENSION));
  }

  protected function urlSafeBase64Encode($str) {
    $val = base64_encode($str);
    return str_replace(array('+', '/', '='), array('_', '-', '.'), $val);
  }

  protected function urlSafeBase64Decode($str) {
    $val = str_replace(array('_','-', '.'), array('+', '/', '='), $str);
    return base64_decode($val);
  }
}
