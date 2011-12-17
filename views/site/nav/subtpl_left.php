<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
	
	//Site leftnav template
	// @todo CMS leftnav which should be sortable.
?>

<div id="nav" class="block">
	<ul>
	<?
		foreach( $page->mptt->getTree() as $mptt )
		{
			$p = $mptt->page;
		
			if ( $p->isVisible() && $p->visible_in_leftnav == 't' )
			{
				echo "<li><a href='" . $p->getAbsoluteUri() . "'>$p->title</a>";			
			}
		}
	?>
	</ul>
</div>
