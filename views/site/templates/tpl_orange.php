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
			<?= $page->getChunk('text', 'standfirst', '<h2 class="standFirst">', '</h2>','ch,ins'); ?>
		</div>
		<?= $page->getChunk('text', 'bodycopy', '<div id="content">', '</div>');?>
		<div id="nav-widget"></div>			
		<?= $page->getChunk('feature', 'feature3', 'centre');?>
		<?= $page->getChunk('feature', 'feature4', 'centre');?>
	</div>
	<div id="aside">	
		<?= $page->getChunk('feature', 'feature1', 'right');?>
		<?= $page->getChunk('feature', 'feature2', 'right');?>
		<?//= O::f('chunk_linkset_v')->get_chunk(O::f('site_page')->get_homepage()->id,'quicklinks','quicklinks');?>
	</div>
					
	<?//= $subtpl_footer; ?>
</div>
