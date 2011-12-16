<?php
	# Copyright 2011, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<? if ($this->page_model=="cms_page" or O::fa('chunk_feature')->find_by_page_vid_and_slotname($this->page->vid,'related1')->rid or
O::fa('chunk_feature')->find_by_page_vid_and_slotname($this->page->vid,'related2')->rid) {?>
<div id="related-info" class="box">
	<div class="box-sw">
		<div class="box-ne">
			<div class="box-se">
				<div class="box-nw">
					<h2><? echo __( 'Related information' ); ?></h2>
					<ul>
						<?= O::fa('chunk_feature')->get_chunk($this->page->rid, 'related1', 'sidebar');?>
						<?= O::fa('chunk_feature')->get_chunk($this->page->rid, 'related2', 'sidebar');?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<?}?>