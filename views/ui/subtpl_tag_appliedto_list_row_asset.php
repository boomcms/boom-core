<tr>
	<td width="10" class="ui-helper-reset">
		<input type="checkbox" class="sledge-tagmanager-select-checkbox ui-helper-reset" id="asset-list-<?=$item->rid?>" />
	</td>
	<td width="25" class="ui-helper-center" align="center">
		<label for="asset-<?=$item->rid?>"><?=date('M-j',strtotime($item->audit_time))?></label>
	</td>
	<td>
		<a href="#asset/<?=$item->rid?>"><img src="/sledge/img/icon_asset_image.gif" /> <?=$item->title?></a>
	</td>
</tr>
