<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/02/20
 * Time: 19:07
 */

App::uses('ComponentCollection', 'Controller'); //これが大事
App::uses('MailSenderComponent', 'Controller/Component');
class FreeTrialMailJobBatchShell extends AppShell
{
  const LOG_INFO = 'batch-info';
  const LOG_ERROR = 'batch-error';

  public $uses = array('TSendSystemMailSchedule');


  private $component;
  /**
   * MailSenderComponent.phpの呼び出し
   * @see https://qiita.com/colorrabbit/items/d302cc0eeec3adc18456
   */
  public function startup() {
    $collection = new ComponentCollection(); //これが大事です。
    $this->component = new MailSenderComponent($collection); //コンポーネントをインスタンス化
    parent::startup();
  }

  public function sendmail() {
    $this->log('BEGIN sendmail schedule.', self::LOG_INFO);
    $now = time();
    $beginDate = date('Y-m-d H:00:00', $now);
    $endDate = date('Y-m-d H:59:59', $now);
    $this->log('TARGET schedule is '.$beginDate.' 〜 '.$endDate.' .', self::LOG_INFO);
    $schedules = $this->TSendSystemMailSchedule->find('all', array(
      'conditions' => array(
        'AND' => array(
          'sending_datetime >= ' => $beginDate,
          'sending_datetime < ' => $endDate
        ),
        'NOT' => array(
          'send-mail_flg' => 1
        )
      )
    ));
    if(empty($schedules)) {
      $this->log('schedule is not found.', self::LOG_INFO);
    } else {
      foreach($schedules as $index => $schedule) {
        try {
          $id = $schedule['TSendSystemMailSchedule']['id'];
          $to = $schedule['TSendSystemMailSchedule']['mail_address'];
          $body = $schedule['TSendSystemMailSchedule']['mail_body'];
          $subject = $schedule['TSendSystemMailSchedule']['subject'];
          $this->log("Sending mail to ".$to." subject : ".$subject." JOB ID: ".$id, self::LOG_INFO);
          $this->component->setTo($to);
          $this->component->setBody($body);
          $this->component->setSubject($subject);
          $this->component->send();

          $schedule['TSendSystemMailSchedule']['send-mail_flg'] = 1;
          $this->TSendSystemMailSchedule->set($schedule);
          $this->TSendSystemMailSchedule->save();
        } catch(Exception $e) {
          $this->log('send mail error !!!!', self::LOG_ERROR);
        }
      }
    }
    $this->log('END   sendmail schedule.', self::LOG_INFO);
  }
}