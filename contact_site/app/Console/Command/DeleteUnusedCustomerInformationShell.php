<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/02/20
 * Time: 19:07
 * @property TAutoMessage $TAutoMessage
 */

class DeleteUnusedCustomerInformationShell extends AppShell
{
  const LOG_INFO = 'batch-info';
  const LOG_ERROR = 'batch-error';

  public $uses = array('MCustomer');

  /**
   * MailSenderComponent.phpの呼び出し
   * @see https://qiita.com/colorrabbit/items/d302cc0eeec3adc18456
   */
  public function startup()
  {
    parent::startup();
  }

  public function delete()
  {
    $data = $this->MCustomer->find('all', array(
        'conditions' => array(
            'm_companies_id' => 7
        )
    ));
    $this->MCustomer->begin();
    try {
      foreach ($data as $index => $datum) {
        $json = $datum['MCustomer']['informations'];
        $obj = json_decode($json, true);
        if ($obj) {
          $this->printLog('============================================');
          $this->printLog('id: ' . $datum['MCustomer']['id']);
          $this->printLog('original data : ' . $json);
          $this->printLog('~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~');
          // 不要なデータがあったら削除する
          foreach ($obj as $key => $value) {
            if (strcmp('会社名', $key) === 0
                && strpos($value, 'iframe') !== false) {
              $obj[$key] = "";

              $afterJSON = json_encode($obj);
              $this->printLog('replaced data : ' . $afterJSON);
              $datum['MCustomer']['informations'] = $afterJSON;
              $this->MCustomer->create();
              $this->MCustomer->set($datum['MCustomer']);
              if (!$this->MCustomer->save()) {
                throw new Exception('保存時にエラー');
              }
            } else {
              $this->printLog('skipped key : ' . $key . ' value: ' . $value);
            }
          }
        }
      }
    } catch (Exception $e) {
      $this->printLog('ERROR : 保存時にエラーが発生');
      $this->MCustomer->rollback();
      return false;
    }
    $this->MCustomer->commit();
  }

  private function printLog($msg)
  {
    $this->log($msg, self::LOG_INFO);
    $this->out($msg);
  }
}