<?php
/**
* Subtemplate for a page tree.
* There's a lot of PHP in here. It could perhaps be moved to a Nav class?
* But then do we want to move all the HTML to a class? It may perhaps be less clear?]
* It's something to think about.
*
* Rendered by Controller_Tree::action_after()
*
*********************** Variables **********************
*	$page		****	instance of Page. Not Model_Page! The leftnav methods are in the page class.
*	$state		****	string		****	Set to either collapsed or expanded. Populated from a GET parameter. 
********************************************************
*
*/
?>
<div id="nav" class="sledge-tree block">
	<ul>
	<?
		$level = 1;	
		$count = sizeof($pages);
		
		for ($i = 0; $i < $count; $i++)
		{	
			$node = $pages[$i];

			// Going down?
			if ($i < ($count - 1) && $pages[ $i + 1 ]['lvl'] > $node['lvl'])
			{
				$level = $node['lvl'];
			}	
			
			// Going up?
			if ($i > 0 && $node['lvl'] < $pages[ $i - 1 ]['lvl'])
			{
				echo str_repeat("</li></ul></li>", $pages[ $i - 1 ]['lvl'] - $node['lvl']);
				$level = $node['lvl'];				
			}	
				
			// Show the page.
			echo "<li id='p", $node['id'], "'><a";
			
			if ($node['visible'] == false)
			{
				echo " class='leftnav-page-invisible'";
			}
			else if ($node['visible_in_leftnav'] == false)
			{
				echo " style='color: #ff0000'";
			}
			
			echo " id='", $node['page_id'], "' href='/", $node['link'], "' rel='", $node['page_id'], "'>", $node['title'], "</a>\n";	
			
			// Start a sub-list if this page has children. Otherwise close the list item.
			if ($i < ($count - 1) && $pages[ $i + 1 ]['parent_id'] == $node['id'])
			{
				echo "<ul";

				// Hide sub-trees unless state is expanded.
				// If current node is not a direct child of the page we're viewing.
				if ($state == 'collapsed' && !($node['lft'] < $page->mptt->lft && $node['rgt'] > $page->mptt->rgt) && $node['page_id'] != $page->id)
				{
					echo " class='hidden ui-helper-hidden'";
				}
				elseif ($node['children_ordering_policy'] & Model_Page::CHILD_ORDER_MANUAL && $node['page_id'] == $page->id)
				{
					echo " class='sledge-sortable'";
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
