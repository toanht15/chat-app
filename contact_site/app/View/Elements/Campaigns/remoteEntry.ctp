<script type="text/javascript">
    popupEvent.closePopup = function(){
        var campaignId = document.getElementById('CampaignId').value;
        var name = document.getElementById('CampaignName').value;
        var parameter = document.getElementById('CampaignParameter').value;
        var comment = document.getElementById('CampaignComment').value;
        $.ajax({
            type: "post",
            url: "<?=$this->Html->url('/Campaigns/remoteSaveEntryForm')?>",
            data: {
                campaignId: campaignId,
                name: name,
                parameter: parameter,
                comment: comment
            },
            cache: false,
            dataType: "JSON",
            success: function(data){
                var keys = Object.keys(data), num = 0;
                $(".error-message").remove();

                if ( keys.length === 0 ) {
                    location.href = "<?=$this->Html->url(array('controller' => 'TDictionaries', 'action' => 'index'))?>";
                    return false;
                }
                for (var i = 0; i < keys.length; i++) {
                    if ( data[keys[i]].length > 0 ) {
                        var target = $("[name='data[TDictionary][" + keys[i] + "]']");
                        for (var u = 0; u < data[keys[i]].length; u++) {
                            target.after("<p class='error-message hide'>" + data[keys[i]][u] + "</p>");
                            num ++;
                        }
                    }
                }
                if ( num > 0 ) {
                    var newHeight = $("#popup-content").height() + (num * 15);
                    $("#popup-frame").animate({
                        height: newHeight + "px"
                    }, {
                        duration: 500,
                        complete: function(){
                            $(".error-message.hide").removeClass("hide");
                            $(this).css("overflow", "");
                        }
                    });
                }
            }
        });
    };


</script>
<?= $this->Form->create('Campaign', array('action' => 'add')); ?>
    <div class="form01">
        <?= $this->Form->input('id', array('type' => 'hidden')); ?>
        <div>
          <span>キャンペーン名
            <?= $this->Form->input('name', array('placeholder' => 'キャンペーン名', 'div' => false, 'label' => false, 'maxlength' => 100)) ?>
          </span></div>
        <div>
          <span>URLパラメータ
            <?= $this->Form->input('parameter', array('placeholder' => 'URLパラメータ', 'div' => false, 'label' => false, 'maxlength' => 100)) ?>
          </span>
        </div>
        <div>
          <span>コメント</span>
          <?= $this->Form->textarea('comment', array('placeholder' => 'コメント', 'div' => false, 'label' => false, 'maxlength' => 300)) ?>
        </div>
    </div>
<?= $this->Form->end(); ?>
