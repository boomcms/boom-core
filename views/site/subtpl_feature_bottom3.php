<?php
	# Copyright 2011, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<div id="feature-bottom">
	<div class="feature-bottom">
		<?= O::fa('chunk_text')->get_chunk($this->page->rid, 'feature-heading-1', '<h3>', '</h3>','ch,ins,br,fo'); ?>
		<?//= O::f('chunk_asset_v')->get_chunk($this->page->rid, 'feature-image-1', 'feature_bottom_image'); ?>
		<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature-intro-1', 'bottom3'); ?>
	</div>
	<div class="feature-bottom">
		<?= O::fa('chunk_text')->get_chunk($this->page->rid, 'feature-heading-2', '<h3>', '</h3>','ch,ins,br,fo'); ?>
		<?//= O::f('chunk_asset_v')->get_chunk($this->page->rid, 'feature-image-2', 'feature_bottom_image'); ?>
		<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature-intro-2', 'bottom3'); ?>
	</div>
	<div class="feature-bottom last">
		<?= O::fa('chunk_text')->get_chunk($this->page->rid, 'feature-heading-3', '<h3>', '</h3>','ch,ins,br,fo'); ?>
		<?//= O::f('chunk_asset_v')->get_chunk($this->page->rid, 'feature-image-3', 'feature_bottom_image'); ?>
		<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature-intro-3', 'bottom3');?>
	</div>
</div>