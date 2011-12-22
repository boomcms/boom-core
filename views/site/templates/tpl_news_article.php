<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<div class="wrapper">
	<?= new View('site/subtpl_siteheader');?>
	<div id="navigation">
		<?= new View('site/subtpl_logo'); ?>
		<?= new View('site/nav/left'); ?>		
	</div>
	<div id="main-content">
		<div class="headings">
		
			<h1 class="pageTitle"><?= $page->title?></h1>
			<?= $page->getSlot('asset', 'newsheaderimage', 'image_news');?>
		
			<?= $page->getSlot('text', 'standfirst', '<h2 class="standFirst">', '</h2>','ch,ins'); ?>
		</div>
		
		<?= $page->getSlot('text', 'bodycopy', '<div id="content">', '</div>');?>
					
	</div>
	<div id="aside">
		<?= $page->getSlot('text', 'quote', '<h3 class="quote">','</h3>','ch,ins'); ?>
		<?= new View('site/subtpl_relatednewsstories');?>
		<?= new View('site/subtpl_news_archive');?>
		<?= $page->getSlot('feature', 'feature1', 'right');?>
		<?= $page->getSlot('feature', 'feature2', 'right');?>
	</div>
	<?= new View('site/subtpl_footer'); ?>
</div>
