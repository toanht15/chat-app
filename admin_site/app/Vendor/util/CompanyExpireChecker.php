<?php
/**
 * Created by PhpStorm.
 * User: masashi_shimizu
 * Date: 2018/05/07
 * Time: 11:27
 */

class CompanyExpireChecker
{
  const EXPIRE_BEFORE_DAYS_THRESHOLD = 30;

  /**
   * メソッド実行時の時間が契約終了日を超えているかどうか
   * @param {string} $agreement_end_day
   * @return bool
   */
  public static function isExpireAgreementDay($agreement_end_day) {
    return strtotime($agreement_end_day.' 23:59:59') < time();
  }

  /**
   * メソッド実行時の時間がトライアル終了日を超えているかどうか
   * @param $trial_end_day
   * @return bool
   */
  public static function isExpireTrialDay($trial_end_day) {
    return strtotime($trial_end_day.' 23:59:59') < time();
  }

  /**
   * メソッド実行時の時間が契約終了日<code>self::EXPIRE_BEFORE_DAYS_THRESHOLD</code>前かどうか
   * @param $agreement_end_day
   * @return bool
   */
  public static function isWarningApplicationDay($agreement_end_day) {
    return (strtotime($agreement_end_day.' 23:59:59') - self::convertExpireBeforeDaysToSec()) < time();
  }

  /**
   * self::EXPIRE_BEFORE_DAYS_THRESHOLDを秒に変換したものを取得する
   * @return float|int
   */
  protected static function convertExpireBeforeDaysToSec()
  {
    return self::EXPIRE_BEFORE_DAYS_THRESHOLD * 24 * 60 * 60;
  }
}