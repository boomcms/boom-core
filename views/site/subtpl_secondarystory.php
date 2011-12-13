<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<? if (O::f('chunk_text_v')->get_chunk($this->page->rid, 'heading') != '' or O::f('chunk_text_v')->get_chunk($this->page->rid, 'secondary_story') != '') {?>
	<div class="secondaryStory">
		<?= O::f('chunk_text_v')->get_chunk($this->page->rid, 'heading', '<h3>','</h3>','ch,ins'); ?>
		<?= O::f('chunk_text_v')->get_chunk($this->page->rid, 'secondary_story', '<p>','</p>','ch,ins'); ?>
	</div>
<?}?>
