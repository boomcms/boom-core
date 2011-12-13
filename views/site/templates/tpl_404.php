<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<div id="wrapper" class="yui-t2">
	<div id="yui-main">
		<div class="yui-b">
			<div id="header" class="yui-g">
				<div id="header-nav">
					<?= new View('site/subtpl_searchform'); ?>
					<?= new View('site/subtpl_topnav'); ?>
				</div>
			</div>

			<div id="bodyContent">
				<h1 class="pageTitle"><?= $page->title?></h1>
	
				<div class="column sm">
					<? //O::f('chunk_text_v')->get_chunk($this->page->id, 'quote', '<h3 class="quote">','</h3>','ch, ins'); ?>
					<? //O::f('chunk_text_v')->get_chunk($this->page->id, 'quoteattribution', '<h3 class="quoteattribution">','</h3>','ch,ins'); ?>
					<? //O::f('chunk_feature_v')->get_chunk($this->page->id, 'featureright1', 'right'); ?>
					<? //O::f('chunk_feature_v')->get_chunk($this->page->id, 'featureright2', 'right'); ?>
				</div>
	
				<div class="column lg">
					<? //O::f('chunk_text_v')->get_chunk($this->page->id, 'standfirst', '<h2 class="standFirst">', '</h2>','ch,ins'); ?>
					<? //O::f('chunk_text_v')->get_chunk($this->page->id, 'bodycopy', '<div id="content">', '</div>');?>
				</div>
			</div>
		</div>
	</div>

	<div class="yui-b">
		<div id="logo" class="block">
			<a title="<?=Kohana::$config->load('core.clientnamelong')?> home" href="/"><img src="/sledge/img/main_logo.jpg" alt="home" /></a>
		</div>

		<? new View('site/subtpl_leftnav'); ?>
	</div>
	<? new View('site/subtpl_footer'); ?>
</div>
