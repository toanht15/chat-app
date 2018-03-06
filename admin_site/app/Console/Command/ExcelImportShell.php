<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/03/06
 * Time: 14:47
 */

// // エクセル出力用ライブラリ
App::import('Vendor', 'PHPExcel', array('file' => 'phpoffice/phpexcel/Classes' . DS . 'PHPExcel.php'));
App::import('Vendor', 'PHPExcel_IOFactory', array('file'=>'phpoffice/phpexcel/Classes'. DS .'PHPExcel'. DS .'IOFactory.php'));
App::import('Vendor', 'PHPExcel_Cell_AdvancedValueBinder', array('file'=>'phpoffice/phpexcel/Classes'. DS .'PHPExcel'. DS .'Cell'. DS .'AdvancedValueBinder.php'));

// Excel95用ライブラリ
App::import('Vendor', 'PHPExcel_Writer_Excel5', array('file'=>'phpoffice/phpexcel/Classes' . DS . 'PHPExcel' . DS . 'Writer' . DS . 'Excel5.php'));
App::import('Vendor', 'PHPExcel_Reader_Excel5', array('file'=>'phpoffice/phpexcel/Classes' . DS . 'PHPExcel' . DS . 'Reader' . DS . 'Excel5.php'));

// Excel2007用ライブラリ
App::import('Vendor', 'PHPExcel_Writer_Excel2007', array('file'=>'phpoffice/phpexcel/Classes' . DS . 'PHPExcel' . DS . 'Writer' . DS . 'Excel2007.php'));
App::import('Vendor', 'PHPExcel_Reader_Excel2007', array('file'=>'phpoffice/phpexcel/Classes' . DS . 'PHPExcel' . DS . 'Reader' . DS . 'Excel2007.php'));

App::uses('ContractController', 'Controller');

class ExcelImportShell extends AppShell
{
  const EXCEL_FILE_PATH = '/var/www/sinclo/admin_site/app/Console/import.xlsx';

  const ROW_CNAME = 'B';
  const ROW_TRIAL_FLG = 'C';
  const ROW_PLAN = 'D';
  const ROW_ADD_REF_COMPANY_DATA = 'E';
  const ROW_ADD_CHATBOT_SCENARIO = 'F';
  const ROW_LIMIT_USERS = 'G';
  const ROW_START_DATE = 'H';
  const ROW_END_DATE = 'I';
  const ROW_ADMIN_NAME = 'J';
  const ROW_DISPLAY_NAME = 'K';
  const ROW_MAIL_ADDRESS = 'L';
  const ROW_PASSWORD = 'M';
  const ROW_INIT_PASSWORD_FLG = 'N';
  const ROW_TAG = 'O';
  const ROW_USER_NUM = 'P';

  protected $objReader;
  protected $phpExcel;
  protected $currentSheet;
  protected $dataArray;

  private $planNameIdMap = array(
    'プレミアムプラン' => 1,
    'チャットスタンダードプラン' => 2,
    'シェアリングプラン' => 3,
    'チャットベーシックプラン' => 4
  );

  public function importAll() {
    try {
      $this->printLog("BEGIN ExcelCompanyImport");
      $this->readExcel();
      $this->setActiveSheet(0);
      $target = $this->getArrayData();
      $beginImport = false;
      foreach($target as $index => $row) {
        if(empty($row[self::ROW_CNAME]) || preg_match('/^[0-9]+$/', $row[self::ROW_CNAME])) {
          if($beginImport) {
            break;
          }
          continue;
        } else if(strcmp(trim($row[self::ROW_CNAME]), "sinlcoアカウント名") === 0 || strcmp(trim($row[self::ROW_CNAME]), "sincloアカウント名") === 0) {
          $beginImport = true;
          continue;
        }
        $companyInfo = $this->createMCompanyData($row);
        $userInfo = $this->createUserData($row);
        $agreementInfo = $this->createAgreementData($row);
        $controller = new ContractController();
        $this->printLog('ADD_COMPANY_INFO   ==================');
        $this->printLog(var_export($companyInfo, TRUE));
        $this->printLog('ADD_USER_INFO      ==================');
        $this->printLog(var_export($userInfo, TRUE));
        $this->printLog('ADD_AGREEMENT_INFO ==================');
        $this->printLog(var_export($agreementInfo, TRUE));
        $this->printLog('=====================================');
        $controller->processTransaction($companyInfo, $userInfo, $agreementInfo);
        $this->printLog('RESULT: OK');
        $this->printLog('=====================================');
      }
      $this->printLog("END   ExcelCompanyImport");
    } catch(Exception $e) {
      $this->printLog('RESULT: NG!!!!');
      $this->printLog('=====================================');
    }
  }

  private function readExcel() {
    $this->objReader = PHPExcel_IOFactory::createReader("Excel2007");
    $this->phpExcel = $this->objReader->load(self::EXCEL_FILE_PATH);
  }

  private function setActiveSheet($index) {
    $this->phpExcel->setActiveSheetIndex($index);
    $this->currentSheet = $this->phpExcel->getActiveSheet();
    $this->dataArray = $this->currentSheet->toArray(null, true, true, true);
  }

  private function getArrayData() {
    $arr = [];
    if(!is_null($this->dataArray)) {
      $arr = $this->dataArray;
    }
    return $arr;
  }

  private function printLog($msg) {
    $this->log($msg, LOG_INFO, 'import');
  }

  private function createMCompanyData($row) {
    $data = array(
      'company_name' => $row[self::ROW_CNAME],
      'm_contact_types_id' => $this->planNameIdMap[$row[self::ROW_PLAN]],
      'limit_users' => $row[self::ROW_LIMIT_USERS],
      'trial_flg' => $row[self::ROW_TRIAL_FLG] ? 1 : 0,
      'options' => array()
    );
    if(!empty($row[self::ROW_ADD_REF_COMPANY_DATA])) {
      $data['options']['refCompanyData'] = true;
    }
    if(!empty($row[self::ROW_ADD_CHATBOT_SCENARIO])) {
      $data['options']['chatbotScenario'] = true;
    }

    return $data;
  }

  private function createUserData($row) {
    $data = array(
      'user_name' => $row[self::ROW_ADMIN_NAME],
      'user_display_name' => $row[self::ROW_DISPLAY_NAME],
      'user_mail_address' => $row[self::ROW_MAIL_ADDRESS],
      'no_change_password_flg' => $row[self::ROW_INIT_PASSWORD_FLG] ? 0 : 1,
      'user_password' => $row[self::ROW_PASSWORD]
    );

    return $data;
  }

  private function createAgreementData($row) {
    $data = array(
      'business_model' => 1,
      'application_day' => date("Y-m-d"),
    );
    $this->printLog("開始日：".$row[self::ROW_START_DATE]." 終了日：".$row[self::ROW_END_DATE]);
    // mm-dd-yyの形になってしまっているので整形する
    $startDate = str_replace('-','/',$row[self::ROW_START_DATE]);
    $endDate = str_replace('-','/',$row[self::ROW_END_DATE]);
    if(!empty($row[self::ROW_TRIAL_FLG])) {
      $data['trial_start_day'] = date("Y-m-d", strtotime($startDate));
      $data['trial_end_day'] = date("Y-m-d", strtotime($endDate));
    } else {
      $data['agreement_start_day'] = date("Y-m-d", strtotime($startDate));
      $data['agreement_end_day'] = date("Y-m-d", strtotime($endDate));
    }
    return $data;
  }
}