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
			<h1 class="pageTitle"><?= $page->title?></h1>
			<?= $page->get_slot('text', 'standfirst', '<h2 class="standFirst">', '</h2>','ch,ins'); ?>
		</div>
		<?$news_posts = News::get_news('blog', 6, 'blog_landing');
		if ($news_posts and count($news_posts)){
			if ($this->pagination->total_pages > 1) {?>
				<div class="pagination">
					<?=$this->pagination->create_links();?>
				</div>
					<?}
					foreach ($news_posts as $post) {
						$bodycopy_chunk_text = O::fa('chunk_text')->find_by_slotname_and_page_vid('bodycopy', $post->vid)->text;
						$standfirst_chunk_text = preg_replace('/<[^>]+>/', '', O::fa('chunk_text')->find_by_slotname_and_page_vid('standfirst', $post->vid)->text);?>
						<div class="newsitem clearfix">
							<h3>
								<a href="<?=$post->absolute_uri();?>">
									<?=$post->title?>
								</a>
							</h3>
							<p>
								<?=$standfirst_chunk_text;?>
							</p>
							<p class="metadata">
								<span class="date">
									<?=date('F j, Y', $post->visiblefrom_timestamp);?>
								</span>
								<a href="<?=$post->absolute_uri();?>" class="comments">
									<em>
										Add a comment
									</em>
								</a>
							</p>
						</div>
					<?}
				} else {?>
					<p><em>There are no news posts.</em></p>
				<?}?>
	</div>
	<div id="aside">
		<?= new View('site/subtpl_archived_blogposts');?>
	</div>
	<?= new View('site/subtpl_footer'); ?>
</div>
