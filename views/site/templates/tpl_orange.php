<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
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
			<h1 class="pageTitle"><?= $page->version->title?></h1>
			<? 
				if ($slot = $page->getSlot('text', 'standfirst' ))
				{ 
					echo '<h2 class="standFirst">' . $slot->show() . '</h2>';
				}
			?>
		</div>
		<?
			if ($slot = $page->getSlot('text', 'bodycopy' ))
			{
				echo '<div id="content">' . $slot->show(). '</div>';
			}
		?>
		<div id="nav-widget"></div>		
		<?
			echo $page->getSlot('feature', 'feature3' )->show();
			echo $page->getSlot('feature', 'feature4' )->show();
		?>
	</div>
	<div id="aside">	
		<?
			echo $page->getSlot('feature', 'feature1' )->show();
			echo $page->getSlot('feature', 'feature2' )->show();
		?>
		<?//= O::f('chunk_linkset_v')->get_chunk(O::f('site_page')->get_homepage()->id,'quicklinks','quicklinks');?>
	</div>
					
	<?//= $subtpl_footer; ?>
</div>
