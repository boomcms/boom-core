<?php
	# Copyright 2011, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?
$pages = Tag::find_or_create_tag(1,'Pages');
$categories = Tag::find_or_create_tag($pages->rid,'Categories');
$tag = Relationship::find_partner('tag',$this->page)->where("tag_v.parent_rid = $categories->rid")->find();
if ($tag->rid) {
	$_GET['category'] = $tag->rid;
}
$news = News::get_news('news',3);
?>
<div class="box">
	<div class="box-ne">
		<div class="box-se">
			<div class="box-nw">
				<div class="box-sw">
					<h2>News</h2>
					<ul>
						<?if ($news) {?>
							<?foreach ($news as $page) {?>
								<li>
									<a href="<?=$page->getAbsoluteUri()?>">
										<h3><?=$page->current_version->getTitle()?></h3>
										<p style="color: #999;"><?=date('j F Y',$page->current_version->visiblefrom_timestamp)?></p>
										<p><?=preg_replace('/<[^>]+>/', '', O::f('chunk_text_v')->get_chunk($page->rid,'standfirst'))?></p>
									</a>
								</li>
							<?}?>
						<?}?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
