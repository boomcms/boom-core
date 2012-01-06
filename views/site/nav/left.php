<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
	
	/*
	This is a half arse effort at the left nav.
	It currently doesn't check for hidden_in_leftnav or hidden_in_leftnav_cms properties
	There's a lot of PHP in here. It could perhaps be moved to a Nav class?
	But it's a start!
	*/
?>

<div id="nav" class="block">
	<ul>
	<?
		$level = 1;	
		
		foreach ($page->leftnav_pages( $person ) as $node)
		{	
			// Going down?
			if ($node->mptt->lvl > $level)
			{
				$level = $node->mptt->lvl;
			}	
			
			// Going up?
			if ($node->mptt->lvl < $level)
			{
				echo str_repeat( "</li></ul></li>", $level - $node->mptt->lvl );
				$level = $node->mptt->lvl;				
			}	
				
			// Show the page.
			echo "<li><a href='" , $node->uri() , "'>" , $node->title , "</a>\n";	
			
			// Start a sub-list if this page has children. Otherwise close the list item.
			if ($node->mptt->has_children())
			{
				echo "<ul";
				
				// Hide sub-trees by default
				if (!$node->mptt->is_in_parents( $page->mptt ) && $node->id !== $page->id)
				{
					echo " class='hidden'";
				}
				echo ">";
			}
			else 
			{
				echo "</li>";
			}
		}
	?>
	
</div>
