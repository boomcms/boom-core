<form name="pagesettings-adminsettings">
	<div id="admin-setings" class="sledge-tabs">
		<table width="100%">
		    <?//if ($p['attributes']['Can edit page internal name']){?>
				<tr>
					<td>Internal name</td>
					<td><input type="text" class="sledge-input" name="internal_name" id="internal_name" value="<?=$page->internal_name;?>" /></td>
				</tr>
			<?//}?>
			<?//if ($p['attributes']['Can edit page type']){?>
				<tr>
					<td>Page type</td>
					<td><input id="pagetype_description" type="text" name="pagetype_description" class="sledge-input" value="" /></td>
				</tr>
			<?//}?>
			<?/*if ($p['attributes']['Can edit page cache duration']){?>
				<tr>
					<td>Cache duration</td>
					<td>
						<select name="cache_duration">
							<option <?if ($page->cache_duration === null){echo "selected=\"selected\" ";}?> value="">Inherit from my parent</option>
							<option <?if ($page->cache_duration == '0'){echo "selected=\"selected\" ";}?> value="0">Disable caching</option>
							<?foreach(array(1,2,3,4,5,10,20,30) as $i) {?>
								<option <?if ($page->cache_duration == $i) {echo "selected=\"selected\" ";}?> value="<?=$i?>"><?=$i?> minute<?if($i!=1)echo 's';?></option>
							<?}?>
							<?foreach(array(1,2,24) as $i) {?>
								<option <?if ($page->cache_duration == ($i*60)) {echo "selected=\"selected\" ";}?> value="<?=($i*60)?>"><?=$i?> hour<?if($i!=1)echo 's';?></option>
							<?}?>
						</select>
					</td>
				</tr>
			<?}*/?>
			<?//if ($p['attributes']['Can edit page SSL']){?>
				<tr>
					<td>Secure page?</td>
					<td>
						<select name="ssl_only">
							<option <?if ($page->ssl_only === null) echo "selected=\"selected\" ";?> value="">Inherit from my parent</option>
							<option <?if ($page->ssl_only === true) echo "selected=\"selected\" ";?> value="true">Yes</option>
							<option <?if ($page->ssl_only === false) echo "selected=\"selected\" ";?> value="false">No</option>
						</select>
					</td>
				</tr>
			<?//}?>
		</table>
	</div>
</form>
