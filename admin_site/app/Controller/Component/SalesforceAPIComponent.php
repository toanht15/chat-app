<?php

App::uses('HttpSocket', 'Network/Http', 'Component', 'Controller');

class SalesforceAPIComponent extends Component
{
  const API_URL = 'http://127.0.0.1/SalesForce/add';
  const API_CALL_TIMEOUT_SEC = 5;

  const PARAM_NAME_TITLE                  = 'name_title';
  const PARAM_LAST_NAME                   = 'last_name';
  const PARAM_FIRST_NAME                  = 'first_name';
  const PARAM_WEBSITE                     = 'website';
  const PARAM_COMPANY_NAME                = 'company_name';
  const PARAM_DEPARTMENT                  = 'department';
  const PARAM_POSITION                    = 'position';
  const PARAM_MAIL                        = 'mail';
  const PARAM_PHONE                       = 'phone';
  const PARAM_MOBILE                      = 'mobile';
  const PARAM_PAYER                       = 'payer';
  const PARAM_TRIAL_BEGIN_DATE            = 'trial_begin_date';
  const PARAM_TRIAL_END_DATE              = 'trial_end_date';
  const PARAM_RATE                        = 'rate';
  const PARAM_LEAD_ACQUISITION_CHANNEL    = 'lead_acquisition_channel';
  const PARAM_CUSTOMER_COLLECTION_CHANNEL = 'customer_collection_channel';
  const PARAM_INTRODUCER                  = 'introducer';
  const PARAM_DISTRIBUTION_CHANNEL        = 'distribution_channel';
  const PARAM_PRODUCT_TYPE                = 'product_type';
  const PARAM_USAGE                       = 'usage';
  const PARAM_SALE_STYLE                  = 'sale_style';
  const PARAM_MEMBER_SITE                 = 'member_site';
  const PARAM_TRANSACTION_TYPE            = 'transaction_type';
  const PARAM_BUSINESS_MODEL              = 'business_model';
  const PARAM_QUESTION                    = 'question';
  const PARAM_INVESTIGATION_MOTIVE        = 'investigation_motive';
  const PARAM_COUNTRY                     = 'country';
  const PARAM_POST_CODE                   = 'post_code';
  const PARAM_ADDRESS1                    = 'address1';
  const PARAM_ADDRESS2                    = 'address2';
  const PARAM_ADDRESS3                    = 'address3';
  const PARAM_TARGET                      = 'target';

  private $datum = array();

  public function __construct()
  {
    $this->initializeDatum();
  }

  public function set($param, $value) {
    if(!empty($value)){
      $this->datum[$param] = $value;
    }
  }

  public function execute() {
    foreach($this->datum as $key => $value) {
      if(empty($this->datum[$key])) {
        unset($this->datum[$key]);
      }
    }
    $socket = new HttpSocket(array(
      'timeout' => self::API_CALL_TIMEOUT_SEC
    ));
    $data = http_build_query($this->datum, '', '&', PHP_QUERY_RFC3986 );
    $socketResult = $socket->post(
      self::API_URL,
      $data,
      array('header' => array('X-Forwarded-Port' => 443))
    );
    $this->log('SalesforceAPIComponent::execute sendResult => '.$socketResult);
  }

  private function initializeDatum() {
    $this->datum = array(
      self::PARAM_NAME_TITLE                  => '',
      self::PARAM_LAST_NAME                   => '',
      self::PARAM_FIRST_NAME                  => '',
      self::PARAM_WEBSITE                     => '',
      self::PARAM_COMPANY_NAME                => '',
      self::PARAM_DEPARTMENT                  => '',
      self::PARAM_POSITION                    => '',
      self::PARAM_MAIL                        => '',
      self::PARAM_PHONE                       => '',
      self::PARAM_MOBILE                      => '',
      self::PARAM_PAYER                       => '',
      self::PARAM_TRIAL_BEGIN_DATE            => '',
      self::PARAM_TRIAL_END_DATE              => '',
      self::PARAM_RATE                        => '',
      self::PARAM_LEAD_ACQUISITION_CHANNEL    => '',
      self::PARAM_CUSTOMER_COLLECTION_CHANNEL => '',
      self::PARAM_INTRODUCER                  => '',
      self::PARAM_DISTRIBUTION_CHANNEL        => '',
      self::PARAM_PRODUCT_TYPE                => '',
      self::PARAM_USAGE                       => '',
      self::PARAM_SALE_STYLE                  => '',
      self::PARAM_MEMBER_SITE                 => '',
      self::PARAM_TRANSACTION_TYPE            => '',
      self::PARAM_BUSINESS_MODEL              => '',
      self::PARAM_QUESTION                    => '',
      self::PARAM_INVESTIGATION_MOTIVE        => '',
      self::PARAM_COUNTRY                     => '',
      self::PARAM_POST_CODE                   => '',
      self::PARAM_ADDRESS1                    => '',
      self::PARAM_ADDRESS2                    => '',
      self::PARAM_ADDRESS3                    => '',
      self::PARAM_TARGET                      => ''
    );
  }
}
