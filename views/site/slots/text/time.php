<?php
/**
* Text chunk template for a time restricted text slot.
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
<select name="<?=$slotname?>" class="chunk-slot {text-restricted <?=$slotname ?>}">
	<? for ($h=0;$h<24;$h++): ?>
		<option value="<?=$h?>.00"<?if ($text == $h.'.00') {?> selected="selected"  <?}?>><?=$h?>.00</option>
		<option value="<?=$h?>.30"<?if ($text == $h.'.30') {?> selected="selected" <?}?>><?=$h?>.30</option>
	<? endfor; ?>
</select>
