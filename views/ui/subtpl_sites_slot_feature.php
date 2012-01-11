<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<?

	$this->page = O::fa('page', (int) $_GET['rid']);

?>

<div style="margin-bottom: .6em">
	<div class="ui-widget">
		<div class="ui-state-highlight ui-corner-all"> 
			<p style="margin: .5em;">
				<span style="float: left; margin-right: 0.3em; margin-top:-.2em" class="ui-icon ui-icon-info"></span>
				Select a page to feature below.
			</p> 
		</div>
	</div>
	<br />
	<ul class="sledge-tree">
		<?
			$r = new Recursion_Page_Tree;
			$r->recurse(O::fa('page')->find_by_title('Site 1'), O::f('site_page')->get_homepage()->rid, true, false, false, false, false, false, false, false);
		?>
	</ul>
</div>
