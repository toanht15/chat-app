<?= $this->element('Statistics/datepicker') ?>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />

<style type="text/css">
/* 基本のテーブル定義 */
table.tableFrame {border:1px solid   #595959;border-collapse:collapse;table-layout:fixed;}
table.tableFrame td{border:1px solid #595959;}
table.tableFrame th{border:1px solid #595959; height: 35px;}
table.tableFrame th{background-color:#C3D69B;color:#595959;}

.tableData{width: 100%;}
.tableData th{width:100px;}
.tableData td{width:100px;}

#header_h {
  position: absolute;top:0px;
  max-width:100%;
  overflow-x:hidden;overflow-y:hidden;
  padding-left:12px;
  z-index:2;
  left:0px;
  right:17px;
}

#header_v {
  position: absolute;left:0px;padding-top:35px;
  width:112px;
  overflow-x:hidden;overflow-y:hidden;
  z-index:2;
  height:100%;
  padding-bottom:17px;
}

#data {
  position: absolute;padding-left:112px;padding-top:35px;
  max-width:100%;
  z-index:1;
  height:100%;
  left:0px;
}

#data2 {
  position: relative;
  overflow-x:scroll;overflow-y:scroll;
  max-width:100%;
  z-index:1;
  height:100%;
}


</style>
<script type="text/javascript">
console.log('ははっははは');

$(function() {
$("#selectName").change(function(){
  console.log('change入っている');
  searchInfo = $("select[name=selectName2]").val();
  //ifでyear,month,dayチェック
  if(searchInfo.length == 4) {
    $.ajax({
      type: 'post',
      dataType: 'html',
      data:{
        year:searchInfo
      },
      cache: false,
      url: "<?= $this->Html->url(['controller' => 'Statistics', 'action' => 'forChat']) ?>",
      success: function(html){
        console.log('成功');
        location.href = "<?=$this->Html->url(array('controller' => 'Statistics', 'action' => 'forChat'))?>";
      }
    });
  }
  else if(searchInfo.length == 7){
    $.ajax({
      type: 'post',
      dataType: 'html',
      data:{
        month:searchInfo
      },
      cache: false,
      url: "<?= $this->Html->url(['controller' => 'Statistics', 'action' => 'forChat']) ?>",
      success: function(html){
        location.href = "<?=$this->Html->url(array('controller' => 'Statistics', 'action' => 'forChat'))?>";
      }
    });
  }
});
});

function functionName()　{
var select1 = document.forms.formName.selectName1; //変数select1を宣言
var select2 = document.forms.formName.selectName2; //変数select2を宣言
var searchInfo;

select2.options.length = 0; // 選択肢の数がそれぞれに異なる場合、これが重要

$("#mainDatePeriod2").change(function(){
  searchInfo = $("input[name=datefilter]").val();
  console.log(searchInfo);
  if(searchInfo.length == 10){
    $.ajax({
      type: 'post',
      dataType: 'html',
      data:{
        day:searchInfo
      },
      cache: false,
      url: "<?= $this->Html->url(['controller' => 'Statistics', 'action' => 'forChat']) ?>",
      success: function(html){
         location.href = "<?=$this->Html->url(array('controller' => 'Statistics', 'action' => 'forChat'))?>";
      }
    });
  }
 });

if (select1.options[select1.selectedIndex].value == "1")
{
  console.log('月別選んだ');
  document.getElementById("selectName").style.display="";
  var input = document.getElementById("mainDatePeriod2");
  input.type = "hidden";
  select2.options[0] = new Option("2016");
  select2.options[1] = new Option("2017");
  searchInfo = $("select[name=selectName2]").val();
}

else if (select1.options[select1.selectedIndex].value == "2")
{
  console.log('日別選んだ');
  document.getElementById("selectName").style.display="";
  var input = document.getElementById("mainDatePeriod2");
  input.type = "hidden";
  select2.options[0] = new Option("2017-04");
  select2.options[1] = new Option("2017-05");
  select2.options[2] = new Option("2017-06");
  select2.options[3] = new Option("2017-07");
  searchInfo = $("select[name=selectName2]").val();
}

else if (select1.options[select1.selectedIndex].value == "3")
  {
    console.log('日選んだ');
    document.getElementById("selectName").style.display="none";
    var input = document.getElementById("mainDatePeriod2");
    input.type = "text";
    searchInfo = $("input[name=datefilter]").val();
  }
}

$(document).ready(function(){
  $('input[name="datefilter"]').daterangepicker({
    "locale": {
      "format": "YYYY-MM-DD",
      "daysOfWeek": [
        "日",
        "月",
        "火",
        "水",
        "木",
        "金",
        "土"
      ],
      "monthNames": [
        "1月",
        "2月",
        "3月",
        "4月",
        "5月",
        "6月",
        "7月",
        "8月",
        "9月",
        "10月",
        "11月",
        "12月"
      ],
    },
    singleDatePicker: true,
  },
  function(start, end, label) {
      var years = moment().diff(start, 'years');
  });

});
</script>

<div id="sincloApp">

  <div id='statistic_menu' class="p20trl">
    <!-- /* 対象期間選択エリア */ -->
    <condition-bar>
      <left-parts>
        対象期間：<?php echo $allInfo['data']; ?>
        <form name = "formName">
        <select name = "selectName1" onChange="functionName()">
          <option value="1" >年別</option>
          <option value="2">日別</option>
          <option value="3">月別</option>
        </select>
        <select name = "selectName2" id = "selectName">
        <?php if(strlen($allInfo['data'])==4) {?>
          <option value="4" selected hidden><?php echo $allInfo['data']; ?></option>
          <option>2016</option>
          <option>2017</option>
        <?php }
        else if(strlen($allInfo['data'])==7) {?>
          <option value="4" selected hidden><?php echo $allInfo['data']; ?></option>
          <option>2017/04</option>
          <option>2017/05</option>
          <option>2017/06</option>
          <option>2017/07</option>
        <?php }
        else if(strlen($allInfo['data'])==19) ?>
        <option value="4"><?php echo substr($allInfo['data'],0,10) ?></option>
        </select>
        <input type = "hidden"  name = "datefilter" id = "mainDatePeriod2" style = "width:8em;cursor:pointer;"></input>
      </form>
      </left-parts>
      <right-parts>
        <a href="#" id="outputCSV" class="btn-shadow blueBtn">CSV出力</a>
      </right-parts>
    </condition-bar>
    <!-- /* 対象期間選択エリア */ -->
  </div><!-- #statistic_menu -->

  <div id='statistics_content' class="p20trl">

  <!-- /* テーブル表示エリア */ -->
  <contents>
    <content>
      <content-table>
          <table class="tableFrame"  style="width:112px;position:absolute;left:0px;top:0px;z-index:3;height:36px;">
          <tr><th>統計項目/月別</th></tr> <!-- 固定ヘッダ -->
          </table>

          <div id="header_h">
          <table class="tableFrame tableData" style="width:100%"> <!-- 水平ヘッダ -->
          <?php if(strlen($allInfo['data'])==4) { ?>
          <th>2017/03</th>
          <th><?= $allInfo['data'] ?>/01</th>
          <th><?= $allInfo['data'] ?>/02</th>
          <th><?= $allInfo['data'] ?>/03</th>
          <th><?= $allInfo['data'] ?>/04</th>
          <th><?= $allInfo['data'] ?>/05</th>
          <th><?= $allInfo['data'] ?>/06</th>
          <th><?= $allInfo['data'] ?>/07</th>
          <th><?= $allInfo['data'] ?>/08</th>
          <th><?= $allInfo['data'] ?>/09</th>
          <th><?= $allInfo['data'] ?>/10</th>
          <th><?= $allInfo['data'] ?>/11</th>
          <th><?= $allInfo['data'] ?>/12</th>
          <th>合計値</th>
          <?php }
          else if(strlen($allInfo['data'])==7) { ?>
          <th>02-03</th>
          <th><?= $allInfo['data'] ?>/01</th>
          <th><?= $allInfo['data'] ?>/02</th>
          <th><?= $allInfo['data'] ?>/03</th>
          <th><?= $allInfo['data'] ?>/04</th>
          <th><?= $allInfo['data'] ?>/05</th>
          <th><?= $allInfo['data'] ?>/06</th>
          <th><?= $allInfo['data'] ?>/07</th>
          <th><?= $allInfo['data'] ?>/08</th>
          <th><?= $allInfo['data'] ?>/09</th>
          <th><?= $allInfo['data'] ?>/10</th>
          <th><?= $allInfo['data'] ?>/11</th>
          <th><?= $allInfo['data'] ?>/12</th>
          <th><?= $allInfo['data'] ?>/13</th>
          <th><?= $allInfo['data'] ?>/14</th>
          <th><?= $allInfo['data'] ?>/15</th>
          <th><?= $allInfo['data'] ?>/16</th>
          <th><?= $allInfo['data'] ?>/17</th>
          <th><?= $allInfo['data'] ?>/18</th>
          <th><?= $allInfo['data'] ?>/19</th>
          <th><?= $allInfo['data'] ?>/20</th>
          <th><?= $allInfo['data'] ?>/21</th>
          <th><?= $allInfo['data'] ?>/22</th>
          <th><?= $allInfo['data'] ?>/23</th>
          <th><?= $allInfo['data'] ?>/24</th>
          <th><?= $allInfo['data'] ?>/25</th>
          <th><?= $allInfo['data'] ?>/26</th>
          <th><?= $allInfo['data'] ?>/27</th>
          <th><?= $allInfo['data'] ?>/28</th>
          <th><?= $allInfo['data'] ?>/29</th>
          <th><?= $allInfo['data'] ?>/30</th>
          <th>合計値</th>
          <?php }
          else if(strlen($allInfo['data'])==19) { ?>
          <tr>
          <th>00-01</th>
          <th>01-02</th>
          <th>02-03</th>
          <th>03-04</th>
          <th>04-05</th>
          <th>05-06</th>
          <th>06-07</th>
          <th>07-08</th>
          <th>08-09</th>
          <th>09-10</th>
          <th>10-11</th>
          <th>11-12</th>
          <th>12-13</th>
          <th>13-14</th>
          <th>14-15</th>
          <th>15-16</th>
          <th>16-17</th>
          <th>17-18</th>
          <th>18-19</th>
          <th>19-20</th>
          <th>20-21</th>
          <th>21-22</th>
          <th>22-23</th>
          <th>23-24</th>
          <th>合計値</th>
          <?php } ?>
          </tr>
          </table>
          </div>

          <div id="header_v">
          <table class="tableFrame"> <!-- 垂直ヘッダ -->
          <tr style="height:5em;"><th>合計アクセス件数</th></tr>
          <tr style="height:5em;"><th>ウィジェット件数</th></tr>
          <tr style="height:5em;"><th>チャットリクエスト件数</th></tr>
          <tr style="height:5em;"><th>チャット応対件数</th></tr>
          <tr style="height:5em;"><th>チャット拒否件数</th></tr>
          <tr style="height:5em;"><th>チャット有効件数</th></tr>
          <tr style="height:5em;"><th>平均チャットリクエスト時間</th></tr>
          <tr style="height:5em;"><th>平均入室時間</th></tr>
          <tr style="height:5em;"><th>平均応答時間</th></tr>
          <tr style="height:5em;"><th>チャット応対率</th></tr>
          <tr style="height:5em;"><th>チャット有効率</th></tr>
          </table>
          </div>

          <div id="data">
          <div id = "data2">
          <table class="tableFrame tableData">
          <?php if(strlen($allInfo['data'])==4) { ?>
          <tr style="height:5em;"><td><?php echo $allInfo['accessNumber'][0][0][0]['count(*)']; ?></td><td><?php echo $allInfo['accessNumber'][1][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][2][0][0]['count(*)']; ?></td><td><?php echo $allInfo['accessNumber'][3][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][4][0][0]['count(*)']; ?></td><td><?php echo $allInfo['accessNumber'][5][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][6][0][0]['count(*)']; ?></td><td><?php echo $allInfo['accessNumber'][7][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][8][0][0]['count(*)']; ?></td><td><?php echo $allInfo['accessNumber'][9][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][10][0][0]['count(*)']; ?></td><td><?php echo $allInfo['accessNumber'][11][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['allAccessNumber']; ?></td></tr>
          <tr style="height:5em;"><td><?php echo $allInfo['widjetNumber'][0][0][0]['count(*)']; ?></td><td><?php echo $allInfo['widjetNumber'][1][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][2][0][0]['count(*)']; ?></td><td><?php echo $allInfo['widjetNumber'][3][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][4][0][0]['count(*)']; ?></td><td><?php echo $allInfo['widjetNumber'][5][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][6][0][0]['count(*)']; ?></td><td><?php echo $allInfo['widjetNumber'][7][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][8][0][0]['count(*)']; ?></td><td><?php echo $allInfo['widjetNumber'][9][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][10][0][0]['count(*)']; ?></td><td><?php echo $allInfo['widjetNumber'][11][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['allwidjetNumber']; ?></td></tr>
          <tr style="height:5em;"><td><?php echo $allInfo['requestNumber'][0][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['requestNumber'][1][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][2][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['requestNumber'][3][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][4][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['requestNumber'][5][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][6][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['requestNumber'][7][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][8][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['requestNumber'][9][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][10][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['requestNumber'][11][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['allrequest']; ?></td></tr>
          <tr style="height:5em;"><td><?php echo $allInfo['responseNumber'][0][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][1][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][2][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][3][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][4][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][5][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][6][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][7][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][8][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][9][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][10][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][11][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['allresponse']; ?></td></tr>
          <tr style="height:5em;"><td><?php echo $allInfo['effectivenessNumber'][0][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][1][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][2][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][3][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][4][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][5][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][6][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][7][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][8][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][9][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][10][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][11][0][0]['no']; ?></td>
          <td><?php echo $allInfo['allno']; ?></td></tr>
          <tr style="height:5em;"><td><?php echo $allInfo['effectivenessNumber'][0][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][1][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][2][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][3][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][4][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][5][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][6][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][7][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][8][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][9][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][10][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][11][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['allweffectiveness']; ?></td></tr>
          <tr style="height:5em;"><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td></tr>
          <tr style="height:5em;"><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td></tr>
          <tr style="height:5em;"><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td></tr>
          <tr style="height:5em;"><td><?php echo $allInfo['responseRate'][0]; ?></td><td><?php echo $allInfo['responseRate'][1]; ?></td>
          <td><?php echo $allInfo['responseRate'][2]; ?></td><td><?php echo $allInfo['responseRate'][3]; ?></td>
          <td><?php echo $allInfo['responseRate'][4]; ?></td><td><?php echo $allInfo['responseRate'][5]; ?></td>
          <td><?php echo $allInfo['responseRate'][6]; ?></td><td><?php echo $allInfo['responseRate'][7]; ?></td>
          <td><?php echo $allInfo['responseRate'][8]; ?></td><td><?php echo $allInfo['responseRate'][9]; ?></td>
          <td><?php echo $allInfo['responseRate'][10]; ?></td><td><?php echo $allInfo['responseRate'][11]; ?></td>
          <td><?php echo $allInfo['allResponseRate']; ?></td></tr>
          <tr style="height:5em;"><td><?php echo $allInfo['effectivenessRate'][0]; ?></td><td><?php echo $allInfo['effectivenessRate'][1]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][2]; ?></td><td><?php echo $allInfo['effectivenessRate'][3]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][4]; ?></td><td><?php echo $allInfo['effectivenessRate'][5]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][6]; ?></td><td><?php echo $allInfo['effectivenessRate'][7]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][8]; ?></td><td><?php echo $allInfo['effectivenessRate'][9]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][10]; ?></td><td><?php echo $allInfo['effectivenessRate'][11]; ?></td>
          <td><?php echo $allInfo['allEffectivenessRate']; ?></td></tr>
          <?php }
          else if(strlen($allInfo['data'])==7) { ?>
          <tr style="height:5em;"><td><?php echo $allInfo['accessNumber'][0][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['accessNumber'][1][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][2][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['accessNumber'][3][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][4][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['accessNumber'][5][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][6][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['accessNumber'][7][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][8][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['accessNumber'][9][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][10][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['accessNumber'][11][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][12][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['accessNumber'][13][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][14][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['accessNumber'][15][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][16][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['accessNumber'][17][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][18][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['accessNumber'][19][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][20][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['accessNumber'][21][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][22][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['accessNumber'][23][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][24][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['accessNumber'][25][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][26][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['accessNumber'][27][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][28][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['accessNumber'][29][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['allAccessNumber']; ?></td></tr>
          <tr style="height:5em;"><td><?php echo $allInfo['widjetNumber'][0][0][0]['count(tw.id)']; ?></td><td><?php echo $allInfo['widjetNumber'][1][0][0]['count(tw.id)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][2][0][0]['count(tw.id)']; ?></td><td><?php echo $allInfo['widjetNumber'][3][0][0]['count(tw.id)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][4][0][0]['count(tw.id)']; ?></td><td><?php echo $allInfo['widjetNumber'][5][0][0]['count(tw.id)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][6][0][0]['count(tw.id)']; ?></td><td><?php echo $allInfo['widjetNumber'][7][0][0]['count(tw.id)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][8][0][0]['count(tw.id)']; ?></td><td><?php echo $allInfo['widjetNumber'][9][0][0]['count(tw.id)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][10][0][0]['count(tw.id)']; ?></td><td><?php echo $allInfo['widjetNumber'][11][0][0]['count(tw.id)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][12][0][0]['count(tw.id)']; ?></td><td><?php echo $allInfo['widjetNumber'][13][0][0]['count(tw.id)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][14][0][0]['count(tw.id)']; ?></td><td><?php echo $allInfo['widjetNumber'][15][0][0]['count(tw.id)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][16][0][0]['count(tw.id)']; ?></td><td><?php echo $allInfo['widjetNumber'][17][0][0]['count(tw.id)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][18][0][0]['count(tw.id)']; ?></td><td><?php echo $allInfo['widjetNumber'][19][0][0]['count(tw.id)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][20][0][0]['count(tw.id)']; ?></td><td><?php echo $allInfo['widjetNumber'][21][0][0]['count(tw.id)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][22][0][0]['count(tw.id)']; ?></td><td><?php echo $allInfo['widjetNumber'][23][0][0]['count(tw.id)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][24][0][0]['count(tw.id)']; ?></td><td><?php echo $allInfo['widjetNumber'][25][0][0]['count(tw.id)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][26][0][0]['count(tw.id)']; ?></td><td><?php echo $allInfo['widjetNumber'][27][0][0]['count(tw.id)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][28][0][0]['count(tw.id)']; ?></td><td><?php echo $allInfo['widjetNumber'][29][0][0]['count(tw.id)']; ?></td>
          <td><?php echo $allInfo['allwidjetNumber']; ?></td></tr>
          <tr style="height:5em;"><td><?php echo $allInfo['requestNumber'][0][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['requestNumber'][1][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][2][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['requestNumber'][3][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][4][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['requestNumber'][5][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][6][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['requestNumber'][7][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][8][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['requestNumber'][9][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][10][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['requestNumber'][11][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][12][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['requestNumber'][13][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][14][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['requestNumber'][15][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][16][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['requestNumber'][17][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][18][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['requestNumber'][19][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][20][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['requestNumber'][21][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][22][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['requestNumber'][23][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][24][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['requestNumber'][25][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][26][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['requestNumber'][27][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][28][0][0]['count(th.id)']; ?></td><td><?php echo $allInfo['requestNumber'][29][0][0]['count(th.id)']; ?></td>
          <td><?php echo $allInfo['allrequest']; ?></td></tr>
          <tr style="height:5em;"><td><?php echo $allInfo['responseNumber'][0][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][1][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][2][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][3][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][4][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][5][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][6][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][7][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][8][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][9][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][10][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][11][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][12][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][13][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][14][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][15][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][16][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][17][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][18][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][19][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][20][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][21][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][22][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][23][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][24][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][25][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][26][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][27][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][28][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][29][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['allresponse']; ?></td></tr>
          <tr style="height:5em;"><td><?php echo $allInfo['effectivenessNumber'][0][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][1][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][2][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][3][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][4][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][5][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][6][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][7][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][8][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][9][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][10][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][11][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][12][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][13][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][14][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][15][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][16][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][17][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][18][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][19][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][20][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][21][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][22][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][23][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][24][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][25][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][26][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][27][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][28][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][29][0][0]['no']; ?></td>
          <td><?php echo $allInfo['allno']; ?></td></tr>
          <tr style="height:5em;"><td><?php echo $allInfo['effectivenessNumber'][0][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][1][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][2][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][3][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][4][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][5][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][6][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][7][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][8][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][9][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][10][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][11][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][12][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][13][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][14][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][15][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][16][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][17][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][18][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][19][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][20][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][21][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][22][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][23][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][24][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][25][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][26][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][27][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][28][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][29][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['allweffectiveness']; ?></td></tr>
          <tr style="height:5em;"><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td></tr>
          <tr style="height:5em;"><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td></tr>
          <tr style="height:5em;"><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td></tr>
          <tr style="height:5em;"><td><?php echo $allInfo['responseRate'][0]; ?></td><td><?php echo $allInfo['responseRate'][1]; ?></td>
          <td><?php echo $allInfo['responseRate'][2]; ?></td><td><?php echo $allInfo['responseRate'][3]; ?></td>
          <td><?php echo $allInfo['responseRate'][4]; ?></td><td><?php echo $allInfo['responseRate'][5]; ?></td>
          <td><?php echo $allInfo['responseRate'][6]; ?></td><td><?php echo $allInfo['responseRate'][7]; ?></td>
          <td><?php echo $allInfo['responseRate'][8]; ?></td><td><?php echo $allInfo['responseRate'][9]; ?></td>
          <td><?php echo $allInfo['responseRate'][10]; ?></td><td><?php echo $allInfo['responseRate'][11]; ?></td>
          <td><?php echo $allInfo['responseRate'][12]; ?></td><td><?php echo $allInfo['responseRate'][13]; ?></td>
          <td><?php echo $allInfo['responseRate'][14]; ?></td><td><?php echo $allInfo['responseRate'][15]; ?></td>
          <td><?php echo $allInfo['responseRate'][16]; ?></td><td><?php echo $allInfo['responseRate'][17]; ?></td>
          <td><?php echo $allInfo['responseRate'][18]; ?></td><td><?php echo $allInfo['responseRate'][19]; ?></td>
          <td><?php echo $allInfo['responseRate'][20]; ?></td><td><?php echo $allInfo['responseRate'][21]; ?></td>
          <td><?php echo $allInfo['responseRate'][22]; ?></td><td><?php echo $allInfo['responseRate'][23]; ?></td>
          <td><?php echo $allInfo['responseRate'][24]; ?></td><td><?php echo $allInfo['responseRate'][25]; ?></td>
          <td><?php echo $allInfo['responseRate'][26]; ?></td><td><?php echo $allInfo['responseRate'][27]; ?></td>
          <td><?php echo $allInfo['responseRate'][28]; ?></td><td><?php echo $allInfo['responseRate'][29]; ?></td>
          <td><?php echo $allInfo['allResponseRate']; ?></td></tr>
          <tr style="height:5em;"><td><?php echo $allInfo['effectivenessRate'][0]; ?></td><td><?php echo $allInfo['effectivenessRate'][1]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][2]; ?></td><td><?php echo $allInfo['effectivenessRate'][3]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][4]; ?></td><td><?php echo $allInfo['effectivenessRate'][5]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][6]; ?></td><td><?php echo $allInfo['effectivenessRate'][7]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][8]; ?></td><td><?php echo $allInfo['effectivenessRate'][9]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][10]; ?></td><td><?php echo $allInfo['effectivenessRate'][11]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][12]; ?></td><td><?php echo $allInfo['effectivenessRate'][13]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][14]; ?></td><td><?php echo $allInfo['effectivenessRate'][15]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][16]; ?></td><td><?php echo $allInfo['effectivenessRate'][17]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][18]; ?></td><td><?php echo $allInfo['effectivenessRate'][19]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][20]; ?></td><td><?php echo $allInfo['effectivenessRate'][21]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][22]; ?></td><td><?php echo $allInfo['effectivenessRate'][23]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][24]; ?></td><td><?php echo $allInfo['effectivenessRate'][25]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][26]; ?></td><td><?php echo $allInfo['effectivenessRate'][27]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][28]; ?></td><td><?php echo $allInfo['effectivenessRate'][29]; ?></td>
          <td><?php echo $allInfo['allEffectivenessRate']; ?></td></tr>
          <?php }
          else if(strlen($allInfo['data'])==19) { ?>
          <tr style="height:5em;"><td><?php echo $allInfo['accessNumber'][0][0][0]['count(*)']; ?></td><td><?php echo $allInfo['accessNumber'][1][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][2][0][0]['count(*)']; ?></td><td><?php echo $allInfo['accessNumber'][3][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][4][0][0]['count(*)']; ?></td><td><?php echo $allInfo['accessNumber'][5][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][6][0][0]['count(*)']; ?></td><td><?php echo $allInfo['accessNumber'][7][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][8][0][0]['count(*)']; ?></td><td><?php echo $allInfo['accessNumber'][9][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][10][0][0]['count(*)']; ?></td><td><?php echo $allInfo['accessNumber'][11][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][12][0][0]['count(*)']; ?></td><td><?php echo $allInfo['accessNumber'][13][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][14][0][0]['count(*)']; ?></td><td><?php echo $allInfo['accessNumber'][15][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][16][0][0]['count(*)']; ?></td><td><?php echo $allInfo['accessNumber'][17][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][18][0][0]['count(*)']; ?></td><td><?php echo $allInfo['accessNumber'][19][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][20][0][0]['count(*)']; ?></td><td><?php echo $allInfo['accessNumber'][21][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['accessNumber'][22][0][0]['count(*)']; ?></td><td><?php echo $allInfo['accessNumber'][23][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['allAccessNumber']; ?></td></tr>
          <tr style="height:5em;"><td><?php echo $allInfo['widjetNumber'][0][0][0]['count(*)']; ?></td><td><?php echo $allInfo['widjetNumber'][1][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][2][0][0]['count(*)']; ?></td><td><?php echo $allInfo['widjetNumber'][3][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][4][0][0]['count(*)']; ?></td><td><?php echo $allInfo['widjetNumber'][5][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][6][0][0]['count(*)']; ?></td><td><?php echo $allInfo['widjetNumber'][7][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][8][0][0]['count(*)']; ?></td><td><?php echo $allInfo['widjetNumber'][9][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][10][0][0]['count(*)']; ?></td><td><?php echo $allInfo['widjetNumber'][11][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][12][0][0]['count(*)']; ?></td><td><?php echo $allInfo['widjetNumber'][13][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][14][0][0]['count(*)']; ?></td><td><?php echo $allInfo['widjetNumber'][15][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][16][0][0]['count(*)']; ?></td><td><?php echo $allInfo['widjetNumber'][17][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][18][0][0]['count(*)']; ?></td><td><?php echo $allInfo['widjetNumber'][19][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][20][0][0]['count(*)']; ?></td><td><?php echo $allInfo['widjetNumber'][21][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['widjetNumber'][22][0][0]['count(*)']; ?></td><td><?php echo $allInfo['widjetNumber'][23][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['allwidjetNumber']; ?></td></tr>
          <tr style="height:5em;"><td><?php echo $allInfo['requestNumber'][0][0][0]['count(*)']; ?></td><td><?php echo $allInfo['requestNumber'][1][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][2][0][0]['count(*)']; ?></td><td><?php echo $allInfo['requestNumber'][3][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][4][0][0]['count(*)']; ?></td><td><?php echo $allInfo['requestNumber'][5][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][6][0][0]['count(*)']; ?></td><td><?php echo $allInfo['requestNumber'][7][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][8][0][0]['count(*)']; ?></td><td><?php echo $allInfo['requestNumber'][9][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][10][0][0]['count(*)']; ?></td><td><?php echo $allInfo['requestNumber'][11][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][12][0][0]['count(*)']; ?></td><td><?php echo $allInfo['requestNumber'][13][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][14][0][0]['count(*)']; ?></td><td><?php echo $allInfo['requestNumber'][15][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][16][0][0]['count(*)']; ?></td><td><?php echo $allInfo['requestNumber'][17][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][18][0][0]['count(*)']; ?></td><td><?php echo $allInfo['requestNumber'][19][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][20][0][0]['count(*)']; ?></td><td><?php echo $allInfo['requestNumber'][21][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['requestNumber'][22][0][0]['count(*)']; ?></td><td><?php echo $allInfo['requestNumber'][23][0][0]['count(*)']; ?></td>
          <td><?php echo $allInfo['allrequest']; ?></td></tr>
          <tr style="height:5em;"><td><?php echo $allInfo['responseNumber'][0][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][1][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][2][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][3][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][4][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][5][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][6][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][7][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][8][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][9][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][10][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][11][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][12][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][13][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][14][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][15][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][16][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][17][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][18][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][19][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][20][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][21][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['responseNumber'][22][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td><td><?php echo $allInfo['responseNumber'][23][0][0]['count(distinct message_distinction,t_histories_id)']; ?></td>
          <td><?php echo $allInfo['allresponse']; ?></td></tr>
          <tr style="height:5em;"><td><?php echo $allInfo['effectivenessNumber'][0][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][1][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][2][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][3][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][4][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][5][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][6][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][7][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][8][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][9][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][10][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][11][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][12][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][13][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][14][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][15][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][16][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][17][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][18][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][19][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][20][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][21][0][0]['no']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][22][0][0]['no']; ?></td><td><?php echo $allInfo['effectivenessNumber'][23][0][0]['no']; ?></td>
          <td><?php echo $allInfo['allno']; ?></td></tr>
          <tr style="height:5em;"><td><?php echo $allInfo['effectivenessNumber'][0][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][1][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][2][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][3][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][4][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][5][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][6][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][7][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][8][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][9][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][10][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][11][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][12][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][13][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][14][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][15][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][16][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][17][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][18][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][19][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][20][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][21][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['effectivenessNumber'][22][0][0]['yukou']; ?></td><td><?php echo $allInfo['effectivenessNumber'][23][0][0]['yukou']; ?></td>
          <td><?php echo $allInfo['allweffectiveness']; ?></td></tr>
          <tr style="height:5em;"><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td></tr>
          <tr style="height:5em;"><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td></tr>
          <tr style="height:5em;"><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td></tr>
          <tr style="height:5em;"><td><?php echo $allInfo['responseRate'][0]; ?></td><td><?php echo $allInfo['responseRate'][1]; ?></td>
          <td><?php echo $allInfo['responseRate'][2]; ?></td><td><?php echo $allInfo['responseRate'][3]; ?></td>
          <td><?php echo $allInfo['responseRate'][4]; ?></td><td><?php echo $allInfo['responseRate'][5]; ?></td>
          <td><?php echo $allInfo['responseRate'][6]; ?></td><td><?php echo $allInfo['responseRate'][7]; ?></td>
          <td><?php echo $allInfo['responseRate'][8]; ?></td><td><?php echo $allInfo['responseRate'][9]; ?></td>
          <td><?php echo $allInfo['responseRate'][10]; ?></td><td><?php echo $allInfo['responseRate'][11]; ?></td>
          <td><?php echo $allInfo['responseRate'][12]; ?></td><td><?php echo $allInfo['responseRate'][13]; ?></td>
          <td><?php echo $allInfo['responseRate'][14]; ?></td><td><?php echo $allInfo['responseRate'][15]; ?></td>
          <td><?php echo $allInfo['responseRate'][16]; ?></td><td><?php echo $allInfo['responseRate'][17]; ?></td>
          <td><?php echo $allInfo['responseRate'][18]; ?></td><td><?php echo $allInfo['responseRate'][19]; ?></td>
          <td><?php echo $allInfo['responseRate'][20]; ?></td><td><?php echo $allInfo['responseRate'][21]; ?></td>
          <td><?php echo $allInfo['responseRate'][22]; ?></td><td><?php echo $allInfo['responseRate'][23]; ?></td>
          <td><?php echo $allInfo['allResponseRate']; ?></td></tr>
          <tr style="height:5em;"><td><?php echo $allInfo['effectivenessRate'][0]; ?></td><td><?php echo $allInfo['effectivenessRate'][1]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][2]; ?></td><td><?php echo $allInfo['effectivenessRate'][3]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][4]; ?></td><td><?php echo $allInfo['effectivenessRate'][5]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][6]; ?></td><td><?php echo $allInfo['effectivenessRate'][7]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][8]; ?></td><td><?php echo $allInfo['effectivenessRate'][9]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][10]; ?></td><td><?php echo $allInfo['effectivenessRate'][11]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][12]; ?></td><td><?php echo $allInfo['effectivenessRate'][13]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][14]; ?></td><td><?php echo $allInfo['effectivenessRate'][15]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][16]; ?></td><td><?php echo $allInfo['effectivenessRate'][17]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][18]; ?></td><td><?php echo $allInfo['effectivenessRate'][19]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][20]; ?></td><td><?php echo $allInfo['effectivenessRate'][21]; ?></td>
          <td><?php echo $allInfo['effectivenessRate'][22]; ?></td><td><?php echo $allInfo['effectivenessRate'][23]; ?></td>
          <td><?php echo $allInfo['allEffectivenessRate']; ?></td></tr>
          <?php } ?>
          </table>
          </div>
          </div>
      </content-table>
    </content>
  </contents>
<script type="text/javascript">
function scroll(){
  document.getElementById("header_h").scrollLeft= document.getElementById("data2").scrollLeft;// データ部のスクロールをヘッダに反映
  document.getElementById("header_v").scrollTop = document.getElementById("data2").scrollTop;// データ部のスクロールをヘッダに反映
}
document.getElementById("data2").onscroll=scroll;
</script>
  <!-- /* テーブル表示エリア */ -->
</div><!-- #statistics_content -->
</div><!-- #sincloApp -->