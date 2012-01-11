<div class="thumb ui-state-active ui-corner-all">

	<input type="checkbox" class="sledge-tagmanager-select-checkbox ui-helper-reset" id="person-thumb-<?=$item->rid?>" />

	<a href="#person/<?=$item->rid?>">
		<img src="/_ajax/call/Person/get_image/<?=$item->rid?>/100/100/85/1" />
		<span class="caption"><?=$item->firstname.' '.$item->lastname?></span>
		<span class="caption-overlay"></span>
	</a>
</div>
