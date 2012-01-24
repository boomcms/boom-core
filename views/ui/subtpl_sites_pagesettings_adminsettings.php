<form name="pagesettings-adminsettings">
	<div id="admin-setings" class="sledge-tabs">
		<table width="100%">
		    <? if ($person->can( 'view', $page, 'internal_name' )):?>
				<tr>
					<td>Internal name</td>
					<td>
						<? if ($person->can( 'edit', $page, 'internal_name' )):?>
							<input type="text" class="sledge-input" name="internal_name" id="internal_name" value="<?=$page->internal_name;?>" />
						<? else:
							echo $page->internal_name;
						endif;?>
					</td>
				</tr>
			<? endif;
			
			if ($person->can( 'view', $page, 'pagetype_description' )):?>
				<tr>
					<td>Page type</td>
					<td>
						<?
							if ($person->can( 'edit', $page, 'pagetype_description' )):
								echo "<input id='pagetype_description' type='text' name='pagetype_description' class='sledge-input' value='", $page->pagetype_description, "'/>";
							else:
								echo $page->pagetype_description;
							endif;
						?>
					</td>
				</tr>
			<?endif;
			
			if ($person->can( 'view', $page, 'cache_duration' )):?>
				<tr>
					<td>Cache duration</td>
					<td>
						<?
							if ($person->can( 'edit', $page, 'cache_duration' )):?>
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
						<?
							else:
								echo $page->cache_duration;
							endif;
						?>
					</td>
				</tr>
			<?endif;
			
			if ($person->can( 'view', $page, 'ssl_only' )):?>
				<tr>
					<td>Secure page?</td>
					<td>
						<?
							if ($person->can( 'edit', $page, 'ssl_only' )):?>
								<select name="ssl_only">
									<option <?if ($page->ssl_only === null) echo "selected=\"selected\" ";?> value="">Inherit from my parent</option>
									<option <?if ($page->ssl_only === true) echo "selected=\"selected\" ";?> value="true">Yes</option>
									<option <?if ($page->ssl_only === false) echo "selected=\"selected\" ";?> value="false">No</option>
								</select>
						<?
							else:
								echo $page->ssl_only? 'Yes' : 'No';
							endif;
						?>
					</td>
				</tr>
			<?endif;?>
		</table>
	</div>
</form>
