<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
	
	/*
	This is a half arse effort at the left nav.
	It currently just spits out the whole tree - without creating sub-lists for child pages.
	It only checks the visible_in_leftnav value and not the cms value if we're in the CMS.
	There's a lot of PHP in here. It could perhaps be moved to a Nav class?
	But it's a start!
	*/
?>

<div id="nav" class="block">
	<ul>
		<?
			$right = array();
			foreach( $page->mptt->getTree() as $node )
			{
				if ( $node->page->isVisible() && $node->page->visible_in_leftnav == 't' )
				{
					if (count( $right ) > 0)
					{
						while ($right[ count($right)-1 ]->right_val < $node->right_val) 
						{
							array_pop( $right );
						}
					}

					$right[] = $node;	
					echo "<li><a href='" . $node->page->uri() . "'>" . $node->page->title . "</a></li>\n";		
				}		
			}
		?>
	</ul>
</div>
