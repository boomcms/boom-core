<?php
	/**
	*
	* Site map template.
	* Traverses the page tree and displays it as a list.
	* This template essentially uses the left nav code but with minor changes to the formatting.
	* 
	*
	*/
?>

<div class="wrapper">
	<?= new View('site/subtpl_siteheader');?>
	<div id="navigation">
		<?= new View('site/subtpl_logo');?>
		<?= new View('site/nav/left'); ?>		
		<?= new View('site/subtpl_newsletter');?>
	</div>
	<div id="main-content">
		<div class="headings">
			<h1 id='sledge-page-title' class="pageTitle"><?= $page->title?></h1>
		</div>

		<ul id='sitemap'>
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
					echo "<ul>";
				}
				else 
				{
					echo "</li>";
				}
			}
		?>

	</div>
					
	<?//= $subtpl_footer; ?>
</div>
