<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<li>
	<? $pic = O::fa('asset')->join('chunk_asset_v','chunk_asset_v.asset_rid','asset_v.rid')->join('chunk_asset','chunk_asset_v.id','chunk_asset.active_vid')->where("chunk_asset_v.page_vid={$target->vid} and slotname='newsheaderimage'")->find(); ?>
	<? if ($pic->id) {?>
		<div class="first">
			<a href="<?=$target->absolute_uri();?>" title="<?=$target->title;?>">
				<img src="/get_asset/<?=$pic->rid?>/120/100/85/0" alt="<?=$pic->description?>" />
			</a>
		</div>
		<div class="description">
	<? } else {?>
		<div>
	<?}?>
		<h3>
			<a href="<?=$target->absolute_uri()?>" title="<?=$target->title;?>">
				<?=$target->title?>
			</a>
		</h3>
		<p>Date: <?=date('d.m.Y', $target->visiblefrom_timestamp);?></p>
		<p><?= O::f('chunk_text_v')->get_chunk($target->rid, 'standfirst');?></p>
		<p class-"more"><a href="<?=$target->absolute_uri();?>" title="<?=$target->title;?>">Read more &raquo;</a></p>
</li>
