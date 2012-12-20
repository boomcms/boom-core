<form id="sledge-form-pagesettings-visibility" name="pagesettings-visibility">
	<div class="s-pagesettings">
		<table width="100%">
			<tr>
				<td><?=__('Visible')?></td>
				<td>
					<select id="page-visible" name="visible" class="sledge-input sledge_select">
						<option value='1' <? if ($page->visible) echo "selected='selected'"; ?>>Yes</option>
						<option value='0' <? if ( ! $page->visible) echo "selected='selected'"; ?>>No</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><?=__('Visible from')?></td>
				<td>
					<input id="page-visible-from" name="visible_from" class="sledge-input sledge-datepicker" value="<?=date("d F Y", $page->visible_from);?>" />
					<select>
						<option>12:00</option>
					</select>
				</td>
			</tr>

			<tr>
				<td>
					<label for="page-visible-to"><?=__('Visible until')?></label>
					<input id="s-page-toggle-visible" type="checkbox" value="1" name='toggle_visible_to' class="ui-helper-right ui-helper-reset"<?=($page->visible_to) ? ' checked="checked"' : ''; ?> />
				</td>
				<td>
					<input id="page-visible-to"
						name="visible_to"
						class="sledge-input sledge-datepicker"
						value="<?=($page->visible_to) ?	date('Y-m-d H:i:s',$page->visible_to) : 'forever'; ?>"
						<?=(!$page->visible_to) ? ' disabled="disabled"' : ''; ?>
					/>
					<select id="page-visible-to-time" disabled="diabled">
						<option>12:00</option>
					</select>
				</td>
			</tr>
		</table>
	</div>
</form>