<?php
	# Copyright 2011, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?
if (isset($_POST)) {
	if (@$_POST['library-categories']) {
		$tag_rid = $_POST['library-categories'];
		$chunk_tag_rid = O::fa('chunk_tag')->find_by_page_vid_and_slotname($this->page->vid,'gallery')->target_tag_rid;

		if ($tag_rid != $chunk_tag_rid && !in_array($tag_rid,Tag::get_descendanttags($chunk_tag_rid))) {
			$tag_rid = $chunk_tag_rid;
		}

		$q = @$_POST['search-text'];
		$uri = $this->page->absolute_uri().'/'.$tag_rid.'/1/'.rawurlencode($q);

		header('Location: '.$uri);
		exit;
	}
}

Library::get_assets();
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
			<h1 class="pageTitle"><?=$this->page->title;?></h1>
			<?= O::fa('chunk_text')->get_chunk($this->page->rid, 'standfirst', '<h2 class="standFirst">', '</h2>','ch,ins,br'); ?>
		</div>
		<?= O::fa('chunk_text')->get_chunk($this->page->rid, 'bodycopy', '<div id="content">', '</div>');?>
		<div id="library-search">
			<form action="<?=$this->page->absolute_uri()?>" method="POST">
				<div class="clearfix">
					<h2>Search the library</h2>
				</div>
				<label for="library-categories">To show all documents that relate to a category choose from the dropdown list.</label>
				<select id="library-categories" name="library-categories">
					<option selected="selected">Choose a category</option>
					<?if ($this->chunk_tag_rid) {?>
						<?
						$descendants = Tag::get_descendanttags($this->chunk_tag_rid);
						if (!empty($descendants)) {?>
							<?foreach (O::fa('tag')->where("rid in (".implode(',',$descendants).")")->orderby('tag_v.name','asc')->find_all () as $tag) {?>
								<option value="<?=$tag->rid?>"<?if ($tag->rid == $this->tag_rid){?> selected="selected"<?}?>>
									<?=$tag->name?>
								</option>
							<?}?>
						<?}?>
					<?}?>
				</select>
				<label for="search-text" style="clear: both; display: block; margin-top: 2em;">To search for documents using specific terms type here.</label>
				<?=form::input('search-text',(@$this->search_text))?>
				<input type="submit" name="submit" value="Search" />
				<!-- p>or</p>
				<a href="<?=$this->page->absolute_uri()?>">
					View all
				</a>
				<p class="hide"><a href="#">Hide options</a></p -->
			</form>
		</div>
		<div class="library-options clearfix" style="margin-top: 1em; border-top: 1px solid #cccccc; padding-top: 1em;">
			<p><?=$this->total?> assets found</p>
		</div>
		<div class="library-pagination clearfix">
			<?=new View('site/subtpl_library_pagination')?>
		</div>
		<? $toplevel_tag = Tag::find_or_create_tag(1,'Assets'); ?>
		<?= O::fa('chunk_tag')->get_chunk($this->page->rid, 'gallery', 'gallery', false, true, false, $toplevel_tag->rid);?>
		<div class="library-pagination clearfix">
			<?=new View('site/subtpl_library_pagination')?>
		</div>
	</div>
	<div id="aside">
		<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature1', 'right');?>
		<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature2', 'right');?>
	</div>
	<?= new View('site/subtpl_footer');?>
</div>
