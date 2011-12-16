<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<div id="pwp" class="block border">
	<h3><?=$target->title?></h3>

	<div>
		<p><strong><?=O::fa('chunk_text')->get_chunk($target->rid,'standfirst')?></strong></p>

		<p><?=Misc::truncatetext(O::fa('chunk_text')->get_chunk($target->rid,'bodycopy'), 'characters',100,'...')?></p>

		<?if (file_exists(APPPATH . "/docroots/site/img/headerimages_small/" . $this->page->id . ".jpg")) {?>
			<img src="/sledge/img/headerimages_small/<?=$target->rid?>.jpg" alt="Image illustrating <?=$target->title?>" />
		<?} else {?>
			<img src="/sledge/img/headerimages_small/placeholder.jpg" alt="Image illustrating <?=$target->title?>" />
		<?}?>
	</div>										
</div>
