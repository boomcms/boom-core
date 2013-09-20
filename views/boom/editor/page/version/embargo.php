<form class="b-form-settings narrow">
	<?= Form::hidden('csrf', Security::token()) ?>

	<p>
		<label for="page-embargo"><?=__('Embargo until')?></label>
		<?= Form::input('embargoed_until', ($version->embargoed_until)? date("d F Y h:i", $version->embargoed_until) : date("d F Y h:i", $_SERVER['REQUEST_TIME']), array('class' => 'boom-datepicker', 'id' => 'page-embargo')) ?>
	</p>
</form>