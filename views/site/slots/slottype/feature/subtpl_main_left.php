<?php
	# Copyright 2011, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?
$feature_image = Relationship::find_partners('asset', $target)->where("rel1.description = 'featureimage' and rel2.description = 'featureimage'")->find();
?>
<div class="feature border-right">
	<div class="feature_ne">
		<div class="feature_se">
			<div class="feature_sw">
				<a href="<?=$target->absolute_uri()?>">
					<h2><?=$target->title?></h2>
					<p><?=preg_replace('/<[^>]+>/', '', O::f('chunk_text_v')->get_chunk($target->rid,'standfirst'))?></p>
				</a>
			</div>
		</div>
	</div>
</div>
