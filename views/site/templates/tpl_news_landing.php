<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<div class="wrapper">
	<?= new View('site/subtpl_siteheader');?>
	<div id="navigation">
		<?= new View('site/subtpl_logo');?>
		<?= new View('site/nav/left'); ?>		
	</div>
	<div id="main-content">
		<div class="headings">
			<h1 class="pageTitle" id='sledge-page-title'><?= $page->title ?></h1>
			<?= $page->get_slot('text', 'standfirst', '<h2 class="standFirst">', '</h2>','ch,ins'); ?>
		</div>
		<?= $page->get_slot('text', 'bodycopy', '<div id="content">', '</div>');?>			
		<h2>
			<?= $page->title ?>
		</h2>
		<ul class="news-listing">
			<?
				// Get 10 Child pages of the news page.
				foreach ($page->mptt->children(null, null, 10) as $child )
				{
					echo View::factory('site/subtpl_newslanding_item', array('page' => $child->page));
				}
			?>
		</ul>
			<?
				//if ($this->pagination->total_pages > 1) {
				//	echo $this->pagination->create_links();
				//}
			?>
	</div>
	<div id="aside">
		<?//= new View('site/subtpl_news_archive');?>
		<?= $page->get_slot( 'feature', 'feature1', 'right');?>
	</div>
	<?= new View('site/subtpl_footer'); ?>
</div>
