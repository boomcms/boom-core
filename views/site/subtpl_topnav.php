<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>

<div id="topnav" class="block">
	<ul>		
		<?
			foreach( $page->mptt->getChildren() as $child_mptt )
			{
				echo "<li><a href='" . $child_mptt->page->getAbsoluteUri();
			
				if ($child_mptt->page_id === $page->id)
				{
					echo " class='current'";
				}
			
				echo ">" . $mptt->page->title . "</a>";
			}
		?>
</ul>
</div>
