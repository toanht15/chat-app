<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2017/07/11
 * Time: 22:44
 */

class TestAccessCountsShell extends AppShell {
  public $uses = array('THistoryAccessCount','THistory','THistoryWidgetCount');

  private $beginDate;
  //private $dataCountPerHourForChat = 10000000;

  public function makeAceessCounts() {
    $startHistoriesId = 37113;
    //$this->beginDate = new DateTime("2015-01-01 00:00:00.00");
    for($day = 0; $day < 8740; $day++) {
      $createdObj = $this->THistory->findById($startHistoriesId);
      $this->beginDate = new DateTime($createdObj['THistory']['access_date']);
      $baseBeginDate = clone $this->beginDate;
      echo 'create ' . $baseBeginDate->format('Y-m-d H:i:s') . PHP_EOL;
      $this->THistoryAccessCount->create();
      $this->THistoryAccessCount->set(array(
        'm_companies_id' => 1,
        'year' => $baseBeginDate->format('Y'),
        'month' => $baseBeginDate->format('m'),
        'day' => $baseBeginDate->format('d'),
        'hour' => $baseBeginDate->format('H'),
        'access_count' => 1200
      ));
      $this->THistoryAccessCount->save();
        $startHistoriesId = $startHistoriesId + 1200;
      //$this->beginDate->modify('+1 hour');
    }
  }

  public function makeWidjetCounts() {
    $startHistoriesId = 37113;
    //$this->beginDate = new DateTime("2015-01-01 00:00:00.00");
    for($day = 0; $day < 8740; $day++) {
      $createdObj = $this->THistory->findById($startHistoriesId);
      $this->beginDate = new DateTime($createdObj['THistory']['access_date']);
      $baseBeginDate = clone $this->beginDate;
      echo 'create ' . $baseBeginDate->format('Y-m-d H:i:s') . PHP_EOL;
      $this->THistoryWidgetCount->create();
      $this->THistoryWidgetCount->set(array(
        'm_companies_id' => 1,
        'year' => $baseBeginDate->format('Y'),
        'month' => $baseBeginDate->format('m'),
        'day' => $baseBeginDate->format('d'),
        'hour' => $baseBeginDate->format('H'),
        'widget_count' => 600
      ));
      $this->THistoryWidgetCount->save();
        $startHistoriesId = $startHistoriesId + 1200;
      //$this->beginDate->modify('+1 hour');
    }
  }
}