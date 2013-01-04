<form id="b-form-pageversion-embargo">
	<div class="b-pagesettings">
		<table width="100%">
			<tr>
				<td><?=__('Embargo')?></td>
				<td>
					<select id="page-visible" name="embargoed" class="boom-input boom_select">
						<option value='1' <? if ($version->embargoed_until > $_SERVER['REQUEST_TIME']) echo "selected='selected'"; ?>>Yes</option>
						<option value='0' <? if ($version->embargoed_until <= $_SERVER['REQUEST_TIME']) echo "selected='selected'"; ?>>No</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><?=__('Embargo until')?></td>
				<td>
					<input id="page-embargo" name="embargoed_until" class="boom-input boom-datepicker" value="<?=date("d F Y", $version->embargoed_until);?>" />
					<select>
						<option>12:00</option>
					</select>
				</td>
			</tr>
		</table>
	</div>
</form>