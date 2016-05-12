<?php
/**
 * 独自ヘルパー
 * FormHelper拡張ヘルパー
 * formExHelper
 */
App::uses('Hash', 'Utility');
class formExHelper extends AppHelper {

	public $helpers = ['Form'];

	public function val($tmp, $column) {
		$val = (isset($tmp[$column])) ? $tmp[$column] : "";
		return h($val);
	}
}
