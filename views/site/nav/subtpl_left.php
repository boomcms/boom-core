<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
	
	//Site leftnav template
	// @todo CMS leftnav which should be sortable.
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
							echo str_repeat('  ',count($right)) . "pop right\n"; 
							//echo "</li>";
							array_pop( $right );
						}
					}

					$right[] = $node;	
					echo str_repeat('  ',count($right)) . "<li><a href='" . $node->page->getAbsoluteUri() . "'>" . $node->page->title . "</a></li>\n";		
				}		
			}
		?>
	</ul>
</div>
