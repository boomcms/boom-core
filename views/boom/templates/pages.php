	<?= View::factory('boom/header')->set('title', 'Templates') ?>

	<div id="b-topbar" class="ui-helper-clearfix ui-tabs ui-widget ui-widget-content ui-corner-all">

		<?= Menu::factory('boom')->sort('priority') ?>

		<div class="ui-helper-clearfix ui-tabs-panel ui-widget-content ui-corner-bottom">
			<div id="b-page-actions" class="ui-helper-right">
				&nbsp;
			</div>
		</div>
	</div>

	<div id="boom-dialogs">
		<div id="boom-dialog-alerts">
			<p>&nbsp;</p>
		</div>
	</div>

	<div id="boom-loader-dialog-overlay" class="ui-widget-overlay"></div>
	<div id="b-page-edit">
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

	<?= HTML::script("media/boom/js/boom.helpers.js") ?>
	<?= HTML::script("media/boom/js/jquery.js") ?>
	<?= HTML::script("media/boom/js/jquery.ui.js") ?>
	<?= HTML::script("media/boom/js/jquery.plugins.js") ?>
	<?= HTML::script("media/boom/js/boom.plugins.js") ?>
	<?= HTML::script("media/boom/js/boom.config.js") ?>
	<?= HTML::script("media/boom/js/boom.core.js") ?>
	<?= HTML::script("media/boom/js/boom.helpers.js") ?>
</body>
</html>
