<?php
/**
 * TrialController controller.
 * 無料トライアル登録画面
 */
App::uses('HttpSocket', 'Network/Http', 'Component', 'Controller', 'Utility/Validation');
App::uses('ComponentCollection', 'Controller'); //これが大事
App::uses('MailSenderComponent', 'Controller/Component');
class TrialController extends AppController {
  /*必ず消す*/
  const LOG_INFO = 'batch-info';
  const LOG_ERROR = 'batch-error';

  /*必ず治す*/
  const FROM_ADDRESS = "henmi0201@gmail.com";
  const FROM_NAME = "sinclo（シンクロ）";
  /*必ず消す*/

  const CONTRACT_ADD_URL = "http://127.0.0.1:81/Contract/add";
  const ML_MAIL_ADDRESS= "henmi0201@gmail.com";
  const ML_MAIL_ADDRESS_AND_ALEX = "henmi0201@gmail.com";
  const API_CALL_TIMEOUT = 5;
  const COMPANY_NAME = "##COMPANY_NAME##";
  const USER_NAME = "##USER_NAME##";
  const PASSWORD = "##PASSWORD##";
  const BUSINESS_MODEL = "##BUSINESS_MODEL##";
  const DEPARTMENT = "##DEPARTMENT##";
  const POSITION = "##POSITION##";
  const MAIL_ADDRESS = "##MAIL_ADDRESS##";
  const PHONE_NUMBER = "##PHONE_NUMBER##";
  const URL = "##URL##";
  const OTHER = "##OTHER##";
  public $components = ['AutoMessageMailTemplate', 'MailSender', 'Auth'];
  public $uses = ['MUser','TMailTransmissionLog','MSystemMailTemplate','TSendSystemMailSchedule','MJobMailTemplate','MCompany'];

  public function beforeFilter(){
    parent::beforeFilter();
    $this->Auth->allow(['index','add','thanks','remoteTermsOfService']);
    $this->header('Access-Control-Allow-Origin: http://127.0.0.1:81/Contract/add');
  }

  /* *
   * 基本設定ページ
   * @return void
   * */
  public function index() {
    $businessModel = Configure::read('businessModelType');
    $this->set('businessModel',$businessModel);
    $this->set('title_for_layout', '無料トライアル登録画面');

    //ここから消す
    //何日後の何時にメールを飛ばすか取得
    $schedules = $this->MJobMailTemplate->find('all');
    //トライアル中の企業を抜粋
    $trialCompany = $this->MCompany->find('all',[
      'conditions' => [
        'trial_flg' => 1,
        'del_flg' => 0
      ]
    ]);
    $companyIds = [];
    foreach($trialCompany as $company) {
      $companyIds[] .= $company['MCompany']['id'];
    }
    //トライアル中の企業のトライアル期間を検出
    $trialDay = $this->MAgreement->find('all',[
      'fields' => [
        'm_companies_id',
        'trial_start_day',
        'trial_end_day'
      ],
      'conditions' => [
        'm_companies_id' => $companyIds
      ]
    ]);
    $jobMailTemplatesData = [];
    $companyIds = [];
    //何日後の何時に当てはまる企業を抜粋
    foreach($schedules as $key => $val) {
        foreach($trialDay as $day) {
          if($val['MJobMailTemplate']['value_type'] == 0) {
            $trialTime = date('Y-m-d '.$val['MJobMailTemplate']['time'], strtotime('+'.$val['MJobMailTemplate']['value'].'day',strtotime($day['MAgreement']['trial_start_day'])));
          }
          if($val['MJobMailTemplate']['value_type'] == 1) {
            $trialTime = date('Y-m-d '.$val['MJobMailTemplate']['time'], strtotime('-'.$val['MJobMailTemplate']['value'].'day',strtotime($day['MAgreement']['trial_end_day'])));
          }
          $nowTime = date('Y-m-d H');
          if($trialTime == $nowTime) {
            $jobMailTemplatesData[$key]['id'] = $val['MJobMailTemplate']['id'];
            $jobMailTemplatesData[$key]['mail_type_cd'] = $val['MJobMailTemplate']['mail_type_cd'];
            $jobMailTemplatesData[$key]['sender'] = $val['MJobMailTemplate']['sender'];
            $jobMailTemplatesData[$key]['subject'] = $val['MJobMailTemplate']['subject'];
            $jobMailTemplatesData[$key]['mail_body'] = $val['MJobMailTemplate']['mail_body'];
            $jobMailTemplatesData[$key]['send_mail_ml_flg'] = $val['MJobMailTemplate']['send_mail_ml_flg'];
            $companyIds[] .= $day['MAgreement']['m_companies_id'];
          }
      }
    }
    //トライアル中の企業のユーザーのメールアドレスを検出
    $mailAdressData = $this->MUser->find('all',[
      'fields' => [
        'mail_address'
      ],
      'conditions' => [
        'm_companies_id' => $companyIds,
        'permission_level' => [1,2]
      ]
    ]);

    if(empty($mailAdressData)) {
      $this->log('schedule is not found.', self::LOG_INFO);
    } else {
      foreach($jobMailTemplatesData as $jobMailTemplate) {
        foreach($mailAdressData as $index => $mailAdress) {
          try {
            $id = $jobMailTemplate['id'];
            $to = $mailAdress['MUser']['mail_address'];
            $sender = $jobMailTemplate['sender'];
            $body = $jobMailTemplate['mail_body'];
            $subject = $jobMailTemplate['subject'];
            $this->log("Sending mail to ".$to." subject : ".$subject." JOB ID: ".$id, self::LOG_INFO);
            /*$this->component->setFrom(self::FROM_ADDRESS);
            $this->component->setFromName($sender);
            $this->component->setTo($to);
            $this->component->setBody($body);
            $this->component->setSubject($subject);
            $this->component->send();*/
            $sender2 = new MailSenderComponent();
            $sender2->setFrom(self::FROM_ADDRESS);
            $sender2->setFromName($sender);
            $sender2->setTo($to);
            $sender2->setBody($body);
            $sender2->setSubject($subject);
            $sender2->send();

          } catch(Exception $e) {
            $this->log('send mail error !!!!', self::LOG_ERROR);
          }
        }
        if($jobMailTemplate['send_mail_ml_flg'] == 0) {
          $sender2 = new MailSenderComponent();
          $sender2->setFrom(self::FROM_ADDRESS);
          $sender2->setFromName($sender);
          $sender2->setTo('yuki.henmi@medialink-ml.co.jp');
          $sender2->setBody($body);
          $sender2->setSubject($subject);
          $sender2->send();
        }
      }
    }
    $this->log('END   sendmail schedule.', self::LOG_INFO);
    //ここまで
  }

  public function add() {
    $this->autoRender = FALSE;
    $this->layout = 'ajax';
    $data = $this->request->data;
    $data['MUser']['mail_address'] = $data['Contract']['user_mail_address'];
    $this->MUser->set($data);
    //mailAddress validattionチェック
    if(!$this->MUser->validates()) {
      $errorMessage = $this->MUser->validationErrors;
      $this->MUser->rollback();
      return $errorMessage['mail_address'][0];
    }

    $data['MCompany']['trial_flg'] = C_TRIAL_FLG;
    $data['MCompany']['options']['refCompanyData'] = 1;
    $data['MCompany']['m_contact_types_id'] = 1;
    $data['MCompany']['limit_users'] = 3;
    $data['MAgreements']['trial_start_day'] = date('Y-m-d');
    $data['MAgreements']['trial_end_day'] = date('Y-m-d', strtotime('+13 day', time()));
    $data['MAgreements']['agreement_start_day'] = '';
    $data['MAgreements']['agreement_end_day'] = '';

    $socket = new HttpSocket(array(
      'timeout' => self::API_CALL_TIMEOUT
    ));
    $result = $socket->post(self::CONTRACT_ADD_URL,$data,array('header' => array('X-Forwarded-Port' => 443)));
  }

  /* *
   * サンクスページ
   * @return void
   * */
  public function thanks() {
    $this->set('title_for_layout', '無料トライアルを受付いたしました');
  }

   /* *
  * 利用規約
  * @return void
  * */
  public function remoteTermsOfService() {
    Configure::write('debug', 0);
    $this->autoRender = FALSE;
    $this->layout = 'ajax';

    $this->render('/Elements/Trial/remoteTermsOfService');
  }

  private function generateRandomPassword($length) {
    $str = array_merge(range('a', 'z'), range('0', '9'), range('A', 'Z'));
    $r_str = null;
    for ($i = 0; $i < $length; $i++) {
      $r_str .= $str[rand(0, count($str) - 1)];
    }
    return $r_str;
  }
}
