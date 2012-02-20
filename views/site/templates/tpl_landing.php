<?php
	# Copyright 2011, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
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
			<h1 class="pageTitle" id='sledge-page-title'><?=$page->title;?></h1>
			<?= $page->get_slot('text', 'standfirst', '<h2 class="standFirst">', '</h2>','ch,ins,br'); ?>
		</div>
		<?= $page->get_slot('text', 'bodycopy', '<div id="content">', '</div>');?>
		<div id="nav-widget">
		</div>
		<div id="features">
			<div class="feature-row">
				<?= $page->get_slot('feature', 'feature1', 'main_left');?>
				<?= $page->get_slot('feature', 'feature2', 'main');?>
			</div>
			<div class="feature-row">
				<?= $page->get_slot('feature', 'feature3', 'main_left');?>
				<?= $page->get_slot('feature', 'feature4', 'main');?>
			</div>
		</div>
	</div>
	<div id="aside">
		<?= $page->get_slot('asset', 'video', 'sidevideo'); ?>
	</div>
	<?= new View('site/subtpl_footer');?>
</div> 
