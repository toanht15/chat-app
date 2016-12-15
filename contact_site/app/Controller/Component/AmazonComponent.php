<?php
require APP_DIR . DS .'Plugin/autoload.php';
use Aws\S3\S3Client;
use Aws\Credentials\Credentials;

App::uses('Component', 'Controller');
class AmazonComponent extends Component {

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
      return "";
    }
  }
}


