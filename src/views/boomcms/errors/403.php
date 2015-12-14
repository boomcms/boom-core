<?= view('boom/errors/header', ['title' => 'Unauthorised']) ?>
	<div id="broken" class="clearfix">
		<div class="column">
			<div class="warning">
				<h1>403 Unauthorised</h1>
			</div>
			<p>We're very sorry but you do not have permission to perform the requested action.</p>
			<p>This could mean that your login session has timed out, in which case you can log back in by <a href="/cms/login">clicking here</a>.</p>
		</div>
	</div>
<?= view('boom/errors/footer') ?>
