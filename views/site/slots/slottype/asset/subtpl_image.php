<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<p>
	<?if ($target->id) {?>
		<img src="/_ajax/call/asset/get_asset/<?=$target->rid?>/200/200/85/0" alt="<?=htmlspecialchars($target->description)?>" />
	<?} else {?>
		<em>(Click here to add an image.)</em>
	<?}?>
</p>
