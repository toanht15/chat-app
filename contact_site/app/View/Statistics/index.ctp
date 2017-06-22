<script type = "text/javascript">
  var app = new Vue({
    el: '#app',
    data: {
      message: 'Hello Vue!'
    }
  })
function setData(momentData) {
  if(momentData == '0'){
    document.getElementById("timeData").style.display = "";
    document.getElementById("dayData").style.display = "none";
    document.getElementById("monthData").style.display = "none";
  }
  else if(momentData == ('1')) {
    document.getElementById("timeData").style.display = "none";
    document.getElementById("dayData").style.display = "";
    document.getElementById("monthData").style.display = "none";
  }
  else if(momentData == ('2')) {
    document.getElementById("timeData").style.display = "none";
    document.getElementById("dayData").style.display = "none";
    document.getElementById("monthData").style.display = "";
  }
}
</script>

<script src="https://unpkg.com/vue"></script>
<div id='statistics_idx' class="card-shadow">

<div id="app">
<?= $test ?>
</div>


  <div id='statistics_add_title'>
    <div class="fLeft"><?= $this->Html->image('campaign_g.png', array('alt' => 'キャンペーン管理', 'width' => 30, 'height' => 30, 'style' => 'margin: 0 auto')) ?></div>
      <h1>統計・分析<span id="sortMessage"></span></h1>
    </div>

  <div id='tcampaigns_list' style = 'padding: 5px 20px 20px 20px;'>
  <span id="searchPeriod2">検索期間:</span>
  <select onChange="setData(this[this.selectedIndex].value)">
    <option value = "0"> 時別</option>
    <option value = "1"> 日別</option>
    <option value = "2"> 月別</option>
  </select>
    <table id = "timeData">
      <thead>
        <tr>
          <th>統計項目</th>
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
          <th>23-00</th>
          <th>合計・平均</th>
        </tr>
        <tr>
         <th class = "evenNumber">合計アクセス件数</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
        </tr>
        <tr>
          <th class = "oddNumber">ウィジェット表示件数</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
        </tr>
        <tr>
          <th class = "evenNumber">チャットリクエスト件数</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
        </tr>
        <tr>
          <th class = "oddNumber">チャット応対件数</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
        </tr>
        <tr>
          <th class = "evenNumber">チャット拒否件数</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
        </tr>
        <tr>
          <th class = "oddNumber">チャット有効件数</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
        </tr>
        <tr>
          <th class = "evenNumber">平均チャットリクエスト時間</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
        </tr>
        <tr>
          <th class = "oddNumber">平均入室時間</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
        </tr>
        <tr>
          <th class = "evenNumber">平均応答時間</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
        </tr>
        <tr>
          <th class = "oddNumber">平均応対時間</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
        </tr>
        <tr>
          <th class = "evenNumber">合計応対時間</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
        </tr>
        <tr>
          <th class = "oddNumber">チャット応対率</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
        </tr>
        <tr>
          <th class = "evenNumber">チャット有効率</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
        </tr>
      </thead>
    </table>

    <table id = "dayData" style="display:none">
      <thead>
        <tr>
          <th>統計項目</th>
          <th>2017/04/01</th>
          <th>2017/04/02</th>
          <th>2017/04/03</th>
          <th>2017/04/04</th>
          <th>2017/04/05</th>
          <th>2017/04/06</th>
          <th>2017/04/07</th>
          <th>2017/04/08</th>
          <th>2017/04/09</th>
          <th>2017/04/10</th>
          <th>2017/04/11</th>
          <th>2017/04/12</th>
          <th>2017/04/13</th>
          <th>2017/04/14</th>
          <th>2017/04/15</th>
          <th>2017/04/16</th>
          <th>2017/04/17</th>
          <th>2017/04/18</th>
          <th>2017/04/19</th>
          <th>2017/04/20</th>
          <th>2017/04/21</th>
          <th>2017/04/22</th>
          <th>2017/04/23</th>
          <th>2017/04/24</th>
          <th>2017/04/25</th>
          <th>2017/04/26</th>
          <th>2017/04/27</th>
          <th>2017/04/28</th>
          <th>2017/04/29</th>
          <th>2017/04/30</th>
          <th>合計・平均</th>
        </tr>
        <tr>
         <th class = "evenNumber">合計アクセス件数</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
        </tr>
        <tr>
          <th class = "oddNumber">ウィジェット表示件数</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
        </tr>
        <tr>
          <th class = "evenNumber">チャットリクエスト件数</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
        </tr>
        <tr>
          <th class = "oddNumber">チャット応対件数</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
        </tr>
        <tr>
          <th class = "evenNumber">チャット拒否件数</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
        </tr>
        <tr>
          <th class = "oddNumber">チャット有効件数</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
        </tr>
        <tr>
          <th class = "evenNumber">平均チャットリクエスト時間</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
        </tr>
        <tr>
          <th class = "oddNumber">平均入室時間</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
        </tr>
        <tr>
          <th class = "evenNumber">平均応答時間</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
        </tr>
        <tr>
          <th class = "oddNumber">平均応対時間</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
        </tr>
        <tr>
          <th class = "evenNumber">合計応対時間</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
        </tr>
        <tr>
          <th class = "oddNumber">チャット応対率</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
        </tr>
        <tr>
          <th class = "evenNumber">チャット有効率</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
        </tr>
      </thead>
    </table>

    <table id = "monthData" style="display: none;">
      <thead>
        <tr>
          <th>統計項目</th>
          <th>2016/01</th>
          <th>2016/02</th>
          <th>2016/03</th>
          <th>2016/04</th>
          <th>2016/05</th>
          <th>2016/06</th>
          <th>2016/07</th>
          <th>2016/08</th>
          <th>2016/09</th>
          <th>2016/10</th>
          <th>2016/11</th>
          <th>2016/12</th>
          <th>合計・平均</th>
        </tr>
        <tr>
         <th class = "evenNumber">合計アクセス件数</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber"><?= $test ?></th>
         <th class = "evenNumber">{{detail.stayCount}} </th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
         <th class = "evenNumber">100</th>
        </tr>
        <tr>
          <th class = "oddNumber">ウィジェット表示件数</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
          <th class = "oddNumber">40</th>
        </tr>
        <tr>
          <th class = "evenNumber">チャットリクエスト件数</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
          <th class = "evenNumber">20</th>
        </tr>
        <tr>
          <th class = "oddNumber">チャット応対件数</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
          <th class = "oddNumber">10</th>
        </tr>
        <tr>
          <th class = "evenNumber">チャット拒否件数</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
          <th class = "evenNumber">4</th>
        </tr>
        <tr>
          <th class = "oddNumber">チャット有効件数</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
          <th class = "oddNumber">2</th>
        </tr>
        <tr>
          <th class = "evenNumber">平均チャットリクエスト時間</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
          <th class = "evenNumber">0:05:12</th>
        </tr>
        <tr>
          <th class = "oddNumber">平均入室時間</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
        </tr>
        <tr>
          <th class = "evenNumber">平均応答時間</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
        </tr>
        <tr>
          <th class = "oddNumber">平均応対時間</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
          <th class = "oddNumber">0:00:49</th>
        </tr>
        <tr>
          <th class = "evenNumber">合計応対時間</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
          <th class = "evenNumber">0:00:49</th>
        </tr>
        <tr>
          <th class = "oddNumber">チャット応対率</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
          <th class = "oddNumber">60%</th>
        </tr>
        <tr>
          <th class = "evenNumber">チャット有効率</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
          <th class = "evenNumber">40%</th>
        </tr>
      </thead>
    </table>
  </div>


</div>
