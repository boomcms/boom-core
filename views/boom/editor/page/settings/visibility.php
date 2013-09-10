<form class="b-form-settings narrow">
	<?= Form::hidden('csrf', Security::token()) ?>

	<div>
		<p>
			<label for="visible"><?=__('Visible')?></label>
			<?= Form::select('visible', array(1 => 'Yes', 0 => 'No'), $page->visible, array('id' => 'visible')) ?>
		</p>

		<p>
			<label for="visible-from"><?=__('Visible from')?></label>
			<?= Form::input('visible_from', date("d F Y h:m", $page->visible_from), array('id' => 'visible-from', 'class' => 'boom-datepicker')) ?>
		</p>

		<p>
			<label for="toggle-visible"><?=__('Visible until')?></label>
			<?= Form::checkbox('toggle_visible_to', 1, $page->visible_to, array('id' => 'toggle-visible')) ?>
		</p>
	</div>
</form>