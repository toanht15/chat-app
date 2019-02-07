<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/01/15
 * Time: 13:44
 */

// // エクセル出力用ライブラリ
App::import('Vendor', 'PHPExcel', array('file' => 'vendor/phpoffice/phpexcel/Classes' . DS . 'PHPExcel.php'));
App::import('Vendor', 'PHPExcel_IOFactory', array('file'=>'vendor/phpoffice/phpexcel/Classes'. DS .'PHPExcel'. DS .'IOFactory.php'));
App::import('Vendor', 'PHPExcel_Cell_AdvancedValueBinder', array('file'=>'vendor/phpoffice/phpexcel/Classes'. DS .'PHPExcel'. DS .'Cell'. DS .'AdvancedValueBinder.php'));

// Excel95用ライブラリ
App::import('Vendor', 'PHPExcel_Writer_Excel5', array('file'=>'vendor/phpoffice/phpexcel/Classes' . DS . 'PHPExcel' . DS . 'Writer' . DS . 'Excel5.php'));
App::import('Vendor', 'PHPExcel_Reader_Excel5', array('file'=>'vendor/phpoffice/phpexcel/Classes' . DS . 'PHPExcel' . DS . 'Reader' . DS . 'Excel5.php'));

// Excel2007用ライブラリ
App::import('Vendor', 'PHPExcel_Writer_Excel2007', array('file'=>'vendor/phpoffice/phpexcel/Classes' . DS . 'PHPExcel' . DS . 'Writer' . DS . 'Excel2007.php'));
App::import('Vendor', 'PHPExcel_Reader_Excel2007', array('file'=>'vendor/phpoffice/phpexcel/Classes' . DS . 'PHPExcel' . DS . 'Reader' . DS . 'Excel2007.php'));

class ExcelParserComponent extends Component {
  protected $filePath;
  protected $objReader;
  protected $phpExcel;
  protected $currentSheet;
  protected $dataArray;

  public function __construct($file) {
    $this->filePath = $file;
  }

  public function readData($readDataOnly = false) {
    $this->objRender = PHPExcel_IOFactory::createReader("Excel2007");
    $this->objRender->setReadDataOnly($readDataOnly);
    $this->phpExcel = $this->objRender->load($this->filePath);
  }

  public function setActiveSheet($index) {
    $this->phpExcel->setActiveSheetIndex($index);
    $this->currentSheet = $this->phpExcel->getActiveSheet();
    $this->dataArray = $this->currentSheet->toArray(null, true, true, true);
  }

  public function getArrayData() {
    $arr = [];
    if(!is_null($this->dataArray)) {
      $arr = $this->dataArray;
    }
    return $arr;
  }

  public function exportData()
  {
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.date('YmdHis').'-auto-message.xlsx"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($this->phpExcel, 'Excel2007');
    $objWriter->save('php://output');
  }
}
