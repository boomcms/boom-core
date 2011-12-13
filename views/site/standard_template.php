<?php
	# Copyright 2009 - 2011, Hoop Associates Ltd
	# Hoop Associates		www.thisishoop.com	 mail@hoopassociates.co.uk
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title><?= $page->version->title ?> | <?=Kohana::$config->load('core')->get('client_name')?></title>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta name="description" content="<?= htmlspecialchars( $page->version->getDescription() );?>" />
		<meta name="keywords" content="<?= htmlspecialchars( $page->version->keywords );?>" />	
		<?
			if ($page->version->indexed == true)
			{
				echo "<meta name='robots' content='index, follow' />";
			}
			else
			{
				echo "<meta name='robots' content='noindex, nofollow' />";
			}
		?>

		<?
			foreach( $css as $file )
			{
				echo "<link rel='stylesheet' type='text/css' href='$file' media='screen' />";
			}
			
			foreach( $js as $file )
			{
				echo "<script type='text/javascript' src='$file'></script>";
			}
		?>
		
		<?
			echo $subtpl_header;
		?>

		<?
			if ( $page->hasRss() )
			{
				echo "<link rel='alternate' type='application/rss+xml' title='RSS' href='" . $page->getAbsoluteUri() . "/.rss' />";
			}
		?>
	</head>
	<body>
		<?
			if ( Auth::Instance()->logged_in() )
			{
				echo "<div id='cmsbars'>";
				echo View::factory( 'cms/subtpl_bar' );
				echo "</div>";
			}

			echo $subtpl_main;

			if (Kohana::$config->load('core')->get('include_analytics') === true)
			{
				echo View::factory( 'site/subtpl_analytics' );
			}
		?>		
	</body>
</html>
