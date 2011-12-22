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
			<h1 class="pageTitle"><?=$page->title;?></h1>
			<?= $page->getSlot('text', 'standfirst', '<h2 class="standFirst">', '</h2>','ch,ins,br'); ?>
		</div>
		<?= $page->getSlot('text', 'bodycopy', '<div id="content">', '</div>');?>
		<div id="nav-widget">
		</div>
		<div id="features">
			<div class="feature-row">
				<?= $page->getSlot('feature', 'feature1', 'main_left');?>
				<?= $page->getSlot('feature', 'feature2', 'main');?>
			</div>
			<div class="feature-row">
				<?= $page->getSlot('feature', 'feature3', 'main_left');?>
				<?= $page->getSlot('feature', 'feature4', 'main');?>
			</div>
		</div>
	</div>
	<div id="aside">
		<?= $page->getSlot('asset', 'video', 'sidevideo'); ?>
	</div>
	<?= new View('site/subtpl_footer');?>
</div> 
