<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
  public function begin() {
    $dataSource = $this->getDataSource();
    $dataSource->begin($this);
  }

  public function commit() {
    $dataSource = $this->getDataSource();
    $dataSource->commit($this);
  }

  public function rollback() {
    $dataSource = $this->getDataSource();
    $dataSource->rollback($this);
  }

  public function logicalDelete($id) {
    $ret = $this->read(null, $id);
    if ( !empty($ret) && !empty($ret[$this->name]) && isset($ret[$this->name]['del_flg']) ) {
      $ret[$this->name]['del_flg'] = 1;
      if ( $this->save($ret, false) ) {
        return true;
      }
    }
    return false;
  }

  public function beforeSave($options = []) {
    $now = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
    // insert
    if(empty($this->id)){
      $this->data[$this->alias]['created_user_id'] = Configure::read('logged_user_id');
      $this->data[$this->alias]['created'] = $now->format("Y/m/d H:i:s");
    }
    // insert && update && delete
    $this->data[$this->alias]['modified_user_id'] = Configure::read('logged_user_id');
    $this->data[$this->alias]['modified'] = $now->format("Y/m/d H:i:s");
    // delete
    if(!empty($this->data[$this->alias]['del_flg'])){
      $this->data[$this->alias]['deleted_user_id'] = Configure::read('logged_user_id');
      $this->data[$this->alias]['deleted'] = $now->format("Y/m/d H:i:s");
    }
    return true;
  }
}
