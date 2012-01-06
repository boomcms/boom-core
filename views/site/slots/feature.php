<?php
/**
* Template to display a feature box.
* Unlike most of the templates in this directory this one is currently being used in Sledge 3.
* @package Templates
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
?>

<div class="feature">
	<div class="feature_ne">
		<div class="feature_se">
			<div class="feature_sw">
				<a href="<?=$page->url()?>">
					<h2><?=$page->title?></h2>
					<p><?=$page->get_slot( 'text', 'standfirst' ) ?></p>
				</a>
			</div>
		</div>
	</div>
</div>
