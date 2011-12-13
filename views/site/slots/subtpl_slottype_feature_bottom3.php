<?php
	# Copyright 2011, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?
	$feature_image = Relationship::find_partners('asset', $target)->where("rel1.description = 'featureimage' and rel2.description = 'featureimage'")->find();
?>
<?if ($feature_image->rid){?>
	<a href="<?=$target->absolute_uri();?>">
		<img alt="<?=htmlspecialchars($feature_image->description);?>" src="/_ajax/call/asset/get_asset/<?=$feature_image->rid?>/130/90/0/1">
	</a>
<?}?>
<h4><?=$target->title?></h4>
<p><?=preg_replace('/<[^>]+>/', '', O::f('chunk_text_v')->get_chunk($target->rid,'standfirst'))?></p>
<p class="more"><a href="<?=$target->absolute_uri();?>">Read more</a></p>
