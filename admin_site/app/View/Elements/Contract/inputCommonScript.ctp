<script type = "text/javascript">
  $(function(){
    var changeDayInputState = function() {
      var checked = $('#MCompanyTrialFlg').prop("checked");
      if(checked) {
        inputEnabled($('#MAgreementsTrialStartDay'));
        inputEnabled($('#MAgreementsTrialEndDay'));
        inputDisabled($('#MAgreementsAgreementStartDay'));
        inputDisabled($('#MAgreementsAgreementEndDay'));
      } else {
        inputDisabled($('#MAgreementsTrialStartDay'));
        inputDisabled($('#MAgreementsTrialEndDay'));
        inputEnabled($('#MAgreementsAgreementStartDay'));
        inputEnabled($('#MAgreementsAgreementEndDay'));
      }
    }

    //日付入力欄にDatePickerを付与
    var datePickerOptions = {
      dateFormat: "yy-mm-dd"
    };
    $('#MAgreementsTrialStartDay').datepicker(datePickerOptions);
    $('#MAgreementsTrialEndDay').datepicker(datePickerOptions);
    $('#MAgreementsAgreementStartDay').datepicker(datePickerOptions);
    $('#MAgreementsAgreementEndDay').datepicker(datePickerOptions);

    if(typeof(io) !== 'undefined') {
      socket = io.connect("<?= C_NODE_SERVER_ADDR . C_NODE_SERVER_WS_PORT ?>", {port: 9090, rememberTransport: false});
    }

    var inputDisabled = function(jqObj) {
      jqObj.datepicker( "option", "disabled", true ).prop("readonly", true).addClass("disabled").prev('div').find('span').first().removeClass('require');
    }

    var inputEnabled = function(jqObj) {
      jqObj.datepicker( "option", "disabled", false ).prop("readonly", false).removeClass("disabled").prev('div').find('span').first().addClass('require')
    }

    $('#MCompanyTrialFlg').on('change', function(event){
      changeDayInputState();
    });

    // 初回読み込み時にUIを適用するため一度呼び出す
    changeDayInputState();
  });
</script>