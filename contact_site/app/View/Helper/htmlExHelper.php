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
                if ( !empty($urlOpt['onclick']) ) {
                 $a .= " onclick='" . h($urlOpt['onclick']) . "'";
               }
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

  public function naviFaIconLink($title, $falClass, $urlOpt = [], $isSubMenu = false){
      if($isSubMenu) {
        $_tmp = "<a %s><p class='icon-wrap'><i class='icon fal %s'></i></p><p>%s</p></a>";
      } else {
        $_tmp = "<a %s><i class='icon fal %s'></i><p>%s</p></a>";
      }
    $img = [
      'src' => null,
      'alt' => null
    ];
    $a = null;

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
        if ( !empty($urlOpt['onclick']) ) {
          $a .= " onclick='" . h($urlOpt['onclick']) . "'";
        }
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
    return sprintf($_tmp, $a, $falClass, $title);
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

    private function addLinkNewTab($matches){
      return "<a href='".$matches[0]."' target='_blank'>".$matches[1]."</a>";
    }

    private function addLink($matches){
      return "<a ".$matches[0].">".$matches[1]."</a>";
    }

    public function makeChatView($value, $isSendFile = false,$isRecieveFile = false,$imgTag = false){
        if($isSendFile) {
          return $this->makeSendChatView($value);
        }
        if($isRecieveFile) {
          return $this->makeRecieveChatView($value);
        }
        $content = null;

        foreach(explode("\n", $value) as $key => $tmp){
            $str = h($tmp);
            if ( preg_match("/^\[\]/", $tmp) ) {
                $str = "<input type='radio' id='radio".$key."' disabled=''>";
                $str .= "<label class='pointer' for='radio".$key."'>".trim(preg_replace("/^\[\]/", "", $tmp))."</label>";
            }
            $linkData = [];
            if ( preg_match('/(http(s)?:\/\/[\w\-\.\/\?\=\,\#\:\%\!\(\)\<\>\"\x3000-\x30FE\x4E00-\x9FA0\xFF01-\xFFE3]+)/', $tmp) ) {
                if ( preg_match('/<a ([\s\S]*?)<\/a>/', $tmp)) {
                  $str = $tmp;
                }
                else {
                  $ret = preg_replace_callback('/(http(s)?:\/\/[\w\-\.\/\?\=\,\#\:\%\!\(\)\<\>\"\x3000-\x30FE\x4E00-\x9FA0\xFF01-\xFFE3]+)/', [$this, 'addLinkNewTab'], $tmp);
                  $str = preg_replace('/(http(s)?:\/\/[\w\-\.\/\?\=\,\#\:\%\!\(\)\<\>\"\x3000-\x30FE\x4E00-\x9FA0\xFF01-\xFFE3]+)/', $ret, $tmp);
                }
            }
            if ( preg_match('/<telno>([\s\S]*?)<\/telno>/', $tmp)) {
                $ret = "<span style='font-weight: normal;'>". preg_replace('/^<telno>|<\/telno>$/', "", $tmp) . "</span>";
                $str = preg_replace('/<telno>([\s\S]*?)<\/telno>/', $ret, $tmp);
            }
            if ( preg_match('/<img([\s\S]*?)>/', $tmp) && $imgTag) {
                //スタイル設定されている場合
                if(strpos($tmp,'style') !== false){
                  preg_match('/style="([\s\S]*?)"/', $tmp, $result);
                  $ret = preg_replace('/style="([\s\S]*?)"/', "style=".$result[1]."width:100%;", $tmp);
                  $str = "<div class='imgTag'>" . $ret . "</div>";
                }
                //スタイル設定されていない場合
                else {
                  $ret = preg_replace('/<img/', '<img style="width:100%;"', $tmp);
                  $str = "<div class='imgTag'>" . $ret . "</div>";
                }
            }
            $content .= $str."\n";
        }
        return $content;
    }

    public function visitorInput($record, $forceInputText = false, $showPlaceHolder = true, $addNgModel = true, $value = "") {
      if($forceInputText && strcmp($record['input_type'], 2) === 0) {
        $record['input_type'] = 1;
      }
      $placeholderAttr = "";
      if($showPlaceHolder) {
        $placeholderAttr = 'placeholder="'.$record['item_name'].'を追加"';
      }
      $ngModelAttr = "";
      if($addNgModel) {
        $ngModelAttr = 'ng-model="customData[\''.$record['item_name'].'\']"';
      }
      switch($record['input_type']) {
        case 1: // テキストボックス
          return sprintf('<input class="infoData" id="ng-customer-custom-%s" type="text" data-key="%s" ng-blur="saveCusInfo(\'%s\', customData)" '.$ngModelAttr.' value="%s" %s/>', $record['id'], $record['item_name'], $record['item_name'], $value, $placeholderAttr);
        case 2: // テキストエリア
          return sprintf('<textarea class="infoData" rows="7" id="ng-customer-custom-%s" data-key="%s" ng-blur="saveCusInfo(\'%s\', customData)" '.$ngModelAttr.' %s>%s</textarea>', $record['id'], $record['item_name'], $record['item_name'], $placeholderAttr, $value);
        case 3: // テキストエリア
          $options =  explode("\n", $record['input_option']);
          $html = sprintf('<select class="infoData" id="ng-customer-custom-%s" ng-blur="saveCusInfo(\'%s\', customData)" data-key="%s" '.$ngModelAttr.' value="%s">', $record['id'], $record['item_name'], $record['item_name'], $value);
          $html .= '<option value="">選択してください</option>';
          if($value && !in_array($value, $options)) {
            $html .= sprintf('<option value="%s" selected disabled>%s</option>', $value, $value);
          }
          for($i = 0; $i < count($options); $i++) {
            if(strcmp($options[$i], $value) === 0) {
              $html .= sprintf('<option value="%s" selected>%s</option>', $options[$i], $options[$i]);
            } else {
              $html .= sprintf('<option value="%s">%s</option>', $options[$i], $options[$i]);
            }
          }
          $html .= '</select>';
          return $html;
      }
    }

  public function visitorSearchInput($record, $forceInputText = false, $showPlaceHolder = true, $data) {
    if($forceInputText && strcmp($record['input_type'], 2) === 0) {
      $record['input_type'] = 1;
    }
    $placeholderAttr = "";
    if($showPlaceHolder) {
      $placeholderAttr = 'placeholder="%sを追加"';
    }
    switch($record['input_type']) {
      case 1: // テキストボックス
        if(!empty($data["CustomData"][$record['item_name']])) {
          return sprintf('<input id="ng-customer-custom-%s" type="text" value = '.$data["CustomData"][$record['item_name']].' name="data[CustomData][%s]"/>', $record['id'], $record['item_name']);
        }
        else {
          return sprintf('<input id="ng-customer-custom-%s" type="text" value = "" name="data[CustomData][%s]"/>', $record['id'], $record['item_name']);
        }
      case 2: // テキストエリア
        return sprintf('<textarea rows="7" id="ng-customer-custom-%s" name="data[CustomData][%s]"></textarea>', $record['id'], $record['item_name']);
      case 3: // テキストエリア
        $options =  explode("\n", $record['input_option']);
        $html = sprintf('<select id="ng-customer-custom-%s" name="data[CustomData][%s]">', $record['id'], $record['item_name']);
        $html .= '<option value="">選択してください</option>';
        for($i = 0; $i < count($options); $i++) {
          $html .= sprintf('<option value="%s">%s</option>', $options[$i], $options[$i]);
        }
        $html .= '</select>';
        return $html;
    }
  }

    private function makeSendChatView($value){
      $content = "";

      // ファイル送信メッセージはJSONが入ってくる
      $value = json_decode($value, TRUE);

      $thumbnail = "";
      if(preg_match('/(jpeg|jpg|gif|png)$/', $value['extension']) && !$this->isExpire($value['expired'])) {
        $thumbnail = "<img src='" . $value['downloadUrl'] . "' class='sendFileThumbnail' width='64' height='64'>";
      } else {
        $thumbnail = "<i class='fa " . $this->selectFontIconClassFromExtension($value['extension']) . " fa-4x sendFileThumbnail' aria-hidden='true'></i>";
      }
      if(isset($value['message'])) { // TODO シナリオメッセージとオペレータからのファイル送信の判定がmessageがあるかどうか。messageが増えたらタイトルが変わる
        $content .= "<span class='cName'>シナリオメッセージ（ファイル送信）" . ($this->isExpire($value['expired']) ? "（ダウンロード有効期限切れ）" : "") . "</span>";
      }
      else {
        $content .= "<span class='cName'>ファイル送信" . ($this->isExpire($value['expired']) ? "（ダウンロード有効期限切れ）" : "") . "</span>";
      }
      if(isset($value['message'])) {
        $content .= "<span class='scenarioSendFileMessage'>".$value['message']."</span>";
      }
      $content .= "<div class='sendFileContent'>";
      $content.= "  <div class='sendFileThumbnailArea'>" . $thumbnail . "</div>";
      $content.= "  <div class='sendFileMetaArea'>";
      $content.= "    <span class='data sendFileName'>" . $value['fileName'] . "</span>";
      $content.= "    <span class='data sendFileSize'>" . $this->prettyByte2Str($value['fileSize']) . "</span>";
      $content.= "  </div>";
      $content.= "</div>";

      return $content;
    }

    private function makeRecieveChatView($value){
      $this->log('ファイル受信～',LOG_DEBUG);
      $content = "";
      $height = "";

      // ファイル送信メッセージはJSONが入ってくる
      $value = json_decode($value, TRUE);

      $thumbnail = "";
      if(preg_match('/(jpeg|jpg|gif|png)$/', $value['extension'])) {
        $thumbnail = "<img src='" . $value['downloadUrl'] . "' class='recieveFileThumbnail' style='max-width: 200px; max-height: 140px'>";
      } else {
        $thumbnail = "<i class='fal " . $this->selectFontIconClassFromExtension($value['extension']) . " fa-4x recieveFileThumbnail' aria-hidden='true'></i>";
        $height = "style = 'height:64px'";
      }
      $content .= "<span class='cName'>シナリオメッセージ（ファイル受信）</span>";
      $content .= "<div class='recieveFileContent'>";
      $content .= "  <div class='recieveFileThumbnailArea' ".$height.">" . $thumbnail . "</div>";
      $content .= "  <div class='recieveFileMetaArea'>";
      $content .= "     <br>";
      $content .= "     <span class='comment'> ＜コメント＞</span>";
      $content .= "     <span class='message'>". $value['comment'] ."</span>";
      $content .= "  </div>";
      $content .= "</div>";
      return $content;
    }

    private function isExpire($expired) {
      return time() >= strtotime($expired);
    }

    private function selectFontIconClassFromExtension($ext) {
      $selectedClass = "";
      $icons = [
        "image" =>     'fa-file-image',
        "pdf"   =>     'fa-file-pdf',
        "word"  =>     'fa-file-word',
        "powerpoint" => 'fa-file-powerpoint',
        "excel" =>      'fa-file-excel',
        "audio" =>      'fa-file-audio',
        "video" =>      'fa-file-video',
        "zip" =>        'fa-file-zip',
        "code" =>       'fa-file-code',
        "text" =>       'fa-file-text',
        "file" =>       'fa-file'
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
        "csv"  => $icons['text'],
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

