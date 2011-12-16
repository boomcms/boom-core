<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<?$ki = Kohana::Instance();?>
<?
	$asset_sub_type = O::fa('asset_type', $ki->recursing["tag_contents"]->asset_type_rid);
	$asset_main_type = O::fa('asset_type', $asset_sub_type->parent_rid);
?>
<?if (!$ki->recursing["rubbishrow"]) {?>
	<div>
		<a rel="ajax" title="<?=$ki->recursing['tag_contents']->title?>" id="<?=$ki->type?>_<?=$ki->recursing['tag_contents']->rid?>" href="#<?=$ki->type?>/<?=$ki->recursing['tag_contents']->rid?>" class="type_<?=$asset_main_type->name?> ext_<?=end(explode('.', $ki->recursing['tag_contents']->filename));?>">
			<span style="display:block;height:100px;width:100px;background:url(/_ajax/call/asset/get_asset/<?=$ki->recursing['tag_contents']->rid?>/100/100) no-repeat center center">&nbsp;</span>       
		</a>
		<small title="<?=$ki->recursing['tag_contents']->title;?>"><?=$ki->recursing['tag_contents']->title?></small>
		<small><?=date("d M y", strtotime($ki->recursing["tag_contents"]->audit_time))?></small>
		<small style="font-weight:bold">
			<a title="Type: <?=$asset_sub_type->name?>; Filesize: <?=$ki->recursing['tag_contents']->get_filesize();?>" href="/_ajax/call/asset/get_asset/<?=$ki->recursing['tag_contents']->rid?>/0/0/0/0/0/1" class="download">Download</a>
		</small>
	</div>
<?}?>
