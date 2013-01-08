<form id="boom-form-pagesettings-search" name="pagesettings-seo">
	<div id="b-pagesettings-search" class="boom-tabs s-pagesettings">
		<ul>
			<li>
				<a href="#b-pagesettings-search-basic">Basic</a>
			</li>
			<? if ($allow_advanced): ?>
				<li>
					<a href="#b-pagesettings-search-advanced">Advanced</a>
				</li>
			<? endif; ?>
		</ul>

		<div id="b-pagesettings-search-basic">
			<table width="100%">
				<tr>
					<td style="vertical-align:top">
						<label for="description" class="ui-helper-clearfix">
							<span class="ui-helper-left" style="padding-top:2px">
								Description
							</span>
							<span class="ui-icon ui-helper-left ui-icon-help boom-tooltip" title="A description of the description field."></span>
						</label>
					</td>
					<td>
						<textarea id="description" name="description" class="boom-textarea"><?=$page->description() ?></textarea>
					</td>
				</tr>
				<tr>
					<td style="vertical-align:top">
						<label for="keywords" class="ui-helper-clearfix">
							<span class="ui-helper-left" style="padding-top:2px">
								Keywords
							</span>
							<span class="ui-icon ui-icon-help ui-helper-left boom-tooltip" title="Keywords description: please separate your keywords with a comma."></span>
						</label>
					</td>
					<td>
						<textarea name="keywords" id="keywords" class="boom-textarea"><?=$page->keywords ?></textarea>
					</td>
				</tr>
			</table>
		</div>

		<? if ($allow_advanced): ?>
			<div id="b-pagesettings-search-advanced">
				<table width="100%">
					<tr>
						<td>Allow indexing by search engines</td>
						<td>
							<select name="indexed">
								<option <?if ($page->external_indexing) echo "selected='selected' "; echo "value='1'>Yes</option>"; ?>
								<option <?if ( ! $page->external_indexing) echo "selected='selected' "; echo "value='0'>No</option>"; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Show in site search results</td>
						<td>
							<select name="internal_indexing">
								<option value="0">No</option>
								<option value="1"<?if ($page->internal_indexing):?> selected="selected"<? endif; ?>>Yes</option>
							</select>
						</td>
					</tr>
				</table>
			</div>
		<? endif; ?>
	</div>
</form>