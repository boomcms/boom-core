<?php
	# Copyright 2011, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<div class="side-image">
	<div class="yellow-se">
		<div class="yellow-sw">
			<img src="/_ajax/call/asset/get_asset/<?= $target->rid;?>/210/1000" alt="<?=htmlspecialchars($target->description);?>">
			<?=O::fa('chunk_asset')->get_caption($this->page->rid, $target->rid, 'image', '<p>', '</p>', 'ch,ins,br,fo',true,'jwysiwyg')?>
		</div>
	</div>
</div>
