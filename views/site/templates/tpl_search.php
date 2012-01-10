<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<div class="wrapper">
	<?= new View('site/subtpl_siteheader');?>
	<div id="navigation">
		<?= new View('site/subtpl_logo');?>
		<?= new View('site/nav/left'); ?>		
		<?= new View('site/subtpl_newsletter');?>
	</div>
	<div id="main-content">
		<div class="headings">
			<h1 class="pageTitle"><?= $page->title?></h1>
			<?= $page->get_slot('text', 'standfirst', '<h2 class="standFirst">', '</h2>' ); ?>
		</div>
		<?= $page->get_slot('text', 'bodycopy', '<div id="content">', '</div>');?>			
		<?
			if ($count === 0):
				echo "Your search returned no results.";
			else:
				echo "<ol class='search-results'>";
				
				$i = 0;
				foreach( $results as $result ):
					echo "<li";
					
					if ($i == 0):
						echo ' class="first"';
					endif;
					
					echo ">";
					
					echo "<h3><a href='", $result->url(), "'>", $result->title, "</a></h3>";
					echo "<p>", $result->get_slot('text', 'standfirst'), "</p>";

					echo "</li>";
					$i++;
				endforeach;	

				echo "</ol>";
			endif;
		?>

		<?= $page->get_slot('feature', 'feature3' );?>
		<?= $page->get_slot('feature', 'feature4' );?>
	</div>
	<div id="aside">	
		<?= $page->get_slot('feature', 'feature1' );?>
		<?= $page->get_slot('feature', 'feature2' );?>
		<?= $page->get_slot( 'linkset', 'quicklinks' );?>
	</div>
					
	<?= new View('site/subtpl_footer'); ?>
</div>
