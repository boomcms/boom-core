<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<?
	foreach (Tag::get_tag_appliedto("Press landing", "page") as $iterator) {
		if ($iterator->to_tablename == 'page') {
			$page_rid = $iterator->to_rid;
		} else {
			$page_rid = $iterator->from_rid;
		}
		$this->news_object = O::fa('page',$page_rid);
		break;
	}
	if (!isset($this->news_object) || !$this->news_object->id) {return;}
	$this->news_stories_array = array(); $news_stories = $this->news_object->get_child_pages(false,NULL,5,NULL);
	foreach ($news_stories as $news_story) { $this->news_stories_array[] = $news_story; }
?>

<?if (isset($this->news_stories_array[0])) {?>
<div class="news style2">
	<h3>
		<a href="<?=$this->news_object->absolute_uri()?>">
			Press releases
		</a>
	</h3>
	<ul>
		<?foreach ($this->news_stories_array as $item) {?>
			<li>
					<a href="<?=$item->absolute_uri();?>">
						<strong>
							<?=$item->title?>
							<img src="/img/arr_2.jpg" alt="press release" />
						</strong>
					</a>
					<?
?>
			</li>
		<?}?>
		<li class="last">
			<a href="<?=$this->news_object->absolute_uri()?>">
				More press releases
				<img src="/img/arr_2.jpg" alt="press release" />
			</a>
		</li>
	</ul>
</div>
<?}?>
