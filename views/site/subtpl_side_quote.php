<?php
	# Copyright 2011, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<? if ($this->page_model=="cms_page" or O::fa("chunk_text")->find_by_page_vid_and_slotname($this->page->vid, 'quotecontent')->text != ''){?>
<div class="quote">
	<div class="q"></div>
	<?= O::f('chunk_text_v')->get_chunk($this->page->rid, 'quotecontent', '<q>','</q>','br,ch,ins'); ?>
</div>
<?}?>