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
							<!-- quote -->
							<?= O::f('chunk_text_v')->get_chunk($this->page->rid, 'quote', '<h3 class="quote">','</h3>','ch,ins'); ?>
		
							<!-- secondary story -->
							<?= new View('site/subtpl_secondarystory');?>
							
							<!-- slot image -->
							<?= new View('site/subtpl_slotimage'); ?>

							<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature1', 'right');?>
							<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature2', 'right');?>
					</div>
					
					<div class="yui-g">
						<h1 class="pageTitle"><?= $this->page->title?></h1>
						<?= O::f('chunk_text_v')->get_chunk($this->page->rid, 'standfirst', '<h2 class="standFirst">', '</h2>','ch,ins'); ?>
						<?= O::f('chunk_text_v')->get_chunk($this->page->rid, 'bodycopy', '<div id="content">', '</div>');?>

						<br />
						<h2>RSS Feeds</h2>
						<? $rss_tag = O::fa('tag')->find_by_name('Has RSS');
						$pages = array();
						foreach (Relationship::find_partners('site_page',$rss_tag,false)->find_all() as $page_v) {
							echo '<a href="'.$page_v->absolute_uri().'/.rss">'.$page_v->title.'</a><br />';
						}
						?>
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
