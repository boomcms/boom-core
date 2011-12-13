<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
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
			<h1 class="pageTitle"><?= $this->page->title?></h1>
			<?= O::f('chunk_text_v')->get_chunk($this->page->rid, 'standfirst', '<h2 class="standFirst">', '</h2>','ch,ins'); ?>
		</div>
		<?= O::f('chunk_text_v')->get_chunk($this->page->rid, 'bodycopy', '<div id="content">', '</div>');?>			
		<ol class="search-results">
		<? foreach($this->sr as $q => $p) {?>
			<li<? if ($q == 0) { echo ' class="first"'; } ?>>
				<h3>
					<a href="<?=$p->absolute_uri();?>"><?=$p->title;?></a>
				</h3>
				<p>
				<?
					$ex = explode("</p>",O::f('chunk_text_v')->get_chunk($p->rid, 'standfirst'));
					echo strip_tags($ex[0]);
				?>
				</p>
			</li>
		<?}?>
		</ol>

		<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature3', 'centre');?>
		<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature4', 'centre');?>
	</div>
	<div id="aside">	
		<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature1', 'right');?>
		<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature2', 'right');?>
		<?= O::f('chunk_linkset_v')->get_chunk(O::f('site_page')->get_homepage()->id,'quicklinks','quicklinks');?>
	</div>
					
	<?= new View('site/subtpl_footer'); ?>
</div>
