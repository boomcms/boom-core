<?php
/**
* Subtemplate for the leftnav.
* There's a lot of PHP in here. It could perhaps be moved to a Nav class?
* But then do we want to move all the HTML to a class? It may perhaps be less clear?]
* It's something to think about.
*
* Rendered by just about any template that wants a leftnav.
*
*********************** Variables **********************
*	$page		****	instance of Page. Not Model_Page! The leftnav methods are in the page class.
*	$person		****	instance of Model_Person	****	Current active user. Used to determine whether to show CMS or site leftnav.
********************************************************
*
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
			echo "<li><a href='" , $node->url() , "'>" , $node->title , "</a>\n";	
			
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
