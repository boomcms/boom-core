<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>

<div id="topnav" class="block">		
	<?
		$first = true;	
		// Show the parent pages.
		foreach( $page->mptt->parents() as $node )
		{
			if ($first === false)
			{
				echo " &gt; ";
			}
			else
			{
				$first = false;
			}
			echo "<a href='" , $node->page->url() , "'>" , $node->page->title , "</a>";
		}
		
		// Show this page.
		if ($first === false)
		{
			echo " &gt; ";
		}
		echo "<a href='", $page->url(), "' class='current'>" , $page->title, "</a>";
	?>
</div>
