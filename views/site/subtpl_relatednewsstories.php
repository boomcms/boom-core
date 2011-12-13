<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<?
	foreach (Tag::get_tag_appliedto("News landing", "page") as $iterator) {
		$news_object = O::fa('page',$iterator->to_rid);
		break;
	}
	if (!isset($news_object)) {return;}
	$news_stories_array = array(); $news_stories = $news_object->get_child_pages(false,NULL,3,NULL);
	foreach ($news_stories as $news_story) { $news_stories_array[] = $news_story; }
?>

<div class="news style2">
	<h3>Recent news</h3>
	<ul>
		<?foreach ($news_stories_array as $item) {?>
			<li>
				<p>
					<strong><?=$item->title?></strong>
					<?= O::f('chunk_text_v')->get_chunk($item->rid, 'standfirst'); ?>
					<br/><a href="<?=$item->absolute_uri()?>" class="readmore">Read more &raquo;</a>
				</p>
			</li>
		<?}?>
		<li>
			<a class="readmore" href="<?=$news_object->absolute_uri()?>">More news&raquo;</a>
		</li>
	</ul>
</div>
