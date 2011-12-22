<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>

<div id="topnav" class="block">		
	<?
		$first = true;
		foreach( $page->mptt->getRoute() as $node )
		{
			if ($first === false)
			{
				echo " &gt; ";
			}
			else
			{
				$first = false;
			}
			echo "<a href='" . $node->page->getAbsoluteUri() . "'";
		
			if ($node->page->id === $page->id)
			{
				echo " class='current'";
			}
		
			echo ">" . $node->page->title . "</a>";

		}
	?>
</div>
