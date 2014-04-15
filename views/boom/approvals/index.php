	<?= View::factory('boom/header', array('title' =>	'Pending Approvals')) ?>

	<div id="b-topbar" class="b-asset-manager">
		<?= Menu::factory('boom')->sort('priority')  ?>
	</div>

	<div id="b-approvals">
		<h1>Pages pending approval</h1>

		<? if (count($pages)): ?>
			<table id="b-items-view-list" class="b-table">
				<tr>
					<th>Page title</th>
					<th>Last edited by</th>
					<th>Time of last edit</th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
				<? foreach ($pages as $page): ?>
					<tr class="<?= Text::alternate('odd', 'even') ?>" data-page-id="<?= $page->id ?>">
						<td><a href="<?= $page->url() ?>"><?= $page->version()->title ?></a></td>
						<td><?= $page->version()->person->name ?> (<?= $page->version()->person->email ?>)</td>
						<td><?= date('d F Y H:i', $page->version()->edited_time) ?></td>
						<td><a href="#" class="b-approvals-publish">Publish</a></td>
						<td><a href="#" class="b-approvals-reject">Revert to published version</a></td>
						<td><a href="<?= $page->url() ?>">View page</a></td>
					</tr>
				<? endforeach ?>
			</table>
		<? else: ?>
			<p>
				None!
			</p>
		<? endif ?>
	</div>

	<?= Boom::include_js() ?>

	<?= Assets::factory('boom_approvals')->js('boom/approvals.js') ?>

	<script type="text/javascript">
		//<![CDATA[
		(function($){
			$.boom.init({
				csrf: '<?= Security::token() ?>'
			});
		})(jQuery);
		//]]>
	</script>
</body>
</html>