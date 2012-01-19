<form id="sledge-form-pagesettings-seo" name="pagesettings-seo">
	<div id="sledge-pagesettings-seo" class="sledge-tabs">
		<ul>
			<li><a href="#sledge-pagesettings-seo-basic">Basic</a></li>
			<li><a href="#sledge-pagesettings-seo-advanced">Advanced</a></li>
		</ul>

		<div id="sledge-pagesettings-seo-basic">
			<table width="100%">
				<?//if ($p['current_version']['Can edit page description']){?>
					<tr>
						<td style="vertical-align:top">
							<label for="description" class="ui-helper-clearfix">
								<span class="ui-helper-left" style="padding-top:2px">
									Description
								</span>
								<span class="ui-icon ui-helper-left ui-icon-help sledge-tooltip" title="A description of the description field."></span>
							</label>
						</td>
						<td><textarea id="description" name="description" class="sledge-textarea"><?=$page->get_description() ?></textarea></td>
					</tr>
				<?//}?>	
		        <?//if ($p['current_version']['Can edit page keywords']){?>
					<tr>
						<td style="vertical-align:top">
							<label for="keywords" class="ui-helper-clearfix">
								<span class="ui-helper-left" style="padding-top:2px">
									Keywords
								</span>
								<span class="ui-icon ui-icon-help ui-helper-left sledge-tooltip" title="Keywords description: please separate your keywords with a comma."></span>
							</label>
						</td>
						<td><textarea name="keywords" id="keywords" class="sledge-textarea"><?=$page->get_keywords() ?></textarea></td>
					</tr>
				<?//}?>
			</table>
		</div>

		<div id="sledge-pagesettings-seo-advanced">
			<table width="100%">
				<?//if ($p['attributes']['Can edit page indexing']){?>
					<tr>
						<td>Allow page indexing</td>
						<td>
							<select name="indexed">
								<option <?if ($page->indexed) echo "selected='selected' "; echo "value='1'>Yes</option>"; ?>
								<option <?if (!$page->indexed) echo "selected='selected' "; echo "value='0'>No</option>"; ?>
							</select>
						</td>
					</tr>
				<?//}?>
				<?/*if ($p['attributes']['Can edit page sitemap priority']){?>
					<tr>
						<td>Sitemap priority</td>
						<td>
							<select name="sitemap_priority">
								<?if ($page->rid == O::f('site_page')->get_homepage()->rid) {
									$default_priority = Kohana::config('core.sitemap_default_priority');
									if (!$default_priority) $default_priority = 0.5;?>
									<option value="">Use default (<?=$default_priority?>)</option>
								<?}else{?>
									<option value="">Inherit from my parent</option>
								<?}?>
								<?for ($i=0.0; $i<=1.0; $i+=0.1) {?>
									<option <?if ($page->sitemap_priority === $i) {?>selected="selected" <?}?> value="<?=$i?>"><?=$i?></option>
								<?}?>
							</select>
							0.0 is lowest, 1.0 is highest
						</td>
					</tr>
				<?}*/?>
				<?/*if ($p['attributes']['Can edit page sitemap update frequency']){?>
					<tr>
						<td>Sitemap update frequency</td>
						<td>
							<select name="sitemap_update_frequency">
								<?if ($page->rid == O::f('site_page')->get_homepage()->rid) {
									$default_update_frequency = Kohana::config('core.sitemap_default_update_frequency');
									if (!$default_update_frequency) $default_update_frequency = 'daily';?>
									<option value="">Use default (<?=$default_update_frequency?>)</option>
								<?}else{?>
									<option value="">Inherit from my parent</option>
								<?}?>
								<?foreach (array('never','yearly','monthly','weekly','daily','hourly','always') as $period) {?>
									<option <?if ($page->sitemap_update_frequency === $period) {?>selected="selected" <?}?> value="<?=$period?>"><?=$period?></option>
								<?}?>
							</select>
						</td>
					</tr>
				<?}*/?>
				<?//if ($p['attributes']['Can edit page hidden from search results']){?>
					<tr>
						<td>Hidden from search results</td>
						<td>
							<select name="hidden_from_search_results">
								<option value="0">No</option>
								<option value="1"<?if ($page->hidden_from_search_results):?> selected="selected"<? endif; ?>>Yes</option>
							</select>
						</td>
					</tr>
				<?/*}else if ($p['attributes']['Can view page hidden from search results']){?>
					<tr>
						<td>Hidden from search results</td>
						<td>
							<?=($page->hidden_from_search_results == 'y') ? 'Yes' : 'No'?>
						</td>
					</tr>
				<?}*/?>
			</table>
		</div>
	</div>
	<?//= $hidden_inputs; ?>
</form>
