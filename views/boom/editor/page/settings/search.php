<form id="boom-form-pagesettings-search" name="pagesettings-seo">
	<div id="b-pagesettings-search" class="boom-tabs s-pagesettings">
		<? if ($allow_advanced): ?>
			<ul>
				<li>
					<a href="#b-pagesettings-search-basic">Basic</a>
				</li>
				<li>
					<a href="#b-pagesettings-search-advanced">Advanced</a>
				</li>
			</ul>
		<? endif; ?>

		<div id="b-pagesettings-search-basic">
			<label for="description" class="ui-helper-clearfix">
				<span class="ui-helper-left" style="padding-top:2px">
					Description
				</span>
				<span class="ui-icon ui-helper-left ui-icon-help boom-tooltip" title="A description of the description field."></span>
			</label>

			<textarea id="description" name="description" class="boom-textarea"><?= $page->description() ?></textarea>

			<label for="keywords" class="ui-helper-clearfix">
				<span class="ui-helper-left" style="padding-top:2px">
					Keywords
				</span>
				<span class="ui-icon ui-icon-help ui-helper-left boom-tooltip" title="Keywords description: please separate your keywords with a comma."></span>
			</label>
			<textarea name="keywords" id="keywords" class="boom-textarea"><?=$page->keywords ?></textarea>
		</div>

		<? if ($allow_advanced): ?>
			<div id="b-pagesettings-search-advanced">
				Allow indexing by search engines
				<select name="indexed">
					<option <?if ($page->external_indexing) echo "selected='selected' "; echo "value='1'>Yes</option>"; ?>
					<option <?if ( ! $page->external_indexing) echo "selected='selected' "; echo "value='0'>No</option>"; ?>
				</select>


				Show in site search results
				<select name="internal_indexing">
					<option value="0">No</option>
					<option value="1"<?if ($page->internal_indexing):?> selected="selected"<? endif; ?>>Yes</option>
				</select>
			</div>
		<? endif; ?>
	</div>
</form>