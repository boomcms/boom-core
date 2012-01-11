<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
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
			foreach( $page->mptt->full_tree() as $node ):
				echo "<li>", $node->page->title, "</li>";
			endforeach;
		?>
	</ul>
</div>
