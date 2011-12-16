<?php
	# Copyright 2011, Hoop Associates Ltd
	# Hoop Associates		www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?
	$toplevel_tag_rid = isset($_REQUEST['toplevel_tag']) && (is_int($_REQUEST['toplevel_tag']) || ctype_digit($_REQUEST['toplevel_tag'])) ? $_REQUEST['toplevel_tag'] : $toplevel_rid;
	if ($target->item_ordering_policy_rid) {
		switch ($target->item_ordering_policy_rid) {
			case 1: $orderby = 'asset.sequence'; break;
			case 2: $orderby = 'title'; break;
			case 3: $orderby = 'asset_v.audit_time'; break;
		}
	} else {
		$orderby = 'asset_v.audit_time';
	}
	if ($target->item_ordering_direction) {
		switch ($target->item_ordering_direction) {
			case 1: $order = 'asc'; break;
			case 2: $order = 'desc'; break;
		}
	} else {
		$order = 'desc';
	}
	$images = Relationship::find_partners('asset', 'tag', $target->rid)->orderby($orderby, $order)->limit(22)->find_all();
?>
<?
$metadata = Tag::find_or_create_tag(1,'Metadata');
$assets = Tag::find_or_create_tag($metadata->rid,'Assets');
?>
<? if(count($images)) {?>
	<div id="carousel_slideshow" class="rel-toplevel-<?=$toplevel_tag_rid;?>">
		<div class="wrapper">
		<ul>
			<?
			$image_rids = array();
			foreach ($images as $image) {
				$image_rids[] = $image->rid;
			}

			$metadata = array();
			$slideshow_rids = array();
			foreach (Relationship::find_partners('metadata_v',$assets)->where("metadata_v.item_tablename='asset' and metadata_v.item_rid IN (".implode(',',$image_rids).") and (key = 'default-caption' or key = 'slideshow-url')")->find_all() as $m) {
				$metadata[$m->item_rid][$m->key] = $m->value;
				if ($m->key == 'slideshow-url' && ctype_digit($m->value)) $slideshow_rids[] = $m->value;
			}
			$slideshow_pages = array();
			if (!empty($slideshow_rids)) {
				foreach (O::f($this->page_model)->where("rid in (".implode(',',$slideshow_rids).")")->find_all() as $page) {
					$slideshow_pages[$page->rid] = $page;
				}
			}
			foreach($images as $image){
				$caption = @$metadata[$image->rid]['default-caption'];
				$url = (isset($slideshow_pages[$metadata[$image->rid]['slideshow-url']]) ? $slideshow_pages[$metadata[$image->rid]['slideshow-url']]->absolute_uri() : '');
			?>
				<li>
					<?if ($url != ''){?>
						<a href="<?=$url?>">
							<img src="/_ajax/call/asset/get_asset/<?=$image->rid?>/210/140/85/1" alt="<?=htmlspecialchars($image->description)?>" />
						</a>
					<?} else {?>
						<img src="/_ajax/call/asset/get_asset/<?=$image->rid?>/210/140/85/1" alt="<?=htmlspecialchars($image->description)?>" />
					<?}?>
					
					<?if ($caption != ''){?>
						<div class="alt-text">
							<p><a href="<?=$url?>"><?=$caption?></a></p>
						</div>
					<?}?>
				</li>
			<?}?>
		</ul>
		</div>
	</div>
	<script type="text/javascript">
		//<![CDATA[
			$('#carousel_slideshow').anythingSlider({
			});
		//]]>
	</script>
<?} else {?>
	<div id="carousel_slideshow" class="rel-toplevel-<?=$toplevel_tag_rid;?>">
		<p class="error">
			<em>
				There are no images for this tag. 
				This image library will only appear when you have tagged some images with the tag: '<?=$target->name;?>'.
				You can change the tag for this slot by clicking in this area.
			</em>
		</p>
	</div>
<?}?>
