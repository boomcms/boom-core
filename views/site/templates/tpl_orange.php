<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<div class="wrapper">
	<?= new View('site/subtpl_siteheader');?>
	<div id="navigation">
		<?= new View('site/subtpl_logo');?>
		<?= new View('site/nav/subtpl_left'); ?>		
		<?= new View('site/subtpl_newsletter');?>
	</div>
	<div id="main-content">
		<div class="headings">
			<h1 class="pageTitle"><?= $page->version->title?></h1>
			<? 
				if ($page->getSlot('text', 'standfirst' ))
				{ 
					echo '<h2 class="standFirst">' . $page->getSlot('text', 'standfirst' ) . '</h2>';
				}
			?>
		</div>
		<?
			if ($page->getSlot('text', 'bodycopy' ))
			{
				echo '<div id="content">' . $page->getSlot( 'text', 'bodycopy' ) . '</div>';
			}
		?>
		<div id="nav-widget"></div>		
		<?
			if ($slot = $page->getSlot('feature', 'feature3' ))
			{
				echo View::factory( 'site/slots/slottype/feature/subtpl_center' )->bind( 'slot', $slot );
			}
			
			if ($slot = $page->getSlot('feature', 'feature4' ))
			{
				echo View::factory( 'site/slots/slottype/feature/subtpl_center' )->bind( 'slot', $slot );
			}
		?>
	</div>
	<div id="aside">	
		<?
			if ($slot = $page->getSlot('feature', 'feature3' ))
			{
				echo View::factory( 'site/slots/slottype/feature/subtpl_right' )->bind( 'slot', $slot );
			}
			
			if ($slot = $page->getSlot('feature', 'feature4' ))
			{
				echo View::factory( 'site/slots/slottype/feature/subtpl_right' )->bind( 'slot', $slot );
			}
		?>
		<?//= O::f('chunk_linkset_v')->get_chunk(O::f('site_page')->get_homepage()->id,'quicklinks','quicklinks');?>
	</div>
					
	<?//= $subtpl_footer; ?>
</div>
