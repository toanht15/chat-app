<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/12/07
 * Time: 10:27
 */

App::uses('Component', 'Controller');

class MailTemplateComponent extends Component {

  protected $body;

  public function __construct()
  {
    $this->body = "no content";
  }

  public function getBody() {
    return $this->body;
  }

  protected function replaceCharacterInBody($assoc) {
    foreach($assoc as $character => $content) {
      $this->body = str_replace($character, $content, $this->body);
    }
  }
}