<tr>
	<td width="10" class="ui-helper-reset">
		<input type="checkbox" class="sledge-tagmanager-select-checkbox ui-helper-reset" id="asset-list-<?=$item->id?>" />
	</td>
	<td width="25" class="ui-helper-center" align="center">
		<label for="asset-<?=$item->id?>"><?=date('M-j',strtotime($item->audit_time))?></label>
	</td>
	<td>
		<a href="#asset/<?=$item->id?>"><img src="/sledge/img/icon_asset_image.gif" /> <?=$item->title?></a>
	</td>
</tr>
