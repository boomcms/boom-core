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
			<?= $page->getSlot('text', 'standfirst', '<h2 class="standFirst">', '</h2>','ch,ins'); ?>
		</div>
		<?= $page->getSlot('text', 'bodycopy', '<div id="content">', '</div>');?>			
		<?
			if ($count === 0)
			{
				echo "Your search returned no results.";
			}
			else 
			{
				echo "<ol class='search-results'>";
				foreach($results as $result)
				{
					?>
						<li<? if ($q == 0) { echo ' class="first"'; } ?>>
							<h3>
								<a href="<?=$result->getPrimaryUri();?>"><?=$p->title;?></a>
							</h3>
							<p>
							<?
								$ex = explode("</p>",$result->getSlot('text', 'standfirst'));
								echo strip_tags($ex[0]);
							?>
							</p>
						</li>
					<?
				}	

				echo "</ol>";
			}
		?>

		<?= $page->getSlot('feature', 'feature3', 'centre');?>
		<?= $page->getSlot('feature', 'feature4', 'centre');?>
	</div>
	<div id="aside">	
		<?= $page->getSlot('feature', 'feature1', 'right');?>
		<?= $page->getSlot('feature', 'feature2', 'right');?>
		<?//= O::f('chunk_linkset_v')->getSlot(O::f('site_page')->get_homepage()->id,'quicklinks','quicklinks');?>
	</div>
					
	<?= new View('site/subtpl_footer'); ?>
</div>
