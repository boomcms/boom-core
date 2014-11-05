<form class="b-form-settings">
	<?= Form::hidden('csrf', Security::token()) ?>

	<div>
		<p>
			<label for="b-page-visible"><?=__('Visible')?></label>
			<?= Form::select('visible', array(1 => 'Yes', 0 => 'No'), (int) $page->isVisibleAtAnyTime(), array('id' => 'b-page-visible')) ?>
		</p>
		<p>
			<label for="visible-from"><?=__('Visible from')?></label>
			<?= Form::input('visible_from', $page->getVisibleFrom()->format('d F Y H:i'), array('id' => 'visible-from', 'class' => 'boom-datepicker')) ?>
		</p>
		<p>
			<label for="toggle-visible"><?=__('Visible until')?></label>
			<?= Form::checkbox('toggle_visible_to', 1, $page->getVisibleTo()->getTimestamp() != 0, array('id' => 'toggle-visible')) ?>
			<?= Form::input('visible_to', ($page->getVisibleTo()->getTimestamp())? $page->getVisibleTo()->format('d F Y H:i') : date("d F Y H:i", time()), array('id' => 'visible-to', 'class' => 'boom-datepicker')) ?>
		</p>
	</div>
</form>
