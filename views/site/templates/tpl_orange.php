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
				$page->get_slot('text', 'standfirst', '<h2 class="standFirst">', '</h2>' );
			?>
		</div>
		<?
			$page->get_slot('text', 'bodycopy', '<div id="content">', '</div>' );
		?>
		<div id="nav-widget"></div>		
		<?
			$page->get_slot('feature', 'feature3' );
			$page->get_slot('feature', 'feature4' );
		?>
	</div>
	<div id="aside">	
		<?
			$page->get_slot('feature', 'feature1' );
			$page->get_slot('feature', 'feature2' );
		?>
		<?//= O::f('chunk_linkset_v')->get_chunk(O::f('site_page')->get_homepage()->id,'quicklinks','quicklinks');?>
	</div>
					
	<?//= $subtpl_footer; ?>
</div>
