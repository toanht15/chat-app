<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/01/16
 * Time: 11:44
 */

class ChatbotScenarioException  extends CakeException
{
  private $errors;
  private $lastPage;

  public function __construct($message = "シナリオ設定エラー", $code =500)
  {
    parent::__construct($message, $code);
    $this->init();
  }

  public function setErrors($errors) {
    $this->errors = $errors;
  }

  public function getErrors() {
    return $this->errors;
  }

  public function setLastPage($lastPage) {
    $this->lastPage = $lastPage;
  }

  public function getLastPage() {
    return $this->lastPage;
  }

  private function init() {
    $this->errors = [];
    $this->lastPage = 0;
  }
}
