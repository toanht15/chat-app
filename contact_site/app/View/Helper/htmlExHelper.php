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
        //commontooltip用
        if ( !empty($urlOpt['class'])) {
          $a .= " class='" . h((string)$urlOpt['class']) . "'";
        }
        if ( !empty($urlOpt['data-text'])) {
          $a .= " data-text='" . h((string)$urlOpt['data-text']) . "'";
        }
        if ( !empty($urlOpt['data-balloon-position'])) {
          $a .= " data-balloon-position='" . h((string)$urlOpt['data-balloon-position']) . "'";
        }
        if ( !empty($urlOpt['data-content-position-left'])) {
          $a .= " data-content-position-left='" . h((string)$urlOpt['data-content-position-left']) . "'";
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

    public function makeChatView($value, $isSendFile){
        if($isSendFile) {
          return $this->makeSendChatView($value);
        }
        $content = null;

        foreach(explode("\n", $value) as $key => $tmp){
            $str = h($tmp);
            if ( preg_match("/^\[\]/", $tmp) ) {
                $str = "<input type='radio' id='radio".$key."' disabled=''>";
                $str .= "<label class='pointer' for='radio".$key."'>".trim(preg_replace("/^\[\]/", "", $tmp))."</label>";
            }
            if ( preg_match('/(http(s)?:\/\/[\w\-\.\/\?\=\,\#\:\%\!\(\)\<\>\"\u3000-\u30FE\u4E00-\u9FA0\uFF01-\uFFE3]+)/', $tmp) ) {
                $ret = preg_replace_callback('/(http(s)?:\/\/[\w\-\.\/\?\=\,\#\:\%\!\(\)\<\>\"\u3000-\u30FE\u4E00-\u9FA0\uFF01-\uFFE3]+)/', [$this, 'addLink'], $tmp);
                $str = preg_replace('/(http(s)?:\/\/[\w\-\.\/\?\=\,\#\:\%\!\(\)\<\>\"\u3000-\u30FE\u4E00-\u9FA0\uFF01-\uFFE3]+)/', $ret, $tmp);
            }
            if ( preg_match('/<telno>([\s\S]*?)<\/telno>/', $tmp)) {
                $ret = "<span style='font-weight: normal;'>". preg_replace('/^<telno>|<\/telno>$/', "", $tmp) . "</span>";
                $str = preg_replace('/<telno>([\s\S]*?)<\/telno>/', $ret, $tmp);
            }
            $content .= $str."\n";
        }
        return $content;
    }

    private function makeSendChatView($value){
      $content = "";

      // ファイル送信メッセージはJSONが入ってくる
      $value = json_decode($value, TRUE);

      $thumbnail = "";
      if(preg_match('/(jpeg|jpg|gif|png)$/', $value['extension']) && !$this->isExpire($value['expired'])) {
        $thumbnail .= "<img src='" + $value['downloadUrl'] + "' class='sendFileThumbnail' width='64' height='64'>";
      } else {
        $thumbnail .= "<i class='fa " . $this->selectFontIconClassFromExtension($value['extension']) . " fa-4x sendFileThumbnail' aria-hidden='true'></i>";
      }
      $content.= "<span class='cName'>ファイル送信" . ($this->isExpire($value['expired']) ? "（ダウンロード有効期限切れ）" : "") . "</span>";
      $content.= "<div class='sendFileContent'>";
      $content.= "  <div class='sendFileThumbnailArea'>" . $thumbnail . "</div>";
      $content.= "  <div class='sendFileMetaArea'>";
      $content.= "    <span class='data sendFileName'>" . $value['fileName'] . "</span>";
      $content.= "    <span class='data sendFileSize'>" . $this->prettyByte2Str($value['fileSize']) . "</span>";
      $content.= "  </div>";
      $content.= "</div>";

      return $content;
    }

    private function isExpire($expired) {
      return time() >= strtotime($expired);
    }

    private function selectFontIconClassFromExtension($ext) {
      $selectedClass = "";
      $icons = [
        "image" =>     'fa-file-image-o',
        "pdf"   =>     'fa-file-pdf-o',
        "word"  =>     'fa-file-word-o',
        "powerpoint" => 'fa-file-powerpoint-o',
        "excel" =>      'fa-file-excel-o',
        "audio" =>      'fa-file-audio-o',
        "video" =>      'fa-file-video-o',
        "zip" =>        'fa-file-zip-o',
        "code" =>       'fa-file-code-o',
        "text" =>       'fa-file-text-o',
        "file" =>       'fa-file-o'
      ];
      $extensions = [
        "gif"  => $icons['image'],
        "jpeg" => $icons['image'],
        "jpg"  => $icons['image'],
        "png"  => $icons['image'],
        "pdf"  => $icons['pdf'],
        "doc"  => $icons['word'],
        "docx" => $icons['word'],
        "ppt"  => $icons['powerpoint'],
        "pptx" => $icons['powerpoint'],
        "xls"  => $icons['excel'],
        "xlsx" => $icons['excel'],
        "aac"  => $icons['audio'],
        "mp3"  => $icons['audio'],
        "ogg"  => $icons['audio'],
        "avi"  => $icons['video'],
        "flv"  => $icons['video'],
        "mkv"  => $icons['video'],
        "mp4"  => $icons['video'],
        "gz"   => $icons['zip'],
        "zip"  => $icons['zip'],
        "css"  => $icons['code'],
        "html" => $icons['code'],
        "js"   => $icons['code'],
        "txt"  => $icons['text'],
        "csv"  => $icons['csv'],
        "file" => $icons['file']
      ];
      if(array_key_exists($ext, $extensions)) {
        $selectedClass = $extensions[$ext];
      } else {
        $selectedClass = $extensions['file'];
      }
      return $selectedClass;
    }

  private function prettyByte2Str($bytes) {
    if ($bytes >= 1073741824) {
      $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
      $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
      $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
      $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
      $bytes = $bytes . ' byte';
    } else {
      $bytes = '0 bytes';
    }
    return $bytes;
  }
}

