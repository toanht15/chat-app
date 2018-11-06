<div id='chat_history_idx' class="card-shadow" ng-app="sincloApp" ng-controller="MainController" >
  <div id='history_title'>
    <div class="fLeft"><i class="fal fa-folder fa-2x"></i></div>
      <h1>リードリスト出力</h1>
  </div>
  <div id="lead_list_body">
    <?=$this->element('TLeadLists/detail')?>
  </div>
</div>