<?php
	# Copyright 2011, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?php
	$video_related_asset = Relationship::find_partner('asset','asset',$target->rid)->find();

	# if a related asset wasn't found, try getting an asset related to the 'Default video related asset' tag
	if (!$video_related_asset or !$video_related_asset->rid) {
		# get the video related tag
		$id_tag = Tag::find_or_create_tag(1,'Tags')->rid;
		$id_system = Tag::find_or_create_tag($id_tag,'System')->rid;
		$id_video_related_tag = Tag::find_or_create_tag($id_system,'Default video related asset')->rid;
		# if there's a video related tag
		if ($id_video_related_tag) {
			# get the video
			$video_related_asset = Relationship::find_partner('asset','tag',$id_video_related_tag)->find();
		}
	}
	# if no video related video asset found, use a hardcoded one
	if (!$video_related_asset or !$video_related_asset->rid) {
		$video_related_asset_path = $video_related_asset_path_large = '/img/default_video.png';	
	} else {
		$video_related_asset_path = '/_ajax/call/asset/get_asset/'.$video_related_asset->rid.'/212/118/85/1/1/0/1/1/null.jpg';
		$video_related_asset_path_large = '/_ajax/call/asset/get_asset/'.$video_related_asset->rid.'/500/279/85/1/1/0/1/1/null.jpg';
	}
?>

<div class="side-video">
	 <div class="yellow-se">
        <div class="yellow-sw">
			<div id="player-<?=$target->rid;?>" class="video-player">
				<p>To view video, you need to have the Flash plugin installed. To get Flash, please visit the <a href="http://www.adobe.com/go/getflashplayer">Adobe website</a>.</em>
			</div>
			<figcaption><?=htmlspecialchars($target->description);?></figcaption>
			<? /*p>
				<a href="#TB_inline?height=279&width=500&inlineId=player-large-<?=$target->rid;?>" title="<?=$target->title;?>" rel="mpl-sm-<?=$target->rid;?>" class="thickbox2 fb_larger right">Larger version </a>
			</p */ ?>
		</div>
	</div>
</div>
<div id="player-large-<?=$target->rid;?>" class="hidden">
	<p class="error">
		<em>To view video, you need to have the Flash plugin installed. To get Flash, please visit the <a href="http://www.adobe.com/go/getflashplayer">Adobe website</a>.</em>
	</p>
</div>
						
	<script type="text/javascript">
	//<![CDATA[
		// small video
		var sm = new SWFObject('/sledge/img/player.swf','mpl-sm-<?=$target->rid;?>','285','159','9');
		sm.addParam('allowscriptaccess','always');
		sm.addParam('allowfullscreen','true');
		sm.addParam('flashvars','&image=<?=$video_related_asset_path;?>&stretching=exactfit&backcolor=#555555&frontcolor=#FFFFFF&file=/_ajax/call/asset/get_asset/<?=$target->rid;?>/NULL/NULL/NULL/NULL/1/0/1/null.flv');
		sm.write('player-<?=$target->rid;?>');

		// large video
		var lg = new SWFObject('/sledge/img/player.swf','mpl-sm-<?=$target->rid;?>','500','279','9');
		lg.addParam('allowscriptaccess','always');
		lg.addParam('allowfullscreen','true');
		lg.addParam('flashvars','&image=<?=$video_related_asset_path_large;?>&stretching=exactfit&backcolor=#555555&frontcolor=#FFFFFF&file=/_ajax/call/asset/get_asset/<?=$target->rid;?>/NULL/NULL/NULL/NULL/1/0/1/null.flv');
		lg.write('player-large-<?=$target->rid;?>');

		// stop playing the small video when user clicks on 'view larger' link		
		$(".fb_larger").click(function(){
			$("#"+this.rel)[0].sendEvent("PLAY", "false");
		});
	//]]>
	</script>
</figure>
