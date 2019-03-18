<?php
/**
 * Created by PhpStorm.
 * User: ryo.hosokawa
 * Date: 2019/02/19
 * Time: 10:37
 */

/**
 *
 */
?>
<script type="text/javascript">
//新規追加

//コピー

//削除

//並び替え

//編集
function openEdit(id) {
  if(!document.getElementById("sort").checked) {
    var index = Number("<?= $this->Paginator->params()["page"] ?>");
    location.href = createUrl(index, id);
  }
  else {
    return false;
  }
}

function createUrl(index, id) {
  var url = "<?= $this->Html->url('/TChatbotDiagrams/add') ?>";
  if(!!id) {
    url = url + "/" + id
  }
  return url + "?lastpage=" + index;
}


function openAdd(){

}


function viewGreenBtn(){

}

function viewRedBtn(){

}

function viewGrayBtn(){

}


</script>
