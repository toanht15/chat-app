<?php
App::uses('AppModel', 'Model');
/**
 * MWidgetSetting Model
 *
 * @property MCompanies $MCompanies
 */
class MWidgetSetting extends AppModel {

  public $name = "MWidgetSetting";

    //アソシエーション
  public $belongsTo = ['MCompany' =>
    ['className' => 'M_company',
      'conditions' => '',
      'order' => '',
      'dependent' => true,
      'foreignKey' => 'm_companies_id'
    ]
  ];

}