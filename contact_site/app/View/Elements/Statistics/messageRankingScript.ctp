<script type="text/javascript">
  function timeChange()　{
    var chosenDateFormat = document.forms.StatisticsForChatForm.dateFormat;

    //  selectで月別を選択した場合
    if (chosenDateFormat.options[chosenDateFormat.selectedIndex].value == "月別")
    {
      document.getElementById("monthlyForm").style.display="";
      document.getElementById("daylyForm").style.display="none";
      document.getElementById("hourlyForm").style.dispzlay="none";
      document.getElementById("monthlyForm").value = "";
      document.getElementById("triangle").style.borderTop = "0px";
    }
    //selectで日別を選択した場合
    else if (chosenDateFormat.options[chosenDateFormat.selectedIndex].value == "日別")
    {
      document.getElementById("monthlyForm").style.display="none";
      document.getElementById("daylyForm").style.display="";
      document.getElementById("hourlyForm").style.display="none";
      document.getElementById("hourlyForm").value = "";
      document.getElementById("triangle").style.borderTop = "0px";
    }
    //selectで時別を選択した場合
    else if (chosenDateFormat.options[chosenDateFormat.selectedIndex].value == "時別")
    {
      var value = new Date().getFullYear() + "/" + ("0" + (new Date().getMonth() + 1)).slice(-2) + "/01";
      document.getElementById("monthlyForm").style.display="none";
      document.getElementById("daylyForm").style.display="none";
      document.getElementById("hourlyForm").style.display="";
      document.getElementById("hourlyForm").value = '選択してください';
      document.getElementById("hourlyForm").options = value;
      document.getElementById("triangle").style.borderTop = "6px solid";
    }
  }
  </script>
