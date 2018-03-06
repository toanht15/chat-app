<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/02/22
 * Time: 15:57
 */

App::uses('LandscapeAPIComponent', 'Controller/Component');

class LandscapeEasyEntryAPIComponent extends LandscapeAPIComponent
{
  const ML_CID = "kttRzcMcssGxq4znx2f";

  const API_URL = "https://api.kantan-touroku.com/lbc_renkei/parse_info";
  const HEADER_HOST = "https://sinclo.jp";

  const PARAM_CMD = "cmd";
  const PARAM_CID = "cid";
  const PARAM_IN_TEXT = "in_text";
  const PARAM_CALLBACK = "callback";

  const RESPONSE_CODE_SUCCESS = 0;
  const RESPONSE_CODE_ACCESS_OVER = 1;
  const RESPONSE_CODE_IN_TEXT_OVER = 2;
  const RESPONSE_INVALID_ACCESS = 9;
  const RESPONSE_SYSTEM_ERROR = 99;

  protected $inText;

  public function __construct(ComponentCollection $collection, array $settings = array())
  {
    parent::__construct($collection, $settings);
    //$this->apiUrl = 'https://api.kantan-touroku.com/lbc_renkei/parse_info?cmd=parse&cid=kttRzcMcssGxq4znx2f&in_text=%E2%96%A1%E2%96%A0%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%96%A0%E2%96%A1%0A%E3%83%A1%E3%83%87%E3%82%A3%E3%82%A2%E3%83%AA%E3%83%B3%E3%82%AF%E6%A0%AA%E5%BC%8F%E4%BC%9A%E7%A4%BE%0A%E3%82%A2%E3%83%AC%E3%83%83%E3%82%AF%E3%82%B9%E3%83%BB%E3%83%A1%E3%83%AB%E3%82%B7%E3%82%A8%EF%BC%88Alexandre%20Mercier%EF%BC%89%0A%E3%80%92108-0014%20%E6%9D%B1%E4%BA%AC%E9%83%BD%E6%B8%AF%E5%8C%BA%E8%8A%9D5-31-17%E3%80%80PMO%E7%94%B0%E7%94%BA5F%0ATEL%EF%BC%9A03-3455-2700%EF%BC%8FFAX%EF%BC%9A03-3455-2708%0AE-MAIL%EF%BC%9Aalexandre.mercier%40medialink-ml.co.jp%0AURL%EF%BC%9Ahttps%3A%2F%2Fwww.medialink-ml.co.jp%2F%0A%E2%96%A1%E2%96%A0%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%94%81%E2%96%A0%E2%96%A1&t=1519883195431';
    $this->apiUrl = self::API_URL;
  }

  /**
   * 解析させたい文字列をセットする
   * @param {string} $text
   */
  public function setText($text) {
    $this->inText = $text;
  }

  public function execute() {
    $this->setParameter();
    $this->setHeader('Origin', self::HEADER_HOST);
    $this->setHeader('Referer', self::HEADER_HOST.'/kantanApiTest.html');
    $this->callApi();
    $this->log('request param => ' . var_export($this->parameter, TRUE), 'mail-request');
    $this->log('response param => ' . var_export($this->apiData, TRUE), 'mail-response');
}

  /**
   * 以下の状態でデータが格納されているため、不要な文字列を排除してデータを取得する
   * <code>
   * callback_get_info({
   *  "lbc_office_id":"",
   *  "lbc_head_office_id":"",
   *  "pref_code":"",
   *  "city_code":"",
   *  "addr":[],
   *  "cname":"",
   *  "oname":"",
   *  "pname":[],
   *  "pname_kana":[],
   *  "pname_kana2":[],
   *  "busho":"",
   *  "yakushoku":"",
   *  " zip":[],
   *  "tel":[],
   *  "fax":[],
   *  "ktai":[],
   *  "chokutsu":[],
   *  "daihyo":[],
   *  "mail":"",
   *  "url":"https://contact.sinclo.local/ScriptSettings/testpage",
   *  "extra":[],
   *  "unknown":[],
   *  "org_addr":[],
   *  "org_zip":[],
   *  "exist_cname":"",
   *  "exist_addr":"",
   *  "exist_zip":"",
   *  "match_pref_addr":"",
   *  "match_pref_zip":"",
   *  "match_pref_tel":"",
   *  "result_code":0
   * })
   * </code>
   * @return mixed
   */
  public function getData() {
    $matches = array();
    preg_match('/^callback_get_info\((.*)?\)$/', $this->apiData->body, $matches);
    return json_decode($matches[1], TRUE);
  }

  private function setParameter() {
    $this->parameter = array(
      self::PARAM_CMD => "parse", // 固定
      self::PARAM_CID => self::ML_CID,
      self::PARAM_CALLBACK => 'callback_get_info',
      self::PARAM_IN_TEXT => $this->inText
    );
  }
}