<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title><?= $page->version()->title; ?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<base target="_top" />
	<?= HTML::style("media/sledge/css/cms.css") ?>
	<?= HTML::style("media/sledge/css/themes/" . Themes::current() . "/jquery-ui.css", array('id' => 'sledge-theme-css')) ?>
</head>
<body>
	<div id="sledge-wysiwyg-toolbar" class="mceEditor o2k7Skin o2k7SkinSilver"></div>

	<?
		if ($editor->state() == Editor::EDIT):
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
	<?= HTML::script("media/sledge/js/sledge.items.js") ?>
	<?= HTML::script("media/sledge/js/sledge.assets.js") ?>
	<?= HTML::script("media/sledge/js/sledge.links.js") ?>


	<script type="text/javascript">
		//<![CDATA[
		(function($){

			$.sledge.init('sites', {
				person: {
					rid: <?= $person->id?>,
					name: "<?= $person->name?>"
				}
			});

			$.sledge.page.init({
				defaultRid: 1,
				<?
					if (isset( $page )):
						echo "id: $page->id,";
						echo "vid: ", $page->id;
					endif;
				?>
			});

			<? if ($editor->state() === Editor::EDIT): ?>
				$.sledge.page.register({
					rid: <?=$page->id;?>,
					vid: <?=$page->id;?>,
					writable: 1,
					editorOptions: {}
				});
			<? endif; ?>
		})(jQuery);
		//]]>
	</script>
</body>
</html>