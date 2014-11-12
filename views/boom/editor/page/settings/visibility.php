<form class="b-form-settings">
        <label>
            <?=__('Visible') ?>
            <?= Form::select('visible', array(1 => 'Yes', 0 => 'No'), (int) $page->isVisibleAtAnyTime(), array('id' => 'b-page-visible')) ?>
        </label>

        <label>
            <?=__('Visible from')?>
            <?= Form::input('visible_from', $page->getVisibleFrom()->format('d F Y H:i'), array('id' => 'visible-from', 'class' => 'boom-datepicker')) ?>
        </label>

        <label>
            <?=__('Visible until')?>

            <?= Form::checkbox('toggle_visible_to', 1, $page->getVisibleTo() != null, array('id' => 'toggle-visible')) ?>
            <?= Form::input('visible_to', ($page->getVisibleTo() != null)? $page->getVisibleTo()->format('d F Y H:i') : date("d F Y H:i", time()), array('id' => 'visible-to', 'class' => 'boom-datepicker')) ?>
        </label>
</form>
