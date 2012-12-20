<?php
/**
* Displays a list of pages which use a template.
*/
?>
	<?= View::factory('sledge/header')->set('title', 'Templates') ?>

	<div id="sledge-topbar" class="ui-helper-clearfix ui-tabs ui-widget ui-widget-content ui-corner-all">

		<?= Menu::factory('sledge') ?>

		<div class="ui-helper-clearfix ui-tabs-panel ui-widget-content ui-corner-bottom">
			<div id="s-page-actions" class="ui-helper-right">
				&nbsp;
			</div>
		</div>
	</div>

	<div id="sledge-dialogs">
		<div id="sledge-dialog-alerts">
			<p>&nbsp;</p>
		</div>
	</div>

	<div id="sledge-loader-dialog-overlay" class="ui-widget-overlay"></div>
	<div id="s-page-edit">
		<div>
			<table>
				<tr>
					<th>Page title</th>
					<th>URL</th>
				</tr>
				<?
					foreach ($pages as $p)
					{
						?>
						<tr>
							<td><?= $p['title'] ?></td>
							<td><a href='<?= URL::site($p['location']) ?>'><?= $p['location'] ?></a></td>
						</tr>
						<?
					}
				?>
			</table>
		</div>
	</div>

	<?= HTML::script("media/sledge/js/sledge.helpers.js") ?>
	<?= HTML::script("media/sledge/js/jquery.js") ?>
	<?= HTML::script("media/sledge/js/sledge.jquery.ui.js") ?>
	<?= HTML::script("media/sledge/js/sledge.plugins.js") ?>
	<?= HTML::script("media/sledge/js/sledge.config.js") ?>
	<?= HTML::script("media/sledge/js/sledge.core.js") ?>
	<?= HTML::script("media/sledge/js/sledge.helpers.js") ?>
</body>
</html>
