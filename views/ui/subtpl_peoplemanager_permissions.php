<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates		www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?
$slot_types = array();
$slot_names = array();
foreach (O::f('metadata_v')->where("key='chunktype'")->select("DISTINCT value")->find_all() as $m) {
	$slot_types[] = $m->value;
}
foreach (O::f('metadata_v')->where("key='chunkname'")->select("DISTINCT value")->find_all() as $m) {
	$slot_names[] = $m->value;
}
?>

<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all">
		<p style="margin: .5em;">
			<span style="float: left; margin-right: 0.3em; margin-top:-.2em" class="ui-icon ui-icon-info"></span>
			Select a page or a page slot to add a permission to.
			<br /><br />
			Expand a page tree to view page slots and child pages.
		</p>
	</div>
</div>

<br />

<ul class="sledge-tree">
	<li id="any:any">
		<a href="#" rel="any:any"<?if ($_GET['where_rid']=='any:any'){?> class="ui-state-active"<?}?>>
			Any page in any tree
		</a>
	</li>
	<li id="any:root">
		<a href="#" rel="any:root"<?if ($_GET['where_rid']=='any:root'){?> class="ui-state-active"<?}?>>
			Any page outside of a tree
		</a>
	</li>
	<?
		$r = new Recursion_Page_Tree;
		$r->recurse(O::f('cms_page')->find_by_title("Site 1"), $_GET['where_rid'], false, false, false, false, true, false, false, true);
	?>
</ul>
<br />
