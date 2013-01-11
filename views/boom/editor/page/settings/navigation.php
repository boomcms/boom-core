<form id="boom-form-pagesettings-navigation" name="pagesettings-navigation">
	<div class="boom-tabs s-pagesettings">
		<ul>
			<li>
				<a href="#navigation-settings-basic"><?=__('Basic')?></a>
			</li>
			<? if ($allow_advanced): ?>
				<li>
					<a href="#navigation-settings-advanced"><?=__('Advanced')?></a>
				</li>
			<? endif; ?>
		</ul>

		<div id="navigation-settings-basic">
			Visible in navigation?
			
			<select name="visible_in_nav">
				<option <?if ($page->visible_in_nav == true) echo "selected=\"selected\" ";?> value="1">Yes</option>
				<option <?if ($page->visible_in_nav == false) echo "selected=\"selected\" ";?> value="0">No</option>
			</select>
			
			Visible in CMS navigation?
			
			<select name="visible_in_nav_cms">
				<option <?if ($page->visible_in_nav_cms == true) echo "selected=\"selected\" ";?> value="1">Yes</option>
				<option <?if ($page->visible_in_nav_cms == false) echo "selected=\"selected\" ";?> value="0">No</option>
			</select>
		</div>

		<? if ($allow_advanced): ?>
			<div id='navigation-settings-advanced'>
				Parent page
				
				<input type="hidden" name="parent_id" value="<?=$page->mptt->parent_id?>">
				<div class="boom-tree">
					<ul>
						<li><a id="page_5" href="/" rel="5">Home</a></li>
					</ul>
				</div>
			</div>
		<? endif; ?>
	</div>
</form>