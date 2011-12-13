<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<div class="asset_video">
	<script type="text/javascript" src="/sledge/js/swfobject.js"></script>
	<div id="player">Video player</div>

	<script type="text/javascript">
		var so = new SWFObject('/sledge/img/player.swf','mpl','200','200','9');
		so.addParam('allowscriptaccess','always');
		so.addParam('allowfullscreen','true');
		so.addParam('flashvars','&stretching=exactfit&file=/_ajax/call/asset/get_asset/'.$target->rid.'/NULL/NULL/NULL/NULL/1/0/1/null.flv');
		so.write('player');
	</script>
</div>
