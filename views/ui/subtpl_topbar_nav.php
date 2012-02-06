<?php
/**
* The lovely little bar along the top of the CMS view.
* Only displayed if the user is logged in, I believe.
*
* Rendered by:	views/cms/standard_template.php & views/site/standard_tempalte_editable.php
*
*********************** Variables **********************
*	$person	****	Instance of Model_Person	****	The active user.
********************************************************
*
*/
?>
<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
	<li>
		<a href='/'>Home</a>
	</li>

	<?
		if ($person->can( 'view messages' )):
			echo "<li><a href='/cms/messages'>Messages</a></li>";
		endif;

		if ($person->can( 'manage tags' )):
			echo "<li><a href='/cms/tags'>Tags</a></li>";
		endif;

		if ($person->can( 'manage assets' )):
			echo "<li><a href='/cms/assets'>Assets</a></li>";
		endif;

		if ($person->can( 'manage people' )):
			echo "<li><a href='/cms/people'>People</a></li>";
			echo "<li><a href='/cms/who'>Who</a></li>";
		endif;

		if ($person->can( 'view logs' )):
			echo "<li><a href='/cms/log'>Logs</a></li>";
		endif;

		if ($person->can( 'manage templates' )):
			echo "<li><a href='/cms/templates'>Templates</a></li>";
		endif;
	?>
</ul>
