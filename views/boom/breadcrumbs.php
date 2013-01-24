<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<div id="b-breadcrumbs">
	<button id="b-breadcrumb-toggle" class="boom-button" data-icon="ui-icon-triangle-1-s"></button>
	<ul id="breadcrumbs" class="block">
		<?
			$first = TRUE;
			// Show the parent pages.
			foreach($page->mptt->parents() as $node)
			{
				if ($first === FALSE)
				{
					echo " &gt; ";
				}
				else
				{
					$first = FALSE;
				}
				echo "<li><a target='_top' href='" , $node->page->url() , "'>" , $node->page->version()->title , "</a></li>";
			}

			// Show this page.
			if ($first === FALSE)
			{
				echo " &gt; ";
			}
			echo "<li><a href='", $page->url(), "' class='current' target='_top'>" , $page->version()->title, "</a></li>";
		?>
	</ul>
</div>
