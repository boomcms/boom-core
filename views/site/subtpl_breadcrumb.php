<?php
	# Copyright 2011, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>

<div id="breadcrumb">
	<p class="toggle">
		<? echo __( 'Hide' ); ?>
		<!-- img src="/img/hide.png" alt="Hide breadcrumbs" -->
	</p>
	<div>
	<p><? echo __( 'You are here' ); ?></p>
	<ul>
		<?foreach ($this->page->getMptt()->getAncestors() as $anc) {?>
			<?if ($anc->current_version->internal_name == 'home') $anc->title = 'Home';?>
			<li><a href="/<?= $anc->getPrimaryUri() ?>"><?=$anc->current_version->getTitle() ?></a></li>
		<?}?>
	</ul>
	</div>
</div>

