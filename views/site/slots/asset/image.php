<?php
/**
* Template to display an image asset
* Unlike most of the templates in this directory this one is currently being used in Sledge 3.
* @package Templates
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
?>
<p>
	<?	
		if ($asset->instance()->loaded()):
			echo "<img src='/asset/", $asset->instance()->id, "/200/200/85/0' alt='", htmlspecialchars($asset->instance()->description), "'>";
		else:
			echo "<em>(Click here to add an image.)</em>";
		endif;
	?>
</p>
