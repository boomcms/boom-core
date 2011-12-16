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
					<h2><? echo __( 'Contact us' ); ?></h2>
					<?= 
						$page->get_chunk('text', 'contactinfo', '<div class="contactinfo">', '</div>');
					?>
				</div>
			</div>
		</div>
	</div>
</div>