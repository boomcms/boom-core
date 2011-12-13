<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<div class="wrapper">
	<?= new View('site/subtpl_siteheader');?>
	<div id="navigation">
		<?= new View('site/subtpl_logo');?>
		<?= new View('site/subtpl_leftnav'); ?>		
	</div>
	<div id="main-content">
		<div class="headings">
			<h1 class="pageTitle"><?= $this->page->title?></h1>
			<?= O::f('chunk_text_v')->get_chunk($this->page->rid, 'standfirst', '<h2 class="standFirst">', '</h2>','ch,ins'); ?>
		</div>
		<?= O::f('chunk_text_v')->get_chunk($this->page->rid, 'bodycopy', '<div id="content">', '</div>');?>			
		<h2><?=$this->page->title?></h2>
		<?
			$page_num = isset($_GET['p']) ? (int)$_GET['p'] : 1;
			$items_per_page = 10;
			$offset = ($page_num-1) * $items_per_page;
			$total_items = count($this->page->get_child_pages());
			$pagination_config = array(
				'base_url' => $this->page->absolute_uri(),
				'current_page' => $page_num,
				'total_items' => $total_items,
				'items_per_page' => $items_per_page,
				'sql_offset' => $offset,
				'style' => 'news'
			);
			$this->pagination = new Pagination($pagination_config);
		
			if($this->pagination->total_pages > 1) {
				echo $this->pagination->create_links();
			}
			?>
			<ul class="news-listing">
				<?
					foreach ($this->page->get_child_pages(false, NULL, $items_per_page, $offset) as $this->procpage) {
						echo new View('site/subtpl_newslanding_item',array('target' => $this->procpage));
					}
				?>
			</ul>
			<?
				if($this->pagination->total_pages > 1) {
					echo $this->pagination->create_links();
				}
			?>
	</div>
	<div id="aside">
		<?//= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature1', 'rss_right');?>
		<?= new View('site/subtpl_news_archive');?>
		<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature1', 'right');?>
	</div>
	<?= new View('site/subtpl_footer'); ?>
</div>
