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
function functionName()　{
console.log('入っています1');
var select1 = document.forms.formName.selectName1; //変数select1を宣言
var select2 = document.forms.formName.selectName2; //変数select2を宣言

select2.options.length = 0; // 選択肢の数がそれぞれに異なる場合、これが重要

if (select1.options[select1.selectedIndex].value == "1")
{
  document.getElementById("selectName").style.display="";
  var input = document.getElementById("mainDatePeriod2");
  input.type = "hidden";
  select2.options[0] = new Option("2016");
  select2.options[1] = new Option("2017");
}

else if (select1.options[select1.selectedIndex].value == "2")
{
    document.getElementById("selectName").style.display="";
  var input = document.getElementById("mainDatePeriod2");
  input.type = "hidden";
select2.options[0] = new Option("2017/04");
select2.options[1] = new Option("2017/05");
select2.options[2] = new Option("2017/06");
select2.options[3] = new Option("2017/07");
}

else if (select1.options[select1.selectedIndex].value == "3")
  {
    document.getElementById("selectName").style.display="none";
    var input = document.getElementById("mainDatePeriod2");
    input.type = "text";
  }
}

$(document).ready(function(){
  $('input[name="datefilter"]').daterangepicker({
    "locale": {
      "format": "YYYY/MM/DD",
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
        対象期間：
        <form name = "formName">
        <select name = "selectName1" onChange="functionName()">
          <option value="1" >年別</option>
          <option value="2" selected>日別</option>
          <option value="3">月別</option>
        </select>
        <select name = "selectName2" id = "selectName">
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
          <tr style="height:5em;"><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td><td>9</td><td>10</td><td>11</td><td>12</td><td>13</td><td>14</td><td>15</td><td>16</td><td>17</td><td>18</td><td>19</td><td>20</td><td>21</td><td>22</td><td>23</td></tr>
          <tr style="height:5em;"><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td></tr>
          <tr style="height:5em;"><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td></tr>
          <tr style="height:5em;"><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td></tr>
          <tr style="height:5em;"><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td></tr>
          <tr style="height:5em;"><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td></tr>
          <tr style="height:5em;"><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td></tr>
          <tr style="height:5em;"><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td></tr>
          <tr style="height:5em;"><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td></tr>
          <tr style="height:5em;"><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td></tr>
          <tr style="height:5em;"><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td></tr>
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