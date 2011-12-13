<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?

	if (Kohana::config('core.quirksmode')) {
		echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title><?= $this->page->current_version->title ?> | <?=Kohana::config('core.clientnamelong')?></title>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<?
			$page_description = $this->page->current_version->description ? 
			htmlspecialchars($this->page->current_version->description) : 
			htmlspecialchars(preg_replace('/<[^>]+>/', '', O::fa('chunk_text')->find_by_slotname_and_page_vid('standfirst', $this->page->current_version->vid)->text));
		?>
		<meta name="description" content="<?=$page_description;?>" />
		<meta name="keywords" content="<?=htmlspecialchars($this->page->current_version->keywords);?>" />  
		<?if ($this->page->current_version->indexed == 't') {?>
			<meta name="robots" content="index, follow" />
		<?}else{?>
			<meta name="robots" content="noindex, nofollow" />
		<?}?>
		
		<?if (file_exists(APPPATH . "/docroots/site/css/main.css")) {?>
			<?= $this->header('css', array('/css/main.css?'.filemtime(APPPATH . "/docroots/site/css/main.css")))?>
		<?} else {?>
			<?=$this->header('css', array('/sledge/css/main.css'))?>
		<?}?>
		<?if (file_exists(APPPATH . "/docroots/site/css/main_ie.css")){?>
			<!--[if IE]><?= $this->header('css', array('/css/main_ie.css'))?><![endif]-->
		<?}?>
		<?if (!file_exists(APPPATH . '/docroots/site/css/print.css')) {?>
			<?= $this->header('css', array('/sledge/css/grids_combined_min.css'), 'print'); ?>
		<?}?>
		<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js"></script>-->
		<?= $this->header('js', array('/sledge/js/jquery.js'))?>
		<?if (file_exists(APPPATH . "docroots/site/js/main_init.js")) {?>
			<?= $this->header('js', array('/js/main_init.js'));?>
		<?} else {?>
			<?= $this->header('js', array('/sledge/js/main_init.js'));?>
		<?}?>
		<?if (file_exists(APPPATH . "views/site/subtpl_header.php") or file_exists(SLEDGEPATH . "views/site/subtpl_header.php")) {?>
			<?= new View('site/subtpl_header'); ?>		       
		<?}?>
		<? if($this->page->has_rss()) { ?>
			<link rel="alternate" type="application/rss+xml" title="RSS" href="<?=$this->page->absolute_uri().'/.rss';?>" />
		<? } ?>
	</head>

	<body>
		<?= new View($tpl); ?>
		<?if (preg_match("/^www|^staging|^websitelive/", $_SERVER['SERVER_NAME'])) {?>
			<? //new View("site/subtpl_analytics"); ?>
		<?}?>
		<?if (Permissions::may_i('write')){
		
			//echo new View('site/editpageoptions');
		}?>

		<?if (Kohana::config('core.environment') == 'dev'){?>
			<!--
				<?= print_r(Benchmark::get(TRUE, 5))?>
			-->
		<?}?>		
	</body>
</html>
