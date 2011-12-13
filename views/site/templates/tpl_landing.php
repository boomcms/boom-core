<?php
	# Copyright 2011, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<div class="wrapper">
	<?= new View('site/subtpl_siteheader');?>
	<div id="navigation">
		<?= new View('site/subtpl_logo');?>
		<?= new View('site/subtpl_leftnav'); ?>
		<?= new View('site/subtpl_newsletter');?>
	</div>
	<div id="main-content">
		<div class="headings">
			<h1 class="pageTitle"><?=$this->page->title;?></h1>
			<?= O::fa('chunk_text')->get_chunk($this->page->rid, 'standfirst', '<h2 class="standFirst">', '</h2>','ch,ins,br'); ?>
		</div>
		<?= O::fa('chunk_text')->get_chunk($this->page->rid, 'bodycopy', '<div id="content">', '</div>');?>
		<?//  $toplevel_tag = O::fa('tag')->find_by_name_and_parent_rid('Assets', 1); ?>
		<?//= O::f('chunk_tag_v')->get_chunk($this->page->rid, 'slideshow', 'carousel_slideshow', false, true, false, $toplevel_tag->rid);?>
		<div id="nav-widget">
		</div>
		<div id="features">
			<div class="feature-row">
				<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature1', 'main_left');?>
				<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature2', 'main');?>
			</div>
			<div class="feature-row">
				<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature3', 'main_left');?>
				<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature4', 'main');?>
			</div>
		</div>
	</div>
	<div id="aside">
		<?= O::f('chunk_asset_v')->get_chunk($this->page->rid, 'video', 'sidevideo'); ?>
	</div>
	<?= new View('site/subtpl_footer');?>
</div> 
