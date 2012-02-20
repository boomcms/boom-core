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
					<?= new View('site/nav/top'); ?>
				</div>
			</div>

			<div id="bodyContent">
				<h1 class="pageTitle" id='sledge-page-title'><?= $page->title?></h1>
	
				<div class="column sm">
					<? $page->get_slot('text', 'quote', '<h3 class="quote">','</h3>','ch, ins'); ?>
					<? $page->get_slot('text', 'quoteattribution', '<h3 class="quoteattribution">','</h3>','ch,ins'); ?>
					<? $page->get_slot('feature', 'featureright1', 'right'); ?>
					<? $page->get_slot('feature', 'featureright2', 'right'); ?>
				</div>
	
				<div class="column lg">
					<? $page->get_slot('text', 'standfirst', '<h2 class="standFirst">', '</h2>','ch,ins'); ?>
					<? $page->get_slot('text', 'bodycopy', '<div id="content">', '</div>');?>
				</div>
			</div>
		</div>
	</div>

	<div class="yui-b">
		<div id="logo" class="block">
			<a title="<?=Kohana::$config->load('config')->get( 'client_name' )?> home" href="/"><img src="/sledge/img/main_logo.jpg" alt="home" /></a>
		</div>

		<? new View('site/nav/left'); ?>
	</div>
	<? new View('site/subtpl_footer'); ?>
</div>
