<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/02/22
 * Time: 15:57
 */

App::uses('HttpSocket', 'Network/Http', 'Component', 'Controller', 'Utility/Validation');

class LandscapeAPIComponent extends Component
{

  const STATUS_OK = '200';
  const STATUS_ERR = '401';

  protected $apiUrl;
  protected $parameter;
  protected $apiData;

  protected $header;

  public function __construct(ComponentCollection $collection, array $settings = array())
  {
    parent::__construct($collection, $settings);
    $this->apiUrl = "";
    $this->parameter = array();
    $this->header = array();
  }

  protected function callApi()
  {
    $socket = new HttpSocket(array(
      'timeout' => LandscapeLbcAPIComponent::API_CALL_TIMEOUT,
    ));
    $request = array(
      'header' => $this->header
    );
    $this->apiData = $socket->get($this->apiUrl, $this->parameter, $request);
    //$this->apiData = $socket->get('https://api.kantan-touroku.com/lbc_renkei/parse_info?cmd=parse&cid=kttRzcMcssGxq4znx2f&callback=callback_get_info&in_text=%E2%96%A1%E2%96%A0%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%96%A0%E2%96%A1%0A%E3%83%A1%E3%83%',null,$request);
  }

  protected function setHeader($param, $value) {
    $this->header[$param] = $value;
  }
}