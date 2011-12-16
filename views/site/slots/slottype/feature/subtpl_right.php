<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<a class="feature" href="<?=$target->absolute_uri();?>" title="<?=$target->title;?>">
	<span class="title"><?=$target->title?> &raquo;</span>
	<span class="content"><?=O::f('chunk_text_v')->get_chunk($target->rid,'standfirst')?></span>
</a>
