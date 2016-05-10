<div class="card-shadow m10b">
    <div id='customer_subtitle'>
        <div class="fLeft"><?= $this->Html->image('sub_icon.png', array('alt' => '詳細情報', 'width' => 25, 'height' => 25, 'style' => 'margin: 0 15px 0 auto;transform: rotate(45deg);')) ?></div>
        <h1>詳細情報</h1>
    </div>
</div>
<div id="customer_detail" class="card-shadow m10b p10x">
    <h2>基本情報</h2>
    <ul class="p20l">
        <li>
          <span>アクセスID：</span><br>
            {{detailData.accessId}}
        </li>
        <li>
          <span>IPアドレス：</span><br>
            {{detailData.ipAddress}}
        </li>
        <li>
          <span>ユーザーエージェント：</span><br>
            {{detailData.userAgent}}
        </li>
        <li>
          <span>閲覧中ページ：</span><br>
          <a ng-href='{{detailData.url}}' target='showBlack'>{{detailData.title}}</a>
        </li>
    </ul>
</div>
<div id="chat-area" class="card-shadow p10x">
    <h2>チャット</h2>
    <ul class="naviBtn p0">
        <li class="w50 tCenter on">チャット</li>
        <li class="w50 tCenter">メモ</li>
    </ul>
    <div id="chatContent">
        <ul id="chatTalk" >
        </ul>
        <div style="position: relative">
          <textarea rows="5" id="sendMessage" placeholder="メッセージ入力後、Ctrl + Enterで送信"></textarea>
          <span id="sinclo_sendbtn" onclick="chatApi.pushMessage()">＋</span>
        </div>
    </div>
</div>
