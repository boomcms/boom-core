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
		<a rel="ajax" title="<?=$ki->recursing['tag_contents']->title?>" id="<?=$ki->type?>_<?=$ki->recursing['tag_contents']->rid?>" href="#<?=$ki->type?>/<?=$ki->recursing['tag_contents']->rid?>" class="type_<?=$asset_main_type->name?> ext_<?=end(explode('.', $ki->recursing['tag_contents']->filename));?>">
		<?/*<a title="<?=$ki->recursing["tag_contents"]->title?>" rel="ajax" id="<?=$ki->type?>_<?=$ki->recursing["tag_contents"]->rid?>" href="#<?=$ki->type?>/<?=$ki->recursing["tag_contents"]->rid?>" class="<?=$ki->type?> <?=$asset_sub_type->name?> type_<?=$asset_main_type->name?> ext_<?=end(explode('.', $ki->recursing["tag_contents"]->filename));?>">*/?>
	<?}?>
	<span style="display:block;height:100px;width:100px;background:url(/sledge/img/icons/pageicon1.png) no-repeat center center">&nbsp;</span>       
	<?if (!$ki->recursing["rubbishrow"]) {?>
		</a>
	<?}?>
	<?
		// list the item's tags
		foreach (Relationship::find_partners('tag',$ki->recursing["tag_contents"])->orderby('tag_v.name')->find_all() as $tag) {
			$full_tagstr = '<span class="tags">';
			$ancestor_tags = $ki->get_ancestortags($tag->rid, false, true);
			foreach($ancestor_tags as $tag_ancestor) {
				$full_tagstr .= $tag_ancestor->name.' &raquo; ';
			}
			$full_tagstr = rtrim($full_tagstr, ' &raquo;').'</span>';
			$small_tagstr = '<span class="tags" title="'.htmlentities($full_tagstr).'">';
			if (!$ki->recursing["rubbishrow"]) {
				$small_tagstr .= '<a name="#tag/'.$tag->rid.'" href="#tag/'.$tag->rid.'">';
			}
			$small_tagstr .= $tag->name;
			if (!$ki->recursing["rubbishrow"]) {
				$small_tagstr .= '</a>';
			}
			$small_tagstr .= (sizeof($ancestor_tags)>1?' &raquo;':'').'</span>';
			//echo $small_tagstr;
			?>
		<?}?>
	<?if ($ki->recursing["tag_contents"]->rubbish=='t' and $ki->tag->name!='Rubbish'){?>
	<?}?>
