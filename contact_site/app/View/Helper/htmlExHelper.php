<?php
/**
 * Angular用
 * FormHelper拡張ヘルパー
 * htmlExHelper
 */
class htmlExHelper extends AppHelper {

	public $helpers = ['Html'];

	public function naviLink($title, $imgPath, $urlOpt = []){
		$_tmp = "<a %s>%s<p>%s</p></a>";
		$img = [
			'src' => null,
			'alt' => null
		];
		$a = null;

		// setting img
		if ( !empty($imgPath) ) {
			$img['src'] = $imgPath;
			$img['option'] = [
				'alt' => $title,
				'width' => 30,
				'height' => 30
			];
		}

		// setting href
		if ( !empty($urlOpt['href']) || !empty($urlOpt['onclick']) ) {
			if ( empty($urlOpt['href']) ) {
				$a = "href='javascript:void(0)'";
				if ( empty($urlOpt['onclick']) ) {
					$a .= " onclick='" . h($urlOpt['onclick']) . "'";
				}
			}
			else {
				$a = "href='" . $this->Html->url($urlOpt['href']) . "'";
			}
		}

		return sprintf($_tmp, $a, $this->Html->image($img['src'], $img['option']), $title);
	}
}
