<form class="b-form-settings">
        <label>
            <?=Lang::get('Visible') ?>
            <?= Form::select('visible', [1 => 'Yes', 0 => 'No'], (int) $page->isVisibleAtAnyTime(), ['id' => 'b-page-visible']) ?>
        </label>

        <label>
            <?=Lang::get('Visible from') ?>
            <?= Form::input('visible_from', $page->getVisibleFrom()->format('d F Y H:i'), ['id' => 'visible-from', 'class' => 'boom-datepicker']) ?>
        </label>

        <label>
            <?=Lang::get('Visible until') ?>

            <?= Form::checkbox('toggle_visible_to', 1, $page->getVisibleTo() != null, ['id' => 'toggle-visible']) ?>
            <?= Form::input('visible_to', ($page->getVisibleTo() != null) ? $page->getVisibleTo()->format('d F Y H:i') : date("d F Y H:i", time()), ['id' => 'visible-to', 'class' => 'boom-datepicker']) ?>
        </label>
</form>
