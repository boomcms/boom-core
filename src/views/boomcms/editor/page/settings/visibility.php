<form>
    <h1><?= trans('boomcms::settings.visibility.heading') ?></h1>

    <?php if (!$page->isVisible()): ?>
        <p><?= trans('boomcms::settings.visibility.preview') ?></p>
    <?php endif ?>

    <label>
        <h2><?= trans('boomcms::settings.visibility.visible') ?></h2>

        <select name="visible" id="b-page-visible">
            <option value="1"<?php if ($page->getVisibleFrom() !== null): ?> selected="selected"<?php endif ?>>Yes</option>
            <option value=""<?php if ($page->getVisibleFrom() === null): ?> selected="selected"<?php endif ?>>No</option>
        </select>
    </label>

    <div class="b-visibility-toggle">
        <label>
            <h2><?= trans('boomcms::settings.visibility.from') ?></h2>
            <p><?= trans('boomcms::settings.visibility.from-description') ?></p>

            <?php $visibleFrom = $page->getVisibleFrom() !== null ? $page->getVisibleFrom()->getTimestamp() : time() ?>
            <input type="text" name="visible_from" value="<?= date('d F Y H:i', $visibleFrom) ?>" id="visible-from" class="boom-datepicker" />
        </label>

        <label>
            <h2><?= trans('boomcms::settings.visibility.to') ?></h2>
            <p><?= trans('boomcms::settings.visibility.to-description') ?></p>

            <input type="checkbox" name="toggle_visible_to" value="1" id="toggle-visible"<?php if ($page->getVisibleTo() !== null): ?> checked="checked"<?php endif ?> />
            <input type="text" name="visible_to" value="<?= (!empty($page->getVisibleTo())) ? $page->getVisibleTo()->format('d F Y H:i') : date('d F Y H:i', time()) ?>" id="visible-to" class="boom-datepicker" />
        </label>
    </div>
</form>

<?= $button('refresh', 'reset', ['class' => 'b-visibility-cancel b-button-withtext']) ?>
<?= $button('save', 'save', ['class' => 'b-visibility-save b-button-withtext']) ?>
