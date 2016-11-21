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

  /**
  * logicalDelete: 論理削除関数
  * @param int $id: ターゲットのID
  * @return boolean true/false: 処理に成功したか、失敗したか
  * */
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
}
