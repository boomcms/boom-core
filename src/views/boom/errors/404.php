<?= View::make('boom/errors/header', ['title' => 'Not found']) ?>
	<div id="broken" class="clearfix">
		<div class="column">
			<div class="warning">
				<h1>404 Not found</h1>
			</div>
			<p>The page you requested could not be found.</p>
		</div>
	</div>
<?= View::make('boom/errors/footer') ?>
