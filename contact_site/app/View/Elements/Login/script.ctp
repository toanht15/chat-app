<script type="text/javascript">
	var elmEv;
	(function(){
		elmEv = {
			submit: {
				func: function () {
					if (typeof MUserIndexForm === 'undefined') return false;
					MUserIndexForm.submit();
				}
			}
		};
	})();
	window.onload = function(){
		if (typeof MUserFormButton === 'undefined') return false;
		MUserFormButton.addEventListener('click', elmEv.submit.func);
	};
</script>
