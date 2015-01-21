<?= View::factory('boom/errors/header', array('title' => 'Gone')) ?>
	<div id="broken" class="clearfix">
		<div class="column">
			<div class="warning">
				<h1>Gone</h1>
			</div>
			<p>The page you requested no longer exists.</p>
		</div>
	</div>
<?= View::factory('boom/errors/footer') ?>