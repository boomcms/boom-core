<?
	$blog_page = O::f($this->page_model)->find_by_internal_name('blog');
	$pages_tag = Tag::find_or_create_tag(1, 'Pages');
	$categories_tag = Tag::find_or_create_tag($pages_tag->rid, 'Categories');
	$tags_tag = Tag::find_or_create_tag($pages_tag->rid, 'Tags');
?>
<p>
	<strong>
		Categories:
	</strong>
	<?
		$categories_tags = $this->page->get_tags($categories_tag->rid);
		
		foreach($categories_tags as $tag) {?>
			<a href="<?=$blog_page->absolute_uri();?>?category=<?=$tag->rid;?>">
				<?=$tag->name;?>
			</a>
		<?}
	?>
</p>
<p>
	<strong>
		Tagged:
	</strong>
	<?
		$tags = $this->page->get_tags($tags_tag->rid);
		foreach($tags as $tag) {?>
			<a href="<?=$blog_page->absolute_uri();?>?category=<?=$tag->rid;?>">
				<?=$tag->name;?>, 
			</a>
		<?}
	?>
</p>
