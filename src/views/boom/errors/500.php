<?= View::factory('boom/errors/header', array('title' => 'Error')) ?>
	<div id="broken" class="clearfix">
		<div class="column">
			<div class="warning">
				<h1>Sorry, something's not working quite right.</h1>
			</div>
			<p>We've been informed and are looking into it.</p>
		</div>
	</div>
<?= View::factory('boom/errors/footer') ?>