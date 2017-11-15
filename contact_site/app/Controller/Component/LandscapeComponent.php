<?php

App::uses('HttpSocket', 'Network/Http', 'Component', 'Controller', 'Utility/Validation');

/**
 * Class LandscapeComponent
 * @property \app\Model\MLandscapeData $MLandscapeData
 */
class LandscapeComponent extends Component
{
  const API_CALL_TIMEOUT = 5;
  const LANDSCAPE_DATA_EXPIRE_SEC = 60 * 60 * 24 * 90; // 90日

  const PATTERN_IP = "/^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}$/";
  const PATTERN_LBC_CODE = "/^[0-9]+$/";
  const LBC_CODE_LENGTH = 11;

  const LANDSCAPE_API_URL = "https://api.cladb.usonar.jp/lbcinfoex/getlbc";
//  const LANDSCAPE_API_URL = "http://127.0.0.1/testdata.json";
  const LANDSCAPE_API_KEY1 = "BN7WjEygVK32UqSV";
  const LANDSCAPE_API_KEY2 = "null";

  const PARAM_KEY1 = 'key1';
  const PARAM_KEY2 = 'key2';
  const PARAM_FORMAT = 'format';
  const PARAM_CHARSET = 'charset';
  const PARAM_IPADDRESS = 'ipadr';

  const STATUS_OK = '200';
  const STATUS_ERR = '401';

  private $format;
  private $charset;
  private $ip;
  private $lbcCode;
  private $parameter;

  private $dbData;
  private $apiData;

  private $defaultOutputData = array(
    'lbcCode' => '',
    'ipAddress' => '',
    'orgName' => '',
    'orgZipCode' => '',
    'orgAddress' => '',
    'orgTel' => '',
    'orgFax' => '',
    'orgIpoType' => '',
    'orgDate' => '',
    'orgCapitalCode' => '',
    'orgEmployeesCode' => '',
    'orgGrossCode' => '',
    'orgPresident' => '',
    'orgIndustrialCategoryM' => '',
    'orgUrl' => '',
    'houjinBangou' => '',
    'houjinAddress' => '',
    'updated' => ''
  );

  private $apiOutputKeyMap = array(
    'LBC' => 'lbcCode',
    'IP' => 'ipAddress',
    'HoujinBangou_3.OrgCode' => 'orgCode',
    'HoujinBangou_4.HoujinBangou' => 'houjinBangou',
    'HoujinBangou_5.HoujinName' => 'houjinName',
    'HoujinBangou_6.HoujinAddress' => 'houjinAddress',
  );

  private $apiDBKeyMap = array(
      'IP' => 'ip_address',
      'LBC' => 'lbc_code',
      'HoujinBangou_3.OrgCode' => 'org_code',
      'HoujinBangou_4.HoujinBangou' => 'houjin_bangou',
      'HoujinBangou_5.HoujinName' => 'houjin_name',
      'HoujinBangou_6.HoujinAddress' => 'houjin_address',
  );

  /**
   * LandscapeComponent constructor.
   * @param string $format
   * @param string $charset
   */
  public function __construct($format = 'json', $charset = 'utf8') {
    $this->format = $format;
    $this->charset = $charset;
    $this->setDefaultParameters();
  }

  public function getFrom($ip, $lbcCode) {
    $this->setParams($ip, $lbcCode);
    $this->executeFindData();
    return $this->convertDataToArray();
  }

  private function setParams($ip, $lbcCode) {
    if(!empty($ip)) {
      $this->validateIp($ip);
      $this->setIp($ip);
    }
    if(!empty($lbcCode)) {
      $this->validateLbcCode($lbcCode);
      $this->setLbcCode($lbcCode);
    }
  }

  private function executeFindData() {
    if(!empty($this->ip) && !empty($this->lbcCode)) {
      // LBCコードを優先して検索
      $this->getFromLbcCode();
    } else if(!empty($this->ip)) {
      $this->getFromIp();
    } else if(!empty($this->lbcCode)) {
      $this->getFromLbcCode();
    } else {
      // 必要なデータが来ていない
      throw new Exception('必要なパラメータが不足しています。', 400);
    }
  }

  private function getFromIp() {
    // まずはDBからデータ取得
    $this->findDataFromDbBy('ip_address');
    if($this->isExpiredDbData()) {
      $this->findDataFromAPI();
      $this->saveToTable();
      // DBの値は古いため、APIの値を返却するためdbDataは削除する
      $this->dbData = null;
    }
  }

  private function getFromLbcCode() {
    // まずはDBからデータ取得
    $this->findDataFromDbBy('lbc_code');
    if($this->isEmptyDbData()) {
      throw new Exception('データが存在しません。', 404);

    }
  }

  private function setDefaultParameters() {
    $this->parameter = array(
      self::PARAM_KEY1 => self::LANDSCAPE_API_KEY1,
      self::PARAM_KEY2 => self::LANDSCAPE_API_KEY2,
      self::PARAM_CHARSET => $this->charset,
      self::PARAM_FORMAT => $this->format,
      self::PARAM_IPADDRESS => ''
    );
  }

  private function validateIp($ip) {
    if(preg_match(self::PATTERN_IP, $ip) !== 1) {
      throw new InvalidArgumentException('IPアドレスの形式ではありません。 value: '.$ip);
    }
  }

  private function setIp($ip) {
    $this->ip = $ip;
    // パラメータにもセットする
    $this->parameter[self::PARAM_IPADDRESS] = $ip;
  }

  private function validateLbcCode($lbcCode) {
    /* FIXME !!!
    if(!Validation::maxLength($lbcCode, self::LBC_CODE_LENGTH)) {
      throw new InvalidArgumentException('定義されたLBCコードの長さではありません。 value: '.$lbcCode);
    }
    if(!Validation::numeric($lbcCode)) {
      throw new InvalidArgumentException('定義されたLBCコードに含んでいない文字があります。 value: '.$lbcCode);
    }
    */
  }

  private function setLbcCode($lbcCode) {
    $this->lbcCode = $lbcCode;
  }

  private function findDataFromDbBy($targetColumn) {
    $conditions = $this->createConditionsByColumn($targetColumn);
    $MLandscapeData = ClassRegistry::init('MLandscapeData');
    $this->dbData = $MLandscapeData->find('all', array(
        'fields' => '*',
        'conditions' => $conditions,
        'order' => 'updated desc'
    ));
    $baseRecord = [];
    foreach($this->dbData as $k => $record) {
      if(empty($baseRecord)) {
        $baseRecord = $record;
        continue;
      }
      $baseRecord['MLandscapeData']['ip_address'] .= ','.$record['MLandscapeData']['ip_address'];
    }
    $this->dbData = $baseRecord;
  }

  private function findDataFromAPI() {
    $socket = new HttpSocket(array(
        'timeout' => self::API_CALL_TIMEOUT
    ));
    $result = $socket->get(self::LANDSCAPE_API_URL, $this->parameter);
    $this->log('request param => '.var_export($this->parameter,TRUE), 'request');
    $this->apiData = json_decode($result->body(), TRUE);
    $this->log('response param => '.var_export($this->apiData,TRUE), 'response');
    if(empty($this->apiData) || strcmp($this->apiData['result_code'], self::STATUS_ERR) === 0) {
      throw new Exception('API呼び出し時にエラーを取得しました => body: '.$result->body(), 502);
    } else if (empty($this->apiData['lbc_code'])) {
      // データは無いが当該IPからアクセスされると何度もAPIをコールしてしまうためIPアドレスのみデータを入れる
      // throw new Exception('検索条件に該当するデータが存在しません => body: '.$result->body(), 404);
    }
  }

  private function isExpiredDbData() {
    if(!empty($this->dbData)) {
      $updatedDatetime = $this->dbData['MLandscapeData']['updated'];
      $now = $this->getNowTimestamp();
      $updatedTimestamp = strtotime($updatedDatetime);
      $this->printDebugLog('now : '.$now.' updatedTimestamp : '.$updatedTimestamp);
      return $now - $updatedTimestamp > self::LANDSCAPE_DATA_EXPIRE_SEC;
    } else {
      // データが無いため有効期限切れとする
      return true;
    }
  }

  private function isEmptyDbData() {
    return empty($this->dbData);
  }

  private function getNowTimestamp() {
    return time();
  }


  private function saveToTable() {
    try {
      if (!empty($this->apiData)) {
        $this->apiData['updated'] = date('Y-m-d H:i:s');
        $MLandscapeData = ClassRegistry::init('MLandscapeData');
        $data = $this->convertAllKeyToUnderscore($this->apiData);
        if ($this->isEmptyDbData()) { //事前にDB情報を参照していない or データが無かった => 新規追加
          $MLandscapeData->create();
          $MLandscapeData->save($data);
        } else {
          // 更新 CakePHP 2.xでは複合プライマリキーに対応していないため自前で用意
          $db = $MLandscapeData->getDataSource();
          $columns = array_keys($MLandscapeData->getColumnTypes());
          $query = '';
          $query .= 'UPDATE m_landscape_data set ';
          foreach ($data as $k => $v) {
            if (in_array($k, $columns)) {
              $query .= '`' . $k . '` = "' . $v . '",';
            }
          }
          $query = rtrim($query, ',');
          $query .= ' WHERE `lbc_code`= "' . $data['lbc_code'] . '" AND `ip_address` = "' . $data['ip_address'] . '"';
          $MLandscapeData->query($query);
        }
      }
    } catch(Exception $e) {
      throw new Exception('DBセーブ時にエラーが発生しました。 message : '.$e->getMessage(), 500);
    }
  }

  private function printDebugLog($msg) {
    $this->log('LandscapeComponent::DEBUG '.$msg, LOG_DEBUG);
  }

  private function convertDataToArray() {
    if(!empty($this->dbData)) {
      return $this->convertAllKeyToCamelcase($this->dbData['MLandscapeData']);
    }
    if(!empty($this->apiData)) {
      return $this->createOutputDataFromAPIData();
    }
  }

  private function createOutputDataFromAPIData()
  {
    $val = [];
    if (!empty($this->apiData)) {
      $convertedData = $this->convertAllKeyToCamelcase($this->apiData);
      // 必要なデータのみ抽出する
      foreach($this->defaultOutputData as $k => $v) {
        $val[$k] = $convertedData[$k];
      }
    }
    return $val;
  }

  private function convertAllKeyToCamelcase($assoc) {
    $val = [];
    foreach($assoc as $k => $v) {
      //HoujinBangou_3.OrgCode, HoujinBangou_4.HoujinBangou, HoujinBangou_5.HoujinName, HoujinBangou_6.HoujinAddressは別名をkeyとする
      //@see $this->apiOutputKeyMap
      if(array_key_exists($k, $this->apiOutputKeyMap)) {
        $val[$this->apiOutputKeyMap[$k]] = $v;
      } else {
        $val[$this->convertUnderscoreToCamelCase($k)] = $v;
      }
    }
    return $val;
  }

  private function convertUnderscoreToCamelCase($str) {
    return Inflector::variable($str);
  }

  private function convertAllKeyToUnderscore($assoc) {
    $val = [];
    foreach($assoc as $k => $v) {
      if(array_key_exists($k, $this->apiOutputKeyMap)) {
        $val[$this->apiDBKeyMap[$k]] = $v;
      } else {
        $val[$this->convertCamelCaseToUnderscore($k)] = $v;
      }
    }
    return $val;
  }

  private function convertCamelCaseToUnderscore($str) {
    return Inflector::underscore($str);
  }

  /**
   * @param $targetColumn
   * @return array
   */
  private function createConditionsByColumn($targetColumn)
  {
    $conditions = [];
    switch ($targetColumn) {
      case 'ip_address':
        $conditions = array('ip_address' => $this->ip);
        break;
      case 'lbc_code':
        $conditions = array('lbc_code' => $this->lbcCode);
        break;
    }
    return $conditions;
  }
}