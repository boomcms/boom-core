<?php
	# Copyright 2009 - 2011, Hoop Associates Ltd
	# Hoop Associates		www.thisishoop.com	 mail@hoopassociates.co.uk
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title><?= $page->title ?> | <?=Kohana::$config->load('config')->get('client_name')?></title>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta name="description" content="<?= htmlspecialchars( $page->description );?>" />
		<meta name="keywords" content="<?= htmlspecialchars( $page->keywords );?>" />
		<link rel="stylesheet" type="text/css" href="sledge/css/main.css" />	
		<?
			if ($page->indexed)
			{
				echo "<meta name='robots' content='index, follow' />";
			}
			else
			{
				echo "<meta name='robots' content='noindex, nofollow' />";
			}
		?>
		
		<?
			echo $subtpl_header;
		?>

		<?
			if ( $page->hasRss() )
			{
				echo "<link rel='alternate' type='application/rss+xml' title='RSS' href='" . $page->uri() . "/.rss' />";
			}
		?>
	</head>
	<body>
		<?			
			echo new View('site/nav/top');

			echo $subtpl_main;

			if (Kohana::$config->load('config')->get('include_analytics') === true)
			{
				echo View::factory( 'site/analytics' );
			}
		?>		
	</body>
</html>
