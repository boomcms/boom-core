<?php
/**
* This is the standard template for editable site pages - i.e. the CMS view.
* This is very similar to the CMS standard template since it requires all the JavaScript stuff.
* But because these pages aren't hard coded the templates require a $page variable.
*
* At one point this was shared with the cms standard template but I think it's important to keep the distinction between hard coded cms pages and variable site page albeit in cms view.
*
*
* Rendered by Sledge_Controller::after() via Controller_Site::before();
*
*********************** Variables **********************
*	global $page			****	Instance of Page. Not Model_Page, but it can be used in the same way.
*	global $actual_person	****	Instance of Model_Person		****	The current logged in user.
*	global $person			****	Instance of Model_Person		****	The active user.
*	global $mode			****	string							****	'cms' or 'site'.
*	global $request			****	Instance of Request				****	See http://kohanaframework.org/3.2/guide/api/Request
*	global $params			****	array 							****	An array of URI parameters.
********************************************************
*
* @uses Request::detect_uri()
* @uses URL::query()
* @uses View::factory()
* @uses Kohana::$config
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title><?= $page->title; ?> | <?= Arr::get($config, 'client_name')?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<?= HTML::style("media/sledge/css/cms.css") ?>
	<?= HTML::style("media/sledge/css/ui-" . $actual_person->theme . "/jquery-ui.css", array('id' => 'sledge-theme-css')) ?>
</head>
<body>
	<div id="sledge-wysiwyg-toolbar" class="mceEditor o2k7Skin o2k7SkinSilver"></div>

	<?
		if (Editor::state() == Editor::EDIT):
			echo View::factory('sledge/editor/topbar');
		else:
			echo View::factory('sledge/editor/topbar_preview');
		endif;
	?>

	<div id="sledge-dialogs">
		<div id="sledge-dialog-alerts">
			<p>&nbsp;</p>
		</div>
	</div>

	<div id="sledge-loader-dialog-overlay" class="ui-widget-overlay"></div>
	<div id="s-page-edit">
		<iframe id="s-page-edit-iframe" src="<?= URL::site(Request::detect_uri()) . URL::query( array('editpage' => 1));?>"></iframe>
	</div>

	<?= HTML::script("media/sledge/js/sledge.helpers.js") ?>
	<?= HTML::script("media/sledge/js/jquery.js") ?>
	<?= HTML::script("media/sledge/js/sledge.jquery.ui.js") ?>
	<?= HTML::script("media/sledge/js/sledge.plugins.js") ?>
	<?= HTML::script("media/sledge/js/sledge.config.js") ?>
	<?= HTML::script("media/sledge/js/sledge.core.js") ?>
	<?= HTML::script("media/sledge/js/sledge.chunk.js") ?>
	<?= HTML::script("media/sledge/js/sledge.page.js") ?>
	<?= HTML::script("media/sledge/js/sledge.helpers.js") ?>
	<?= HTML::script("media/sledge/js/sledge.tagmanager.js") ?>
	<?= HTML::script("media/sledge/js/sledge.assets.js") ?>
	<?= HTML::script("media/sledge/js/sledge.links.js") ?>
	<?= HTML::script("media/sledge/js/sledge.items.js") ?>

	<script type="text/javascript">
		//<![CDATA[
		(function($){

			$.sledge.init('sites', {
				person: {
					rid: <?= $person->id?>,
					firstname: '<?= $person->firstname?>',
					lastname: "<?= $person->lastname?>"
				}
			});

			$.sledge.page.init({
				defaultRid: 1,
				<? 
					if (isset($page)): 
						echo "id: $page->id,"; 
						echo "vid: ", $page->version->id, ",";
					endif;
				
				?> 
			});

		})(jQuery);
		//]]>
	</script>
</body>
</html>
