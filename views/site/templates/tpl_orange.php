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
			<h1 id='sledge-page-title' class="pageTitle"><?= $page->title?></h1>
			<? 
				$page->get_slot('text', 'standfirst', '<h2>', '</h2>' );
			?>
		</div>
		<?
			$page->get_slot('text', 'bodycopy', '<div id="content">', '</div>' );
		?>
		<div id="nav-widget">
			
		<? $page->get_slot('slideshow', 'slide1' ); ?>	
			
		</div>		
		<?
			$page->get_slot('feature', 'feature3' );
			$page->get_slot('feature', 'feature4' );
		?>
	</div>
	<div id="aside">	
		<?
			$page->get_slot('feature', 'feature1' );
			$page->get_slot('feature', 'feature2' );
			$page->get_slot( 'linkset', 'quicklinks' );
		?>
	</div>
					
	<?//= $subtpl_footer; ?>
</div>
