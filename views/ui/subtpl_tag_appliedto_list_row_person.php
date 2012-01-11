<tr>
	<td width="10" class="ui-helper-reset">
		<input type="checkbox" class="sledge-tagmanager-select-checkbox ui-helper-reset" id="person-list-<?=$item->rid?>" />
	</td>
	<td width="25" class="ui-helper-center" align="center">
		<label for="asset-<?=$item->rid?>"><?=date('M-j',strtotime($item->audit_time))?></label>
	</td>
	<td>
		<a href="#person/<?=$item->rid?>">
			<img src="/sledge/img/icons/16x16/icon_user.gif" /> <?=$item->firstname?> <?=$item->lastname?>
		</a>
	</td>
</tr>
