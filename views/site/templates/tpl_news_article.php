<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<div class="wrapper">
	<?= new View('site/subtpl_siteheader');?>
	<div id="navigation">
		<?= new View('site/subtpl_logo'); ?>
		<?= new View('site/subtpl_leftnav'); ?>		
	</div>
	<div id="main-content">
		<div class="headings">
		
			<h1 class="pageTitle"><?= $this->page->title?></h1>
			<?= O::f('chunk_asset_v')->get_chunk($this->page->rid, 'newsheaderimage', 'image_news');?>
		
			<?= O::f('chunk_text_v')->get_chunk($this->page->rid, 'standfirst', '<h2 class="standFirst">', '</h2>','ch,ins'); ?>
		</div>
		
		<?= O::f('chunk_text_v')->get_chunk($this->page->rid, 'bodycopy', '<div id="content">', '</div>');?>
					
	</div>
	<div id="aside">
		<?= O::f('chunk_text_v')->get_chunk($this->page->rid, 'quote', '<h3 class="quote">','</h3>','ch,ins'); ?>
		<?= new View('site/subtpl_relatednewsstories');?>
		<?= new View('site/subtpl_news_archive');?>
		<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature1', 'right');?>
		<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature2', 'right');?>
	</div>
	<?= new View('site/subtpl_footer'); ?>
</div>
