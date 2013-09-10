<form class="b-form-settings narrow">
	<?= Form::hidden('csrf', Security::token()) ?>
	<div>
		<p>
			<label for="page-visible"><?=__('Embargo')?></label>
			<?= Form::select('embargoed', array(1 => 'Yes', 0 => 'No'), ($version->embargoed_until > $_SERVER['REQUEST_TIME']), array('id' => 'page-visible')) ?>
		</p>

		<p>
			<label for="page-embargo"><?=__('Embargo until')?></label>
			<?= Form::input('embargoed_until', ($version->embargoed_until)? date("d F Y h:i", $version->embargoed_until) : date("d F Y h:i", $_SERVER['REQUEST_TIME']), array('class' => 'boom-datepicker', 'id' => 'page-embargo')) ?>
		</p>
	</div>
</form>