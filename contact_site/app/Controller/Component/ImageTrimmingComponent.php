<?php
require APP.'/Vendor/vendor/autoload.php';
use Intervention\Image\ImageManager;

class ImageTrimmingComponent extends Component {

  protected $fileData;
  protected $savePath;

  protected $x;
  protected $y;
  protected $width;
  protected $height;


  protected $manager;
  protected $image;

  public function __construct()
  {
    $this->createImageManager();
  }

  public function setX($x) {
    $this->x = $x;
  }

  public function setY($y) {
    $this->y = $y;
  }

  public function setWidth($width) {
    $this->width = $width;
  }

  public function setHeight($height) {
    $this->height = $height;
  }

  public function setFileData($fileData) {
    $this->fileData = $fileData;
  }

  public function setSavePath($savePath) {
    $this->savePath = $savePath;
  }

  public function save() {
    $this->crop();
    if(!empty($this->image)) {
      $this->image->save($this->savePath);
    }
  }

  protected function createImageManager() {
    $this->manager = new ImageManager(array('driver' => 'gd'));
  }

  protected function crop() {
    $this->image = $this->manager->make($this->fileData['tmp_name'])->crop($this->width, $this->height, $this->x, $this->y);
  }
}