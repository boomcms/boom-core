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
		$pages = $page->leftnav_pages( $person );
		$count = sizeof( $pages );
		
		for ($i = 0; $i < $count; $i++)
		{	
			$node = $pages[$i];

			// Going down?
			if ($node['lvl'] > $level)
			{
				$level = $node['lvl'];
			}	
			
			// Going up?
			if ($i < ($count - 1) && $node['lvl'] > $pages[ $i + 1 ]['lvl'])
			{
				echo str_repeat( "</li></ul></li>", $node['lvl'] - $pages[ $i + 1 ]['lvl'] );
				$level = $node['lvl'];				
			}	
				
			// Show the page.
			echo "<li><a href='/" , $node['uri'] , "'>" , $node['title'] , "</a>\n";	
			
			// Start a sub-list if this page has children. Otherwise close the list item.
			if ($i < ($count - 1) && $pages[ $i + 1 ]['lft'] > $node['lft'] && $pages[ $i + 1 ]['rgt'] < $node['rgt'])
			{
				echo "<ul";
				
				// Hide sub-trees by default
				// If current node is not a direct child of the page we're viewing.
				if ($node['lvl']['parent_id'] != $page->mptt->id && $node['id'] != $page->id)
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
