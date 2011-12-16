<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates		www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<div class="audio">
<!--assetwrapperstart-->
	<p class="inline-asset">
		<?if (get_class($this) == 'Cms_page_manager_Controller'){?>
			<div id="cms-flash-overlay" style="width:328px;height:575px;"></div>
		<?}?>
		<?$asset_rid = $this->uri->last_segment();?>
		<!--assetstart-->
			<script type="text/javascript" src="/sledge/js/audio-player.js"></script> 
			<object type="application/x-shockwave-flash" data="/sledge/img/player2.swf" id="audioplayer1" height="24" width="290"> 
			<param name="movie" value="/sledge/img/player2.swf"> 
			<param name="FlashVars" value="playerID=1&soundFile=/_ajax/call/asset/get_mp3/<?=$asset_rid?>/null.mp3">
			<param name="quality" value="high"> 
			<param name="menu" value="false"> 
			<param name="wmode" value="transparent"> 
			</object>
		<!--assetend-->
		<div class="video-footnote">
			<?=O::fa('asset', $asset_rid)->description;?>
		</div>
	</p>
<!--assetwrapperend-->
</div>
