<?php
	# Copyright 2011, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<li>
	<a href="<?=$target->absolute_uri()?>">
		<h3><?=$target->title?></h3>
		<p><?=preg_replace('/<[^>]+>/', '', O::f('chunk_text_v')->get_chunk($target->rid,'standfirst'))?></p>
	</a>
</li>
