	<?= View::make('boom::header', ['title' => 'Templates']) ?>
	<?= $menu() ?>

	<div id="b-topbar" class="b-toolbar">
		<?= $menuButton() ?>
		<?= $button('download', Lang::get('Download as CSV'), ['id' => 'b-template-pages-download', 'class' => 'b-button-withtext']) ?>
	</div>

    <div>
        <table class="tablesorter">
			<thead>
				<tr>
					<th>Page title</th>
					<th>URL</th>
					<th>Visible?</th>
					<th>Last Edited</th>
				</tr>
			</thead>
			
			<tbody>
				<?php foreach ($pages as $p): ?>
					<tr>
						<td><?= $p->getTitle() ?></td>
						<td><a href='<?= $p->url() ?>'><?= $p->url()->getLocation() ?></a></td>
						<td><?= $p->isVisible()? 'Yes' : 'No' ?></td>
						<td><?= $p->getLastModified()->format('Y-m-d H:i:s') ?></td>
					</tr>
				<?php endforeach ?>
			</tbody>
        </table>
    </div>

	<?= $boomJS ?>
	<script type="text/javascript">
		//<![CDATA[
		(function ($) {
			$.boom.init();
			$('body').templateManager();
		})(jQuery);
		//]]>
	</script>
</body>
</html>
