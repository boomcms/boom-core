<?php
/**
* Text chunk template for a multiline restricted text slot.
*
* Rendered by: Model_Chunk_Text::show()
*
*********************** Variables **********************
*	$slotname		****	text			****	The name of the slot.
*	$text			****	text			****	Current vlaue of the slot.
********************************************************
*
* @package Templates
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2012, Hoop Associates
*/
?>
<span class="chunk-slot {text <?=$slotname ?> [ch ins] jwysiwyg}">
	<?=$text?>
</span>
