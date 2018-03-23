<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/08/08
 * Time: 12:09
 */

App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
class MailTemplateSettingsController extends AppController
{
  public $uses = ['MCompany', 'MJobMailTemplate','MAgreements', 'MUser', 'MMailTemplate', 'TransactionManager','MSystemMailTemplate'];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->set('title_for_layout', 'サイトキー管理');
    header('Access-Control-Allow-Origin: *');
  }

  /**
   * 初期画面
   * @return void
   */
  public function index() {
    $this->set('title_for_layout', 'サイトキー管理');
    $jobMailData = $this->MJobMailTemplate->find('all');
    $systemMailData = $this->MSystemMailTemplate->find('all',[
      'conditions' => [
        'id' => array(1,4,5,7),
      ]
    ]);
    $this->set('jobMailData', $jobMailData);
    $this->set('systemMailData', $systemMailData);
  }

  public function add() {
    Configure::write('debug', 0);
    $this->set('title_for_layout', 'メール登録');

    if( $this->request->is('post') ) {
      $this->autoRender = false;
      $this->layout = "ajax";
      $data = $this->getParams();

      try {
        $this->processTransaction($data);
      } catch(Exception $e) {
        $this->log("Exception Occured : ".$e->getMessage(), LOG_WARNING);
        $this->log($e->getTraceAsString(),LOG_WARNING);
        $this->response->statusCode(400);
        return json_encode([
          'success' => false,
          'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
      }
    }
    else {
      $agreement = Configure::read('agreement');
      $mailRegistration = Configure::read('mailRegistration');
      $sendingMailML = Configure::read('sendingMailML');
      $this->set('agreement',$agreement);
      $this->set('agreementFlg',C_FREE_TRIAL_AGREEMENT);
      $this->set('mailRegistration',$mailRegistration);
      $this->set('sendingMailML',$sendingMailML);
      $this->set('value',C_AFTER_APPLICATION);
    }
  }

  private function getParams() {
    return $this->request->data;
  }

  private function processTransaction($mailInfo) {
    try {
      //N日後orN日前
      if($mailInfo['MailTemplateSettings']['timeToSendMail'] == C_AFTER_DAYS || $mailInfo['MailTemplateSettings']['timeToSendMail'] == C_BEFORE_DAYS) {
        //N日後
        if($mailInfo['MailTemplateSettings']['timeToSendMail'] == C_AFTER_DAYS) {
          $mailInfo['MJobMailTemplate']['value_type'] = 0;
        }
        //N日前
        if($mailInfo['MailTemplateSettings']['timeToSendMail'] == C_BEFORE_DAYS) {
          $mailInfo['MJobMailTemplate']['value_type'] = 1;
        }
        $transaction = $this->TransactionManager->begin();
        $this->createMailInfo($mailInfo);
      }
      //無料トライアル契約
      if($mailInfo['MJobMailTemplate']['agreement_flg'] == C_FREE_TRIAL_AGREEMENT) {
        //無料トライアル申込み後or契約申込み後or初期パスワード変更後
        if($mailInfo['MailTemplateSettings']['timeToSendMail'] == C_AFTER_APPLICATION ||  $mailInfo['MailTemplateSettings']['timeToSendMail'] == C_AFTER_PASSWORD_CHANGE) {
          $transaction = $this->TransactionManager->begin();
          $this->createFreeAnotherMailInfo($mailInfo);
        }
      }
       //本契約
      if($mailInfo['MJobMailTemplate']['agreement_flg'] == C_AGREEMENT) {
        //無料トライアル申込み後or契約申込み後or初期パスワード変更後
        if($mailInfo['MailTemplateSettings']['timeToSendMail'] == C_AFTER_APPLICATION ||  $mailInfo['MailTemplateSettings']['timeToSendMail'] == C_AFTER_PASSWORD_CHANGE) {
          $transaction = $this->TransactionManager->begin();
          $this->createAnotherMailInfo($mailInfo);
        }
      }
    } catch (Exception $e) {
      $this->TransactionManager->rollback($transaction);
      throw $e;
    }
    $this->TransactionManager->commit($transaction);
  }

  private function createMailInfo($mailInfo) {
    $errors = [];
    $mailInfo = $mailInfo['MJobMailTemplate'];
    $tmpData = [
        "mail_type_cd" => $mailInfo["mail_type_cd"],
        "subject" => $mailInfo["subject"],
        "sender" => $mailInfo["sender"],
        "mail_body" => $mailInfo["mail_body"],
        "value_type" => $mailInfo["value_type"],
        "value" => $mailInfo["value"],
        "time" => $mailInfo["time"],
        "agreement_flg" => $mailInfo["agreement_flg"],
        "send_mail_ml_flg" => $mailInfo["mail_body"]
    ];
    $this->MJobMailTemplate->create();
    $this->MJobMailTemplate->set($tmpData);
    if(!$this->MJobMailTemplate->validates()) {
      $this->MJobMailTemplate->rollback();
      // 画面に返す
      $errors = $this->MJobMailTemplate->validationErrors;
      throw new Exception("MJobMailTemplate validation error");
    }
    else {
      $this->MJobMailTemplate->save();
    }
  }

  private function createFreeAnotherMailInfo($mailInfo) {
    $errors = [];
    if($mailInfo['MailTemplateSettings']['timeToSendMail'] == C_AFTER_APPLICATION) {
      $mailInfo = $mailInfo['MSystemMailTemplate'];
      $tmpData = [
          'id' => 1,
          "mail_type_cd" => $mailInfo["mail_type_cd"],
          "sender" => $mailInfo["sender"],
          "subject" => $mailInfo["subject"],
          "mail_body" => $mailInfo["mail_body"]
      ];
    }
    else if($mailInfo['MailTemplateSettings']['timeToSendMail'] == C_AFTER_PASSWORD_CHANGE) {
      $mailInfo = $mailInfo['MSystemMailTemplate'];
      $tmpData = [
          'id' => 4,
          "mail_type_cd" => $mailInfo["mail_type_cd"],
          "sender" => $mailInfo["sender"],
          "subject" => $mailInfo["subject"],
          "mail_body" => $mailInfo["mail_body"],
      ];
    }
    $this->MSystemMailTemplate->set($tmpData);
    if(!$this->MSystemMailTemplate->validates()) {
      $this->MSystemMailTemplate->rollback();
      // 画面に返す
      $errors = $this->MSystemMailTemplate->validationErrors;
      throw new Exception("MSystemMailTemplate validation error");
    }
    else {
      $this->MSystemMailTemplate->save();
    }
  }

  private function createAnotherMailInfo($mailInfo) {
    $errors = [];
    if($mailInfo['MailTemplateSettings']['timeToSendMail'] == C_AFTER_APPLICATION) {
      $mailInfo = $mailInfo['MSystemMailTemplate'];
      $tmpData = [
          'id' => 5,
          "mail_type_cd" => $mailInfo["mail_type_cd"],
          "sender" => $mailInfo["sender"],
          "subject" => $mailInfo["subject"],
          "mail_body" => $mailInfo["mail_body"]
      ];
    }
    else if($mailInfo['MailTemplateSettings']['timeToSendMail'] == C_AFTER_PASSWORD_CHANGE) {
      $mailInfo = $mailInfo['MSystemMailTemplate'];
      $tmpData = [
          'id' => 7,
          "mail_type_cd" => $mailInfo["mail_type_cd"],
          "sender" => $mailInfo["sender"],
          "subject" => $mailInfo["subject"],
          "mail_body" => $mailInfo["mail_body"],
      ];
    }
    $this->MSystemMailTemplate->set($tmpData);
    if(!$this->MSystemMailTemplate->validates()) {
      $this->MSystemMailTemplate->rollback();
      // 画面に返す
      $errors = $this->MSystemMailTemplate->validationErrors;
      throw new Exception("MSystemMailTemplate validation error");
    }
    else {
      $this->MSystemMailTemplate->save();
    }
  }


  /* *
   * 更新画面
   * @param id
   * @return void
   * */
  public function edit($id,$when,$exam)
  {
    if ($this->request->is('post') || $this->request->is('put')) {
      $saveData = $this->request->data;
      //何日後,何日前の場合
      if(($saveData['MailTemplateSettings']['timeToSendMail'] == C_AFTER_DAYS || $saveData['MailTemplateSettings']['timeToSendMail'] == C_BEFORE_DAYS)) {
        //時間設定
        if($saveData['MJobMailTemplate']['time'] == 0) {
          $saveData['MJobMailTemplate']['time'] = 9;
        }
        if($saveData['MJobMailTemplate']['time'] == 1) {
          $saveData['MJobMailTemplate']['time'] = 12;
        }
        if($saveData['MJobMailTemplate']['time'] == 2) {
          $saveData['MJobMailTemplate']['time'] = 15;
        }
        if($saveData['MJobMailTemplate']['time'] == 2) {
          $saveData['MJobMailTemplate']['time'] = 19;
        }
        //何日後、何日前更新の場合
        if($when == 3 || $when == 4) {
          $mailInfo = $this->MJobMailTemplate->read(null, $id);
        }
        //申込情報登録時、初期パスワード変更時に変更の場合
        if($when == 1 || $when == 2) {
        $this->MJobMailTemplate->create();
        }
      }
      //申込み後or初期パスワード変更後
      else if(($saveData['MailTemplateSettings']['timeToSendMail'] == C_AFTER_APPLICATION ||
        $saveData['MailTemplateSettings']['timeToSendMail'] == C_AFTER_PASSWORD_CHANGE) && ($when == 1 || $when == 2)) {
        $mailInfo = $this->MSystemMailTemplate->read(null, $id);
      }
      else if(($saveData['MailTemplateSettings']['timeToSendMail'] == C_AFTER_APPLICATION ||
        $saveData['MailTemplateSettings']['timeToSendMail'] == C_AFTER_PASSWORD_CHANGE) &&
        ($when == 3 || $when == 4)) {
        if($saveData['MailTemplateSettings']['timeToSendMail'] == C_AFTER_APPLICATION && $saveData['MJobMailTemplate']['agreement_flg'] == C_FREE_TRIAL_AGREEMENT) {
          $this->MJobMailTemplate->delete($id);
          $saveData['MSystemMailTemplate']['id'] = 1;
        }
        if($saveData['MailTemplateSettings']['timeToSendMail'] == C_AFTER_PASSWORD_CHANGE && $saveData['MJobMailTemplate']['agreement_flg'] == C_FREE_TRIAL_AGREEMENT) {
          $this->MJobMailTemplate->delete($id);
          $saveData['MSystemMailTemplate']['id'] = 4;
        }
        if($saveData['MailTemplateSettings']['timeToSendMail'] == C_AFTER_APPLICATION && $saveData['MJobMailTemplate']['agreement_flg'] == C_AGREEMENT) {
          $this->MJobMailTemplate->delete($id);
          $saveData['MSystemMailTemplate']['id'] = 5;
        }
        if($saveData['MailTemplateSettings']['timeToSendMail'] == C_AFTER_PASSWORD_CHANGE && $saveData['MJobMailTemplate']['agreement_flg'] == C_AGREEMENT) {
          $this->MJobMailTemplate->delete($id);
          $saveData['MSystemMailTemplate']['id'] = 7;
        }
      }
      $transactions = $this->TransactionManager->begin();
      //N日後orN日前
      if($saveData['MailTemplateSettings']['timeToSendMail'] == C_AFTER_DAYS || $saveData['MailTemplateSettings']['timeToSendMail'] == C_BEFORE_DAYS) {
        //N日後
        if($saveData['MailTemplateSettings']['timeToSendMail'] == C_AFTER_DAYS) {
          $saveData['MJobMailTemplate']['value_type'] = 0;
        }
        //N日前
        if($saveData['MailTemplateSettings']['timeToSendMail'] == C_BEFORE_DAYS) {
          $saveData['MJobMailTemplate']['value_type'] = 1;
        }
        $saveData = $saveData['MJobMailTemplate'];
        $this->MJobMailTemplate->set($saveData);
        $this->MJobMailTemplate->save();
        $this->MJobMailTemplate->commit();
      }
      //申込み後or初期パスワード変更後
      else if($saveData['MailTemplateSettings']['timeToSendMail'] == C_AFTER_APPLICATION ||
        $saveData['MailTemplateSettings']['timeToSendMail'] == C_AFTER_PASSWORD_CHANGE) {
        $saveData = $saveData['MSystemMailTemplate'];
        $this->MSystemMailTemplate->set($saveData);
        $this->MSystemMailTemplate->save();
        $this->MSystemMailTemplate->commit();
      }
    }
    else {
      //何日後,何日前
      if($when == C_AFTER_DAYS || $when == C_BEFORE_DAYS) {
        $editData = $this->MJobMailTemplate->read(null, $id);
        $this->set('id', $editData['MJobMailTemplate']['id']);//削除に必要なもの
        //時間設定
        if($editData['MJobMailTemplate']['time'] == 9) {
          $editData['MJobMailTemplate']['time'] = 0;
        }
        if($editData['MJobMailTemplate']['time'] == 12) {
          $editData['MJobMailTemplate']['time'] = 1;
        }
        if($editData['MJobMailTemplate']['time'] == 15) {
          $editData['MJobMailTemplate']['time'] = 2;
        }
        if($editData['MJobMailTemplate']['time'] == 19) {
          $editData['MJobMailTemplate']['time'] = 3;
        }
        //何日後(無料トライアル)
        if($when == C_AFTER_DAYS && $editData['MJobMailTemplate']['agreement_flg'] == C_FREE_TRIAL_AGREEMENT) {
          $this->set('value',C_AFTER_DAYS);
          $this->set('agreementFlg',C_FREE_TRIAL_AGREEMENT);
        }
        //何日前(無料トライアル)
        if($when == C_BEFORE_DAYS && $editData['MJobMailTemplate']['agreement_flg'] == C_FREE_TRIAL_AGREEMENT) {
          $this->set('value',C_BEFORE_DAYS);
          $this->set('agreementFlg',C_FREE_TRIAL_AGREEMENT);
        }
        //何日後(本契約)
        if($when == C_AFTER_DAYS && $editData['MJobMailTemplate']['agreement_flg'] == C_AGREEMENT) {
          $this->set('value',C_AFTER_DAYS);
          $this->set('agreementFlg',C_AGREEMENT);
        }
        //何日前(本契約)
        if($when == C_BEFORE_DAYS && $editData['MJobMailTemplate']['agreement_flg'] == C_AGREEMENT) {
          $this->set('value',C_BEFORE_DAYS);
          $this->set('agreementFlg',C_AGREEMENT);
        }
      }
      //無料トライアル後or初期パスワード変更後
      else if($when == C_AFTER_APPLICATION || $when == C_AFTER_PASSWORD_CHANGE) {
        $editData = $this->MSystemMailTemplate->read(null, $id);
        $this->set('id', $editData['MSystemMailTemplate']['id']);//削除に必要なもの
        if($id == 1) {
          //無料トライアル登録後
          $this->set('value',C_AFTER_APPLICATION);
          $this->set('agreementFlg',C_FREE_TRIAL_AGREEMENT);
        }
        if($id == 4) {
          //初期パスワード変更後(無料トライアル登録後)
          $this->set('value',C_AFTER_PASSWORD_CHANGE);
          $this->set('agreementFlg',C_FREE_TRIAL_AGREEMENT);
        }
        if($id == 5) {
          //本契約登録後
          $this->set('value',C_AFTER_APPLICATION);
          $this->set('agreementFlg',C_AGREEMENT);
        }
        if($id == 7) {
          //初期パスワード変更後(本契約登録後)
          $this->set('value',C_AFTER_PASSWORD_CHANGE);
          $this->set('agreementFlg',C_AGREEMENT);
        }
      }
      $this->request->data = $editData;
      $agreement = Configure::read('agreement');
      $mailRegistration = Configure::read('mailRegistration');
      $sendingMailML = Configure::read('sendingMailML');
      $this->set('agreement',$agreement);
      $this->set('mailRegistration',$mailRegistration);
      $this->set('sendingMailML',$sendingMailML);
    }
  }

  public function deleteMailInfo() {
    $this->autoRender = false;
    $this->layout = "ajax";
    if($this->request->is('post')) {

      $transaction = $this->TransactionManager->begin();
      $id = $this->request->data['id'];
      $when = $this->request->data['when'];

      try {
        //何日後,何日前
        if($when == C_AFTER_DAYS || $when == C_BEFORE_DAYS) {
          //物理削除
          $this->MJobMailTemplate->delete($id);
        }
        else if($when == C_AFTER_APPLICATION || $when == C_AFTER_PASSWORD_CHANGE) {
          //物理削除
          $this->MSystemMailTemplate->delete($id);
        }
      } catch(Exception $e) {
        $this->TransactionManager->rollback($transaction);
        $this->log("Delete Exception Occured : ".$e->getMessage(), LOG_WARNING);
      }
      $this->TransactionManager->commit($transaction);
    }
    $this->autoRender = false;
  }
}