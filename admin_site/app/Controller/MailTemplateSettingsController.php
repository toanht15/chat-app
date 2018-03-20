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
  public $uses = ['MCompany', 'MJobMailTemplate','MAgreements', 'MUser', 'MMailTemplate', 'TransactionManager'];

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
        'id' => array(1,4,5,6),
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
      $this->log('data',LOG_DEBUG);
      $this->log($data,LOG_DEBUG);

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
      if($mailInfo['MJobMailTemplate']['Agreement'] == C_FREE_TRIAL_AGREEMENT) {
        //無料トライアル申込み後or契約申込み後or初期パスワード変更後
        if($mailInfo['MailTemplateSettings']['timeToSendMail'] == C_AFTER_APPLICATION ||  $mailInfo['MailTemplateSettings']['timeToSendMail'] == C_AFTER_PASSWORD_CHANGE) {
          $transaction = $this->TransactionManager->begin();
          $this->createFreeAnotherMailInfo($mailInfo);
        }
      }
       //本契約
      if($mailInfo['MJobMailTemplate']['Agreement'] == C_AGREEMENT) {
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
    $tmpData = [
        "mail_type_cd" => $mailInfo["mail_type_cd"],
        "subject" => $mailInfo["subject"],
        "sender" => $mailInfo["sender"],
        "mail_body" => $mailInfo["mail_body"],
        "value_type" => $mailInfo["value_type"],
        "value" => $mailInfo["value"],
        "time" => $mailInfo["time"],
        "agreement_flg" => $mailInfo["Agreement"],
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
          'id' => 6,
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
  public function edit($id)
  {
    if ($this->request->is('post') || $this->request->is('put')) {
      $this->log('when',LOG_DEBUG);
      $this->log($when,LOG_DEBUG);
      $saveData = $this->request->data;
      //何日後,何日前の場合
      if($saveData['MailTemplateSettings']['timeToSendMail'] == C_AFTER_DAYS || $saveData['MailTemplateSettings']['timeToSendMail'] == C_BEFORE_DAYS) {
        $mailInfo = $this->MJobMailTemplate->read(null, $id);
      }
      /*else if(($when == C_AFTER_DAYS || $when == C_BEFORE_DAYS) && empty($exam)) {
        $this->log('新しく',LOG_DEBUG);
        $this->MJobMailTemplate->create();
      }*/
      //申込み後or初期パスワード変更後
      else if(($when == C_AFTER_APPLICATION || $when == C_AFTER_PASSWORD_CHANGE) && $when == $exam) {
        $mailInfo = $this->MSystemMailTemplate->read(null, $id);
      }
      else if(($when == C_AFTER_APPLICATION || $when == C_AFTER_PASSWORD_CHANGE) && $when !== $exam) {
        $this->MJobMailTemplate->delete($id);
        if($when == C_AFTER_APPLICATION && $this->request->data['MJobMailTemplate']['Agreement'] == C_FREE_TRIAL_AGREEMENT) {
          $this->request->data['MSystemMailTemplate']['id'] = 1;
        }
        if($when == C_AFTER_PASSWORD_CHANGE && $this->request->data['MJobMailTemplate']['Agreement'] == C_FREE_TRIAL_AGREEMENT) {
          $this->request->data['MSystemMailTemplate']['id'] = 4;
        }
        if($when == C_AFTER_APPLICATION && $this->request->data['MJobMailTemplate']['Agreement'] == C_AGREEMENT) {
          $this->request->data['MSystemMailTemplate']['id'] = 5;
        }
        if($when == C_AFTER_PASSWORD_CHANGE && $this->request->data['MJobMailTemplate']['Agreement'] == C_AGREEMENT) {
          $this->request->data['MSystemMailTemplate']['id'] = 6;
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
      else if($when == C_AFTER_APPLICATION || $when == C_AFTER_PASSWORD_CHANGE) {
        $saveData = $this->request->data['MSystemMailTemplate'];
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
        if($id == 6) {
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

      try {
        //何日後
        if($when == C_AFTER_DAYS) {
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