<?php
App::uses('AppModel', 'Model');
/**
 * THistory Model
 *
 * @property MCustomers $MCompanies
 */
class MCustomer extends AppModel {

  public $name = "MCustomer";

  public function getCustomerInfoForVisitorId($companyId, $vId){
    $ret = [];
    $params = [
      'fields' => [
        '*',
      ],
      'conditions' => [
        'MCustomer.m_companies_id' => $companyId,
        'MCustomer.visitors_id' => $vId
      ]
    ];
    $ret = $this->find('first', $params);

    if ( isset($ret['MCustomer']['informations']) ) {
      $ret['informations'] = (array)json_decode($ret['MCustomer']['informations']);
    }

    return $ret;
  }

}
