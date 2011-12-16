<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<div>
<!--assetwrapperstart-->
<?  $new_src = '/_ajax/call/asset/get_asset/'.$webgetasset->rid; ?>
<p class="inline-asset">
	<!--assetstart--><img src="<?=$new_src?>/<?=$width;?>/<?=$height?>/<?=$quality;?>" alt="<?=str_replace("\n", ' ', htmlspecialchars($webgetasset->description))?>" /><!--assetend-->
</p>
<!--assetwrapperend-->
</div>
