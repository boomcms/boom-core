<form id="boom-form-pagesettings-navigation" name="pagesettings-navigation">
	<div class="boom-tabs s-pagesettings">
		<? if ($allow_advanced): ?>
			<ul>
				<li>
					<a href="#navigation-settings-basic"><?=__('Basic')?></a>
				</li>
				<li>
					<a href="#navigation-settings-advanced"><?=__('Advanced')?></a>
				</li>
			</ul>
		<? endif; ?>

		<div id="navigation-settings-basic">
			<label for="visible_in_nav">Visible in navigation?

			<select id="visible_in_nav" name="visible_in_nav">
				<option <?if ($page->visible_in_nav == true) echo "selected=\"selected\" ";?> value="1">Yes</option>
				<option <?if ($page->visible_in_nav == false) echo "selected=\"selected\" ";?> value="0">No</option>
			</select>
			</label>

			<label for="visible_in_nav_cms">Visible in CMS navigation?

			<select id="visible_in_nav_cms" name="visible_in_nav_cms">
				<option <?if ($page->visible_in_nav_cms == true) echo "selected=\"selected\" ";?> value="1">Yes</option>
				<option <?if ($page->visible_in_nav_cms == false) echo "selected=\"selected\" ";?> value="0">No</option>
			</select>
			</label>
		</div>

		<? if ($allow_advanced): ?>
			<div id='navigation-settings-advanced'>
				<label for="parent_id">Parent page</label>

				<input type="hidden" name="parent_id" value="<?=$page->mptt->parent_id?>">
					<ul class="boom-tree">
						<li><a id="page_5" href="/" rel="5">Home</a></li>
					</ul>
			</div>
		<? endif; ?>
	</div>
</form>