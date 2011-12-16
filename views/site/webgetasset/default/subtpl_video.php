<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<div class="video">
	<?if (get_class($this) == 'Cms_page_manager_Controller'){?>
		<div id="cms-flash-overlay" style="width:328px;height:575px;"></div>
	<?}?>
	<script type="text/javascript" src="/sledge/js/swfobject.js"></script>
		<?
			$asset_rid = $this->uri->last_segment();

			$related_pic = Relationship::find_partner('asset','asset',$asset_rid)->find();

			# if a related asset wasn't found, try getting an asset related to the 'Default video related asset' tag
			if ($related_pic->rid) {
				# get the video related tag
				$id_tag = Tag::find_or_create_tag(null,'Tags')->rid;
				$id_system = Tag::find_or_create_tag($id_tag,'System')->rid;
				$id_video_related_tag = Tag::find_or_create_tag($id_system,'Default video related asset')->rid;
				# if there's a video related tag
				if ($id_video_related_tag and ($id_video_related_tag > 0)) {
					# get the video pic
					$related_pic = Relationship::find_partner('asset','tag',$id_video_related_tag)->find();
				}
			}
		?>
	<div id="video-player">
		<p class="error">
			<em>
				To view video, you need to have the Flash plugin installed. To get Flash, please visit the <a href="http://www.adobe.com/go/getflashplayer">Adobe website</a>.
			</em>
		</p>
		<p>
			<?if ($related_pic->rid) {?>
				<img src="/get_asset/<?=$related_pic->rid?>/575/328" alt="<?=htmlspecialchars($related_pic->description);?>" />
			<?} else {?>
				<img src="/sledge/img/default-video-related-asset.jpg" alt="video" />
			<?}?>
		</p>
	</div>
	<div class="video-print-image">
		<p>
			<?if ($related_pic->rid) {?>
				<img src="/get_asset/<?=$related_pic->rid?>/575/328" alt="<?=htmlspecialchars($related_pic->description);?>" />
			<?} else {?>
				<img src="/sledge/img/default-video-related-asset.jpg" alt="video" />
			<?}?>
		</p>
	</div>
		
	<script type="text/javascript">
	//<![CDATA[
	var so = new SWFObject('/sledge/img/player.swf','mpl','575','328','9');
	so.addParam('allowscriptaccess','always');
	so.addParam('allowfullscreen','true');
	so.addParam('wmode','transparent');
		<?
			# if we still don't have a picture, use the hardcoded one, else render the retrieved one
			if ($related_pic->rid) {
				?>so.addParam('flashvars','&image=/get_asset/<?=$related_pic->rid?>/575/328&stretching=exactfit&skin=/sledge/img/nacht.swf&file=/get_asset/<?=$asset_rid;?>/575/328/NULL/NULL/1/0/1/null.flv');<?
			} else {
				?>so.addParam('flashvars','&image=/sledge/img/default-video-related-asset.jpg&stretching=exactfit&skin=/sledge/img/nacht.swf&file=/get_asset/<?=$asset_rid;?>/575/328/NULL/NULL/1/0/1/null.flv');<?
			}
		?>
		so.write('video-player');
		//]]>
	</script>
	<div class="video-footnote">
		<?=O::fa('asset', $asset_rid)->description;?>
	</div>
</div>
