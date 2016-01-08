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
   * begin: トランザクション開始
   * @return void
   * */
  public function begin() {
    $dataSource = $this->getDataSource();
    $dataSource->begin($this);
  }

  /**
   * commit: コミット処理へ
   * @return void
   * */
  public function commit() {
    $dataSource = $this->getDataSource();
    $dataSource->commit($this);
  }

  /**
   * rollback: ロールバック処理へ
   * @return void
   * */
  public function rollback() {
    $dataSource = $this->getDataSource();
    $dataSource->rollback($this);
  }

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

  /**
   * beforeSave: データ更新情報を格納
   * [登録処理]
   *   条件：データにIDがセットされていない
   *   処理：登録者ID、登録日、更新者ID、更新日を保存
   * [更新処理]
   *   条件：データにIDがセットされている、削除フラグが立っていない
   *   処理：更新者ID、更新日を保存
   * [削除処理]
   *   条件：削除フラグが立っている
   *   処理：更新者ID、更新日、削除者ID、削除日を保存
   *
   * @param array $options
   * @return boolean true
   * */
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
