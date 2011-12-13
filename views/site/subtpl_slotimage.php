<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<? if (O::f('chunk_asset_v')->get_chunk($this->page->rid, 'slotimage_right', 'image_right') != ''){?>
	<div class="slotimage">
		<?= O::f('chunk_asset_v')->get_chunk($this->page->rid, 'slotimage_right', 'image_right');?>
		<?= O::f('chunk_text_v')->get_chunk($this->page->rid, 'imagecaption', '<p style="margin:0;font-style:italic">', '</p>', 'ch,ins,fo')?>
	</div>
<?}?>
