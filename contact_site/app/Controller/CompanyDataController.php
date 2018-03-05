<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/11/14
 * Time: 10:08
 */

class CompanyDataController extends AppController
{
  public $components = ['LandscapeLbcAPI', 'LandscapeEasyEntryAPI', 'Auth'];

  const PARAM_ACCESS_TOKEN = 'accessToken';
  const PARAM_IP_ADDRESS = 'ipAddress';
  const PARAM_LBC = 'lbc';
  const PARAM_TARGET_TEXT = 'targetText';

  public function beforeFilter() {
    $this->Auth->allow('getDetailInfo', 'parseSignature');
  }

  public function getDetailInfo() {
    Configure::write('debug', 0);
    $this->autoRender = false;
    $this->layout = "ajax";

    $jsonObj = $this->getRequestJSONData();
    try {
      $this->isValidAccessToken($jsonObj[self::PARAM_ACCESS_TOKEN]);
      $component = new LandscapeLbcAPIComponent();
      $ipAddress = !empty($jsonObj[self::PARAM_IP_ADDRESS]) ? $jsonObj[self::PARAM_IP_ADDRESS] : null;
      $lbcCode = !empty($jsonObj[self::PARAM_LBC]) ? $jsonObj[self::PARAM_LBC] : null;
      $result = $component->getFrom($ipAddress, $lbcCode);
    } catch(Exception $e) {
      $this->log('getDetailInfo呼び出し時にエラーが発生しました。 エラーメッセージ: '.$e->getMessage().' エラー番号 '.$e->getCode(), 'api-error');
      $this->response->statusCode($e->getCode());
      return json_encode(array(
          'success' => false,
          'errorCode' => $e->getCode(),
          'data' => []
      ));
    }
    $this->response->statusCode(200);
    if(isset($jsonObj['format']) && strcmp($jsonObj['format'], 'popupElement') === 0) {
      $this->set('data', $result);
      $this->render('/Elements/Customers/companyDetailInfoView');
    } else {
      return json_encode(array(
          'success' => true,
          'data' => $result
      ));
    }
  }

  /**
   * {
        "accessToken":"x64rGrNWCHVJMNQ6P4wQyNYjW9him3ZK",
        "targetText":"□■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■□\\nメディアリンク株式会社\\nアレックス・メルシエ（Alexandre Mercier）\\n〒108-0014 東京都港区芝5-31-17　PMO田町5F\\nTEL：03-3455-2700／FAX：03-3455-2708\\nE-MAIL：alexandre.mercier@medialink-ml.co.jp\\nURL：https://www.medialink-ml.co.jp/\\n□■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■□"
     }
   * @return string
   */
  public function parseSignature() {
    Configure::write('debug', 0);
    $this->autoRender = false;
    $this->layout = "ajax";

    $jsonObj = $this->getRequestJSONData();
    try {
      $this->isValidAccessToken($jsonObj[self::PARAM_ACCESS_TOKEN]);
      $component = new LandscapeEasyEntryAPIComponent();
      $targetText = !empty($jsonObj[self::PARAM_TARGET_TEXT]) ? $jsonObj[self::PARAM_TARGET_TEXT] : null;
      $component->setText($targetText);
      $component->execute();
      $this->log('HOGE!! : '.var_export($component->getData(),TRUE), 'response');
    } catch(Exception $e) {
      $this->log('getDetailInfo呼び出し時にエラーが発生しました。 エラーメッセージ: '.$e->getMessage().' エラー番号 '.$e->getCode(), 'api-error');
      $this->response->statusCode($e->getCode());
      return json_encode(array(
        'success' => false,
        'errorCode' => $e->getCode(),
        'data' => []
      ));
    }

    $this->response->statusCode(200);
    return json_encode(array(
      'success' => true,
      'data' => $component->getData()
    ));
  }
}