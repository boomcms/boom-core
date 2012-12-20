<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>

<div id="breadcrumbs" class="block">
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
			echo "<a href='" , $node->page->link() , "'>" , $node->page->version()->title , "</a>";
		}

		// Show this page.
		if ($first === FALSE)
		{
			echo " &gt; ";
		}
		echo "<a href='", $page->link(), "' class='current'>" , $page->version()->title, "</a>";
	?>
</div>
