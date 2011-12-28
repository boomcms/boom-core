<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
	
	/*
	This is a half arse effort at the left nav.
	It currently doesn't check for hidden_in_leftnav or hidden_in_leftnav_cms properties
	Need to auto colapse pages which aren't part of the current sub-tree.
	There's a lot of PHP in here. It could perhaps be moved to a Nav class?
	But it's a start!
	*/
?>

<div id="nav" class="block">
	<ul>
	<?
		$level = 1;		
		
		foreach ($page->mptt->fulltree() as $node)
		{	
			// Going down?
			if ($node->lvl > $level)
			{
				$level = $node->lvl;
			}	
			
			// Going up?
			if ($node->lvl < $level)
			{
				echo str_repeat( "</li></ul></li>", $level - $node->lvl );
				$level = $node->lvl;				
			}	
				
			echo "<li><a href='" , $node->page->uri() , "'>" , $node->page->title , "</a>\n";	
			
			if ($node->has_children())
			{
				echo "<ul>";
			}
			else 
			{
				echo "</li>";
			}
		}
	?>
	
</div>
