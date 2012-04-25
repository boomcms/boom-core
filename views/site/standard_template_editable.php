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
	<title><?= $page->title; ?> | <?=Kohana::$config->load('config')->get('client_name')?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<?= HTML::style( "sledge/js/tiny_mce/themes/advanced/skins/o2k7/ui.css" ) ?>
	<?= HTML::style( "sledge/js/tiny_mce/themes/advanced/skins/o2k7/ui_silver.css" ) ?>
	<?= HTML::style( "sledge/css/sledge.tagmanager.css" ) ?>
	<?= HTML::style( "sledge/css/ui-" . $person->theme . "/jquery-ui.css" ) ?>
	<?= HTML::style( "sledge/css/sledge.ui.css" ) ?>
	<?= HTML::style( "sledge/css/cms.css" ) ?>
</head>
<body>
	<div id="sledge-wysiwyg-toolbar" class="mceEditor o2k7Skin o2k7SkinSilver"></div>

	<?= View::factory( 'cms/ui/site/topbar' ) ?>

	<div id="sledge-dialogs">
		<div id="sledge-dialog-alerts">
			<p>&nbsp;</p>
		</div>
	</div>

	<div id="sledge-loader-dialog-overlay" class="ui-widget-overlay"></div>
	<div id="sledge-page-edit">
		<iframe id="sledge-page-edit-iframe" src="<?= '/ajax' . Request::detect_uri() . URL::query();?>"></iframe>
	</div>

	<?= HTML::script( "sledge/js/sledge.helpers.js" ) ?>
	<?= HTML::script( "sledge/js/jquery.js" ) ?>
	<?= HTML::script( "sledge/js/sledge.jquery.ui.js" ) ?>
	<?= HTML::script( "sledge/js/jquery.ui.button.js" ) ?>
	<?= HTML::script( "sledge/js/sledge.plugins.js" ) ?>
	<?= HTML::script( "sledge/js/sledge.config.js" ) ?>
	<?= HTML::script( "sledge/js/sledge.core.js" ) ?>
	<?= HTML::script( "sledge/js/sledge.chunk.js" ) ?>
	<?= HTML::script( "sledge/js/sledge.page.js" ) ?>
	<?= HTML::script( "sledge/js/sledge.helpers.js" ) ?>
	<?= HTML::script( "sledge/js/sledge.tagmanager.js" ) ?>
	<?= HTML::script( "sledge/js/sledge.tagmanager.assets.js" ) ?>
	<?= HTML::script( "sledge/js/sledge.tagmanager.items.js" ) ?>

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
					if (isset( $page )): 
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