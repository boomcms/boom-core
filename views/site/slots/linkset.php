<?php
/**
* Template to display a linkset chunk
* Unlike most of the templates in this directory this one is currently being used in Sledge 3.
* @package Templates
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
?>

<div id="quicklinks">
        <h3>
			<?= $linkset->title; ?>
        </h3>

	<div>
		<?
			$count = $linkset->links->count_all();
			
			if ($count > 0):
				echo "<ul>";

				$i = 0;
				
				foreach($linkset->links->find_all() as $link):
			 		$i++;
				
					echo "<li";
					echo ($i == $count)? " style='border:0px'>" : ">";
				
					if ($link->target_page_id):
						// internal link
						echo "<a href='", $link->page->url(), "'>", $link->page->title, "</a>";
					else:
						// external link
						echo "<a href='$link->url'>$link->title &raquo;</a>";
					endif;

					echo "</li>";
				endforeach;
				
				echo "</ul>";
			endif;
		?>
	</div>
</div>
