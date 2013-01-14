<form id="boom-form-pagesettings-visibility" name="pagesettings-visibility">
	<div class="b-pagesettings">
		<label for="page-visible"><?=__('Visible')?></label>
		<select id="page-visible" name="visible" class="boom-input boom_select">
			<option value='1' <? if ($page->visible) echo "selected='selected'"; ?>>Yes</option>
			<option value='0' <? if ( ! $page->visible) echo "selected='selected'"; ?>>No</option>
		</select>
		
		<label for="page-visible-from"><?=__('Visible from')?></label>
		
		<input id="page-visible-from" name="visible_from" class="boom-input boom-datepicker" value="<?=date("d F Y", $page->visible_from);?>" />
		<select>
			<option>12:00</option>
		</select>
		
		<label for="page-visible-to"><?=__('Visible until')?></label>
		<input id="b-page-toggle-visible" type="checkbox" value="1" name='toggle_visible_to' class="ui-helper-right ui-helper-reset"<?=($page->visible_to) ? ' checked="checked"' : ''; ?> />
		
		<input id="page-visible-to"
			name="visible_to"
			class="boom-input boom-datepicker"
			value="<?=($page->visible_to) ?	date('Y-m-d H:i:s',$page->visible_to) : 'forever'; ?>"
			<?=(!$page->visible_to) ? ' disabled="disabled"' : ''; ?>
		/>
		<select id="page-visible-to-time" disabled="diabled">
			<option>12:00</option>
		</select>
	</div>
</form>