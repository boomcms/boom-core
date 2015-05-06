<?= View::factory('boom/errors/header', array('title' => 'Not found')) ?>
	<div id="broken" class="clearfix">
		<div class="column">
			<div class="warning">
				<h1>404 Not found</h1>
			</div>
			<p>The page you requested could not be found.</p>
		</div>
	</div>
<?= View::factory('boom/errors/footer') ?>
