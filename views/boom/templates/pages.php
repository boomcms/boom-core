	<?= View::factory('boom/header')->set('title', 'Templates') ?>

	<div id="b-topbar" class="ui-helper-clearfix ui-tabs ui-widget ui-widget-content ui-corner-all">

		<?= \Boom\Menu\Menu::factory('boom')->sort('priority') ?>

		<div class="ui-helper-clearfix ui-tabs-panel ui-widget-content ui-corner-bottom">
			<div id="b-page-actions" class="ui-helper-right">
				&nbsp;
			</div>
		</div>
	</div>

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

	<?= Boom::include_js() ?>

	<script type="text/javascript">
		//<![CDATA[
		(function($){
			$.boom.init();
		})(jQuery);
		//]]>
	</script>
</body>
</html>