<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/11/14
 * Time: 10:08
 */

class CompanyDataController extends AppController
{
  public $components = ['Landscape', 'Auth'];

  const PARAM_ACCESS_TOKEN = 'accessToken';
  const PARAM_IP_ADDRESS = 'ipAddress';
  const PARAM_LBC = 'lbc';

  public function beforeFilter() {
    $this->Auth->allow('getDetailInfo');
  }

  public function getDetailInfo() {
    Configure::write('debug', 0);
    $this->autoRender = false;
    $this->layout = "ajax";

    $jsonObj = $this->getRequestJSONData();
    try {
      $this->isValidAccessToken($jsonObj[self::PARAM_ACCESS_TOKEN]);
      $component = new LandscapeComponent();
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
}