<?php
/**
 * Created by PhpStorm.
 * User: masashi.shimizu
 * Date: 2018/07/11
 * Time: 17:50
 */
require APP.'/Vendor/vendor/autoload.php';
use Intervention\Image\ImageManager;

class ImageThumbnailCreatorComponent extends Component
{
  const TMP_DIR = '/tmp';
  const THUMBNAIL_PREFIX = 'thumb_';
  const MAX_WIDTH = 265; // ウィジェット：大の最大サイズ
  const MAX_HEIGHT = 285; // ウィジェット：大の最大サイズ

  private $scale;
  private $filename;
  private $file;

  private $manager;
  private $image;

  public function __construct()
  {
    $this->scale = 0.5; //default
  }

  public function setScale($scale)
  {
    if(0 > $scale && $scale > 1) {
      throw new InvalidArgumentException('引数は0より大きく、1未満の値を指定してください');
    }
    $this->scale = $scale;
  }

  public function setFilename($fileName) {
    $this->filename = $fileName;
  }

  public function setFileData($file) {
    $this->file = $file;
  }

  /**
   * @return array
   */
  public function create() {
    try {
      $this->validImageFile();
      $this->createImageManager();
      $this->resize();
    } catch(Exception $e) {
      $this->log('サムネイル生成中にエラーが発生しました', LOG_WARNING);
      throw $e;
    }
    return array(
      'path' => $this->getStoredFilePath(),
      'filename' => $this->getStoredFilename()
    );
  }

  protected function createImageManager() {
    $this->manager = new ImageManager(array('driver' => 'gd'));
  }

  protected function resize() {
    $imageSize = $this->getImagesize();
    $isWidthLargerThanHeight = $imageSize[0] >= $imageSize[1];
    if($isWidthLargerThanHeight) {
      $imageWidth = self::MAX_WIDTH > $imageSize[0] ? $imageSize[0] : self::MAX_WIDTH;
      $this->image = $this->manager->make($this->file['tmp_name'])->resize($imageWidth, NULL, function ($constraint) {
        $constraint->aspectRatio();
      });
    } else {
      $imageHeight = self::MAX_HEIGHT > $imageSize[1] ? $imageSize[1] : self::MAX_HEIGHT;

      $this->image = $this->manager->make($this->file['tmp_name'])->resize(NULL, $imageHeight, function ($constraint) {
        $constraint->aspectRatio();
      });
    }
    $this->image->save($this->getStoredFilePath());
  }

  private function validImageFile() {
    if(!$this->isImage()) {
      throw new InvalidArgumentException('指定のファイルはイメージファイルではありません');
    }
  }

  private function getImagesize() {
    return getImagesize($this->file['tmp_name']);
  }

  private function getStoredFilename() {
    return self::THUMBNAIL_PREFIX.$this->filename;
  }

  private function getStoredFilePath() {
    return self::TMP_DIR.DS.$this->getStoredFilename();
  }

  private function isImage()
  {
    $a = $this->getImagesize();
    $image_type = $a[2];

    if(in_array($image_type , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)))
    {
      return true;
    }
    return false;
  }

}