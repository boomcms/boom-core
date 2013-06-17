<form id="boom-form-pagesettings-visibility" name="pagesettings-visibility">
	<div class="b-pagesettings">
		<label for="page-visible"><?=__('Visible')?>
			<select id="page-visible" name="visible" class="boom-input boom_select">
				<option value='1' <? if ($page->visible) echo "selected='selected'"; ?>>Yes</option>
				<option value='0' <? if ( ! $page->visible) echo "selected='selected'"; ?>>No</option>
			</select>
		</label>

		<label for="page-visible-from"><?=__('Visible from')?>
			<input id="page-visible-from" name="visible_from" class="boom-input boom-datepicker" value="<?=date("d F Y h:m", $page->visible_from);?>" />
		</label>

		<label for="b-page-toggle-visible"><?=__('Visible until')?>
			<input id="page-visible-to"
				name="visible_to"
				class="boom-input boom-datepicker"
				value="<?=($page->visible_to) ?	date('d F Y h:m',$page->visible_to) : 'forever'; ?>"
				<?=(!$page->visible_to) ? ' disabled="disabled"' : ''; ?>
			/>
		</label>

		<input id="b-page-toggle-visible" type="checkbox" value="1" name='toggle_visible_to' class="ui-helper-right ui-helper-reset"<?=($page->visible_to) ? ' checked="checked"' : ''; ?>
	</div>
</form>