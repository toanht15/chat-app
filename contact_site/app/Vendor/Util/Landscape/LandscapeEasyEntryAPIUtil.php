<?php

class LandscapeEasyEntryAPIUtil {
  private $allKeyLabelMap = array(
    "lbc_office_id"     => "",
    "lbc_head_office_id"=> "",
    "pref_code"         => "都道府県コード",
    "city_code"         => "市区町村コード",
    "addr"              => "住所",
    "cname"             => "企業名",
    "oname"             => "事業所名",
    "pname"             => "姓名",
    "pname_kana"        => "姓名カナ",
    "pname_kana2"       => "姓名かな",
    "busho"             => "部署名",
    "yakushoku"         => "役職名",
    "zip"               => "郵便番号",
    "tel"               => "電話番号",
    "fax"               => "FAX番号",
    "ktai"              => "携帯番号",
    "chokutsu"          => "直通番号",
    "daihyo"            => "代表番号",
    "mail"              => "メールアドレス",
    "url"               => "URL",
    "extra"             => "その他",
    "unknown"           => "その他",
    "org_addr"          => "住所",
    "org_zip"           => "郵便番号",
    "exist_cname"       => "企業名マスタ存在",
    "exist_addr"        => "住所マスタ存在",
    "exist_zip"         => "郵便番号マスタ存在",
    "match_pref_add"    => "都道府県・住所一致",
    "match_pref_zip"    => "都道府県・郵便番号一致",
    "match_pref_tel"    => "都道府県・電話番号一致"
  );

  private $selectableLabelMap = array(
    "addr"              => "住所",
    "cname"             => "企業名",
    "oname"             => "事業所名",
    "pname"             => "姓名",
    "pname_kana"        => "姓名カナ",
    "pname_kana2"       => "姓名かな",
    "busho"             => "部署名",
    "yakushoku"         => "役職名",
    "zip"               => "郵便番号",
    "tel"               => "電話番号",
    "fax"               => "FAX番号",
    "ktai"              => "携帯番号",
    "chokutsu"          => "直通番号",
    "daihyo"            => "代表番号",
    "mail"              => "メールアドレス",
    "url"               => "URL",
    "extra"             => "その他"
  );

  public function getLabelMap() {
    return $this->selectableLabelMap;
  }


}