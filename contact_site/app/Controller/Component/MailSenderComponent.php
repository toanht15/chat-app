<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/12/06
 * Time: 21:31
 */

App::uses('ComponentCollection', 'Controller'); //これが大事
App::uses('CakeEmail', 'Network/Email');

class MailSenderComponent extends Component
{
  const MAIL_SYSTEM_FROM_ADDRESS = 'no-reply@sinclo.jp';
  const MAIL_FORMAT = 'text';

  private $companiesId;

  private $mailer;

  private $from;
  private $fromName;
  private $to;
  private $subject;
  private $body;

  public function __construct($collection, $companiesId = 0)
  {
    if(empty($collection)) {
      $collection = new ComponentCollection();
    }
    parent::__construct($collection);
    $this->companiesId = $companiesId;
    $this->setup();
    $this->setDefaultSettings();
  }

  public function setFrom($from)
  {
    $custom = $this->getCustomFromMailAddress();
    if (!empty($custom)) {
      $this->from = $custom;
    } else {
      $this->from = $from;
    }
  }

  public function getFrom()
  {
    return $this->from;
  }

  public function setFromName($name)
  {
    $this->fromName = $name;
  }

  /**
   * To を設定
   * @param $csv 複数のメールアドレスをカンマを区切り文字とした文字列
   */
  public function setTo($csv)
  {
    $this->to = explode(',', $csv);
  }

  public function setSubject($subject)
  {
    $this->subject = $subject;
  }

  public function setBody($body)
  {
    $this->body = $body;
  }

  public function send()
  {
    $this->mailer
      ->from([$this->from => $this->fromName])
      ->to($this->to)
      ->subject($this->subject)
      ->send($this->_preventGarbledCharacters($this->body));
  }

  private function setup()
  {
    $this->mailer = new CakeEmail();
    $this->mailer->config([
      'host' => C_AWS_SES_SMTP_SERVER_NAME,
      'port' => C_AWS_SES_SMTP_PORT,
      'username' => C_AWS_SES_SMTP_USER_NAME,
      'password' => C_AWS_SES_SMTP_CREDENTIAL,
      'transport' => 'Smtp'
    ]);
    $this->mailer->emailFormat(self::MAIL_FORMAT);
  }

  private function setDefaultSettings()
  {
    $this->setFrom(self::MAIL_SYSTEM_FROM_ADDRESS);
  }

  /**
   * 文字が長い場合に自動改行で文字化けするため、強制改行を入れる
   * @see https://qiita.com/saekis/items/7ef6b0d6a9a7180e3ebe
   * @param $bigText
   * @param int $width
   * @return null|string|string[]
   */
  private function _preventGarbledCharacters($bigText, $width = 249)
  {
    // wordwrap()はマルチバイト未対応のため正規表現を使う。
    $pattern = "/(.{1,{$width}})(?:\\s|$)|(.{{$width}})/uS";
    $replace = '$1$2' . "\n";
    $wrappedText = preg_replace($pattern, $replace, $bigText);
    return $wrappedText;
  }

  private function getCustomFromMailAddress()
  {
    switch ($this->companiesId) {
      case 443:
        return 'biz_phc@post.pioneer.co.jp';
      case 489:
        return 'info@kagukuro.com';
      case 533:
        return 'yoyaku@247workout.net';
      default:
        return '';
    }
  }
}
