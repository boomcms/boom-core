<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<?$ki = Kohana::Instance();?>
<div class="col2 date">
	<?=date("d-M-y", strtotime($ki->recursing["tag_contents"]->audit_time))?>
</div>
<div class="col3 title">
	<?
	$asset_sub_type = O::fa('asset_type', $ki->recursing["tag_contents"]->asset_type_rid);
	$asset_main_type = O::fa('asset_type', $asset_sub_type->parent_rid);
	?>
	<a title="<?=$ki->recursing["tag_contents"]->title?>" rel="ajax" id="<?=$ki->type?>_<?=$ki->recursing["tag_contents"]->rid?>" href="#<?=$ki->type?>/<?=$ki->recursing["tag_contents"]->rid?>" class="<?=$ki->type?> <?=$asset_sub_type->name?> type_<?=$asset_main_type->name?> ext_<?=end(explode('.', $ki->recursing["tag_contents"]->filename));?>">
		<?=$ki->recursing["tag_contents"]->title?>
	</a>
	<small><a title="Type: <?=$asset_sub_type->name?>; Filesize: <?=$ki->recursing['tag_contents']->get_filesize();?>" href="/_ajax/call/asset/get_asset/<?=$ki->recursing['tag_contents']->rid?>/0/0/0/0/0/1" style="background:transparent;padding:0px">Download</a></small>
</div>&nbsp;
