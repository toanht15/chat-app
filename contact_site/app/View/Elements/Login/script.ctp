<script type="text/javascript">
	var elmEv;
	(function(){
		elmEv = {
			submit: {
				func: function () {
					MUserIndexForm.submit();
				}
			}
		};
	})();
	window.onload = function(){
		MUserFormButton.addEventListener('click', elmEv.submit.func);
	};
</script>