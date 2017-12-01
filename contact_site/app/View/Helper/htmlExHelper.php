<?php
/**
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
        if ( !empty($urlOpt['href']) || !empty($urlOpt['onclick'])  || !empty($urlOpt['target']) ) {
            if ( empty($urlOpt['href']) ) {
                $a = "href='javascript:void(0)'";
                if ( empty($urlOpt['onclick']) ) {
                    $a .= " onclick='" . h($urlOpt['onclick']) . "'";
                }
            }
            else {
                $a = "href='" . $this->Html->url($urlOpt['href']) . "'";
            }
            if ( !empty($urlOpt['target']) ) {
                $a .= " target='" . h((string)$urlOpt['target']) . "'";
            }
        }
        return sprintf($_tmp, $a, $this->Html->image($img['src'], $img['option']), $title);
    }

    public function timepad($str){
        return sprintf("%02d", $str);
    }

    public function calcTime($startDateTime, $endDateTime){
        if ( empty($startDateTime) || empty($endDateTime) ) {
            return "-";
        }
        $start = strtotime($startDateTime);
        $end = strtotime($endDateTime);
        $term = intval($end - $start);
        $hour = intval($term / 3600);
        $min = intval(($term / 60) % 60);
        $sec = $term % 60;
        return $this->timepad($hour) . ":" . $this->timepad($min) . ":" . $this->timepad($sec);
    }

    private function addLink($matches){
        return "<a href='".$matches[0]."' target='_blank'>".$matches[0]."</a>";
    }

    public function makeChatView($value){
        $content = null;

        foreach(explode("\n", $value) as $key => $tmp){
            $str = h($tmp);
            if ( preg_match("/^\[\]/", $tmp) ) {
                $str = "<input type='radio' id='radio".$key."' disabled=''>";
                $str .= "<label class='pointer' for='radio".$key."'>".trim(preg_replace("/^\[\]/", "", $tmp))."</label>";
            }
            if ( preg_match('/(http(s)?:\/\/[\w\-\.\/\?\,\#\:\%\!\(\)\<\>\"\u3000-\u30FE\u4E00-\u9FA0\uFF01-\uFFE3]+)/', $tmp) ) {
                $ret = preg_replace_callback('/(http(s)?:\/\/[\w\-\.\/\?\,\#\:\%\!\(\)\<\>\"\u3000-\u30FE\u4E00-\u9FA0\uFF01-\uFFE3]+)/', [$this, 'addLink'], $tmp);
                $str = preg_replace('/(http(s)?:\/\/[\w\-\.\/\?\,\#\:\%\!\(\)\<\>\"\u3000-\u30FE\u4E00-\u9FA0\uFF01-\uFFE3]+)/', $ret, $tmp);
            }
            if ( preg_match('/<telno>([\s\S]*?)<\/telno>/', $tmp)) {
                $ret = "<span style='font-weight: normal;'>". preg_replace('/^<telno>|<\/telno>$/', "", $tmp) . "</span>";
                $str = preg_replace('/<telno>([\s\S]*?)<\/telno>/', $ret, $tmp);
            }
            $content .= $str."\n";
        }
        return $content;
    }
}
