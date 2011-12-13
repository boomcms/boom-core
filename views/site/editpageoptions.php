<?
$editoroptions = array();
foreach($this->editoroptions as $slotname => $options) {
	if (is_array($options)) {
		foreach($options as $index => $option){
			$options[$index] = "'".$option."'";
		}       
		$editoroptions[] = "'{$slotname}': [".implode(',', $options)."]";
	}
}
$editoroptions = join(",\n", $editoroptions);
?>
<script type="text/javascript">
//<![CDATA[
	var timer = setInterval(function(){

		if (window.parent.$ && window.parent.$.sledge && window.parent.$.sledge.page) {

			clearInterval(timer);

			window.parent.$.sledge.page.register({
				rid: <?=$this->page->rid;?>,
				vid: <?=$this->page->vid;?>,
				writable: 1,
				editorOptions: {
					<?= $editoroptions; ?>
				}
			});
		}
	}, 100);
//]]>
</script>
