<?
	$pages_tag = Tag::find_or_create_tag(1, 'Pages');
	$categories_tag = Tag::find_or_create_tag($pages_tag->rid, 'Categories');
	$tags_tag = Tag::find_or_create_tag($pages_tag->rid, 'Tags');

	$news_page = O::f($this->page_model)->find_by_internal_name('news');
	$archived_blog_dates = News::get_archived_news_dates($news_page, 'desc', 'desc', true);
	$archived_blog_categories = News::get_archived_news_categories($news_page, $categories_tag, 'asc', 'category', true);
	$archived_blog_tags = News::get_archived_news_categories($news_page, $tags_tag, 'asc', 'tag', true);
?>

<div id="news-archive">
	<h2><a href="<?=$news_page->absolute_uri();?>"><? echo __( 'Archive' ); ?></a></h2>
	<? 
	$open_cats = isset($_COOKIE['news_archive']) ? explode(',', $_COOKIE['news_archive']) : array();
	?>

	<ul>
		<li>
			<h3 class="title"><? echo __( 'Date' ); ?></h3>
			<?if (count($archived_blog_dates)){?>
				<ul>
					<? foreach ($archived_blog_dates['years'] as $_year) { ?>
						<li class="year">
							<a class="toggle-archive" title="Toggle display of this section" href="#toggle">+</a>
							<span class="title"><?=$_year?></span>
							<ul>
								<? foreach ($archived_blog_dates['dates'] as $date) {
									if ($date['year'] == $_year) {?>
										<li>
											<a href="<?=$date['uri'];?>"<? if (isset($_GET['year']) and isset($_GET['month']) and $_GET['year'] == $date['year'] and $_GET['month'] == $date['month']) { ?> class="selected"<? } ?>>
												<?=$date['month_text']?> <span class="number">(<?=$date['posts']?> post<?if($date['posts'] != 1) echo 's';?>)</span>
											</a>
										</li>
									<?}
								}?>
							</ul>
						</li>
					<?}?>
				</ul>
			<?}?>
		</li>
		<li>
			<h3 class="title"><? echo __( 'Categories' ); ?></h3>
			<? if (count($archived_blog_categories)) {?>
				<ul>
					<? foreach ($archived_blog_categories as $tag_rid => $category) {?>
						<li>
							<a href="<?=$category['uri'];?>"<? if (isset($_GET['category']) and (int) $_GET['category'] == $category['tag']['rid']){?> class="selected"<? } ?>>
								<?=htmlspecialchars($category['tag']['name'])?> <span class="number">(<?=count($category['pages']);?> post<? if (count($category['pages']) != 1) {echo 's';} ?>)</span>
							</a>
						</li>
					<?}?>
				</ul>
			<?} else {?>
				<small>(<? echo __( 'No categories' ); ?>)</small>
			<?}?>
		</li>
	</ul>
</div>


