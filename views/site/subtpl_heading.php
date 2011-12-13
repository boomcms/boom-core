<?php
	# Copyright 2011, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?
	$cms = Permissions::may_i("write");
	$page_model = ($cms) ? 'cms_page' : 'site_page';
	$homepage = ORM::factory( 'page' )->find_by_internal_name( 'homepage' );

	if (!@$_REQUEST['search']) {
		@$_REQUEST['search'] = "Search this site...";
	}
?>
<div id="header">
	<div class="se">
		<div id="logo">
			<p>
				<a href="/">
					<img src="/img/logo.png" alt="Notting Hill Housing Association" height="102" width="112">
				</a>
			</p>
		</div>
		<div id="utility">
			<ul>
				<li class="first"><a href="/">Home</a>
				<li><a href="https://www.nottinghillhousing.org.uk/OCSlogon.aspx?id_Content=1134">Resident login</a></li>
				<li>Change text size 
					<abbr class="medium" title="medium">A</abbr>
					<abbr class="large" title="large">A</abbr>
					<abbr class="extra-large" title="extra large">A</abbr>
				</li>
			</ul>
		</div>
		<div id="search-form">
			<form role="search" method="post" action="/search">
				<fieldset>
					<?=form::input('search',@$_REQUEST['search'],' class="active" title="Search"')?>
					<input type="image" id="submit" src="/img/light_grey_arrow.png" alt="Search" height="10" width="10" />		
				</fieldset>
			</form>
		</div>
		<div id="main-nav">
			<?
			ob_start();

			$ancestor_rids = array();
			foreach ($this->page->getMptt()->getAncestors() as $page_mptt) {
				$ancestor_rids[] = $page->rid;
			}

			$r = new Topnav($page, 2);
			$parent = $r->parent_tree[0];

			$parent_page = O::f(($r->cms)?'cms_page':'site_page',$parent);
			$found = false;

			if (count( $this->page->getMptt()->getAncestors() ) > 0) {
				$can_navsort = Permissions::may_i('navsort');
				?>
				<ul class="ui-sortable" <?=($can_navsort and $r->navsort($parent))?>>
					<?foreach ($this->page->getMptt()->getAncestors() as $i => $page_mptt) {
						$classes = '';
						if ($i == 0) {
							$classes .= 'first';
						}
						if ($i == count($pages)-1) {
							$classes .= ' last';
						}?>
						<li<?if(get_class( $page ) == 'cms_page_Model' ){?> id="p<?=$page->rid;?>"<?}?><?if(strlen(trim($classes))){?> class="<?=trim($classes);?>"<?}?>>
							<a href="<?=$page->getAbsoluteUri();?>"<?if ($page->rid === $this->page->rid){?> class="current"<?}?>>
								<?=ucfirst($page->title);?>
							</a>
						</li>
					<?}?>
				</ul>
			<?}?>
			<?
			$html = ob_get_contents();
			ob_end_clean();
			echo $html;
			
			?>
		</div>
	</div>
</div>
<script type="text/javascript">
$('#search').focus(function() {
	if ($('#search').val() == 'Search this site...') {
		$('#search').val('');
	}
});
$('#search').focusout(function() {
	if ($('#search').val().length <1) {
		$('#search').val('Search this site...');
	}
});
</script>
