<?
	$blog_parent_page = O::f($this->page_model)->find_by_internal_name('blog');
	
	$pages_tag = Tag::find_or_create_tag(1, 'Pages');
	$categories_tag = Tag::find_or_create_tag($pages_tag->rid, 'Categories');
	$tags_tag = Tag::find_or_create_tag($pages_tag->rid, 'Tags');
	
	$archived_news_dates = News::get_archived_news_dates($blog_parent_page, 'desc', 'asc');
	$archived_news_categories = News::get_archived_news_categories($blog_parent_page, $categories_tag);
?>
<h2>
	<a href="<?=$blog_parent_page->absolute_uri();?>">
		Archive
	</a>
</h2>
<?if (count($archived_news_dates)){?>
	<? foreach ($archived_news_dates['years'] as $_year) { ?>
		<dl>
			<dt>
				<span>
					<?=$_year?>
				</span>
			</dt>
			<? foreach ($archived_news_dates['dates'] as $date) {
				if ($date['year'] == $_year) {?>
					<dd>
						<a href="<?=$date['uri'];?>">
							<?=$date['month_text']?> (<?=$date['posts']?> post<?if($date['posts'] != 1) echo 's';?>)
							<span>&raquo;</span>
						</a>
					</dd>
				<?}
			}?>
		</dl>
	<?}
}?>
<dl>
	<dt>
		<span>
			Categories
		</span>
	</dt>
	<? if (count($archived_news_categories)){
		foreach ($archived_news_categories as $tag_rid => $category) {?>
			<dd>
				<a href="<?=$category['uri'];?>">
					<?=$category['tag']['name']?> (<?=count($category['pages']);?> post<?if (count($category['pages']) != 1) {echo 's';}?>)
					<span>&raquo;</span>
				</a>
			</dd>
		<?}
	} else {?>
		<dd>
			<em>
				(No categories)
			</em>
		</dd>
	<?}?>
</dl>
