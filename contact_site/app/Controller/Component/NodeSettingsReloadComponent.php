<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/04/13
 * Time: 12:38
 */

App::uses('HttpSocket', 'Network/Http', 'Component');
class NodeSettingsReloadComponent extends Component
{
  const WIDGET_SETTINGS_API = 'http://127.0.0.1:8080/settings/reload/widgetSettings';
  const AUTO_MESSAGE_SETTINGS_API = 'http://127.0.0.1:8080/settings/reload/autoMessages';
  const OPERATION_HOUR_SETTINGS_API = 'http://127.0.0.1:8080/settings/reload/operationHour';

  public static function reloadWidgetSettings($companyKey) {
    self::callApi(self::WIDGET_SETTINGS_API, $companyKey);
  }

  public static function reloadAutoMessages($companyKey) {
    self::callApi(self::AUTO_MESSAGE_SETTINGS_API, $companyKey);
  }

  public static function reloadOperationHour($companyKey) {
    self::callApi(self::OPERATION_HOUR_SETTINGS_API, $companyKey);
  }

  private static function callApi($url, $siteKey) {
    $socket = new HttpSocket(array(
      'timeout' => 3
    ));
    $socket->post($url,array('sitekey' => $siteKey));
  }
}