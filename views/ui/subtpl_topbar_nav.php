<?
	$homepage = O::f('site_page')->get_homepage();

	$ancestors = $this->page->get_ancestor_pages();

	# get cms home object
	$cms_home = O::fa('page')->find_by_uri('cms');

	if (count($pages = O::f('cms_page')->find_all_by_parent_rid($cms_home->rid))) {?>

		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">

			<!-- CMS - HOME -->
			<li class="ui-state-default ui-corner-top<?if ($this->page->rid == $cms_home->rid) {?> ui-tabs-selected ui-state-active<?}?>">
				<a href="<?=$cms_home->absolute_uri()?>">Home</a>
			</li>

			<!-- CMS - SITES -->
			<?
			$selected = false;
			if ($this->page->uri == $homepage->uri) {
				$selected = true;
			} else {
				foreach ($ancestors as $anc) {
					if ($anc->rid == $homepage->rid) {
						$selected = true;
						break;
					}
				}
			}?>
			<li class="ui-state-default ui-corner-top<?= $selected ? ' ui-tabs-selected ui-state-active' : ''?>">
				<a href="<?=$homepage->absolute_uri()?>">Sites</a>
			</li>

			<!-- CMS - ALL OTHER PAGES -->
			<?
			foreach ($pages as $page) {
				if ((Permissions::may_i('read', $page)) and ($page->uri != 'cms/login')){
					$selected = false;
					foreach ($ancestors as $anc) {
						if ($anc->rid == $page->rid or $this->page->rid == $page->rid) {
							$selected = true;
						}
					}?>
					<li class="ui-state-default ui-corner-top<?= $selected ? ' ui-tabs-selected ui-state-active' : ''?>">
						<a href="<?=$page->absolute_uri();?>"><?=$page->title;?></a>
					</li>
				<?}
			}?>
		</ul>
	<?}
?>
