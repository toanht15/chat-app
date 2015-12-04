<script type="text/javascript">
(function(){
	$('#labelHideList').multiSelect({
		selectableHeader: "<div>表示している項目</div>",
		selectionHeader: "<div>表示していない項目</div>"
	});
}());
</script>

<?= $this->Form->input('labelHideList', array('type' => 'select', 'multiple' => true, 'label' => false, 'options' => $labelHideList, 'selected' => $selectedLabelList)); ?>