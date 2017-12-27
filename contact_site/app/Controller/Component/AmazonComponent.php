<?php
require ROOT. DS . APP_DIR . DS .'Plugin/AmazonSDK/vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Credentials\Credentials;

App::uses('Component', 'Controller');
class AmazonComponent extends Component {

  /**
   * S3へのファイルアップロード
   * @params $key string 保存先パス + ファイル名
   * @params $file string 実ファイルのパス
   * @return string 保存先
   **/
  function putObject($key, $file){
    try {
      $credentials = new Credentials(C_AWS_S3_KEY, C_AWS_S3_SECURITY);
      $aws = S3Client::factory([
        'version'    => C_AWS_S3_VERSION,
        'credentials' => $credentials,
        'region' => C_AWS_S3_REGION,
      ]);
      $result = $aws->putObject([
        'Bucket' => C_AWS_S3_BUCKET,
        'Key'    => $key,
        'SourceFile'   => $file,
        'StorageClass' => C_AWS_S3_STORAGE,
      ]);

      return $result['ObjectURL'];
    } catch (Exception $e) {
      echo $e->getMessage();
      return "";
    }
  }

  /**
   * S3のファイル削除
   * @params $file string 保存先パス + ファイル名
   * @return void
   **/
  function removeObject($file){
    try {
      $credentials = new Credentials(C_AWS_S3_KEY, C_AWS_S3_SECURITY);
      $aws = S3Client::factory([
        'version'    => C_AWS_S3_VERSION,
        'credentials' => $credentials,
        'region' => C_AWS_S3_REGION,
      ]);
      $result = $aws->deleteObject([
        'Bucket' => C_AWS_S3_BUCKET,
        'Key'    => $file
      ]);
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }

  function getObject($key) {
    $credentials = new Credentials(C_AWS_S3_KEY, C_AWS_S3_SECURITY);
    $aws = S3Client::factory([
        'version'    => C_AWS_S3_VERSION,
        'credentials' => $credentials,
        'region' => C_AWS_S3_REGION,
    ]);
    return $aws->getObject(array(
        'Bucket' => C_AWS_S3_BUCKET,
        'Key'    => $key
    ));
  }
}


