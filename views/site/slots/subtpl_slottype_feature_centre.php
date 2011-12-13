<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<a href="<?=$target->absolute_uri();?>" class="midfeature">
	<span class="bg iecsspng">
		<span class="head"><?=$target->title;?> &raquo;</span>
		<span class="par">
				<?=O::fa('chunk_text')->get_chunk($target->rid,'standfirst')?>
		</span>
	</span>
</a>
