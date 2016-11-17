<?php
/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class AppHelper extends Helper {

  /**
   * 指定されたパラメータを除外する
   * @param $excludes array パラメーターリスト
   * @param $url url URL
   * @return 加工後URL
   * */
  function trimToURL($excludes, $url){
    if (empty($excludes)) return $url;

    $elements = parse_url($url);
    if (!isset($elements['query'])) return $url;
    parse_str($elements['query'], $params);
    $elements['query'] = "";
    foreach (array_diff_key($params, array_flip($excludes)) as $key => $val) {
      $elements['query'] .= ($elements['query'] !== "") ? "&" :  "";
      $elements['query'] .= (isset($val) && $val !== "") ? $key . "=" . $val : $key;
    }
    return $this->build_url($elements);
  }

  /**
   * parseしたURLを元に戻す
   * @param $elements array parse_urlの結果配列
   * @return URL
   * */
  function build_url(array $elements) {
      $e = $elements;
      return
          (isset($e['host']) ? (
              (isset($e['scheme']) ? "$e[scheme]://" : '//') .
              (isset($e['user']) ? $e['user'] . (isset($e['pass']) ? ":$e[pass]" : '') . '@' : '') .
              $e['host'] .
              (isset($e['port']) ? ":$e[port]" : '')
          ) : '') .
          (isset($e['path']) ? $e['path'] : '/') .
          (isset($e['query']) ? (
            is_array($e['query']) ?
              '?' . http_build_query($e['query'], '', '&') :
              (($e['query'] !== "") ? '?' . $e['query'] : '')
          ) : '') .
          (isset($e['fragment']) ? "#$e[fragment]" : '');
  }
}
