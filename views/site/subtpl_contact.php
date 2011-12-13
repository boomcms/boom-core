<?php
	# Copyright 2011, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<div id="contact-box">
	<div class="box-sw">
		<div class="box-ne">
			<div class="box-se">
				<div class="box-nw">
					<div class="arrow">&nbsp;</div>
					<h2>Contact us</h2>
					<?= O::fa('chunk_text')->get_chunk($this->page->rid, 'contactinfo', '<div class="contactinfo">', '</div>');?>
					<!-- I don't even know if this will be a link -->
					<!-- whatever goes here -->
					<? /* !-- <?= O::fa('chunk_text')->get_chunk($this->page->rid, 'contactinf', '<p class="contactinfo">', '</p>');?> -->
					<address class="contactinfo">
						Contact name<br>
						Contact e-mail<br>
						Contact phone number
					</address */?>
				</div>
			</div>
		</div>
	</div>
</div>