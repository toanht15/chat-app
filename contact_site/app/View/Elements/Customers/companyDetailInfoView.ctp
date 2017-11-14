<?php
App::uses('LandscapeCodeMapper', 'Vendor/Util/Landscape');
?>

<script type="text/javascript">
</script>
<ul class="left-area boxContainer">
  <li>
    <p><span>法人番号</span></p>
    <span><?=$data['houjinBangou']?></span>
  </li>
  <li>
    <p><span>企業名</span></p>
    <span><?=$data['orgName']?></span>
  </li>
  <li>
    <p><span>代表者</span></p>
    <span><?=$data['orgPresident']?></span>
  </li>
  <li>
    <p><span>設立</span></p>
    <span><?=$data['orgDate']?></span>
  </li>
  <li>
    <p><span>売上高</span></p>
    <span><?=LandscapeCodeMapper::getLabel('orgGrossCode', $data['orgGrossCode']);?></span>
  </li>
  <li>
    <p><span>資本金</span></p>
    <span><?=LandscapeCodeMapper::getLabel('orgCapitalCode',$data['orgCapitalCode']);?></span>
  </li>
  <li>
    <p><span>従業員数</span></p>
    <span><?=LandscapeCodeMapper::getLabel('orgEmployeesCode',$data['orgEmployeesCode']);?></span>
  </li>
  <li>
    <p><span>業種</span></p>
    <span><?=LandscapeCodeMapper::getLabel('orgIndustrialCategoryM',$data['orgIndustrialCategoryM']);?></span>
  </li>
  <li>
    <p><span>上場区分</span></p>
    <span><?=LandscapeCodeMapper::getLabel('orgIpoType',$data['orgIpoType']);?></span>
  </li>
  <li>
    <p><span>企業URL</span></p>
    <span><a href="<?=$data['orgUrl']?>" target="_blank"><?=$data['orgUrl']?></a></span>
  </li>
</ul>
<ul class="right-area boxContainer">
  <li>
    <p><span>LBCコード</span></p>
    <span><?=$data['lbcCode']?></span>
  </li>
  <li>
    <p><span>本社住所</span></p>
    <span><?=$data['orgAddress']?></span>
  </li>
  <li>
    <p><span>電話番号</span></p>
    <span><?=$data['orgTel']?></span>
  </li>
  <li>
    <p><span>FAX番号</span></p>
    <span><?=$data['orgFax']?></span>
  </li>
  <li class="ip-view">
    <p><span>IPアドレス</span></p>
    <span><?=$data['ipAddress'];?></span>
  </li>
</ul>