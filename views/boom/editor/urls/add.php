<?php
/**
* Displays a form to add a secondary URI to a page.
*
* Rendered by:	Controller_Cms_Page::action_addurl()
* Submits by:	Controller_Cms_Page::action_addurl()
*
*********************** Variables **********************
*	$person	****	Instance of Model_Person	****	The active user.
*	$page	****	Instance of Model_Page		****	The page being edited.
********************************************************
*
*/
?>
<form id="boom-form-addurl" name="form-addurl">
	<?= Form::hidden('csrf', Security::token()) ?>
	<input type='hidden' name='page' value='<?= $page->id ?>' />
	<input type='text' name='url' id='url' />
</form>
