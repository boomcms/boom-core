<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title><?= $page->version()->title; ?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<base target="_top" />
	<?= HTML::style("media/boom/css/cms.css") ?>
	<?= HTML::style("media/boom/css/themes/hoop/jquery-ui.css") ?>
</head>
<body>
	<div id="boom-wysiwyg-toolbar" class="mceEditor o2k7Skin o2k7SkinSilver"></div>

	<?
		if ($editor->state() == Editor::EDIT):
			echo View::factory('boom/editor/topbar');
		else:
			echo View::factory('boom/editor/topbar_preview');
		endif;
	?>

	<div id="boom-dialogs">
		<div id="boom-dialog-alerts">
			<p>&nbsp;</p>
		</div>
	</div>

	<div id="boom-loader-dialog-overlay" class="ui-widget-overlay"></div>

	<?= HTML::script("media/boom/js/boom.helpers.js") ?>
	<?= HTML::script("media/boom/js/jquery.js") ?>
	<?= HTML::script("media/boom/js/boom.jquery.ui.js") ?>
	<?= HTML::script("media/boom/js/boom.plugins.js") ?>
	<?= HTML::script("media/boom/js/boom.config.js") ?>
	<?= HTML::script("media/boom/js/boom.core.js") ?>
	<?= HTML::script("media/boom/js/boom.chunk.js") ?>
	<?= HTML::script("media/boom/js/boom.page.js") ?>
	<?= HTML::script("media/boom/js/boom.helpers.js") ?>
	<?= HTML::script("media/boom/js/boom.tagmanager.js") ?>
	<?= HTML::script("media/boom/js/boom.items.js") ?>
	<?= HTML::script("media/boom/js/boom.assets.js") ?>
	<?= HTML::script("media/boom/js/boom.links.js") ?>


	<script type="text/javascript">
		//<![CDATA[
		(function($){

			$.boom.init('sites', {
				person: {
					rid: <?= $person->id?>,
					name: "<?= $person->name?>"
				}
			});

			$.boom.page.init({
				defaultRid: 1,
				<?
					if (isset( $page )):
						echo "id: $page->id,";
						echo "vid: ", $page->id;
					endif;
				?>
			});

			<? if ($editor->state() === Editor::EDIT): ?>
				$.boom.page.register({
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