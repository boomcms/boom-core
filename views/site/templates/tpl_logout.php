<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<div class="yui-t2 wrapper">
	<div class="padd clearfix">
		
		<!-- right primary block -->
		<div id="yui-main">
			<div class="yui-b">

				<?= new View('site/subtpl_siteheader');?>
				
				<div class="yui-gc">
					<div class="yui-g first">
						<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature1', 'right');?>
						<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature2', 'right');?>
						<?= O::f('chunk_linkset_v')->get_chunk(O::f('site_page')->get_homepage()->id,'quicklinks','quicklinks');?>
					</div>
					
					<div class="yui-g">
						<h1 class="pageTitle"><?= $this->page->title?></h1>
						<?= O::f('chunk_text_v')->get_chunk($this->page->rid, 'standfirst', '<h2 class="standFirst">', '</h2>','ch,ins'); ?>
						<?= O::f('chunk_text_v')->get_chunk($this->page->rid, 'bodycopy', '<div id="content">', '</div>');?>			
						<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature3', 'centre');?>
						<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature4', 'centre');?>
					</div>
				</div>
			</div>
		</div>

		<!-- left secondary block -->
		<div class="yui-b">
			<?= new View('site/subtpl_logo');?>
			<?= new View('site/subtpl_leftnav'); ?>		
			<?= new View('site/subtpl_newsletter');?>
		</div>

	</div>
	<?= new View('site/subtpl_footer'); ?>
</div>
